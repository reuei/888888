import { useCallback, useEffect, useMemo, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchAgents } from '../../services/api';
import { Network, Search, Users } from 'lucide-react';
import type { Agent } from '../../types';

interface AgentRow {
  id: string;
  name: string;
  parent: string | null;
  parentName: string;
  level: number;
  commission: number;
}

export default function AgentTree() {
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [agents, setAgents] = useState<Agent[]>([]);
  const [loading, setLoading] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchAgents();
    setAgents(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const list = useMemo<AgentRow[]>(() => {
    const agentMap = new Map(agents.map((a) => [a.id, a]));
    return agents
      .map((a) => ({
        ...a,
        parentName: a.parent ? agentMap.get(a.parent)?.name ?? '-' : '-',
      }))
      .sort((a, b) => {
        if (a.level !== b.level) return a.level - b.level;
        if ((a.parent ?? '') !== (b.parent ?? '')) return (a.parent ?? '').localeCompare(b.parent ?? '');
        return a.id.localeCompare(b.id);
      });
  }, [agents]);

  const filtered = useMemo(() => {
    if (!debouncedKeyword) return list;
    return list.filter((a) =>
      a.name.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      a.id.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      a.parentName.toLowerCase().includes(debouncedKeyword.toLowerCase())
    );
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'level', initialDirection: 'asc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, setPage]);

  return (
    <div>
      <PageHeader
        title="代理关系树"
        breadcrumb={['代理/分销管理', '代理关系树']}
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索代理ID / 代理名称 / 上级代理"
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
              <th>代理ID</th>
              <th><SortableHeader label="代理名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="上级代理" sortKey="parentName" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="等级" sortKey="level" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="佣金比例(%)" sortKey="commission" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {loading && (
              <tr>
                <td colSpan={6}>
                  <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>
                </td>
              </tr>
            )}
            {!loading && pagedList.map((a) => (
              <tr key={a.id}>
                <td className="text-text-secondary">{a.id}</td>
                <td>
                  <div className="flex items-center gap-2" style={{ paddingLeft: `${(a.level - 1) * 1.5}rem` }}>
                    <Network size={16} className="text-primary" />
                    <span className="font-medium">{a.name}</span>
                  </div>
                </td>
                <td>{a.parentName}</td>
                <td>{a.level}</td>
                <td>{a.commission}%</td>
                <td>
                  <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                    <Network size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {!loading && filtered.length === 0 && (
          <EmptyState title="暂无代理" description="没有符合筛选条件的代理" icon={<Users size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>
    </div>
  );
}
