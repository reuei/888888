import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { stations } from '../../data/mock';
import { TrendingUp, TrendingDown } from 'lucide-react';

export default function SStationMonitor() {
  const { show } = useToast();

  // Mock growth % per station (in real scenario would come from API)
  const growthMap: Record<string, number> = {
    ST01: 12.5,
    ST02: 8.3,
    ST03: -2.1,
  };

  const totalRevenue = stations.reduce((s, st) => s + st.revenue, 0);
  const totalOrders = stations.reduce((s, st) => s + st.orderCount, 0);
  const totalMerchants = stations.reduce((s, st) => s + st.merchantCount, 0);

  return (
    <div>
      <PageHeader title="分站运营监控" breadcrumb={['分站管理', '分站运营监控']} />

      {/* Summary row */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div className="card p-5 border-l-4 border-l-primary">
          <div className="text-sm text-text-secondary mb-2">全平台交易额</div>
          <div className="text-2xl font-bold">¥{totalRevenue.toLocaleString()}</div>
        </div>
        <div className="card p-5 border-l-4 border-l-success">
          <div className="text-sm text-text-secondary mb-2">全平台订单数</div>
          <div className="text-2xl font-bold">{totalOrders.toLocaleString()}</div>
        </div>
        <div className="card p-5 border-l-4 border-l-warning">
          <div className="text-sm text-text-secondary mb-2">全平台商户数</div>
          <div className="text-2xl font-bold">{totalMerchants.toLocaleString()}</div>
        </div>
      </div>

      {/* Per-station comparison cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {stations.map((st) => {
          const growth = growthMap[st.id] ?? 0;
          const isUp = growth >= 0;
          const revenueShare = totalRevenue > 0 ? (st.revenue / totalRevenue) * 100 : 0;
          return (
            <div
              key={st.id}
              className="card p-5 cursor-pointer hover:border-primary"
              onClick={() => show(`查看分站「${st.name}」详细运营数据`, 'info')}
            >
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <span
                    className="w-3 h-3 rounded-full"
                    style={{ backgroundColor: st.themeColor }}
                  />
                  <h3 className="font-semibold">{st.name}</h3>
                </div>
                <span
                  className={`badge ${st.status === 'active' ? 'badge-success' : 'badge-danger'}`}
                >
                  {st.status === 'active' ? '运行中' : '已停用'}
                </span>
              </div>

              <div className="space-y-3 text-sm">
                <div className="flex justify-between">
                  <span className="text-text-secondary">商户数</span>
                  <span className="font-medium">{st.merchantCount}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-text-secondary">订单数</span>
                  <span className="font-medium">{st.orderCount.toLocaleString()}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-text-secondary">交易额</span>
                  <span className="font-medium">¥{st.revenue.toLocaleString()}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-text-secondary">营收占比</span>
                  <span className="font-medium">{revenueShare.toFixed(1)}%</span>
                </div>
                <div className="flex justify-between items-center pt-2 border-t border-border">
                  <span className="text-text-secondary">环比增长</span>
                  <span
                    className={`flex items-center gap-1 font-semibold ${
                      isUp ? 'text-success' : 'text-danger'
                    }`}
                  >
                    {isUp ? <TrendingUp size={14} /> : <TrendingDown size={14} />}
                    {isUp ? '+' : ''}
                    {growth.toFixed(1)}%
                  </span>
                </div>
              </div>
            </div>
          );
        })}
      </div>
    </div>
  );
}
