import { useState, useMemo } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { sites } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Edit, Trash2, Activity, Plus, Search, CheckCircle, Globe, RefreshCcw } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import type { Site } from '../../types';

export default function BSites() {
  const { show } = useToast();
  const [list, setList] = useState(sites);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [addOpen, setAddOpen] = useState(false);
  const [editOpen, setEditOpen] = useState(false);
  const [current, setCurrent] = useState<Site | null>(null);
  const [form, setForm] = useState({ domain: '', pkg: 'PKG02' });
  const [tab, setTab] = useState('cname');

  const filtered = useMemo(() => {
    const kw = debouncedKeyword.trim().toLowerCase();
    if (!kw) return list;
    return list.filter((s) => s.domain.toLowerCase().includes(kw) || s.id.includes(kw));
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const reset = () => {
    setKeyword('');
    setPage(1);
  };

  const handleAdd = () => {
    const newSite: Site = {
      id: String(list.length + 1),
      name: form.domain,
      domain: form.domain,
      template: 'PC-01',
      products: 0,
      nodes: 0,
      status: 'pending',
      createdAt: new Date().toISOString().slice(0, 10),
    };
    setList([newSite, ...list]);
    setAddOpen(false);
    setForm({ domain: '', pkg: 'PKG02' });
    setPage(1);
    show(`站点 ${newSite.domain} 添加成功`, 'success');
  };

  const openEdit = (s: Site) => {
    setCurrent(s);
    setEditOpen(true);
  };

  const checkCname = () => {
    if (current) {
      setList(list.map((s) => (s.id === current.id ? { ...s, status: 'running' as const } : s)));
      setCurrent({ ...current, status: 'running' });
      show(`站点 ${current.domain} CNAME 检测通过`, 'success');
    }
  };

  return (
    <div>
      <PageHeader
        title="我的站点"
        breadcrumb={['站点管理', '我的站点']}
        actions={
          <button onClick={() => setAddOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加站点
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索域名 / ID"
              className="input pl-8"
              value={keyword}
              onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
            />
          </div>
          <button className="btn btn-primary">查询</button>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="站点域名" sortKey="domain" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>CNAME</th>
              <th>源站</th>
              <th>状态</th>
              <th>套餐到期</th>
              <th><SortableHeader label="创建时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((s) => (
              <tr key={s.id}>
                <td className="font-medium">{s.domain}</td>
                <td className="text-text-secondary">{s.status === 'running' ? `${s.domain}.cdn.dns` : '-'}</td>
                <td className="text-text-secondary">origin.{s.domain}</td>
                <td>
                  <span className={`badge ${statusBadge(s.status)}`}>{statusText(s.status)}</span>
                </td>
                <td className="text-text-secondary">2026-12-31</td>
                <td className="text-text-secondary">{s.createdAt}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => openEdit(s)} className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => show(`站点 ${s.domain} 检测正常`, 'success')} className="p-1.5 rounded hover:bg-gray-100 text-success" title="检测">
                      <Activity size={16} />
                    </button>
                    <button onClick={() => show(`站点 ${s.domain} 已删除`, 'warning')} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {sorted.length === 0 && (
          <EmptyState title="暂无站点" description="没有符合筛选条件的站点" icon={<Globe size={24} />} />
        )}

        {sorted.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>

      <Modal
        open={addOpen}
        title="添加站点"
        onClose={() => setAddOpen(false)}
        footer={
          <>
            <button onClick={() => setAddOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">保存</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">域名</label>
            <input value={form.domain} onChange={(e) => setForm({ ...form, domain: e.target.value })} className="input" placeholder="例如 www.example.com" />
          </div>
          <div>
            <label className="block text-sm mb-1">选择套餐</label>
            <select value={form.pkg} onChange={(e) => setForm({ ...form, pkg: e.target.value })} className="input">
              <option value="PKG01">入门版 ¥9.90/月</option>
              <option value="PKG02">标准版 ¥49.00/月</option>
              <option value="PKG03">专业版 ¥199.00/月</option>
              <option value="PKG04">企业版 ¥999.00/月</option>
            </select>
          </div>
          <div className="text-xs text-text-secondary bg-gray-50 p-3 rounded">
            系统将自动生成 CNAME，请稍后到 DNS 服务商处配置解析。
          </div>
        </div>
      </Modal>

      <Modal
        open={editOpen}
        title="编辑站点"
        onClose={() => setEditOpen(false)}
        footer={
          <>
            <button onClick={() => setEditOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => { setEditOpen(false); show('站点配置保存成功', 'success'); }} className="btn btn-primary">保存</button>
          </>
        }
      >
        {current && (
          <div>
            <div className="flex gap-2 mb-4 border-b border-border">
              {[
                { key: 'cname', label: 'CNAME' },
                { key: 'origin', label: '源站' },
                { key: 'security', label: '安全策略' },
                { key: 'access', label: '访问设置' },
                { key: 'cert', label: '证书' },
              ].map((t) => (
                <button
                  key={t.key}
                  onClick={() => setTab(t.key)}
                  className={`px-3 py-2 text-sm border-b-2 ${tab === t.key ? 'border-primary text-primary' : 'border-transparent text-text-secondary'}`}
                >
                  {t.label}
                </button>
              ))}
            </div>

            {tab === 'cname' && (
              <div className="space-y-4">
                <div>
                  <label className="block text-sm mb-1">系统分配 CNAME</label>
                  <div className="input bg-gray-50 flex items-center justify-between">
                    <span>{current.domain}.cdn.dns</span>
                    <button className="text-primary text-xs">复制</button>
                  </div>
                </div>
                <div className="flex items-center gap-2">
                  <button onClick={checkCname} className="btn btn-success flex items-center gap-1">
                    <CheckCircle size={16} /> 检测 CNAME
                  </button>
                  {current.status === 'running' && <span className="text-success text-sm">解析已生效</span>}
                </div>
              </div>
            )}

            {tab === 'origin' && (
              <div className="space-y-4">
                <div>
                  <label className="block text-sm mb-1">源站地址</label>
                  <input className="input" defaultValue={`origin.${current.domain}`} />
                </div>
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-sm mb-1">源站端口</label>
                    <input className="input" defaultValue="80" />
                  </div>
                  <div>
                    <label className="block text-sm mb-1">回源协议</label>
                    <select className="input">
                      <option>HTTP</option>
                      <option>HTTPS</option>
                    </select>
                  </div>
                </div>
              </div>
            )}

            {tab === 'security' && (
              <div className="space-y-4">
                <div>
                  <label className="block text-sm mb-1">CC 防护等级</label>
                  <select className="input">
                    <option>宽松</option>
                    <option>标准</option>
                    <option>严格</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm mb-1">IP 黑名单</label>
                  <textarea className="input" rows={3} placeholder="每行一个IP"></textarea>
                </div>
                <div>
                  <label className="block text-sm mb-1">防盗链</label>
                  <input className="input" placeholder="允许访问的 Referer" />
                </div>
              </div>
            )}

            {tab === 'access' && (
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-sm">HTTPS 强制跳转</span>
                  <input type="checkbox" defaultChecked className="w-4 h-4" />
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">GZIP 压缩</span>
                  <input type="checkbox" defaultChecked className="w-4 h-4" />
                </div>
                <div className="flex items-center justify-between">
                  <span className="text-sm">HTTP/2</span>
                  <input type="checkbox" defaultChecked className="w-4 h-4" />
                </div>
                <div>
                  <label className="block text-sm mb-1">缓存规则</label>
                  <textarea className="input" rows={3} defaultValue="/* 缓存 1小时\n/static/* 缓存 7天"></textarea>
                </div>
              </div>
            )}

            {tab === 'cert' && (
              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <span className="text-sm">自动申请 ZeroSSL / Let's Encrypt</span>
                  <input type="checkbox" defaultChecked className="w-4 h-4" />
                </div>
                <div className="text-xs text-text-secondary bg-gray-50 p-3 rounded">
                  证书到期前 7 天系统将自动续期，无需手动操作。
                </div>
                <div>
                  <label className="block text-sm mb-1">手动上传证书</label>
                  <textarea className="input" rows={3} placeholder="PEM 格式证书"></textarea>
                </div>
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
}
