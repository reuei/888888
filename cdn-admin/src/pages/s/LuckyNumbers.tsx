import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Modal from '../../components/Modal';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { luckyNumbers } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus, ToggleLeft, ToggleRight, Search, Hash } from 'lucide-react';

export default function LuckyNumbers() {
  const [list, setList] = useState(luckyNumbers);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [soldFilter, setSoldFilter] = useState('all');
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ number: '', price: 0 });

  const filtered = list.filter((n) => {
    const matchKeyword = !debouncedKeyword || n.number.includes(debouncedKeyword) || n.id.toLowerCase().includes(debouncedKeyword.toLowerCase());
    const matchSold = soldFilter === 'all' || String(n.sold) === soldFilter;
    return matchKeyword && matchSold;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'price', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, soldFilter, setPage]);

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
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索编号ID / 靓号"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <select className="input w-32" value={soldFilter} onChange={(e) => setSoldFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="false">未售出</option>
            <option value="true">已售出</option>
          </select>
          <button onClick={() => { setKeyword(''); setSoldFilter('all'); }} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>编号ID</th>
              <th><SortableHeader label="靓号" sortKey="number" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="价格" sortKey="price" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="售出状态" sortKey="sold" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((n) => (
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

        {filtered.length === 0 && (
          <EmptyState title="暂无靓号" description="没有符合筛选条件的靓号" icon={<Hash size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
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
