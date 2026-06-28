import { useState, useMemo, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { settlementRecords } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { Plus, CheckCircle, Search, Inbox } from 'lucide-react';

interface SettlementRecord {
  id: string;
  merchant: string;
  cycle: string;
  amount: number;
  fee: number;
  status: string;
  time: string;
}

export default function SettlementManual() {
  const [records, setRecords] = useState<SettlementRecord[]>(settlementRecords as SettlementRecord[]);
  const [open, setOpen] = useState(false);
  const [merchant, setMerchant] = useState('极速云');
  const [cycle, setCycle] = useState('T+1');
  const [amount, setAmount] = useState('');
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const merchantOptions = Array.from(new Set(records.map((r) => r.merchant)));

  const statusClass = (status: string) =>
    status === 'settled' ? 'badge-success' : 'badge-warning';
  const statusText = (status: string) =>
    status === 'settled' ? '已结算' : '待处理';

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return records.filter((r) => {
      if (!q) return true;
      return [r.id, r.merchant, r.cycle, r.status, r.time].some((v) =>
        String(v).toLowerCase().includes(q)
      );
    });
  }, [records, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<SettlementRecord>({
    data: filtered,
    initialKey: 'time',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const handleConfirm = () => {
    const value = parseFloat(amount);
    if (!value || value <= 0) return;
    const fee = Math.round(value * 0.01 * 100) / 100;
    const now = new Date();
    const time = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    const newRecord: SettlementRecord = {
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
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索结算单号 / 商户 / 周期 / 状态 / 时间"
              className="input pl-8"
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>
                <SortableHeader<keyof SettlementRecord> label="结算单号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="商户" sortKey="merchant" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="周期" sortKey="cycle" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="手续费" sortKey="fee" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof SettlementRecord> label="时间" sortKey="time" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((r) => (
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

        {pagedList.length === 0 && (
          <EmptyState title="暂无结算记录" description="没有符合搜索条件的结算记录" icon={<Inbox size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
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
