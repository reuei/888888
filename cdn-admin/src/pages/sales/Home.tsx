import { useNavigate } from 'react-router-dom';
import {
  Server,
  Download,
  ArrowRight,
  CheckCircle,
  Shield,
  Zap,
  Globe,
  Lock,
  Code,
  Headphones,
  Cpu,
} from 'lucide-react';
import Hero3D from '../../components/sales/Hero3D';
import SalesHeader from '../../components/sales/SalesHeader';
import SalesFooter from '../../components/sales/SalesFooter';
import AnnouncementModal from '../../components/sales/AnnouncementModal';
import { useState, useEffect } from 'react';

const highlights = [
  { icon: Shield, title: '源码授权', desc: '完整前后端源码交付，自主可控二次开发' },
  { icon: Server, title: 'CDN 节点', desc: '国内优质节点资源，按量/按带宽灵活选购' },
  { icon: Download, title: '在线更新', desc: '版本终身免费更新，一键升级无忧' },
  { icon: Headphones, title: '技术支持', desc: '7×24 小时专家答疑与部署协助' },
];

const stats = [
  { value: '500+', label: '企业客户' },
  { value: '99.99%', label: '服务可用性' },
  { value: '50+', label: '覆盖节点' },
  { value: '24/7', label: '技术支撑' },
];

const sourcePlans = [
  { name: '标准授权', price: '9,999', desc: '单域名部署授权', features: ['完整源码', '部署文档', '1年更新', '社区支持'] },
  { name: '商业授权', price: '29,999', desc: '多域名商业使用', features: ['完整源码', '部署文档', '终身更新', '专属客服', '二次开发指导'], highlighted: true },
  { name: '旗舰授权', price: '59,999', desc: '无限制企业授权', features: ['完整源码', '白标方案', '终身更新', '1对1架构师', '源码定制'] },
];

