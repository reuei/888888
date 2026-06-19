import { Router, type Request, type Response } from 'express'
import bcrypt from 'bcryptjs'
import db from '../db.js'
import { authenticate, generateToken, type AuthRequest } from '../middleware/auth.js'

const router = Router()

router.post('/login', (req: Request, res: Response): void => {
  const { username, password } = req.body
  if (!username || !password) {
    res.status(400).json({ success: false, error: '请提供用户名和密码' })
    return
  }

  const stmt = db.prepare('SELECT * FROM admin WHERE username = ?')
  const admin = stmt.get(username) as { id: number; username: string; password_hash: string } | undefined

  if (!admin || !bcrypt.compareSync(password, admin.password_hash)) {
    res.status(401).json({ success: false, error: '用户名或密码错误' })
    return
  }

  const token = generateToken({ id: admin.id, username: admin.username })
  res.json({
    success: true,
    token,
    admin: { id: admin.id, username: admin.username },
  })
})

router.get('/me', authenticate, (req: AuthRequest, res: Response): void => {
  res.json({ success: true, admin: req.admin })
})

router.put('/password', authenticate, (req: AuthRequest, res: Response): void => {
  const { oldPassword, newPassword } = req.body
  if (!oldPassword || !newPassword || newPassword.length < 6) {
    res.status(400).json({ success: false, error: '请提供旧密码和不少于6位的新密码' })
    return
  }

  const stmt = db.prepare('SELECT * FROM admin WHERE id = ?')
  const admin = stmt.get(req.admin!.id) as { id: number; password_hash: string } | undefined

  if (!admin || !bcrypt.compareSync(oldPassword, admin.password_hash)) {
    res.status(401).json({ success: false, error: '旧密码错误' })
    return
  }

  const hash = bcrypt.hashSync(newPassword, 10)
  db.prepare('UPDATE admin SET password_hash = ? WHERE id = ?').run(hash, req.admin!.id)
  res.json({ success: true, message: '密码修改成功' })
})

export default router
