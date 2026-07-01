import { useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { useToast } from '../../components/Toast';
import * as api from '../../services/api';
import type { AdSlot } from '../../types';
import { Plus, Edit, Trash2, Image, Search, Megaphone } from 'lucide-react';

export default function SAds() {
  const { show } = useToast();
  const [list, setList] = useState<AdSlot[]>([]);
  const [loading, setLoading] = useState(true);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [configModalOpen, setConfigModalOpen] = useState(false);
  const [current, setCurrent] = useState<AdSlot | null>(null);
  const [addModalOpen, setAddModalOpen] = useState(false);
  const [addForm, setAddForm] = useState<{ name: string; position: string; size: string; status: 'on' | 'off' }>({
    name: '',
    position: '',
    size: '',
    status: 'on',
  });

  const load = async () => {
    setLoading(true);
    try {
      const data = await api.fetchAdSlots();
      setList(data);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, []);

  const filtered = list.filter((a) => {
    const q = debouncedKeyword.trim().toLowerCase();
    if (!q) return true;
    return a.name.toLowerCase().includes(q) || a.position.toLowerCase().includes(q);
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'name' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const openConfig = (slot: AdSlot) => {
    setCurrent(slot);
    setConfigModalOpen(true);
  };

  const handleDelete = async (id: string) => {
    await api.deleteAdSlot(id);
    show('删除成功', 'success');
    await load();
  };

  const handleToggleStatus = async (slot: AdSlot) => {
    const nextStatus = slot.status === 'on' ? 'off' : 'on';
    await api.updateAdSlot(slot.id, { status: nextStatus });
    show(`广告位已${nextStatus === 'on' ? '启用' : '禁用'}`, 'success');
    await load();
  };

  const handleAddSave = async () => {
    if (!addForm.name.trim() || !addForm.position.trim() || !addForm.size.trim()) return;
    await api.createAdSlot({
      name: addForm.name,
      position: addForm.position,
      size: addForm.size,
      status: addForm.status,
    });
    show('新增成功', 'success');
    setAddModalOpen(false);
    setAddForm({ name: '', position: '', size: '', status: 'on' });
    await load();
  };

  return (
    <div>
      <PageHeader
        title="广告位列表"
        breadcrumb={['广告位管理', '广告位列表']}
        actions={
          <button onClick={() => setAddModalOpen(true)} className="btn btn-primary flex items-center gap-1">
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

        {loading ? (
          <Loading />
        ) : (
          <>
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
                      <button
                        onClick={() => handleToggleStatus(a)}
                        className={`badge ${a.status === 'on' ? 'badge-success' : 'badge-default'}`}
                        title="点击切换状态"
                      >
                        {a.status === 'on' ? '启用' : '禁用'}
                      </button>
                    </td>
                    <td>
                      <div className="flex items-center gap-2">
                        <button onClick={() => openConfig(a)} className="p-1.5 rounded hover:bg-gray-100 text-primary" title="配置内容">
                          <Image size={16} />
                        </button>
                        <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                          <Edit size={16} />
                        </button>
                        <button
                          onClick={() => handleDelete(a.id)}
                          className="p-1.5 rounded hover:bg-gray-100 text-danger"
                          title="删除"
                        >
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
          </>
        )}
      </div>

      <Modal
        open={configModalOpen}
        title="广告内容配置"
        onClose={() => setConfigModalOpen(false)}
        footer={
          <>
            <button onClick={() => setConfigModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => setConfigModalOpen(false)} className="btn btn-primary">保存</button>
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

      <Modal
        open={addModalOpen}
        title="新增广告位"
        onClose={() => setAddModalOpen(false)}
        footer={
          <>
            <button onClick={() => setAddModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAddSave} className="btn btn-primary">保存</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">广告位名称</label>
            <input
              value={addForm.name}
              onChange={(e) => setAddForm({ ...addForm, name: e.target.value })}
              className="input"
              placeholder="例如：PC 首页轮播"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">展示位置</label>
            <input
              value={addForm.position}
              onChange={(e) => setAddForm({ ...addForm, position: e.target.value })}
              className="input"
              placeholder="例如：电脑端首页顶部"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">尺寸</label>
            <input
              value={addForm.size}
              onChange={(e) => setAddForm({ ...addForm, size: e.target.value })}
              className="input"
              placeholder="例如：1920x400"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">状态</label>
            <select
              value={addForm.status}
              onChange={(e) => setAddForm({ ...addForm, status: e.target.value as 'on' | 'off' })}
              className="input"
            >
              <option value="on">启用</option>
              <option value="off">禁用</option>
            </select>
          </div>
        </div>
      </Modal>
    </div>
  );
}
