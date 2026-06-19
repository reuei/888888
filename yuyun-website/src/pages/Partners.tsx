import { useEffect, useState } from 'react'
import { motion } from 'framer-motion'
import { ExternalLink } from 'lucide-react'
import { getPartners } from '../api/index.js'
import type { Partner } from '../types/index.js'

export default function Partners() {
  const [partners, setPartners] = useState<Partner[]>([])

  useEffect(() => {
    getPartners().then((res) => {
      if (res.success) {
        setPartners((res.data as Partner[]).filter((p) => p.enabled))
      }
    })
  }, [])

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
            <h1 className="text-3xl md:text-5xl font-black mb-4">合作伙伴</h1>
            <p className="text-gray-300 max-w-2xl mx-auto">携手全球领先企业，共建开放共赢的数字化生态</p>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">我们与以下企业/组织携手共进</h2>
            <p className="text-gray-500 text-sm">深度合作，共创价值</p>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {partners.map((partner, index) => (
              <motion.div
                key={partner.id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: index * 0.05 }}
                className="group relative bg-[#F6F9FC] rounded-2xl p-8 border border-gray-100 hover:shadow-lg transition-all"
              >
                <div className="aspect-[3/2] flex items-center justify-center mb-4">
                  {partner.logo ? (
                    <img
                      src={partner.logo}
                      alt={partner.name}
                      className="max-w-full max-h-full object-contain grayscale group-hover:grayscale-0 transition-all duration-300"
                    />
                  ) : (
                    <div className="text-2xl font-black text-[#0A2540]">{partner.name.charAt(0)}</div>
                  )}
                </div>
                <h3 className="text-center font-bold text-[#0A2540] mb-2">{partner.name}</h3>
                {partner.website && (
                  <a
                    href={partner.website}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex items-center justify-center gap-1 text-xs text-[#00A4E4] hover:underline"
                  >
                    访问官网 <ExternalLink className="w-3 h-3" />
                  </a>
                )}
              </motion.div>
            ))}
          </div>

          {partners.length === 0 && (
            <div className="text-center py-20 text-gray-400">
              <p>暂无合作伙伴信息</p>
            </div>
          )}
        </div>
      </section>
    </main>
  )
}
