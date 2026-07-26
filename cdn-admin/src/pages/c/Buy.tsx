import { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ChevronLeft, Minus, Plus, Shield, AlertTriangle } from 'lucide-react';

interface Product {
  id: string;
  name: string;
  price: number;
  stock: number;
  merchantName: string;
  description: string;
}

const productMap: Record<string, Product> = {
  CP001: { id: 'CP001', name: '腾讯视频VIP月卡', price: 15.0, stock: 1280, merchantName: '极速云', description: '官方直充，秒到账，全国通用，下单后系统自动发货，30秒内到账。' },
  CP002: { id: 'CP002', name: '爱奇艺黄金会员季卡', price: 45.0, stock: 860, merchantName: '蓝海防护', description: '黄金会员权益，免广告追剧，下单后系统自动发货。' },
  CP003: { id: 'CP003', name: '京东E卡 100元', price: 99.5, stock: 500, merchantName: '站点卫士', description: '京东商城全场通用，无门槛使用，下单后自动发送卡密。' },
  CP004: { id: 'CP004', name: '话费充值100元', price: 99.8, stock: 5000, merchantName: '极速云', description: '移动联通电信通用，30秒到账，下单后自动充值。' },
  CP005: { id: 'CP005', name: '网易云音乐黑胶VIP年卡', price: 128.0, stock: 320, merchantName: '云盾科技', description: '无损音质，专属曲库权益，下单后系统自动发货。' },
  CP006: { id: 'CP006', name: '天猫超市优惠券50元', price: 35.0, stock: 200, merchantName: '蓝海防护', description: '天猫超市全场满200可用，下单后自动发送卡密。' },
  CP007: { id: 'CP007', name: 'Steam钱包50美元充值卡', price: 358.0, stock: 80, merchantName: '站点卫士', description: 'Steam钱包充值，全球可用，下单后自动发送卡密。' },
  CP008: { id: 'CP008', name: '美团外卖红包20元', price: 12.0, stock: 1500, merchantName: '云盾科技', description: '美团外卖下单立减，多门店可用，下单后自动发送。' },
};

const paymentMethods = [
  { code: 'wxpay', name: '微信支付', desc: '推荐使用，秒到账', icon: '💳' },
  { code: 'alipay', name: '支付宝', desc: '安全便捷', icon: '💰' },
  { code: 'usdt', name: 'USDT', desc: 'TRC20 链上支付', icon: '🪙' },
];

