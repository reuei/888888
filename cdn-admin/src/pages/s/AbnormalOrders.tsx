import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { useToast } from '../../hooks/useToast';
import { fetchOrders, updateOrder } from '../../services/api';
import { statusBadge, statusText, formatMoney } from '../../utils/helpers';
import { AlertTriangle, RefreshCcw, RotateCcw, Search, Inbox, Loader2 } from 'lucide-react';
import type { Order } from '../../types';

export default function SAbnormalOrders() {
  const { show } = useToast();
  const [list, setList] = useState<Order[]>([]);
  const [loading, setLoading] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<Order | null>(null);
  const [reason, setReason] = useState('');
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return list.filter((o) => {
      if (!q) return true;
      return [o.id, o.buyer, o.merchant, o.product, o.status, o.createdAt].some((v) =>
        String(v).toLowerCase().includes(q)
      ) || String(o.amount).includes(q);
    });
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<Order>({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchOrders();
    setList(data.filter((o) => o.status === 'refunded' || o.status === 'closed'));
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const openHandle = (o: Order) => {
    setCurrent(o);
    setReason('');
    setModalOpen(true);
  };

  const updateStatus = async (status: 'paid' | 'closed') => {
    if (!current || loading) return;
    await updateOrder(current.id, { status });
    await load();
    setModalOpen(false);
    show(`订单 ${current.id} 已${status === 'paid' ? '补单' : '关闭'}`, status === 'paid' ? 'success' : 'warning');
  };

  const abnormalReason = (status: Order['status']) =>
    status === 'refunded' ? '用户申请退款' : '支付超时关闭';

  return (
    <div>
      <PageHeader title="异常订单处理" breadcrumb={['订单管理', '异常订单处理']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索订单号 / 买家 / 商户 / 商品 / 金额 / 状态"
              className="input pl-8"
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>
                <SortableHeader<keyof Order> label="订单号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Order> label="买家" sortKey="buyer" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Order> label="商户" sortKey="merchant" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Order> label="商品" sortKey="product" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Order> label="金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>异常原因</th>
              <th>
                <SortableHeader<keyof Order> label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((o) => (
              <tr key={o.id}>
                <td className="font-medium">{o.id}</td>
                <td>{o.buyer}</td>
                <td>{o.merchant}</td>
                <td>{o.product}</td>
                <td>¥{formatMoney(o.amount)}</td>
                <td className="text-text-secondary">{abnormalReason(o.status)}</td>
                <td>
                  <span className={`badge ${statusBadge(o.status)}`}>{statusText(o.status)}</span>
                </td>
                <td>
                  <button onClick={() => openHandle(o)} className="p-1.5 rounded hover:bg-gray-100 text-warning" title="处理">
                    <AlertTriangle size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && pagedList.length === 0 && (
          <EmptyState title="暂无异常订单" description="没有符合搜索条件的异常订单" icon={<Inbox size={24} />} />
        )}

        {!loading && pagedList.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>

      <Modal
        open={modalOpen}
        title="处理异常订单"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('closed')} disabled={loading} className="btn btn-danger flex items-center gap-1 disabled:opacity-70">
              <RotateCcw size={16} /> 关闭订单
            </button>
            <button onClick={() => updateStatus('paid')} disabled={loading} className="btn btn-success flex items-center gap-1 disabled:opacity-70">
              {loading ? <Loader2 size={16} className="animate-spin" /> : <RefreshCcw size={16} />}
              补单
            </button>
          </>
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">订单号：</span>{current.id}</div>
            <div><span className="text-text-secondary">买家：</span>{current.buyer}</div>
            <div><span className="text-text-secondary">商户：</span>{current.merchant}</div>
            <div><span className="text-text-secondary">商品：</span>{current.product}</div>
            <div><span className="text-text-secondary">金额：</span>¥{formatMoney(current.amount)}</div>
            <div>
              <label className="block text-text-secondary mb-1">异常原因 / 处理备注</label>
              <textarea className="input" rows={3} value={reason} onChange={(e) => setReason(e.target.value)} placeholder="请填写异常原因或处理备注"></textarea>
            </div>
            <div className="text-xs text-text-secondary bg-orange-50 p-2 rounded">
              补单将恢复订单为已支付状态并通知双方；关闭订单将无法恢复。
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
