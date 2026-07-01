import { useEffect, useMemo, useState, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchUsers } from '../../services/api';
import { formatMoney } from '../../utils/helpers';
import { Medal, Search, TrendingUp } from 'lucide-react';
import type { User } from '../../types';

interface RankItem {
  id: string;
  nickname: string;
  amount: number;
  orders: number;
  lastAt: string;
}

const seedAmounts = [128450.0, 96320.5, 72400.0, 56100.0, 42800.0, 31500.0];
const seedOrders = [86, 64, 51, 38, 29, 22];
const seedLastAt = [
  '2026-06-28 10:23',
  '2026-06-28 09:45',
  '2026-06-27 22:10',
  '2026-06-27 18:33',
  '2026-06-26 15:20',
  '2026-06-26 11:05',
];

export default function UserRank() {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const loadUsers = useCallback(async () => {
    setLoading(true);
    try {
      const data = await fetchUsers();
      setUsers(data);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadUsers();
  }, [loadUsers]);

  const list = useMemo<RankItem[]>(() => {
    return users.map((u, index) => ({
      id: u.id,
      nickname: u.nickname,
      amount: seedAmounts[index % seedAmounts.length],
      orders: seedOrders[index % seedOrders.length],
      lastAt: seedLastAt[index % seedLastAt.length],
    }));
  }, [users]);

  const filtered = useMemo(() => {
    if (!debouncedKeyword) return list;
    return list.filter((u) =>
      u.nickname.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
      u.id.toLowerCase().includes(debouncedKeyword.toLowerCase())
    );
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'amount', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, setPage]);

  return (
    <div>
      <PageHeader title="用户流水排行" breadcrumb={['会员/用户管理', '用户流水排行']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索用户ID / 昵称"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <button onClick={() => setKeyword('')} className="btn btn-default">重置</button>
        </div>

        {loading && <div className="text-sm text-text-secondary mb-3">加载中...</div>}

        {!loading && (
          <>
            <table className="table">
              <thead>
                <tr>
                  <th>排名</th>
                  <th><SortableHeader label="用户" sortKey="nickname" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="消费金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="订单数" sortKey="orders" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="最近消费时间" sortKey="lastAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((u, index) => (
                  <tr key={u.id}>
                    <td>
                      <div className="flex items-center gap-2">
                        {index < 3 && (
                          <Medal
                            size={16}
                            className={index === 0 ? 'text-warning' : index === 1 ? 'text-text-secondary' : 'text-warning'}
                          />
                        )}
                        <span className="font-medium">{(page - 1) * pageSize + index + 1}</span>
                      </div>
                    </td>
                    <td>
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                          {u.nickname.slice(0, 2).toUpperCase()}
                        </div>
                        <span className="font-medium">{u.nickname}</span>
                      </div>
                    </td>
                    <td className="font-medium text-primary">¥{formatMoney(u.amount)}</td>
                    <td>{u.orders}</td>
                    <td className="text-text-secondary">{u.lastAt}</td>
                  </tr>
                ))}
              </tbody>
            </table>

            {filtered.length === 0 && (
              <EmptyState title="暂无排行数据" description="没有符合筛选条件的用户" icon={<TrendingUp size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>
    </div>
  );
}
