import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { fetchFinanceRecords, fetchSettlementRecords } from '../../services/api';
import { formatMoney } from '../../utils/helpers';
import { trendLabels, trendValues } from '../../data/mock';
import LineChart from '../../components/LineChart';
import { Plus, CheckCircle, Receipt } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import { usePagination } from '../../hooks/usePagination';
import type { FinanceRecord, SettlementRecord } from '../../types';

export default function SFinance() {
  const [tab, setTab] = useState<'overview' | 'rates' | 'settlement'>('overview');
  const [settleOpen, setSettleOpen] = useState(false);
  const [financeRecords, setFinanceRecords] = useState<FinanceRecord[]>([]);
  const [settlementRecords, setSettlementRecords] = useState<SettlementRecord[]>([]);
  const [loading, setLoading] = useState(false);
  const [settlementLoading, setSettlementLoading] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchFinanceRecords();
    setFinanceRecords(data);
    setLoading(false);
  }, []);

  const loadSettlements = useCallback(async () => {
    setSettlementLoading(true);
    const data = await fetchSettlementRecords();
    setSettlementRecords(data);
    setSettlementLoading(false);
  }, []);

  useEffect(() => {
    load();
    loadSettlements();
  }, [load, loadSettlements]);

  const overviewPagination = usePagination({ total: financeRecords.length });
  const settlementPagination = usePagination({ total: settlementRecords.length });

  const typeText = (type: string) => {
    const map: Record<string, string> = { income: '收入', expense: '支出', frozen: '冻结', withdraw: '提现' };
    return map[type] || type;
  };

  return (
    <div>
      <PageHeader title="财务管理" breadcrumb={['财务管理', '资金流水总览']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'overview', label: '资金流水总览' },
          { key: 'rates', label: '费率与通道' },
          { key: 'settlement', label: '结算打款' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => setTab(t.key as any)}
            className={`btn text-xs ${tab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab === 'overview' && (
        <>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {[
              { title: '平台总资金', value: '3,284,592.00', color: 'primary' },
              { title: '今日收入', value: '18,492.50', color: 'success' },
              { title: '今日支出', value: '5,230.00', color: 'danger' },
              { title: '冻结金额', value: '42,100.00', color: 'warning' },
            ].map((s, i) => (
              <div key={i} className={`card p-5 border-l-4 ${s.color === 'primary' ? 'border-l-primary' : s.color === 'success' ? 'border-l-success' : s.color === 'danger' ? 'border-l-danger' : 'border-l-warning'}`}>
                <div className="text-sm text-text-secondary mb-1">{s.title}</div>
                <div className="text-2xl font-bold">¥{s.value}</div>
              </div>
            ))}
          </div>

          <div className="card p-5 mb-6">
            <h3 className="font-semibold mb-4">资金趋势</h3>
            <LineChart labels={trendLabels} datasets={[{ label: '净流入（千元）', values: trendValues, color: '#2196F3' }]} />
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4">资金明细</h3>
            <table className="table">
              <thead>
                <tr>
                  <th>流水号</th>
                  <th>类型</th>
                  <th>金额</th>
                  <th>描述</th>
                  <th>时间</th>
                </tr>
              </thead>
              <tbody>
                {overviewPagination.slice(financeRecords).map((f) => (
                  <tr key={f.id}>
                    <td className="font-medium">{f.id}</td>
                    <td>{typeText(f.type)}</td>
                    <td className={f.amount > 0 ? 'text-success' : 'text-danger'}>
                      {f.amount > 0 ? '+' : ''}¥{formatMoney(Math.abs(f.amount))}
                    </td>
                    <td>{f.desc}</td>
                    <td className="text-text-secondary">{f.createdAt}</td>
                  </tr>
                ))}
              </tbody>
            </table>

            {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

            {!loading && financeRecords.length === 0 && (
              <EmptyState title="暂无资金明细" description="当前没有资金流水记录" icon={<Receipt size={24} />} />
            )}

            {!loading && financeRecords.length > 0 && (
              <Pagination
                page={overviewPagination.page}
                totalPages={overviewPagination.totalPages}
                total={financeRecords.length}
                pageSize={overviewPagination.pageSize}
                onChange={overviewPagination.setPage}
              />
            )}
          </div>
        </>
      )}

      {tab === 'rates' && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div className="card p-5">
            <h3 className="font-semibold mb-4">费率分组</h3>
            <table className="table">
              <thead>
                <tr>
                  <th>分组名称</th>
                  <th>默认费率</th>
                  <th>结算周期</th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {[
                  { name: '默认分组', rate: '2.0%', cycle: 'T+1' },
                  { name: 'VIP 分组', rate: '1.5%', cycle: 'T+0' },
                  { name: '普通分组', rate: '2.5%', cycle: 'T+7' },
                ].map((g, i) => (
                  <tr key={i}>
                    <td className="font-medium">{g.name}</td>
                    <td>{g.rate}</td>
                    <td>{g.cycle}</td>
                    <td><button className="btn btn-default text-xs">编辑</button></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4">支付通道开关</h3>
            <div className="space-y-3">
              {['微信支付', '支付宝', '易支付', 'USDT', '码支付'].map((p) => (
                <div key={p} className="flex items-center justify-between p-3 border border-border rounded">
                  <span>{p}</span>
                  <div className="flex items-center gap-2">
                    <label className="flex items-center gap-1 text-sm text-text-secondary"><input type="checkbox" defaultChecked /> 默认</label>
                    <label className="flex items-center gap-1 text-sm text-text-secondary"><input type="checkbox" defaultChecked /> VIP</label>
                    <label className="flex items-center gap-1 text-sm text-text-secondary"><input type="checkbox" /> 普通</label>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}

      {tab === 'settlement' && (
        <div className="card p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold">结算记录</h3>
            <button onClick={() => setSettleOpen(true)} className="btn btn-primary flex items-center gap-1">
              <Plus size={16} /> 手动结算
            </button>
          </div>
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
              {settlementPagination.slice(settlementRecords).map((s) => (
                <tr key={s.id}>
                  <td className="font-medium">{s.id}</td>
                  <td>{s.merchant}</td>
                  <td>{s.cycle}</td>
                  <td>¥{formatMoney(s.amount)}</td>
                  <td>¥{formatMoney(s.fee)}</td>
                  <td>
                    <span className={`badge ${s.status === 'settled' ? 'badge-success' : 'badge-warning'}`}>
                      {s.status === 'settled' ? '已结算' : '待处理'}
                    </span>
                  </td>
                  <td className="text-text-secondary">{s.time}</td>
                </tr>
              ))}
            </tbody>
          </table>

          {settlementLoading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

          {!settlementLoading && settlementRecords.length === 0 && (
            <EmptyState title="暂无结算记录" description="当前没有结算打款记录" icon={<Receipt size={24} />} />
          )}

          {!settlementLoading && settlementRecords.length > 0 && (
            <Pagination
              page={settlementPagination.page}
              totalPages={settlementPagination.totalPages}
              total={settlementRecords.length}
              pageSize={settlementPagination.pageSize}
              onChange={settlementPagination.setPage}
            />
          )}
        </div>
      )}

      <Modal
        open={settleOpen}
        title="手动结算"
        onClose={() => setSettleOpen(false)}
        footer={
          <>
            <button onClick={() => setSettleOpen(false)} className="btn btn-default">取消</button>
            <button onClick={() => setSettleOpen(false)} className="btn btn-success flex items-center gap-1">
              <CheckCircle size={16} /> 确认结算
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">选择商户</label>
            <select className="input">
              <option>极速云</option>
              <option>蓝海防护</option>
              <option>站点卫士</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">结算账单</label>
            <select className="input">
              <option>2026-06-28 账单 ¥4820.00</option>
              <option>2026-06-27 账单 ¥3150.50</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">打款方式</label>
            <select className="input">
              <option>支付宝批量打款</option>
              <option>自动结算</option>
            </select>
          </div>
        </div>
      </Modal>
    </div>
  );
}
