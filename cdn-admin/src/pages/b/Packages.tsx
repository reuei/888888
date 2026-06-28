import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { packages } from '../../data/mock';
import { ShoppingCart, Check } from 'lucide-react';

export default function BPackages() {
  const [activeTab, setActiveTab] = useState<'buy' | 'my' | 'renew'>('buy');
  const [buyOpen, setBuyOpen] = useState(false);
  const [selected, setSelected] = useState<typeof packages[0] | null>(null);

  const openBuy = (p: typeof packages[0]) => {
    setSelected(p);
    setBuyOpen(true);
  };

  return (
    <div>
      <PageHeader title="套餐管理" breadcrumb={['套餐管理', activeTab === 'buy' ? '在线订购套餐' : activeTab === 'my' ? '我的套餐' : '套餐续费']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'buy', label: '在线订购套餐' },
          { key: 'my', label: '我的套餐' },
          { key: 'renew', label: '套餐续费' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => setActiveTab(t.key as any)}
            className={`btn text-xs ${activeTab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {activeTab === 'buy' && (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {packages.map((p) => (
            <div key={p.id} className="card p-5 flex flex-col">
              <h3 className="text-lg font-bold">{p.name}</h3>
              <div className="text-3xl font-bold text-primary my-3">
                ¥{p.price.toFixed(2)}<span className="text-sm text-text-secondary font-normal">/{p.period}</span>
              </div>
              <ul className="space-y-2 text-sm text-text-secondary flex-1 mb-4">
                <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 流量 {p.flow}</li>
                <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 带宽 {p.bandwidth}</li>
                <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 域名数 {p.domains} 个</li>
                <li className="flex items-center gap-2"><Check size={14} className="text-success" /> CC 基础防护</li>
              </ul>
              <button onClick={() => openBuy(p)} className="btn btn-primary flex items-center justify-center gap-1">
                <ShoppingCart size={16} /> 立即购买
              </button>
            </div>
          ))}
        </div>
      )}

      {activeTab === 'my' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th>套餐名称</th>
                <th>流量 / 带宽</th>
                <th>域名数</th>
                <th>到期时间</th>
                <th>状态</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              {packages.slice(1, 3).map((p) => (
                <tr key={p.id}>
                  <td className="font-medium">{p.name}</td>
                  <td>{p.flow} / {p.bandwidth}</td>
                  <td>{p.domains}</td>
                  <td>2026-12-31</td>
                  <td><span className="badge badge-success">生效中</span></td>
                  <td><button className="btn btn-default text-xs">续费</button></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {activeTab === 'renew' && (
        <div className="card p-5">
          <div className="space-y-4 max-w-lg">
            <div>
              <label className="block text-sm mb-1">选择套餐</label>
              <select className="input">
                <option>标准版 - 到期 2026-12-31</option>
                <option>专业版 - 到期 2027-01-15</option>
              </select>
            </div>
            <div>
              <label className="block text-sm mb-1">续费周期</label>
              <select className="input">
                <option>1 个月</option>
                <option>3 个月</option>
                <option>6 个月</option>
                <option>12 个月</option>
              </select>
            </div>
            <div className="text-lg font-bold text-primary">应付：¥49.00</div>
            <button className="btn btn-primary">立即支付</button>
          </div>
        </div>
      )}

      <Modal
        open={buyOpen}
        title="确认订单"
        onClose={() => setBuyOpen(false)}
        footer={
          <>
            <button onClick={() => setBuyOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => setBuyOpen(false)} className="btn btn-primary">立即支付</button>
          </>
        }
      >
        {selected && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between"><span className="text-text-secondary">套餐</span><span>{selected.name}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">流量</span><span>{selected.flow}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">带宽</span><span>{selected.bandwidth}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">周期</span><span>1{selected.period}</span></div>
            <div className="flex justify-between text-lg font-bold pt-2 border-t border-border">
              <span>应付金额</span>
              <span className="text-primary">¥{selected.price.toFixed(2)}</span>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
