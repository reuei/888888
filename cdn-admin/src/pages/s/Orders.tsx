import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { orders } from '../../data/mock';
import { statusBadge, statusText, formatMoney } from '../../utils/helpers';
import { Eye, RefreshCcw } from 'lucide-react';

export default function SOrders() {
  const { show } = useToast();
  const [list] = useState(orders);
  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: list.length });
  const pagedList = slice(list);

  return (
    <div>
      <PageHeader title="全部订单" breadcrumb={['订单管理', '全部订单']} />

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
              <th>订单号</th>
              <th>买家</th>
              <th>商户</th>
              <th>商品</th>
              <th>金额</th>
              <th>状态</th>
              <th>下单时间</th>
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
