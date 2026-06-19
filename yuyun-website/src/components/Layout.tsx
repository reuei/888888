import { useEffect } from 'react'
import Navbar from './Navbar.js'
import Footer from './Footer.js'
import Popup from './Popup.js'
import ContactFloat from './ContactFloat.js'
import { useSiteStore } from '../store/siteStore.js'

export default function Layout({ children }: { children: React.ReactNode }) {
  const { fetchConfig } = useSiteStore()

  useEffect(() => {
    fetchConfig()
  }, [fetchConfig])

  return (
    <div className="min-h-screen flex flex-col">
      <Navbar />
      <div className="flex-1">{children}</div>
      <Footer />
      <Popup />
      <ContactFloat />
    </div>
  )
}
