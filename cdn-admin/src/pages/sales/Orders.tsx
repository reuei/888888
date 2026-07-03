import { ShoppingBag, Server, CheckCircle, Clock, XCircle } from 'lucide-react';

const orders = [
  { id: 'SO001', type: '源码授权', name: '商业授权', price: 29999, status: 'paid', date: '2026-06-15' },
  { id: 'SO002', type: 'CDN 节点', name: '标准节点包', price: 999, status: 'paid', date: '2026-06-20' },
  { id: 'SO003', type: 'CDN 节点', name: '高级节点包', price: 2499, status: 'pending', date: '2026-07-02' },
];

const statusMap: Record<string, { label: string; className: string; icon: typeof CheckCircle }> = {
  paid: { label: '已支付', className: 'text-[var(--sales-success)] bg-[var(--sales-success)]/10', icon: CheckCircle },
  pending: { label: '待支付', className: 'text-[var(--sales-warning)] bg-[var(--sales-warning)]/10', icon: Clock },
  cancelled: { label: '已取消', className: 'text-danger bg-danger/10', icon: XCircle },
};

export default function Orders() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center max-w-2xl mx-auto mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-4">
          <ShoppingBag size={14} />
          我的订单
        </div>
        <h1 className="text-3xl md:text-4xl font-bold mb-4">订单记录</h1>
        <p className="text-[var(--sales-text-secondary)]">查看所有源码授权与 CDN 节点购买记录。</p>
      </div>

      <div className="bg-[var(--sales-card)] border border-[var(--sales-border)] rounded-2xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b border-[var(--sales-border)] text-[var(--sales-text-secondary)]">
                <th className="text-left px-6 py-4 font-medium">订单号</th>
                <th className="text-left px-6 py-4 font-medium">类型</th>
                <th className="text-left px-6 py-4 font-medium">商品</th>
                <th className="text-left px-6 py-4 font-medium">金额</th>
                <th className="text-left px-6 py-4 font-medium">状态</th>
                <th className="text-left px-6 py-4 font-medium">下单时间</th>
              </tr>
            </thead>
            <tbody>
              {orders.map((o) => {
                const status = statusMap[o.status];
                const Icon = status.icon;
                return (
                  <tr key={o.id} className="border-b border-[var(--sales-border)] last:border-0 hover:bg-black/[0.02] dark:hover:bg-white/[0.02]">
                    <td className="px-6 py-4 font-mono">{o.id}</td>
                    <td className="px-6 py-4">
                      <span className="inline-flex items-center gap-1.5">
                        {o.type === '源码授权' ? <ShoppingBag size={14} /> : <Server size={14} />}
                        {o.type}
                      </span>
                    </td>
                    <td className="px-6 py-4">{o.name}</td>
                    <td className="px-6 py-4 font-medium">¥{o.price.toLocaleString()}</td>
                    <td className="px-6 py-4">
                      <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium ${status.className}`}>
                        <Icon size={12} />
                        {status.label}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-[var(--sales-text-secondary)]">{o.date}</td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}
