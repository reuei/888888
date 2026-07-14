import { useState } from 'react';
import { Mail, Phone, MapPin, Send, Clock, Headphones, MessageCircle } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const contactInfo = [
  { icon: Phone, title: '客服热线', content: '400-888-6cdn', desc: '7×24小时服务' },
  { icon: Mail, title: '邮箱', content: 'contact@6cdn.com', desc: '工作日24小时内回复' },
  { icon: MapPin, title: '公司地址', content: '上海市浦东新区张江高科技园区', desc: '' },
  { icon: Clock, title: '工作时间', content: '周一至周五 9:00-18:00', desc: '技术支持全天候' },
];

const emailInfo = [
  { title: '技术支持', email: 'tech@6cdn.com' },
  { title: '商务合作', email: 'business@6cdn.com' },
  { title: '投诉建议', email: 'feedback@6cdn.com' },
];

export default function Contact() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    subject: '',
    message: '',
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    alert('提交成功！我们会尽快与您联系。');
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">联系我们</h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto">
              有任何问题或需求，欢迎随时与我们联系
            </p>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {contactInfo.map((item, index) => (
                <div
                  key={index}
                  className="text-center p-6 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors"
                >
                  <div className="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mx-auto mb-4">
                    <item.icon size={28} />
                  </div>
                  <h3 className="font-semibold text-gray-800 mb-2">{item.title}</h3>
                  <p className="text-blue-600 font-medium mb-1">{item.content}</p>
                  {item.desc && <p className="text-sm text-gray-500">{item.desc}</p>}
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
              <div>
                <h2 className="text-3xl font-bold text-gray-800 mb-6">发送消息</h2>
                <p className="text-gray-600 mb-8">
                  填写以下表单，我们的专业团队将在24小时内与您联系
                </p>
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">姓名</label>
                      <input
                        type="text"
                        name="name"
                        value={formData.name}
                        onChange={handleChange}
                        required
                        className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="请输入姓名"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">邮箱</label>
                      <input
                        type="email"
                        name="email"
                        value={formData.email}
                        onChange={handleChange}
                        required
                        className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="请输入邮箱"
                      />
                    </div>
                  </div>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">电话</label>
                      <input
                        type="tel"
                        name="phone"
                        value={formData.phone}
                        onChange={handleChange}
                        className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="请输入电话（选填）"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-2">公司</label>
                      <input
                        type="text"
                        name="company"
                        value={formData.company}
                        onChange={handleChange}
                        className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="请输入公司名称（选填）"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">咨询类型</label>
                    <select
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white"
                    >
                      <option value="">请选择咨询类型</option>
                      <option value="product">产品咨询</option>
                      <option value="price">价格咨询</option>
                      <option value="technical">技术支持</option>
                      <option value="agent">代理合作</option>
                      <option value="other">其他</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">留言内容</label>
                    <textarea
                      name="message"
                      value={formData.message}
                      onChange={handleChange}
                      required
                      rows={4}
                      className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all resize-none"
                      placeholder="请详细描述您的需求或问题"
                    />
                  </div>
                  <button
                    type="submit"
                    className="inline-flex items-center gap-2 px-8 py-3 rounded-lg text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:shadow-lg hover:-translate-y-0.5 transition-all"
                  >
                    <Send size={20} />
                    发送消息
                  </button>
                </form>
              </div>

              <div className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-8 text-white">
                <h3 className="text-2xl font-bold mb-6">在线客服</h3>
                <p className="text-blue-100 mb-8">
                  工作时间内，您可以通过在线客服即时获取帮助
                </p>
                <div className="bg-white/10 rounded-xl p-6 mb-6">
                  <div className="flex items-center gap-3 mb-4">
                    <div className="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                      <Headphones size={24} />
                    </div>
                    <div>
                      <div className="font-semibold">6cdn在线客服</div>
                      <div className="text-sm text-blue-200">工作日 9:00-18:00</div>
                    </div>
                  </div>
                  <p className="text-blue-100 text-sm mb-4">
                    您可以直接通过控制台提交工单，我们的技术团队会尽快处理
                  </p>
                  <button className="w-full py-3 rounded-lg font-semibold bg-white text-blue-600 hover:bg-blue-50 transition-colors">
                    前往控制台提交工单
                  </button>
                </div>
                <div className="space-y-3">
                  {emailInfo.map((item, index) => (
                    <div key={index} className="flex items-center gap-3 text-sm">
                      <Mail size={16} className="text-blue-300" />
                      <span>{item.title}：{item.email}</span>
                    </div>
                  ))}
                </div>
                <div className="mt-6 pt-6 border-t border-white/20">
                  <div className="flex items-center gap-3">
                    <MessageCircle size={20} className="text-blue-300" />
                    <span className="text-sm">交流群：欢迎加入我们的用户交流群</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}