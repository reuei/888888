import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowRight, Shield, Zap, Globe, Lock, BarChart3, Server, CheckCircle, ChevronDown, Database, Layers, Key, RefreshCw } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const slides = [
  {
    title: '6cdn 专业的CDN加速、防御服务商',
    subtitle: '加速您的在线世界，无处不在的访问速度',
    desc: '为用户提供了强大的工具，以优化数字体验，满足现代用户对速度、安全性和可靠性的高标准要求。',
    button: '前往体验',
    route: '/cdn/login',
    bgColor: 'from-blue-600 via-blue-700 to-indigo-800',
    features: ['极速访问 – 三网优化节点，全绿访问，以最快的方式访问资源', '安全稳定 – 千G群防智能负载，无感抗压，实力保护源站安全', '快速接入 – 操作简单，5分钟快速接入，提供完整的接入文档'],
  },
  {
    title: 'DDoS攻击防御',
    subtitle: '企业级防护 · 智能清洗 · 无感防御',
    desc: '语鹿云盾全方位保护源站安全，以达到用户无感防御的效果',
    button: '了解更多',
    route: '/cdn',
    bgColor: 'from-purple-600 via-purple-700 to-indigo-800',
    features: ['流量清洗', '攻击检测', '智能调度', '实时告警'],
  },
  {
    title: 'CC攻击防御',
    subtitle: '多种模式 · 智能识别 · 精准拦截',
    desc: '支持多种防cc模式，满足多种场景防CC需求',
    button: '免费试用',
    route: '/cdn',
    bgColor: 'from-teal-600 via-teal-700 to-cyan-800',
    features: ['请求速率限制', 'JS浏览器识别', '验证码验证', '滑动验证'],
  },
];

const cdnFeatures = [
  { icon: Globe, title: 'CDN内容分发', desc: '客户就近加速，根据所在地区智能分配最近节点，提前预缓存资源，超高资源命中率' },
  { icon: Shield, title: 'DDoS攻击防御', desc: '企业级DDoS防护，多样化访问控制策略，6cdn全方位保护源站安全，以达到用户无感防御' },
  { icon: Zap, title: 'CC攻击防御', desc: '支持多种防cc模式，如请求速率，跳转，JS浏览器识别，验证码，滑动等，以满足多种场景防CC需求' },
  { icon: Layers, title: '四层代理支持', desc: '不仅支持七层代理如http，https，也支持四层协议代理，如tcp和udp' },
  { icon: Lock, title: '免费申请证书', desc: '可一键申请Let\'s encrypt等免费证书，可上传已有证书，自动续期证书，一键开启网站HTTPS支持' },
  { icon: Database, title: '高级缓存配置', desc: '针对后缀名，目录，全路径来设置缓存。可设置不需要缓存的资源，实现缓存的精细管理' },
  { icon: BarChart3, title: '实时统计', desc: '可查询网站带宽，流量，命中率，回源质量等的关键指标，实时了解网站运行状态' },
  { icon: Server, title: '负载均衡', desc: '高防CDN节点为您的业务提供负载均衡管理，网站源站也可支持配置多个回源实现负载均衡' },
];

const services = [
  {
    icon: Zap,
    title: '加速',
    desc: '我们拥有高质量的千兆节点及 CN2 线路节点，确保在高峰期也能保持稳定的网络速度。国内提供百兆节点提升加速效果，让您的用户无论身处何地，都能享受到流畅的访问体验。',
  },
  {
    icon: Shield,
    title: '防御',
    desc: '我们的 CDN 具备强大的防护能力，能够有效抵御 CC 攻击和 DDoS 攻击。我们拥有大量节点实现负载均衡，并动态调配防御策略，确保您的网络始终保持稳定和安全。',
  },
  {
    icon: Key,
    title: '价格',
    desc: '我们的价格非常亲民，提供多种套餐选择。无论是轻量级还是重量级需求，您都能找到适合的解决方案，确保高性价比和灵活性。',
  },
  {
    icon: RefreshCw,
    title: '服务',
    desc: '我们提供详细的对接文档，并且提供7x24小时技术支持。无论何时何地，我们的一对一指导对接服务都能确保您顺利上手，快速解决问题。',
  },
];

