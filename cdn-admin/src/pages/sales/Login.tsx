import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Shield, AlertCircle, Eye, EyeOff, CheckCircle, Zap, Globe } from 'lucide-react';

interface SalesLoginProps {
  onLogin: (remember?: boolean) => void;
}

const featureCards = [
  { icon: Zap, title: '全局加速', desc: '智能边缘调度，毫秒级响应' },
  { icon: Globe, title: '安全防护', desc: 'DDoS 清洗与 WAF 规则引擎' },
];

export default function SalesLogin({ onLogin }: SalesLoginProps) {
  const navigate = useNavigate();
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [remember, setRemember] = useState(true);

  const handleSubmit = async () => {
    if (!phone.trim()) {
      setError('请输入手机号');
      return;
    }
    if (!password.trim()) {
      setError('请输入密码');
      return;
    }
    if (password.length < 6) {
      setError('密码长度不能少于 6 位');
      return;
    }
    setError('');
    setLoading(true);

    setTimeout(() => {
      setLoading(false);
      onLogin(remember);
      navigate('/user');
    }, 800);
  };

  return (
    <div className="min-h-screen flex flex-col md:flex-row">
      {/* Left brand area */}
      <div className="relative hidden md:flex md:w-1/2 lg:w-[45%] flex-col justify-between p-10 lg:p-14 text-white overflow-hidden bg-gradient-to-br from-[#0052d9] via-[#3d52e8] to-[#9275ff]">
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.12),transparent_40%)]" />
        <div className="absolute inset-0 bg-[radial-gradient(circle_at_bottom_left,rgba(0,0,0,0.1),transparent_40%)]" />

        <div className="relative z-10">
          <div className="flex items-center gap-3 mb-8">
            <div className="w-10 h-10 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
              <Shield size={22} />
            </div>
            <span className="text-xl font-bold">EdgeOne</span>
          </div>
          <h1 className="text-3xl lg:text-4xl font-bold leading-tight mb-4">
            统一控制台 · 规则编排 · 异步任务同步
          </h1>
          <p className="text-white/70 max-w-md leading-relaxed">
            为企业提供全球化的边缘加速、安全防护与商业运营能力，一站式管理您的数字业务。
          </p>
        </div>

        <div className="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-4">
          {featureCards.map((card, index) => (
            <div
              key={index}
              className="p-5 rounded-xl bg-white/10 backdrop-blur-sm border border-white/10 hover:bg-white/15 transition-colors"
            >
              <card.icon size={24} className="mb-3" />
              <h3 className="font-semibold mb-1">{card.title}</h3>
              <p className="text-sm text-white/60">{card.desc}</p>
            </div>
          ))}
        </div>
      </div>

      {/* Right login form */}
      <div className="flex-1 flex items-center justify-center p-4 md:p-8 lg:p-12 bg-[var(--sales-bg)] relative">
        <div className="absolute inset-0 bg-gradient-to-br from-[#0052d9]/5 via-transparent to-[#9275ff]/5 pointer-events-none" />

        <div className="relative w-full max-w-md bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl ring-1 ring-slate-200 p-8 md:p-10">
          {/* Mobile logo */}
          <div className="md:hidden flex items-center gap-3 mb-8">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-[#0052d9] to-[#4656ff] text-white flex items-center justify-center">
              <Shield size={22} />
            </div>
            <span className="text-xl font-bold text-[var(--sales-text)]">EdgeOne</span>
          </div>

          <div className="mb-8">
            <h2 className="text-2xl font-bold text-[var(--sales-text)] mb-2">欢迎登录</h2>
            <p className="text-sm text-[var(--sales-text-secondary)]">登录后管理您的边缘加速与安全服务</p>
          </div>

          {error && (
            <div className="flex items-center gap-2 text-sm text-danger bg-danger/5 px-3 py-2 rounded-lg mb-4">
              <AlertCircle size={16} />
              {error}
            </div>
          )}

          <div className="space-y-5">
            <div>
              <label className="block text-sm font-medium text-[var(--sales-text)] mb-1.5">手机号</label>
              <input
                type="tel"
                className="w-full px-4 py-2.5 rounded-lg border border-[var(--sales-border)] bg-[var(--sales-input-bg)] text-sm outline-none focus:border-[#0052d9] focus:ring-2 focus:ring-[#0052d9]/10 transition-all"
                placeholder="请输入手机号"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-[var(--sales-text)] mb-1.5">密码</label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  className="w-full px-4 py-2.5 rounded-lg border border-[var(--sales-border)] bg-[var(--sales-input-bg)] text-sm outline-none focus:border-[#0052d9] focus:ring-2 focus:ring-[#0052d9]/10 transition-all"
                  placeholder="请输入密码"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--sales-text-secondary)] hover:text-[var(--sales-text)]"
                >
                  {showPassword ? <EyeOff size={16} /> : <Eye size={16} />}
                </button>
              </div>
            </div>

            <div className="flex items-center justify-between text-sm">
              <label className="flex items-center gap-2 text-[var(--sales-text-secondary)] cursor-pointer">
                <input
                  type="checkbox"
                  checked={remember}
                  onChange={(e) => setRemember(e.target.checked)}
                  className="rounded border-[var(--sales-border)] text-[#0052d9] focus:ring-[#0052d9]/20"
                />
                记住我
              </label>
              <button onClick={() => alert('请联系客服重置密码')} className="text-[#0052d9] hover:underline">
                忘记密码？
              </button>
            </div>

            <button
              onClick={handleSubmit}
              disabled={loading}
              className="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-gradient-to-r from-[#0052d9] to-[#4656ff] shadow-lg shadow-[#0052d9]/25 hover:shadow-[#0052d9]/40 disabled:opacity-70 transition-all"
            >
              {loading ? '登录中...' : '登录'}
            </button>
          </div>

          <div className="mt-6 flex items-center gap-2 text-xs text-[var(--sales-text-secondary)]">
            <CheckCircle size={14} className="text-success" />
            <span>登录即表示同意</span>
            <button className="text-[#0052d9] hover:underline">服务条款</button>
            <span>和</span>
            <button className="text-[#0052d9] hover:underline">隐私政策</button>
          </div>

          <div className="mt-8 text-center text-sm text-[var(--sales-text-secondary)]">
            还没有账号？{' '}
            <button onClick={() => navigate('/login')} className="text-[#0052d9] hover:underline font-medium">
              立即注册
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
