import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../components/Toast';
import { merchants } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle, XCircle, Eye, Plus, Store, RefreshCcw } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import { usePagination } from '../../hooks/usePagination';

export default function SMerchants() {
  const { show } = useToast();
  const [list, setList] = useState(merchants);
  const [keyword, setKeyword] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [auditOpen, setAuditOpen] = useState(false);
  const [current, setCurrent] = useState<typeof merchants[0] | null>(null);

  const filtered = list.filter((m) => {
    const matchKeyword = !keyword || m.shopName.includes(keyword) || m.phone.includes(keyword) || m.id.includes(keyword);
    const matchStatus = statusFilter === 'all' || m.status === statusFilter;
    return matchKeyword && matchStatus;
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: filtered.length });
  const pagedList = slice(filtered);

  const reset = () => {
    setKeyword('');
    setStatusFilter('all');
  };

  const handleAudit = (m: typeof merchants[0]) => {
    setCurrent(m);
    setAuditOpen(true);
  };

  const updateStatus = (status: 'normal' | 'rejected') => {
    if (!current) return;
    setList(list.map((m) => (m.id === current.id ? { ...m, status: status === 'normal' ? 'normal' : 'banned' } : m)));
    setAuditOpen(false);
    show(`商户 ${current.shopName} 审核${status === 'normal' ? '通过' : '已驳回'}`, status === 'normal' ? 'success' : 'warning');
  };

  return (
    <div>
      <PageHeader title="商户列表" breadcrumb={['商户管理', '商户列表']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <input
            type="text"
            placeholder="搜索店铺名 / 手机号 / ID"
            className="input flex-1 min-w-[200px]"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
          />
          <select className="input w-32" value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)}>
            <option value="all">全部状态</option>
            <option value="normal">正常</option>
            <option value="pending">待审核</option>
            <option value="banned">已封禁</option>
          </select>
          <button className="btn btn-primary">查询</button>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>商户</th>
              <th>店铺ID</th>
              <th>手机号</th>
              <th>开店时间</th>
              <th>保证金</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((m) => (
              <tr key={m.id}>
                <td>
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                      {m.avatar}
                    </div>
                    <span className="font-medium">{m.shopName}</span>
                  </div>
                </td>
                <td className="text-text-secondary">{m.id}</td>
                <td>{m.phone}</td>
                <td className="text-text-secondary">{m.registerAt}</td>
                <td>¥{m.deposit.toLocaleString()}</td>
                <td>
                  <span className={`badge ${statusBadge(m.status)}`}>{statusText(m.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                      <Eye size={16} />
                    </button>
                    {m.status === 'pending' && (
                      <button onClick={() => handleAudit(m)} className="p-1.5 rounded hover:bg-gray-100 text-success" title="审核">
                        <CheckCircle size={16} />
                      </button>
                    )}
                    {m.status !== 'banned' ? (
                      <button onClick={() => show(`商户 ${m.shopName} 已封禁`, 'warning')} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="封禁">
                        <XCircle size={16} />
                      </button>
                    ) : (
                      <button onClick={() => show(`商户 ${m.shopName} 已解禁`, 'success')} className="p-1.5 rounded hover:bg-gray-100 text-success" title="解禁">
                        <Plus size={16} />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无商户" description="没有符合筛选条件的商户" icon={<Store size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={auditOpen}
        title="商户审核"
        onClose={() => setAuditOpen(false)}
        footer={
          <>
            <button onClick={() => setAuditOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('rejected')} className="btn btn-danger">驳回</button>
            <button onClick={() => updateStatus('normal')} className="btn btn-success">通过</button>
          </>
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">店铺名：</span>{current.shopName}</div>
            <div><span className="text-text-secondary">手机号：</span>{current.phone}</div>
            <div><span className="text-text-secondary">身份证：</span>11010119900101****</div>
            <div><span className="text-text-secondary">三要素验证：</span><span className="text-success">通过</span></div>
            <div className="grid grid-cols-2 gap-3 mt-3">
              <div className="h-24 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证正面</div>
              <div className="h-24 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">身份证反面</div>
            </div>
            <div>
              <label className="block text-text-secondary mb-1">审核备注 / 驳回原因</label>
              <textarea className="input" rows={3} placeholder="选填"></textarea>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
