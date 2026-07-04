import { useState, useEffect } from 'react';
import { NavLink, useNavigate } from 'react-router-dom';
import { Shield, Search, Headphones, Grid3X3, LayoutDashboard, User, Menu, X, LogOut } from 'lucide-react';

interface SalesHeaderProps {
  loggedIn?: boolean;
  onLogout?: () => void;
}

const navItems = [
  { key: '/', label: '首页' },
  { key: '/buy-nodes', label: '产品' },
  { key: '/buy-source', label: '快速开始' },
  { key: '/updates', label: '能力' },
];

const searchKeywords = ['边缘加速', 'DDoS 防护', 'WAF 规则', '套餐订购', '工单支持', '对象存储'];

export default function SalesHeader({ loggedIn = false, onLogout }: SalesHeaderProps) {
  const navigate = useNavigate();
  const [mobileOpen, setMobileOpen] = useState(false);
  const [keywordIndex, setKeywordIndex] = useState(0);

  useEffect(() => {
    const timer = setInterval(() => {
      setKeywordIndex((prev) => (prev + 1) % searchKeywords.length);
    }, 2500);
    return () => clearInterval(timer);
  }, []);

  return (
    <header className="sticky top-0 z-50 border-b border-[var(--sales-border)] bg-white/90 backdrop-blur">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        {/* Logo + nav */}
        <div className="flex items-center gap-6">
          <button
            onClick={() => navigate('/')}
            className="flex items-center gap-2 group shrink-0"
          >
            <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-[#0052d9] to-[#4656ff] text-white flex items-center justify-center shadow-md shadow-[#0052d9]/20 group-hover:scale-105 transition-transform">
              <Shield size={18} />
            </div>
            <span className="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-[#0052d9] to-[#606eff]">
              EdgeOne
            </span>
          </button>

          <nav className="hidden md:flex items-center gap-1">
            {navItems.map((item) => (
              <NavLink
                key={item.key}
                to={item.key}
                className={({ isActive }) =>
                  `px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                    isActive
                      ? 'text-[#0052d9] bg-[#0052d9]/8'
                      : 'text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5'
                  }`
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>
        </div>

        {/* Center search */}
        <div className="hidden lg:flex flex-1 max-w-md mx-4">
          <button className="w-full flex items-center gap-2 h-9 px-4 rounded-full bg-[var(--sales-bg)] border border-[var(--sales-border)] text-sm text-[var(--sales-text-secondary)] hover:border-[#0052d9]/40 hover:text-[#0052d9] transition-colors">
            <Search size={15} />
            <span className="flex-1 text-left truncate">
              搜索
              <span className="text-[#0052d9] ml-1">{searchKeywords[keywordIndex]}</span>
            </span>
          </button>
        </div>

        {/* Right actions */}
        <div className="hidden md:flex items-center gap-1 shrink-0">
          <button
            onClick={() => navigate('/announcements')}
            className="px-3 py-2 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5 transition-colors"
          >
            工单
          </button>
          <button
            onClick={() => navigate('/buy-nodes')}
            className="px-3 py-2 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5 transition-colors"
          >
            产品目录
          </button>
          <button
            onClick={() => navigate('/user')}
            className="px-3 py-2 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5 transition-colors"
          >
            控制台
          </button>

          {loggedIn ? (
            <>
              <NavLink
                to="/user"
                className="ml-2 flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)] hover:bg-black/5 transition-colors"
              >
                <User size={16} />
                用户中心
              </NavLink>
              <button
                onClick={onLogout}
                className="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-danger hover:bg-danger/10 transition-colors"
              >
                <LogOut size={16} />
                退出
              </button>
            </>
          ) : (
            <>
              <button
                onClick={() => navigate('/login')}
                className="ml-2 px-4 py-2 rounded-lg text-sm font-medium text-[var(--sales-text)] hover:bg-black/5 transition-colors"
              >
                登录
              </button>
              <button
                onClick={() => navigate('/login')}
                className="px-4 py-2 rounded-md text-sm font-medium text-white bg-gradient-to-r from-[#0052d9] to-[#4656ff] shadow-md shadow-[#0052d9]/20 hover:shadow-[#0052d9]/35 hover:-translate-y-0.5 transition-all"
              >
                免费注册
              </button>
            </>
          )}
        </div>

        {/* Mobile toggle */}
        <button
          className="md:hidden p-2 rounded-lg text-[var(--sales-text-secondary)] hover:bg-black/5"
          onClick={() => setMobileOpen(!mobileOpen)}
        >
          {mobileOpen ? <X size={20} /> : <Menu size={20} />}
        </button>
      </div>

      {mobileOpen && (
        <div className="md:hidden border-t border-[var(--sales-border)] bg-white">
          <div className="px-4 py-3">
            <button className="w-full flex items-center gap-2 h-10 px-4 mb-3 rounded-full bg-[var(--sales-bg)] border border-[var(--sales-border)] text-sm text-[var(--sales-text-secondary)]">
              <Search size={15} />
              搜索 {searchKeywords[keywordIndex]}
            </button>
            <nav className="space-y-1">
              {navItems.map((item) => (
                <NavLink
                  key={item.key}
                  to={item.key}
                  onClick={() => setMobileOpen(false)}
                  className={({ isActive }) =>
                    `flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium ${
                      isActive
                        ? 'text-[#0052d9] bg-[#0052d9]/8'
                        : 'text-[var(--sales-text-secondary)] hover:bg-black/5'
                    }`
                  }
                >
                  {item.label}
                </NavLink>
              ))}
              <button
                onClick={() => { setMobileOpen(false); navigate('/announcements'); }}
                className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:bg-black/5"
              >
                <Headphones size={16} />
                工单
              </button>
              <button
                onClick={() => { setMobileOpen(false); navigate('/buy-nodes'); }}
                className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:bg-black/5"
              >
                <Grid3X3 size={16} />
                产品目录
              </button>
              <button
                onClick={() => { setMobileOpen(false); navigate('/user'); }}
                className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:bg-black/5"
              >
                <LayoutDashboard size={16} />
                控制台
              </button>
              {loggedIn ? (
                <>
                  <NavLink
                    to="/user"
                    onClick={() => setMobileOpen(false)}
                    className="flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-[var(--sales-text-secondary)] hover:bg-black/5"
                  >
                    <User size={16} />
                    用户中心
                  </NavLink>
                  <button
                    onClick={() => {
                      setMobileOpen(false);
                      onLogout?.();
                    }}
                    className="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-danger hover:bg-danger/10"
                  >
                    <LogOut size={16} />
                    退出
                  </button>
                </>
              ) : (
                <button
                  onClick={() => {
                    setMobileOpen(false);
                    navigate('/login');
                  }}
                  className="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-[#0052d9] to-[#4656ff]"
                >
                  登录 / 注册
                </button>
              )}
            </nav>
          </div>
        </div>
      )}
    </header>
  );
}
