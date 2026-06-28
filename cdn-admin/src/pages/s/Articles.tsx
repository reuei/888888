import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { articles } from '../../data/mock';
import { Plus, Edit, Trash2, Pin } from 'lucide-react';

export default function SArticles() {
  const [list, setList] = useState(articles);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ title: '', category: '平台公告', content: '', isTop: false, status: 'draft' });

  const handleAdd = () => {
    setList([
      {
        id: `A00${list.length + 1}`,
        title: form.title,
        category: form.category,
        isTop: form.isTop,
        status: form.status as 'published' | 'draft',
        publishAt: form.status === 'published' ? new Date().toLocaleString('zh-CN') : '-',
      },
      ...list,
    ]);
    setModalOpen(false);
    setForm({ title: '', category: '平台公告', content: '', isTop: false, status: 'draft' });
  };

  const toggleTop = (id: string) => {
    setList(list.map((a) => (a.id === id ? { ...a, isTop: !a.isTop } : a)));
  };

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
        <table className="table">
          <thead>
            <tr>
              <th>标题</th>
              <th>分类</th>
              <th>置顶</th>
              <th>状态</th>
              <th>发布时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((a) => (
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
                    <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
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
