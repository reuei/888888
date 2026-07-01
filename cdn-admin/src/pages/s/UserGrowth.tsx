import { useCallback, useEffect, useState } from 'react';
import PageHeader from '../../components/PageHeader';
import LineChart from '../../components/LineChart';
import { fetchUserGrowthStats } from '../../services/api';
import { Users, UserPlus, Activity, CreditCard } from 'lucide-react';
import type { UserGrowthStat } from '../../types';

export default function UserGrowth() {
  const [period, setPeriod] = useState<'7d' | '30d' | '90d'>('7d');
  const [userGrowthStats, setUserGrowthStats] = useState<UserGrowthStat[]>([]);
  const [loading, setLoading] = useState(false);

  const loadUserGrowthStats = useCallback(async () => {
    setLoading(true);
    try {
      const data = await fetchUserGrowthStats();
      setUserGrowthStats(data);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    loadUserGrowthStats();
  }, [loadUserGrowthStats]);

  const totalNewUsers = userGrowthStats.reduce((sum, d) => sum + d.newUsers, 0);
  const latest = userGrowthStats[userGrowthStats.length - 1] ?? { newUsers: 0, activeUsers: 0, paidUsers: 0 };
  const avgActive = userGrowthStats.length
    ? Math.round(userGrowthStats.reduce((sum, d) => sum + d.activeUsers, 0) / userGrowthStats.length)
    : 0;
  const avgPaid = userGrowthStats.length
    ? Math.round(userGrowthStats.reduce((sum, d) => sum + d.paidUsers, 0) / userGrowthStats.length)
    : 0;
  const avgPaidRate = avgActive ? (avgPaid / avgActive) * 100 : 0;

  const statCards = [
    { title: '总用户数', value: '56,832', unit: '人', icon: Users, color: 'text-primary' },
    { title: '今日新增', value: `+${latest.newUsers}`, unit: '人', icon: UserPlus, color: 'text-success' },
    { title: '活跃用户数', value: `${latest.activeUsers.toLocaleString('zh-CN')}`, unit: '人', icon: Activity, color: 'text-warning' },
    { title: '付费用户数', value: `${latest.paidUsers.toLocaleString('zh-CN')}`, unit: '人', icon: CreditCard, color: 'text-danger' },
  ];

  return (
    <div>
      <PageHeader title="用户增长" breadcrumb={['数据报表', '用户增长']} />

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

      <div className="card p-5 mb-6">
        <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
          <h3 className="font-semibold">用户增长趋势</h3>
          <div className="flex gap-2">
            {[
              { key: '7d', label: '近7天' },
              { key: '30d', label: '近30天' },
              { key: '90d', label: '近90天' },
            ].map((t) => (
              <button
                key={t.key}
                onClick={() => setPeriod(t.key as '7d' | '30d' | '90d')}
                className={`btn text-xs ${period === t.key ? 'btn-primary' : 'btn-default'}`}
              >
                {t.label}
              </button>
            ))}
          </div>
        </div>
        {loading ? (
          <div className="text-center py-12 text-text-secondary">加载中...</div>
        ) : (
          <LineChart
            labels={userGrowthStats.map((d) => d.date)}
            datasets={[
              { label: '新增用户', values: userGrowthStats.map((d) => d.newUsers), color: '#2196F3' },
              { label: '活跃用户', values: userGrowthStats.map((d) => d.activeUsers), color: '#4CAF50' },
              { label: '付费用户', values: userGrowthStats.map((d) => d.paidUsers), color: '#FF9800' },
            ]}
          />
        )}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="card p-5 lg:col-span-2">
          <h3 className="font-semibold mb-4">用户增长明细</h3>
          {loading ? (
            <div className="text-center py-12 text-text-secondary">加载中...</div>
          ) : (
            <div className="table-responsive">
              <table className="table">
                <thead>
                  <tr>
                    <th>日期</th>
                    <th>新增用户</th>
                    <th>活跃用户</th>
                    <th>付费用户</th>
                    <th>付费率</th>
                  </tr>
                </thead>
                <tbody>
                  {userGrowthStats.map((d) => {
                    const paidRate = (d.paidUsers / d.activeUsers) * 100;
                    return (
                      <tr key={d.date}>
                        <td className="font-medium">{d.date}</td>
                        <td className="text-primary">+{d.newUsers}</td>
                        <td>{d.activeUsers.toLocaleString('zh-CN')}</td>
                        <td>{d.paidUsers.toLocaleString('zh-CN')}</td>
                        <td>
                          <span className="badge badge-success">{paidRate.toFixed(2)}%</span>
                        </td>
                      </tr>
                    );
                  })}
                </tbody>
              </table>
            </div>
          )}
        </div>

        <div className="card p-5">
          <h3 className="font-semibold mb-4">增长概览</h3>
          <div className="space-y-4 text-sm">
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">统计周期</span>
              <span className="font-medium">{userGrowthStats.length} 天</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">周期新增用户</span>
              <span className="font-medium text-success">+{totalNewUsers}</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">平均活跃用户</span>
              <span className="font-medium">{avgActive.toLocaleString('zh-CN')}</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">平均付费用户</span>
              <span className="font-medium">{avgPaid.toLocaleString('zh-CN')}</span>
            </div>
            <div className="flex items-center justify-between p-3 border border-border rounded">
              <span className="text-text-secondary">平均付费率</span>
              <span className="font-medium text-primary">{avgPaidRate.toFixed(2)}%</span>
            </div>
            <p className="text-text-secondary leading-relaxed mt-2">
              近 {userGrowthStats.length} 天平台共新增用户 {totalNewUsers} 人，活跃用户稳步增长，付费转化率保持在
              {avgPaidRate.toFixed(2)}% 左右，整体增长态势良好。
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
