import { useState, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Loading from '../../components/Loading';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import * as api from '../../services/api';
import type { User } from '../../types';
import { statusBadge, statusText } from '../../utils/helpers';
import { Search, Ban, CheckCircle, Users as UsersIcon, FileDown } from 'lucide-react';
import { exportToCsv } from '../../utils/export';

export default function Users() {
  const { show } = useToast();
  const [list, setList] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [statusFilter, setStatusFilter] = useState('all');

  const load = async () => {
    setLoading(true);
    const data = await api.fetchUsers();
    setList(data);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const toggleStatus = async (id: string) => {
    const target = list.find((u) => u.id === id);
    if (!target) return;
    await api.updateUser(id, { status: target.status === 'normal' ? 'banned' : 'normal' });
    await load();
    show('用户状态已更新', 'success');
  };

  const filtered = list.filter((u) => {
    const matchKeyword = !debouncedKeyword || u.nickname.includes(debouncedKeyword) || u.phone.includes(debouncedKeyword) || u.id.includes(debouncedKeyword);
    const matchStatus = statusFilter === 'all' || u.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'registerAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const handleExport = () => {
    exportToCsv(
      '用户列表',
      sorted,
      [
        { key: 'id', label: '用户ID' },
        { key: 'nickname', label: '昵称' },
        { key: 'phone', label: '手机号' },
        { key: 'level', label: '等级' },
        { key: 'group', label: '分组' },
        { key: 'registerAt', label: '注册时间' },
        { key: 'status', label: '状态' },
      ]
    );
    show('用户列表导出成功', 'success');
  };

  if (loading) return <Loading />;

  return (
    <div>
      <PageHeader
        title="用户列表"
        breadcrumb={['会员/用户管理', '用户列表']}
        actions={
          <button onClick={handleExport} className="btn btn-default flex items-center gap-1">
            <FileDown size={16} /> 导出
          </button>
        }
      />

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
