import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchBOrders, fetchInvoices, createInvoice } from '../../services/api';
import { formatMoney, statusBadge, statusText, invoiceTypeText } from '../../utils/helpers';
import { FileText, Plus, Eye, Search, RefreshCcw } from 'lucide-react';
import type { Invoice, BOrder } from '../../types';

export default function Invoice() {
  const { show } = useToast();
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [bOrders, setBOrders] = useState<BOrder[]>([]);
  const [loading, setLoading] = useState(false);
  const [applyOpen, setApplyOpen] = useState(false);
  const [detail, setDetail] = useState<Invoice | null>(null);

  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const load = useCallback(async () => {
    setLoading(true);
    const [invData, orderData] = await Promise.all([fetchInvoices(), fetchBOrders()]);
    setInvoices(invData);
    setBOrders(orderData);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const paidOrders = useMemo(() => bOrders.filter((o) => o.status === 'paid'), [bOrders]);

  const [form, setForm] = useState({
    orderId: paidOrders[0]?.id || '',
    type: 'personal' as 'personal' | 'company',
    title: '',
    taxId: '',
  });

  const selectedOrder = paidOrders.find((o) => o.id === form.orderId);

  const filtered = useMemo(() => {
    const kw = debouncedKeyword.trim().toLowerCase();
    if (!kw) return invoices;
    return invoices.filter((inv) =>
      inv.id.toLowerCase().includes(kw) ||
      inv.orderId.toLowerCase().includes(kw) ||
      inv.title.toLowerCase().includes(kw) ||
      (inv.taxId && inv.taxId.toLowerCase().includes(kw))
    );
  }, [invoices, debouncedKeyword]);

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

  const handleSubmit = async () => {
    if (!selectedOrder || !form.title.trim() || loading) return;
    const now = new Date();
    const createdAt = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    await createInvoice({
      orderId: selectedOrder.id,
      amount: selectedOrder.amount,
      status: 'pending',
      type: form.type,
      title: form.title.trim(),
      taxId: form.type === 'company' ? form.taxId.trim() || undefined : undefined,
      items: [
        {
          name: `${selectedOrder.product}（${selectedOrder.period}）`,
          quantity: 1,
          unitPrice: selectedOrder.amount,
          amount: selectedOrder.amount,
        },
      ],
      createdAt,
    });
    await load();
    setApplyOpen(false);
    setForm({ orderId: paidOrders[0]?.id || '', type: 'personal', title: '', taxId: '' });
    setPage(1);
    show(`发票申请提交成功`, 'success');
  };

  return (
    <div>
      <PageHeader title="发票申请" breadcrumb={['订单管理', '发票申请']} />

      <div className="flex items-center justify-between mb-4">
        <div className="text-sm text-text-secondary">
          已开票金额：<span className="text-primary font-medium">¥{formatMoney(invoices.filter((i) => i.status === 'issued').reduce((s, i) => s + i.amount, 0))}</span>
          <span className="mx-3">|</span>
          待开票金额：<span className="text-warning font-medium">¥{formatMoney(invoices.filter((i) => i.status === 'pending').reduce((s, i) => s + i.amount, 0))}</span>
        </div>
        <button
          onClick={() => setApplyOpen(true)}
          className="btn btn-primary flex items-center gap-1"
        >
          <Plus size={16} /> 申请发票
        </button>
      </div>

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索发票号 / 订单号 / 抬头 / 税号"
              className="input pl-8"
              value={keyword}
              onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
            />
          </div>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="发票号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>关联订单</th>
              <th>发票类型</th>
              <th>发票抬头</th>
              <th><SortableHeader label="金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th><SortableHeader label="申请时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((inv) => (
              <tr key={inv.id}>
                <td className="font-medium">{inv.id}</td>
                <td>{inv.orderId}</td>
                <td>{invoiceTypeText(inv.type)}</td>
                <td>{inv.title}</td>
                <td>¥{formatMoney(inv.amount)}</td>
                <td>
                  <span className={`badge ${statusBadge(inv.status)}`}>{statusText(inv.status)}</span>
                </td>
                <td className="text-text-secondary">{inv.createdAt}</td>
                <td>
                  <button
                    onClick={() => setDetail(inv)}
                    className="p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 text-primary"
                    title="详情"
                  >
                    <Eye size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && sorted.length === 0 && (
          <EmptyState
            title="暂无发票记录"
            description="您还没有申请过发票"
            icon={<FileText size={24} />}
            action={
              <button onClick={() => setApplyOpen(true)} className="btn btn-primary text-xs flex items-center gap-1">
                <Plus size={14} /> 申请发票
              </button>
            }
          />
        )}

        {sorted.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>

      <Modal
        open={applyOpen}
        title="申请发票"
        onClose={() => setApplyOpen(false)}
        footer={
          <>
            <button onClick={() => setApplyOpen(false)} className="btn btn-default">取消</button>
            <button
              onClick={handleSubmit}
              disabled={!form.title || !form.orderId}
              className="btn btn-primary disabled:opacity-70"
            >
              提交申请
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">选择订单</label>
            <select
              value={form.orderId}
              onChange={(e) => setForm({ ...form, orderId: e.target.value })}
              className="input"
            >
              {paidOrders.map((o) => (
                <option key={o.id} value={o.id}>
                  {o.id} - {o.product} - ¥{formatMoney(o.amount)}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">发票类型</label>
            <div className="flex gap-4">
              <label className="flex items-center gap-1.5 text-sm">
                <input
                  type="radio"
                  checked={form.type === 'personal'}
                  onChange={() => setForm({ ...form, type: 'personal' })}
                />
                个人发票
              </label>
              <label className="flex items-center gap-1.5 text-sm">
                <input
                  type="radio"
                  checked={form.type === 'company'}
                  onChange={() => setForm({ ...form, type: 'company' })}
                />
                企业发票
              </label>
            </div>
          </div>
          <div>
            <label className="block text-sm mb-1">发票抬头</label>
            <input
              value={form.title}
              onChange={(e) => setForm({ ...form, title: e.target.value })}
              className="input"
              placeholder="请输入发票抬头"
            />
          </div>
          {form.type === 'company' && (
            <div>
              <label className="block text-sm mb-1">纳税人识别号</label>
              <input
                value={form.taxId}
                onChange={(e) => setForm({ ...form, taxId: e.target.value })}
                className="input"
                placeholder="请输入税号"
              />
            </div>
          )}
          <div className="flex justify-between text-lg font-bold pt-2 border-t border-border">
            <span>开票金额</span>
            <span className="text-primary">¥{formatMoney(selectedOrder?.amount || 0)}</span>
          </div>
        </div>
      </Modal>

      <Modal
        open={!!detail}
        title="发票详情"
        onClose={() => setDetail(null)}
        footer={
          <button onClick={() => setDetail(null)} className="btn btn-default">关闭</button>
        }
      >
        {detail && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">发票号</span>
              <span className="font-medium">{detail.id}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">关联订单</span>
              <span>{detail.orderId}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">发票类型</span>
              <span>{invoiceTypeText(detail.type)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">发票抬头</span>
              <span>{detail.title}</span>
            </div>
            {detail.taxId && (
              <div className="flex justify-between py-2 border-b border-border">
                <span className="text-text-secondary">纳税人识别号</span>
                <span>{detail.taxId}</span>
              </div>
            )}
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">开票金额</span>
              <span className="text-primary font-medium">¥{formatMoney(detail.amount)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">发票状态</span>
              <span className={`badge ${statusBadge(detail.status)}`}>{statusText(detail.status)}</span>
            </div>
            <div className="py-2">
              <div className="text-text-secondary mb-2">开票明细</div>
              <table className="table">
                <thead>
                  <tr>
                    <th>项目名称</th>
                    <th>数量</th>
                    <th>单价</th>
                    <th>金额</th>
                  </tr>
                </thead>
                <tbody>
                  {detail.items.map((item, idx) => (
                    <tr key={idx}>
                      <td>{item.name}</td>
                      <td>{item.quantity}</td>
                      <td>¥{formatMoney(item.unitPrice)}</td>
                      <td>¥{formatMoney(item.amount)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
