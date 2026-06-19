import { motion } from 'framer-motion'
import { Award, Rocket, Eye, Heart, TrendingUp } from 'lucide-react'

const milestones = [
  { year: '2018', title: '公司成立', desc: '语云科技在北京成立，开启云服务征程' },
  { year: '2019', title: '首个数据中心上线', desc: '北京、青岛双节点数据中心正式运营' },
  { year: '2020', title: '产品线扩展', desc: '推出企业财务系统与智能运维平台' },
  { year: '2021', title: '国际化布局', desc: '新加坡、莫斯科、旧金山节点投入使用' },
  { year: '2022', title: '资质认证', desc: '获得增值电信业务经营许可证等多项资质' },
  { year: '2024', title: '服务升级', desc: '全球节点突破 30 个，服务客户超过 10,000 家' },
]

const values = [
  { icon: Rocket, title: '使命', desc: '让全球企业的数字化转型更简单、更高效、更安全' },
  { icon: Eye, title: '愿景', desc: '成为全球领先的云服务与数字化解决方案提供商' },
  { icon: Heart, title: '价值观', desc: '客户至上、技术创新、诚信经营、合作共赢' },
]

const honors = [
  '国家高新技术企业',
  '增值电信业务经营许可证',
  'ISO 27001 信息安全管理体系认证',
  'ISO 9001 质量管理体系认证',
  '企业信用评价 AAA 级信用企业',
  '云计算服务能力标准符合性证书',
]

export default function Company() {
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
            <h1 className="text-3xl md:text-5xl font-black mb-4">公司简介</h1>
            <p className="text-gray-300 max-w-2xl mx-auto">发展历程、愿景使命与荣誉资质</p>
          </motion.div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">发展历程</h2>
            <p className="text-gray-500 text-sm">稳步前行，持续创新</p>
          </div>
          <div className="relative">
            <div className="absolute left-4 md:left-1/2 top-0 bottom-0 w-0.5 bg-[#00A4E4]/20" />
            {milestones.map((item, index) => (
              <motion.div
                key={item.year}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: index * 0.1 }}
                className={`relative flex items-center mb-10 ${
                  index % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'
                }`}
              >
                <div className="hidden md:block w-1/2" />
                <div className="absolute left-4 md:left-1/2 -translate-x-1/2 w-4 h-4 rounded-full bg-[#00A4E4] border-4 border-white shadow-md" />
                <div className="ml-12 md:ml-0 md:w-1/2 md:px-12">
                  <div className="bg-[#F6F9FC] rounded-2xl p-6 border border-gray-100">
                    <span className="inline-block px-3 py-1 rounded-full bg-[#00A4E4]/10 text-[#00A4E4] text-sm font-bold mb-2">
                      {item.year}
                    </span>
                    <h3 className="text-lg font-bold text-[#0A2540] mb-2">{item.title}</h3>
                    <p className="text-sm text-gray-600">{item.desc}</p>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 bg-[#F6F9FC]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {values.map((item, index) => {
              const Icon = item.icon
              return (
                <motion.div
                  key={item.title}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ duration: 0.4, delay: index * 0.1 }}
                  className="bg-white rounded-2xl p-8 border border-gray-100 text-center hover:shadow-lg transition-shadow"
                >
                  <div className="w-16 h-16 mx-auto rounded-2xl bg-[#00A4E4]/10 flex items-center justify-center mb-5">
                    <Icon className="w-8 h-8 text-[#00A4E4]" />
                  </div>
                  <h3 className="text-xl font-bold text-[#0A2540] mb-3">{item.title}</h3>
                  <p className="text-gray-600 leading-relaxed">{item.desc}</p>
                </motion.div>
              )
            })}
          </div>
        </div>
      </section>

      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-14">
            <h2 className="text-2xl md:text-3xl font-bold text-[#0A2540] mb-3">荣誉资质</h2>
            <p className="text-gray-500 text-sm">权威认证，实力见证</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {honors.map((honor, index) => (
              <motion.div
                key={honor}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.4, delay: index * 0.05 }}
                className="flex items-center gap-4 p-5 rounded-xl bg-[#F6F9FC] border border-gray-100"
              >
                <Award className="w-8 h-8 text-[#FF6B00] flex-shrink-0" />
                <span className="font-medium text-[#0A2540]">{honor}</span>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      <section className="py-20 gradient-dark relative overflow-hidden">
        <div className="absolute inset-0 bg-grid-pattern opacity-20" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            {[
              { value: '99.99%', label: '服务可用性' },
              { value: '30+', label: '全球节点' },
              { value: '10,000+', label: '服务企业' },
              { value: '7×24', label: '技术支持' },
            ].map((stat) => (
              <div key={stat.label}>
                <div className="flex items-center justify-center gap-2 mb-2">
                  <TrendingUp className="w-5 h-5 text-[#00A4E4]" />
                  <span className="text-3xl md:text-4xl font-black text-white">{stat.value}</span>
                </div>
                <p className="text-gray-400 text-sm">{stat.label}</p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </main>
  )
}
