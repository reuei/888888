import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { luckyNumbers } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus, ToggleLeft, ToggleRight } from 'lucide-react';

export default function LuckyNumbers() {
  const [list, setList] = useState(luckyNumbers);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ number: '', price: 0 });

  const toggleSold = (id: string) => {
    setList(list.map((n) => (n.id === id ? { ...n, sold: !n.sold } : n)));
  };

  const handleAdd = () => {
    if (!form.number.trim()) return;
    setList([
      ...list,
      {
        id: `N${String(list.length + 1).padStart(3, '0')}`,
        number: form.number.trim(),
        price: form.price,
        sold: false,
      },
    ]);
    setForm({ number: '', price: 0 });
    setModalOpen(false);
  };

  return (
    <div>
      <PageHeader
        title="自助选号"
        breadcrumb={['会员/用户管理', '自助选号']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加靓号
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>编号ID</th>
              <th>靓号</th>
              <th>价格</th>
              <th>售出状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((n) => (
              <tr key={n.id}>
                <td className="text-text-secondary">{n.id}</td>
                <td className="font-medium">{n.number}</td>
                <td>¥{formatMoney(n.price)}</td>
                <td>
                  <span className={`badge ${n.sold ? 'badge-danger' : 'badge-success'}`}>
                    {n.sold ? '已售出' : '未售出'}
                  </span>
                </td>
                <td>
                  <button
                    onClick={() => toggleSold(n.id)}
                    className={`p-1.5 rounded hover:bg-gray-100 ${n.sold ? 'text-success' : 'text-warning'}`}
                    title={n.sold ? '标记未售出' : '标记已售出'}
                  >
                    {n.sold ? <ToggleRight size={16} /> : <ToggleLeft size={16} />}
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={modalOpen}
        title="添加靓号"
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
            <label className="block text-sm mb-1">靓号</label>
            <input
              value={form.number}
              onChange={(e) => setForm({ ...form, number: e.target.value })}
              className="input"
              placeholder="例如 888888"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">价格（元）</label>
            <input
              type="number"
              value={form.price}
              onChange={(e) => setForm({ ...form, price: parseFloat(e.target.value) || 0 })}
              className="input"
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
