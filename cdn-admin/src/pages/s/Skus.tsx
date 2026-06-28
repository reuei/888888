import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { skus } from '../../data/mock';
import { Edit, Trash2, Plus } from 'lucide-react';

export default function SSkus() {
  const [list, setList] = useState(skus);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', bandwidth: '', flow: '', domains: 1, ccLevel: '基础', price: 0 });

  const handleAdd = () => {
    setList([
      {
        id: `S00${list.length + 1}`,
        name: form.name,
        bandwidth: form.bandwidth,
        flow: form.flow,
        domains: form.domains,
        ccLevel: form.ccLevel,
        price: form.price,
      },
      ...list,
    ]);
    setModalOpen(false);
    setForm({ name: '', bandwidth: '', flow: '', domains: 1, ccLevel: '基础', price: 0 });
  };

  return (
    <div>
      <PageHeader
        title="套餐规格管理"
        breadcrumb={['商品管理', '套餐规格管理']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新增规格
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>规格名称</th>
              <th>带宽</th>
              <th>流量</th>
              <th>域名数</th>
              <th>CC 防护等级</th>
              <th>价格</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((s) => (
              <tr key={s.id}>
                <td className="font-medium">{s.name}</td>
                <td>{s.bandwidth}</td>
                <td>{s.flow}</td>
                <td>{s.domains}</td>
                <td>{s.ccLevel}</td>
                <td>¥{s.price.toFixed(2)}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={modalOpen}
        title="新增套餐规格"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">保存</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">规格名称</label>
            <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="input" />
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">带宽</label>
              <input value={form.bandwidth} onChange={(e) => setForm({ ...form, bandwidth: e.target.value })} className="input" placeholder="例如 50Mbps" />
            </div>
            <div>
              <label className="block text-sm mb-1">流量</label>
              <input value={form.flow} onChange={(e) => setForm({ ...form, flow: e.target.value })} className="input" placeholder="例如 500GB/月" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">域名数</label>
              <input type="number" value={form.domains} onChange={(e) => setForm({ ...form, domains: parseInt(e.target.value) || 0 })} className="input" />
            </div>
            <div>
              <label className="block text-sm mb-1">CC 防护等级</label>
              <select value={form.ccLevel} onChange={(e) => setForm({ ...form, ccLevel: e.target.value })} className="input">
                <option>基础</option>
                <option>标准</option>
                <option>高级</option>
                <option>企业</option>
              </select>
            </div>
          </div>
          <div>
            <label className="block text-sm mb-1">价格</label>
            <input type="number" value={form.price} onChange={(e) => setForm({ ...form, price: parseFloat(e.target.value) || 0 })} className="input" />
          </div>
        </div>
      </Modal>
    </div>
  );
}
