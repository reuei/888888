import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import { useToast } from '../../hooks/useToast';
import { stations } from '../../data/mock';
import { Plus, Eye, Edit, Ban } from 'lucide-react';
import type { StatCardData } from '../../types';

export default function SStations() {
  const { show } = useToast();

  const totalMerchants = stations.reduce((s, st) => s + st.merchantCount, 0);
  const totalOrders = stations.reduce((s, st) => s + st.orderCount, 0);
  const totalRevenue = stations.reduce((s, st) => s + st.revenue, 0);

  const stats: StatCardData[] = [
    { title: '分站数', value: String(stations.length), unit: '个', color: 'primary' },
    { title: '商户总数', value: totalMerchants.toLocaleString(), unit: '户', color: 'success' },
    { title: '订单总数', value: totalOrders.toLocaleString(), unit: '笔', color: 'warning' },
    { title: '交易总额', value: (totalRevenue / 1000).toFixed(0) + 'K', unit: '元', color: 'danger' },
  ];

  const handleSuspend = (name: string) => {
    show(`分站「${name}」已停用`, 'warning');
  };

  return (
    <div>
      <PageHeader
        title="分站列表"
        breadcrumb={['分站管理', '分站列表']}
        actions={
          <button onClick={() => show('跳转至新建分站页面', 'info')} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 新建分站
          </button>
        }
      />

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {stats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>分站名称</th>
              <th>域名</th>
              <th>主题色</th>
              <th>超管</th>
              <th>商户数</th>
              <th>订单数</th>
              <th>交易额</th>
              <th>状态</th>
              <th>创建时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {stations.map((st) => (
              <tr key={st.id}>
                <td className="font-medium">{st.name}</td>
                <td className="text-text-secondary">{st.domain}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <span
                      className="w-3 h-3 rounded-full border border-border"
                      style={{ backgroundColor: st.themeColor }}
                    />
                    <span className="text-text-secondary text-xs">{st.themeColor}</span>
                  </div>
                </td>
                <td>{st.superAdmin}</td>
                <td>{st.merchantCount}</td>
                <td>{st.orderCount.toLocaleString()}</td>
                <td>¥{st.revenue.toLocaleString()}</td>
                <td>
                  <span className={`badge ${st.status === 'active' ? 'badge-success' : 'badge-danger'}`}>
                    {st.status === 'active' ? '运行中' : '已停用'}
                  </span>
                </td>
                <td className="text-text-secondary">{st.createdAt}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => show(`查看分站「${st.name}」详情`, 'info')}
                      className="p-1.5 rounded hover:bg-gray-100 text-primary"
                      title="查看"
                    >
                      <Eye size={16} />
                    </button>
                    <button
                      onClick={() => show(`编辑分站「${st.name}」`, 'info')}
                      className="p-1.5 rounded hover:bg-gray-100 text-warning"
                      title="编辑"
                    >
                      <Edit size={16} />
                    </button>
                    {st.status === 'active' && (
                      <button
                        onClick={() => handleSuspend(st.name)}
                        className="p-1.5 rounded hover:bg-gray-100 text-danger"
                        title="停用"
                      >
                        <Ban size={16} />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
