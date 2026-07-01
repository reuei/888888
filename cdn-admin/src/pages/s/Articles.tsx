import { useState, useEffect } from 'react';
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
import type { Article } from '../../types';
import { Plus, Edit, Trash2, Pin, Search, FileText } from 'lucide-react';

export default function SArticles() {
  const { show } = useToast();
  const [list, setList] = useState<Article[]>([]);
  const [loading, setLoading] = useState(true);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ title: '', category: '平台公告', content: '', isTop: false, status: 'draft' });

  const load = async () => {
    setLoading(true);
    const data = await api.fetchArticles();
    setList(data);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const filtered = list.filter((a) => {
    const q = debouncedKeyword.trim().toLowerCase();
    if (!q) return true;
    return a.title.toLowerCase().includes(q) || a.category.toLowerCase().includes(q);
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({
    data: filtered,
    initialKey: 'publishAt',
    initialDirection: 'desc',
  });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const handleAdd = async () => {
    await api.createArticle({
      title: form.title,
      category: form.category,
      isTop: form.isTop,
      status: form.status as 'published' | 'draft',
    });
    await load();
    show('公告发布成功', 'success');
    setModalOpen(false);
    setForm({ title: '', category: '平台公告', content: '', isTop: false, status: 'draft' });
  };

  const handleDelete = async (id: string) => {
    if (!confirm('确定删除该公告吗？')) return;
    await api.deleteArticle(id);
    await load();
    show('公告已删除', 'info');
  };

  const toggleTop = async (id: string) => {
    const target = list.find((a) => a.id === id);
    if (!target) return;
    await api.updateArticle(id, { isTop: !target.isTop });
    await load();
  };

  if (loading) return <Loading />;

  return (
    <div>
      <PageHeader
        title="平台公告"
        breadcrumb={['文章/公告管理', '平台公告']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 发布公告
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索标题 / 分类"
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
              <th><SortableHeader label="标题" sortKey="title" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>分类</th>
              <th>置顶</th>
              <th>状态</th>
              <th><SortableHeader label="发布时间" sortKey="publishAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((a) => (
              <tr key={a.id}>
                <td className="font-medium">{a.title}</td>
                <td>{a.category}</td>
                <td>
                  <button onClick={() => toggleTop(a.id)} className={a.isTop ? 'text-warning' : 'text-text-secondary'}>
                    <Pin size={16} className={a.isTop ? 'fill-current' : ''} />
                  </button>
                </td>
                <td>
                  <span className={`badge ${a.status === 'published' ? 'badge-success' : 'badge-default'}`}>
                    {a.status === 'published' ? '已发布' : '草稿'}
                  </span>
                </td>
                <td className="text-text-secondary">{a.publishAt}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => handleDelete(a.id)} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无公告" description="没有符合搜索条件的公告" icon={<FileText size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={modalOpen}
        title="发布公告"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">保存</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">标题</label>
            <input value={form.title} onChange={(e) => setForm({ ...form, title: e.target.value })} className="input" />
          </div>
          <div>
            <label className="block text-sm mb-1">分类</label>
            <select value={form.category} onChange={(e) => setForm({ ...form, category: e.target.value })} className="input">
              <option>平台公告</option>
              <option>结算公告</option>
              <option>新闻公告</option>
              <option>帮助文档</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">内容</label>
            <textarea value={form.content} onChange={(e) => setForm({ ...form, content: e.target.value })} className="input" rows={6} placeholder="支持 HTML、图片、视频"></textarea>
          </div>
          <div className="flex items-center gap-4">
            <label className="flex items-center gap-1.5 text-sm">
              <input type="checkbox" checked={form.isTop} onChange={(e) => setForm({ ...form, isTop: e.target.checked })} /> 置顶
            </label>
            <label className="flex items-center gap-1.5 text-sm">
              <input type="checkbox" checked={form.status === 'published'} onChange={(e) => setForm({ ...form, status: e.target.checked ? 'published' : 'draft' })} /> 立即发布
            </label>
          </div>
        </div>
      </Modal>
    </div>
  );
}
