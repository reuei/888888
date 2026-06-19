import express, { type Request, type Response, type NextFunction } from 'express'
import cors from 'cors'
import path from 'path'
import dotenv from 'dotenv'
import { fileURLToPath } from 'url'

import authRoutes from './routes/auth.js'
import siteRoutes from './routes/site.js'
import slidesRoutes from './routes/slides.js'
import productsRoutes from './routes/products.js'
import partnersRoutes from './routes/partners.js'
import linksRoutes from './routes/links.js'
import certificatesRoutes from './routes/certificates.js'
import testimonialsRoutes from './routes/testimonials.js'
import uploadRoutes from './routes/upload.js'
import contactRoutes from './routes/contact.js'

import './db.js'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

dotenv.config()

const app: express.Application = express()

app.use(cors())
app.use(express.json({ limit: '10mb' }))
app.use(express.urlencoded({ extended: true, limit: '10mb' }))

app.use('/uploads', express.static(path.join(__dirname, '..', 'public', 'uploads')))

app.use('/api/auth', authRoutes)
app.use('/api/site', siteRoutes)
app.use('/api/slides', slidesRoutes)
app.use('/api/products', productsRoutes)
app.use('/api/partners', partnersRoutes)
app.use('/api/links', linksRoutes)
app.use('/api/certificates', certificatesRoutes)
app.use('/api/testimonials', testimonialsRoutes)
app.use('/api/upload', uploadRoutes)
app.use('/api/contact', contactRoutes)

app.use('/api/health', (_req: Request, res: Response): void => {
  res.status(200).json({ success: true, message: 'ok' })
})

// eslint-disable-next-line @typescript-eslint/no-unused-vars
app.use((error: Error, _req: Request, res: Response, _next: NextFunction) => {
  console.error(error)
  res.status(500).json({
    success: false,
    error: error.message || 'Server internal error',
  })
})

app.use((req: Request, res: Response) => {
  res.status(404).json({
    success: false,
    error: 'API not found',
  })
})

export default app
