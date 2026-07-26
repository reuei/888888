import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { feeGroups } from '../../data/mock';
import { Plus, Edit } from 'lucide-react';

export default function SFeeGroups() {
  const { show } = useToast();
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState({ id: '', name: '', rate: '0.02', description: '' });

  const openCreate = () => {
    setForm({ id: '', name: '', rate: '0.02', description: '' });
    setOpen(true);
  };

  const openEdit = (id: string) => {
    const g = feeGroups.find((f) => f.id === id);
    if (!g) return;
    setForm({ id: g.id, name: g.name, rate: String(g.rate), description: g.description });
    setOpen(true);
  };

  const handleSave = () => {
    if (!form.name.trim()) {
      show('请输入分组名称', 'warning');
      return;
    }
    const rate = parseFloat(form.rate);
    if (isNaN(rate) || rate < 0 || rate > 1) {
      show('费率应在 0~1 之间', 'warning');
      return;
    }
    setOpen(false);
    show(`费率组「${form.name}」已${form.id ? '更新' : '创建'}`, 'success');
  };

  return (
    <div>
      <PageHeader
        title="费率分组管理"
        breadcrumb={['财务管理', '费率分组管理']}
        actions={
          <button onClick={openCreate} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新建费率组
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>分组名</th>
              <th>费率(%)</th>
              <th>商户数</th>
              <th>描述</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {feeGroups.map((g) => (
              <tr key={g.id}>
                <td className="font-medium">{g.name}</td>
                <td>
                  <span className="text-primary font-semibold">{(g.rate * 100).toFixed(1)}%</span>
                </td>
                <td>{g.merchantCount}</td>
                <td className="text-text-secondary">{g.description}</td>
                <td>
                  <button
                    onClick={() => openEdit(g.id)}
                    className="p-1.5 rounded hover:bg-gray-100 text-primary"
                    title="编辑"
                  >
                    <Edit size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {open && (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50" onClick={() => setOpen(false)}>
          <div className="card p-6 w-full max-w-md" onClick={(e) => e.stopPropagation()}>
            <h3 className="font-semibold mb-4">{form.id ? '编辑费率组' : '新建费率组'}</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm mb-1">分组名称</label>
                <input
                  className="input"
                  value={form.name}
                  onChange={(e) => setForm({ ...form, name: e.target.value })}
                  placeholder="例如：标准费率"
                />
              </div>
              <div>
                <label className="block text-sm mb-1">费率（小数，例如 0.02 = 2%）</label>
                <input
                  className="input"
                  type="number"
                  step="0.001"
                  min="0"
                  max="1"
                  value={form.rate}
                  onChange={(e) => setForm({ ...form, rate: e.target.value })}
                />
              </div>
              <div>
                <label className="block text-sm mb-1">描述</label>
                <textarea
                  className="input"
                  rows={3}
                  value={form.description}
                  onChange={(e) => setForm({ ...form, description: e.target.value })}
                  placeholder="选填"
                />
              </div>
            </div>
            <div className="flex justify-end gap-2 mt-5">
              <button onClick={() => setOpen(false)} className="btn btn-default">
                取消
              </button>
              <button onClick={handleSave} className="btn btn-primary">
                保存
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
