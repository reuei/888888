import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  ArrowRight,
  Rocket,
  Zap,
  Wrench,
  Wallet,
  Newspaper,
  Shield,
  Globe,
  UserCircle,
  Headphones,
  Users,
  Database,
  LayoutDashboard,
  Server,
  Cpu,
  Lock,
  Code2,
  TrendingUp,
  Cloud,
  FileText,
  MessageSquare,
  ShoppingCart,
  RefreshCw,
  ChevronRight,
  HelpCircle,
  Phone,
} from 'lucide-react';
import SalesHeader from '../../components/sales/SalesHeader';
import SalesFooter from '../../components/sales/SalesFooter';
import AnnouncementModal from '../../components/sales/AnnouncementModal';

const slides = [
  {
    tag: '核心',
    tagGradient: 'from-[#5678f5] to-[#9275ff]',
    title: '全局加速与边缘防护统一平台',
    highlight: '统一平台',
    desc: '覆盖全球的边缘节点与智能调度体系，为您的业务提供低延迟、高可用的加速与安全防护。',
    points: ['智能路由调度', 'DDoS 防护', 'WAF 规则引擎', '实时流量分析'],
    cta: '了解边缘加速',
    route: '/buy-nodes',
  },
  {
    tag: '热门',
    tagGradient: 'from-[#ff7a45] to-[#ff4d6d]',
    title: '企业级 WAF 防护防火墙',
    highlight: 'WAF 防护',
    desc: '基于规则与行为分析的多层 Web 防护，精准拦截 SQL 注入、XSS、CC 攻击等常见威胁。',
    points: ['自定义规则', 'Bot 管理', '速率限制', '攻击可视化'],
    cta: '查看防护能力',
    route: '/buy-source',
  },
  {
    tag: '推荐',
    tagGradient: 'from-[#22c55e] to-[#14b8a6]',
    title: '商业化运营与套餐管理',
    highlight: '套餐管理',
    desc: '灵活的套餐、计费与分销体系，助力您快速构建可商业化的 CDN 与边缘服务平台。',
    points: ['套餐模板', '多级代理', '自动结算', '数据统计'],
    cta: '探索商业方案',
    route: '/buy-source',
  },
  {
    tag: '费用',
    tagGradient: 'from-[#f59e0b] to-[#f97316]',
    title: '透明费用中心与订单管理',
    highlight: '订单管理',
    desc: '实时账单、明细流水与多维度报表，让您的每一笔支出都清晰可见、可控可管。',
    points: ['实时账单', '套餐订单', '充值记录', '发票管理'],
    cta: '查看费用中心',
    route: '/orders',
  },
  {
    tag: '工单',
    tagGradient: 'from-[#3b82f6] to-[#8b5cf6]',
    title: '7×24 小时工单与技术支持',
    highlight: '技术支持',
    desc: '专业技术团队全天候响应，从接入调试到故障排查，为您提供全生命周期的服务支持。',
    points: ['快速响应', '专家一对一', '在线文档', '远程协助'],
    cta: '提交工单',
    route: '/announcements',
  },
];

const quickCards = [
  { icon: Rocket, label: '快速开始', desc: '快速接入与部署指南', route: '/buy-source' },
  { icon: Zap, label: '边缘加速', desc: '全球节点智能调度', route: '/buy-nodes' },
  { icon: Wrench, label: '运维', desc: '监控告警与日志审计', route: '/announcements' },
  { icon: Wallet, label: '费用', desc: '账单、订单与套餐', route: '/orders' },
  { icon: Newspaper, label: '产品动态', desc: '最新功能与更新', route: '/updates' },
];

const products = [
  { icon: Shield, label: '边缘加速与安全', desc: '核心产品', tag: '核心', tagGradient: 'from-[#5678f5] to-[#9275ff]', route: '/buy-nodes' },
  { icon: Wallet, label: '费用中心', desc: '账单与订单管理', route: '/orders' },
  { icon: UserCircle, label: '账号中心', desc: '实名、权限与安全', route: '/user' },
  { icon: Headphones, label: '工单与支持', desc: '7×24 在线服务', route: '/announcements' },
  { icon: Users, label: '渠道与分销', desc: '合作伙伴计划', tag: '合作伙伴', tagGradient: 'from-[#22c55e] to-[#14b8a6]', route: '/buy-source' },
  { icon: Database, label: '对象存储', desc: '即将开放', muted: true, route: '/updates' },
  { icon: LayoutDashboard, label: '平台控制台', desc: '统一运营与管理', route: '/user' },
];

