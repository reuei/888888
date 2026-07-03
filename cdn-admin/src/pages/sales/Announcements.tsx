import { Bell, Calendar } from 'lucide-react';

const announcements = [
  { id: 1, title: 'CloudShield Store 全新上线', date: '2026-07-03', type: '活动', desc: '销售系统正式上线，前 100 名购买源码授权用户享 8 折优惠。' },
  { id: 2, title: 'v2.5.0 版本发布', date: '2026-06-28', type: '更新', desc: '新增销售系统、3D 首页、弹窗公告等功能。' },
  { id: 3, title: '节点资源扩容通知', date: '2026-06-15', type: '通知', desc: '海外香港、新加坡节点资源已扩容，欢迎选购。' },
  { id: 4, title: '关于 PHP 8.2 兼容性说明', date: '2026-05-20', type: '公告', desc: '系统已全面适配 PHP 8.2，建议用户升级运行环境。' },
];

const typeColors: Record<string, string> = {
  活动: 'text-[var(--sales-primary)] bg-[var(--sales-primary)]/10',
  更新: 'text-[var(--sales-success)] bg-[var(--sales-success)]/10',
  通知: 'text-[var(--sales-warning)] bg-[var(--sales-warning)]/10',
  公告: 'text-[var(--sales-text-secondary)] bg-black/5 dark:bg-white/10',
};

export default function Announcements() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center max-w-2xl mx-auto mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-4">
          <Bell size={14} />
          公告中心
        </div>
        <h1 className="text-3xl md:text-4xl font-bold mb-4">最新公告</h1>
        <p className="text-[var(--sales-text-secondary)]">及时了解产品更新、优惠活动与重要通知。</p>
      </div>

      <div className="space-y-4 max-w-4xl mx-auto">
        {announcements.map((a) => (
          <div key={a.id} className="p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)] hover:border-[var(--sales-primary)]/30 transition-all">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3">
              <div className="flex items-center gap-3">
                <span className={`px-2.5 py-0.5 rounded-lg text-xs font-medium ${typeColors[a.type]}`}>{a.type}</span>
                <h3 className="font-semibold">{a.title}</h3>
              </div>
              <div className="flex items-center gap-1 text-xs text-[var(--sales-text-secondary)]">
                <Calendar size={12} />
                {a.date}
              </div>
            </div>
            <p className="text-sm text-[var(--sales-text-secondary)] leading-relaxed">{a.desc}</p>
          </div>
        ))}
      </div>
    </div>
  );
}
