import PageHeader from '../../components/PageHeader';
import { formatMoney } from '../../utils/helpers';

interface RankItem {
  rank: number;
  product: string;
  amount: number;
  orders: number;
}

const mockRank: RankItem[] = [
  { rank: 1, product: 'VPN月卡', amount: 48290.5, orders: 5230 },
  { rank: 2, product: '游戏点券100元', amount: 39120.0, orders: 412 },
  { rank: 3, product: 'Steam充值卡', amount: 28450.0, orders: 95 },
  { rank: 4, product: '话费充值50元', amount: 19830.0, orders: 403 },
  { rank: 5, product: 'Netflix会员月卡', amount: 16500.0, orders: 423 },
  { rank: 6, product: 'Spotify会员季卡', amount: 14280.0, orders: 180 },
  { rank: 7, product: 'ChatGPT Plus月卡', amount: 12800.0, orders: 256 },
];

export default function FlowRank() {
  const total = mockRank.reduce((s, r) => s + r.amount, 0);

  return (
    <div>
      <PageHeader title="流水排行" breadcrumb={['资金管理', '流水排行']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>排名</th>
              <th>商品</th>
              <th>交易额</th>
              <th>订单数</th>
              <th>占比</th>
            </tr>
          </thead>
          <tbody>
            {mockRank.map((r) => {
              const pct = total > 0 ? (r.amount / total) * 100 : 0;
              return (
                <tr key={r.rank}>
                  <td>
                    <span
                      className={`inline-flex w-6 h-6 rounded-full items-center justify-center text-xs font-medium ${
                        r.rank <= 3 ? 'bg-primary text-white' : 'bg-gray-100 text-text-secondary'
                      }`}
                    >
                      {r.rank}
                    </span>
                  </td>
                  <td className="font-medium">{r.product}</td>
                  <td className="text-primary font-semibold">¥{formatMoney(r.amount)}</td>
                  <td className="text-text-secondary">{r.orders}</td>
                  <td>
                    <div className="flex items-center gap-2">
                      <div className="flex-1 h-2 bg-gray-100 rounded overflow-hidden min-w-[100px]">
                        <div className="h-full bg-primary" style={{ width: `${pct.toFixed(1)}%` }} />
                      </div>
                      <span className="text-xs text-text-secondary w-12 text-right">{pct.toFixed(1)}%</span>
                    </div>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
