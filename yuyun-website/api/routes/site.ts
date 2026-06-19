import { Router, type Request, type Response } from 'express'
import db from '../db.js'
import { authenticate, type AuthRequest } from '../middleware/auth.js'

const router = Router()

const camelToSnake = (str: string): string => str.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`)

router.get('/', (_req: Request, res: Response): void => {
  const row = db.prepare('SELECT * FROM site_config WHERE id = 1').get() as Record<string, string | number> | undefined
  if (!row) {
    res.status(404).json({ success: false, error: '站点配置不存在' })
    return
  }

  const config: Record<string, string | number | boolean> = {}
  for (const [key, value] of Object.entries(row)) {
    const camelKey = key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase())
    if (key === 'popup_enabled') {
      config[camelKey] = Boolean(value)
    } else {
      config[camelKey] = value
    }
  }

  res.json({ success: true, config })
})

router.put('/', authenticate, (req: AuthRequest, res: Response): void => {
  const updates = req.body
  const fields: string[] = []
  const values: (string | number)[] = []

  for (const [key, value] of Object.entries(updates)) {
    if (key === 'id') continue
    const snakeKey = camelToSnake(key)
    fields.push(`${snakeKey} = ?`)
    values.push(value as string | number)
  }

  if (fields.length === 0) {
    res.status(400).json({ success: false, error: '没有要更新的字段' })
    return
  }

  db.prepare(`UPDATE site_config SET ${fields.join(', ')} WHERE id = 1`).run(...values)
  const row = db.prepare('SELECT * FROM site_config WHERE id = 1').get() as Record<string, string | number> | undefined
  res.json({ success: true, config: row })
})

export default router
