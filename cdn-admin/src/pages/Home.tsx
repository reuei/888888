import { useNavigate } from 'react-router-dom';
import {
  Shield,
  Zap,
  Globe,
  BarChart3,
  Server,
  Lock,
  ArrowRight,
  CheckCircle,
  Activity,
  Clock,
  Headphones,
  ChevronRight,
} from 'lucide-react';

const features = [
  {
    icon: <Zap size={24} />,
    title: '全球加速',
    desc: '覆盖全球 500+ 节点，智能调度最近边缘节点，平均延迟降低 60%。',
  },
  {
    icon: <Shield size={24} />,
    title: '企业级防护',
    desc: 'T 级 DDoS 清洗、CC 智能防护、WAF 规则引擎，守护业务安全。',
  },
  {
    icon: <Globe size={24} />,
    title: '智能缓存',
    desc: '多级缓存策略与预热机制，静态资源命中率高达 99%，源站压力直降。',
  },
  {
    icon: <BarChart3 size={24} />,
    title: '实时数据',
    desc: '流量、带宽、QPS、命中率多维可视化，分钟级监控告警。',
  },
  {
    icon: <Server size={24} />,
    title: '多协议支持',
    desc: '支持 HTTP/2、HTTP/3、WebSocket、TLS 1.3，满足现代应用需求。',
  },
  {
    icon: <Lock size={24} />,
    title: 'HTTPS 一键配置',
    desc: '免费 SSL 证书自动签发、续期，支持自定义证书与强制 HTTPS。',
  },
];

const stats = [
  { value: '500+', label: '全球节点' },
  { value: '99.99%', label: '可用性承诺' },
  { value: '10T+', label: '防护带宽' },
  { value: '50,000+', label: '服务企业' },
];

const plans = [
  {
    name: '入门版',
    price: '49',
    period: '月',
    desc: '适合个人站点与初创项目',
    features: ['100GB/月 流量', '10Mbps 带宽', '1 个域名', '基础 CC 防护', '邮件支持'],
    highlighted: false,
  },
  {
    name: '标准版',
    price: '199',
    period: '月',
    desc: '适合成长型中小企业',
    features: ['1TB/月 流量', '50Mbps 带宽', '5 个域名', '高级 CC 防护', '7×24 在线支持'],
    highlighted: true,
  },
  {
    name: '专业版',
    price: '599',
    period: '月',
    desc: '适合高流量商业应用',
    features: ['5TB/月 流量', '200Mbps 带宽', '20 个域名', '企业级 WAF', '专属客户经理'],
    highlighted: false,
  },
];

const highlights = [
  { icon: <Activity size={18} />, text: '实时监控与告警' },
  { icon: <Clock size={18} />, text: '分钟级接入' },
  { icon: <Headphones size={18} />, text: '7×24 技术支持' },
  { icon: <CheckCircle size={18} />, text: 'SLA 99.99%' },
];

