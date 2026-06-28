import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { categories } from '../../data/mock';
import { Edit, Trash2, Plus, GripVertical } from 'lucide-react';

export default function SCategories() {
  const [list, setList] = useState(categories);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', parentId: '' });

  const handleAdd = () => {
    setList([...list, { id: `C${list.length + 1}`, name: form.name, parentId: form.parentId || null, sort: list.length + 1 }]);
    setModalOpen(false);
    setForm({ name: '', parentId: '' });
  };

  const renderTree = (parentId: string | null, level = 0) => {
    return list
      .filter((c) => c.parentId === parentId)
      .sort((a, b) => a.sort - b.sort)
      .map((c) => (
        <div key={c.id}>
          <div
            className="flex items-center justify-between p-3 border-b border-border hover:bg-gray-50"
            style={{ paddingLeft: `${level * 24 + 12}px` }}
          >
            <div className="flex items-center gap-2">
              <GripVertical size={14} className="text-text-secondary cursor-move" />
              <span className="font-medium">{c.name}</span>
            </div>
            <div className="flex items-center gap-2">
              <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                <Edit size={16} />
              </button>
              <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                <Trash2 size={16} />
              </button>
            </div>
          </div>
          {renderTree(c.id, level + 1)}
        </div>
      ));
  };

  return (
    <div>
      <PageHeader
        title="产品分类"
        breadcrumb={['商品管理', '产品分类']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新增分类
          </button>
        }
      />

      <div className="card p-5">
        <div className="border border-border rounded overflow-hidden">
          {renderTree(null)}
        </div>
      </div>

      <Modal
        open={modalOpen}
        title="新增分类"
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
            <label className="block text-sm mb-1">分类名称</label>
            <input value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} className="input" />
          </div>
          <div>
            <label className="block text-sm mb-1">上级分类</label>
            <select value={form.parentId} onChange={(e) => setForm({ ...form, parentId: e.target.value })} className="input">
              <option value="">顶级分类</option>
              {list.filter((c) => c.parentId === null).map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </select>
          </div>
        </div>
      </Modal>
    </div>
  );
}
