import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { merchants } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle, XCircle, Eye, Store } from 'lucide-react';
import EmptyState from '../../components/EmptyState';

export default function SMerchantAudit() {
  const [list, setList] = useState(merchants.filter((m) => m.status === 'pending'));
  const [auditOpen, setAuditOpen] = useState(false);
  const [current, setCurrent] = useState<typeof merchants[0] | null>(null);
  const [reason, setReason] = useState('');

  const openAudit = (m: typeof merchants[0]) => {
    setCurrent(m);
    setReason('');
    setAuditOpen(true);
  };

  const updateStatus = (_status: 'normal' | 'banned') => {
    if (!current) return;
    setList(list.filter((m) => m.id !== current.id));
    setAuditOpen(false);
  };

  return (
    <div>
      <PageHeader title="商户审核" breadcrumb={['商户管理', '商户审核']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>商户</th>
              <th>手机号</th>
              <th>申请时间</th>
              <th>三要素验证</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((m) => (
              <tr key={m.id}>
                <td>
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">{m.avatar}</div>
                    <span className="font-medium">{m.shopName}</span>
                  </div>
                </td>
                <td>{m.phone}</td>
                <td className="text-text-secondary">{m.registerAt}</td>
                <td><span className="text-success text-sm">通过</span></td>
                <td>
                  <span className={`badge ${statusBadge(m.status)}`}>{statusText(m.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                      <Eye size={16} />
                    </button>
                    <button onClick={() => openAudit(m)} className="p-1.5 rounded hover:bg-gray-100 text-success" title="审核">
                      <CheckCircle size={16} />
                    </button>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-danger" title="驳回">
                      <XCircle size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
            {list.length === 0 && (
              <tr>
                <td colSpan={6}>
                  <EmptyState title="暂无待审核商户" description="当前没有需要审核的入驻商户" icon={<Store size={24} />} />
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <Modal
        open={auditOpen}
        title="商户审核"
        onClose={() => setAuditOpen(false)}
        footer={
          <>
            <button onClick={() => setAuditOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('banned')} className="btn btn-danger">驳回</button>
            <button onClick={() => updateStatus('normal')} className="btn btn-success">通过</button>
          </>
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">店铺名：</span>{current.shopName}</div>
            <div><span className="text-text-secondary">手机号：</span>{current.phone}</div>
            <div><span className="text-text-secondary">身份证：</span>11010119900101****</div>
            <div><span className="text-text-secondary">三要素验证：</span><span className="text-success">通过</span></div>
            <div className="grid grid-cols-2 gap-3 mt-3">
              <div className="h-24 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证正面</div>
              <div className="h-24 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证反面</div>
            </div>
            <div>
              <label className="block text-text-secondary mb-1">审核备注 / 驳回原因</label>
              <textarea className="input" rows={3} value={reason} onChange={(e) => setReason(e.target.value)} placeholder="选填"></textarea>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