export default function Home() {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-bg text-text">
      {/* Header */}
      <header className="sticky top-0 z-40 border-b border-border bg-bg/80 backdrop-blur">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center">
              <Globe size={18} />
            </div>
            <span className="text-lg font-bold">CloudShield CDN</span>
          </div>
          <nav className="hidden md:flex items-center gap-6 text-sm text-text-secondary">
            <a href="#features" className="hover:text-primary transition-colors">产品功能</a>
            <a href="#pricing" className="hover:text-primary transition-colors">价格</a>
            <a href="#stats" className="hover:text-primary transition-colors">数据</a>
          </nav>
          <div className="flex items-center gap-3">
            <button onClick={() => navigate('/login')} className="btn btn-default text-xs">
              登录
            </button>
            <button onClick={() => navigate('/login')} className="btn btn-primary text-xs hidden sm:flex">
              免费试用
            </button>
          </div>
        </div>
      </header>

      {/* Hero */}
      <section className="relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-primary/5 pointer-events-none" />
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28 relative">
          <div className="max-w-3xl mx-auto text-center">
            <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-medium mb-6">
              <span className="relative flex h-2 w-2">
                <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75" />
                <span className="relative inline-flex rounded-full h-2 w-2 bg-primary" />
              </span>
              企业级 CDN 防护加速平台
            </div>
            <h1 className="text-4xl md:text-6xl font-extrabold tracking-tight mb-6">
              比鹿云盾更开放
              <span className="text-primary block mt-2">比 SCDN 更经济</span>
            </h1>
            <p className="text-lg text-text-secondary mb-8 max-w-2xl mx-auto leading-relaxed">
              自主可控的企业级 CDN 防护加速后台，源码交付、数据私有、按需部署。告别平台绑架与高额流量税，为电商、金融、游戏、政企提供一站式加速与 WAF 解决方案。
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <button
                onClick={() => navigate('/login')}
                className="btn btn-primary px-6 py-2.5 text-sm flex items-center gap-2"
              >
                立即开始使用 <ArrowRight size={16} />
              </button>
              <button
                onClick={() => navigate('/login')}
                className="btn btn-default px-6 py-2.5 text-sm"
              >
                查看控制台演示
              </button>
            </div>
            <div className="flex flex-wrap items-center justify-center gap-4 mt-10 text-xs text-text-secondary">
              {highlights.map((h, i) => (
                <div key={i} className="flex items-center gap-1.5">
                  <span className="text-primary">{h.icon}</span>
                  <span>{h.text}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Stats */}
      <section id="stats" className="border-y border-border bg-black/[0.02] dark:bg-white/[0.02]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((s, i) => (
              <div key={i} className="text-center">
                <div className="text-3xl md:text-4xl font-bold text-primary mb-1">{s.value}</div>
                <div className="text-sm text-text-secondary">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Features */}
      <section id="features" className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto mb-14">
            <h2 className="text-2xl md:text-3xl font-bold mb-4">全栈加速与防护能力</h2>
            <p className="text-text-secondary">从边缘加速到安全防御，覆盖业务上线、运营、增长全生命周期。</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((f, i) => (
              <div
                key={i}
                className="card p-6 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200"
              >
                <div className="w-10 h-10 rounded-lg bg-primary/10 text-primary flex items-center justify-center mb-4">
                  {f.icon}
                </div>
                <h3 className="text-lg font-semibold mb-2">{f.title}</h3>
                <p className="text-sm text-text-secondary leading-relaxed">{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Comparison */}
      <section className="py-20 bg-black/[0.02] dark:bg-white/[0.02]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto mb-14">
            <h2 className="text-2xl md:text-3xl font-bold mb-4">为什么选择 CloudShield CDN？</h2>
            <p className="text-text-secondary">源码级交付，数据与部署完全自主，不再被第三方平台绑定。</p>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-sm border-collapse">
              <thead>
                <tr className="border-b border-border">
                  <th className="py-4 px-4 text-left font-semibold">能力</th>
                  <th className="py-4 px-4 text-center font-semibold text-primary">CloudShield CDN</th>
                  <th className="py-4 px-4 text-center font-semibold text-text-secondary">鹿云盾</th>
                  <th className="py-4 px-4 text-center font-semibold text-text-secondary">SCDN</th>
                </tr>
              </thead>
              <tbody className="text-text-secondary">
                {[
                  ['源码交付 / 二次开发', '支持', '不支持', '不支持'],
                  ['数据私有 / 自主部署', '支持', '部分支持', '不支持'],
                  ['一次性授权 / 无流量税', '支持', '按量计费', '按量计费'],
                  ['多商户 / 代理分销', '内置', '增值模块', '增值模块'],
                  ['自定义节点与套餐', '完全开放', '受限', '受限'],
                  ['EasyPanel 虚拟主机部署', '一键安装', '不适用', '不适用'],
                ].map((row, i) => (
                  <tr key={i} className="border-b border-border last:border-0">
                    <td className="py-4 px-4">{row[0]}</td>
                    <td className="py-4 px-4 text-center text-success font-medium">{row[1]}</td>
                    <td className="py-4 px-4 text-center">{row[2]}</td>
                    <td className="py-4 px-4 text-center">{row[3]}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* Pricing */}
      <section id="pricing" className="py-20 bg-black/[0.02] dark:bg-white/[0.02]">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center max-w-2xl mx-auto mb-14">
            <h2 className="text-2xl md:text-3xl font-bold mb-4">灵活的价格方案</h2>
            <p className="text-text-secondary">按量计费与包年包月灵活选择，满足不同规模业务需求。</p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {plans.map((p, i) => (
              <div
                key={i}
                className={`card p-6 relative ${p.highlighted ? 'border-primary ring-1 ring-primary' : ''}`}
              >
                {p.highlighted && (
                  <div className="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-primary text-white text-xs rounded-full">
                    最受欢迎
                  </div>
                )}
                <h3 className="text-lg font-semibold">{p.name}</h3>
                <p className="text-sm text-text-secondary mt-1 mb-4">{p.desc}</p>
                <div className="flex items-baseline gap-1 mb-6">
                  <span className="text-4xl font-bold">¥{p.price}</span>
                  <span className="text-text-secondary">/{p.period}</span>
                </div>
                <ul className="space-y-3 mb-6">
                  {p.features.map((feat, idx) => (
                    <li key={idx} className="flex items-center gap-2 text-sm text-text-secondary">
                      <CheckCircle size={14} className="text-success shrink-0" />
                      <span>{feat}</span>
                    </li>
                  ))}
                </ul>
                <button
                  onClick={() => navigate('/login')}
                  className={`w-full btn text-sm ${p.highlighted ? 'btn-primary' : 'btn-default'}`}
                >
                  选择方案
                </button>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-20">
        <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="card p-8 md:p-12 text-center bg-gradient-to-br from-primary/5 to-primary/10 border-primary/20">
            <h2 className="text-2xl md:text-3xl font-bold mb-4">准备好加速您的业务了吗？</h2>
            <p className="text-text-secondary mb-8 max-w-xl mx-auto">
              立即注册，享受 7 天免费试用。专业技术团队协助您完成接入与配置。
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <button
                onClick={() => navigate('/login')}
                className="btn btn-primary px-6 py-2.5 text-sm flex items-center gap-2"
              >
                免费试用 7 天 <ArrowRight size={16} />
              </button>
              <button
                onClick={() => navigate('/login')}
                className="btn btn-default px-6 py-2.5 text-sm flex items-center gap-1"
              >
                联系销售 <ChevronRight size={14} />
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-border py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row items-center justify-between gap-4">
            <div className="flex items-center gap-2">
              <div className="w-7 h-7 rounded-lg bg-primary text-white flex items-center justify-center">
                <Globe size={16} />
              </div>
              <span className="font-semibold">CloudShield CDN</span>
            </div>
            <div className="text-sm text-text-secondary">
              © 2026 CloudShield CDN. 企业级 CDN 防护加速平台.
            </div>
            <div className="flex items-center gap-4 text-sm text-text-secondary">
              <button onClick={() => navigate('/login')} className="hover:text-primary">登录</button>
              <button onClick={() => navigate('/login')} className="hover:text-primary">控制台</button>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}
