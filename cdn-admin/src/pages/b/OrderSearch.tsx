import { useState, type ReactNode } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { formatMoney, orderStatusText } from '../../utils/helpers';
import { Search, KeyRound, User, CreditCard, FileText } from 'lucide-react';

interface OrderDetail {
  id: string;
  product: string;
  amount: number;
  status: 'pending' | 'paid' | 'cancelled' | 'refunded';
  createdAt: string;
  paidAt: string;
  buyer: { nickname: string; phone: string; email: string };
  payment: { method: string; tradeNo: string; amount: number };
  cards: { content: string }[];
}

const mockOrder: OrderDetail = {
  id: 'O202607100001',
  product: 'VPN月卡',
  amount: 29.0,
  status: 'paid',
  createdAt: '2026-07-10 10:23',
  paidAt: '2026-07-10 10:25',
  buyer: { nickname: 'user_9527', phone: '138****1234', email: '9527@example.com' },
  payment: { method: '支付宝', tradeNo: '2026071022001401234567890', amount: 29.0 },
  cards: [
    { content: 'VPN-MONTH-ABCD-1234-EFGH' },
    { content: 'VPN-MONTH-IJKL-5678-MNOP' },
  ],
};

function Row({ label, value, valueClass }: { label: string; value: ReactNode; valueClass?: string }) {
  return (
    <div className="flex items-center justify-between">
      <span className="text-text-secondary">{label}</span>
      <span className={valueClass}>{value}</span>
    </div>
  );
}

export default function OrderSearch() {
  const { show } = useToast();
  const [keyword, setKeyword] = useState('');
  const [result, setResult] = useState<OrderDetail | null>(null);
  const [searched, setSearched] = useState(false);
  const [loading, setLoading] = useState(false);

  const search = () => {
    if (!keyword.trim()) {
      show('请输入订单号', 'warning');
      return;
    }
    setLoading(true);
    setTimeout(() => {
      setResult(keyword.trim() === mockOrder.id ? mockOrder : null);
      setSearched(true);
      setLoading(false);
    }, 400);
  };

  return (
    <div>
      <PageHeader title="查单" breadcrumb={['订单管理', '查单']} />

      <div className="card p-5 mb-5">
        <div className="flex gap-3">
          <div className="relative flex-1">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              onKeyDown={(e) => e.key === 'Enter' && search()}
              placeholder="请输入订单号，例如 O202607100001"
              className="input pl-10 text-base py-2.5"
            />
          </div>
          <button onClick={search} className="btn btn-primary px-6">
            查询
          </button>
        </div>
      </div>

      {loading && <div className="card p-8 text-center text-sm text-text-secondary">查询中...</div>}

      {!loading && result && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <FileText size={16} className="text-primary" /> 订单详情
            </h3>
            <div className="space-y-2.5 text-sm">
              <Row label="订单号" value={result.id} />
              <Row label="商品" value={result.product} />
              <Row label="金额" value={`¥${formatMoney(result.amount)}`} valueClass="text-primary font-semibold" />
              <Row
                label="状态"
                value={
                  <span className={`badge ${result.status === 'paid' ? 'badge-success' : 'badge-warning'}`}>
                    {orderStatusText(result.status)}
                  </span>
                }
              />
              <Row label="下单时间" value={result.createdAt} />
              <Row label="支付时间" value={result.paidAt} />
            </div>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <User size={16} className="text-primary" /> 买家信息
            </h3>
            <div className="space-y-2.5 text-sm">
              <Row label="昵称" value={result.buyer.nickname} />
              <Row label="手机" value={result.buyer.phone} />
              <Row label="邮箱" value={result.buyer.email} />
            </div>
            <h3 className="font-semibold mt-5 mb-4 flex items-center gap-2">
              <CreditCard size={16} className="text-primary" /> 支付信息
            </h3>
            <div className="space-y-2.5 text-sm">
              <Row label="支付方式" value={result.payment.method} />
              <Row label="交易号" value={result.payment.tradeNo} />
              <Row label="支付金额" value={`¥${formatMoney(result.payment.amount)}`} />
            </div>
          </div>

          <div className="card p-5 lg:col-span-2">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <KeyRound size={16} className="text-primary" /> 已发卡密
            </h3>
            <div className="space-y-2">
              {result.cards.map((c, i) => (
                <div key={i} className="flex items-center justify-between p-3 bg-gray-50 rounded font-mono text-sm">
                  <span>{c.content}</span>
                  <button
                    onClick={() => {
                      navigator.clipboard?.writeText(c.content);
                      show('卡密已复制', 'success');
                    }}
                    className="text-primary text-xs"
                  >
                    复制
                  </button>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {!loading && searched && !result && (
        <div className="card p-8 text-center text-sm text-text-secondary">未找到订单「{keyword}」</div>
      )}
    </div>
  );
}
