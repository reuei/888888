import { useState } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { Menu, X, Shield } from 'lucide-react';

const navItems = [
  { key: '/cdn', label: '首页' },
  { key: '/cdn/activity', label: '🎁 活动中心' },
  { key: '/cdn/agent', label: '🔥 合作代理' },
  { key: '/cdn/pricing', label: '价格' },
  { key: '/cdn/contact', label: '联系我们' },
];

const mobileMenuItems = [
  { key: '/cdn', label: '首页' },
  { key: '/cdn/activity', label: '🎁 活动中心' },
  { key: '/cdn/agent', label: '🔥 合作代理' },
  { key: '/cdn/pricing', label: '价格' },
  { key: '/cdn/contact', label: '联系我们' },
  { key: '/cdn/report', label: '站点举报' },
  { key: '/cdn/faq', label: '常见问题' },
  { key: '/cdn/docs', label: '接入文档' },
];

export default function CdnHeader() {
  const navigate = useNavigate();
  const [mobileOpen, setMobileOpen] = useState(false);

  return (
    <>
      <header className="fixed top-0 left-0 right-0 z-50">
        <div className="absolute inset-0 bg-white/80 backdrop-blur-xl border-b border-white/40" />
        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <button
            onClick={() => navigate('/cdn')}
            className="flex items-center gap-2 group"
          >
            <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center shadow-lg shadow-blue-600/20">
              <Shield size={20} />
            </div>
            <span className="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-800">
              6cdn
            </span>
          </button>

          <nav className="hidden md:flex items-center gap-2">
            {navItems.map((item) => (
              <NavLink
                key={item.key}
                to={item.key}
                className={({ isActive }) =>
                  `px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                    isActive
                      ? 'text-blue-600 bg-blue-50'
                      : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50/50'
                  }`
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>

          <div className="hidden md:flex items-center gap-3">
            <NavLink
              to="/cdn/docs"
              className="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50/50 transition-all"
            >
              接入文档
            </NavLink>
            <button
              onClick={() => navigate('/cdn/login')}
              className="px-5 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-800 shadow-md shadow-blue-600/20 hover:shadow-lg hover:-translate-y-0.5 transition-all"
            >
              控制台
            </button>
          </div>

          <button
            className="md:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100"
            onClick={() => setMobileOpen(true)}
          >
            <Menu size={24} />
          </button>
        </div>
      </header>

      {mobileOpen && (
        <div className="fixed inset-0 z-[60]">
          <div className="absolute inset-0 bg-black/30 backdrop-blur-sm" onClick={() => setMobileOpen(false)} />
          <div className="absolute top-0 right-0 w-80 h-full bg-white shadow-2xl animate-slideIn">
            <div className="p-6">
              <div className="flex items-center justify-between mb-6">
                <div className="flex items-center gap-2">
                  <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center">
                    <Shield size={20} />
                  </div>
                  <span className="text-xl font-bold text-gray-800">6cdn</span>
                </div>
                <button
                  onClick={() => setMobileOpen(false)}
                  className="p-2 rounded-lg hover:bg-gray-100"
                >
                  <X size={20} />
                </button>
              </div>

              <nav className="space-y-1">
                {mobileMenuItems.map((item) => (
                  <NavLink
                    key={item.key}
                    to={item.key}
                    onClick={() => setMobileOpen(false)}
                    className={({ isActive }) =>
                      `flex items-center gap-2 px-4 py-3 rounded-lg text-sm font-medium ${
                        isActive
                          ? 'text-blue-600 bg-blue-50'
                          : 'text-gray-700 hover:bg-gray-50'
                      }`
                    }
                  >
                    {item.label}
                  </NavLink>
                ))}
              </nav>

              <div className="mt-6">
                <button
                  onClick={() => { setMobileOpen(false); navigate('/cdn/login'); }}
                  className="w-full px-5 py-3 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-800"
                >
                  登录控制台
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}