import { useEffect, useMemo, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { agentProducts } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { Search, Eye, Package } from 'lucide-react';
import type { AgentProduct } from '../../types';

interface AgentProductRow extends AgentProduct {
  profit: number;
}

export default function AgentProducts() {
  const [search, setSearch] = useState('');
  const debouncedSearch = useDebounce(search);

  const list = useMemo<AgentProductRow[]>(
    () => agentProducts.map((p) => ({ ...p, profit: p.retailPrice - p.costPrice })),
    []
  );

  const filtered = list.filter((p) => {
    const keyword = debouncedSearch.trim().toLowerCase();
    if (!keyword) return true;
    return p.source.toLowerCase().includes(keyword) || p.name.toLowerCase().includes(keyword) || p.id.toLowerCase().includes(keyword);
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'profit', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedSearch, setPage]);

  return (
    <div>
      <PageHeader
        title="下级代理商品"
        breadcrumb={['代理/分销管理', '下级代理商品']}
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索商品ID / 商品名称 / 代理商"
              className="input"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
          <button onClick={() => setSearch('')} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>商品ID</th>
              <th><SortableHeader label="商品名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="代理商" sortKey="source" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="成本价" sortKey="costPrice" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="零售价" sortKey="retailPrice" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="利润" sortKey="profit" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((p) => (
              <tr key={p.id}>
                <td className="text-text-secondary">{p.id}</td>
                <td className="font-medium">{p.name}</td>
                <td>{p.source}</td>
                <td>¥{formatMoney(p.costPrice)}</td>
                <td>¥{formatMoney(p.retailPrice)}</td>
                <td className="text-success">¥{formatMoney(p.profit)}</td>
                <td>
                  <span className={`badge ${statusBadge(p.status)}`}>{statusText(p.status)}</span>
                </td>
                <td>
                  <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                    <Eye size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无商品" description="没有符合筛选条件的代理商品" icon={<Package size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>
    </div>
  );
}
