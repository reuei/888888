import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { roles, permissionOptions } from '../../data/mock';
import { Plus, Edit, Trash2, Shield } from 'lucide-react';
import type { RolePermission } from '../../types';

export default function Roles() {
  const [list, setList] = useState<RolePermission[]>(roles);
  const [addModalOpen, setAddModalOpen] = useState(false);
  const [editModalOpen, setEditModalOpen] = useState(false);
  const [editingRole, setEditingRole] = useState<RolePermission | null>(null);
  const [addForm, setAddForm] = useState({ name: '', description: '', permissions: [] as string[] });
  const [editPermissions, setEditPermissions] = useState<string[]>([]);

  const togglePermission = (current: string[], key: string) => {
    if (current.includes(key)) {
      return current.filter((k) => k !== key);
    }
    return [...current, key];
  };

  const handleAdd = () => {
    if (!addForm.name.trim()) return;
    const newRole: RolePermission = {
      id: `R${String(list.length + 1).padStart(3, '0')}`,
      name: addForm.name.trim(),
      description: addForm.description.trim(),
      permissions: addForm.permissions,
      userCount: 0,
    };
    setList([...list, newRole]);
    setAddForm({ name: '', description: '', permissions: [] });
    setAddModalOpen(false);
  };

  const openEditModal = (role: RolePermission) => {
    setEditingRole(role);
    setEditPermissions(role.permissions.includes('*') ? permissionOptions.map((p) => p.key) : role.permissions);
    setEditModalOpen(true);
  };

  const handleEditSave = () => {
    if (!editingRole) return;
    setList((prev) =>
      prev.map((r) => (r.id === editingRole.id ? { ...r, permissions: editPermissions } : r))
    );
    setEditModalOpen(false);
    setEditingRole(null);
  };

  const handleDelete = (id: string) => {
    setList((prev) => prev.filter((r) => r.id !== id));
  };

  return (
    <div>
      <PageHeader
        title="权限角色管理"
        breadcrumb={['系统运维', '权限角色管理']}
        actions={
          <button onClick={() => setAddModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加角色
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>角色ID</th>
              <th>角色名称</th>
              <th>描述</th>
              <th>权限数</th>
              <th>关联用户数</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td className="font-medium">{r.name}</td>
                <td className="text-text-secondary">{r.description}</td>
                <td>{r.permissions.includes('*') ? '全部' : r.permissions.length}</td>
                <td>{r.userCount}</td>
                <td>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => openEditModal(r)}
                      className="p-1.5 rounded hover:bg-gray-100 text-primary"
                      title="编辑权限"
                    >
                      <Shield size={16} />
                    </button>
                    <button
                      onClick={() => openEditModal(r)}
                      className="p-1.5 rounded hover:bg-gray-100 text-warning"
                      title="编辑"
                    >
                      <Edit size={16} />
                    </button>
                    <button
                      onClick={() => handleDelete(r.id)}
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
      </div>

      <Modal
        open={addModalOpen}
        title="添加角色"
        onClose={() => setAddModalOpen(false)}
        footer={
          <>
            <button onClick={() => setAddModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">确认</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">角色名称</label>
            <input
              value={addForm.name}
              onChange={(e) => setAddForm({ ...addForm, name: e.target.value })}
              className="input"
              placeholder="请输入角色名称"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">描述</label>
            <input
              value={addForm.description}
              onChange={(e) => setAddForm({ ...addForm, description: e.target.value })}
              className="input"
              placeholder="请输入角色描述"
            />
          </div>
          <div>
            <label className="block text-sm mb-2">权限配置</label>
            <div className="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto p-2 border border-border rounded">
              {permissionOptions.map((p) => (
                <label key={p.key} className="flex items-center gap-2 text-sm">
                  <input
                    type="checkbox"
                    checked={addForm.permissions.includes(p.key)}
                    onChange={() =>
                      setAddForm({ ...addForm, permissions: togglePermission(addForm.permissions, p.key) })
                    }
                    className="w-4 h-4"
                  />
                  {p.label}
                </label>
              ))}
            </div>
          </div>
        </div>
      </Modal>

      <Modal
        open={editModalOpen}
        title={editingRole ? `编辑权限 - ${editingRole.name}` : '编辑权限'}
        onClose={() => setEditModalOpen(false)}
        footer={
          <>
            <button onClick={() => setEditModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleEditSave} className="btn btn-primary">保存</button>
          </>
        }
      >
        <div className="space-y-3">
          <div className="text-sm text-text-secondary mb-2">勾选该角色拥有的权限</div>
          <div className="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto p-2 border border-border rounded">
            {permissionOptions.map((p) => (
              <label key={p.key} className="flex items-center gap-2 text-sm">
                <input
                  type="checkbox"
                  checked={editPermissions.includes(p.key)}
                  onChange={() => setEditPermissions((prev) => togglePermission(prev, p.key))}
                  className="w-4 h-4"
                />
                {p.label}
              </label>
            ))}
          </div>
        </div>
      </Modal>
    </div>
  );
}
