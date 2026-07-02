import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import StatCard from '../../components/StatCard';
import LineChart from '../../components/LineChart';
import { sStats, trendLabels, trendValues, merchantRank } from '../../data/mock';
import { Search } from 'lucide-react';

export default function SDashboard() {
  const { show } = useToast();
  return (
    <div>
      <PageHeader title="数据大屏" breadcrumb={['仪表盘', '数据大屏']} />

      {/* Top stats */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        {sStats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      {/* Middle section */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div className="card p-5 lg:col-span-2">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold">近 7 天 / 30 天交易趋势</h3>
            <div className="flex gap-2">
              <button onClick={() => show('已切换近7天数据', 'info')} className="btn btn-primary text-xs">近7天</button>
              <button onClick={() => show('已切换近30天数据', 'info')} className="btn btn-default text-xs">近30天</button>
            </div>
          </div>
          <LineChart
            labels={trendLabels}
            datasets={[{ label: '交易额（千元）', values: trendValues, color: '#2196F3' }]}
          />
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4">商家交易额排行榜 TOP10</h3>
          <div className="space-y-3">
            {merchantRank.map((m, i) => (
              <div key={i} className="flex items-center gap-3">
                <div
                  className={`w-5 h-5 rounded flex items-center justify-center text-xs text-white ${
                    i < 3 ? 'bg-primary' : 'bg-gray-300'
                  }`}
                >
                  {i + 1}
                </div>
                <div className="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs text-text-secondary">
                  {m.name[0]}
                </div>
                <div className="flex-1 min-w-0">
                  <div className="text-sm truncate">{m.name}</div>
                </div>
                <div className="text-sm font-medium">¥{(m.amount / 10000).toFixed(2)}万</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Bottom section */}
      <div className="card p-5">
        <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
          <h3 className="font-semibold">快捷订单查询</h3>
          <div className="flex gap-2">
            <button className="btn btn-default text-xs">按日</button>
            <button className="btn btn-default text-xs">按周</button>
            <button className="btn btn-primary text-xs">按月</button>
          </div>
        </div>
        <div className="flex gap-2 max-w-xl">
          <div className="relative flex-1">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" placeholder="输入订单号精确检索，回车直达详情" className="input pl-8" />
          </div>
          <button onClick={() => show('订单搜索功能已触发', 'info')} className="btn btn-primary">搜索</button>
        </div>
      </div>
    </div>
  );
}
