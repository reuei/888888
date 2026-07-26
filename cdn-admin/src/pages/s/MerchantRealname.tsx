import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { realnameRecords, users } from '../../data/mock';
import { CheckCircle, XCircle } from 'lucide-react';

interface Row {
  id: string;
  merchantName: string;
  merchantId: string;
  realName: string;
  idCard: string;
  phone: string;
  submittedAt: string;
  status: 'pending' | 'approved' | 'rejected';
}

// Build merchant realname records. Reuse realnameRecords but treat them as merchant submissions.
const initialRows: Row[] = realnameRecords.map((r) => {
  const u = users.find((u) => u.id === r.userId);
  return {
    id: r.id,
    merchantName: u?.nickname || `商户${r.userId}`,
    merchantId: r.userId,
    realName: r.name,
    idCard: r.idCard,
    phone: r.phone,
    submittedAt: r.submittedAt,
    status: r.status,
  };
});

const maskIdCard = (id: string) => {
  if (id.length <= 6) return id;
  return id.slice(0, 4) + '********' + id.slice(-4);
};

export default function SMerchantRealname() {
  const { show } = useToast();
  const [rows, setRows] = useState(initialRows);
  const [statusFilter, setStatusFilter] = useState('all');

  const filtered = rows.filter((r) => statusFilter === 'all' || r.status === statusFilter);

  const updateStatus = (id: string, status: 'approved' | 'rejected') => {
    setRows(rows.map((r) => (r.id === id ? { ...r, status } : r)));
    const target = rows.find((r) => r.id === id);
    show(
      `商户「${target?.merchantName}」实名认证${status === 'approved' ? '已通过' : '已拒绝'}`,
      status === 'approved' ? 'success' : 'warning'
    );
  };

  const badgeInfo = (s: string) => {
    switch (s) {
      case 'approved':
        return { cls: 'badge-success', text: '已通过' };
      case 'pending':
        return { cls: 'badge-warning', text: '审核中' };
      case 'rejected':
        return { cls: 'badge-danger', text: '已拒绝' };
      default:
        return { cls: 'badge-default', text: s };
    }
  };

  return (
    <div>
      <PageHeader title="实名认证管理" breadcrumb={['商户管理', '实名认证管理']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <select
            className="input w-40"
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
          >
            <option value="all">全部状态</option>
            <option value="pending">审核中</option>
            <option value="approved">已通过</option>
            <option value="rejected">已拒绝</option>
          </select>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>商户</th>
              <th>真实姓名</th>
              <th>身份证号</th>
              <th>提交时间</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((r) => {
              const info = badgeInfo(r.status);
              return (
                <tr key={r.id}>
                  <td>
                    <div className="font-medium">{r.merchantName}</div>
                    <div className="text-xs text-text-secondary">{r.merchantId}</div>
                  </td>
                  <td>{r.realName}</td>
                  <td className="font-mono text-text-secondary">{maskIdCard(r.idCard)}</td>
                  <td className="text-text-secondary">{r.submittedAt}</td>
                  <td>
                    <span className={`badge ${info.cls}`}>{info.text}</span>
                  </td>
                  <td>
                    {r.status === 'pending' ? (
                      <div className="flex items-center gap-2">
                        <button
                          onClick={() => updateStatus(r.id, 'approved')}
                          className="p-1.5 rounded hover:bg-gray-100 text-success"
                          title="通过"
                        >
                          <CheckCircle size={16} />
                        </button>
                        <button
                          onClick={() => updateStatus(r.id, 'rejected')}
                          className="p-1.5 rounded hover:bg-gray-100 text-danger"
                          title="拒绝"
                        >
                          <XCircle size={16} />
                        </button>
                      </div>
                    ) : (
                      <span className="text-xs text-text-secondary">已处理</span>
                    )}
                  </td>
                </tr>
              );
            })}
            {filtered.length === 0 && (
              <tr>
                <td colSpan={6}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无实名认证记录</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