const steps = [
  { number: '01', title: '注册账号', desc: '完成企业/个人实名认证' },
  { number: '02', title: '创建站点', desc: '接入域名并选择套餐' },
  { number: '03', title: '配置上线', desc: '一键下发并启用加速' },
];

const capabilityTabs = [
  {
    key: 'acceleration',
    label: '边缘加速',
    icon: Zap,
    items: [
      { icon: Globe, title: '全球节点调度', desc: '智能 DNS 与 Anycast，自动选择最优边缘节点。' },
      { icon: Cpu, title: '智能缓存策略', desc: '多级缓存与预热机制，静态资源命中率高达 99%。' },
      { icon: Server, title: '多协议支持', desc: '支持 HTTP/2、HTTP/3、WebSocket、TLS 1.3。' },
      { icon: TrendingUp, title: '实时性能优化', desc: '动态压缩、Brotli、图片优化与智能路由。' },
    ],
  },
  {
    key: 'protection',
    label: 'Web 防护',
    icon: Shield,
    items: [
      { icon: Lock, title: 'WAF 防火墙', desc: '自定义规则引擎，拦截 SQL 注入、XSS、CSRF。' },
      { icon: Shield, title: 'DDoS 清洗', desc: 'T 级防护能力，抵御 SYN Flood、CC 等攻击。' },
      { icon: Code2, title: 'Bot 管理', desc: '人机识别与行为分析，保护核心接口。' },
      { icon: Cloud, title: '速率限制', desc: '基于 IP、URL、用户的精细化访问控制。' },
    ],
  },
  {
    key: 'commercial',
    label: '商业化运营',
    icon: ShoppingCart,
    items: [
      { icon: FileText, title: '套餐模板', desc: '按流量、带宽、域名灵活定义销售套餐。' },
      { icon: Users, title: '多级分销', desc: '代理商、渠道商分润与独立控制台。' },
      { icon: Wallet, title: '自动结算', desc: '佣金计算、提现审批与财务对账。' },
      { icon: TrendingUp, title: '运营报表', desc: '订单、用户、收入多维度统计。' },
    ],
  },
  {
    key: 'open',
    label: '开放能力',
    icon: Code2,
    items: [
      { icon: Code2, title: 'RESTful API', desc: '站点、域名、证书、套餐全量接口。' },
      { icon: RefreshCw, title: 'Webhook 事件', desc: '订单、告警、状态变更实时推送。' },
      { icon: Database, title: '数据导出', desc: '账单、日志、报表多格式导出。' },
      { icon: Server, title: 'CLI 工具', desc: '命令行快速部署与批量操作。' },
    ],
  },
];

const infrastructure = [
  { title: 'EdgeOne', desc: '统一边缘加速与安全防护平台', icon: Shield },
  { title: '异步同步', desc: '任务队列与数据同步机制', icon: RefreshCw },
  { title: '套餐订单', desc: '灵活的套餐计费与订单流转', icon: ShoppingCart },
  { title: '工单', desc: '全生命周期客户服务支持', icon: Headphones },
];

const floatActions = [
  { icon: Phone, label: '电话咨询', onClick: () => alert('请联系客服') },
  { icon: MessageSquare, label: '在线咨询', onClick: () => alert('请使用工单系统') },
  { icon: HelpCircle, label: '帮助中心', onClick: () => alert('帮助文档即将上线') },
];

const AUTOPLAY_MS = 5000;

