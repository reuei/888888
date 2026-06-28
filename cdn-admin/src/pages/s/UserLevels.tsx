import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Modal from '../../components/Modal';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { userLevels } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus, Trophy } from 'lucide-react';

function formatDiscount(d: number) {
  if (d === 1) return '无折扣';
  const v = Math.round(d * 100);
  if (v % 10 === 0) return `${v / 10}折`;
  return `${v}折`;
}

export default function UserLevels() {
  const [list, setList] = useState(userLevels);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', minAmount: 0, discount: 1 });

  const filtered = list.filter((l) =>
    !debouncedKeyword ||
    l.name.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
    l.id.toLowerCase().includes(debouncedKeyword.toLowerCase())
  );

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'minAmount', initialDirection: 'asc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, setPage]);

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
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Trophy size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索等级ID / 等级名称"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <button onClick={() => setKeyword('')} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="等级ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="等级名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="最低消费金额" sortKey="minAmount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="折扣" sortKey="discount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((l) => (
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

        {filtered.length === 0 && (
          <EmptyState title="暂无等级" description="没有符合筛选条件的等级" icon={<Trophy size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
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
