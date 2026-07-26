import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import {
  ChevronLeft,
  ChevronRight,
  Copy,
  Check,
  ShieldCheck,
  Clock,
  Award,
  UserCircle,
} from 'lucide-react';

interface OrderRow {
  id: string;
  product: string;
  amount: number;
  status: 'paid' | 'pending' | 'closed';
  createdAt: string;
  secret?: string;
}

const userInfo = {
  nickname: 'user_9527',
  avatar: 'U',
  level: 'VIP2',
  phone: '138****1234',
  email: 'user***@example.com',
  realname: '已认证',
  balance: 0,
  totalOrders: 18,
  totalSpent: 1280.5,
};

const orderHistory: OrderRow[] = [
  { id: 'O20260726001', product: '腾讯视频VIP月卡', amount: 15.0, status: 'paid', createdAt: '2026-07-26 14:32', secret: 'TXSP-VIP-M8K2-9NXQ-7P4R' },
  { id: 'O20260725008', product: 'Steam钱包50美元充值卡', amount: 358.0, status: 'pending', createdAt: '2026-07-25 10:15' },
  { id: 'O20260724002', product: '话费充值100元', amount: 99.8, status: 'paid', createdAt: '2026-07-24 18:20', secret: 'HF100-7K2N-9PXM-2RQ8' },
  { id: 'O20260720005', product: '京东E卡 100元', amount: 99.5, status: 'paid', createdAt: '2026-07-20 09:45', secret: 'JD-E100-X8K2-9NXQ-7P4R' },
  { id: 'O20260718003', product: '美团外卖红包20元', amount: 12.0, status: 'closed', createdAt: '2026-07-18 12:10' },
];

const tabs = [
  { key: 'orders', label: '订单记录' },
  { key: 'secrets', label: '卡密查看' },
  { key: 'realname', label: '实名认证' },
];

const statusMap = {
  paid: { label: '已支付', color: 'text-[#22C55E] bg-[#22C55E]/10' },
  pending: { label: '待支付', color: 'text-[#F97316] bg-[#F97316]/10' },
  closed: { label: '已关闭', color: 'text-[#7C2D12]/60 bg-[#7C2D12]/10' },
};

