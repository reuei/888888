import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  Shield,
  Zap,
  Globe,
  BarChart3,
  Server,
  AlertTriangle,
  CheckCircle,
  TrendingUp,
  TrendingDown,
  Menu,
  X,
  Settings,
  Bell,
  User,
  LogOut,
  Plus,
  ChevronRight,
  RefreshCw,
  Search,
  CreditCard,
  FileText,
  ShieldCheck,
  Cloud,
  CloudOff,
  Lock,
  LockOpen,
} from 'lucide-react';

const stats = [
  { label: '总流量', value: '2.4TB', change: '+12.5%', trend: 'up', icon: Globe, color: 'blue' },
  { label: '带宽峰值', value: '500Mbps', change: '+8.3%', trend: 'up', icon: Zap, color: 'orange' },
  { label: '防护次数', value: '1,234', change: '+25.6%', trend: 'up', icon: Shield, color: 'red' },
  { label: '缓存命中率', value: '99.2%', change: '+0.5%', trend: 'up', icon: Server, color: 'green' },
];

const sites = [
  { id: 1, name: 'example.com', status: 'running', traffic: '500GB', bandwidth: '100Mbps', cname: 'example.6cdn.net', https: true, waf: true, lastUpdate: '5分钟前' },
  { id: 2, name: 'api.example.com', status: 'running', traffic: '300GB', bandwidth: '50Mbps', cname: 'api.6cdn.net', https: true, waf: false, lastUpdate: '10分钟前' },
  { id: 3, name: 'static.example.com', status: 'running', traffic: '1.2TB', bandwidth: '200Mbps', cname: 'static.6cdn.net', https: true, waf: true, lastUpdate: '3分钟前' },
  { id: 4, name: 'test.example.com', status: 'stopped', traffic: '0GB', bandwidth: '0Mbps', cname: 'test.6cdn.net', https: false, waf: false, lastUpdate: '1小时前' },
];

const recentAlerts = [
  { id: 1, type: 'warning', title: '证书即将过期', desc: 'api.example.com 的SSL证书将在7天后过期', time: '10分钟前' },
  { id: 2, type: 'danger', title: 'DDoS攻击检测', desc: '检测到对 example.com 的DDoS攻击，已自动清洗', time: '30分钟前' },
  { id: 3, type: 'info', title: '节点切换', desc: 'static.example.com 自动切换到备用节点', time: '1小时前' },
];

const trafficData = {
  labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00'],
  values: [20, 15, 35, 50, 45, 30],
};

const navItems = [
  { key: 'dashboard', label: '控制台', icon: BarChart3 },
  { key: 'sites', label: '站点管理', icon: Globe },
  { key: 'packages', label: '套餐管理', icon: Server },
  { key: 'finance', label: '费用中心', icon: CreditCard },
  { key: 'docs', label: '文档中心', icon: FileText },
  { key: 'waf', label: 'WAF规则', icon: ShieldCheck },
  { key: 'settings', label: '系统设置', icon: Settings },
];

