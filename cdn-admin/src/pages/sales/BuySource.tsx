import { useState } from 'react';
import { CheckCircle, Code, Shield, FileText, Headphones, ArrowRight } from 'lucide-react';

const plans = [
  {
    name: '标准授权',
    price: 9999,
    desc: '适合个人开发者或单项目部署',
    features: ['完整前后端源码', '部署文档与视频', '1 年免费更新', '社区技术支持', '单域名商业授权'],
  },
  {
    name: '商业授权',
    price: 29999,
    desc: '适合中小企业与创业团队',
    features: ['完整前后端源码', '部署文档与视频', '终身免费更新', '专属客服支持', '多域名商业授权', '二次开发指导'],
    highlighted: true,
  },
  {
    name: '旗舰授权',
    price: 59999,
    desc: '适合大型企业及 SaaS 平台',
    features: ['完整前后端源码', '白标与定制方案', '终身免费更新', '1 对 1 架构师', '无限制企业授权', '源码定制开发'],
  },
];

export default function BuySource() {
  const [selected, setSelected] = useState(1);
  const plan = plans[selected];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center max-w-2xl mx-auto mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-4">
          <Code size={14} />
          源码授权
        </div>
        <h1 className="text-3xl md:text-4xl font-bold mb-4">购买源码授权</h1>
        <p className="text-[var(--sales-text-secondary)]">一次购买，源码交付，自主可控，终身使用。</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-4">
          {plans.map((p, i) => (
            <div
              key={i}
              onClick={() => setSelected(i)}
              className={`relative p-6 rounded-2xl border cursor-pointer transition-all ${
                selected === i
                  ? 'border-[var(--sales-primary)] bg-[var(--sales-primary)]/5'
                  : 'border-[var(--sales-border)] bg-[var(--sales-card)] hover:border-[var(--sales-primary)]/30'
              }`}
            >
              {p.highlighted && (
                <div className="absolute -top-2 right-6 px-2 py-0.5 rounded-full text-[10px] font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)]">
                  推荐
                </div>
              )}
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex-1">
                  <h3 className="text-lg font-semibold">{p.name}</h3>
                  <p className="text-sm text-[var(--sales-text-secondary)] mt-1">{p.desc}</p>
                </div>
                <div className="text-right">
                  <div className="text-2xl font-bold text-[var(--sales-primary)]">¥{p.price.toLocaleString()}</div>
                  <div className="text-xs text-[var(--sales-text-secondary)]">一次性费用</div>
                </div>
              </div>
              <ul className="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-4">
                {p.features.map((feat, idx) => (
                  <li key={idx} className="flex items-center gap-2 text-sm text-[var(--sales-text-secondary)]">
                    <CheckCircle size={14} className="text-[var(--sales-success)] shrink-0" />
                    {feat}
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        <div className="lg:col-span-1">
          <div className="sticky top-24 p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)]">
            <h3 className="text-lg font-semibold mb-4">订单 summary</h3>
            <div className="flex justify-between text-sm mb-2">
              <span className="text-[var(--sales-text-secondary)]">授权方案</span>
              <span className="font-medium">{plan.name}</span>
            </div>
            <div className="flex justify-between text-sm mb-4">
              <span className="text-[var(--sales-text-secondary)]">授权范围</span>
              <span className="font-medium">{plan.features.find((f) => f.includes('授权'))}</span>
            </div>
            <div className="border-t border-[var(--sales-border)] pt-4 mb-6">
              <div className="flex justify-between items-baseline">
                <span className="text-[var(--sales-text-secondary)]">应付金额</span>
                <span className="text-2xl font-bold text-[var(--sales-primary)]">¥{plan.price.toLocaleString()}</span>
              </div>
            </div>
            <button className="w-full py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 transition-all flex items-center justify-center gap-2">
              立即下单 <ArrowRight size={16} />
            </button>
            <p className="mt-4 text-xs text-[var(--sales-text-secondary)] text-center">
              下单后客服将在 24 小时内与您确认授权协议与交付方式。
            </p>
          </div>
        </div>
      </div>

      <div className="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        {[
          { icon: Shield, title: '源码安全', desc: '通过加密通道交付源码，签署保密协议。' },
          { icon: FileText, title: '完整文档', desc: '提供部署、配置、二次开发完整文档。' },
          { icon: Headphones, title: '售后支持', desc: '专属技术群，问题快速响应与解决。' },
        ].map((item, i) => (
          <div key={i} className="p-5 rounded-xl bg-[var(--sales-card)] border border-[var(--sales-border)]">
            <div className="w-10 h-10 rounded-lg bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] flex items-center justify-center mb-3">
              <item.icon size={20} />
            </div>
            <h4 className="font-semibold mb-1">{item.title}</h4>
            <p className="text-sm text-[var(--sales-text-secondary)]">{item.desc}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
