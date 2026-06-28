import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { realnameRecords } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Eye } from 'lucide-react';

export default function UserRealname() {
  const [list, setList] = useState(realnameRecords);
  const [auditOpen, setAuditOpen] = useState(false);
  const [current, setCurrent] = useState<typeof list[0] | null>(null);

  const openAudit = (r: typeof list[0]) => {
    setCurrent(r);
    setAuditOpen(true);
  };

  const updateStatus = (status: 'approved' | 'rejected') => {
    if (!current) return;
    setList(list.map((r) => (r.id === current.id ? { ...r, status } : r)));
    setAuditOpen(false);
    setCurrent(null);
  };

  return (
    <div>
      <PageHeader title="用户实名审核" breadcrumb={['会员/用户管理', '用户实名审核']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>申请ID</th>
              <th>用户ID</th>
              <th>姓名</th>
              <th>身份证号</th>
              <th>手机号</th>
              <th>提交时间</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td className="text-text-secondary">{r.userId}</td>
                <td className="font-medium">{r.name}</td>
                <td>{r.idCard}</td>
                <td>{r.phone}</td>
                <td className="text-text-secondary">{r.submittedAt}</td>
                <td>
                  <span className={`badge ${statusBadge(r.status)}`}>{statusText(r.status)}</span>
                </td>
                <td>
                  <button
                    onClick={() => openAudit(r)}
                    className="p-1.5 rounded hover:bg-gray-100 text-primary"
                    title="审核"
                  >
                    <Eye size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={auditOpen}
        title="实名审核"
        onClose={() => setAuditOpen(false)}
        footer={
          <>
            <button onClick={() => setAuditOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('rejected')} className="btn btn-danger">驳回</button>
            <button onClick={() => updateStatus('approved')} className="btn btn-success">通过</button>
          </>
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">申请ID：</span>{current.id}</div>
            <div><span className="text-text-secondary">用户ID：</span>{current.userId}</div>
            <div><span className="text-text-secondary">姓名：</span>{current.name}</div>
            <div><span className="text-text-secondary">身份证号：</span>{current.idCard}</div>
            <div><span className="text-text-secondary">手机号：</span>{current.phone}</div>
            <div><span className="text-text-secondary">提交时间：</span>{current.submittedAt}</div>
            <div>
              <label className="block text-text-secondary mb-1">审核备注 / 驳回原因</label>
              <textarea className="input" rows={3} placeholder="选填"></textarea>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
