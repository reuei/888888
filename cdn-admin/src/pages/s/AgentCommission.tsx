import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { commissionRecords } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle } from 'lucide-react';
import type { CommissionRecord } from '../../types';

export default function AgentCommission() {
  const [records, setRecords] = useState<CommissionRecord[]>(commissionRecords);

  const batchSettle = () => {
    setRecords(records.map((r) => (r.status === 'pending' ? { ...r, status: 'settled' as const } : r)));
  };

  const settleOne = (id: string) => {
    setRecords(records.map((r) => (r.id === id ? { ...r, status: 'settled' as const } : r)));
  };

  const pendingCount = records.filter((r) => r.status === 'pending').length;

  return (
    <div>
      <PageHeader
        title="佣金结算"
        breadcrumb={['代理/分销管理', '佣金结算']}
        actions={
          <button
            onClick={batchSettle}
            disabled={pendingCount === 0}
            className="btn btn-success flex items-center gap-1 disabled:opacity-50"
          >
            <CheckCircle size={16} /> 批量结算 ({pendingCount})
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>记录ID</th>
              <th>代理商</th>
              <th>订单号</th>
              <th>佣金金额</th>
              <th>状态</th>
              <th>时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {records.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td>{r.agent}</td>
                <td>{r.orderId}</td>
                <td className="text-warning font-medium">¥{formatMoney(r.amount)}</td>
                <td>
                  <span className={`badge ${statusBadge(r.status)}`}>{statusText(r.status)}</span>
                </td>
                <td className="text-text-secondary">{r.createdAt}</td>
                <td>
                  {r.status === 'pending' ? (
                    <button onClick={() => settleOne(r.id)} className="btn btn-success py-1 px-2 text-xs">
                      结算
                    </button>
                  ) : (
                    <span className="text-text-secondary text-xs">已结算</span>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
