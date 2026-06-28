import { useState, useMemo } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { myPackages as myPackagesData, packages } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { CreditCard, CheckCircle } from 'lucide-react';

const periods = [1, 3, 6, 12];

export default function Renew() {
  const activePackages = useMemo(
    () => myPackagesData.filter((p) => p.status === 'active'),
    []
  );
  const [selectedId, setSelectedId] = useState(activePackages[0]?.id || '');
  const [months, setMonths] = useState(1);
  const [payOpen, setPayOpen] = useState(false);

  const selectedPackage = activePackages.find((p) => p.id === selectedId);
  const unitPrice = selectedPackage
    ? packages.find((p) => p.name === selectedPackage.name)?.price || 0
    : 0;
  const payable = unitPrice * months;

  const handlePay = () => {
    setPayOpen(false);
  };

  return (
    <div>
      <PageHeader title="套餐续费" breadcrumb={['套餐管理', '套餐续费']} />

      <div className="card p-5 max-w-lg">
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">选择套餐</label>
            <select
              value={selectedId}
              onChange={(e) => setSelectedId(e.target.value)}
              className="input"
            >
              {activePackages.length === 0 && (
                <option value="">暂无生效套餐</option>
              )}
              {activePackages.map((p) => (
                <option key={p.id} value={p.id}>
                  {p.name}（到期 {p.expireAt}）
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block text-sm mb-1">续费周期</label>
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

          <div className="text-lg font-bold text-primary">
            应付：¥{formatMoney(payable)}
          </div>

          <button
            onClick={() => setPayOpen(true)}
            disabled={!selectedPackage}
            className="btn btn-primary flex items-center gap-1 disabled:opacity-50"
          >
            <CreditCard size={16} /> 立即支付
          </button>
        </div>
      </div>

      <Modal
        open={payOpen}
        title="支付确认"
        onClose={() => setPayOpen(false)}
        footer={
          <>
            <button onClick={() => setPayOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handlePay} className="btn btn-success flex items-center gap-1">
              <CheckCircle size={16} /> 确认支付
            </button>
          </>
        }
      >
        {selectedPackage && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-text-secondary">套餐</span>
              <span>{selectedPackage.name}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">续费周期</span>
              <span>{months} 个月</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">到期时间</span>
              <span>{selectedPackage.expireAt}</span>
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
