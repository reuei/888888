import { useState } from 'react'
import { MessageCircle, Phone, Mail, X, ChevronUp, Users } from 'lucide-react'
import { useSiteStore } from '../store/siteStore.js'

export default function ContactFloat() {
  const [isOpen, setIsOpen] = useState(false)
  const { config } = useSiteStore()

  const toggle = () => setIsOpen(!isOpen)

  return (
    <div className="fixed right-4 bottom-4 md:right-8 md:bottom-8 z-40 flex flex-col items-end gap-3">
      <div
        className={`flex flex-col gap-3 transition-all duration-300 ${
          isOpen ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4 pointer-events-none'
        }`}
      >
        <a
          href={`tel:${config?.salesPhone || '400-800-8451'}`}
          className="flex items-center gap-3 px-4 py-3 rounded-xl bg-white text-[#0A2540] shadow-lg hover:shadow-xl border border-gray-100 hover:text-[#00A4E4] transition-all"
        >
          <Phone className="w-5 h-5 text-[#FF6B00]" />
          <div>
            <p className="text-xs text-gray-500">销售热线</p>
            <p className="font-bold text-sm">{config?.salesPhone || '400-800-8451'}</p>
          </div>
        </a>

        <a
          href={`mailto:${config?.email || 'contact@yuyun.com'}`}
          className="flex items-center gap-3 px-4 py-3 rounded-xl bg-white text-[#0A2540] shadow-lg hover:shadow-xl border border-gray-100 hover:text-[#00A4E4] transition-all"
        >
          <Mail className="w-5 h-5 text-[#00A4E4]" />
          <div>
            <p className="text-xs text-gray-500">商务邮箱</p>
            <p className="font-bold text-sm">{config?.email || 'contact@yuyun.com'}</p>
          </div>
        </a>

        <div className="flex items-center gap-3 px-4 py-3 rounded-xl bg-white text-[#0A2540] shadow-lg border border-gray-100">
          <Users className="w-5 h-5 text-[#00A4E4]" />
          <div>
            <p className="text-xs text-gray-500">官方 QQ 群</p>
            <p className="font-bold text-sm">{config?.qqGroup || '123456789'}</p>
          </div>
        </div>
      </div>

      <button
        onClick={toggle}
        className={`flex items-center justify-center w-14 h-14 rounded-full shadow-lg transition-all duration-300 ${
          isOpen ? 'bg-gray-800 text-white rotate-0' : 'bg-[#00A4E4] text-white hover:bg-[#0093cd]'
        }`}
        aria-label={isOpen ? '关闭客服' : '联系客服'}
      >
        {isOpen ? <X className="w-6 h-6" /> : <MessageCircle className="w-6 h-6" />}
      </button>

      {!isOpen && (
        <span className="hidden md:flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#FF6B00] text-white text-xs font-bold absolute -top-2 -left-2 animate-bounce">
          <ChevronUp className="w-3 h-3" />
          联系客服
        </span>
      )}
    </div>
  )
}