export default function UserCenter() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState('orders');
  const [copiedId, setCopiedId] = useState<string | null>(null);

  const handleCopy = (text: string, id: string) => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(text).catch(() => {});
    }
    setCopiedId(id);
    setTimeout(() => setCopiedId(null), 1500);
  };

  return (
    <div className="theme-c min-h-screen bg-[#FFF7ED]">
      {/* Header */}
      <header className="bg-white border-b border-[#FFE4D1] sticky top-0 z-10">
        <div className="max-w-3xl mx-auto px-4 py-3 flex items-center gap-3">
          <button onClick={() => navigate('/c/home')} className="text-[#7C2D12] hover:text-[#F97316]">
            <ChevronLeft size={20} />
          </button>
          <h1 className="font-semibold text-[#7C2D12]">用户中心</h1>
        </div>
      </header>

      <div className="max-w-3xl mx-auto px-4 py-4">
        {/* User info card */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-5 mb-4">
          <div className="flex items-center gap-4">
            <div className="w-16 h-16 rounded-full bg-[#F97316] text-white flex items-center justify-center text-2xl font-bold shrink-0">
              {userInfo.avatar}
            </div>
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-2 mb-1">
                <span className="font-bold text-[#7C2D12] truncate">{userInfo.nickname}</span>
                <span className="text-xs px-2 py-0.5 rounded bg-[#F97316] text-white flex items-center gap-1">
                  <Award size={12} /> {userInfo.level}
                </span>
              </div>
              <div className="text-xs text-[#7C2D12]/70">{userInfo.phone}</div>
              <div className="text-xs text-[#7C2D12]/70">{userInfo.email}</div>
            </div>
            <button
              onClick={() => navigate('/c/profile')}
              className="text-[#7C2D12]/60 hover:text-[#F97316]"
            >
              <ChevronRight size={18} />
            </button>
          </div>

          <div className="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-[#FFE4D1]">
            <div className="text-center">
              <div className="text-lg font-bold text-[#F97316]">{userInfo.totalOrders}</div>
              <div className="text-xs text-[#7C2D12]/70">总订单数</div>
            </div>
            <div className="text-center">
              <div className="text-lg font-bold text-[#F97316]">¥{userInfo.totalSpent.toFixed(2)}</div>
              <div className="text-xs text-[#7C2D12]/70">累计消费</div>
            </div>
            <div className="text-center">
              <div className="text-lg font-bold text-[#22C55E] flex items-center justify-center gap-1">
                <ShieldCheck size={16} />
              </div>
              <div className="text-xs text-[#7C2D12]/70">{userInfo.realname}</div>
            </div>
          </div>
        </div>

        {/* Tab navigation */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] mb-4">
          <div className="flex border-b border-[#FFE4D1]">
            {tabs.map((t) => (
              <button
                key={t.key}
                onClick={() => setActiveTab(t.key)}
                className={`flex-1 py-3 text-sm font-medium transition-colors ${
                  activeTab === t.key
                    ? 'text-[#F97316] border-b-2 border-[#F97316]'
                    : 'text-[#7C2D12]/70 hover:text-[#F97316]'
                }`}
              >
                {t.label}
              </button>
            ))}
          </div>

          {/* Tab content */}
          <div className="p-4">
            {activeTab === 'orders' && (
              <div className="overflow-x-auto">
                <table className="w-full text-sm">
                  <thead>
                    <tr className="text-[#7C2D12]/60 text-xs border-b border-[#FFE4D1]">
                      <th className="text-left py-2 px-2 font-medium">订单号</th>
                      <th className="text-left py-2 px-2 font-medium">商品</th>
                      <th className="text-right py-2 px-2 font-medium">金额</th>
                      <th className="text-center py-2 px-2 font-medium">状态</th>
                      <th className="text-left py-2 px-2 font-medium">时间</th>
                    </tr>
                  </thead>
                  <tbody>
                    {orderHistory.map((o) => (
                      <tr key={o.id} className="border-b border-[#FFE4D1]/60 last:border-0">
                        <td className="py-3 px-2 text-[#7C2D12] font-mono text-xs">{o.id}</td>
                        <td className="py-3 px-2 text-[#7C2D12]">{o.product}</td>
                        <td className="py-3 px-2 text-right text-[#F97316] font-medium">¥{o.amount.toFixed(2)}</td>
                        <td className="py-3 px-2 text-center">
                          <span className={`text-xs px-2 py-0.5 rounded ${statusMap[o.status].color}`}>
                            {statusMap[o.status].label}
                          </span>
                        </td>
                        <td className="py-3 px-2 text-[#7C2D12]/70 text-xs whitespace-nowrap">{o.createdAt}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            {activeTab === 'secrets' && (
              <div className="space-y-3">
                {orderHistory
                  .filter((o) => o.status === 'paid' && o.secret)
                  .map((o) => (
                    <div key={o.id} className="bg-[#FFF7ED] border border-[#FFE4D1] rounded-lg p-3">
                      <div className="flex items-center justify-between mb-2">
                        <span className="text-sm text-[#7C2D12]">{o.product}</span>
                        <span className="text-xs text-[#7C2D12]/60">{o.id}</span>
                      </div>
                      <div className="flex items-center justify-between gap-2">
                        <span className="font-mono text-sm text-[#7C2D12] bg-white border border-[#FFE4D1] rounded px-2 py-1 flex-1 break-all tracking-wider">
                          {o.secret}
                        </span>
                        <button
                          onClick={() => handleCopy(o.secret!, o.id)}
                          className="flex items-center gap-1 text-xs text-[#F97316] hover:text-[#EA580C] shrink-0"
                        >
                          {copiedId === o.id ? <Check size={14} /> : <Copy size={14} />}
                          {copiedId === o.id ? '已复制' : '复制'}
                        </button>
                      </div>
                    </div>
                  ))}
                {orderHistory.filter((o) => o.status === 'paid' && o.secret).length === 0 && (
                  <div className="text-center text-sm text-[#7C2D12]/60 py-6">暂无可用卡密</div>
                )}
              </div>
            )}

            {activeTab === 'realname' && (
              <div className="space-y-4">
                <div className="bg-[#FFF7ED] border border-[#FFE4D1] rounded-lg p-4 flex items-start gap-3">
                  <ShieldCheck size={20} className="text-[#22C55E] shrink-0 mt-0.5" />
                  <div className="flex-1">
                    <div className="text-sm font-medium text-[#7C2D12] mb-1">实名认证已通过</div>
                    <div className="text-xs text-[#7C2D12]/70 leading-relaxed">
                      完成实名认证后可享受更高额度的购买权限与更快的发货速度。
                    </div>
                  </div>
                </div>
                <div className="space-y-3 text-sm">
                  <div className="flex justify-between py-2 border-b border-[#FFE4D1]/60">
                    <span className="text-[#7C2D12]/60">真实姓名</span>
                    <span className="text-[#7C2D12]">张*</span>
                  </div>
                  <div className="flex justify-between py-2 border-b border-[#FFE4D1]/60">
                    <span className="text-[#7C2D12]/60">证件号码</span>
                    <span className="text-[#7C2D12]">11010119900101****</span>
                  </div>
                  <div className="flex justify-between py-2 border-b border-[#FFE4D1]/60">
                    <span className="text-[#7C2D12]/60">认证状态</span>
                    <span className="text-[#22C55E] flex items-center gap-1">
                      <ShieldCheck size={14} /> 已认证
                    </span>
                  </div>
                  <div className="flex justify-between py-2">
                    <span className="text-[#7C2D12]/60">认证时间</span>
                    <span className="text-[#7C2D12]">2026-06-20 10:00</span>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Quick actions */}
        <div className="grid grid-cols-2 gap-3">
          <button
            onClick={() => navigate('/c/order-query')}
            className="bg-white rounded-xl border border-[#FFE4D1] p-4 flex items-center gap-3 hover:border-[#F97316] transition-colors"
          >
            <div className="w-10 h-10 rounded-lg bg-[#FFF7ED] flex items-center justify-center text-[#F97316]">
              <Clock size={18} />
            </div>
            <div className="text-left">
              <div className="text-sm font-medium text-[#7C2D12]">订单查询</div>
              <div className="text-xs text-[#7C2D12]/60">查看订单状态和卡密</div>
            </div>
          </button>
          <button
            onClick={() => navigate('/c/profile')}
            className="bg-white rounded-xl border border-[#FFE4D1] p-4 flex items-center gap-3 hover:border-[#F97316] transition-colors"
          >
            <div className="w-10 h-10 rounded-lg bg-[#FFF7ED] flex items-center justify-center text-[#F97316]">
              <UserCircle size={18} />
            </div>
            <div className="text-left">
              <div className="text-sm font-medium text-[#7C2D12]">账户设置</div>
              <div className="text-xs text-[#7C2D12]/60">修改资料与安全设置</div>
            </div>
          </button>
        </div>
      </div>
    </div>
  );
}
