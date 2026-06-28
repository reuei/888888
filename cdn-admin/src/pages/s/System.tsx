import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { Mail, MessageSquare, HardDrive, Clock, Wrench, FileText, Send, Save } from 'lucide-react';

export default function SSystem() {
  const [tab, setTab] = useState<'email' | 'sms' | 'wechat' | 'storage' | 'cron' | 'tools' | 'protocol'>('email');

  const sections = [
    { key: 'email', label: '邮件系统', icon: Mail },
    { key: 'sms', label: '短信通知', icon: MessageSquare },
    { key: 'wechat', label: '微信公众号', icon: MessageSquare },
    { key: 'storage', label: '文件存储', icon: HardDrive },
    { key: 'cron', label: '定时任务', icon: Clock },
    { key: 'tools', label: '系统工具箱', icon: Wrench },
    { key: 'protocol', label: '弹窗协议', icon: FileText },
  ];

  return (
    <div>
      <PageHeader title="系统设置" breadcrumb={['系统设置', sections.find((s) => s.key === tab)?.label || '邮件系统']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-2 mb-6 border-b border-border">
          {sections.map((s) => (
            <button
              key={s.key}
              onClick={() => setTab(s.key as any)}
              className={`flex items-center gap-1.5 px-4 py-2 text-sm border-b-2 ${tab === s.key ? 'border-primary text-primary' : 'border-transparent text-text-secondary'}`}
            >
              <s.icon size={14} /> {s.label}
            </button>
          ))}
        </div>

        {tab === 'email' && (
          <div className="max-w-xl space-y-4">
            <div>
              <label className="block text-sm mb-1">发件人名称</label>
              <input className="input" defaultValue="CDN 平台" />
            </div>
            <div>
              <label className="block text-sm mb-1">邮箱账号</label>
              <input className="input" defaultValue="noreply@example.com" />
            </div>
            <div>
              <label className="block text-sm mb-1">SMTP 服务器</label>
              <input className="input" defaultValue="smtp.example.com" />
            </div>
            <div>
              <label className="block text-sm mb-1">SMTP 授权码</label>
              <input type="password" className="input" defaultValue="********" />
            </div>
            <div className="flex gap-2">
              <button className="btn btn-primary flex items-center gap-1"><Save size={16} /> 保存</button>
              <button className="btn btn-default flex items-center gap-1"><Send size={16} /> 测试发送</button>
            </div>
          </div>
        )}

        {tab === 'sms' && (
          <div className="max-w-xl space-y-6">
            <div className="p-4 border border-border rounded">
              <h4 className="font-medium mb-3">阿里云短信</h4>
              <div className="space-y-3">
                <input className="input" placeholder="AccessKey ID" />
                <input className="input" placeholder="AccessKey Secret" />
                <input className="input" placeholder="短信签名" />
                <input className="input" placeholder="模板 ID" />
              </div>
            </div>
            <div className="p-4 border border-border rounded">
              <h4 className="font-medium mb-3">短信宝</h4>
              <div className="space-y-3">
                <input className="input" placeholder="用户名" />
                <input type="password" className="input" placeholder="密码 / API Key" />
                <input className="input" placeholder="签名" />
              </div>
            </div>
            <button className="btn btn-primary">保存</button>
          </div>
        )}

        {tab === 'wechat' && (
          <div className="max-w-xl space-y-4">
            <div>
              <label className="block text-sm mb-1">公众号 AppID</label>
              <input className="input" placeholder="请输入 AppID" />
            </div>
            <div>
              <label className="block text-sm mb-1">公众号 AppSecret</label>
              <input type="password" className="input" placeholder="请输入 AppSecret" />
            </div>
            <div>
              <label className="block text-sm mb-1">模板消息 ID</label>
              <input className="input" placeholder="请输入模板消息 ID" />
            </div>
            <div>
              <label className="block text-sm mb-1">跳转链接</label>
              <input className="input" placeholder="https://..." />
            </div>
            <button className="btn btn-primary">保存</button>
          </div>
        )}

        {tab === 'storage' && (
          <div className="max-w-xl space-y-6">
            <div className="p-4 border border-border rounded">
              <h4 className="font-medium mb-3">本地存储</h4>
              <input className="input" defaultValue="/uploads" />
            </div>
            <div className="p-4 border border-border rounded">
              <h4 className="font-medium mb-3">七牛云 OSS</h4>
              <div className="space-y-3">
                <input className="input" placeholder="AccessKey" />
                <input className="input" placeholder="SecretKey" />
                <input className="input" placeholder="Bucket" />
                <input className="input" placeholder="域名" />
              </div>
            </div>
            <div className="p-4 border border-border rounded">
              <h4 className="font-medium mb-3">阿里云 OSS</h4>
              <div className="space-y-3">
                <input className="input" placeholder="AccessKey ID" />
                <input className="input" placeholder="AccessKey Secret" />
                <input className="input" placeholder="Bucket" />
                <input className="input" placeholder="Endpoint" />
              </div>
            </div>
            <button className="btn btn-primary">保存</button>
          </div>
        )}

        {tab === 'cron' && (
          <div>
            <table className="table">
              <thead>
                <tr>
                  <th>任务名称</th>
                  <th>Cron 表达式</th>
                  <th>状态</th>
                  <th>最近执行</th>
                </tr>
              </thead>
              <tbody>
                {[
                  { name: '自动结算', cron: '0 10 * * *', status: 'running', last: '2026-06-28 10:00' },
                  { name: '证书自动续期', cron: '0 2 * * *', status: 'running', last: '2026-06-28 02:00' },
                  { name: '节点健康检测', cron: '*/5 * * * *', status: 'running', last: '2026-06-28 10:55' },
                ].map((c, i) => (
                  <tr key={i}>
                    <td className="font-medium">{c.name}</td>
                    <td className="font-mono text-text-secondary">{c.cron}</td>
                    <td><span className="badge badge-success">运行中</span></td>
                    <td className="text-text-secondary">{c.last}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}

        {tab === 'tools' && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {[
              { name: '清理缓存', desc: '清除模板缓存与数据缓存' },
              { name: '数据库备份', desc: '导出当前数据库 SQL' },
              { name: '查看日志', desc: '查看系统运行日志' },
              { name: '一键修复', desc: '修复常见配置异常' },
            ].map((t, i) => (
              <div key={i} className="card p-4">
                <h4 className="font-medium mb-1">{t.name}</h4>
                <p className="text-xs text-text-secondary mb-3">{t.desc}</p>
                <button className="btn btn-primary text-xs w-full">执行</button>
              </div>
            ))}
          </div>
        )}

        {tab === 'protocol' && (
          <div className="max-w-2xl space-y-4">
            <div>
              <label className="block text-sm mb-1">购买页协议内容</label>
              <textarea className="input" rows={8} defaultValue="请仔细阅读并同意以下购买协议..."></textarea>
            </div>
            <div className="flex items-center gap-2">
              <input type="checkbox" id="force" defaultChecked className="w-4 h-4" />
              <label htmlFor="force" className="text-sm">强制勾选后才能购买</label>
            </div>
            <button className="btn btn-primary">保存</button>
          </div>
        )}
      </div>
    </div>
  );
}
