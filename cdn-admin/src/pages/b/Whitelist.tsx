import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchWhitelistRecords, createWhitelistRecord } from '../../services/api';
import { statusBadge, statusText } from '../../utils/helpers';
import { Plus, Search, RefreshCcw, ShieldCheck } from 'lucide-react';
import type { WhitelistRecord } from '../../types';

export default function BWhitelist() {
  const { show } = useToast();
  const [list, setList] = useState<WhitelistRecord[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [statusFilter, setStatusFilter] = useState<'all' | WhitelistRecord['status']>('all');

  const [form, setForm] = useState({ domain: '', purpose: '', icp: '' });

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchWhitelistRecords();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const filtered = useMemo(() => {
    const kw = debouncedKeyword.trim().toLowerCase();
    return list.filter((w) => {
      const matchKw = !kw ||
        w.domain.toLowerCase().includes(kw) ||
        w.purpose.toLowerCase().includes(kw) ||
        w.icp.toLowerCase().includes(kw) ||
        (w.reason && w.reason.toLowerCase().includes(kw));
      const matchStatus = statusFilter === 'all' || w.status === statusFilter;
      return matchKw && matchStatus;
    });
  }, [list, debouncedKeyword, statusFilter]);

  const { sorted, sortKey, sortDirection, toggle } = useSort({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const reset = () => {
    setKeyword('');
    setStatusFilter('all');
    setPage(1);
  };

  const handleSubmit = async () => {
    if (!form.domain.trim() || loading) return;
    const now = new Date();
    const createdAt = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    await createWhitelistRecord({
      domain: form.domain.trim(),
      purpose: form.purpose.trim() || '-',
      icp: form.icp.trim() || '-',
      status: 'pending',
      createdAt,
    });
    await load();
    setForm({ domain: '', purpose: '', icp: '' });
    setPage(1);
    show('过白申请已提交', 'success');
  };

  return (
    <div>
      <PageHeader title="域名过白管理" breadcrumb={['域名过白管理', '申请记录']} />

      <div className="card p-5 mb-6">
        <h3 className="font-semibold mb-4">域名过白申请</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <input
            className="input"
            placeholder="请输入域名"
            value={form.domain}
            onChange={(e) => setForm({ ...form, domain: e.target.value })}
          />
          <input
            className="input"
            placeholder="用途说明"
            value={form.purpose}
            onChange={(e) => setForm({ ...form, purpose: e.target.value })}
          />
          <input
            className="input"
            placeholder="备案号（如适用）"
            value={form.icp}
            onChange={(e) => setForm({ ...form, icp: e.target.value })}
          />
        </div>
        <div className="mt-4 flex justify-end">
          <button onClick={handleSubmit} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 提交申请
          </button>
        </div>
      </div>

      <div className="card p-5">
        <h3 className="font-semibold mb-4">申请记录</h3>
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索域名 / 用途 / 备案号 / 备注"
              className="input pl-8"
              value={keyword}
              onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
            />
          </div>
          <select
            className="input"
            value={statusFilter}
            onChange={(e) => { setStatusFilter(e.target.value as 'all' | WhitelistRecord['status']); setPage(1); }}
          >
            <option value="all">全部状态</option>
            <option value="pending">审核中</option>
            <option value="approved">已通过</option>
            <option value="rejected">已驳回</option>
          </select>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="域名" sortKey="domain" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>用途</th>
              <th>备案号</th>
              <th><SortableHeader label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="申请时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>备注</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((w) => (
              <tr key={w.id}>
                <td className="font-medium">{w.domain}</td>
                <td>{w.purpose}</td>
                <td className="text-text-secondary">{w.icp}</td>
                <td>
                  <span className={`badge ${statusBadge(w.status)}`}>{statusText(w.status)}</span>
                </td>
                <td className="text-text-secondary">{w.createdAt}</td>
                <td className="text-text-secondary">{w.reason || '-'}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && sorted.length === 0 && (
          <EmptyState title="暂无申请记录" description="没有符合筛选条件的过白申请" icon={<ShieldCheck size={24} />} />
        )}

        {sorted.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>
    </div>
  );
}
