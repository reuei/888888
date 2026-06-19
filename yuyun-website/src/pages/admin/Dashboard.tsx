import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import {
  Image,
  Package,
  Handshake,
  Link as LinkIcon,
  Award,
  MessageSquare,
  ArrowRight,
  Settings,
} from 'lucide-react'
import { getSlides, getProducts, getPartners, getLinks, getCertificates, getTestimonials } from '../../api/index.js'

const statsConfig = [
  { key: 'settings', label: '站点配置', icon: Settings, path: '/admin/settings', color: 'bg-indigo-500' },
  { key: 'slides', label: '轮播图', icon: Image, path: '/admin/slides', color: 'bg-blue-500' },
  { key: 'products', label: '产品', icon: Package, path: '/admin/products', color: 'bg-green-500' },
  { key: 'partners', label: '合作伙伴', icon: Handshake, path: '/admin/partners', color: 'bg-purple-500' },
  { key: 'links', label: '友情链接', icon: LinkIcon, path: '/admin/links', color: 'bg-orange-500' },
  { key: 'certificates', label: '资质证书', icon: Award, path: '/admin/certificates', color: 'bg-red-500' },
  { key: 'testimonials', label: '用户评价', icon: MessageSquare, path: '/admin/testimonials', color: 'bg-teal-500' },
]

export default function Dashboard() {
  const [stats, setStats] = useState<Record<string, number>>({})

  useEffect(() => {
    const load = async () => {
      const [
        slidesRes,
        productsRes,
        partnersRes,
        linksRes,
        certsRes,
        testimonialsRes,
      ] = await Promise.all([
        getSlides(),
        getProducts(),
        getPartners(),
        getLinks(),
        getCertificates(),
        getTestimonials(),
      ])
      setStats({
        settings: 1,
        slides: slidesRes.success ? (slidesRes.data as unknown[]).length : 0,
        products: productsRes.success ? (productsRes.data as unknown[]).length : 0,
        partners: partnersRes.success ? (partnersRes.data as unknown[]).length : 0,
        links: linksRes.success ? (linksRes.data as unknown[]).length : 0,
        certificates: certsRes.success ? (certsRes.data as unknown[]).length : 0,
        testimonials: testimonialsRes.success ? (testimonialsRes.data as unknown[]).length : 0,
      })
    }
    load()
  }, [])

  return (
    <div>
      <h1 className="text-2xl font-bold text-[#0A2540] mb-6">仪表盘</h1>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {statsConfig.map((item) => {
          const Icon = item.icon
          return (
            <Link
              key={item.key}
              to={item.path}
              className="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow group"
            >
              <div className="flex items-start justify-between mb-4">
                <div className={`w-12 h-12 rounded-xl ${item.color} bg-opacity-10 flex items-center justify-center`}>
                  <Icon className={`w-6 h-6 ${item.color.replace('bg-', 'text-')}`} />
                </div>
                <ArrowRight className="w-5 h-5 text-gray-300 group-hover:text-[#00A4E4] transition-colors" />
              </div>
              <p className="text-gray-500 text-sm">{item.label}</p>
              <p className="text-3xl font-black text-[#0A2540]">{stats[item.key] ?? 0}</p>
            </Link>
          )
        })}
      </div>

      <div className="mt-8 bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <h2 className="text-lg font-bold text-[#0A2540] mb-4">快速入口</h2>
        <div className="flex flex-wrap gap-3">
          {statsConfig.map((item) => (
            <Link
              key={item.key}
              to={item.path}
              className="px-4 py-2 rounded-lg bg-[#F6F9FC] text-sm text-[#0A2540] hover:bg-[#00A4E4]/10 hover:text-[#00A4E4] transition-colors"
            >
              管理{item.label}
            </Link>
          ))}
        </div>
      </div>
    </div>
  )
}
