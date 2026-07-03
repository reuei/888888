import { useState } from 'react';
import { Server, Globe, Zap, ArrowRight, MapPin } from 'lucide-react';

const nodePlans = [
  { name: '基础节点包', bandwidth: '10Mbps', flow: '500GB/月', regions: ['华东', '华南'], price: 299, unit: '月' },
  { name: '标准节点包', bandwidth: '50Mbps', flow: '2TB/月', regions: ['华东', '华南', '华北', '西南'], price: 999, unit: '月', highlighted: true },
  { name: '高级节点包', bandwidth: '100Mbps', flow: '5TB/月', regions: ['全国八线', '海外香港'], price: 2499, unit: '月' },
  { name: '旗舰节点包', bandwidth: '200Mbps', flow: '20TB/月', regions: ['全国八线', '海外香港', '海外新加坡'], price: 6999, unit: '月' },
];

export default function BuyNodes() {
  const [selected, setSelected] = useState(1);
  const plan = nodePlans[selected];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center max-w-2xl mx-auto mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-4">
          <Server size={14} />
          CDN 节点
        </div>
        <h1 className="text-3xl md:text-4xl font-bold mb-4">购买 CDN 节点</h1>
        <p className="text-[var(--sales-text-secondary)]">优质节点资源，灵活带宽与流量，助力业务全球加速。</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        {nodePlans.map((p, i) => (
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
              <div className="absolute -top-2 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-full text-[10px] font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)]">
                推荐
              </div>
            )}
            <div className="w-10 h-10 rounded-xl bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] flex items-center justify-center mb-4">
              <Server size={20} />
            </div>
            <h3 className="text-lg font-semibold mb-1">{p.name}</h3>
            <div className="flex items-baseline gap-1 mb-4">
              <span className="text-3xl font-bold text-[var(--sales-primary)]">¥{p.price}</span>
              <span className="text-sm text-[var(--sales-text-secondary)]">/{p.unit}</span>
            </div>
            <ul className="space-y-2 text-sm text-[var(--sales-text-secondary)]">
              <li className="flex items-center gap-2"><Zap size={14} /> 带宽 {p.bandwidth}</li>
              <li className="flex items-center gap-2"><Globe size={14} /> 流量 {p.flow}</li>
              <li className="flex items-center gap-2"><MapPin size={14} /> {p.regions.join('、')}</li>
            </ul>
          </div>
        ))}
      </div>

      <div className="max-w-3xl mx-auto p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)]">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
          <div>
            <h3 className="text-lg font-semibold">{plan.name}</h3>
            <p className="text-sm text-[var(--sales-text-secondary)]">{plan.regions.join('、')} · {plan.bandwidth} · {plan.flow}</p>
          </div>
          <div className="text-2xl font-bold text-[var(--sales-primary)]">¥{plan.price}/{plan.unit}</div>
        </div>
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
          {[
            { title: '智能调度', desc: '自动选择最优节点' },
            { title: 'DDoS 清洗', desc: '基础防护能力' },
            { title: '实时监控', desc: '流量与带宽可视化' },
          ].map((item, i) => (
            <div key={i} className="p-4 rounded-xl bg-[var(--sales-bg)] border border-[var(--sales-border)]">
              <div className="text-sm font-medium mb-1">{item.title}</div>
              <div className="text-xs text-[var(--sales-text-secondary)]">{item.desc}</div>
            </div>
          ))}
        </div>
        <button className="w-full py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 transition-all flex items-center justify-center gap-2">
          立即购买 <ArrowRight size={16} />
        </button>
      </div>
    </div>
  );
}
