import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { bannedProducts } from '../../data/mock';
import { Plus, Trash2 } from 'lucide-react';

export default function SBannedProducts() {
  const { show } = useToast();
  const [list, setList] = useState(bannedProducts);
  const [openAdd, setOpenAdd] = useState(false);
  const [form, setForm] = useState({ keyword: '', category: '虚拟商品', matchType: 'keyword' });

  const handleAdd = () => {
    if (!form.keyword.trim()) {
      show('请输入关键词', 'warning');
      return;
    }
    const newItem = {
      id: 'BP' + String(list.length + 1).padStart(2, '0'),
      keyword: form.keyword.trim(),
      category: form.category,
      matchType: form.matchType as 'keyword' | 'category',
      createdAt: new Date().toISOString().slice(0, 10),
    };
    setList([newItem, ...list]);
    setForm({ keyword: '', category: '虚拟商品', matchType: 'keyword' });
    setOpenAdd(false);
    show(`关键词「${newItem.keyword}」已添加`, 'success');
  };

  const handleDelete = (id: string, keyword: string) => {
    if (!confirm(`确定删除关键词「${keyword}」吗？`)) return;
    setList(list.filter((b) => b.id !== id));
    show(`关键词「${keyword}」已删除`, 'info');
  };

  const matchTypeBadge = (t: string) =>
    t === 'keyword' ? { cls: 'badge-info', text: '关键词匹配' } : { cls: 'badge-warning', text: '分类匹配' };

  return (
    <div>
      <PageHeader
        title="禁售目录"
        breadcrumb={['商品管理', '禁售目录']}
        actions={
          <button onClick={() => setOpenAdd(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加关键词
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>关键词</th>
              <th>分类</th>
              <th>匹配类型</th>
              <th>创建时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((b) => {
              const info = matchTypeBadge(b.matchType);
              return (
                <tr key={b.id}>
                  <td className="font-medium">{b.keyword}</td>
                  <td className="text-text-secondary">{b.category}</td>
                  <td>
                    <span className={`badge ${info.cls}`}>{info.text}</span>
                  </td>
                  <td className="text-text-secondary">{b.createdAt}</td>
                  <td>
                    <button
                      onClick={() => handleDelete(b.id, b.keyword)}
                      className="p-1.5 rounded hover:bg-gray-100 text-danger"
                      title="删除"
                    >
                      <Trash2 size={16} />
                    </button>
                  </td>
                </tr>
              );
            })}
            {list.length === 0 && (
              <tr>
                <td colSpan={5}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无禁售关键词</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {openAdd && (
        <div className="fixed inset-0 bg-black/40 flex items-center justify-center z-50" onClick={() => setOpenAdd(false)}>
          <div className="card p-6 w-full max-w-md" onClick={(e) => e.stopPropagation()}>
            <h3 className="font-semibold mb-4">添加禁售关键词</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm mb-1">关键词</label>
                <input
                  className="input"
                  value={form.keyword}
                  onChange={(e) => setForm({ ...form, keyword: e.target.value })}
                  placeholder="例如：违禁品A"
                />
              </div>
              <div>
                <label className="block text-sm mb-1">分类</label>
                <select
                  className="input"
                  value={form.category}
                  onChange={(e) => setForm({ ...form, category: e.target.value })}
                >
                  <option>虚拟商品</option>
                  <option>服务类</option>
                  <option>实物商品</option>
                  <option>其他</option>
                </select>
              </div>
              <div>
                <label className="block text-sm mb-1">匹配类型</label>
                <select
                  className="input"
                  value={form.matchType}
                  onChange={(e) => setForm({ ...form, matchType: e.target.value })}
                >
                  <option value="keyword">关键词匹配</option>
                  <option value="category">分类匹配</option>
                </select>
              </div>
            </div>
            <div className="flex justify-end gap-2 mt-5">
              <button onClick={() => setOpenAdd(false)} className="btn btn-default">
                取消
              </button>
              <button onClick={handleAdd} className="btn btn-primary">
                添加
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
