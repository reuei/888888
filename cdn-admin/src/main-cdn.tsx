import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import CdnApp from './CdnApp.tsx'

document.documentElement.classList.add('cdn-theme')

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <CdnApp />
  </StrictMode>,
)
