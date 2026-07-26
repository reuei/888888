import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import { useToast } from '../../hooks/useToast';
import { stockAlerts } from '../../data/mock';
import { Bell } from 'lucide-react';
import type { StatCardData } from '../../types';

export default function SStockAlerts() {
  const { show } = useToast();

  const alertingCount = stockAlerts.filter((s) => s.status === 'alerting').length;

  const stats: StatCardData[] = [
    { title: '预警中商品', value: String(alertingCount), unit: '个', color: 'danger' },
    { title: '已处理预警', value: String(stockAlerts.length - alertingCount), unit: '条', color: 'success' },
    { title: '预警总数', value: String(stockAlerts.length), unit: '条', color: 'warning' },
  ];

  const handleNotify = (productName: string, merchantName: string) => {
    show(`已通知商户「${merchantName}」补充商品「${productName}」库存`, 'success');
  };

  return (
    <div>
      <PageHeader title="库存预警" breadcrumb={['商品管理', '库存预警']} />

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {stats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>商品ID</th>
              <th>商品名</th>
              <th>预警阈值</th>
              <th>当前库存</th>
              <th>商户</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {stockAlerts.map((s) => (
              <tr key={s.id}>
                <td className="text-text-secondary">{s.productId}</td>
                <td className="font-medium">{s.productName}</td>
                <td>{s.threshold}</td>
                <td>
                  <span className={s.currentStock < s.threshold ? 'text-danger font-semibold' : ''}>
                    {s.currentStock}
                  </span>
                </td>
                <td>{s.merchantName}</td>
                <td>
                  <span className={`badge ${s.status === 'alerting' ? 'badge-danger' : 'badge-success'}`}>
                    {s.status === 'alerting' ? '预警中' : '已恢复'}
                  </span>
                </td>
                <td>
                  <button
                    onClick={() => handleNotify(s.productName, s.merchantName)}
                    className="btn btn-default text-xs flex items-center gap-1"
                    disabled={s.status !== 'alerting'}
                  >
                    <Bell size={14} /> 通知商户
                  </button>
                </td>
              </tr>
            ))}
            {stockAlerts.length === 0 && (
              <tr>
                <td colSpan={7}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无库存预警</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
