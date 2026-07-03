import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import SalesApp from './SalesApp.tsx'

document.documentElement.classList.add('sales-theme')

createRoot(document.getElementById('root')!).render(
  <StrictMode>
    <SalesApp />
  </StrictMode>,
)
