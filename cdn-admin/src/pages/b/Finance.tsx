import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import { usePagination } from '../../hooks/usePagination';
import { fetchSettlementRecords } from '../../services/api';
import { formatMoney } from '../../utils/helpers';
import { Receipt } from 'lucide-react';
import type { StatCardData, SettlementRecord } from '../../types';

const stats: StatCardData[] = [
  { title: '今日交易额', value: '12,847.50', unit: '元', sub: '昨日 10,234.00', color: 'primary' },
  { title: '待结算', value: '8,450.00', unit: '元', sub: 'T+1 结算周期', color: 'warning' },
  { title: '已结算', value: '156,920.50', unit: '元', sub: '本月累计', color: 'success' },
  { title: '本月流水', value: '348,210.80', unit: '元', sub: '环比 +12.5%', color: 'primary' },
];

export default function BFinance() {
  const [records, setRecords] = useState<SettlementRecord[]>([]);
  const [loading, setLoading] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchSettlementRecords();
    setRecords(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const pagination = usePagination({ total: records.length });

  return (
    <div>
      <PageHeader title="结算记录" breadcrumb={['资金管理', '结算记录']} />

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
              <th>状态</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            {pagination.slice(records).map((s) => (
              <tr key={s.id}>
                <td>{s.cycle}</td>
                <td className="font-medium">¥{formatMoney(s.amount)}</td>
                <td className="text-text-secondary">¥{formatMoney(s.fee)}</td>
                <td className="text-success font-medium">¥{formatMoney(s.netAmount || (s.amount - s.fee))}</td>
                <td>
                  <span className={`badge ${s.status === 'settled' ? 'badge-success' : 'badge-warning'}`}>
                    {s.status === 'settled' ? '已结算' : '待结算'}
                  </span>
                </td>
                <td className="text-text-secondary">{s.time}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && records.length === 0 && (
          <EmptyState title="暂无结算记录" description="当前没有结算打款记录" icon={<Receipt size={24} />} />
        )}

        {!loading && records.length > 0 && (
          <Pagination
            page={pagination.page}
            totalPages={pagination.totalPages}
            total={records.length}
            pageSize={pagination.pageSize}
            onChange={pagination.setPage}
          />
        )}
      </div>
    </div>
  );
}
