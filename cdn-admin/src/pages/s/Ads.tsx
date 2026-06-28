import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { adSlots } from '../../data/mock';
import { Plus, Edit, Trash2, Image, Search, Megaphone } from 'lucide-react';

export default function SAds() {
  const [list] = useState(adSlots);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<typeof adSlots[0] | null>(null);

  const filtered = list.filter((a) => {
    const q = debouncedKeyword.trim().toLowerCase();
    if (!q) return true;
    return a.name.toLowerCase().includes(q) || a.position.toLowerCase().includes(q);
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'name' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const openConfig = (slot: typeof adSlots[0]) => {
    setCurrent(slot);
    setModalOpen(true);
  };

  return (
    <div>
      <PageHeader
        title="广告位列表"
        breadcrumb={['广告位管理', '广告位列表']}
        actions={
          <button className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新增广告位
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索广告位 / 展示位置"
              className="input pl-9 w-full"
              value={keyword}
              onChange={(e) => {
                setKeyword(e.target.value);
                setPage(1);
              }}
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="广告位名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>展示位置</th>
              <th>尺寸</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((a) => (
              <tr key={a.id}>
                <td className="font-medium">{a.name}</td>
                <td>{a.position}</td>
                <td>{a.size}</td>
                <td>
                  <span className={`badge ${a.status === 'on' ? 'badge-success' : 'badge-default'}`}>
                    {a.status === 'on' ? '启用' : '禁用'}
                  </span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => openConfig(a)} className="p-1.5 rounded hover:bg-gray-100 text-primary" title="配置内容">
                      <Image size={16} />
                    </button>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无广告位" description="没有符合搜索条件的广告位" icon={<Megaphone size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={modalOpen}
        title="广告内容配置"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => setModalOpen(false)} className="btn btn-primary">保存</button>
          </>
        }
      >
        {current && (
          <div className="space-y-4">
            <div><span className="text-text-secondary">广告位：</span>{current.name}</div>
            <div><span className="text-text-secondary">尺寸：</span>{current.size}</div>
            <div>
              <label className="block text-sm mb-1">上传图片 / 文字</label>
              <button className="btn btn-default text-xs">上传素材</button>
            </div>
            <div>
              <label className="block text-sm mb-1">跳转链接</label>
              <input className="input" placeholder="https://..." />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className="block text-sm mb-1">开始时间</label>
                <input type="datetime-local" className="input" />
              </div>
              <div>
                <label className="block text-sm mb-1">结束时间</label>
                <input type="datetime-local" className="input" />
              </div>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
