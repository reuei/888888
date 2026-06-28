import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { settlementRecords } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus, CheckCircle } from 'lucide-react';

export default function SettlementManual() {
  const [records, setRecords] = useState(settlementRecords);
  const [open, setOpen] = useState(false);
  const [merchant, setMerchant] = useState('极速云');
  const [cycle, setCycle] = useState('T+1');
  const [amount, setAmount] = useState('');

  const merchantOptions = Array.from(new Set(records.map((r) => r.merchant)));

  const statusClass = (status: string) =>
    status === 'settled' ? 'badge-success' : 'badge-warning';
  const statusText = (status: string) =>
    status === 'settled' ? '已结算' : '待处理';

  const handleConfirm = () => {
    const value = parseFloat(amount);
    if (!value || value <= 0) return;
    const fee = Math.round(value * 0.01 * 100) / 100;
    const now = new Date();
    const time = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    const newRecord = {
      id: `SET${String(records.length + 1).padStart(3, '0')}`,
      merchant,
      cycle,
      amount: value,
      fee,
      status: 'pending',
      time,
    };
    setRecords([newRecord, ...records]);
    setAmount('');
    setOpen(false);
  };

  return (
    <div>
      <PageHeader
        title="手动结算"
        breadcrumb={['财务管理', '手动结算']}
        actions={
          <button onClick={() => setOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 手动发起结算
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>结算单号</th>
              <th>商户</th>
              <th>周期</th>
              <th>金额</th>
              <th>手续费</th>
              <th>状态</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            {records.map((r) => (
              <tr key={r.id}>
                <td className="font-medium">{r.id}</td>
                <td>{r.merchant}</td>
                <td>{r.cycle}</td>
                <td>¥{formatMoney(r.amount)}</td>
                <td>¥{formatMoney(r.fee)}</td>
                <td>
                  <span className={`badge ${statusClass(r.status)}`}>
                    {statusText(r.status)}
                  </span>
                </td>
                <td className="text-text-secondary">{r.time}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={open}
        title="手动发起结算"
        onClose={() => setOpen(false)}
        footer={
          <>
            <button onClick={() => setOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleConfirm} className="btn btn-success flex items-center gap-1">
              <CheckCircle size={16} /> 确认结算
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">选择商户</label>
            <select className="input" value={merchant} onChange={(e) => setMerchant(e.target.value)}>
              {merchantOptions.map((m) => (
                <option key={m} value={m}>
                  {m}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">结算周期</label>
            <select className="input" value={cycle} onChange={(e) => setCycle(e.target.value)}>
              <option>T+0</option>
              <option>T+1</option>
              <option>T+7</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">结算金额（元）</label>
            <input
              type="number"
              className="input"
              placeholder="请输入金额"
              value={amount}
              onChange={(e) => setAmount(e.target.value)}
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
