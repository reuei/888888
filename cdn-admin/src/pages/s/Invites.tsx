import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { inviteCodes } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Plus, Copy, Ban } from 'lucide-react';

export default function SInvites() {
  const [list, setList] = useState(inviteCodes);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ maxUses: 100, expiry: '2026-12-31' });

  const generateCode = () => {
    const code = 'INVITE' + Math.random().toString(36).substring(2, 8).toUpperCase();
    setList([
      {
        id: `I00${list.length + 1}`,
        code,
        maxUses: form.maxUses,
        usedCount: 0,
        expiry: form.expiry,
        status: 'active',
      },
      ...list,
    ]);
    setModalOpen(false);
  };

  const disableCode = (id: string) => {
    setList(list.map((i) => (i.id === id ? { ...i, status: 'disabled' as const } : i)));
  };

  return (
    <div>
      <PageHeader
        title="邀请码管理"
        breadcrumb={['商户管理', '邀请码管理']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 生成邀请码
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>邀请码</th>
              <th>有效期</th>
              <th>使用次数上限</th>
              <th>已使用</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((i) => (
              <tr key={i.id}>
                <td className="font-medium">{i.code}</td>
                <td className="text-text-secondary">{i.expiry}</td>
                <td>{i.maxUses}</td>
                <td>{i.usedCount}</td>
                <td>
                  <span className={`badge ${statusBadge(i.status)}`}>{statusText(i.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="复制">
                      <Copy size={16} />
                    </button>
                    {i.status === 'active' && (
                      <button onClick={() => disableCode(i.id)} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="禁用">
                        <Ban size={16} />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={modalOpen}
        title="生成邀请码"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={generateCode} className="btn btn-primary">生成</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">使用次数上限</label>
            <input
              type="number"
              value={form.maxUses}
              onChange={(e) => setForm({ ...form, maxUses: parseInt(e.target.value) || 0 })}
              className="input"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">有效期至</label>
            <input
              type="date"
              value={form.expiry}
              onChange={(e) => setForm({ ...form, expiry: e.target.value })}
              className="input"
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
