import { motion } from 'framer-motion'
import { MapPin, Phone, Mail, Users, Building2, Target } from 'lucide-react'
import { useSiteStore } from '../store/siteStore.js'

export default function About() {
  const { config } = useSiteStore()

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
            <h1 className="text-3xl md:text-5xl font-black mb-4">关于我们</h1>
            <p className="text-gray-300 max-w-2xl mx-auto">了解语云科技的发展历程、企业愿景与核心价值观</p>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <motion.div
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
            >
              <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-6">
                {config?.siteName || '语云科技'} — 全球云服务与数字化解决方案专家
              </h2>
              <div className="space-y-4 text-gray-600 leading-relaxed">
                <p>
                  语云科技致力于为企业和组织提供安全、稳定、高效的云计算基础设施与数字化解决方案。我们的业务覆盖云计算、IDC
                  数据中心、企业财务系统、智能运维及全球化网络加速等领域。
                </p>
                <p>
                  自成立以来，语云科技始终坚持技术创新与客户至上的服务理念，服务客户遍及互联网、金融、制造、教育、政府等多个行业。我们在中东、欧洲、亚太、北美及澳洲等地区建立了完善的服务节点，能够为全球客户提供本地化、低延迟的技术支持。
                </p>
                <p>
                  语云科技® 是语云科技美国有限公司在中国的注册授权，严格遵循中国企业官网标准及行业合规要求，持有营业执照、增值电信业务经营许可证等多项资质。
                </p>
              </div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              className="grid grid-cols-2 gap-4"
            >
              {[
                { icon: Building2, label: '成立时间', value: '2018 年' },
                { icon: Users, label: '服务客户', value: '10,000+' },
                { icon: Target, label: '全球节点', value: '30+' },
                { icon: MapPin, label: '覆盖区域', value: '全球 6 大洲' },
              ].map((item) => {
                const Icon = item.icon
                return (
                  <div
                    key={item.label}
                    className="p-6 rounded-2xl bg-[#F6F9FC] border border-gray-100 text-center"
                  >
                    <Icon className="w-8 h-8 text-[#00A4E4] mx-auto mb-3" />
                    <p className="text-2xl font-black text-[#0A2540] mb-1">{item.value}</p>
                    <p className="text-sm text-gray-500">{item.label}</p>
                  </div>
                )
              })}
            </motion.div>
          </div>
        </div>
      </section>

      <section className="py-20 bg-[#F6F9FC]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              className="lg:col-span-2 bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100"
            >
              <div className="aspect-video w-full">
                <iframe
                  src="https://map.baidu.com/search/%E5%8C%97%E4%BA%AC%E5%B8%82"
                  title="公司地图"
                  className="w-full h-full border-0"
                  allowFullScreen
                  loading="lazy"
                />
              </div>
            </motion.div>
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: 0.1 }}
              className="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 h-fit"
            >
              <h3 className="text-xl font-bold text-[#0A2540] mb-6">联系信息</h3>
              <div className="space-y-5">
                <div className="flex items-start gap-3">
                  <Building2 className="w-5 h-5 text-[#00A4E4] mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-500">公司名称</p>
                    <p className="font-medium text-[#0A2540]">{config?.siteName || '语云科技'}</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <MapPin className="w-5 h-5 text-[#00A4E4] mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-500">公司地址</p>
                    <p className="font-medium text-[#0A2540]">{config?.address || '中国北京市朝阳区建国路88号SOHO现代城A座1208室'}</p>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Phone className="w-5 h-5 text-[#FF6B00] mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-500">营销电话</p>
                    <a href={`tel:${config?.marketingPhone || '400-800-8541'}`} className="font-bold text-[#FF6B00] text-lg">
                      {config?.marketingPhone || '400-800-8541'}
                    </a>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Mail className="w-5 h-5 text-[#00A4E4] mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-500">商务邮箱</p>
                    <a href={`mailto:${config?.email || 'contact@yuyun.com'}`} className="font-medium text-[#0A2540] hover:text-[#00A4E4]">
                      {config?.email || 'contact@yuyun.com'}
                    </a>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <Users className="w-5 h-5 text-[#00A4E4] mt-0.5" />
                  <div>
                    <p className="text-sm text-gray-500">官方群聊</p>
                    <p className="font-medium text-[#0A2540]">QQ 群：{config?.qqGroup || '123456789'}</p>
                  </div>
                </div>
              </div>
            </motion.div>
          </div>
        </div>
      </section>
    </main>
  )
}
