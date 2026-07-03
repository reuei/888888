import { Download, CheckCircle, Clock, ArrowRight, Shield, FileText } from 'lucide-react';

const updates = [
  { version: 'v2.5.0', date: '2026-06-28', type: '重大更新', status: 'available', desc: '新增销售系统独立入口、3D 首页、弹窗公告。' },
  { version: 'v2.4.1', date: '2026-05-20', type: '安全更新', status: 'installed', desc: '修复已知安全漏洞，优化 PHP 8.2 兼容性。' },
  { version: 'v2.4.0', date: '2026-04-10', type: '功能更新', status: 'installed', desc: '新增节点健康检查、WAF 规则市场。' },
  { version: 'v2.3.0', date: '2026-03-01', type: '功能更新', status: 'installed', desc: '优化 B 端套餐购买流程与发票管理。' },
];

export default function Updates() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div className="text-center max-w-2xl mx-auto mb-12">
        <div className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[var(--sales-primary)]/10 text-[var(--sales-primary)] text-xs font-medium mb-4">
          <Download size={14} />
          在线更新
        </div>
        <h1 className="text-3xl md:text-4xl font-bold mb-4">在线更新服务</h1>
        <p className="text-[var(--sales-text-secondary)]">购买授权后享受终身免费更新，一键升级至最新版本。</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-4">
          {updates.map((u, i) => (
            <div key={i} className="p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)]">
              <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">
                <div className="flex items-center gap-3">
                  <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${u.status === 'available' ? 'bg-[var(--sales-primary)]/10 text-[var(--sales-primary)]' : 'bg-[var(--sales-success)]/10 text-[var(--sales-success)]'}`}>
                    {u.status === 'available' ? <Download size={20} /> : <CheckCircle size={20} />}
                  </div>
                  <div>
                    <h3 className="font-semibold">{u.version}</h3>
                    <p className="text-xs text-[var(--sales-text-secondary)]">{u.date} · {u.type}</p>
                  </div>
                </div>
                {u.status === 'available' ? (
                  <button className="px-4 py-1.5 rounded-lg text-xs font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30">
                    立即更新
                  </button>
                ) : (
                  <span className="px-3 py-1 rounded-lg text-xs font-medium text-[var(--sales-success)] bg-[var(--sales-success)]/10">
                    已安装
                  </span>
                )}
              </div>
              <p className="text-sm text-[var(--sales-text-secondary)]">{u.desc}</p>
            </div>
          ))}
        </div>

        <div className="lg:col-span-1">
          <div className="sticky top-24 p-6 rounded-2xl bg-[var(--sales-card)] border border-[var(--sales-border)]">
            <h3 className="text-lg font-semibold mb-4">当前授权信息</h3>
            <div className="space-y-3 text-sm mb-6">
              <div className="flex justify-between">
                <span className="text-[var(--sales-text-secondary)]">授权方案</span>
                <span className="font-medium">商业授权</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[var(--sales-text-secondary)]">当前版本</span>
                <span className="font-medium">v2.4.1</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[var(--sales-text-secondary)]">更新服务</span>
                <span className="font-medium text-[var(--sales-success)]">终身免费</span>
              </div>
              <div className="flex justify-between">
                <span className="text-[var(--sales-text-secondary)]">到期时间</span>
                <span className="font-medium">永久</span>
              </div>
            </div>
            <div className="border-t border-[var(--sales-border)] pt-4 space-y-3">
              <div className="flex items-start gap-2 text-xs text-[var(--sales-text-secondary)]">
                <Shield size={14} className="mt-0.5 shrink-0" />
                <span>更新前建议备份数据库与代码，避免数据丢失。</span>
              </div>
              <div className="flex items-start gap-2 text-xs text-[var(--sales-text-secondary)]">
                <FileText size={14} className="mt-0.5 shrink-0" />
                <span>详细更新日志请查看官方文档中心。</span>
              </div>
              <div className="flex items-start gap-2 text-xs text-[var(--sales-text-secondary)]">
                <Clock size={14} className="mt-0.5 shrink-0" />
                <span>大版本更新建议选择业务低峰期执行。</span>
              </div>
            </div>
            <button className="w-full mt-6 py-2.5 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-[var(--sales-primary)] to-[var(--sales-accent)] shadow-lg shadow-[var(--sales-primary)]/30 hover:shadow-[var(--sales-primary)]/50 transition-all flex items-center justify-center gap-2">
              检查更新 <ArrowRight size={16} />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
