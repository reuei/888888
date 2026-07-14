import { CheckCircle, ArrowRight, Building2, Users, TrendingUp, Award, Wallet, Headphones, FileText } from 'lucide-react';
import CdnHeader from '../../components/cdn/CdnHeader';
import CdnFooter from '../../components/cdn/CdnFooter';

const benefits = [
  { icon: TrendingUp, title: '高额返佣', desc: '最高可达30%的返佣比例，收益丰厚' },
  { icon: Users, title: '独立后台', desc: '专属代理后台，实时查看业绩和佣金' },
  { icon: Building2, title: '品牌支持', desc: '提供专业的品牌宣传材料和技术支持' },
  { icon: Award, title: '等级制度', desc: '完善的代理等级制度，升级享更多权益' },
  { icon: Wallet, title: '灵活结算', desc: '支持周结、月结多种结算方式' },
  { icon: Headphones, title: '专属客服', desc: '一对一专属客服，及时解答疑问' },
  { icon: FileText, title: '营销支持', desc: '提供宣传素材、案例分享等营销资源' },
];

const requirements = [
  '具有合法的营业执照或个体工商户资质',
  '有一定的客户资源或销售渠道',
  '认同6cdn的发展理念和服务宗旨',
  '能够积极推广6cdn的产品和服务',
];

const levels = [
  { level: '初级代理', commission: '15%', requirement: '首次充值1000元', benefits: ['基础返佣', '技术支持', '营销材料'] },
  { level: '中级代理', commission: '22%', requirement: '累计充值5000元', benefits: ['高级返佣', '专属客服', '定制方案'] },
  { level: '高级代理', commission: '30%', requirement: '累计充值20000元', benefits: ['顶级返佣', '专属经理', '优先服务'] },
];

export default function Agent() {
  return (
    <div className="min-h-screen bg-gray-50">
      <CdnHeader />

      <main className="pt-24 pb-16">
        <section className="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 className="text-3xl md:text-5xl font-bold text-white mb-6">🔥 合作代理</h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto">
              加入6cdn代理计划，共享CDN市场红利，实现互利共赢
            </p>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">代理优势</h2>
              <p className="text-gray-600">成为6cdn代理，享受全方位的支持与服务</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {benefits.map((benefit, index) => (
                <div
                  key={index}
                  className="text-center p-8 rounded-xl bg-gray-50 hover:bg-blue-50 transition-colors"
                >
                  <div className="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center mx-auto mb-6">
                    <benefit.icon size={32} />
                  </div>
                  <h3 className="text-xl font-bold text-gray-800 mb-3">{benefit.title}</h3>
                  <p className="text-gray-600">{benefit.desc}</p>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">代理等级</h2>
              <p className="text-gray-600">不同等级享受不同的返佣比例和权益</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {levels.map((level, index) => (
                <div
                  key={index}
                  className={`p-8 rounded-2xl border-2 text-center ${
                    index === 1
                      ? 'border-blue-500 bg-blue-50'
                      : 'border-gray-200 bg-white'
                  }`}
                >
                  {index === 1 && (
                    <span className="inline-block px-3 py-1 rounded-full text-xs font-medium bg-blue-600 text-white mb-4">
                      推荐
                    </span>
                  )}
                  <h3 className="text-2xl font-bold text-gray-800 mb-2">{level.level}</h3>
                  <div className="text-4xl font-extrabold text-blue-600 mb-4">{level.commission}</div>
                  <p className="text-gray-500 text-sm mb-6">{level.requirement}</p>
                  <ul className="space-y-3 mb-6">
                    {level.benefits.map((benefit, idx) => (
                      <li key={idx} className="flex items-center justify-center gap-2 text-gray-600">
                        <CheckCircle size={16} className="text-green-500" />
                        {benefit}
                      </li>
                    ))}
                  </ul>
                  <button className={`w-full py-3 rounded-lg font-semibold ${
                    index === 1
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  }`}>
                    立即申请
                  </button>
                </div>
              ))}
            </div>
          </div>
        </section>

        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center mb-16">
              <h2 className="text-3xl font-bold text-gray-800 mb-4">代理要求</h2>
              <p className="text-gray-600">成为6cdn代理需要满足以下条件</p>
            </div>

            <div className="max-w-2xl mx-auto">
              <ul className="space-y-4">
                {requirements.map((req, index) => (
                  <li key={index} className="flex items-start gap-4 p-4 rounded-xl bg-gray-50">
                    <div className="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0">
                      {index + 1}
                    </div>
                    <span className="text-gray-700">{req}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>
        </section>

        <section className="py-20 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800">
          <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 className="text-3xl font-bold text-white mb-6">立即加入代理计划</h2>
            <p className="text-xl text-blue-100 mb-8">
              填写申请表单，我们的代理经理将在24小时内与您联系
            </p>
            <button className="inline-flex items-center gap-2 px-8 py-4 rounded-lg text-lg font-semibold text-blue-600 bg-white shadow-xl hover:shadow-2xl transition-all">
              申请成为代理
              <ArrowRight size={20} />
            </button>
          </div>
        </section>
      </main>

      <CdnFooter />
    </div>
  );
}