export default function SalesHome() {
  const navigate = useNavigate();
  const [showAnnouncement, setShowAnnouncement] = useState(false);

  useEffect(() => {
    const closed = sessionStorage.getItem('sales-announcement-closed');
    if (!closed) {
      const timer = setTimeout(() => setShowAnnouncement(true), 1500);
      return () => clearTimeout(timer);
    }
  }, []);

  return (
    <div className="min-h-screen flex flex-col bg-[var(--sales-bg)] text-[var(--sales-text)]">
      <SalesHeader />

      <main className="flex-1">
        {/* Hero */}
        <section className="relative overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-[var(--sales-primary)]/5 via-transparent to-[var(--sales-accent)]/5 pointer-events-none" />
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
              <div className="order-2 lg:order-1">
                <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-6">
                  <span className="relative flex h-2 w-2">
                    <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-[var(--sales-primary)] opacity-75" />
                    <span className="relative inline-flex rounded-full h-2 w-2 bg-[var(--sales-primary)]" />
                  </span>
                  CloudShield 官方销售系统
                </div>
                <h1 className="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6">
                  企业级 CDN
                  <span className="block mt-2 bg-clip-text text-transparent bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)]">
                    源码与节点一站式采购
                  </span>
                </h1>
                <p className="text-lg text-[var(--sales-text-secondary)] mb-8 max-w-xl leading-relaxed">
                  对标鹿云盾与 SCDN 的自主可控 CDN 平台。源码交付、节点自选、在线更新，让您的业务加速与安全不再受制于人。
                </p>
                <div className="flex flex-col sm:flex-row gap-3">
                  <button
                    onClick={() => navigate('/buy-source')}
                    className="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 hover:-translate-y-0.5 transition-all"
                  >
                    <Code size={18} />
                    购买源码授权 <ArrowRight size={16} />
                  </button>
                  <button
                    onClick={() => navigate('/buy-nodes')}
                    className="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-[var(--sales-text)] bg-[var(--sales-card)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/50 transition-all"
                  >
                    <Server size={18} />
                    选购 CDN 节点
                  </button>
                </div>
              </div>
              <div className="order-1 lg:order-2">
                <Hero3D />
              </div>
            </div>
          </div>
        </section>

        {/* Stats */}
        <section className="border-y border-[var(--sales-border)] bg-[var(--sales-card)]/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
              {stats.map((s, i) => (
                <div key={i} className="text-center">
                  <div className="text-3xl md:text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] mb-1">
                    {s.value}
                  </div>
                  <div className="text-sm text-[var(--sales-text-secondary)]">{s.label}</div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Highlights */}
        <section className="py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">为什么选择 CloudShield Store</h2>
              <p className="text-[var(--sales-text-secondary)]">从源码授权到节点采购，提供完整的企业级 CDN 解决方案。</p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {highlights.map((h, i) => (
                <div
                  key={i}
                  className="group p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/30 hover:shadow-xl hover:shadow-[var(--sales-primary)]/10 hover:-translate-y-1 transition-all duration-300"
                >
                  <div className="w-12 h-12 rounded-xl bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] text-white flex items-center justify-center mb-4 shadow-lg shadow-[var(--sales-primary)]/30 group-hover:scale-110 transition-transform">
                    <h.icon size={22} />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">{h.title}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)] leading-relaxed">{h.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Source Plans */}
        <section className="py-20 bg-[var(--sales-card)]/50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">源码授权方案</h2>
              <p className="text-[var(--sales-text-secondary)]">一次购买，终身使用，源码交付，自主可控。</p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              {sourcePlans.map((p, i) => (
                <div
                  key={i}
                  className={`relative p-6 rounded-2xl bg-[var(--sales-card)] border ${
                    p.highlighted
                      ? 'border-[var(--sales-primary)] ring-1 ring-[var(--sales-primary)] shadow-xl shadow-[var(--sales-primary)]/15'
                      : 'border-[var(--sales-border)]'
                  }`}
                >
                  {p.highlighted && (
                    <div className="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] text-white text-xs rounded-full font-medium">
                      最受欢迎
                    </div>
                  )}
                  <h3 className="text-lg font-semibold">{p.name}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)] mt-1 mb-4">{p.desc}</p>
                  <div className="flex items-baseline gap-1 mb-6">
                    <span className="text-lg text-[var(--sales-text-secondary)]">¥</span>
                    <span className="text-4xl font-bold">{p.price}</span>
                    <span className="text-sm text-[var(--sales-text-secondary)]">/起</span>
                  </div>
                  <ul className="space-y-3 mb-6">
                    {p.features.map((feat, idx) => (
                      <li key={idx} className="flex items-center gap-2 text-sm text-[var(--sales-text-secondary)]">
                        <CheckCircle size={14} className="text-[var(--sales-success)] shrink-0" />
                        <span>{feat}</span>
                      </li>
                    ))}
                  </ul>
                  <button
                    onClick={() => navigate('/buy-source')}
                    className={`w-full py-2.5 rounded-xl text-sm font-medium transition-all ${
                      p.highlighted
                        ? 'text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50'
                        : 'text-[var(--sales-text)] bg-[var(--sales-bg)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/50'
                    }`}
                  >
                    立即咨询
                  </button>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Features Grid */}
        <section className="py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">平台核心能力</h2>
              <p className="text-[var(--sales-text-secondary)]">对标行业领先产品，覆盖加速、防护、管理全流程。</p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[
                { icon: Zap, title: '智能加速', desc: '全球节点智能调度，静态资源缓存命中率高达 99%。' },
                { icon: Shield, title: 'DDoS 防护', desc: 'T 级清洗能力，有效抵御 CC、SYN Flood 等攻击。' },
                { icon: Lock, title: 'WAF 防火墙', desc: '自定义规则引擎，拦截 SQL 注入、XSS 等 Web 攻击。' },
                { icon: Globe, title: '多协议支持', desc: '支持 HTTP/2、HTTP/3、WebSocket、TLS 1.3。' },
                { icon: Cpu, title: '实时监控', desc: '流量、带宽、QPS 多维可视化，分钟级告警。' },
                { icon: Code, title: '开放 API', desc: '完整 RESTful API，方便对接自有业务系统。' },
              ].map((f, i) => (
                <div
                  key={i}
                  className="p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/30 transition-all"
                >
                  <div className="w-10 h-10 rounded-lg bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] flex items-center justify-center mb-4">
                    <f.icon size={20} />
                  </div>
                  <h3 className="text-base font-semibold mb-2">{f.title}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)] leading-relaxed">{f.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="py-20 bg-[var(--sales-card)]/50">
          <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="relative overflow-hidden rounded-3xl p-8 md:p-12 text-center bg-gradient-to-br from-[var(--sales-primary)]/10 to-[var(--sales-accent)]/10 border border-[var(--sales-primary)]/20">
              <div className="absolute top-0 right-0 w-64 h-64 bg-[var(--sales-primary)]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
              <h2 className="text-2xl md:text-3xl font-bold mb-4 relative">准备好构建您的 CDN 平台了吗？</h2>
              <p className="text-[var(--sales-text-secondary)] mb-8 max-w-xl mx-auto relative">
                立即咨询获取源码授权报价与节点资源清单，专业技术团队协助您完成部署。
              </p>
              <div className="flex flex-col sm:flex-row items-center justify-center gap-3 relative">
                <button
                  onClick={() => navigate('/buy-source')}
                  className="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 hover:-translate-y-0.5 transition-all"
                >
                  获取报价 <ArrowRight size={16} />
                </button>
                <button
                  onClick={() => navigate('/buy-nodes')}
                  className="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-medium text-[var(--sales-text)] bg-[var(--sales-card)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/50 transition-all"
                >
                  查看节点资源
                </button>
              </div>
            </div>
          </div>
        </section>
      </main>

      <SalesFooter />
      <AnnouncementModal isOpen={showAnnouncement} onClose={() => setShowAnnouncement(false)} />
    </div>
  );
}
