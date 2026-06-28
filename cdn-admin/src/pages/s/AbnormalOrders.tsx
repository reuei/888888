import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { orders } from '../../data/mock';
import { statusBadge, statusText, formatMoney } from '../../utils/helpers';
import { AlertTriangle, RefreshCcw, RotateCcw } from 'lucide-react';

export default function SAbnormalOrders() {
  const [list, setList] = useState(orders.filter((o) => o.status === 'refunded' || o.status === 'closed'));
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<typeof orders[0] | null>(null);
  const [reason, setReason] = useState('');

  const openHandle = (o: typeof orders[0]) => {
    setCurrent(o);
    setReason('');
    setModalOpen(true);
  };

  const updateStatus = (status: 'paid' | 'closed') => {
    if (!current) return;
    setList(list.map((o) => (o.id === current.id ? { ...o, status } : o)));
    setModalOpen(false);
  };

  return (
    <div>
      <PageHeader title="异常订单处理" breadcrumb={['订单管理', '异常订单处理']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>订单号</th>
              <th>买家</th>
              <th>商户</th>
              <th>商品</th>
              <th>金额</th>
              <th>异常原因</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((o) => (
              <tr key={o.id}>
                <td className="font-medium">{o.id}</td>
                <td>{o.buyer}</td>
                <td>{o.merchant}</td>
                <td>{o.product}</td>
                <td>¥{formatMoney(o.amount)}</td>
                <td className="text-text-secondary">{o.status === 'refunded' ? '用户申请退款' : '支付超时关闭'}</td>
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
      </div>

      <Modal
        open={modalOpen}
        title="处理异常订单"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => updateStatus('closed')} className="btn btn-danger flex items-center gap-1">
              <RotateCcw size={16} /> 关闭订单
            </button>
            <button onClick={() => updateStatus('paid')} className="btn btn-success flex items-center gap-1">
              <RefreshCcw size={16} /> 补单
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
