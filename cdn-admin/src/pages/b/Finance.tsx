import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../components/Toast';
import { financeRecords } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { ArrowDownLeft, ArrowUpRight, Minus, Wallet } from 'lucide-react';

export default function BFinance() {
  const [activeTab, setActiveTab] = useState<'detail' | 'settlement' | 'withdraw'>('detail');
  const { show } = useToast();

  const typeIcon = (type: string) => {
    switch (type) {
      case 'income':
        return <ArrowDownLeft size={14} className="text-success" />;
      case 'expense':
      case 'frozen':
        return <ArrowUpRight size={14} className="text-danger" />;
      case 'withdraw':
        return <Minus size={14} className="text-warning" />;
      default:
        return <Wallet size={14} />;
    }
  };

  const typeText = (type: string) => {
    const map: Record<string, string> = { income: '收入', expense: '支出', frozen: '冻结', withdraw: '提现' };
    return map[type] || type;
  };

  return (
    <div>
      <PageHeader title="财务管理" breadcrumb={['财务管理', activeTab === 'detail' ? '资金明细' : activeTab === 'settlement' ? '结算记录' : '提现申请']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'detail', label: '资金明细' },
          { key: 'settlement', label: '结算记录' },
          { key: 'withdraw', label: '提现申请' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => setActiveTab(t.key as any)}
            className={`btn text-xs ${activeTab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {activeTab === 'detail' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th>流水号</th>
                <th>类型</th>
                <th>金额</th>
                <th>余额</th>
                <th>描述</th>
                <th>时间</th>
              </tr>
            </thead>
            <tbody>
              {financeRecords.map((f) => (
                <tr key={f.id}>
                  <td className="font-medium">{f.id}</td>
                  <td>
                    <div className="flex items-center gap-1.5">
                      {typeIcon(f.type)}
                      <span>{typeText(f.type)}</span>
                    </div>
                  </td>
                  <td className={f.amount > 0 ? 'text-success' : 'text-danger'}>
                    {f.amount > 0 ? '+' : ''}¥{formatMoney(Math.abs(f.amount))}
                  </td>
                  <td>¥{formatMoney(f.balance)}</td>
                  <td>{f.desc}</td>
                  <td className="text-text-secondary">{f.createdAt}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {activeTab === 'settlement' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th>结算单号</th>
                <th>结算周期</th>
                <th>结算金额</th>
                <th>手续费</th>
                <th>状态</th>
                <th>时间</th>
              </tr>
            </thead>
            <tbody>
              {[
                { no: 'S20260627001', cycle: 'T+1', amount: 4820.00, fee: 48.20, status: '已结算', time: '2026-06-28 10:00' },
                { no: 'S20260626001', cycle: 'T+1', amount: 3150.50, fee: 31.51, status: '已结算', time: '2026-06-27 10:00' },
              ].map((s, i) => (
                <tr key={i}>
                  <td className="font-medium">{s.no}</td>
                  <td>{s.cycle}</td>
                  <td>¥{formatMoney(s.amount)}</td>
                  <td>¥{formatMoney(s.fee)}</td>
                  <td><span className="badge badge-success">{s.status}</span></td>
                  <td className="text-text-secondary">{s.time}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {activeTab === 'withdraw' && (
        <div className="card p-5 max-w-lg">
          <div className="space-y-4">
            <div>
              <label className="block text-sm mb-1">可提现余额</label>
              <div className="text-2xl font-bold text-primary">¥12,850.65</div>
            </div>
            <div>
              <label className="block text-sm mb-1">提现金额</label>
              <input type="number" className="input" placeholder="请输入提现金额" />
            </div>
            <div>
              <label className="block text-sm mb-1">支付宝账号</label>
              <input className="input" placeholder="请输入支付宝账号" />
            </div>
            <div>
              <label className="block text-sm mb-1">提现密码</label>
              <input type="password" className="input" placeholder="请输入提现密码" />
            </div>
            <button onClick={() => show('提现申请已提交，等待审核', 'success')} className="btn btn-primary">提交提现申请</button>
          </div>
        </div>
      )}
    </div>
  );
}
