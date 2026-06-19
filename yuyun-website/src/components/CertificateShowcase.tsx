import { useState } from 'react'
import { X, ZoomIn } from 'lucide-react'
import { motion, AnimatePresence } from 'framer-motion'
import type { Certificate } from '../types/index.js'

interface CertificateShowcaseProps {
  certificates: Certificate[]
}

export default function CertificateShowcase({ certificates }: CertificateShowcaseProps) {
  const [selected, setSelected] = useState<Certificate | null>(null)
  const enabled = certificates.filter((c) => c.enabled)

  if (enabled.length === 0) return null

  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-14">
          <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">企业资质与荣誉</h2>
          <p className="text-gray-500 text-sm">合规经营，值得信赖的合作伙伴</p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {enabled.map((cert, index) => (
            <motion.div
              key={cert.id}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.4, delay: index * 0.1 }}
              onClick={() => setSelected(cert)}
              className="group relative bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100 hover:shadow-lg transition-all cursor-pointer"
            >
              <div className="aspect-[3/4] rounded-xl bg-white border border-gray-200 flex items-center justify-center overflow-hidden mb-4">
                {cert.image ? (
                  <img
                    src={cert.image}
                    alt={cert.title}
                    className="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-500"
                  />
                ) : (
                  <div className="text-center p-6">
                    <div className="w-16 h-16 mx-auto rounded-full bg-[#00A4E4]/10 flex items-center justify-center mb-3">
                      <ZoomIn className="w-7 h-7 text-[#00A4E4]" />
                    </div>
                    <p className="text-xs text-gray-400">点击上传证书图片</p>
                  </div>
                )}
              </div>
              <h3 className="text-base font-bold text-[#0A2540] text-center">{cert.title}</h3>
            </motion.div>
          ))}
        </div>
      </div>

      <AnimatePresence>
        {selected && (
          <div
            className="fixed inset-0 z-[100] flex items-center justify-center p-4"
            onClick={() => setSelected(null)}
          >
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              className="absolute inset-0 bg-black/80 backdrop-blur-sm"
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              className="relative z-10 max-w-3xl w-full bg-white rounded-2xl overflow-hidden shadow-2xl"
              onClick={(e) => e.stopPropagation()}
            >
              <button
                onClick={() => setSelected(null)}
                className="absolute top-4 right-4 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors z-10"
              >
                <X className="w-5 h-5" />
              </button>
              <div className="aspect-[4/3] bg-[#F6F9FC] flex items-center justify-center">
                {selected.image ? (
                  <img
                    src={selected.image}
                    alt={selected.title}
                    className="max-w-full max-h-full object-contain p-8"
                  />
                ) : (
                  <p className="text-gray-400">暂无图片</p>
                )}
              </div>
              <div className="p-6 text-center">
                <h3 className="text-xl font-bold text-[#0A2540]">{selected.title}</h3>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </section>
  )
}
