import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Modal from '../../components/Modal';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import Loading from '../../components/Loading';
import { useToast } from '../../components/Toast';
import * as api from '../../services/api';
import type { InviteCode } from '../../types';
import { statusBadge, statusText } from '../../utils/helpers';
import { Plus, Copy, Ban, Search, Ticket } from 'lucide-react';

export default function SInvites() {
  const { show } = useToast();
  const [list, setList] = useState<InviteCode[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [statusFilter, setStatusFilter] = useState('all');
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ maxUses: 100, expiry: '2026-12-31' });

  const load = async () => {
    setLoading(true);
    try {
      const data = await api.fetchInviteCodes();
      setList(data);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, []);

  const filtered = list.filter((i) => {
    const matchKeyword = !debouncedKeyword || i.code.toLowerCase().includes(debouncedKeyword.toLowerCase());
    const matchStatus = statusFilter === 'all' || i.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'usedCount', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, statusFilter, setPage]);

  const generateCode = async () => {
    const code = 'INVITE' + Math.random().toString(36).substring(2, 8).toUpperCase();
    await api.createInviteCode({ code, maxUses: form.maxUses, usedCount: 0, expiry: form.expiry, status: 'active' });
    await load();
    setModalOpen(false);
    show('邀请码生成成功', 'success');
  };

  const disableCode = async (id: string) => {
    await api.updateInviteCode(id, { status: 'disabled' });
    await load();
    show('邀请码已禁用', 'warning');
  };

  return (
    <div>
      <PageHeader
        title="邀请码管理"
        breadcrumb={['商户管理', '邀请码管理']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 生成邀请码
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索邀请码"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <select className="input w-32" value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="active">生效中</option>
            <option value="expired">已过期</option>
            <option value="disabled">已禁用</option>
          </select>
          <button onClick={() => { setKeyword(''); setStatusFilter('all'); }} className="btn btn-default">重置</button>
        </div>

        {loading ? (
          <Loading />
        ) : (
          <>
            <table className="table">
              <thead>
                <tr>
                  <th><SortableHeader label="邀请码" sortKey="code" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="有效期" sortKey="expiry" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="使用次数上限" sortKey="maxUses" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="已使用" sortKey="usedCount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th>状态</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((i) => (
                  <tr key={i.id}>
                    <td className="font-medium">{i.code}</td>
                    <td className="text-text-secondary">{i.expiry}</td>
                    <td>{i.maxUses}</td>
                    <td>{i.usedCount}</td>
                    <td>
                      <span className={`badge ${statusBadge(i.status)}`}>{statusText(i.status)}</span>
                    </td>
                    <td>
                      <div className="flex items-center gap-2">
                        <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="复制">
                          <Copy size={16} />
                        </button>
                        {i.status === 'active' && (
                          <button onClick={() => disableCode(i.id)} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="禁用">
                            <Ban size={16} />
                          </button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {filtered.length === 0 && (
              <EmptyState title="暂无邀请码" description="没有符合筛选条件的邀请码" icon={<Ticket size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>

      <Modal
        open={modalOpen}
        title="生成邀请码"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={generateCode} className="btn btn-primary">生成</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">使用次数上限</label>
            <input
              type="number"
              value={form.maxUses}
              onChange={(e) => setForm({ ...form, maxUses: parseInt(e.target.value) || 0 })}
              className="input"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">有效期至</label>
            <input
              type="date"
              value={form.expiry}
              onChange={(e) => setForm({ ...form, expiry: e.target.value })}
              className="input"
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
