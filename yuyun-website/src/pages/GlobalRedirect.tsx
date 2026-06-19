import { useEffect, useState } from 'react'
import { Globe, ArrowRight } from 'lucide-react'
import { motion } from 'framer-motion'

export default function GlobalRedirect() {
  const [countdown, setCountdown] = useState(3)

  useEffect(() => {
    const timer = setInterval(() => {
      setCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(timer)
          window.location.href = 'https://cloud.loveym.cloud'
          return 0
        }
        return prev - 1
      })
    }, 1000)
    return () => clearInterval(timer)
  }, [])

  return (
    <main className="min-h-screen gradient-dark flex items-center justify-center px-4">
      <div className="absolute inset-0 bg-grid-pattern opacity-20" />
      <motion.div
        initial={{ opacity: 0, scale: 0.95 }}
        animate={{ opacity: 1, scale: 1 }}
        className="relative z-10 max-w-lg w-full bg-white rounded-2xl p-10 text-center shadow-2xl"
      >
        <div className="w-20 h-20 mx-auto rounded-2xl bg-[#00A4E4]/10 flex items-center justify-center mb-6">
          <Globe className="w-10 h-10 text-[#00A4E4]" />
        </div>
        <h1 className="text-2xl font-bold text-[#0A2540] mb-3">正在前往国际版官网</h1>
        <p className="text-gray-500 mb-8">您即将跳转至语云科技国际版官网 https://cloud.loveym.cloud</p>
        <div className="flex items-center justify-center gap-2 mb-8">
          <span className="text-4xl font-black text-[#FF6B00]">{countdown}</span>
          <span className="text-gray-500">秒后自动跳转</span>
        </div>
        <a
          href="https://cloud.loveym.cloud"
          className="inline-flex items-center gap-2 px-8 py-3 rounded-lg bg-[#00A4E4] text-white font-bold hover:bg-[#0093cd] transition-colors"
        >
          立即跳转 <ArrowRight className="w-4 h-4" />
        </a>
      </motion.div>
    </main>
  )
}
