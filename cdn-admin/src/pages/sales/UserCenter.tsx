import { User, ShoppingBag, Server, Download, CreditCard, Settings } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const menuItems = [
  { key: 'orders', label: '我的订单', icon: ShoppingBag, desc: '查看源码授权与节点购买记录' },
  { key: 'nodes', label: '我的节点', icon: Server, desc: '管理已购买的 CDN 节点资源' },
  { key: 'updates', label: '在线更新', icon: Download, desc: '检查并安装最新版本' },
  { key: 'finance', label: '资金管理', icon: CreditCard, desc: '余额、充值与消费记录' },
  { key: 'profile', label: '账号设置', icon: Settings, desc: '修改密码与绑定信息' },
];

export default function UserCenter() {
  const navigate = useNavigate();

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-1">
          <div className="p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] text-center">
            <div className="w-20 h-20 rounded-full bg-gradient-to-br from-[var(--sales-primary)] to-[var(--sales-accent)] text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[var(--sales-primary)]/30">
              <User size={36} />
            </div>
            <h2 className="text-xl font-bold">企业用户</h2>
            <p className="text-sm text-[var(--sales-text-secondary)] mt-1">enterprise@example.com</p>
            <div className="mt-4 pt-4 border-t border-[var(--sales-border)]">
              <div className="grid grid-cols-2 gap-4 text-center">
                <div>
                  <div className="text-lg font-bold text-[var(--sales-primary)]">2</div>
                  <div className="text-xs text-[var(--sales-text-secondary)]">授权订单</div>
                </div>
                <div>
                  <div className="text-lg font-bold text-[var(--sales-primary)]">3</div>
                  <div className="text-xs text-[var(--sales-text-secondary)]">节点资源</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="lg:col-span-2">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {menuItems.map((item) => (
              <button
                key={item.key}
                onClick={() => {
                  if (item.key === 'orders') navigate('/orders');
                  else if (item.key === 'nodes') navigate('/buy-nodes');
                  else if (item.key === 'updates') navigate('/updates');
                  else alert('功能开发中');
                }}
                className="p-5 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] text-left hover:border-[var(--sales-primary)]/30 hover:shadow-lg hover:shadow-[var(--sales-primary)]/10 transition-all"
              >
                <div className="flex items-start gap-4">
                  <div className="w-10 h-10 rounded-xl bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] flex items-center justify-center shrink-0">
                    <item.icon size={20} />
                  </div>
                  <div>
                    <h3 className="font-semibold">{item.label}</h3>
                    <p className="text-sm text-[var(--sales-text-secondary)] mt-1">{item.desc}</p>
                  </div>
                </div>
              </button>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
