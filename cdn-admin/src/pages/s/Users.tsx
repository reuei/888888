import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { users } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Search, Ban, CheckCircle, Users as UsersIcon } from 'lucide-react';

export default function Users() {
  const [list, setList] = useState(users);
  const [keyword, setKeyword] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');

  const toggleStatus = (id: string) => {
    setList(list.map((u) => (u.id === id ? { ...u, status: u.status === 'normal' ? 'banned' : 'normal' } : u)));
  };

  const filtered = list.filter((u) => {
    const matchKeyword = u.nickname.includes(keyword) || u.phone.includes(keyword) || u.id.includes(keyword);
    const matchStatus = statusFilter === 'all' || u.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'registerAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  return (
    <div>
      <PageHeader title="用户列表" breadcrumb={['会员/用户管理', '用户列表']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <Search size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索用户ID / 昵称 / 手机号"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <select className="input w-32" value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="normal">正常</option>
            <option value="banned">已封禁</option>
          </select>
          <button className="btn btn-primary">查询</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="用户ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="昵称" sortKey="nickname" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>手机号</th>
              <th>等级</th>
              <th>分组</th>
              <th><SortableHeader label="注册时间" sortKey="registerAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((u) => (
              <tr key={u.id}>
                <td className="text-text-secondary">{u.id}</td>
                <td className="font-medium">{u.nickname}</td>
                <td>{u.phone}</td>
                <td>{u.level}</td>
                <td>{u.group}</td>
                <td className="text-text-secondary">{u.registerAt}</td>
                <td>
                  <span className={`badge ${statusBadge(u.status)}`}>{statusText(u.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    {u.status === 'normal' ? (
                      <button
                        onClick={() => toggleStatus(u.id)}
                        className="p-1.5 rounded hover:bg-gray-100 text-danger"
                        title="封禁"
                      >
                        <Ban size={16} />
                      </button>
                    ) : (
                      <button
                        onClick={() => toggleStatus(u.id)}
                        className="p-1.5 rounded hover:bg-gray-100 text-success"
                        title="解禁"
                      >
                        <CheckCircle size={16} />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无用户" description="没有符合筛选条件的用户" icon={<UsersIcon size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>
    </div>
  );
}
