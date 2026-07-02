import { useState, useMemo, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { Mail, MessageSquare, HardDrive, Clock, Wrench, FileText, Send, Save, Search, Inbox } from 'lucide-react';

interface CronJob {
  id: string;
  name: string;
  cron: string;
  status: 'running' | 'paused';
  last: string;
}

export default function SSystem() {
  const [tab, setTab] = useState<'email' | 'sms' | 'wechat' | 'storage' | 'cron' | 'tools' | 'protocol'>('email');
  const { show } = useToast();

  const sections = [
    { key: 'email', label: '邮件系统', icon: Mail },
    { key: 'sms', label: '短信通知', icon: MessageSquare },
    { key: 'wechat', label: '微信公众号', icon: MessageSquare },
    { key: 'storage', label: '文件存储', icon: HardDrive },
    { key: 'cron', label: '定时任务', icon: Clock },
    { key: 'tools', label: '系统工具箱', icon: Wrench },
    { key: 'protocol', label: '弹窗协议', icon: FileText },
  ];

  const [cronJobs, setCronJobs] = useState<CronJob[]>([
    { id: 'C001', name: '自动结算', cron: '0 10 * * *', status: 'running', last: '2026-06-28 10:00' },
    { id: 'C002', name: '证书自动续期', cron: '0 2 * * *', status: 'running', last: '2026-06-28 02:00' },
    { id: 'C003', name: '节点健康检测', cron: '*/5 * * * *', status: 'running', last: '2026-06-28 10:55' },
  ]);

  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return cronJobs.filter((c) => {
      if (!q) return true;
      return [c.id, c.name, c.cron, c.status, c.last].some((v) => String(v).toLowerCase().includes(q));
    });
  }, [cronJobs, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<CronJob>({
    data: filtered,
    initialKey: 'name',
    initialDirection: 'asc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const toggleCronStatus = (id: string) => {
    setCronJobs((prev) =>
      prev.map((c) => (c.id === id ? { ...c, status: c.status === 'running' ? 'paused' : 'running' } : c))
    );
  };

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
              <button onClick={() => show('邮件配置保存成功', 'success')} className="btn btn-primary flex items-center gap-1"><Save size={16} /> 保存</button>
              <button onClick={() => show('测试邮件已发送', 'info')} className="btn btn-default flex items-center gap-1"><Send size={16} /> 测试发送</button>
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
            <button onClick={() => show('短信配置保存成功', 'success')} className="btn btn-primary">保存</button>
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
            <button onClick={() => show('微信公众号配置保存成功', 'success')} className="btn btn-primary">保存</button>
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
            <button onClick={() => show('文件存储配置保存成功', 'success')} className="btn btn-primary">保存</button>
          </div>
        )}

        {tab === 'cron' && (
          <div>
            <div className="flex flex-wrap gap-3 mb-4">
              <div className="relative flex-1 min-w-[200px]">
                <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                <input
                  type="text"
                  value={keyword}
                  onChange={(e) => setKeyword(e.target.value)}
                  placeholder="搜索任务名称 / Cron / 状态 / 最近执行"
                  className="input pl-8"
                />
              </div>
            </div>

            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof CronJob> label="任务名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof CronJob> label="Cron 表达式" sortKey="cron" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof CronJob> label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof CronJob> label="最近执行" sortKey="last" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((c) => (
                  <tr key={c.id}>
                    <td className="font-medium">{c.name}</td>
                    <td className="font-mono text-text-secondary">{c.cron}</td>
                    <td>
                      <span className={`badge ${c.status === 'running' ? 'badge-success' : 'badge-default'}`}>
                        {c.status === 'running' ? '运行中' : '已暂停'}
                      </span>
                    </td>
                    <td className="text-text-secondary">{c.last}</td>
                    <td>
                      <button
                        onClick={() => toggleCronStatus(c.id)}
                        className="text-xs text-primary hover:underline"
                      >
                        {c.status === 'running' ? '暂停' : '启动'}
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {pagedList.length === 0 && (
              <EmptyState title="暂无定时任务" description="没有符合搜索条件的定时任务" icon={<Inbox size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
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
                <button onClick={() => show(`${t.name}执行成功`, 'success')} className="btn btn-primary text-xs w-full">执行</button>
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
            <button onClick={() => show('弹窗协议保存成功', 'success')} className="btn btn-primary">保存</button>
          </div>
        )}
      </div>
    </div>
  );
}