const faqs = [
  {
    question: '国内高防CDN域名需要备案吗？',
    answer: '需要的，国内高防型均采用国内高防机房机器，强制备案域名。如您域名未备案可以选择亚太或美国型CDN。',
  },
  {
    question: '源服务器在欧美，使用亚太CDN可以加速吗？',
    answer: '可以的，亚太CDN节点覆盖亚洲地区，对于欧美源站同样可以提供加速服务，特别是针对亚洲用户的访问速度会有明显提升。',
  },
  {
    question: '套完CDN可以隐藏源服务器IP吗?',
    answer: '是的，使用CDN后，用户访问的是CDN节点的IP，源服务器IP会被隐藏，有效保护源站安全。但需要注意正确配置，避免通过其他方式泄露源站IP。',
  },
  {
    question: '不会使用CDN，有教程可以协助吗？',
    answer: '当然可以，我们提供详细的接入文档和视频教程，同时提供7x24小时技术支持。您可以随时联系客服获取一对一指导，确保您顺利完成接入。',
  },
];

const AUTOPLAY_MS = 5000;

export default function CdnHome() {
  const navigate = useNavigate();
  const [currentSlide, setCurrentSlide] = useState(0);
  const [progress, setProgress] = useState(0);
  const [activeFaq, setActiveFaq] = useState<number | null>(0);

  const nextSlide = useCallback(() => {
    setCurrentSlide((prev) => (prev + 1) % slides.length);
    setProgress(0);
  }, []);

  useEffect(() => {
    const timer = setInterval(() => {
      setProgress((prev) => {
        if (prev >= 100) {
          nextSlide();
          return 0;
        }
        return prev + (100 / (AUTOPLAY_MS / 100));
      });
    }, 100);
    return () => clearInterval(timer);
  }, [nextSlide]);

  const goToSlide = useCallback((index: number) => {
    setCurrentSlide(index);
    setProgress(0);
  }, []);

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main>
        <section className="relative h-screen overflow-hidden">
          {slides.map((slide, index) => (
            <div
              key={index}
              className={`absolute inset-0 transition-opacity duration-1000 ${
                index === currentSlide ? 'opacity-100' : 'opacity-0'
              }`}
            >
              <div className={`absolute inset-0 bg-gradient-to-br ${slide.bgColor}`} />
              <div className="absolute inset-0 bg-[url('https://trae-api-cn.mchost.guru/api/ide/v1/text_to_image?prompt=abstract%20network%20technology%20background%20with%20flowing%20data%20streams%20and%20geometric%20shapes%20blue%20purple%20gradient&image_size=landscape_16_9')] bg-cover bg-center opacity-30" />
              <div className="absolute inset-0 bg-black/20" />
            </div>
          ))}

          <div className="relative z-10 h-full flex items-center">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
              <div className="text-center">
                <div
                  key={currentSlide}
                  className="transition-all duration-1000"
                  style={{ animation: 'fadeIn 1s ease' }}
                >
                  <p className="text-xl md:text-2xl text-blue-100 mb-4 drop-shadow-md">
                    {slides[currentSlide].subtitle}
                  </p>
                  <h1 className="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 drop-shadow-lg">
                    {slides[currentSlide].title}
                  </h1>
                  <p className="text-base md:text-lg text-blue-200 max-w-2xl mx-auto mb-8">
                    {slides[currentSlide].desc}
                  </p>
                  <ul className="text-left max-w-xl mx-auto space-y-3 mb-8">
                    {slides[currentSlide].features.map((feature, idx) => (
                      <li key={idx} className="flex items-center gap-3 text-white/90">
                        <CheckCircle size={18} className="text-green-400" />
                        <span>{feature}</span>
                      </li>
                    ))}
                  </ul>
                  <button
                    onClick={() => navigate(slides[currentSlide].route)}
                    className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-blue-600 bg-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all"
                  >
                    {slides[currentSlide].button}
                    <ArrowRight size={20} />
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div className="absolute bottom-0 left-0 right-0 z-20">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
              <div className="flex items-center justify-between mb-4">
                <span className="text-white/70 text-sm font-medium">
                  {currentSlide + 1} / {slides.length}
                </span>
                <div className="flex gap-2">
                  {slides.map((_, index) => (
                    <button
                      key={index}
                      onClick={() => goToSlide(index)}
                      className={`w-2 h-2 rounded-full transition-all ${
                        index === currentSlide ? 'bg-white w-6' : 'bg-white/50'
                      }`}
                    />
                  ))}
                </div>
              </div>
              <div className="h-1 bg-white/20 rounded-full overflow-hidden">
                <div
                  className="h-full bg-white rounded-full transition-all duration-100"
                  style={{ width: `${progress}%` }}
                />
              </div>
            </div>
          </div>

          <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 animate-bounce">
            <ChevronDown className="w-8 h-8 text-white/50" />
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                一站式CDN集成系统
              </h2>
              <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                为您提供一站式网站加速及防御WAF防火墙智能拦截，在提供加速的同时并保护网站免受恶意软件和数据泄露的威胁。
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {cdnFeatures.map((feature, index) => (
                <div
                  key={index}
                  className="p-6 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors"
                >
                  <div className="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mb-4">
                    <feature.icon size={24} />
                  </div>
                  <h3 className="font-semibold text-gray-800 mb-2">{feature.title}</h3>
                  <p className="text-sm text-gray-500">{feature.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                我们为您提供的服务
              </h2>
              <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                选择我们的CDN服务，凭借全球加速节点大幅提升访问速度，强力防御体系全面保护您的网站安全，并提供7x24小时专业支持，确保您的业务始终高效、稳定运行！
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
              {services.map((service, index) => (
                <div
                  key={index}
                  className="p-8 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-lg transition-shadow"
                >
                  <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mb-6">
                    <service.icon size={32} />
                  </div>
                  <h3 className="text-xl font-bold text-gray-800 mb-3">{service.title}</h3>
                  <p className="text-gray-600">{service.desc}</p>
                </div>
              ))}
            </div>

            <div className="mt-16 text-center">
              <button
                onClick={() => navigate('/cdn/login')}
                className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-800 shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all"
              >
                立即体验
                <ArrowRight size={20} />
              </button>
            </div>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                为什么加入6cdn
              </h2>
              <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                专门针对国内的使用环境进行三网优化，持续稳定的加速服务，让页面更快开启
              </p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div className="bg-gray-50 rounded-2xl p-6">
                <h3 className="text-xl font-bold text-gray-800 mb-4">未使用CDN前的全国访问速度</h3>
                <div className="space-y-3">
                  <div className="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <span className="text-gray-700">好（＜1s）</span>
                    <span className="text-red-600 font-semibold">20%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                    <span className="text-gray-700">较好（1s~2s）</span>
                    <span className="text-orange-600 font-semibold">30%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span className="text-gray-700">警告（2s~3s）</span>
                    <span className="text-yellow-600 font-semibold">25%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-100 rounded-lg">
                    <span className="text-gray-700">较差（3s~5s）</span>
                    <span className="text-gray-600 font-semibold">15%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-200 rounded-lg">
                    <span className="text-gray-700">差（＞5s）</span>
                    <span className="text-gray-500 font-semibold">10%</span>
                  </div>
                </div>
              </div>

              <div className="bg-gray-50 rounded-2xl p-6">
                <h3 className="text-xl font-bold text-gray-800 mb-4">使用CDN后的全国访问速度</h3>
                <div className="space-y-3">
                  <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span className="text-gray-700">好（＜1s）</span>
                    <span className="text-green-600 font-semibold">85%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span className="text-gray-700">较好（1s~2s）</span>
                    <span className="text-blue-600 font-semibold">12%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span className="text-gray-700">警告（2s~3s）</span>
                    <span className="text-yellow-600 font-semibold">2%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-100 rounded-lg opacity-50">
                    <span className="text-gray-700">较差（3s~5s）</span>
                    <span className="text-gray-600 font-semibold">1%</span>
                  </div>
                  <div className="flex items-center justify-between p-3 bg-gray-200 rounded-lg opacity-30">
                    <span className="text-gray-700">差（＞5s）</span>
                    <span className="text-gray-500 font-semibold">0%</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section className="py-20 bg-gray-50">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                常见问题
              </h2>
              <p className="text-lg text-gray-600">
                关于6cdn的常见问题解答
              </p>
            </div>

            <div className="space-y-4">
              {faqs.map((faq, index) => (
                <div
                  key={index}
                  className="rounded-xl bg-white border border-gray-100 overflow-hidden"
                >
                  <button
                    onClick={() => setActiveFaq(activeFaq === index ? null : index)}
                    className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
                  >
                    <span className="font-semibold text-gray-800">{faq.question}</span>
                    <ChevronDown
                      className={`w-5 h-5 text-gray-400 transition-transform ${
                        activeFaq === index ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      activeFaq === index ? 'max-h-96' : 'max-h-0'
                    }`}
                  >
                    <div className="px-6 pb-5 text-gray-600">{faq.answer}</div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">
              立即开启加速之旅
            </h2>
            <p className="text-xl text-blue-100 mb-8">
              注册即可免费体验，专业技术团队协助您完成接入与配置
            </p>
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
              <button
                onClick={() => navigate('/cdn/login')}
                className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-blue-600 bg-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all"
              >
                免费注册
                <ArrowRight size={20} />
              </button>
              <button
                onClick={() => navigate('/cdn/contact')}
                className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-white border-2 border-white/30 hover:bg-white/10 transition-all"
              >
                联系销售
              </button>
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}