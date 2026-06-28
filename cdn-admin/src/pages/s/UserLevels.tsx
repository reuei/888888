import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { userLevels } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus } from 'lucide-react';

function formatDiscount(d: number) {
  if (d === 1) return '无折扣';
  const v = Math.round(d * 100);
  if (v % 10 === 0) return `${v / 10}折`;
  return `${v}折`;
}

export default function UserLevels() {
  const [list, setList] = useState(userLevels);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', minAmount: 0, discount: 1 });

  const handleAdd = () => {
    if (!form.name.trim()) return;
    setList([
      ...list,
      {
        id: `L${list.length + 1}`,
        name: form.name.trim(),
        minAmount: form.minAmount,
        discount: form.discount,
      },
    ]);
    setForm({ name: '', minAmount: 0, discount: 1 });
    setModalOpen(false);
  };

  return (
    <div>
      <PageHeader
        title="用户等级"
        breadcrumb={['会员/用户管理', '用户等级']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加等级
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>等级ID</th>
              <th>等级名称</th>
              <th>最低消费金额</th>
              <th>折扣</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((l) => (
              <tr key={l.id}>
                <td className="text-text-secondary">{l.id}</td>
                <td className="font-medium">{l.name}</td>
                <td>¥{formatMoney(l.minAmount)}</td>
                <td>{formatDiscount(l.discount)}</td>
                <td>
                  <button className="text-sm text-primary hover:underline">编辑</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={modalOpen}
        title="添加等级"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">确认</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">等级名称</label>
            <input
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              className="input"
              placeholder="例如 VIP1"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">最低消费金额（元）</label>
            <input
              type="number"
              value={form.minAmount}
              onChange={(e) => setForm({ ...form, minAmount: parseFloat(e.target.value) || 0 })}
              className="input"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">折扣（0-1，1表示无折扣）</label>
            <input
              type="number"
              step="0.01"
              min="0"
              max="1"
              value={form.discount}
              onChange={(e) => setForm({ ...form, discount: parseFloat(e.target.value) || 0 })}
              className="input"
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
