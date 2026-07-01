import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../components/Toast';
import { fetchMerchants, updateMerchant } from '../../services/api';
import { statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle, XCircle, Eye, Store } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import type { Merchant } from '../../types';

export default function SMerchantAudit() {
  const { show } = useToast();
  const [loading, setLoading] = useState(false);
  const [list, setList] = useState<Merchant[]>([]);
  const [auditOpen, setAuditOpen] = useState(false);
  const [current, setCurrent] = useState<Merchant | null>(null);
  const [reason, setReason] = useState('');

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchMerchants();
    setList(data.filter((m) => m.status === 'pending'));
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const openAudit = (m: Merchant) => {
    setCurrent(m);
    setReason('');
    setAuditOpen(true);
  };

  const updateStatus = async (status: 'normal' | 'banned') => {
    if (!current || loading) return;
    await updateMerchant(current.id, { status });
    await load();
    setAuditOpen(false);
    show(`商户 ${current.shopName} 审核${status === 'normal' ? '通过' : '已驳回'}`, status === 'normal' ? 'success' : 'warning');
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
            {loading && (
              <tr>
                <td colSpan={6}>
                  <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>
                </td>
              </tr>
            )}
            {!loading && list.length === 0 && (
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
            <button onClick={() => updateStatus('banned')} disabled={loading} className="btn btn-danger disabled:opacity-70">驳回</button>
            <button onClick={() => updateStatus('normal')} disabled={loading} className="btn btn-success disabled:opacity-70">通过</button>
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
