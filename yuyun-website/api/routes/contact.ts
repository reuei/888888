import { Router, type Request, type Response } from 'express'
import db from '../db.js'

const router = Router()

router.post('/', (req: Request, res: Response): void => {
  const { name, phone, email, message } = req.body
  if (!name || !message) {
    res.status(400).json({ success: false, error: '请填写姓名和留言内容' })
    return
  }

  db.prepare('INSERT INTO contact_messages (name, phone, email, message) VALUES (?, ?, ?, ?)').run(
    name,
    phone || '',
    email || '',
    message,
  )

  res.json({ success: true, message: '留言提交成功，我们会尽快与您联系' })
})

export default router
