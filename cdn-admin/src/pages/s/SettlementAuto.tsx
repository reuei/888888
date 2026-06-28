import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { CheckCircle, Settings } from 'lucide-react';

export default function SettlementAuto() {
  const [enabled, setEnabled] = useState(true);
  const [cycle, setCycle] = useState('T+1');
  const [minAmount, setMinAmount] = useState('100');
  const [settleTime, setSettleTime] = useState('10:00');
  const [method, setMethod] = useState('alipay');
  const [saved, setSaved] = useState(false);

  const handleSave = () => {
    setSaved(true);
    setTimeout(() => setSaved(false), 2000);
  };

  return (
    <div>
      <PageHeader title="自动结算" breadcrumb={['财务管理', '自动结算']} />

      <div className="card p-5 max-w-2xl">
        <h3 className="font-semibold mb-4 flex items-center gap-2">
          <Settings size={18} className="text-primary" /> 自动结算设置
        </h3>
        <div className="space-y-5">
          <div className="flex items-center justify-between p-3 border border-border rounded">
            <span className="text-sm">自动结算开关</span>
            <label className="flex items-center gap-2 text-sm cursor-pointer">
              <input
                type="checkbox"
                checked={enabled}
                onChange={(e) => setEnabled(e.target.checked)}
              />
              <span className={enabled ? 'text-success' : 'text-text-secondary'}>
                {enabled ? '已开启' : '已关闭'}
              </span>
            </label>
          </div>

          <div>
            <label className="block text-sm mb-1">结算周期</label>
            <select className="input" value={cycle} onChange={(e) => setCycle(e.target.value)}>
              <option value="T+0">T+0</option>
              <option value="T+1">T+1</option>
              <option value="T+7">T+7</option>
            </select>
          </div>

          <div>
            <label className="block text-sm mb-1">最低结算金额（元）</label>
            <input
              type="number"
              className="input"
              value={minAmount}
              onChange={(e) => setMinAmount(e.target.value)}
            />
          </div>

          <div>
            <label className="block text-sm mb-1">结算时间</label>
            <input
              type="time"
              className="input"
              value={settleTime}
              onChange={(e) => setSettleTime(e.target.value)}
            />
          </div>

          <div>
            <label className="block text-sm mb-1">自动打款方式</label>
            <select className="input" value={method} onChange={(e) => setMethod(e.target.value)}>
              <option value="alipay">支付宝</option>
              <option value="wxpay">微信支付</option>
            </select>
          </div>

          <div className="flex items-center gap-3 pt-2">
            <button onClick={handleSave} className="btn btn-primary flex items-center gap-1">
              <CheckCircle size={16} /> 保存设置
            </button>
            {saved && <span className="text-sm text-success">保存成功</span>}
          </div>
        </div>
      </div>
    </div>
  );
}
