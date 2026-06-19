import { useEffect, useState } from 'react'
import { motion } from 'framer-motion'
import { Shield, Globe, Zap, Headphones } from 'lucide-react'
import HeroSlider from '../components/HeroSlider.js'
import ServiceCard from '../components/ServiceCard.js'
import PartnerMarquee from '../components/PartnerMarquee.js'
import WorldMap from '../components/WorldMap.js'
import CertificateShowcase from '../components/CertificateShowcase.js'
import { getSlides, getProducts, getPartners, getCertificates } from '../api/index.js'
import type { Slide, Product, Partner, Certificate } from '../types/index.js'

const highlights = [
  { icon: Globe, title: '全球覆盖', desc: '业务遍及中东、欧洲、亚太、北美、澳洲' },
  { icon: Shield, title: '安全合规', desc: '多项资质认证，保障数据与业务安全' },
  { icon: Zap, title: '稳定高效', desc: '99.99% 服务可用性承诺' },
  { icon: Headphones, title: '7×24 支持', desc: '专业技术团队全天候响应' },
]

export default function Home() {
  const [slides, setSlides] = useState<Slide[]>([])
  const [products, setProducts] = useState<Product[]>([])
  const [partners, setPartners] = useState<Partner[]>([])
  const [certificates, setCertificates] = useState<Certificate[]>([])

  useEffect(() => {
    const loadData = async () => {
      const [slidesRes, productsRes, partnersRes, certsRes] = await Promise.all([
        getSlides(),
        getProducts(),
        getPartners(),
        getCertificates(),
      ])
      if (slidesRes.success) setSlides((slidesRes.data as Slide[]).filter((s) => s.enabled))
      if (productsRes.success) setProducts((productsRes.data as Product[]).filter((p) => p.enabled))
      if (partnersRes.success) setPartners((partnersRes.data as Partner[]).filter((p) => p.enabled))
      if (certsRes.success) setCertificates((certsRes.data as Certificate[]).filter((c) => c.enabled))
    }
    loadData()
  }, [])

  return (
    <main>
      <HeroSlider slides={slides} />

      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {highlights.map((item, index) => {
              const Icon = item.icon
              return (
                <motion.div
                  key={item.title}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.4, delay: index * 0.1 }}
                  className="flex items-start gap-4 p-5 rounded-2xl bg-[#F6F9FC] hover:bg-white hover:shadow-lg transition-all border border-transparent hover:border-gray-100"
                >
                  <div className="h-11 w-11 rounded-xl bg-[#00A4E4]/10 flex items-center justify-center flex-shrink-0">
                    <Icon className="w-6 h-6 text-[#00A4E4]" />
                  </div>
                  <div>
                    <h3 className="font-bold text-[#0A2540] mb-1">{item.title}</h3>
                    <p className="text-xs text-gray-500 leading-relaxed">{item.desc}</p>
                  </div>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>

      <section className="py-20 bg-[#F6F9FC]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">核心业务与产品</h2>
            <p className="text-gray-500 text-sm max-w-2xl mx-auto">
              提供云计算、IDC、企业数字化、财务系统等一站式解决方案，助力企业高效增长
            </p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {products.map((product, index) => (
              <ServiceCard key={product.id} product={product} index={index} />
            ))}
          </div>
        </div>
      </section>

      <WorldMap />

      <CertificateShowcase certificates={certificates} />

      <PartnerMarquee partners={partners} />

      <section className="py-20 gradient-dark relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-pattern opacity-20" />
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
          <h2 className="text-3xl md:text-4xl font-black text-white mb-6">准备好开启数字化转型了吗？</h2>
          <p className="text-gray-300 mb-8 text-lg">立即联系语云科技专业团队，获取专属解决方案与优惠报价</p>
          <a
            href="/contact"
            className="inline-flex items-center gap-2 px-8 py-4 rounded-lg bg-[#FF6B00] text-white font-bold hover:bg-[#e66000] transition-colors shadow-lg"
          >
            免费咨询
          </a>
        </div>
      </section>
    </main>
  )
}
