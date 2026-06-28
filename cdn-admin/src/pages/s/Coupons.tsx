import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { coupons } from '../../data/mock';
import { Plus } from 'lucide-react';

export default function SCoupons() {
  const [tab, setTab] = useState<'generate' | 'records' | 'stats'>('generate');
  const [list, setList] = useState(coupons);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ batch: '', type: 'fixed', value: 0, threshold: 0, total: 100, limitPerUser: 1 });

  const handleAdd = () => {
    setList([
      {
        id: `CO00${list.length + 1}`,
        batch: form.batch,
        type: form.type as 'fixed' | 'percent',
        value: form.value,
        threshold: form.threshold,
        total: form.total,
        received: 0,
        status: 'active',
      },
      ...list,
    ]);
    setModalOpen(false);
    setForm({ batch: '', type: 'fixed', value: 0, threshold: 0, total: 100, limitPerUser: 1 });
  };

  return (
    <div>
      <PageHeader
        title="优惠券生成"
        breadcrumb={['优惠券/营销管理', '优惠券生成']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 生成优惠券
          </button>
        }
      />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'generate', label: '优惠券生成' },
          { key: 'records', label: '发放/核销记录' },
          { key: 'stats', label: '营销效果统计' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => setTab(t.key as any)}
            className={`btn text-xs ${tab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab === 'generate' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th>批次号</th>
                <th>类型</th>
                <th>面额 / 折扣</th>
                <th>使用门槛</th>
                <th>总量</th>
                <th>已领取</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              {list.map((c) => (
                <tr key={c.id}>
                  <td className="font-medium">{c.batch}</td>
                  <td>{c.type === 'fixed' ? '固定金额' : '百分比折扣'}</td>
                  <td>{c.type === 'fixed' ? `¥${c.value}` : `${c.value}%`}</td>
                  <td>满 ¥{c.threshold}</td>
                  <td>{c.total}</td>
                  <td>{c.received}</td>
                  <td>
                    <span className={`badge ${c.status === 'active' ? 'badge-success' : 'badge-default'}`}>
                      {c.status === 'active' ? '进行中' : '已过期'}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === 'records' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th>优惠券码</th>
                <th>用户</th>
                <th>关联订单</th>
                <th>使用时间</th>
              </tr>
            </thead>
            <tbody>
              {[
                { code: 'BATCH0618-001', user: 'user_9527', order: 'O202606280001', time: '2026-06-28 10:25' },
                { code: 'BATCH0618-002', user: 'user_3344', order: 'O202606280002', time: '2026-06-28 09:46' },
              ].map((r, i) => (
                <tr key={i}>
                  <td className="font-medium">{r.code}</td>
                  <td>{r.user}</td>
                  <td>{r.order}</td>
                  <td className="text-text-secondary">{r.time}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {tab === 'stats' && (
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {[
            { label: '领取率', value: '85.6%', sub: '856 / 1000' },
            { label: '使用率', value: '62.3%', sub: '533 / 856' },
            { label: '带动交易额', value: '¥128,450.00', sub: '较上期 +12%' },
          ].map((s, i) => (
            <div key={i} className="card p-5 text-center">
              <div className="text-sm text-text-secondary mb-2">{s.label}</div>
              <div className="text-2xl font-bold text-primary">{s.value}</div>
              <div className="text-xs text-text-secondary mt-1">{s.sub}</div>
            </div>
          ))}
        </div>
      )}

      <Modal
        open={modalOpen}
        title="生成优惠券"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">生成</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">批次号</label>
            <input value={form.batch} onChange={(e) => setForm({ ...form, batch: e.target.value })} className="input" placeholder="例如 BATCH0628" />
          </div>
          <div>
            <label className="block text-sm mb-1">优惠券类型</label>
            <select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })} className="input">
              <option value="fixed">固定面额</option>
              <option value="percent">百分比折扣</option>
            </select>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">{form.type === 'fixed' ? '面额' : '折扣'}（{form.type === 'fixed' ? '元' : '%'}）</label>
              <input type="number" value={form.value} onChange={(e) => setForm({ ...form, value: parseFloat(e.target.value) || 0 })} className="input" />
            </div>
            <div>
              <label className="block text-sm mb-1">使用门槛（元）</label>
              <input type="number" value={form.threshold} onChange={(e) => setForm({ ...form, threshold: parseFloat(e.target.value) || 0 })} className="input" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">总量</label>
              <input type="number" value={form.total} onChange={(e) => setForm({ ...form, total: parseInt(e.target.value) || 0 })} className="input" />
            </div>
            <div>
              <label className="block text-sm mb-1">每人限领</label>
              <input type="number" value={form.limitPerUser} onChange={(e) => setForm({ ...form, limitPerUser: parseInt(e.target.value) || 0 })} className="input" />
            </div>
          </div>
        </div>
      </Modal>
    </div>
  );
}
