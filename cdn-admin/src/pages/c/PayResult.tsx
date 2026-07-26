import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { CheckCircle, Mail, Home, Copy, Check } from 'lucide-react';

const cardSecret = 'TXSP-VIP-M8K2-9NXQ-7P4R';
const orderInfo = {
  orderId: 'O20260726001',
  amount: '15.00',
  payTime: '2026-07-26 14:32:18',
  merchant: '极速云',
  product: '腾讯视频VIP月卡',
  email: 'user***@example.com',
};

export default function PayResult() {
  const navigate = useNavigate();
  const [copied, setCopied] = useState(false);
  const [mailSent, setMailSent] = useState(false);

  const handleCopy = () => {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      navigator.clipboard.writeText(cardSecret).catch(() => {});
    }
    setCopied(true);
    setTimeout(() => setCopied(false), 1500);
  };

  const handleSendMail = () => {
    setMailSent(true);
    setTimeout(() => setMailSent(false), 2000);
  };

  return (
    <div className="theme-c min-h-screen bg-[#FFF7ED]">
      {/* Header */}
      <header className="bg-white border-b border-[#FFE4D1]">
        <div className="max-w-2xl mx-auto px-4 py-3">
          <h1 className="font-semibold text-[#7C2D12] text-center">支付结果</h1>
        </div>
      </header>

      <div className="max-w-2xl mx-auto px-4 py-6">
        {/* Success */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-8 mb-4 text-center">
          <CheckCircle size={56} className="mx-auto text-[#22C55E] mb-3" />
          <h2 className="text-xl font-bold text-[#7C2D12] mb-1">支付成功</h2>
          <p className="text-sm text-[#7C2D12]/70">卡密已自动发送，请妥善保存</p>
        </div>

        {/* Card secret */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-5 mb-4">
          <div className="flex items-center justify-between mb-3">
            <span className="text-sm font-medium text-[#7C2D12]">卡密内容</span>
            <button
              onClick={handleCopy}
              className="flex items-center gap-1 text-xs text-[#F97316] hover:text-[#EA580C]"
            >
              {copied ? <Check size={14} /> : <Copy size={14} />}
              {copied ? '已复制' : '复制'}
            </button>
          </div>
          <div className="font-mono text-base text-[#7C2D12] bg-[#FFF7ED] border border-[#FFE4D1] rounded-lg p-3 break-all tracking-wider">
            {cardSecret}
          </div>
          <div className="text-xs text-[#7C2D12]/60 mt-2">
            请勿泄露给他人，卡密一经发出概不退换
          </div>
        </div>

        {/* Order details */}
        <div className="bg-white rounded-xl border border-[#FFE4D1] p-5 mb-6">
          <div className="text-sm font-medium text-[#7C2D12] mb-3">订单详情</div>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">订单编号</span>
              <span className="text-[#7C2D12] font-mono">{orderInfo.orderId}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">商品名称</span>
              <span className="text-[#7C2D12]">{orderInfo.product}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">支付金额</span>
              <span className="text-[#F97316] font-semibold">¥{orderInfo.amount}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">支付时间</span>
              <span className="text-[#7C2D12]">{orderInfo.payTime}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">商户名称</span>
              <span className="text-[#C2410C]">{orderInfo.merchant}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-[#7C2D12]/60">接收邮箱</span>
              <span className="text-[#7C2D12]">{orderInfo.email}</span>
            </div>
          </div>
        </div>

        {/* Actions */}
        <div className="space-y-3">
          <button
            onClick={handleSendMail}
            className="w-full py-3 rounded-lg border border-[#F97316] text-[#F97316] font-medium hover:bg-[#FFF7ED] transition-colors flex items-center justify-center gap-2"
          >
            <Mail size={16} />
            {mailSent ? '邮件已重新发送' : '重新发送邮件'}
          </button>
          <button
            onClick={() => navigate('/c/home')}
            className="w-full py-3 rounded-lg bg-[#F97316] text-white font-medium hover:bg-[#EA580C] transition-colors flex items-center justify-center gap-2"
          >
            <Home size={16} />
            返回首页
          </button>
        </div>
      </div>
    </div>
  );
}
