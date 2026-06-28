import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { formatMoney } from '../../utils/helpers';
import { Medal } from 'lucide-react';

const rankData = [
  { id: 'U001', nickname: 'user_9527', amount: 128450.0, orders: 86, lastAt: '2026-06-28 10:23' },
  { id: 'U002', nickname: 'user_3344', amount: 96320.5, orders: 64, lastAt: '2026-06-28 09:45' },
  { id: 'U003', nickname: 'user_7788', amount: 72400.0, orders: 51, lastAt: '2026-06-27 22:10' },
  { id: 'U004', nickname: 'user_1122', amount: 56100.0, orders: 38, lastAt: '2026-06-27 18:33' },
  { id: 'U005', nickname: 'user_5566', amount: 42800.0, orders: 29, lastAt: '2026-06-26 15:20' },
  { id: 'U006', nickname: 'user_8899', amount: 31500.0, orders: 22, lastAt: '2026-06-26 11:05' },
];

export default function UserRank() {
  const [list] = useState(rankData);

  return (
    <div>
      <PageHeader title="用户流水排行" breadcrumb={['会员/用户管理', '用户流水排行']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>排名</th>
              <th>用户</th>
              <th>消费金额</th>
              <th>订单数</th>
              <th>最近消费时间</th>
            </tr>
          </thead>
          <tbody>
            {list.map((u, index) => (
              <tr key={u.id}>
                <td>
                  <div className="flex items-center gap-2">
                    {index < 3 && (
                      <Medal
                        size={16}
                        className={index === 0 ? 'text-warning' : index === 1 ? 'text-text-secondary' : 'text-warning'}
                      />
                    )}
                    <span className="font-medium">{index + 1}</span>
                  </div>
                </td>
                <td>
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                      {u.nickname.slice(0, 2).toUpperCase()}
                    </div>
                    <span className="font-medium">{u.nickname}</span>
                  </div>
                </td>
                <td className="font-medium text-primary">¥{formatMoney(u.amount)}</td>
                <td>{u.orders}</td>
                <td className="text-text-secondary">{u.lastAt}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
