import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../components/Toast';
import { products } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Edit, Trash2, ArrowUpDown, Plus, PackageSearch, RefreshCcw } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import { usePagination } from '../../hooks/usePagination';

export default function SProducts() {
  const { show } = useToast();
  const [list, setList] = useState(products);
  const [keyword, setKeyword] = useState('');
  const [typeFilter, setTypeFilter] = useState('all');
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', type: 'CDN', nodePool: '', priceRange: '' });

  const filtered = list.filter((p) => {
    const matchKeyword = !keyword || p.name.toLowerCase().includes(keyword.toLowerCase()) || p.id.toLowerCase().includes(keyword.toLowerCase());
    const matchType = typeFilter === 'all' || p.type === typeFilter;
    return matchKeyword && matchType;
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: filtered.length });
  const pagedList = slice(filtered);

  const reset = () => {
    setKeyword('');
    setTypeFilter('all');
  };

  const toggleStatus = (id: string) => {
    const target = list.find((p) => p.id === id);
    const nextStatus = target?.status === 'on' ? 'off' : 'on';
    setList(list.map((p) => (p.id === id ? { ...p, status: nextStatus } : p)));
    show(`产品 ${target?.name} 已${nextStatus === 'on' ? '上架' : '下架'}`, nextStatus === 'on' ? 'success' : 'warning');
  };

  const handleAdd = () => {
    setList([
      {
        id: `P00${list.length + 1}`,
        name: form.name,
        type: form.type,
        nodePool: form.nodePool,
        priceRange: form.priceRange,
        status: 'on',
      },
      ...list,
    ]);
    setModalOpen(false);
    setForm({ name: '', type: 'CDN', nodePool: '', priceRange: '' });
    show('新产品添加成功', 'success');
  };

  return (
    <div>
      <PageHeader
        title="CDN 产品列表"
        breadcrumb={['商品管理', 'CDN产品列表']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新增产品
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <input
            type="text"
            placeholder="产品名称 / ID"
            className="input flex-1 min-w-[200px]"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
          />
          <select className="input w-32" value={typeFilter} onChange={(e) => setTypeFilter(e.target.value)}>
            <option value="all">全部类型</option>
            <option value="CDN">CDN</option>
            <option value="高防CDN">高防CDN</option>
            <option value="游戏盾">游戏盾</option>
          </select>
          <button className="btn btn-primary">查询</button>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>产品名称</th>
              <th>类型</th>
              <th>节点池</th>
              <th>价格区间</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((p) => (
              <tr key={p.id}>
                <td className="font-medium">{p.name}</td>
                <td>{p.type}</td>
                <td className="text-text-secondary">{p.nodePool}</td>
                <td>{p.priceRange}</td>
                <td>
                  <span className={`badge ${statusBadge(p.status)}`}>{statusText(p.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => toggleStatus(p.id)} className="p-1.5 rounded hover:bg-gray-100 text-warning" title="上/下架">
                      <ArrowUpDown size={16} />
                    </button>
                    <button onClick={() => show(`产品 ${p.name} 已删除`, 'warning')} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无产品" description="没有符合筛选条件的产品" icon={<PackageSearch size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={modalOpen}
        title="新增产品"
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
            <label className="block text-sm mb-1">产品名称</label>
            <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="input" />
          </div>
          <div>
            <label className="block text-sm mb-1">产品类型</label>
            <select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })} className="input">
              <option>CDN</option>
              <option>高防CDN</option>
              <option>游戏盾</option>
              <option>全球加速</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">节点池</label>
            <input value={form.nodePool} onChange={(e) => setForm({ ...form, nodePool: e.target.value })} className="input" placeholder="例如 公开节点池A" />
          </div>
          <div>
            <label className="block text-sm mb-1">价格区间</label>
            <input value={form.priceRange} onChange={(e) => setForm({ ...form, priceRange: e.target.value })} className="input" placeholder="例如 ¥9.90 - ¥99.00" />
          </div>
        </div>
      </Modal>
    </div>
  );
}
