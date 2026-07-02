import { useCallback, useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../hooks/useToast';
import { fetchAgentProducts, createAgentProduct, updateAgentProduct } from '../../services/api';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { Plus, Edit, Trash2 } from 'lucide-react';
import type { AgentProduct } from '../../types';

export default function AgentDock() {
  const { show } = useToast();
  const [list, setList] = useState<AgentProduct[]>([]);
  const [loading, setLoading] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', source: '', costPrice: '', retailPrice: '' });

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchAgentProducts();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const setStatus = async (id: string, status: AgentProduct['status']) => {
    if (loading) return;
    await updateAgentProduct(id, { status });
    await load();
    show(`商品已${status === 'on' ? '上架' : status === 'off' ? '下架' : '设为审核中'}`, 'success');
  };

  const handleAdd = async () => {
    if (loading) return;
    const costPrice = parseFloat(form.costPrice);
    const retailPrice = parseFloat(form.retailPrice);
    if (!form.name || !form.source || Number.isNaN(costPrice) || Number.isNaN(retailPrice)) return;
    await createAgentProduct({ name: form.name, source: form.source, costPrice, retailPrice, status: 'pending' });
    await load();
    show('商品对接成功', 'success');
    setModalOpen(false);
    setForm({ name: '', source: '', costPrice: '', retailPrice: '' });
  };

  return (
    <div>
      <PageHeader
        title="代理商品对接"
        breadcrumb={['代理/分销管理', '代理商品对接']}
        actions={
          <button onClick={() => setModalOpen(true)} disabled={loading} className="btn btn-primary flex items-center gap-1 disabled:opacity-50">
            <Plus size={16} /> 对接新商品
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>商品ID</th>
              <th>商品名称</th>
              <th>来源商户</th>
              <th>成本价</th>
              <th>零售价</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {loading && (
              <tr>
                <td colSpan={7}>
                  <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>
                </td>
              </tr>
            )}
            {!loading && list.map((p) => (
              <tr key={p.id}>
                <td className="text-text-secondary">{p.id}</td>
                <td className="font-medium">{p.name}</td>
                <td>{p.source}</td>
                <td>¥{formatMoney(p.costPrice)}</td>
                <td>¥{formatMoney(p.retailPrice)}</td>
                <td>
                  <span className={`badge ${statusBadge(p.status)}`}>{statusText(p.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <select
                      value={p.status}
                      onChange={(e) => setStatus(p.id, e.target.value as AgentProduct['status'])}
                      disabled={loading}
                      className="input py-1 px-2 w-28 text-xs disabled:opacity-50"
                    >
                      <option value="on">上架</option>
                      <option value="off">下架</option>
                      <option value="pending">审核中</option>
                    </select>
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
        title="对接新商品"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} disabled={loading} className="btn btn-default disabled:opacity-50">取消</button>
            <button onClick={handleAdd} disabled={loading} className="btn btn-primary disabled:opacity-50">保存</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">商品名称</label>
            <input
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              className="input"
              placeholder="例如 基础CDN加速-代理版"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">来源商户</label>
            <input
              value={form.source}
              onChange={(e) => setForm({ ...form, source: e.target.value })}
              className="input"
              placeholder="例如 极速云"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">成本价</label>
            <input
              type="number"
              step="0.01"
              value={form.costPrice}
              onChange={(e) => setForm({ ...form, costPrice: e.target.value })}
              className="input"
              placeholder="0.00"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">零售价</label>
            <input
              type="number"
              step="0.01"
              value={form.retailPrice}
              onChange={(e) => setForm({ ...form, retailPrice: e.target.value })}
              className="input"
              placeholder="0.00"
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
