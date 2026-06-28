import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Modal from '../../components/Modal';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { realnameRecords } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Eye, Search, UserCheck } from 'lucide-react';

export default function UserRealname() {
  const [list, setList] = useState(realnameRecords);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [statusFilter, setStatusFilter] = useState('all');
  const [auditOpen, setAuditOpen] = useState(false);
  const [current, setCurrent] = useState<typeof list[0] | null>(null);

  const filtered = list.filter((r) => {
    const matchKeyword = !debouncedKeyword ||
      r.name.includes(debouncedKeyword) ||
      r.phone.includes(debouncedKeyword) ||
      r.idCard.includes(debouncedKeyword) ||
      r.userId.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      r.id.toLowerCase().includes(debouncedKeyword.toLowerCase());
    const matchStatus = statusFilter === 'all' || r.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'submittedAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, statusFilter, setPage]);

  const openAudit = (r: typeof list[0]) => {
    setCurrent(r);
    setAuditOpen(true);
  };

  const updateStatus = (status: 'approved' | 'rejected') => {
    if (!current) return;
    setList(list.map((r) => (r.id === current.id ? { ...r, status } : r)));
    setAuditOpen(false);
    setCurrent(null);
  };

  return (
    <div>
      <PageHeader title="用户实名审核" breadcrumb={['会员/用户管理', '用户实名审核']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索用户ID / 姓名 / 手机号 / 身份证号"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <select className="input w-32" value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="pending">审核中</option>
            <option value="approved">已通过</option>
            <option value="rejected">已驳回</option>
          </select>
          <button onClick={() => { setKeyword(''); setStatusFilter('all'); }} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>申请ID</th>
              <th>用户ID</th>
              <th><SortableHeader label="姓名" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>身份证号</th>
              <th>手机号</th>
              <th><SortableHeader label="提交时间" sortKey="submittedAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td className="text-text-secondary">{r.userId}</td>
                <td className="font-medium">{r.name}</td>
                <td>{r.idCard}</td>
                <td>{r.phone}</td>
                <td className="text-text-secondary">{r.submittedAt}</td>
                <td>
                  <span className={`badge ${statusBadge(r.status)}`}>{statusText(r.status)}</span>
                </td>
                <td>
                  <button
                    onClick={() => openAudit(r)}
                    className="p-1.5 rounded hover:bg-gray-100 text-primary"
                    title="审核"
                  >
                    <Eye size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无实名申请" description="没有符合筛选条件的实名记录" icon={<UserCheck size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={auditOpen}
        title="实名审核"
        onClose={() => setAuditOpen(false)}
        footer={
          <>
            <button onClick={() => setAuditOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('rejected')} className="btn btn-danger">驳回</button>
            <button onClick={() => updateStatus('approved')} className="btn btn-success">通过</button>
          </>
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">申请ID：</span>{current.id}</div>
            <div><span className="text-text-secondary">用户ID：</span>{current.userId}</div>
            <div><span className="text-text-secondary">姓名：</span>{current.name}</div>
            <div><span className="text-text-secondary">身份证号：</span>{current.idCard}</div>
            <div><span className="text-text-secondary">手机号：</span>{current.phone}</div>
            <div><span className="text-text-secondary">提交时间：</span>{current.submittedAt}</div>
            <div>
              <label className="block text-text-secondary mb-1">审核备注 / 驳回原因</label>
              <textarea className="input" rows={3} placeholder="选填"></textarea>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
