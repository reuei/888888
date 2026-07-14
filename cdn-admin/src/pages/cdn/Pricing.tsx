import { useState } from 'react';
import { CheckCircle, Zap, Shield, Globe, ArrowRight, Server, Lock, BarChart3, Headphones } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const plans = [
  {
    name: '亚太-Lite',
    price: '99',
    period: '月',
    description: '适合个人网站和小型应用',
    features: [
      { text: '500GB流量/月', included: true },
      { text: '10个域名', included: true },
      { text: '基础DDoS防护', included: true },
      { text: '基础WAF防护', included: true },
      { text: '智能缓存', included: true },
      { text: '7×24小时技术支持', included: true },
      { text: 'API接口', included: false },
      { text: '专属客服', included: false },
      { text: '定制化配置', included: false },
    ],
    popular: false,
  },
  {
    name: '亚太-Pro',
    price: '299',
    period: '月',
    description: '适合中型企业和高流量网站',
    features: [
      { text: '3TB流量/月', included: true },
      { text: '50个域名', included: true },
      { text: 'T级DDoS防护', included: true },
      { text: '企业级WAF', included: true },
      { text: '智能缓存+预热', included: true },
      { text: '7×24小时技术支持', included: true },
      { text: 'API接口', included: true },
      { text: '专属客服', included: true },
      { text: '定制化配置', included: false },
    ],
    popular: true,
  },
  {
    name: '国内高防型',
    price: '599',
    period: '月',
    description: '适合国内企业和安全性要求高的业务',
    features: [
      { text: '1TB流量/月', included: true },
      { text: '20个域名', included: true },
      { text: 'T级DDoS防护', included: true },
      { text: '企业级WAF+自定义规则', included: true },
      { text: '智能缓存+预热', included: true },
      { text: '7×24小时技术支持', included: true },
      { text: 'API接口', included: true },
      { text: '专属客服', included: true },
      { text: '定制化配置', included: false },
    ],
    popular: false,
  },
  {
    name: '美国高防型',
    price: '499',
    period: '月',
    description: '适合面向全球用户的网站',
    features: [
      { text: '2TB流量/月', included: true },
      { text: '30个域名', included: true },
      { text: 'T级DDoS防护', included: true },
      { text: '企业级WAF', included: true },
      { text: '智能缓存+预热', included: true },
      { text: '7×24小时技术支持', included: true },
      { text: 'API接口', included: true },
      { text: '专属客服', included: true },
      { text: '定制化配置', included: false },
    ],
    popular: false,
  },
  {
    name: '企业版',
    price: '999',
    period: '月',
    description: '适合大型企业和核心业务',
    features: [
      { text: '不限流量', included: true },
      { text: '不限域名', included: true },
      { text: 'T级DDoS防护', included: true },
      { text: '企业级WAF+自定义规则', included: true },
      { text: '智能缓存+预热+边缘计算', included: true },
      { text: '7×24小时技术支持', included: true },
      { text: 'API接口', included: true },
      { text: '专属客服经理', included: true },
      { text: '定制化配置', included: true },
    ],
    popular: false,
  },
];

const addons = [
  { name: '额外流量', price: '0.15', unit: 'GB', desc: '超出套餐部分按实际使用计费' },
  { name: '额外域名', price: '5', unit: '个/月', desc: '超出套餐域名数量限制' },
  { name: '高级WAF规则', price: '100', unit: '月', desc: '自定义规则库，精准防护' },
  { name: '专属节点', price: '500', unit: '月', desc: '独立节点，独享资源' },
];

const guarantees = [
  { icon: Zap, title: '99.99%可用性', desc: '全年无间断服务，智能故障切换' },
  { icon: Shield, title: '数据安全', desc: 'HTTPS加密传输，数据隐私保护' },
  { icon: Globe, title: '全球加速', desc: '2000+节点，全球覆盖无死角' },
  { icon: Server, title: '高可靠性', desc: '多节点冗余，自动故障转移' },
  { icon: Lock, title: '安全防护', desc: '企业级WAF，全方位安全保障' },
  { icon: BarChart3, title: '实时监控', desc: '实时流量统计，随时掌握状态' },
  { icon: Headphones, title: '专业服务', desc: '7×24小时技术支持，快速响应' },
];

