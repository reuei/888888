import { useState } from 'react'
import { motion } from 'framer-motion'
import { MapPin, Phone, Mail, Clock, Send, CheckCircle2 } from 'lucide-react'
import { useSiteStore } from '../store/siteStore.js'
import { submitContact } from '../api/index.js'

export default function Contact() {
  const { config } = useSiteStore()
  const [form, setForm] = useState({ name: '', phone: '', email: '', message: '' })
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!form.name || !form.message) return
    setLoading(true)
    const res = await submitContact(form)
    setLoading(false)
    if (res.success) {
      setSuccess(true)
      setForm({ name: '', phone: '', email: '', message: '' })
      setTimeout(() => setSuccess(false), 5000)
    }
  }

  return (
    <main className="pt-24 pb-20">
      <section className="relative py-20 gradient-dark overflow-hidden">
        <div className="absolute inset-0 bg-grid-pattern opacity-20" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            className="text-center text-white"
          >
            <h1 className="text-3xl md:text-5xl font-black mb-4">联系我们</h1>
            <p className="text-gray-300 max-w-2xl mx-auto">有任何问题或合作意向，欢迎随时与我们联系</p>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-8"
            >
              <h2 className="text-xl font-bold text-[#0A2540] mb-6">在线留言</h2>
              {success && (
                <div className="mb-6 flex items-center gap-2 p-4 rounded-xl bg-green-50 text-green-700">
                  <CheckCircle2 className="w-5 h-5" />
                  <span>留言提交成功，我们会尽快与您联系！</span>
                </div>
              )}
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">您的姓名 *</label>
                    <input
                      type="text"
                      required
                      value={form.name}
                      onChange={(e) => setForm({ ...form, name: e.target.value })}
                      className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all"
                      placeholder="请输入姓名"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">联系电话</label>
                    <input
                      type="tel"
                      value={form.phone}
                      onChange={(e) => setForm({ ...form, phone: e.target.value })}
                      className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all"
                      placeholder="请输入电话"
                    />
                  </div>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">电子邮箱</label>
                  <input
                    type="email"
                    value={form.email}
                    onChange={(e) => setForm({ ...form, email: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all"
                    placeholder="请输入邮箱"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">留言内容 *</label>
                  <textarea
                    required
                    rows={5}
                    value={form.message}
                    onChange={(e) => setForm({ ...form, message: e.target.value })}
                    className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-[#00A4E4] focus:ring-2 focus:ring-[#00A4E4]/20 outline-none transition-all resize-none"
                    placeholder="请描述您的需求或问题"
                  />
                </div>
                <button
                  type="submit"
                  disabled={loading}
                  className="inline-flex items-center gap-2 px-8 py-3 rounded-lg bg-[#00A4E4] text-white font-bold hover:bg-[#0093cd] transition-colors disabled:opacity-60"
                >
                  <Send className="w-4 h-4" />
                  {loading ? '提交中...' : '提交留言'}
                </button>
              </form>
            </motion.div>

            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="space-y-6"
            >
              <div className="bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-lg bg-[#00A4E4]/10 flex items-center justify-center">
                    <MapPin className="w-5 h-5 text-[#00A4E4]" />
                  </div>
                  <h3 className="font-bold text-[#0A2540]">公司地址</h3>
                </div>
                <p className="text-gray-600 text-sm">{config?.address || '中国北京市朝阳区建国路88号SOHO现代城A座1208室'}</p>
              </div>

              <div className="bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-lg bg-[#FF6B00]/10 flex items-center justify-center">
                    <Phone className="w-5 h-5 text-[#FF6B00]" />
                  </div>
                  <h3 className="font-bold text-[#0A2540]">联系电话</h3>
                </div>
                <p className="text-gray-500 text-xs mb-1">销售热线</p>
                <a href={`tel:${config?.salesPhone || '400-800-8451'}`} className="text-[#FF6B00] font-bold text-lg">
                  {config?.salesPhone || '400-800-8451'}
                </a>
                <p className="text-gray-500 text-xs mt-3 mb-1">营销电话</p>
                <a href={`tel:${config?.marketingPhone || '400-800-8541'}`} className="text-[#0A2540] font-bold">
                  {config?.marketingPhone || '400-800-8541'}
                </a>
              </div>

              <div className="bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-lg bg-[#00A4E4]/10 flex items-center justify-center">
                    <Mail className="w-5 h-5 text-[#00A4E4]" />
                  </div>
                  <h3 className="font-bold text-[#0A2540]">电子邮箱</h3>
                </div>
                <a href={`mailto:${config?.email || 'contact@yuyun.com'}`} className="text-gray-600 text-sm hover:text-[#00A4E4]">
                  {config?.email || 'contact@yuyun.com'}
                </a>
              </div>

              <div className="bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-lg bg-[#00A4E4]/10 flex items-center justify-center">
                    <Clock className="w-5 h-5 text-[#00A4E4]" />
                  </div>
                  <h3 className="font-bold text-[#0A2540]">服务时间</h3>
                </div>
                <p className="text-gray-600 text-sm">7 × 24 小时全天候技术支持</p>
              </div>
            </motion.div>
          </div>

          <div className="mt-12 rounded-2xl overflow-hidden border border-gray-100 aspect-[21/9]">
            <iframe
              src="https://map.baidu.com/search/%E5%8C%97%E4%BA%AC%E5%B8%82%E6%9C%9D%E9%98%B3%E5%8C%BA"
              title="公司位置地图"
              className="w-full h-full border-0"
              allowFullScreen
              loading="lazy"
            />
          </div>
        </div>
      </section>
    </main>
  )
}
