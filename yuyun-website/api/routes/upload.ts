import { Router, type Request, type Response } from 'express'
import upload from '../middleware/upload.js'
import { authenticate } from '../middleware/auth.js'

const router = Router()

router.post('/', authenticate, upload.single('file'), (req: Request, res: Response): void => {
  if (!req.file) {
    res.status(400).json({ success: false, error: '没有上传文件' })
    return
  }

  const url = `/uploads/${req.file.filename}`
  res.json({ success: true, url })
})

export default router
