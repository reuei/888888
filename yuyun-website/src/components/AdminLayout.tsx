import { Link, useLocation, useNavigate } from 'react-router-dom'
import {
  LayoutDashboard,
  Settings,
  Image,
  Package,
  Handshake,
  Link as LinkIcon,
  Award,
  MessageSquare,
  LogOut,
  Menu,
  X,
} from 'lucide-react'
import { useState } from 'react'
import { useAuthStore } from '../store/authStore.js'

const menuItems = [
  { label: '仪表盘', path: '/admin', icon: LayoutDashboard },
  { label: '站点配置', path: '/admin/settings', icon: Settings },
  { label: '轮播图管理', path: '/admin/slides', icon: Image },
  { label: '产品管理', path: '/admin/products', icon: Package },
  { label: '合作伙伴', path: '/admin/partners', icon: Handshake },
  { label: '友情链接', path: '/admin/links', icon: LinkIcon },
  { label: '资质证书', path: '/admin/certificates', icon: Award },
  { label: '用户评价', path: '/admin/testimonials', icon: MessageSquare },
]

export default function AdminLayout({ children }: { children: React.ReactNode }) {
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const location = useLocation()
  const navigate = useNavigate()
  const { logout, admin } = useAuthStore()

  const handleLogout = () => {
    logout()
    navigate('/admin/login')
  }

  return (
    <div className="min-h-screen bg-[#F6F9FC] flex">
      <aside
        className={`fixed inset-y-0 left-0 z-50 w-64 bg-[#0A2540] text-white transform transition-transform duration-300 lg:translate-x-0 lg:static ${
          sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="h-16 flex items-center justify-between px-6 border-b border-white/10">
          <Link to="/admin" className="text-lg font-bold">语云后台</Link>
          <button onClick={() => setSidebarOpen(false)} className="lg:hidden">
            <X className="w-5 h-5" />
          </button>
        </div>
        <nav className="p-4 space-y-1">
          {menuItems.map((item) => {
            const Icon = item.icon
            const active = location.pathname === item.path
            return (
              <Link
                key={item.path}
                to={item.path}
                className={`flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors ${
                  active ? 'bg-[#00A4E4] text-white' : 'text-gray-300 hover:bg-white/10 hover:text-white'
                }`}
              >
                <Icon className="w-5 h-5" />
                {item.label}
              </Link>
            )
          })}
        </nav>
        <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-white/10">
          <div className="flex items-center justify-between mb-3">
            <span className="text-sm text-gray-300">{admin?.username || '管理员'}</span>
          </div>
          <button
            onClick={handleLogout}
            className="flex items-center gap-2 w-full px-4 py-2 rounded-lg text-sm text-gray-300 hover:bg-white/10 hover:text-white transition-colors"
          >
            <LogOut className="w-4 h-4" />
            退出登录
          </button>
        </div>
      </aside>

      <div className="flex-1 flex flex-col min-w-0">
        <header className="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-8">
          <button
            onClick={() => setSidebarOpen(true)}
            className="lg:hidden p-2 rounded-lg hover:bg-gray-100"
          >
            <Menu className="w-5 h-5" />
          </button>
          <div className="flex items-center gap-4 ml-auto">
            <Link to="/" className="text-sm text-[#00A4E4] hover:underline">访问前台</Link>
            <span className="text-sm text-gray-500 hidden sm:inline">欢迎，{admin?.username || '管理员'}</span>
          </div>
        </header>
        <main className="flex-1 p-4 lg:p-8 overflow-auto">{children}</main>
      </div>

      {sidebarOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-40 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}
    </div>
  )
}
