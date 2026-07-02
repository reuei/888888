import { useCallback, useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { useToast } from '../../hooks/useToast';
import { fetchCommissionRecords, updateCommissionRecord } from '../../services/api';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle, Search, Wallet } from 'lucide-react';
import type { CommissionRecord } from '../../types';

export default function AgentCommission() {
  const { show } = useToast();
  const [records, setRecords] = useState<CommissionRecord[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [statusFilter, setStatusFilter] = useState('all');

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchCommissionRecords();
    setRecords(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const filtered = records.filter((r) => {
    const matchKeyword = !debouncedKeyword ||
      r.agent.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      r.orderId.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      r.id.toLowerCase().includes(debouncedKeyword.toLowerCase());
    const matchStatus = statusFilter === 'all' || r.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'createdAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, statusFilter, setPage]);

  const batchSettle = async () => {
    if (loading) return;
    const pending = records.filter((r) => r.status === 'pending');
    if (pending.length === 0) return;
    await Promise.all(pending.map((r) => updateCommissionRecord(r.id, { status: 'settled' })));
    await load();
    show(`已批量结算 ${pending.length} 条佣金记录`, 'success');
  };

  const settleOne = async (id: string) => {
    if (loading) return;
    await updateCommissionRecord(id, { status: 'settled' });
    await load();
    show('佣金结算成功', 'success');
  };

  const pendingCount = records.filter((r) => r.status === 'pending').length;

  return (
    <div>
      <PageHeader
        title="佣金结算"
        breadcrumb={['代理/分销管理', '佣金结算']}
        actions={
          <button
            onClick={batchSettle}
            disabled={loading || pendingCount === 0}
            className="btn btn-success flex items-center gap-1 disabled:opacity-50"
          >
            <CheckCircle size={16} /> 批量结算 ({pendingCount})
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索记录ID / 代理商 / 订单号"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <select className="input w-32" value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="pending">待结算</option>
            <option value="settled">已结算</option>
          </select>
          <button onClick={() => { setKeyword(''); setStatusFilter('all'); }} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>记录ID</th>
              <th><SortableHeader label="代理商" sortKey="agent" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>订单号</th>
              <th><SortableHeader label="佣金金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th><SortableHeader label="时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
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
            {!loading && pagedList.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td>{r.agent}</td>
                <td>{r.orderId}</td>
                <td className="text-warning font-medium">¥{formatMoney(r.amount)}</td>
                <td>
                  <span className={`badge ${statusBadge(r.status)}`}>{statusText(r.status)}</span>
                </td>
                <td className="text-text-secondary">{r.createdAt}</td>
                <td>
                  {r.status === 'pending' ? (
                    <button onClick={() => settleOne(r.id)} disabled={loading} className="btn btn-success py-1 px-2 text-xs disabled:opacity-50">
                      结算
                    </button>
                  ) : (
                    <span className="text-text-secondary text-xs">已结算</span>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {!loading && filtered.length === 0 && (
          <EmptyState title="暂无佣金记录" description="没有符合筛选条件的佣金记录" icon={<Wallet size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>
    </div>
  );
}