export default function Buy() {
  const navigate = useNavigate();
  const { productId } = useParams<{ productId: string }>();
  const product = productMap[productId || 'CP001'] || productMap.CP001;

  const [quantity, setQuantity] = useState(1);
  const [payMethod, setPayMethod] = useState('wxpay');
  const [showAnnouncement, setShowAnnouncement] = useState(true);
  const [countdown, setCountdown] = useState(5);

  useEffect(() => {
    if (!showAnnouncement) return;
    if (countdown <= 0) return;
    const t = setTimeout(() => setCountdown((c) => c - 1), 1000);
    return () => clearTimeout(t);
  }, [countdown, showAnnouncement]);

  const total = (product.price * quantity).toFixed(2);

  const handlePay = () => {
    navigate('/c/pay-result');
  };

  return (
    <div className="theme-c min-h-screen bg-[#FFF7ED]">
      {/* Header */}
      <header className="bg-white border-b border-[#FFE4D1] sticky top-0 z-10">
        <div className="max-w-3xl mx-auto px-4 py-3 flex items-center gap-3">
          <button onClick={() => navigate('/c/home')} className="text-[#7C2D12] hover:text-[#F97316]">
            <ChevronLeft size={20} />
          </button>
          <h1 className="font-semibold text-[#7C2D12]">确认订单</h1>
        </div>
      </header>

      <div className="max-w-3xl mx-auto px-4 py-4 pb-32">
        {/* Product info */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-4 mb-4">
          <div className="flex items-start justify-between mb-3">
            <div className="flex-1">
              <h2 className="text-lg font-semibold text-[#7C2D12]">{product.name}</h2>
              <p className="text-xs text-[#7C2D12]/70 mt-1">{product.description}</p>
            </div>
            <div className="text-right ml-3">
              <span className="text-xs text-[#7C2D12]/60">¥</span>
              <span className="text-2xl font-bold text-[#F97316]">{product.price.toFixed(2)}</span>
            </div>
          </div>
          <div className="grid grid-cols-2 gap-3 text-sm pt-3 border-t border-[#FFE4D1]">
            <div>
              <span className="text-[#7C2D12]/60">库存：</span>
              <span className="text-[#7C2D12]">{product.stock}</span>
            </div>
            <div>
              <span className="text-[#7C2D12]/60">商户：</span>
              <span className="text-[#C2410C]">{product.merchantName}</span>
            </div>
            <div>
              <span className="text-[#7C2D12]/60">发货：</span>
              <span className="text-[#7C2D12]">自动发货</span>
            </div>
            <div>
              <span className="text-[#7C2D12]/60">有效期：</span>
              <span className="text-[#7C2D12]">长期有效</span>
            </div>
          </div>
        </div>

        {/* Quantity selector */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-4 mb-4">
          <div className="flex items-center justify-between">
            <span className="text-sm text-[#7C2D12]">购买数量</span>
            <div className="flex items-center gap-3">
              <button
                onClick={() => setQuantity((q) => Math.max(1, q - 1))}
                disabled={quantity <= 1}
                className="w-8 h-8 rounded-lg border border-[#FFE4D1] flex items-center justify-center text-[#7C2D12] hover:border-[#F97316] disabled:opacity-40 disabled:hover:border-[#FFE4D1]"
              >
                <Minus size={14} />
              </button>
              <span className="w-10 text-center font-semibold text-[#7C2D12]">{quantity}</span>
              <button
                onClick={() => setQuantity((q) => Math.min(10, q + 1))}
                disabled={quantity >= 10}
                className="w-8 h-8 rounded-lg border border-[#FFE4D1] flex items-center justify-center text-[#7C2D12] hover:border-[#F97316] disabled:opacity-40 disabled:hover:border-[#FFE4D1]"
              >
                <Plus size={14} />
              </button>
            </div>
          </div>
          <div className="text-xs text-[#7C2D12]/60 mt-2">单笔最多可购买 10 件</div>
        </div>

        {/* Payment methods */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-4 mb-4">
          <div className="text-sm font-medium text-[#7C2D12] mb-3">支付方式</div>
          <div className="space-y-2">
            {paymentMethods.map((m) => (
              <label
                key={m.code}
                className={`flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors ${
                  payMethod === m.code
                    ? 'border-[#F97316] bg-[#FFF7ED]'
                    : 'border-[#FFE4D1] hover:border-[#F97316]/50'
                }`}
              >
                <input
                  type="radio"
                  name="pay"
                  checked={payMethod === m.code}
                  onChange={() => setPayMethod(m.code)}
                  className="accent-[#F97316]"
                />
                <span className="text-xl">{m.icon}</span>
                <div className="flex-1">
                  <div className="text-sm font-medium text-[#7C2D12]">{m.name}</div>
                  <div className="text-xs text-[#7C2D12]/60">{m.desc}</div>
                </div>
              </label>
            ))}
          </div>
        </div>

        {/* Safety tips */}
        <div className="bg-[#FFF7ED] rounded-xl border border-[#FFE4D1] p-3 flex items-start gap-2">
          <Shield size={16} className="text-[#F97316] shrink-0 mt-0.5" />
          <div className="text-xs text-[#7C2D12]/80 leading-relaxed">
            支付成功后卡密将自动发送至您的账户，请在「我的订单」中查看。本平台所有交易均受加密保护。
          </div>
        </div>
      </div>

      {/* Bottom buy bar */}
      <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-[#FFE4D1]">
        <div className="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
          <div>
            <div className="text-xs text-[#7C2D12]/60">应付金额</div>
            <div>
              <span className="text-sm text-[#F97316]">¥</span>
              <span className="text-2xl font-bold text-[#F97316]">{total}</span>
            </div>
          </div>
          <button
            onClick={handlePay}
            className="px-8 py-3 rounded-lg bg-[#F97316] text-white font-medium hover:bg-[#EA580C] transition-colors"
          >
            立即支付
          </button>
        </div>
      </div>

      {/* Forced announcement popup */}
      {showAnnouncement && (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4">
          <div className="bg-white rounded-xl max-w-sm w-full p-6">
            <div className="flex items-center gap-2 mb-3 text-[#F97316]">
              <AlertTriangle size={20} />
              <h3 className="font-bold text-lg">购买须知</h3>
            </div>
            <div className="text-sm text-[#7C2D12] leading-relaxed mb-4 space-y-2">
              <p>1. 本商品为虚拟商品，支付成功后系统自动发货，不支持退货退款。</p>
              <p>2. 请确认购买数量和支付方式无误后再付款。</p>
              <p>3. 卡密将在支付成功后立即显示，请及时保存。</p>
              <p>4. 如有疑问请联系商户客服或平台客服处理。</p>
            </div>
            <button
              onClick={() => setShowAnnouncement(false)}
              disabled={countdown > 0}
              className={`w-full py-2.5 rounded-lg font-medium transition-colors ${
                countdown > 0
                  ? 'bg-[#F97316]/30 text-white/80 opacity-50 cursor-not-allowed'
                  : 'bg-[#F97316] text-white hover:bg-[#EA580C]'
              }`}
            >
              {countdown > 0 ? `我知道了 (${countdown}s)` : '我知道了'}
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
