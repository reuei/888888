import { useState, useEffect } from 'react'
import { Link, useLocation } from 'react-router-dom'
import { Menu, X, Phone, Globe } from 'lucide-react'
import { useSiteStore } from '../store/siteStore.js'

const navLinks = [
  { label: '首页', path: '/' },
  { label: '关于我们', path: '/about' },
  { label: '公司简介', path: '/company' },
  { label: '产品介绍', path: '/products' },
  { label: '合作伙伴', path: '/partners' },
  { label: '联系我们', path: '/contact' },
  { label: '国际版', path: '/global' },
]

export default function Navbar() {
  const [isOpen, setIsOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const location = useLocation()
  const { config } = useSiteStore()

  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20)
    }
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  useEffect(() => {
    setIsOpen(false)
  }, [location.pathname])

  const isActive = (path: string) => {
    if (path === '/') return location.pathname === '/'
    return location.pathname.startsWith(path)
  }

  return (
    <header
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled ? 'bg-white/95 backdrop-blur-md shadow-md py-3' : 'bg-white/80 backdrop-blur-sm py-4'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between">
          <Link to="/" className="flex items-center gap-2 group">
            {config?.logo ? (
              <img src={config.logo} alt={config.siteName} className="h-9 w-auto object-contain" />
            ) : (
              <div className="h-9 w-9 rounded-lg gradient-blue flex items-center justify-center text-white font-bold text-lg">
                语
              </div>
            )}
            <span className="text-xl font-bold text-[#0A2540] group-hover:text-[#00A4E4] transition-colors">
              {config?.siteName || '语云科技'}
            </span>
          </Link>

          <nav className="hidden lg:flex items-center gap-8">
            {navLinks.map((link) => (
              <Link
                key={link.path}
                to={link.path}
                className={`text-sm font-medium transition-colors relative py-1 ${
                  isActive(link.path)
                    ? 'text-[#00A4E4]'
                    : 'text-[#1A1A1A] hover:text-[#00A4E4]'
                }`}
              >
                {link.label}
                {isActive(link.path) && (
                  <span className="absolute bottom-0 left-0 right-0 h-0.5 bg-[#00A4E4] rounded-full" />
                )}
              </Link>
            ))}
          </nav>

          <div className="hidden lg:flex items-center gap-4">
            <a
              href={`tel:${config?.salesPhone || '400-800-8451'}`}
              className="flex items-center gap-2 text-[#FF6B00] font-bold text-sm hover:opacity-80 transition-opacity"
            >
              <Phone className="w-4 h-4" />
              {config?.salesPhone || '400-800-8451'}
            </a>
            <Link
              to="/global"
              className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#0A2540] text-white text-sm font-medium hover:bg-[#00A4E4] transition-colors"
            >
              <Globe className="w-4 h-4" />
              Global
            </Link>
          </div>

          <button
            onClick={() => setIsOpen(!isOpen)}
            className="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors"
            aria-label="切换菜单"
          >
            {isOpen ? <X className="w-6 h-6 text-[#0A2540]" /> : <Menu className="w-6 h-6 text-[#0A2540]" />}
          </button>
        </div>
      </div>

      <div
        className={`lg:hidden absolute top-full left-0 right-0 bg-white shadow-lg border-t transition-all duration-300 ${
          isOpen ? 'opacity-100 visible' : 'opacity-0 invisible'
        }`}
      >
        <nav className="flex flex-col p-4 gap-2">
          {navLinks.map((link) => (
            <Link
              key={link.path}
              to={link.path}
              className={`px-4 py-3 rounded-lg text-sm font-medium transition-colors ${
                isActive(link.path)
                  ? 'bg-[#00A4E4]/10 text-[#00A4E4]'
                  : 'text-[#1A1A1A] hover:bg-gray-50'
              }`}
            >
              {link.label}
            </Link>
          ))}
          <div className="mt-2 pt-3 border-t flex items-center justify-between">
            <a
              href={`tel:${config?.salesPhone || '400-800-8451'}`}
              className="flex items-center gap-2 text-[#FF6B00] font-bold text-sm"
            >
              <Phone className="w-4 h-4" />
              {config?.salesPhone || '400-800-8451'}
            </a>
            <Link
              to="/global"
              className="flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#0A2540] text-white text-sm font-medium"
            >
              <Globe className="w-4 h-4" />
              Global
            </Link>
          </div>
        </nav>
      </div>
    </header>
  )
}
