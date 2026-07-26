import PageHeader from '../../components/PageHeader';
import StatCard from '../../components/StatCard';
import { useToast } from '../../hooks/useToast';
import { ShieldAlert, Lock, AlertTriangle } from 'lucide-react';
import type { StatCardData } from '../../types';

interface RiskEvent {
  id: string;
  type: 'intercept' | 'lock' | 'abnormal';
  target: string;
  desc: string;
  amount: number;
  time: string;
}

const riskEvents: RiskEvent[] = [
  { id: 'R001', type: 'intercept', target: 'user_9527', desc: '同IP高频下单，触发拦截', amount: 299.0, time: '2026-07-26 10:23' },
  { id: 'R002', type: 'lock', target: 'O202607100002', desc: '随机金额校验失败，订单锁定', amount: 95.0, time: '2026-07-26 09:45' },
  { id: 'R003', type: 'abnormal', target: 'user_3344', desc: '30分钟内支付5笔异常订单', amount: 1280.0, time: '2026-07-26 08:30' },
  { id: 'R004', type: 'intercept', target: 'user_7788', desc: '黑名单设备指纹命中', amount: 0, time: '2026-07-26 08:12' },
  { id: 'R005', type: 'lock', target: 'O202607100001', desc: '随机金额超时未支付，订单锁定', amount: 29.0, time: '2026-07-26 07:55' },
  { id: 'R006', type: 'abnormal', target: 'user_1122', desc: '退款率超过阈值50%', amount: 0, time: '2026-07-26 07:20' },
  { id: 'R007', type: 'intercept', target: 'user_5566', desc: '风控规则：金额超过单笔上限5000', amount: 5200.0, time: '2026-07-26 06:48' },
  { id: 'R008', type: 'lock', target: 'O202607090008', desc: '随机金额校验失败，订单锁定', amount: 480.0, time: '2026-07-26 06:10' },
];

export default function SRiskMonitor() {
  const { show } = useToast();

  const stats: StatCardData[] = [
    { title: '今日拦截', value: '12', unit: '次', color: 'danger' },
    { title: '随机金额锁定', value: '8', unit: '笔', color: 'warning' },
    { title: '异常订单', value: '3', unit: '笔', color: 'primary' },
  ];

  const badgeInfo = (t: string) => {
    switch (t) {
      case 'intercept':
        return { cls: 'badge-danger', text: '拦截', icon: <ShieldAlert size={12} className="mr-1" /> };
      case 'lock':
        return { cls: 'badge-warning', text: '锁定', icon: <Lock size={12} className="mr-1" /> };
      case 'abnormal':
        return { cls: 'badge-info', text: '异常', icon: <AlertTriangle size={12} className="mr-1" /> };
      default:
        return { cls: 'badge-default', text: t, icon: null };
    }
  };

  return (
    <div>
      <PageHeader title="风控大屏" breadcrumb={['数据统计与日志', '风控大屏']} />

      <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        {stats.map((s, i) => (
          <StatCard key={i} data={s} />
        ))}
      </div>

      <div className="card p-5">
        <div className="flex items-center justify-between mb-4">
          <h3 className="font-semibold">最近风控事件</h3>
          <button
            onClick={() => show('已导出今日风控事件', 'success')}
            className="btn btn-default text-xs"
          >
            导出报表
          </button>
        </div>
        <table className="table">
          <thead>
            <tr>
              <th>事件ID</th>
              <th>类型</th>
              <th>目标</th>
              <th>描述</th>
              <th>涉及金额</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            {riskEvents.map((r) => {
              const info = badgeInfo(r.type);
              return (
                <tr key={r.id}>
                  <td className="text-text-secondary">{r.id}</td>
                  <td>
                    <span className={`badge ${info.cls} flex items-center w-fit`}>
                      {info.icon}
                      {info.text}
                    </span>
                  </td>
                  <td className="font-medium">{r.target}</td>
                  <td className="text-text-secondary">{r.desc}</td>
                  <td>{r.amount > 0 ? `¥${r.amount.toLocaleString()}` : '-'}</td>
                  <td className="text-text-secondary">{r.time}</td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
