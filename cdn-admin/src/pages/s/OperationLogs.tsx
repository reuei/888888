import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchOperationLogs } from '../../services/api';
import { Search, FileSearch } from 'lucide-react';
import type { OperationLog } from '../../types';

export default function OperationLogs() {
  const { show } = useToast();
  const [operationLogs, setOperationLogs] = useState<OperationLog[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [moduleFilter, setModuleFilter] = useState('');
  const [actionFilter, setActionFilter] = useState('');

  const loadOperationLogs = useCallback(async () => {
    setLoading(true);
    try {
      const data = await fetchOperationLogs();
      setOperationLogs(data);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadOperationLogs();
  }, [loadOperationLogs]);

  const modules = useMemo(() => Array.from(new Set(operationLogs.map((l) => l.module))), [operationLogs]);
  const actions = useMemo(() => Array.from(new Set(operationLogs.map((l) => l.action))), [operationLogs]);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return operationLogs.filter((l) => {
      const matchKeyword =
        !q ||
        l.id.toLowerCase().includes(q) ||
        l.operator.toLowerCase().includes(q) ||
        l.detail.toLowerCase().includes(q) ||
        l.ip.includes(q);
      const matchModule = !moduleFilter || l.module === moduleFilter;
      const matchAction = !actionFilter || l.action === actionFilter;
      return matchKeyword && matchModule && matchAction;
    });
  }, [debouncedKeyword, moduleFilter, actionFilter, operationLogs]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<OperationLog>({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, moduleFilter, actionFilter, sortKey, setPage]);

  return (
    <div>
      <PageHeader title="操作日志" breadcrumb={['系统运维', '操作日志']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索日志ID / 操作人 / 详情 / IP"
              className="input pl-8"
            />
          </div>
          <select
            value={moduleFilter}
            onChange={(e) => setModuleFilter(e.target.value)}
            className="input w-40"
          >
            <option value="">全部模块</option>
            {modules.map((m) => (
              <option key={m} value={m}>
                {m}
              </option>
            ))}
          </select>
          <select
            value={actionFilter}
            onChange={(e) => setActionFilter(e.target.value)}
            className="input w-40"
          >
            <option value="">全部操作</option>
            {actions.map((a) => (
              <option key={a} value={a}>
                {a}
              </option>
            ))}
          </select>
          <button
            onClick={() => {
              setKeyword('');
              setModuleFilter('');
              setActionFilter('');
              show('筛选条件已重置', 'info');
            }}
            className="btn btn-default"
          >
            重置
          </button>
        </div>

        <div className="text-sm text-text-secondary mb-3">共 {sorted.length} 条记录</div>

        {loading && <div className="text-sm text-text-secondary mb-3">加载中...</div>}

        {!loading && (
          <>
            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof OperationLog> label="日志ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="操作人" sortKey="operator" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="模块" sortKey="module" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="操作" sortKey="action" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="详情" sortKey="detail" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="IP" sortKey="ip" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof OperationLog> label="时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((l) => (
                  <tr key={l.id}>
                    <td className="text-text-secondary">{l.id}</td>
                    <td className="font-medium">{l.operator}</td>
                    <td>{l.module}</td>
                    <td>
                      <span className="badge badge-default">{l.action}</span>
                    </td>
                    <td>{l.detail}</td>
                    <td className="font-mono text-text-secondary">{l.ip}</td>
                    <td className="text-text-secondary">{l.createdAt}</td>
                  </tr>
                ))}
              </tbody>
            </table>

            {pagedList.length === 0 && (
              <EmptyState title="暂无日志" description="没有符合筛选条件的操作日志" icon={<FileSearch size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>
    </div>
  );
}
