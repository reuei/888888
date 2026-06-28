import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import LineChart from '../../components/LineChart';
import { bStats, trendLabels, bTrendValues1, bTrendValues2, bTrendValues3, bOrders } from '../../data/mock';
import { formatMoney, orderStatusText } from '../../utils/helpers';
import { AlertTriangle, ShoppingCart } from 'lucide-react';

export default function BDashboard() {
  return (
    <div>
      <PageHeader title="数据监控" breadcrumb={['仪表盘', '数据监控']} />

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {bStats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5 mb-6">
        <div className="flex items-center justify-between mb-4">
          <h3 className="font-semibold">访问量 / QFS / 流量趋势</h3>
          <div className="flex gap-2">
            <button className="btn btn-primary text-xs">近7天</button>
            <button className="btn btn-default text-xs">近30天</button>
          </div>
        </div>
        <LineChart
          labels={trendLabels}
          datasets={[
            { label: '访问量', values: bTrendValues1, color: '#2196F3' },
            { label: 'QFS', values: bTrendValues2, color: '#4CAF50' },
            { label: '流量(GB)', values: bTrendValues3, color: '#FF9800' },
          ]}
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div className="card p-5">
          <h3 className="font-semibold mb-4 flex items-center gap-2">
            <ShoppingCart size={18} className="text-primary" /> 最近订单
          </h3>
          <div className="space-y-3 text-sm">
            {bOrders.slice(0, 5).map((o) => (
              <div key={o.id} className="flex items-center justify-between border-b border-border pb-2 last:border-0">
                <div>
                  <div className="font-medium">{o.id}</div>
                  <div className="text-text-secondary text-xs">{o.product}</div>
                </div>
                <div className="text-right">
                  <div className="font-medium text-success">+¥{formatMoney(o.amount)}</div>
                  <div className="text-text-secondary text-xs">{orderStatusText(o.status)} · {o.createdAt.split(' ')[1]}</div>
                </div>
              </div>
            ))}
          </div>
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4 flex items-center gap-2">
            <AlertTriangle size={18} className="text-warning" /> 最近告警
          </h3>
          <div className="space-y-3 text-sm">
            {[
              { msg: '站点 game.xxx.com DDoS 攻击峰值 12Gbps', level: 'danger', time: '10分钟前' },
              { msg: '证书 api.xxx.com 将在 7 天后过期', level: 'warning', time: '2小时前' },
              { msg: '节点池 高防B 延迟超过 200ms', level: 'warning', time: '5小时前' },
            ].map((a, i) => (
              <div key={i} className="flex items-start gap-2 border-b border-border pb-2 last:border-0">
                <span className={`w-2 h-2 rounded-full mt-1.5 ${a.level === 'danger' ? 'bg-danger' : 'bg-warning'}`}></span>
                <div className="flex-1">
                  <div>{a.msg}</div>
                  <div className="text-text-secondary text-xs">{a.time}</div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
