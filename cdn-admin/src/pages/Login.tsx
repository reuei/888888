import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import type { Role } from '../types';
import { Shield, Store, AlertCircle, Globe, ArrowLeft } from 'lucide-react';

interface LoginProps {
  onLogin: (role: Role) => void;
}

export default function Login({ onLogin }: LoginProps) {
  const navigate = useNavigate();
  const [role, setRole] = useState<Role>('s');
  const [account, setAccount] = useState('admin');
  const [password, setPassword] = useState('123456');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [remember, setRemember] = useState(true);

  const handleRoleChange = (r: Role) => {
    setRole(r);
    setAccount(r === 's' ? 'admin' : 'merchant');
    setPassword('123456');
    setError('');
  };

  const handleSubmit = () => {
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
    setTimeout(() => {
      setLoading(false);
      if (!remember) {
        localStorage.removeItem('role');
      }
      onLogin(role);
    }, 500);
  };

  return (
    <div className="min-h-screen flex flex-col bg-bg">
      <header className="border-b border-border bg-bg/80 backdrop-blur">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <button onClick={() => navigate('/')} className="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <div className="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center">
              <Globe size={18} />
            </div>
            <span className="text-lg font-bold">CloudShield CDN</span>
          </button>
        </div>
      </header>

      <div className="flex-1 flex items-center justify-center p-4">
        <div className="card p-8 w-full max-w-md relative">
          <button
            onClick={() => navigate('/')}
            className="absolute top-4 left-4 flex items-center gap-1 text-xs text-text-secondary hover:text-primary transition-colors"
          >
            <ArrowLeft size={14} /> 返回首页
          </button>

          <div className="text-center mb-8 mt-4">
            <div className="w-14 h-14 bg-primary text-white rounded flex items-center justify-center mx-auto mb-4">
              <Shield size={28} />
            </div>
            <h1 className="text-2xl font-bold">CDN 防护加速平台</h1>
            <p className="text-sm text-text-secondary mt-1">企业级 CDN 防护加速后台</p>
          </div>

        <div className="flex gap-2 mb-6">
          <button
            onClick={() => handleRoleChange('s')}
            className={`flex-1 py-2 rounded text-sm font-medium border flex items-center justify-center gap-2 ${
              role === 's' ? 'bg-primary text-white border-primary' : 'bg-card text-text border-border'
            }`}
          >
            <Shield size={16} /> S 端总站长
          </button>
          <button
            onClick={() => handleRoleChange('b')}
            className={`flex-1 py-2 rounded text-sm font-medium border flex items-center justify-center gap-2 ${
              role === 'b' ? 'bg-primary text-white border-primary' : 'bg-card text-text border-border'
            }`}
          >
            <Store size={16} /> B 端商户
          </button>
        </div>

        <div className="space-y-4">
          {error && (
            <div className="flex items-center gap-2 text-sm text-danger bg-danger/5 px-3 py-2 rounded">
              <AlertCircle size={16} />
              {error}
            </div>
          )}
          <div>
            <label className="block text-sm mb-1">账号</label>
            <input
              className="input"
              placeholder="请输入账号"
              value={account}
              onChange={(e) => setAccount(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">密码</label>
            <input
              type="password"
              className="input"
              placeholder="请输入密码"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && handleSubmit()}
            />
          </div>
          <div className="flex items-center justify-between text-sm">
            <label className="flex items-center gap-1.5 text-text-secondary cursor-pointer">
              <input
                type="checkbox"
                checked={remember}
                onChange={(e) => setRemember(e.target.checked)}
              /> 记住我
            </label>
            <button onClick={() => alert('请联系管理员重置密码')} className="text-primary">忘记密码？</button>
          </div>
          <button
            onClick={handleSubmit}
            disabled={loading}
            className="btn btn-primary w-full disabled:opacity-70"
          >
            {loading ? '登录中...' : '登录'}
          </button>
        </div>
      </div>
    </div>
  </div>
  );
}
