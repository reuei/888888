import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import LineChart from '../../components/LineChart';
import { dailyStats } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Download, Calendar } from 'lucide-react';

export default function TransactionStats() {
  const [startDate, setStartDate] = useState('2026-06-22');
  const [endDate, setEndDate] = useState('2026-06-28');

  const totalRevenue = dailyStats.reduce((sum, d) => sum + d.revenue, 0);
  const totalOrders = dailyStats.reduce((sum, d) => sum + d.orders, 0);
  const today = dailyStats[dailyStats.length - 1];

  const statCards = [
    { title: '总交易额', value: `¥${formatMoney(totalRevenue)}`, color: 'text-primary' },
    { title: '今日交易额', value: `¥${formatMoney(today.revenue)}`, color: 'text-success' },
    { title: '总订单数', value: `${totalOrders.toLocaleString('zh-CN')}`, color: 'text-warning' },
    { title: '今日订单数', value: `${today.orders.toLocaleString('zh-CN')}`, color: 'text-danger' },
  ];

  const handleExport = () => {
    alert(`导出 ${startDate} 至 ${endDate} 的交易统计数据`);
  };

  return (
    <div>
      <PageHeader
        title="交易统计"
        breadcrumb={['数据报表', '交易统计']}
        actions={
          <button onClick={handleExport} className="btn btn-primary flex items-center gap-1">
            <Download size={16} /> 导出
          </button>
        }
      />

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {statCards.map((s, i) => (
          <div key={i} className="card p-5">
            <div className="text-sm text-text-secondary mb-2">{s.title}</div>
            <div className={`text-2xl font-bold tracking-tight ${s.color}`}>{s.value}</div>
          </div>
        ))}
      </div>

      <div className="card p-5 mb-6">
        <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
          <h3 className="font-semibold">每日交易额与订单趋势</h3>
          <div className="flex items-center gap-2 flex-wrap">
            <div className="flex items-center gap-1 text-sm text-text-secondary">
              <Calendar size={14} />
              <span>开始</span>
            </div>
            <input
              type="date"
              value={startDate}
              onChange={(e) => setStartDate(e.target.value)}
              className="input w-auto"
            />
            <span className="text-text-secondary">-</span>
            <input
              type="date"
              value={endDate}
              onChange={(e) => setEndDate(e.target.value)}
              className="input w-auto"
            />
          </div>
        </div>
        <LineChart
          labels={dailyStats.map((d) => d.date)}
          datasets={[
            { label: '交易额（元）', values: dailyStats.map((d) => d.revenue), color: '#2196F3' },
            { label: '订单数（笔）', values: dailyStats.map((d) => d.orders), color: '#4CAF50' },
          ]}
        />
      </div>

      <div className="card p-5">
        <h3 className="font-semibold mb-4">每日交易明细</h3>
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>日期</th>
                <th>交易额</th>
                <th>订单数</th>
                <th>新增用户</th>
                <th>新增商户</th>
              </tr>
            </thead>
            <tbody>
              {dailyStats.map((d) => (
                <tr key={d.date}>
                  <td className="font-medium">{d.date}</td>
                  <td>¥{formatMoney(d.revenue)}</td>
                  <td>{d.orders}</td>
                  <td>{d.users}</td>
                  <td>{d.merchants}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
        <div className="flex items-center justify-between mt-4 text-sm text-text-secondary">
          <div>共 {dailyStats.length} 条</div>
          <div className="flex items-center gap-2">
            <button className="btn btn-default text-xs">上一页</button>
            <button className="btn btn-primary text-xs">1</button>
            <button className="btn btn-default text-xs">下一页</button>
          </div>
        </div>
      </div>
    </div>
  );
}
