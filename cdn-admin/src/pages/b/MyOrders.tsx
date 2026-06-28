import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import type { BOrder } from '../../types';
import { bOrders } from '../../data/mock';
import { formatMoney, statusBadge, orderStatusText } from '../../utils/helpers';
import { Eye, FileText, RefreshCcw } from 'lucide-react';
import EmptyState from '../../components/EmptyState';

export default function MyOrders() {
  const { show } = useToast();
  const [list] = useState(bOrders);
  const [detail, setDetail] = useState<typeof bOrders[0] | null>(null);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [productKeyword, setProductKeyword] = useState('');
  const debouncedProductKeyword = useDebounce(productKeyword);
  const [statusFilter, setStatusFilter] = useState<'all' | BOrder['status']>('all');

  const statusOptions: { value: 'all' | BOrder['status']; label: string }[] = [
    { value: 'all', label: '全部状态' },
    { value: 'pending', label: '待支付' },
    { value: 'paid', label: '已支付' },
    { value: 'refunded', label: '已退款' },
    { value: 'cancelled', label: '已取消' },
  ];

  const filtered = list.filter((o) => {
    const matchId = !debouncedKeyword || o.id.toLowerCase().includes(debouncedKeyword.trim().toLowerCase());
    const matchProduct = !debouncedProductKeyword || o.product.toLowerCase().includes(debouncedProductKeyword.trim().toLowerCase());
    const matchStatus = statusFilter === 'all' || o.status === statusFilter;
    return matchId && matchProduct && matchStatus;
  });

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: filtered, initialKey: 'createdAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const reset = () => {
    setKeyword('');
    setProductKeyword('');
    setStatusFilter('all');
  };

  return (
    <div>
      <PageHeader title="我的订单" breadcrumb={['订单管理', '我的订单']} />

      <div className="card p-5">
        <div className="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
          <input
            type="text"
            placeholder="订单号"
            className="input"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
          />
          <input
            type="text"
            placeholder="商品名称"
            className="input"
            value={productKeyword}
            onChange={(e) => setProductKeyword(e.target.value)}
          />
          <select
            className="input"
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value as 'all' | BOrder['status'])}
          >
            {statusOptions.map((opt) => (
              <option key={opt.value} value={opt.value}>{opt.label}</option>
            ))}
          </select>
          <input type="date" className="input" />
          <div className="flex gap-2">
            <button onClick={() => show('查询完成', 'info')} className="btn btn-primary">查询</button>
            <button onClick={() => { reset(); show('筛选条件已重置', 'info'); }} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="订单号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>商品</th>
              <th>套餐ID</th>
              <th>周期</th>
              <th><SortableHeader label="金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th><SortableHeader label="下单时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((o) => (
              <tr key={o.id}>
                <td className="font-medium">{o.id}</td>
                <td>{o.product}</td>
                <td>{o.packageId}</td>
                <td>{o.period}</td>
                <td>¥{formatMoney(o.amount)}</td>
                <td>
                  <span className={`badge ${statusBadge(o.status)}`}>{orderStatusText(o.status)}</span>
                </td>
                <td className="text-text-secondary">{o.createdAt}</td>
                <td>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => setDetail(o)}
                      className="p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 text-primary"
                      title="详情"
                    >
                      <Eye size={16} />
                    </button>
                    {o.status === 'paid' && (
                      <button className="p-1.5 rounded hover:bg-black/5 dark:hover:bg-white/10 text-success" title="申请发票">
                        <FileText size={16} />
                      </button>
                    )}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && (
          <EmptyState title="暂无订单" description="没有符合筛选条件的订单" />
        )}

        <Pagination page={page} totalPages={totalPages} total={filtered.length} pageSize={pageSize} onChange={setPage} />
      </div>

      <Modal
        open={!!detail}
        title="订单详情"
        onClose={() => setDetail(null)}
        footer={
          <button onClick={() => setDetail(null)} className="btn btn-default">关闭</button>
        }
      >
        {detail && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">订单号</span>
              <span className="font-medium">{detail.id}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">商品</span>
              <span>{detail.product}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">套餐ID</span>
              <span>{detail.packageId}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">周期</span>
              <span>{detail.period}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">订单金额</span>
              <span className="text-primary font-medium">¥{formatMoney(detail.amount)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">订单状态</span>
              <span className={`badge ${statusBadge(detail.status)}`}>{orderStatusText(detail.status)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-border">
              <span className="text-text-secondary">下单时间</span>
              <span>{detail.createdAt}</span>
            </div>
            {detail.paidAt && (
              <div className="flex justify-between py-2 border-b border-border">
                <span className="text-text-secondary">支付时间</span>
                <span>{detail.paidAt}</span>
              </div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
}
