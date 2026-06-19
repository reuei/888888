import { Link } from 'react-router-dom'
import { Phone, Mail, MapPin } from 'lucide-react'
import { useSiteStore } from '../store/siteStore.js'

const footerLinks = [
  { label: '首页', path: '/' },
  { label: '关于我们', path: '/about' },
  { label: '公司简介', path: '/company' },
  { label: '产品介绍', path: '/products' },
  { label: '合作伙伴', path: '/partners' },
  { label: '联系我们', path: '/contact' },
  { label: '后台管理', path: '/admin/login' },
]

export default function Footer() {
  const { config } = useSiteStore()

  return (
    <footer className="bg-[#0D0D0D] text-white pt-16 pb-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
          <div className="lg:col-span-1">
            <div className="flex items-center gap-2 mb-6">
              {config?.logo ? (
                <img src={config.logo} alt={config.siteName} className="h-10 w-auto object-contain brightness-0 invert" />
              ) : (
                <div className="h-10 w-10 rounded-lg bg-[#00A4E4] flex items-center justify-center text-white font-bold text-xl">
                  语
                </div>
              )}
              <span className="text-xl font-bold">{config?.siteName || '语云科技'}</span>
            </div>
            <div className="mb-4">
              <p className="text-gray-400 text-sm mb-2">销售电话</p>
              <a
                href={`tel:${config?.salesPhone || '400-800-8451'}`}
                className="text-[#FF6B00] text-2xl font-black tracking-wide hover:text-[#ff8533] transition-colors"
              >
                {config?.salesPhone || '400-800-8451'}
              </a>
            </div>
            <p className="text-gray-500 text-sm mt-4 leading-relaxed">
              {config?.copyright || '语云科技® 是语云科技美国有限公司在中国的注册授权'}
            </p>
          </div>

          <div>
            <h3 className="text-base font-bold mb-5 text-white">快速导航</h3>
            <ul className="space-y-3">
              {footerLinks.map((link) => (
                <li key={link.path}>
                  <Link
                    to={link.path}
                    className="text-gray-400 text-sm hover:text-[#00A4E4] transition-colors"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-base font-bold mb-5 text-white">联系方式</h3>
            <ul className="space-y-4">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 text-[#00A4E4] flex-shrink-0 mt-0.5" />
                <span className="text-gray-400 text-sm">{config?.address || '中国北京市朝阳区建国路88号SOHO现代城A座1208室'}</span>
              </li>
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 text-[#00A4E4] flex-shrink-0" />
                <a href={`mailto:${config?.email || 'contact@yuyun.com'}`} className="text-gray-400 text-sm hover:text-white transition-colors">
                  {config?.email || 'contact@yuyun.com'}
                </a>
              </li>
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 text-[#00A4E4] flex-shrink-0" />
                <a href={`tel:${config?.marketingPhone || '400-800-8541'}`} className="text-gray-400 text-sm hover:text-white transition-colors">
                  {config?.marketingPhone || '400-800-8541'}
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="text-base font-bold mb-5 text-white">官方群聊</h3>
            <div className="bg-white/5 rounded-xl p-4 border border-white/10">
              <p className="text-gray-400 text-sm mb-2">QQ 交流群</p>
              <p className="text-white font-mono font-bold text-lg">{config?.qqGroup || '123456789'}</p>
              <p className="text-gray-500 text-xs mt-2">扫码或搜索群号加入，获取最新产品资讯</p>
            </div>
          </div>
        </div>

        <div className="border-t border-white/10 pt-8">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <div className="flex flex-wrap items-center justify-center md:justify-start gap-4">
              <span>{config?.icp || '京ICP备XXXXXXXX号'}</span>
              <span>{config?.publicSecurityRecord || '京公网安备XXXXXXXXXXX号'}</span>
              <span>增值电信业务经营许可证：B1-XXXXXXXX</span>
            </div>
            <p>© {new Date().getFullYear()} {config?.siteName || '语云科技'} 版权所有</p>
          </div>
        </div>
      </div>
    </footer>
  )
}
