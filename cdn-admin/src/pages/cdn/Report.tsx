import { useState } from 'react';
import { RefreshCw, CheckCircle } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

export default function Report() {
  const [formData, setFormData] = useState({
    domain: '',
    reason: '',
    contact: '',
    captcha: '',
  });
  const [submitted, setSubmitted] = useState(false);
  const [captchaCode, setCaptchaCode] = useState('ABCD');

  const generateCaptcha = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let code = '';
    for (let i = 0; i < 4; i++) {
      code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    setCaptchaCode(code);
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitted(true);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  if (submitted) {
    return (
      <div className="min-h-screen bg-gray-50">
        <CdnHeader />
        <main className="pt-24 pb-16">
          <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-20">
            <div className="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
              <CheckCircle size={48} className="text-green-600" />
            </div>
            <h2 className="text-3xl font-bold text-gray-800 mb-4">举报提交成功</h2>
            <p className="text-gray-600 mb-8">
              感谢您的举报，我们会在24小时内进行核实处理，并将结果反馈给您
            </p>
            <button
              onClick={() => setSubmitted(false)}
              className="px-8 py-3 rounded-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700"
            >
              继续举报
            </button>
          </div>
        </main>
        <CdnFooter />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">站点举报</h1>
            <p className="text-xl text-blue-100 max-w-3xl mx-auto">
              如发现使用我们产品进行滥用、仿冒、钓鱼、博彩、欺诈或其他违法违规内容，可在此提交举报。
              系统会记录提交 IP 作为举报标识，并在新记录产生后邮件通知管理员。
            </p>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="mb-8">
              <h2 className="text-xl font-bold text-gray-800 mb-2">填写举报信息</h2>
              <p className="text-gray-600">请尽量填写完整信息，便于我们尽快核查处理。</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-6">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  举报域名 <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  name="domain"
                  value={formData.domain}
                  onChange={handleChange}
                  required
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  placeholder="请输入要举报的域名"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  举报原因 <span className="text-red-500">*</span>
                </label>
                <textarea
                  name="reason"
                  value={formData.reason}
                  onChange={handleChange}
                  required
                  rows={4}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none"
                  placeholder="请详细描述举报原因和违规内容"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  联系方式 <span className="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  name="contact"
                  value={formData.contact}
                  onChange={handleChange}
                  required
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                  placeholder="请输入您的邮箱或电话，便于核实细节或反馈处理结果"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  验证码 <span className="text-red-500">*</span>
                </label>
                <div className="flex items-center gap-3">
                  <input
                    type="text"
                    name="captcha"
                    value={formData.captcha}
                    onChange={handleChange}
                    required
                    className="flex-1 px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                    placeholder="请输入验证码"
                  />
                  <div className="flex items-center gap-2">
                    <div className="w-32 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-xl font-bold text-gray-700 select-none">
                      {captchaCode}
                    </div>
                    <button
                      type="button"
                      onClick={generateCaptcha}
                      className="p-3 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors"
                    >
                      <RefreshCw size={18} />
                    </button>
                  </div>
                </div>
              </div>

              <button
                type="submit"
                className="w-full px-8 py-4 rounded-lg text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all"
              >
                提交举报
              </button>
            </form>

            <div className="mt-8 p-6 rounded-xl bg-gray-50">
              <h3 className="text-lg font-bold text-gray-800 mb-4">处理说明</h3>
              <p className="text-gray-600 mb-4">
                举报提交后会进入人工核查流程，建议同时保留访问截图和证据链接。
              </p>
              <ul className="space-y-2 text-gray-600">
                <li className="flex items-start gap-2">
                  <span className="text-blue-600">-</span>
                  <span>请填写完整、可直接访问的域名。</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-blue-600">-</span>
                  <span>联系方式必填，便于核实细节或反馈处理结果。</span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-blue-600">-</span>
                  <span>若涉及紧急违法内容，建议同步联系站点客服或监管渠道。</span>
                </li>
              </ul>
            </div>

            <div className="mt-6 p-6 rounded-xl bg-blue-50">
              <h3 className="text-lg font-bold text-blue-800 mb-2">隐私保护</h3>
              <p className="text-blue-700">
                举报内容及任何信息都将被保密，不会公开。
              </p>
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}