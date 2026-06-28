import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import Modal from '../../components/Modal';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { userGroups } from '../../data/mock';
import { Plus, Trash2, Users as UsersIcon } from 'lucide-react';

export default function UserGroups() {
  const [list, setList] = useState(userGroups);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [name, setName] = useState('');

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

  const handleAdd = () => {
    if (!name.trim()) return;
    setList([{ id: `G${list.length + 1}`, name: name.trim(), userCount: 0 }, ...list]);
    setName('');
    setModalOpen(false);
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
                  <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
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