export default function Pricing() {
  const [billingPeriod, setBillingPeriod] = useState('monthly');

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">价格方案</h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto mb-8">
              灵活的价格方案，满足不同规模企业的需求
            </p>
            <div className="inline-flex items-center gap-4 p-1 rounded-full bg-white/10">
              <button
                onClick={() => setBillingPeriod('monthly')}
                className={`px-6 py-2 rounded-full text-sm font-medium transition-all ${
                  billingPeriod === 'monthly'
                    ? 'bg-white text-blue-600'
                    : 'text-white hover:bg-white/10'
                }`}
              >
                月付
              </button>
              <button
                onClick={() => setBillingPeriod('yearly')}
                className={`px-6 py-2 rounded-full text-sm font-medium transition-all ${
                  billingPeriod === 'yearly'
                    ? 'bg-white text-blue-600'
                    : 'text-white hover:bg-white/10'
                }`}
              >
                年付 <span className="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full ml-1">省20%</span>
              </button>
            </div>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {plans.map((plan, index) => (
                <div
                  key={index}
                  className={`relative p-8 rounded-2xl ${
                    plan.popular
                      ? 'border-2 border-blue-500 bg-blue-50'
                      : 'border-2 border-gray-200 bg-white'
                  }`}
                >
                  {plan.popular && (
                    <div className="absolute -top-4 left-1/2 -translate-x-1/2">
                      <span className="px-4 py-1 rounded-full text-sm font-medium bg-blue-600 text-white">
                        最受欢迎
                      </span>
                    </div>
                  )}
                  <div className="text-center mb-6">
                    <h3 className="text-xl font-bold text-gray-800 mb-2">{plan.name}</h3>
                    <p className="text-gray-500 text-sm">{plan.description}</p>
                  </div>
                  <div className="text-center mb-6">
                    <span className="text-4xl font-extrabold text-blue-600">
                      ¥{billingPeriod === 'yearly' ? Math.floor(parseInt(plan.price) * 12 * 0.8) : plan.price}
                    </span>
                    <span className="text-gray-500">/{billingPeriod === 'yearly' ? '年' : plan.period}</span>
                  </div>
                  <ul className="space-y-3 mb-8">
                    {plan.features.map((feature, idx) => (
                      <li key={idx} className="flex items-center gap-3">
                        <CheckCircle
                          size={16}
                          className={feature.included ? 'text-green-500' : 'text-gray-300'}
                        />
                        <span className={feature.included ? 'text-gray-700' : 'text-gray-400'}>
                          {feature.text}
                        </span>
                      </li>
                    ))}
                  </ul>
                  <button
                    className={`w-full py-3 rounded-lg font-semibold ${
                      plan.popular
                        ? 'bg-blue-600 text-white hover:bg-blue-700'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                  >
                    立即购买
                  </button>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">增值服务</h2>
              <p className="text-gray-600">按需选择，灵活搭配</p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {addons.map((addon, index) => (
                <div key={index} className="p-6 rounded-xl bg-white border border-gray-100">
                  <h3 className="font-semibold text-gray-800 mb-2">{addon.name}</h3>
                  <p className="text-sm text-gray-500 mb-4">{addon.desc}</p>
                  <div className="flex items-center justify-between">
                    <span className="text-lg font-bold text-blue-600">
                      ¥{addon.price}/{addon.unit}
                    </span>
                    <button className="px-4 py-2 rounded-lg text-sm font-medium bg-blue-50 text-blue-600 hover:bg-blue-100">
                      添加
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-12">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">服务保障</h2>
              <p className="text-gray-600">我们承诺为您提供稳定可靠的服务</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {guarantees.map((item, index) => (
                <div key={index} className="text-center p-8 rounded-xl bg-gray-50">
                  <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mx-auto mb-6">
                    <item.icon size={32} />
                  </div>
                  <h3 className="text-xl font-bold text-gray-800 mb-3">{item.title}</h3>
                  <p className="text-gray-600">{item.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 className="text-3xl font-bold text-white mb-6">需要定制方案？</h2>
            <p className="text-xl text-blue-100 mb-8">
              大型企业可联系销售顾问获取定制化解决方案
            </p>
            <button className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-blue-600 bg-white shadow-xl hover:shadow-2xl transition-all">
              联系销售
              <ArrowRight size={20} />
            </button>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}