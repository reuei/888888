import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import { useToast } from '../../hooks/useToast';
import { Bell } from 'lucide-react';
import type { StatCardData } from '../../types';

interface PayRow {
  id: string;
  buyer: string;
  amount: number;
  method: string;
  time: string;
  status: 'pending' | 'notified';
}

const rows: PayRow[] = Array.from({ length: 28 }).map((_, i) => {
  const methods = ['支付宝', '微信支付', 'USDT-TRC20', '易支付'];
  const idx = i % 4;
  return {
    id: 'O2026072' + String(8000 + i).padStart(4, '0'),
    buyer: `user_${9000 + i}`,
    amount: 50 + i * 12.5,
    method: methods[idx],
    time: `2026-07-26 ${String(8 + (i % 12)).padStart(2, '0')}:${String((i * 7) % 60).padStart(2, '0')}`,
    status: 'pending',
  };
});

export default function SBatchPayment() {
  const { show } = useToast();
  const [list, setList] = useState(rows);
  const [selected, setSelected] = useState<string[]>([]);

  const pendingCount = list.filter((r) => r.status === 'pending').length;

  const stats: StatCardData[] = [
    { title: '待通知', value: String(pendingCount), unit: '笔', color: 'warning' },
    { title: '已通知', value: String(list.length - pendingCount), unit: '笔', color: 'success' },
    { title: '待通知总金额', value: list.filter((r) => r.status === 'pending').reduce((s, r) => s + r.amount, 0).toFixed(2), unit: '元', color: 'primary' },
  ];

  const toggle = (id: string) => {
    setSelected((prev) => (prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]));
  };

  const toggleAll = () => {
    const pendingIds = list.filter((r) => r.status === 'pending').map((r) => r.id);
    if (selected.length === pendingIds.length) {
      setSelected([]);
    } else {
      setSelected(pendingIds);
    }
  };

  const handleBatchNotify = () => {
    if (selected.length === 0) {
      show('请先勾选要通知的订单', 'warning');
      return;
    }
    setList(list.map((r) => (selected.includes(r.id) ? { ...r, status: 'notified' } : r)));
    show(`已批量通知 ${selected.length} 笔订单的付款信息`, 'success');
    setSelected([]);
  };

  const pendingIds = list.filter((r) => r.status === 'pending').map((r) => r.id);
  const allChecked = pendingIds.length > 0 && selected.length === pendingIds.length;

  return (
    <div>
      <PageHeader title="批量付款通知" breadcrumb={['订单管理', '批量付款通知']} />

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {stats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5">
        <div className="flex items-center justify-between mb-4">
          <div className="text-sm text-text-secondary">
            已选择 <span className="text-primary font-semibold">{selected.length}</span> 笔
          </div>
          <button
            onClick={handleBatchNotify}
            className="btn btn-primary flex items-center gap-1"
            disabled={selected.length === 0}
          >
            <Bell size={16} /> 批量通知
          </button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th className="w-10">
                <input
                  type="checkbox"
                  checked={allChecked}
                  onChange={toggleAll}
                  className="w-4 h-4"
                />
              </th>
              <th>订单号</th>
              <th>买家</th>
              <th>金额</th>
              <th>支付方式</th>
              <th>时间</th>
              <th>状态</th>
            </tr>
          </thead>
          <tbody>
            {list.map((r) => (
              <tr key={r.id}>
                <td>
                  <input
                    type="checkbox"
                    checked={selected.includes(r.id)}
                    onChange={() => toggle(r.id)}
                    disabled={r.status !== 'pending'}
                    className="w-4 h-4"
                  />
                </td>
                <td className="font-mono text-text-secondary">{r.id}</td>
                <td>{r.buyer}</td>
                <td className="font-medium">¥{r.amount.toFixed(2)}</td>
                <td>{r.method}</td>
                <td className="text-text-secondary">{r.time}</td>
                <td>
                  <span className={`badge ${r.status === 'pending' ? 'badge-warning' : 'badge-success'}`}>
                    {r.status === 'pending' ? '待通知' : '已通知'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
