import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { ShoppingBag, AlertCircle, ArrowLeft, Eye, EyeOff } from 'lucide-react';
import SalesHeader from '../../components/sales/SalesHeader';
import SalesFooter from '../../components/sales/SalesFooter';

interface SalesLoginProps {
  onLogin: (remember?: boolean) => void;
}

export default function SalesLogin({ onLogin }: SalesLoginProps) {
  const navigate = useNavigate();
  const [account, setAccount] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [remember, setRemember] = useState(true);

  const handleSubmit = async () => {
    if (!account.trim()) {
      setError('请输入账号');
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

    // In production this would call the login API
    setTimeout(() => {
      setLoading(false);
      onLogin(remember);
      navigate('/user');
    }, 800);
  };

  return (
    <div className="min-h-screen flex flex-col bg-[var(--sales-bg)] text-[var(--sales-text)]">
      <SalesHeader />

      <div className="flex-1 flex items-center justify-center p-4">
        <div className="w-full max-w-md bg-[var(--sales-card)] border border-[var(--sales-border)] rounded-2xl shadow-xl shadow-[var(--sales-primary)]/5 p-8 relative">
          <button
            onClick={() => navigate('/')}
            className="absolute top-4 left-4 flex items-center gap-1 text-xs text-[var(--sales-text-secondary)] hover:text-[var(--sales-primary)] transition-colors"
          >
            <ArrowLeft size={14} /> 返回首页
          </button>

          <div className="text-center mb-8 mt-4">
            <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[var(--sales-primary)]/30">
              <ShoppingBag size={28} />
            </div>
            <h1 className="text-2xl font-bold">登录销售系统</h1>
            <p className="text-sm text-[var(--sales-text-secondary)] mt-1">管理您的授权、节点与订单</p>
          </div>

          {error && (
            <div className="flex items-center gap-2 text-sm text-danger bg-danger/5 px-3 py-2 rounded-lg mb-4">
              <AlertCircle size={16} />
              {error}
            </div>
          )}

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium mb-1.5">账号</label>
              <input
                className="w-full px-4 py-2.5 rounded-xl border border-[var(--sales-border)] bg-[var(--sales-input-bg)] text-sm outline-none focus:border-[var(--sales-primary)] transition-colors"
                placeholder="请输入账号 / 邮箱 / 手机号"
                value={account}
                onChange={(e) => setAccount(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
              />
            </div>
            <div>
              <label className="block text-sm font-medium mb-1.5">密码</label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  className="w-full px-4 py-2.5 rounded-xl border border-[var(--sales-border)] bg-[var(--sales-input-bg)] text-sm outline-none focus:border-[var(--sales-primary)] transition-colors"
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
                  className="rounded border-[var(--sales-border)]"
                />
                记住我
              </label>
              <button onClick={() => alert('请联系客服重置密码')} className="text-[var(--sales-primary)] hover:underline">
                忘记密码？
              </button>
            </div>

            <button
              onClick={handleSubmit}
              disabled={loading}
              className="w-full py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 disabled:opacity-70 transition-all"
            >
              {loading ? '登录中...' : '登录'}
            </button>
          </div>

          <div className="mt-6 text-center text-sm text-[var(--sales-text-secondary)]">
            还没有账号？{' '}
            <button className="text-[var(--sales-primary)] hover:underline font-medium">立即注册</button>
          </div>
        </div>
      </div>

      <SalesFooter />
    </div>
  );
}
