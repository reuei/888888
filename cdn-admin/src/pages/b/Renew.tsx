import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../components/Toast';
import { fetchMyPackages, updateMyPackage } from '../../services/api';
import { packages } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { CreditCard, CheckCircle, PackageX, Loader2 } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import type { MyPackage } from '../../types';

const periods = [1, 3, 6, 12];

export default function Renew() {
  const { show } = useToast();
  const [myPackages, setMyPackages] = useState<MyPackage[]>([]);
  const [loading, setLoading] = useState(false);
  const [paying, setPaying] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchMyPackages();
    setMyPackages(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const activePackages = useMemo(
    () => myPackages.filter((p) => p.status === 'active'),
    [myPackages]
  );
  const [selectedId, setSelectedId] = useState(activePackages[0]?.id || '');
  const [months, setMonths] = useState(1);
  const [payOpen, setPayOpen] = useState(false);

  useEffect(() => {
    if (activePackages.length > 0 && !selectedId) {
      setSelectedId(activePackages[0].id);
    }
  }, [activePackages, selectedId]);

  const selectedPackage = activePackages.find((p) => p.id === selectedId);
  const unitPrice = selectedPackage
    ? packages.find((p) => p.name === selectedPackage.name)?.price || 0
    : 0;
  const payable = unitPrice * months;

  const handlePay = async () => {
    if (!selectedPackage || paying) return;
    setPaying(true);
    const [year, month, day] = selectedPackage.expireAt.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    date.setMonth(date.getMonth() + months);
    const newExpireAt = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    await updateMyPackage(selectedPackage.id, { expireAt: newExpireAt, status: 'active' });
    await load();
    setPayOpen(false);
    setPaying(false);
    show(`套餐续费支付成功，到期时间已延长至 ${newExpireAt}`, 'success');
  };

  return (
    <div>
      <PageHeader title="套餐续费" breadcrumb={['套餐管理', '套餐续费']} />

      {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

      {!loading && activePackages.length === 0 ? (
        <EmptyState
          title="暂无生效套餐"
          description="您当前没有可续费的生效套餐，请先订购套餐"
          icon={<PackageX size={24} />}
          action={
            <button onClick={() => window.location.href = '/b/packages'} className="btn btn-primary text-xs flex items-center gap-1">
              前往订购套餐
            </button>
          }
        />
      ) : (
      <div className="card p-5 max-w-lg">
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">选择套餐</label>
            <select
              value={selectedId}
              onChange={(e) => setSelectedId(e.target.value)}
              className="input"
            >
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
      )}

      <Modal
        open={payOpen}
        title="支付确认"
        onClose={() => setPayOpen(false)}
        footer={
          <>
            <button onClick={() => setPayOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handlePay} disabled={paying} className="btn btn-success flex items-center gap-1 disabled:opacity-70">
              {paying ? <Loader2 size={16} className="animate-spin" /> : <CheckCircle size={16} />}
              确认支付
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
