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
import { useToast } from '../../hooks/useToast';
import * as api from '../../services/api';
import type { UserGroup } from '../../types';
import { Plus, Trash2, Users as UsersIcon } from 'lucide-react';

export default function UserGroups() {
  const { show } = useToast();
  const [list, setList] = useState<UserGroup[]>([]);
  const [loading, setLoading] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [name, setName] = useState('');

  const load = async () => {
    setLoading(true);
    try {
      const data = await api.fetchUserGroups();
      setList(data);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, []);

  const filtered = list.filter((g) =>
    !debouncedKeyword ||
    g.name.toLowerCase().includes(debouncedKeyword.toLowerCase()) ||
    g.id.toLowerCase().includes(debouncedKeyword.toLowerCase())
  );

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'userCount', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, setPage]);

  const handleAdd = async () => {
    if (!name.trim()) return;
    await api.createUserGroup({ name: name.trim(), userCount: 0 });
    await load();
    setName('');
    setModalOpen(false);
    show('分组添加成功', 'success');
  };

  const handleDelete = async (id: string) => {
    const target = list.find((g) => g.id === id);
    await api.deleteUserGroup(id);
    await load();
    show(`分组 ${target?.name} 已删除`, 'warning');
  };

  return (
    <div>
      <PageHeader
        title="用户分组"
        breadcrumb={['会员/用户管理', '用户分组']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加分组
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="flex items-center gap-2 flex-1 min-w-[200px]">
            <UsersIcon size={16} className="text-text-secondary" />
            <input
              type="text"
              placeholder="搜索分组ID / 分组名称"
              className="input"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <button onClick={() => setKeyword('')} className="btn btn-default">重置</button>
        </div>

        {loading ? (
          <Loading />
        ) : (
          <>
            <table className="table">
              <thead>
                <tr>
                  <th><SortableHeader label="分组ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="分组名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th><SortableHeader label="用户数量" sortKey="userCount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((g) => (
                  <tr key={g.id}>
                    <td className="text-text-secondary">{g.id}</td>
                    <td className="font-medium">{g.name}</td>
                    <td>{g.userCount}</td>
                    <td>
                      <button onClick={() => handleDelete(g.id)} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                        <Trash2 size={16} />
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {filtered.length === 0 && (
              <EmptyState title="暂无分组" description="没有符合筛选条件的分组" icon={<UsersIcon size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>

      <Modal
        open={modalOpen}
        title="添加分组"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">确认</button>
          </>
        }
      >
        <div>
          <label className="block text-sm mb-1">分组名称</label>
          <input
            value={name}
            onChange={(e) => setName(e.target.value)}
            className="input"
            placeholder="请输入分组名称"
          />
        </div>
      </Modal>
    </div>
  );
}
