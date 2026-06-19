import { useState, useEffect } from 'react'
import { X } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import { useSiteStore } from '../store/siteStore.js'

export default function Popup() {
  const { config } = useSiteStore()
  const [isOpen, setIsOpen] = useState(false)

  useEffect(() => {
    if (!config?.popupEnabled) return
    const closed = sessionStorage.getItem('yuyun_popup_closed')
    if (!closed) {
      const timer = setTimeout(() => setIsOpen(true), 1500)
      return () => clearTimeout(timer)
    }
  }, [config?.popupEnabled])

  const handleClose = () => {
    setIsOpen(false)
    sessionStorage.setItem('yuyun_popup_closed', '1')
  }

  if (!config?.popupEnabled || !config.popupTitle) return null

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onClick={handleClose}
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.9, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.9, y: 20 }}
            className="relative z-10 w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden"
          >
            <div className="h-2 gradient-blue" />
            <button
              onClick={handleClose}
              className="absolute top-4 right-4 p-1.5 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
              aria-label="关闭"
            >
              <X className="w-5 h-5" />
            </button>
            <div className="p-8">
              <h3 className="text-2xl font-bold text-[#0A2540] mb-4">{config.popupTitle}</h3>
              <p className="text-gray-600 leading-relaxed mb-8 whitespace-pre-line">
                {config.popupContent}
              </p>
              {config.popupButtonText && (
                <a
                  href={config.popupButtonLink || '/contact'}
                  onClick={handleClose}
                  className="inline-flex items-center justify-center w-full px-6 py-3 rounded-lg bg-[#00A4E4] text-white font-bold hover:bg-[#0093cd] transition-colors"
                >
                  {config.popupButtonText}
                </a>
              )}
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  )
}
