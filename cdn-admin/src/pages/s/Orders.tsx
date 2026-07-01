import { useState, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import * as api from '../../services/api';
import type { Order } from '../../types';
import { statusBadge, statusText, formatMoney } from '../../utils/helpers';
import { Eye, RefreshCcw, FileDown } from 'lucide-react';
import { exportToCsv } from '../../utils/export';

export default function SOrders() {
  const { show } = useToast();
  const [list, setList] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    const data = await api.fetchOrders();
    setList(data);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const { sorted, sortKey, sortDirection, toggle } = useSort({ data: list, initialKey: 'createdAt', initialDirection: 'desc' });
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const handleExport = () => {
    exportToCsv(
      '全部订单',
      sorted,
      [
        { key: 'id', label: '订单号' },
        { key: 'buyer', label: '买家' },
        { key: 'merchant', label: '商户' },
        { key: 'product', label: '商品' },
        { key: 'amount', label: '金额' },
        { key: 'status', label: '状态' },
        { key: 'createdAt', label: '下单时间' },
      ]
    );
    show('订单导出成功', 'success');
  };

  if (loading) return <Loading />;

  return (
    <div>
      <PageHeader
        title="全部订单"
        breadcrumb={['订单管理', '全部订单']}
        actions={
          <button onClick={handleExport} className="btn btn-default flex items-center gap-1">
            <FileDown size={16} /> 导出
          </button>
        }
      />

      <div className="card p-5">
        <div className="grid grid-cols-1 md:grid-cols-5 gap-3 mb-4">
          <input type="text" placeholder="订单号" className="input" />
          <input type="text" placeholder="商户" className="input" />
          <input type="text" placeholder="商品" className="input" />
          <select className="input">
            <option>全部状态</option>
            <option>已支付</option>
            <option>待支付</option>
            <option>已退款</option>
            <option>已关闭</option>
          </select>
          <div className="flex gap-2">
            <button onClick={() => show('订单查询完成', 'info')} className="btn btn-primary">查询</button>
            <button onClick={() => show('筛选条件已重置', 'info')} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="订单号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>买家</th>
              <th>商户</th>
              <th>商品</th>
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
                <td>{o.buyer}</td>
                <td>{o.merchant}</td>
                <td>{o.product}</td>
                <td>¥{formatMoney(o.amount)}</td>
                <td>
                  <span className={`badge ${statusBadge(o.status)}`}>{statusText(o.status)}</span>
                </td>
                <td className="text-text-secondary">{o.createdAt}</td>
                <td>
                  <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="详情">
                    <Eye size={16} />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        <Pagination page={page} totalPages={totalPages} total={list.length} pageSize={pageSize} onChange={setPage} />
      </div>
    </div>
  );
}
