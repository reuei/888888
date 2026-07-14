import { useState } from 'react';
import { ArrowRight, ChevronDown, Info, FileText, RefreshCw, Phone } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const rules = [
  {
    id: 'activity',
    title: '活动说明',
    content: '1. 活动期间，新用户注册并完成实名认证，即可获得100GB免费流量。\n2. 免费流量有效期为30天，过期自动清零。\n3. 每个账号仅可参与一次活动。\n4. 活动最终解释权归6cdn所有。',
  },
  {
    id: 'content',
    title: '内容说明',
    content: '1. 免费流量可用于CDN加速服务。\n2. 超出免费流量部分按正常价格计费。\n3. 免费流量不可转让、不可兑换现金。\n4. 如发现违规使用，6cdn有权收回免费流量。',
  },
  {
    id: 'refund',
    title: '退款说明',
    content: '1. 活动期间购买的套餐支持7天无理由退款。\n2. 退款时将扣除已使用天数的费用。\n3. 免费流量部分不予退款。\n4. 退款申请提交后，将在3-5个工作日内处理。',
  },
  {
    id: 'contact',
    title: '联系方式',
    content: '1. 客服热线：400-888-6cdn\n2. 邮箱：contact@6cdn.com\n3. 工作时间：周一至周五 9:00-18:00\n4. 技术支持：7×24小时在线',
  },
];

export default function Activity() {
  const [activeRule, setActiveRule] = useState('activity');

  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">6cdn YULUCDN</h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto mb-8">
              * 活动时间
            </p>
            <button className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-blue-600 bg-white shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all">
              立即参与
              <ArrowRight size={20} />
            </button>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-2xl font-bold text-gray-800 mb-8 text-center">活动规则</h2>

            <div className="flex flex-wrap justify-center gap-4 mb-8">
              {rules.map((rule) => (
                <button
                  key={rule.id}
                  onClick={() => setActiveRule(rule.id)}
                  className={`flex items-center gap-2 px-6 py-3 rounded-lg font-medium transition-all ${
                    activeRule === rule.id
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  }`}
                >
                  {rule.id === 'activity' && <Info size={18} />}
                  {rule.id === 'content' && <FileText size={18} />}
                  {rule.id === 'refund' && <RefreshCw size={18} />}
                  {rule.id === 'contact' && <Phone size={18} />}
                  {rule.title}
                </button>
              ))}
            </div>

            <div className="bg-gray-50 rounded-xl p-8">
              <div className="flex items-center justify-between mb-6">
                <h3 className="text-xl font-bold text-gray-800">
                  {rules.find((r) => r.id === activeRule)?.title}
                </h3>
                <button
                  onClick={() => setActiveRule('')}
                  className="text-gray-400 hover:text-gray-600"
                >
                  <ChevronDown size={20} />
                </button>
              </div>
              <div className="text-gray-600 whitespace-pre-line leading-relaxed">
                {rules.find((r) => r.id === activeRule)?.content}
              </div>
            </div>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}