export default function CdnDashboard() {
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [activeNav, setActiveNav] = useState('dashboard');

  useEffect(() => {
    const role = localStorage.getItem('cdn-role');
    if (!role) {
      navigate('/cdn/login');
    }
  }, [navigate]);

  const handleLogout = () => {
    localStorage.removeItem('cdn-role');
    navigate('/cdn/login');
  };

  const getColorClass = (color: string) => {
    const colors: Record<string, string> = {
      blue: 'bg-blue-50 text-blue-600',
      orange: 'bg-orange-50 text-orange-600',
      red: 'bg-red-50 text-red-600',
      green: 'bg-green-50 text-green-600',
    };
    return colors[color] || colors.blue;
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <aside
        className={`fixed left-0 top-0 h-full bg-white border-r border-gray-100 z-50 transition-all duration-300 ${
          sidebarOpen ? 'w-64' : 'w-16'
        }`}
      >
        <div className="h-16 flex items-center justify-between px-4 border-b border-gray-100">
          {sidebarOpen && (
            <div className="flex items-center gap-2">
              <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center">
                <Shield size={20} />
              </div>
              <span className="text-xl font-bold text-gray-800">6cdn</span>
            </div>
          )}
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700"
          >
            {sidebarOpen ? <X size={20} /> : <Menu size={20} />}
          </button>
        </div>

        <nav className="p-4 space-y-1">
          {navItems.map((item) => (
            <button
              key={item.key}
              onClick={() => setActiveNav(item.key)}
              className={`w-full flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all ${
                activeNav === item.key
                  ? 'bg-blue-50 text-blue-600'
                  : 'text-gray-600 hover:bg-gray-50'
              }`}
            >
              <item.icon size={20} />
              {sidebarOpen && <span className="font-medium">{item.label}</span>}
            </button>
          ))}
        </nav>

        {sidebarOpen && (
          <div className="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100">
            <button
              onClick={handleLogout}
              className="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-red-600 hover:bg-red-50 transition-all"
            >
              <LogOut size={20} />
              <span className="font-medium">退出登录</span>
            </button>
          </div>
        )}
      </aside>

      <div className={`transition-all duration-300 ${sidebarOpen ? 'ml-64' : 'ml-16'}`}>
        <header className="fixed top-0 right-0 h-16 bg-white/90 backdrop-blur-xl border-b border-gray-100 z-40 px-6 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <h1 className="text-lg font-bold text-gray-800">控制台</h1>
            <button className="p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700">
              <RefreshCw size={18} />
            </button>
          </div>

          <div className="flex items-center gap-4">
            <div className="relative">
              <Search size={18} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
              <input
                type="text"
                placeholder="搜索站点..."
                className="pl-9 pr-4 py-2 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all text-sm bg-gray-50/50 w-64"
              />
            </div>

            <button className="relative p-2 rounded-lg hover:bg-gray-100 text-gray-500 hover:text-gray-700">
              <Bell size={20} />
              <span className="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full" />
            </button>

            <div className="flex items-center gap-3 pl-4 border-l border-gray-200">
              <div className="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center">
                <User size={18} />
              </div>
              {sidebarOpen && (
                <div className="text-left">
                  <div className="text-sm font-medium text-gray-800">用户</div>
                  <div className="text-xs text-gray-500">普通用户</div>
                </div>
              )}
            </div>
          </div>
        </header>

        <main className="pt-20 pb-8 px-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            {stats.map((stat, index) => (
              <div
                key={index}
                className="bg-white rounded-xl p-5 border border-gray-100 hover:shadow-lg hover:shadow-gray-200/50 transition-all"
              >
                <div className="flex items-center justify-between mb-4">
                  <div className={`w-10 h-10 rounded-lg ${getColorClass(stat.color)} flex items-center justify-center`}>
                    <stat.icon size={20} />
                  </div>
                  {stat.trend === 'up' ? (
                    <TrendingUp size={18} className="text-green-500" />
                  ) : (
                    <TrendingDown size={18} className="text-red-500" />
                  )}
                </div>
                <div className="text-2xl font-bold text-gray-800 mb-1">{stat.value}</div>
                <div className="text-sm text-gray-500">{stat.label}</div>
                <div className={`text-xs mt-1 font-medium ${stat.trend === 'up' ? 'text-green-500' : 'text-red-500'}`}>
                  {stat.change}
                </div>
              </div>
            ))}
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div className="lg:col-span-2 bg-white rounded-xl p-5 border border-gray-100">
              <div className="flex items-center justify-between mb-6">
                <h2 className="font-semibold text-gray-800">实时流量趋势</h2>
                <div className="flex gap-2">
                  <button className="px-3 py-1.5 rounded-lg text-sm bg-blue-50 text-blue-600 font-medium">今日</button>
                  <button className="px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 font-medium">近7天</button>
                  <button className="px-3 py-1.5 rounded-lg text-sm text-gray-600 hover:bg-gray-100 font-medium">近30天</button>
                </div>
              </div>
              <div className="h-48">
                <svg className="w-full h-full" viewBox="0 0 600 200">
                  <defs>
                    <linearGradient id="gradient" x1="0%" y1="0%" x2="0%" y2="100%">
                      <stop offset="0%" stopColor="#3b82f6" stopOpacity="0.3" />
                      <stop offset="100%" stopColor="#3b82f6" stopOpacity="0" />
                    </linearGradient>
                  </defs>
                  <path
                    d={`M 0 ${200 - trafficData.values[0] * 4} ${trafficData.values.map((v, i) => `L ${(i + 1) * 100} ${200 - v * 4}`).join(' ')} L 600 200 L 0 200 Z`}
                    fill="url(#gradient)"
                  />
                  <path
                    d={`M 0 ${200 - trafficData.values[0] * 4} ${trafficData.values.map((v, i) => `L ${(i + 1) * 100} ${200 - v * 4}`).join(' ')}`}
                    fill="none"
                    stroke="#3b82f6"
                    strokeWidth="3"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                  {trafficData.labels.map((label, i) => (
                    <text key={i} x={i * 100 + 50} y={190} textAnchor="middle" className="text-xs fill-gray-400">
                      {label}
                    </text>
                  ))}
                </svg>
              </div>
              <div className="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                <div>
                  <div className="text-sm text-gray-500">今日总流量</div>
                  <div className="text-lg font-bold text-gray-800">125.6 GB</div>
                </div>
                <div>
                  <div className="text-sm text-gray-500">峰值带宽</div>
                  <div className="text-lg font-bold text-gray-800">180 Mbps</div>
                </div>
                <div>
                  <div className="text-sm text-gray-500">请求数</div>
                  <div className="text-lg font-bold text-gray-800">2.3M</div>
                </div>
                <div>
                  <div className="text-sm text-gray-500">缓存命中率</div>
                  <div className="text-lg font-bold text-green-600">99.2%</div>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl p-5 border border-gray-100">
              <div className="flex items-center justify-between mb-4">
                <h2 className="font-semibold text-gray-800">最近告警</h2>
                <button className="text-sm text-blue-600 hover:text-blue-700 font-medium">查看全部</button>
              </div>
              <div className="space-y-3">
                {recentAlerts.map((alert) => (
                  <div
                    key={alert.id}
                    className={`p-3 rounded-lg border ${
                      alert.type === 'danger'
                        ? 'bg-red-50 border-red-100'
                        : alert.type === 'warning'
                        ? 'bg-yellow-50 border-yellow-100'
                        : 'bg-blue-50 border-blue-100'
                    }`}
                  >
                    <div className="flex items-start gap-3">
                      {alert.type === 'danger' ? (
                        <AlertTriangle size={18} className="text-red-500 flex-shrink-0 mt-0.5" />
                      ) : alert.type === 'warning' ? (
                        <AlertTriangle size={18} className="text-yellow-500 flex-shrink-0 mt-0.5" />
                      ) : (
                        <CheckCircle size={18} className="text-blue-500 flex-shrink-0 mt-0.5" />
                      )}
                      <div className="flex-1">
                        <div className="font-medium text-gray-800 text-sm">{alert.title}</div>
                        <div className="text-xs text-gray-500 mt-1">{alert.desc}</div>
                        <div className="text-xs text-gray-400 mt-1">{alert.time}</div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div className="bg-white rounded-xl border border-gray-100">
              <div className="p-5 border-b border-gray-100 flex items-center justify-between">
                <h2 className="font-semibold text-gray-800">站点概览</h2>
                <button className="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-800 hover:shadow-lg hover:shadow-blue-600/20 transition-all">
                  <Plus size={16} />
                  添加站点
                </button>
              </div>
              <div className="overflow-x-auto">
                <table className="w-full">
                  <thead>
                    <tr className="bg-gray-50">
                      <th className="px-5 py-3 text-left text-sm font-medium text-gray-500">域名</th>
                      <th className="px-5 py-3 text-center text-sm font-medium text-gray-500">状态</th>
                      <th className="px-5 py-3 text-center text-sm font-medium text-gray-500">HTTPS</th>
                      <th className="px-5 py-3 text-center text-sm font-medium text-gray-500">WAF</th>
                      <th className="px-5 py-3 text-right text-sm font-medium text-gray-500">今日流量</th>
                      <th className="px-5 py-3 text-center text-sm font-medium text-gray-500">操作</th>
                    </tr>
                  </thead>
                  <tbody>
                    {sites.map((site) => (
                      <tr key={site.id} className="border-b border-gray-100 hover:bg-gray-50">
                        <td className="px-5 py-4">
                          <div className="flex items-center gap-2">
                            {site.status === 'running' ? (
                              <Cloud className="w-4 h-4 text-blue-500" />
                            ) : (
                              <CloudOff className="w-4 h-4 text-gray-400" />
                            )}
                            <div className="font-medium text-gray-800">{site.name}</div>
                          </div>
                        </td>
                        <td className="px-5 py-4 text-center">
                          <div className="flex items-center justify-center gap-1">
                            {site.status === 'running' ? (
                              <>
                                <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse" />
                                <span className="text-sm text-green-600">运行中</span>
                              </>
                            ) : (
                              <>
                                <span className="w-2 h-2 rounded-full bg-gray-400" />
                                <span className="text-sm text-gray-500">已停止</span>
                              </>
                            )}
                          </div>
                        </td>
                        <td className="px-5 py-4 text-center">
                          {site.https ? (
                            <Lock className="w-4 h-4 text-green-500 mx-auto" />
                          ) : (
                            <LockOpen className="w-4 h-4 text-gray-400 mx-auto" />
                          )}
                        </td>
                        <td className="px-5 py-4 text-center">
                          {site.waf ? (
                            <ShieldCheck className="w-4 h-4 text-green-500 mx-auto" />
                          ) : (
                            <Shield className="w-4 h-4 text-gray-400 mx-auto" />
                          )}
                        </td>
                        <td className="px-5 py-4 text-right text-gray-600">{site.traffic}</td>
                        <td className="px-5 py-4 text-center">
                          <button className="flex items-center gap-1 mx-auto text-blue-600 hover:text-blue-700 text-sm">
                            管理
                            <ChevronRight size={14} />
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <div className="bg-white rounded-xl border border-gray-100">
              <div className="p-5 border-b border-gray-100">
                <h2 className="font-semibold text-gray-800">快捷操作</h2>
              </div>
              <div className="p-5">
                <div className="grid grid-cols-2 gap-4">
                  <button className="p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors text-left">
                    <div className="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center mb-3">
                      <Plus size={20} />
                    </div>
                    <div className="font-medium text-gray-800">添加站点</div>
                    <div className="text-xs text-gray-500 mt-1">快速添加新域名</div>
                  </button>
                  <button className="p-4 rounded-xl bg-green-50 hover:bg-green-100 transition-colors text-left">
                    <div className="w-10 h-10 rounded-lg bg-green-600 text-white flex items-center justify-center mb-3">
                      <CreditCard size={20} />
                    </div>
                    <div className="font-medium text-gray-800">充值续费</div>
                    <div className="text-xs text-gray-500 mt-1">管理账户余额</div>
                  </button>
                  <button className="p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors text-left">
                    <div className="w-10 h-10 rounded-lg bg-purple-600 text-white flex items-center justify-center mb-3">
                      <ShieldCheck size={20} />
                    </div>
                    <div className="font-medium text-gray-800">WAF规则</div>
                    <div className="text-xs text-gray-500 mt-1">配置安全策略</div>
                  </button>
                  <button className="p-4 rounded-xl bg-orange-50 hover:bg-orange-100 transition-colors text-left">
                    <div className="w-10 h-10 rounded-lg bg-orange-600 text-white flex items-center justify-center mb-3">
                      <FileText size={20} />
                    </div>
                    <div className="font-medium text-gray-800">查看文档</div>
                    <div className="text-xs text-gray-500 mt-1">接入指南与API</div>
                  </button>
                </div>

                <div className="mt-6 p-4 rounded-xl bg-gradient-to-br from-blue-600 to-blue-800">
                  <div className="flex items-center justify-between">
                    <div className="text-white">
                      <div className="text-sm opacity-80">账户余额</div>
                      <div className="text-2xl font-bold">¥1,234.56</div>
                    </div>
                    <button className="px-4 py-2 rounded-lg text-sm font-medium bg-white text-blue-600 hover:bg-blue-50 transition-colors">
                      充值
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}