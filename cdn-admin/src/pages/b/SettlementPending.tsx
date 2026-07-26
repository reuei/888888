import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import { fetchSettlementRecords } from '../../services/api';
import { formatMoney } from '../../utils/helpers';
import type { StatCardData, SettlementRecord } from '../../types';

export default function SettlementPending() {
  const [pending, setPending] = useState<SettlementRecord[]>([]);

  const load = useCallback(async () => {
    const data = await fetchSettlementRecords();
    setPending(data.filter((r) => r.status === 'pending'));
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const total = pending.reduce((sum, r) => sum + r.amount, 0);

  const stats: StatCardData[] = [
    { title: '待结算总额', value: formatMoney(total), unit: '元', sub: `${pending.length} 笔待结算`, color: 'warning' },
    { title: '最近结算日', value: '2026-07-28', sub: 'T+1 自动结算', color: 'primary' },
  ];

  return (
    <div>
      <PageHeader title="待结算" breadcrumb={['资金管理', '待结算']} />

      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        {stats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>结算周期</th>
              <th>总金额</th>
              <th>手续费</th>
              <th>到账金额</th>
              <th>预计结算时间</th>
            </tr>
          </thead>
          <tbody>
            {pending.map((r) => (
              <tr key={r.id}>
                <td>{r.cycle}</td>
                <td className="font-medium">¥{formatMoney(r.amount)}</td>
                <td className="text-text-secondary">¥{formatMoney(r.fee)}</td>
                <td className="text-success font-medium">¥{formatMoney(r.netAmount || (r.amount - r.fee))}</td>
                <td className="text-text-secondary">{r.time}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {pending.length === 0 && <div className="py-8 text-center text-sm text-text-secondary">暂无待结算记录</div>}
      </div>
    </div>
  );
}
