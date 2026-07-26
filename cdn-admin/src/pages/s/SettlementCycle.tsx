import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';

const SETTLE_OPTIONS = [
  { value: 't0', label: 'T+0（实时结算）' },
  { value: 't1', label: 'T+1（次日结算）' },
  { value: 't7', label: 'T+7（周结算）' },
];

export default function SSettlementCycle() {
  const { show } = useToast();
  const [form, setForm] = useState({
    cycle: 't1',
    minWithdraw: '100',
    autoSettleTime: '10:00',
  });

  const handleSave = () => {
    const min = parseFloat(form.minWithdraw);
    if (isNaN(min) || min < 0) {
      show('最小提现金额应为非负数', 'warning');
      return;
    }
    if (!/^\d{2}:\d{2}$/.test(form.autoSettleTime)) {
      show('自动结算时间格式应为 HH:mm', 'warning');
      return;
    }
    const cycleText = SETTLE_OPTIONS.find((s) => s.value === form.cycle)?.label || '';
    show(`结算配置已保存：${cycleText} / 起提 ¥${min} / ${form.autoSettleTime}`, 'success');
  };

  return (
    <div>
      <PageHeader title="结算周期设置" breadcrumb={['财务管理', '结算周期设置']} />

      <div className="card p-6 max-w-2xl">
        <div className="space-y-5">
          <div>
            <label className="block text-sm mb-1">默认结算周期</label>
            <select
              className="input"
              value={form.cycle}
              onChange={(e) => setForm({ ...form, cycle: e.target.value })}
            >
              {SETTLE_OPTIONS.map((s) => (
                <option key={s.value} value={s.value}>
                  {s.label}
                </option>
              ))}
            </select>
            <div className="text-xs text-text-secondary mt-1">
              新商户默认使用此结算周期，可后续在「单商户费率」中单独调整。
            </div>
          </div>

          <div>
            <label className="block text-sm mb-1">最小提现金额（元）</label>
            <input
              type="number"
              min="0"
              step="1"
              className="input"
              value={form.minWithdraw}
              onChange={(e) => setForm({ ...form, minWithdraw: e.target.value })}
              placeholder="例如 100"
            />
            <div className="text-xs text-text-secondary mt-1">
              商户余额低于此金额时无法发起提现申请。
            </div>
          </div>

          <div>
            <label className="block text-sm mb-1">自动结算时间（HH:mm）</label>
            <input
              type="text"
              className="input"
              value={form.autoSettleTime}
              onChange={(e) => setForm({ ...form, autoSettleTime: e.target.value })}
              placeholder="例如 10:00"
            />
            <div className="text-xs text-text-secondary mt-1">
              系统将在此时间自动发起结算打款，建议设置在凌晨低峰期。
            </div>
          </div>

          <div className="flex gap-2 pt-2">
            <button onClick={handleSave} className="btn btn-primary">
              保存
            </button>
            <button
              onClick={() =>
                setForm({ cycle: 't1', minWithdraw: '100', autoSettleTime: '10:00' })
              }
              className="btn btn-default"
            >
              重置
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
