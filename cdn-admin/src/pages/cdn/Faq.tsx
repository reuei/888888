import { useState } from 'react';
import { ChevronDown, HelpCircle, ExternalLink } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const siteFaqs = [
  {
    question: '网站如何接入CDN？',
    answer: '接入6cdn非常简单，只需完成以下步骤：1. 注册账号并登录控制台；2. 添加您的域名；3. 修改DNS解析指向我们的CNAME；4. 等待解析生效即可开始享受加速服务。详细步骤请查看接入文档。',
  },
  {
    question: '如何开启WebSocket？',
    answer: '在控制台站点配置中，找到WebSocket设置选项，开启WebSocket支持即可。开启后，6cdn会自动处理WebSocket协议的转发和加速，无需额外配置。',
  },
  {
    question: '如何设置CC防护，开启最优防御？',
    answer: '在控制台的防护设置中，您可以配置CC防护规则。建议开启智能防护模式，并根据实际情况调整请求速率限制。同时，可以结合JS浏览器识别和验证码验证，提升防护效果。',
  },
  {
    question: '如何设置自定义规则？',
    answer: '在控制台的规则设置中，您可以添加自定义规则，支持URL重写、访问控制、缓存规则等。规则设置灵活，支持多种匹配条件和动作，满足不同场景的需求。',
  },
];

const nodeFaqs = [
  {
    question: '亚太-Lite节点介绍',
    answer: '亚太-Lite节点是我们的基础节点，覆盖亚洲主要地区，提供稳定的加速服务。适合中小型网站使用，性价比高，能够满足大部分用户的需求。',
  },
  {
    question: '亚太-Pro节点介绍',
    answer: '亚太-Pro节点是我们的高级节点，采用CN2线路，延迟更低，速度更快。适合对网络质量要求较高的用户，如游戏、视频等对延迟敏感的业务。',
  },
  {
    question: '国内高防型节点介绍',
    answer: '国内高防型节点位于国内高防机房，提供强大的DDoS防护能力，最高支持T级防护。适合对安全性要求较高的企业用户，域名需完成工信部备案。',
  },
  {
    question: '美国高防型节点介绍',
    answer: '美国高防型节点位于美国高防机房，提供优质的国际带宽和防护能力。适合面向全球用户的网站，无需备案，直接使用。',
  },
];

const packageFaqs = [
  {
    question: '套餐内流量用完可以加吗？什么价格？',
    answer: '可以的，套餐内流量用完后，超出部分按0.15元/GB计费。您也可以购买流量包，价格更优惠。具体流量包价格请查看价格页面。',
  },
  {
    question: '套餐内主域名数用完可以加吗？什么价格？',
    answer: '可以的，您可以在控制台购买额外的域名配额，价格为5元/个/月。购买后立即生效，无需等待。',
  },
  {
    question: '为什么亚太、美国海外套餐没有转发数？',
    answer: '海外套餐采用不同的计费模式，主要按流量计费，不限制转发次数。这样的计费方式更适合海外业务的使用特点，更加灵活和经济。',
  },
  {
    question: '套餐可以自主升级或切换吗？',
    answer: '可以的，您可以在控制台自主升级或切换套餐。升级套餐立即生效，差价按天计算；降级套餐次月生效。',
  },
  {
    question: '海外服务可以用国内套餐吗？',
    answer: '不可以，国内套餐仅支持国内节点，海外服务需要使用海外套餐。您可以根据实际需求选择合适的套餐类型。',
  },
];

export default function Faq() {
  const [activeSiteFaq, setActiveSiteFaq] = useState<number | null>(0);
  const [activeNodeFaq, setActiveNodeFaq] = useState<number | null>(null);
  const [activePackageFaq, setActivePackageFaq] = useState<number | null>(null);

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">FAQ</h1>
            <p className="text-xl text-blue-100">常见问题</p>
          </div>
        </section>

        <section className="py-16 bg-white">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-xl font-bold text-gray-800 mb-6">网站相关</h2>
            <p className="text-gray-600 mb-6">更多网站相关问题，请查阅 <a href="/cdn/docs" className="text-blue-600 hover:underline flex items-center gap-1 inline-flex">文档中心 <ExternalLink size={14} /></a></p>

            <div className="space-y-4">
              {siteFaqs.map((faq, index) => (
                <div
                  key={index}
                  className="rounded-xl bg-gray-50 border border-gray-100 overflow-hidden"
                >
                  <button
                    onClick={() => setActiveSiteFaq(activeSiteFaq === index ? null : index)}
                    className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-100 transition-colors"
                  >
                    <span className="font-semibold text-gray-800">{faq.question}</span>
                    <ChevronDown
                      className={`w-5 h-5 text-gray-400 transition-transform ${
                        activeSiteFaq === index ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      activeSiteFaq === index ? 'max-h-96' : 'max-h-0'
                    }`}
                  >
                    <div className="px-6 pb-5 pt-2">
                      <div className="flex items-start gap-3">
                        <HelpCircle size={16} className="text-blue-500 mt-0.5 flex-shrink-0" />
                        <p className="text-gray-600 leading-relaxed">{faq.answer}</p>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-16 bg-gray-50">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-xl font-bold text-gray-800 mb-6">节点相关</h2>

            <div className="space-y-4">
              {nodeFaqs.map((faq, index) => (
                <div
                  key={index}
                  className="rounded-xl bg-white border border-gray-100 overflow-hidden"
                >
                  <button
                    onClick={() => setActiveNodeFaq(activeNodeFaq === index ? null : index)}
                    className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-50 transition-colors"
                  >
                    <span className="font-semibold text-gray-800">{faq.question}</span>
                    <ChevronDown
                      className={`w-5 h-5 text-gray-400 transition-transform ${
                        activeNodeFaq === index ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      activeNodeFaq === index ? 'max-h-96' : 'max-h-0'
                    }`}
                  >
                    <div className="px-6 pb-5 pt-2">
                      <div className="flex items-start gap-3">
                        <HelpCircle size={16} className="text-blue-500 mt-0.5 flex-shrink-0" />
                        <p className="text-gray-600 leading-relaxed">{faq.answer}</p>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-16 bg-white">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-xl font-bold text-gray-800 mb-6">套餐相关</h2>

            <div className="space-y-4">
              {packageFaqs.map((faq, index) => (
                <div
                  key={index}
                  className="rounded-xl bg-gray-50 border border-gray-100 overflow-hidden"
                >
                  <button
                    onClick={() => setActivePackageFaq(activePackageFaq === index ? null : index)}
                    className="w-full flex items-center justify-between px-6 py-5 text-left hover:bg-gray-100 transition-colors"
                  >
                    <span className="font-semibold text-gray-800">{faq.question}</span>
                    <ChevronDown
                      className={`w-5 h-5 text-gray-400 transition-transform ${
                        activePackageFaq === index ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  <div
                    className={`overflow-hidden transition-all duration-300 ${
                      activePackageFaq === index ? 'max-h-96' : 'max-h-0'
                    }`}
                  >
                    <div className="px-6 pb-5 pt-2">
                      <div className="flex items-start gap-3">
                        <HelpCircle size={16} className="text-blue-500 mt-0.5 flex-shrink-0" />
                        <p className="text-gray-600 leading-relaxed">{faq.answer}</p>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}