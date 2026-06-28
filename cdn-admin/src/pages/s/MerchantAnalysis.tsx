import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { merchantStats } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Store, TrendingUp, DollarSign, ShoppingCart } from 'lucide-react';

export default function MerchantAnalysis() {
  const [sortBy, setSortBy] = useState<'revenue' | 'orders' | 'growth'>('revenue');

  const totalMerchants = merchantStats.length;
  const activeMerchants = merchantStats.filter((m) => m.growth >= 0).length;
  const totalRevenue = merchantStats.reduce((sum, m) => sum + m.revenue, 0);
  const totalOrders = merchantStats.reduce((sum, m) => sum + m.orders, 0);
  const avgOrderValue = totalRevenue / totalOrders;

  const sortedStats = [...merchantStats].sort((a, b) => {
    if (sortBy === 'revenue') return b.revenue - a.revenue;
    if (sortBy === 'orders') return b.orders - a.orders;
    return b.growth - a.growth;
  });

  const top5 = sortedStats.slice(0, 5);
  const maxTopRevenue = Math.max(...top5.map((m) => m.revenue), 1);

  const statCards = [
    { title: '总商户数', value: `${totalMerchants}`, unit: '家', icon: Store, color: 'text-primary' },
    { title: '活跃商户', value: `${activeMerchants}`, unit: '家', icon: TrendingUp, color: 'text-success' },
    { title: '总交易额', value: `¥${formatMoney(totalRevenue)}`, icon: DollarSign, color: 'text-warning' },
    { title: '平均客单价', value: `¥${formatMoney(avgOrderValue)}`, icon: ShoppingCart, color: 'text-danger' },
  ];

  return (
    <div>
      <PageHeader title="商户分析" breadcrumb={['数据报表', '商户分析']} />

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {statCards.map((s, i) => (
          <div key={i} className="card p-5">
            <div className="flex items-center gap-2 text-sm text-text-secondary mb-2">
              <s.icon size={16} />
              <span>{s.title}</span>
            </div>
            <div className={`text-2xl font-bold tracking-tight ${s.color}`}>
              {s.value}
              {s.unit && <span className="text-sm font-normal ml-1">{s.unit}</span>}
            </div>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div className="card p-5 lg:col-span-2">
          <h3 className="font-semibold mb-4">TOP5 商户交易额</h3>
          <div className="space-y-4">
            {top5.map((m, i) => (
              <div key={m.merchant} className="flex items-center gap-3">
                <div
                  className={`w-6 h-6 rounded flex items-center justify-center text-xs text-white ${
                    i < 3 ? 'bg-primary' : 'bg-gray-300'
                  }`}
                >
                  {i + 1}
                </div>
                <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs text-text-secondary">
                  {m.merchant[0]}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-sm truncate">{m.merchant}</span>
                    <span className="text-sm font-medium">¥{formatMoney(m.revenue)}</span>
                  </div>
                  <div className="w-full h-2 bg-gray-100 rounded overflow-hidden">
                    <div
                      className="h-full bg-primary"
                      style={{ width: `${(m.revenue / maxTopRevenue) * 100}%` }}
                    ></div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4">商户健康度</h3>
          <div className="space-y-4 text-sm">
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">活跃商户占比</span>
              <span className="font-medium text-success">
                {((activeMerchants / totalMerchants) * 100).toFixed(1)}%
              </span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">负增长商户</span>
              <span className="font-medium text-danger">{totalMerchants - activeMerchants} 家</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">平均客单价</span>
              <span className="font-medium">¥{formatMoney(avgOrderValue)}</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">总订单数</span>
              <span className="font-medium">{totalOrders.toLocaleString('zh-CN')} 笔</span>
            </div>
          </div>
        </div>
      </div>

      <div className="card p-5">
        <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
          <h3 className="font-semibold">商户交易排行</h3>
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value as 'revenue' | 'orders' | 'growth')}
            className="input w-auto"
          >
            <option value="revenue">按交易额排序</option>
            <option value="orders">按订单数排序</option>
            <option value="growth">按环比增长排序</option>
          </select>
        </div>
        <div className="table-responsive">
          <table className="table">
            <thead>
              <tr>
                <th>排名</th>
                <th>商户</th>
                <th>交易额</th>
                <th>订单数</th>
                <th>客单价</th>
                <th>环比增长</th>
              </tr>
            </thead>
            <tbody>
              {sortedStats.map((m, i) => (
                <tr key={m.merchant}>
                  <td>
                    <span
                      className={`inline-flex w-6 h-6 rounded items-center justify-center text-xs text-white ${
                        i < 3 ? 'bg-primary' : 'bg-gray-300'
                      }`}
                    >
                      {i + 1}
                    </span>
                  </td>
                  <td className="font-medium">{m.merchant}</td>
                  <td>¥{formatMoney(m.revenue)}</td>
                  <td>{m.orders.toLocaleString('zh-CN')}</td>
                  <td>¥{formatMoney(m.avgOrderValue)}</td>
                  <td>
                    <span className={`badge ${m.growth >= 0 ? 'badge-success' : 'badge-danger'}`}>
                      {m.growth >= 0 ? '+' : ''}
                      {m.growth}%
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
