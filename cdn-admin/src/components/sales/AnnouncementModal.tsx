import { X, Volume2 } from 'lucide-react';

interface AnnouncementModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function AnnouncementModal({ isOpen, onClose }: AnnouncementModalProps) {
  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm modal-overlay">
      <div className="relative w-full max-w-lg bg-[var(--sales-card)] rounded-2xl border border-[var(--sales-border)] shadow-2xl modal-content overflow-hidden">
        <div className="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-[var(--sales-primary)] via-[var(--sales-accent)] to-[var(--sales-success)]" />

        <button
          onClick={onClose}
          className="absolute top-4 right-4 p-1.5 rounded-full text-[var(--sales-text-secondary)] hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
        >
          <X size={18} />
        </button>

        <div className="p-6 md:p-8">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-10 h-10 rounded-xl bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] flex items-center justify-center">
              <Volume2 size={20} />
            </div>
            <div>
              <h3 className="text-lg font-bold text-[var(--sales-text)]">最新公告</h3>
              <p className="text-xs text-[var(--sales-text-secondary)]">2026-07-03</p>
            </div>
          </div>

          <div className="space-y-3 text-sm text-[var(--sales-text-secondary)] leading-relaxed">
            <p className="font-medium text-[var(--sales-text)]">
              CloudShield Store 全新上线，源码授权 + CDN 节点一站式采购！
            </p>
            <p>
              为庆祝销售系统正式上线，前 100 名购买源码授权的用户可享受 8 折优惠，并赠送 3 个月 CDN 节点使用权。活动时间有限，立即下单锁定优惠。
            </p>
            <ul className="space-y-1.5 list-disc list-inside pl-1">
              <li>源码授权包含完整前后端代码与部署文档</li>
              <li>CDN 节点覆盖国内主流运营商</li>
              <li>在线更新服务终身免费</li>
              <li>7×24 技术支持与答疑</li>
            </ul>
          </div>

          <div className="mt-6 flex gap-3">
            <button
              onClick={onClose}
              className="flex-1 py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 transition-all"
            >
              我知道了
            </button>
            <button
              onClick={() => {
                sessionStorage.setItem('sales-announcement-closed', '1');
                onClose();
              }}
              className="px-5 py-2.5 rounded-xl text-sm font-medium text-[var(--sales-text-secondary)] hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
            >
              今日不再提示
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
