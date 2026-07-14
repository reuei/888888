import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { Shield, Mail, User, Lock, Eye, EyeOff, ArrowRight, RefreshCw } from 'lucide-react';

export default function CdnLogin() {
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    loginType: 'email',
    email: '',
    password: '',
    captcha: '',
    remember: false,
  });
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [captchaCode, setCaptchaCode] = useState('');

  const generateCaptcha = () => {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    let code = '';
    for (let i = 0; i < 4; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    setCaptchaCode(code);
  };

  useEffect(() => {
    generateCaptcha();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    await new Promise((resolve) => setTimeout(resolve, 1000));
    localStorage.setItem('cdn-role', 'b');
    navigate('/cdn/dashboard');
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, remember: e.target.checked });
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
      <div className="min-h-screen flex items-center justify-center">
        <div className="absolute inset-0 overflow-hidden">
          <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl" />
          <div className="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl" />
          <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-indigo-400/10 rounded-full blur-3xl" />
        </div>

        <div className="relative z-10 w-full max-w-md mx-4">
          <div className="text-center mb-10">
            <div className="inline-flex items-center gap-3 mb-6">
              <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white flex items-center justify-center shadow-xl shadow-blue-600/20">
                <Shield size={32} />
              </div>
              <div className="text-left">
                <span className="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-800">6cdn</span>
                <p className="text-xs text-gray-500 mt-1">语云科技 - 专业CDN服务商</p>
              </div>
            </div>
            <h1 className="text-2xl font-bold text-gray-800 mb-3">欢迎登录控制台</h1>
            <p className="text-gray-500">管理您的CDN站点和服务</p>
          </div>

          <div className="bg-white/90 backdrop-blur-xl rounded-2xl border border-gray-100 shadow-2xl p-8">
            <div className="flex items-center justify-center gap-4 mb-8">
              <button
                onClick={() => setFormData({ ...formData, loginType: 'email' })}
                className={`flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-medium transition-all ${
                  formData.loginType === 'email'
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                    : 'text-gray-500 hover:bg-gray-100'
                }`}
              >
                <Mail size={16} />
                邮箱登录
              </button>
              <button
                onClick={() => setFormData({ ...formData, loginType: 'username' })}
                className={`flex items-center gap-2 px-6 py-2 rounded-lg text-sm font-medium transition-all ${
                  formData.loginType === 'username'
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30'
                    : 'text-gray-500 hover:bg-gray-100'
                }`}
              >
                <User size={16} />
                账号登录
              </button>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  {formData.loginType === 'email' ? '邮箱地址' : '账号'}
                </label>
                <div className="relative">
                  {formData.loginType === 'email' ? (
                    <Mail size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                  ) : (
                    <User size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                  )}
                  <input
                    type={formData.loginType === 'email' ? 'email' : 'text'}
                    name="email"
                    value={formData.email}
                    onChange={handleChange}
                    required
                    className="w-full pl-11 pr-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-gray-50/50"
                    placeholder={formData.loginType === 'email' ? '请输入邮箱地址' : '请输入账号'}
                  />
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">密码</label>
                <div className="relative">
                  <Lock size={18} className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" />
                  <input
                    type={showPassword ? 'text' : 'password'}
                    name="password"
                    value={formData.password}
                    onChange={handleChange}
                    required
                    className="w-full pl-11 pr-12 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-gray-50/50"
                    placeholder="请输入密码"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                    {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">验证码</label>
                <div className="flex items-center gap-3">
                  <div className="relative flex-1">
                    <input
                      type="text"
                      name="captcha"
                      value={formData.captcha}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-gray-50/50"
                      placeholder="请输入验证码"
                    />
                  </div>
                  <div className="flex items-center gap-2">
                    <div
                      onClick={generateCaptcha}
                      className="w-36 h-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-xl font-bold text-gray-700 select-none cursor-pointer hover:from-gray-200 hover:to-gray-300 transition-all"
                      style={{
                        letterSpacing: '4px',
                        textShadow: '1px 1px 0 rgba(0,0,0,0.1)',
                      }}
                    >
                      {captchaCode || 'LOADING'}
                    </div>
                    <button
                      type="button"
                      onClick={generateCaptcha}
                      className="p-3 rounded-xl bg-gray-100 hover:bg-gray-200 transition-colors"
                      title="刷新验证码"
                    >
                      <RefreshCw size={18} className="text-gray-600" />
                    </button>
                  </div>
                </div>
              </div>

              <div className="flex items-center justify-between">
                <label className="flex items-center gap-2 cursor-pointer">
                  <input
                    type="checkbox"
                    name="remember"
                    checked={formData.remember}
                    onChange={handleCheckboxChange}
                    className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                  />
                  <span className="text-sm text-gray-600">记住我</span>
                </label>
                <button
                  type="button"
                  className="text-sm text-blue-600 hover:text-blue-700"
                >
                  忘记密码？
                </button>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl font-semibold text-white bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 hover:shadow-xl hover:shadow-blue-600/30 hover:-translate-y-0.5 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
              >
                {loading ? (
                  <>
                    <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                    登录中...
                  </>
                ) : (
                  <>
                    登录
                    <ArrowRight size={18} />
                  </>
                )}
              </button>
            </form>

            <div className="mt-6">
              <div className="relative">
                <div className="absolute inset-0 flex items-center">
                  <div className="w-full border-t border-gray-200" />
                </div>
                <div className="relative flex justify-center text-sm">
                  <span className="px-4 bg-white text-gray-500">其他登录方式</span>
                </div>
              </div>
              <div className="mt-4 grid grid-cols-2 gap-3">
                <button className="flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                  <svg className="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                  </svg>
                  Google
                </button>
                <button className="flex items-center justify-center gap-2 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition-colors">
                  <svg className="w-5 h-5" viewBox="0 0 24 24" fill="#07C160">
                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm4.64 6.8c-.15 1.58-.8 2.95-1.85 3.99-.27.27-.57.49-.9.67-.36.2-.77.31-1.21.31h-.08c-.46 0-.87-.12-1.22-.33-.35-.2-.64-.43-.88-.7-.38-.48-.63-1.06-.73-1.7-.1-1.18.34-2.26 1.04-3.12.7-1.25 1.7-2.16 2.92-2.75 1.21-.6 2.57-.91 4.02-.91.47 0 .93.04 1.37.12.18.02.35.08.5.17.15.09.27.2.34.35.07.15.1.32.1.5v.08c0 .27-.07.52-.2.74-.13.22-.3.41-.5.57-.18.15-.38.27-.61.36-.22.08-.46.13-.7.15-.02.07-.03.14-.03.22 0 2.92 2.38 5.3 5.3 5.3s5.3-2.38 5.3-5.3-2.38-5.3-5.3-5.3z"/>
                  </svg>
                  微信
                </button>
              </div>
            </div>

            <div className="mt-6 text-center">
              <p className="text-sm text-gray-500">
                还没有账号？
                <button className="ml-1 text-blue-600 hover:text-blue-700 font-medium">
                  立即注册
                </button>
              </p>
            </div>
          </div>

          <div className="mt-6 text-center text-sm text-gray-400">
            <p>登录即表示您同意我们的</p>
            <p>
              <button className="text-blue-600 hover:text-blue-700">服务协议</button>
              <span className="mx-2">和</span>
              <button className="text-blue-600 hover:text-blue-700">隐私政策</button>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}