import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { adSlots } from '../../data/mock';
import { Plus, Edit, Trash2, Image } from 'lucide-react';

export default function SAds() {
  const [list] = useState(adSlots);
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<typeof adSlots[0] | null>(null);

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
        <table className="table">
          <thead>
            <tr>
              <th>广告位名称</th>
              <th>展示位置</th>
              <th>尺寸</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((a) => (
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
