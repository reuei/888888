import { Router, type Request, type Response } from 'express'
import db from '../db.js'
import { authenticate, type AuthRequest } from '../middleware/auth.js'

interface CrudOptions {
  table: string
  requiredFields: string[]
  jsonFields?: string[]
}

const snakeToCamel = (str: string): string =>
  str.replace(/_([a-z])/g, (_, letter: string) => letter.toUpperCase())

const camelToSnake = (str: string): string => str.replace(/[A-Z]/g, (letter) => `_${letter.toLowerCase()}`)

const formatRow = (row: Record<string, unknown>): Record<string, unknown> => {
  const formatted: Record<string, unknown> = {}
  for (const [key, value] of Object.entries(row)) {
    const camelKey = snakeToCamel(key)
    if (key === 'enabled' || key === 'popup_enabled' || key === 'read_status') {
      formatted[camelKey] = Boolean(value)
    } else if (key === 'features') {
      try {
        formatted[camelKey] = JSON.parse(value as string)
      } catch {
        formatted[camelKey] = value
      }
    } else if (key === 'order_index') {
      formatted[camelKey] = Number(value)
    } else {
      formatted[camelKey] = value
    }
  }
  return formatted
}

export const createCrudRoutes = ({ table, requiredFields, jsonFields = [] }: CrudOptions): Router => {
  const router = Router()

  router.get('/', (_req: Request, res: Response): void => {
    const rows = db.prepare(`SELECT * FROM ${table} ORDER BY order_index ASC, id DESC`).all() as Record<string, unknown>[]
    res.json({ success: true, data: rows.map(formatRow) })
  })

  router.get('/:id', (req: Request, res: Response): void => {
    const row = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(req.params.id) as Record<string, unknown> | undefined
    if (!row) {
      res.status(404).json({ success: false, error: '记录不存在' })
      return
    }
    res.json({ success: true, data: formatRow(row) })
  })

  router.post('/', authenticate, (req: AuthRequest, res: Response): void => {
    const body = req.body
    for (const field of requiredFields) {
      if (!body[field] && body[field] !== 0) {
        res.status(400).json({ success: false, error: `缺少必填字段：${field}` })
        return
      }
    }

    const fields: string[] = []
    const placeholders: string[] = []
    const values: unknown[] = []

    for (const [key, value] of Object.entries(body)) {
      if (key === 'id') continue
      fields.push(camelToSnake(key))
      placeholders.push('?')
      if (jsonFields.includes(key) && Array.isArray(value)) {
        values.push(JSON.stringify(value))
      } else {
        values.push(value)
      }
    }

    const result = db
      .prepare(`INSERT INTO ${table} (${fields.join(', ')}) VALUES (${placeholders.join(', ')})`)
      .run(...values)
    const row = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(result.lastInsertRowid) as Record<string, unknown>
    res.json({ success: true, data: formatRow(row) })
  })

  router.put('/:id', authenticate, (req: AuthRequest, res: Response): void => {
    const updates = req.body
    const fields: string[] = []
    const values: unknown[] = []

    for (const [key, value] of Object.entries(updates)) {
      if (key === 'id') continue
      fields.push(`${camelToSnake(key)} = ?`)
      if (jsonFields.includes(key) && Array.isArray(value)) {
        values.push(JSON.stringify(value))
      } else {
        values.push(value)
      }
    }

    if (fields.length === 0) {
      res.status(400).json({ success: false, error: '没有要更新的字段' })
      return
    }

    values.push(req.params.id)
    db.prepare(`UPDATE ${table} SET ${fields.join(', ')} WHERE id = ?`).run(...values)
    const row = db.prepare(`SELECT * FROM ${table} WHERE id = ?`).get(req.params.id) as Record<string, unknown>
    res.json({ success: true, data: formatRow(row) })
  })

  router.delete('/:id', authenticate, (req: AuthRequest, res: Response): void => {
    db.prepare(`DELETE FROM ${table} WHERE id = ?`).run(req.params.id)
    res.json({ success: true, message: '删除成功' })
  })

  return router
}