export default function SalesHome() {
  const navigate = useNavigate();
  const [currentSlide, setCurrentSlide] = useState(0);
  const [progressKey, setProgressKey] = useState(0);
  const [activeTab, setActiveTab] = useState('acceleration');
  const [showAnnouncement, setShowAnnouncement] = useState(false);

  useEffect(() => {
    const closed = sessionStorage.getItem('sales-announcement-closed');
    if (!closed) {
      const timer = setTimeout(() => setShowAnnouncement(true), 1500);
      return () => clearTimeout(timer);
    }
  }, []);

  const goToSlide = useCallback((index: number) => {
    setCurrentSlide(index);
    setProgressKey((k) => k + 1);
  }, []);

  const nextSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev + 1) % slides.length);
    setProgressKey((k) => k + 1);
  }, []);

  useEffect(() => {
    const timer = setInterval(nextSlide, AUTOPLAY_MS);
    return () => clearInterval(timer);
  }, [nextSlide]);

  const activeCapability = capabilityTabs.find((t) => t.key === activeTab)!;

  return (
    <div className="min-h-screen flex flex-col bg-[var(--sales-bg)] text-[var(--sales-text)]">
      <SalesHeader />

      <main className="flex-1">
        {/* Hero carousel */}
        <section className="relative overflow-hidden bg-[#ecf2ff]">
          <div className="absolute top-0 right-0 w-[60%] h-full bg-gradient-to-l from-[#d6e4ff]/60 via-[#e8efff]/30 to-transparent pointer-events-none" />
          <div className="absolute top-20 right-20 w-96 h-96 bg-gradient-to-br from-[#0052d9]/10 to-[#9275ff]/10 rounded-full blur-3xl pointer-events-none" />
          <div className="absolute bottom-0 right-40 w-80 h-80 bg-gradient-to-tr from-[#4656ff]/10 to-[#22c55e]/5 rounded-full blur-3xl pointer-events-none" />

          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
              {/* Left controls */}
              <div className="hidden lg:flex lg:col-span-1 flex-col gap-3">
                {slides.map((_, index) => (
                  <button
                    key={index}
                    onClick={() => goToSlide(index)}
                    className={`group relative h-12 w-12 rounded-lg border flex items-center justify-center text-sm font-semibold transition-all ${
                      index === currentSlide
                        ? 'border-[#0052d9] text-white bg-gradient-to-br from-[#0052d9] to-[#4656ff] shadow-lg shadow-[#0052d9]/25'
                        : 'border-[var(--sales-border)] bg-white text-[var(--sales-text-secondary)] hover:border-[#0052d9]/40 hover:text-[#0052d9]'
                    }`}
                  >
                    {index + 1}
                    {index === currentSlide && (
                      <span
                        key={progressKey}
                        className="absolute -bottom-1 left-1/2 -translate-x-1/2 h-0.5 bg-[#0052d9]/30 rounded-full animate-progress"
                        style={{ width: '80%', animationDuration: `${AUTOPLAY_MS}ms` }}
                      />
                    )}
                  </button>
                ))}
              </div>

              {/* Slide content */}
              <div className="lg:col-span-6 relative z-10">
                <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-[var(--sales-border)] shadow-sm mb-5">
                  <span className={`inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gradient-to-r ${slides[currentSlide].tagGradient} text-white text-xs font-medium`}>
                    {slides[currentSlide].tag}
                  </span>
                  <span className="text-[var(--sales-text-secondary)] text-xs">EdgeOne 控制台</span>
                </div>
                <h1 className="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight mb-5 leading-tight">
                  {slides[currentSlide].title.split(slides[currentSlide].highlight)[0]}
                  <span className="bg-clip-text text-transparent bg-gradient-to-r from-[#0052d9] to-[#606eff]">
                    {slides[currentSlide].highlight}
                  </span>
                  {slides[currentSlide].title.split(slides[currentSlide].highlight)[1]}
                </h1>
                <p className="text-base md:text-lg text-[var(--sales-text-secondary)] mb-6 max-w-xl leading-relaxed">
                  {slides[currentSlide].desc}
                </p>
                <div className="flex flex-wrap gap-2 mb-8">
                  {slides[currentSlide].points.map((point, idx) => (
                    <span
                      key={idx}
                      className="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white border border-[var(--sales-border)] text-xs text-[var(--sales-text-secondary)]"
                    >
                      <span className="w-1.5 h-1.5 rounded-full bg-[#0052d9]" />
                      {point}
                    </span>
                  ))}
                </div>
                <div className="flex flex-col sm:flex-row gap-3">
                  <button
                    onClick={() => navigate(slides[currentSlide].route)}
                    className="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-md text-sm font-medium text-white bg-gradient-to-r from-[#0052d9] to-[#4656ff] shadow-lg shadow-[#0052d9]/25 hover:shadow-[#0052d9]/40 hover:-translate-y-0.5 transition-all"
                  >
                    {slides[currentSlide].cta} <ArrowRight size={16} />
                  </button>
                  <button
                    onClick={() => navigate('/login')}
                    className="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-md text-sm font-medium text-[#0052d9] bg-white border border-[#0052d9]/30 hover:border-[#0052d9] hover:bg-[#0052d9]/5 transition-all"
                  >
                    免费注册
                  </button>
                </div>
              </div>

              {/* Abstract illustration */}
              <div className="lg:col-span-5 relative hidden md:block">
                <div className="relative w-full aspect-square max-w-md mx-auto">
                  <div className="absolute inset-0 bg-gradient-to-br from-[#0052d9]/10 via-[#9275ff]/10 to-transparent rounded-full blur-2xl" />
                  <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 md:w-56 md:h-56">
                    <div className="absolute inset-0 bg-gradient-to-br from-[#0052d9] to-[#4656ff] rounded-[2rem] rotate-45 shadow-2xl shadow-[#0052d9]/30" />
                    <div className="absolute inset-0 flex items-center justify-center text-white drop-shadow-lg">
                      <Shield size={72} strokeWidth={1.2} />
                    </div>
                  </div>
                  {[Zap, Globe, Server, Lock].map((Icon, idx) => (
                    <div
                      key={idx}
                      className="absolute top-1/2 left-1/2 w-14 h-14 rounded-xl bg-white border border-[var(--sales-border)] shadow-lg flex items-center justify-center text-[#0052d9]"
                      style={{
                        transform: `translate(-50%, -50%) rotate(${idx * 90}deg) translateX(130px) rotate(${-idx * 90}deg)`,
                      }}
                    >
                      <Icon size={24} />
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </div>

          {/* Mobile controls */}
          <div className="flex lg:hidden justify-center gap-2 pb-6">
            {slides.map((_, index) => (
              <button
                key={index}
                onClick={() => goToSlide(index)}
                className={`h-2 rounded-full transition-all ${index === currentSlide ? 'w-8 bg-[#0052d9]' : 'w-2 bg-[var(--sales-border)]'}`}
              />
            ))}
          </div>
        </section>

        {/* Quick access cards */}
        <section className="relative z-20 -mt-6 md:-mt-8 mb-16">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
              {quickCards.map((card, index) => (
                <button
                  key={index}
                  onClick={() => navigate(card.route)}
                  className="group text-left p-4 md:p-5 rounded-lg bg-white border border-[var(--sales-border)] shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all"
                >
                  <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-[#0052d9]/10 to-[#9275ff]/10 text-[#0052d9] flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <card.icon size={18} />
                  </div>
                  <div className="font-semibold text-sm mb-1">{card.label}</div>
                  <div className="text-xs text-[var(--sales-text-secondary)]">{card.desc}</div>
                </button>
              ))}
            </div>
          </div>
        </section>

        {/* 3-step onboarding */}
        <section className="py-16 md:py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">三步快速上线</h2>
              <p className="text-[var(--sales-text-secondary)]">从账号注册到业务上线，最快仅需 5 分钟。</p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {steps.map((step, index) => (
                <div key={index} className="relative p-6 rounded-xl bg-[var(--sales-bg)] border border-[var(--sales-border)] overflow-hidden">
                  <span className="absolute -top-4 -right-2 text-8xl font-extrabold text-[#0052d9]/5 select-none pointer-events-none">
                    {step.number}
                  </span>
                  <div className="relative z-10">
                    <div className="text-4xl font-extrabold text-[#0052d9]/20 mb-3">{step.number}</div>
                    <h3 className="text-lg font-bold mb-2 flex items-center gap-2">
                      {step.title}
                      {index < steps.length - 1 && <ChevronRight size={16} className="text-[var(--sales-text-secondary)]" />}
                    </h3>
                    <p className="text-sm text-[var(--sales-text-secondary)]">{step.desc}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Products & services */}
        <section className="py-16 md:py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">平台产品与服务</h2>
              <p className="text-[var(--sales-text-secondary)]">覆盖加速、安全、运营、支持的全栈边缘云服务。</p>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
              {products.map((p, index) => (
                <button
                  key={index}
                  onClick={() => !p.muted && navigate(p.route)}
                  disabled={p.muted}
                  className={`group text-left p-5 rounded-lg border transition-all relative ${
                    p.muted
                      ? 'bg-[var(--sales-bg)]/50 border-[var(--sales-border)] opacity-70 cursor-not-allowed'
                      : 'bg-white border-[var(--sales-border)] hover:border-[#0052d9]/30 hover:shadow-lg hover:-translate-y-0.5'
                  }`}
                >
                  {p.tag && (
                    <span className={`absolute top-4 right-4 px-2 py-0.5 rounded text-[10px] text-white bg-gradient-to-r ${p.tagGradient}`}>
                      {p.tag}
                    </span>
                  )}
                  <div className={`w-10 h-10 rounded-lg flex items-center justify-center mb-4 ${p.muted ? 'bg-gray-100 text-gray-400' : 'bg-gradient-to-br from-[#0052d9]/10 to-[#9275ff]/10 text-[#0052d9] group-hover:scale-110 transition-transform'}`}>
                    <p.icon size={20} />
                  </div>
                  <h3 className="text-base font-semibold mb-1">{p.label}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)]">{p.desc}</p>
                </button>
              ))}
            </div>
          </div>
        </section>

        {/* Core capabilities tabs */}
        <section className="py-16 md:py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-10">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">核心能力</h2>
              <p className="text-[var(--sales-text-secondary)]">从边缘加速到开放集成，满足企业级业务需求。</p>
            </div>
            <div className="flex flex-wrap justify-center gap-2 mb-10">
              {capabilityTabs.map((tab) => (
                <button
                  key={tab.key}
                  onClick={() => setActiveTab(tab.key)}
                  className={`inline-flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium transition-all ${
                    activeTab === tab.key
                      ? 'text-white bg-gradient-to-r from-[#0052d9] to-[#4656ff] shadow-md'
                      : 'text-[var(--sales-text-secondary)] bg-[var(--sales-bg)] border border-[var(--sales-border)] hover:text-[#0052d9]'
                  }`}
                >
                  <tab.icon size={16} />
                  {tab.label}
                </button>
              ))}
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
              {activeCapability.items.map((item, index) => (
                <div
                  key={index}
                  className="p-5 rounded-lg bg-[var(--sales-bg)] border border-[var(--sales-border)] hover:border-[#0052d9]/30 transition-all"
                >
                  <div className="w-10 h-10 rounded-lg bg-white text-[#0052d9] flex items-center justify-center mb-4 shadow-sm">
                    <item.icon size={20} />
                  </div>
                  <h3 className="text-base font-semibold mb-2">{item.title}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)] leading-relaxed">{item.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Infrastructure */}
        <section className="py-16 md:py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center max-w-2xl mx-auto mb-14">
              <h2 className="text-2xl md:text-3xl font-bold mb-4">平台架构</h2>
              <p className="text-[var(--sales-text-secondary)]">稳定、可扩展的底层架构，支撑业务持续增长。</p>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
              {infrastructure.map((item, index) => (
                <div
                  key={index}
                  className="p-5 rounded-lg bg-white border border-[var(--sales-border)] hover:shadow-md transition-all"
                >
                  <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-[#0052d9] to-[#4656ff] text-white flex items-center justify-center mb-4">
                    <item.icon size={20} />
                  </div>
                  <h3 className="text-base font-semibold mb-1">{item.title}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)]">{item.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* Bottom CTA */}
        <section className="py-16 md:py-20">
          <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="relative overflow-hidden rounded-2xl p-8 md:p-12 text-center bg-gradient-to-r from-[#0052d9] to-[#4656ff] text-white shadow-xl">
              <div className="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2" />
              <div className="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2" />
              <h2 className="text-2xl md:text-3xl font-bold mb-4 relative">立即开启边缘加速之旅</h2>
              <p className="text-white/80 mb-8 max-w-xl mx-auto relative">
                注册即可免费体验，专业技术团队协助您完成接入与配置。
              </p>
              <div className="flex flex-col sm:flex-row items-center justify-center gap-3 relative">
                <button
                  onClick={() => navigate('/login')}
                  className="inline-flex items-center gap-2 px-6 py-3 rounded-md text-sm font-medium text-[#0052d9] bg-white hover:bg-white/90 shadow-lg transition-all"
                >
                  免费注册 <ArrowRight size={16} />
                </button>
                <button
                  onClick={() => navigate('/buy-nodes')}
                  className="inline-flex items-center gap-2 px-6 py-3 rounded-md text-sm font-medium text-white border border-white/40 hover:bg-white/10 transition-all"
                >
                  了解产品
                </button>
              </div>
            </div>
          </div>
        </section>
      </main>

      <SalesFooter />

      {/* Floating quick actions */}
      <div className="fixed right-4 bottom-24 md:bottom-10 z-40 flex flex-col gap-2">
        {floatActions.map((action, index) => (
          <button
            key={index}
            onClick={action.onClick}
            className="group flex items-center justify-center w-10 h-10 rounded-full bg-white border border-[var(--sales-border)] shadow-md text-[var(--sales-text-secondary)] hover:text-[#0052d9] hover:border-[#0052d9]/30 transition-all"
            title={action.label}
          >
            <action.icon size={18} />
          </button>
        ))}
      </div>

      <AnnouncementModal isOpen={showAnnouncement} onClose={() => setShowAnnouncement(false)} />
    </div>
  );
}
