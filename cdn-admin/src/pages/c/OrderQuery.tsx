import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { ChevronLeft, Search, Copy, Check, AlertCircle, CheckCircle, Clock } from 'lucide-react';

interface OrderResult {
  orderId: string;
  product: string;
  amount: string;
  status: 'paid' | 'pending' | 'closed';
  createdAt: string;
  merchant: string;
  secrets: string[];
}

const orderDb: Record<string, OrderResult> = {
  O20260726001: {
    orderId: 'O20260726001',
    product: '腾讯视频VIP月卡',
    amount: '15.00',
    status: 'paid',
    createdAt: '2026-07-26 14:32:18',
    merchant: '极速云',
    secrets: ['TXSP-VIP-M8K2-9NXQ-7P4R'],
  },
  O20260725008: {
    orderId: 'O20260725008',
    product: 'Steam钱包50美元充值卡',
    amount: '358.00',
    status: 'pending',
    createdAt: '2026-07-25 10:15:00',
    merchant: '站点卫士',
    secrets: [],
  },
  O20260724002: {
    orderId: 'O20260724002',
    product: '话费充值100元',
    amount: '99.80',
    status: 'closed',
    createdAt: '2026-07-24 18:20:00',
    merchant: '极速云',
    secrets: [],
  },
};

const statusMap = {
  paid: { label: '已支付', color: 'text-[#22C55E]', icon: CheckCircle },
  pending: { label: '待支付', color: 'text-[#F97316]', icon: Clock },
  closed: { label: '已关闭', color: 'text-[#7C2D12]/60', icon: AlertCircle },
};

export default function OrderQuery() {
  const navigate = useNavigate();
  const [orderNo, setOrderNo] = useState('');
  const [result, setResult] = useState<OrderResult | null>(null);
  const [searched, setSearched] = useState(false);
  const [copiedIdx, setCopiedIdx] = useState<number | null>(null);

  const handleQuery = () => {
    setSearched(true);
    const trimmed = orderNo.trim();
    if (!trimmed) {
      setResult(null);
      return;
    }
    setResult(orderDb[trimmed] || null);
  };

  const handleCopy = (text: string, idx: number) => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(text).catch(() => {});
    }
    setCopiedIdx(idx);
    setTimeout(() => setCopiedIdx(null), 1500);
  };

  return (
    <div className="theme-c min-h-screen bg-[#FFF7ED]">
      {/* Header */}
      <header className="bg-white border-b border-[#FFE4D1] sticky top-0 z-10">
        <div className="max-w-2xl mx-auto px-4 py-3 flex items-center gap-3">
          <button onClick={() => navigate('/c/home')} className="text-[#7C2D12] hover:text-[#F97316]">
            <ChevronLeft size={20} />
          </button>
          <h1 className="font-semibold text-[#7C2D12]">订单查询</h1>
        </div>
      </header>

      <div className="max-w-2xl mx-auto px-4 py-6">
        {/* Search */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-5 mb-4">
          <label className="block text-sm font-medium text-[#7C2D12] mb-2">输入订单编号</label>
          <input
            className="w-full px-4 py-3 rounded-lg border border-[#FFE4D1] bg-[#FFF7ED] text-base text-[#7C2D12] outline-none focus:border-[#F97316] transition-colors"
            placeholder="请输入订单编号，例如 O20260726001"
            value={orderNo}
            onChange={(e) => setOrderNo(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && handleQuery()}
          />
          <button
            onClick={handleQuery}
            className="w-full mt-3 py-3 rounded-lg bg-[#F97316] text-white font-medium hover:bg-[#EA580C] transition-colors flex items-center justify-center gap-2"
          >
            <Search size={16} />
            查询
          </button>
          <div className="text-xs text-[#7C2D12]/60 mt-2">
            提示：可尝试 O20260726001（已支付）/ O20260725008（待支付）/ O20260724002（已关闭）
          </div>
        </div>

        {/* Results */}
        {searched && !result && (
          <div className="bg-white rounded-xl border border-[#FFE4D1] p-8 text-center">
            <AlertCircle size={40} className="mx-auto text-[#7C2D12]/40 mb-2" />
            <div className="text-sm text-[#7C2D12]/70">未找到对应订单，请检查订单编号是否正确</div>
          </div>
        )}

        {result && (
          <div className="space-y-4">
            {/* Status */}
            <div className="bg-white rounded-xl border border-[#FFE4D1] p-5">
              <div className="flex items-center justify-between mb-4">
                <span className="text-sm font-medium text-[#7C2D12]">订单状态</span>
                <div className={`flex items-center gap-1 text-sm font-medium ${statusMap[result.status].color}`}>
                  {(() => {
                    const Icon = statusMap[result.status].icon;
                    return <Icon size={16} />;
                  })()}
                  {statusMap[result.status].label}
                </div>
              </div>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-[#7C2D12]/60">订单编号</span>
                  <span className="text-[#7C2D12] font-mono">{result.orderId}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#7C2D12]/60">商品名称</span>
                  <span className="text-[#7C2D12]">{result.product}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#7C2D12]/60">订单金额</span>
                  <span className="text-[#F97316] font-semibold">¥{result.amount}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#7C2D12]/60">下单时间</span>
                  <span className="text-[#7C2D12]">{result.createdAt}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-[#7C2D12]/60">商户</span>
                  <span className="text-[#C2410C]">{result.merchant}</span>
                </div>
              </div>
            </div>

            {/* Card secrets */}
            {result.status === 'paid' && (
              <div className="bg-white rounded-xl border border-[#FFE4D1] p-5">
                <div className="text-sm font-medium text-[#7C2D12] mb-3">卡密内容</div>
                {result.secrets.length > 0 ? (
                  <div className="space-y-2">
                    {result.secrets.map((s, idx) => (
                      <div
                        key={idx}
                        className="flex items-center justify-between bg-[#FFF7ED] border border-[#FFE4D1] rounded-lg p-3"
                      >
                        <span className="font-mono text-sm text-[#7C2D12] tracking-wider break-all">{s}</span>
                        <button
                          onClick={() => handleCopy(s, idx)}
                          className="flex items-center gap-1 text-xs text-[#F97316] hover:text-[#EA580C] shrink-0 ml-2"
                        >
                          {copiedIdx === idx ? <Check size={14} /> : <Copy size={14} />}
                          {copiedIdx === idx ? '已复制' : '复制'}
                        </button>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="text-sm text-[#7C2D12]/60">暂无卡密</div>
                )}
              </div>
            )}

            {result.status === 'pending' && (
              <div className="bg-[#FFF7ED] rounded-xl border border-[#FFE4D1] p-4 text-sm text-[#7C2D12]/80">
                订单尚未支付，请先完成支付后再查询卡密。
              </div>
            )}

            {result.status === 'closed' && (
              <div className="bg-[#FFF7ED] rounded-xl border border-[#FFE4D1] p-4 text-sm text-[#7C2D12]/80">
                订单已关闭，无法查看卡密。如有疑问请联系商户客服。
              </div>
            )}
          </div>
        )}

        {!searched && (
          <div className="bg-white rounded-xl border border-[#FFE4D1] p-8 text-center">
            <Search size={40} className="mx-auto text-[#7C2D12]/30 mb-2" />
            <div className="text-sm text-[#7C2D12]/70">输入订单编号查询订单状态和卡密</div>
          </div>
        )}
      </div>
    </div>
  );
}
