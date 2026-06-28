import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { myPackages as myPackagesData, packages } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { RefreshCw, CreditCard } from 'lucide-react';

const periods = [1, 3, 6, 12];

export default function MyPackages() {
  const [renewItem, setRenewItem] = useState<typeof myPackagesData[0] | null>(null);
  const [months, setMonths] = useState(1);

  const openRenew = (item: typeof myPackagesData[0]) => {
    setRenewItem(item);
    setMonths(1);
  };

  const payable = renewItem
    ? (packages.find((p) => p.name === renewItem.name)?.price || 0) * months
    : 0;

  return (
    <div>
      <PageHeader title="我的套餐" breadcrumb={['套餐管理', '我的套餐']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>套餐ID</th>
              <th>套餐名称</th>
              <th>流量</th>
              <th>带宽</th>
              <th>域名数</th>
              <th>到期时间</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {myPackagesData.map((item) => (
              <tr key={item.id}>
                <td className="font-medium">{item.id}</td>
                <td>{item.name}</td>
                <td>{item.flow}</td>
                <td>{item.bandwidth}</td>
                <td>{item.domains}</td>
                <td>{item.expireAt}</td>
                <td>
                  <span className={`badge ${statusBadge(item.status)}`}>
                    {statusText(item.status)}
                  </span>
                </td>
                <td>
                  <button
                    onClick={() => openRenew(item)}
                    className="btn btn-default text-xs flex items-center gap-1"
                  >
                    <RefreshCw size={14} /> 续费
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={!!renewItem}
        title="套餐续费"
        onClose={() => setRenewItem(null)}
        footer={
          <>
            <button onClick={() => setRenewItem(null)} className="btn btn-default">取消</button>
            <button onClick={() => setRenewItem(null)} className="btn btn-primary flex items-center gap-1">
              <CreditCard size={16} /> 确认续费
            </button>
          </>
        }
      >
        {renewItem && (
          <div className="space-y-4">
            <div className="text-sm">
              <span className="text-text-secondary">当前套餐：</span>
              <span className="font-medium">{renewItem.name}</span>
            </div>
            <div>
              <label className="block text-sm mb-1">续费时长</label>
              <select
                value={months}
                onChange={(e) => setMonths(Number(e.target.value))}
                className="input"
              >
                {periods.map((m) => (
                  <option key={m} value={m}>
                    {m} 个月
                  </option>
                ))}
              </select>
            </div>
            <div className="flex justify-between text-lg font-bold pt-2 border-t border-border">
              <span>应付金额</span>
              <span className="text-primary">¥{formatMoney(payable)}</span>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
