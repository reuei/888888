import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { userGroups } from '../../data/mock';
import { Plus, Trash2 } from 'lucide-react';

export default function UserGroups() {
  const [list, setList] = useState(userGroups);
  const [modalOpen, setModalOpen] = useState(false);
  const [name, setName] = useState('');

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
        <table className="table">
          <thead>
            <tr>
              <th>分组ID</th>
              <th>分组名称</th>
              <th>用户数量</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((g) => (
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
