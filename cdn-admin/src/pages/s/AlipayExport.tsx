import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchSettlementRecords } from '../../services/api';
import { formatMoney } from '../../utils/helpers';
import { FileDown, Download, Search, Inbox } from 'lucide-react';

interface SettlementItem {
  id: string;
  merchant: string;
  cycle: string;
  amount: number;
  fee: number;
  status: string;
  time: string;
}

const alipayMap: Record<string, { account: string; name: string }> = {
  极速云: { account: 'jsyun@example.com', name: '极速云网络' },
  蓝海防护: { account: 'lhshield@example.com', name: '蓝海防护科技' },
  站点卫士: { account: 'zdweishi@example.com', name: '站点卫士' },
};

export default function AlipayExport() {
  const [records, setRecords] = useState<SettlementItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [selected, setSelected] = useState<string[]>([]);
  const [preview, setPreview] = useState(false);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchSettlementRecords();
    setRecords(data as SettlementItem[]);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const pending = useMemo(
    () => records.filter((r) => r.status === 'pending'),
    [records]
  );

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return pending.filter((r) => {
      if (!q) return true;
      return [r.id, r.merchant, r.cycle, r.time].some((v) => String(v).toLowerCase().includes(q));
    });
  }, [pending, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle: toggleSort } = useSort<SettlementItem>({
    data: filtered,
    initialKey: 'time',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const toggleSelect = (id: string) => {
    setSelected((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    );
  };

  const toggleAll = () => {
    setSelected(
      selected.length === sorted.length ? [] : sorted.map((r) => r.id)
    );
  };

  const rows = useMemo(
    () =>
      pending
        .filter((r) => selected.includes(r.id))
        .map((r) => {
          const info = alipayMap[r.merchant] || { account: '', name: r.merchant };
          return {
            account: info.account,
            name: info.name,
            amount: formatMoney(r.amount),
          };
        }),
    [pending, selected]
  );

  const handleDownload = () => {
    const header = 'alipay_account,real_name,amount';
    const body = rows.map((r) => `${r.account},${r.name},${r.amount}`).join('\n');
    const blob = new Blob([header + '\n' + body], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `alipay_export_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <div>
      <PageHeader title="支付宝打款导出" breadcrumb={['财务管理', '支付宝打款导出']} />

      <div className="card p-5 mb-6">
        <div className="flex items-center justify-between mb-4 flex-wrap gap-3">
          <h3 className="font-semibold">待结算记录</h3>
          <button
            onClick={() => setPreview(true)}
            disabled={selected.length === 0}
            className="btn btn-primary flex items-center gap-1 disabled:opacity-50"
          >
            <FileDown size={16} /> 生成导出文件
          </button>
        </div>

        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索结算单号 / 商户 / 周期 / 时间"
              className="input pl-8"
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>
                <input
                  type="checkbox"
                  checked={sorted.length > 0 && selected.length === sorted.length}
                  onChange={toggleAll}
                />
              </th>
              <th>
                <SortableHeader<keyof SettlementItem> label="结算单号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggleSort} />
              </th>
              <th>
                <SortableHeader<keyof SettlementItem> label="商户" sortKey="merchant" activeKey={sortKey} direction={sortDirection} onSort={toggleSort} />
              </th>
              <th>
                <SortableHeader<keyof SettlementItem> label="金额" sortKey="amount" activeKey={sortKey} direction={sortDirection} onSort={toggleSort} />
              </th>
              <th>
                <SortableHeader<keyof SettlementItem> label="时间" sortKey="time" activeKey={sortKey} direction={sortDirection} onSort={toggleSort} />
              </th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((r) => (
              <tr key={r.id}>
                <td>
                  <input
                    type="checkbox"
                    checked={selected.includes(r.id)}
                    onChange={() => toggleSelect(r.id)}
                  />
                </td>
                <td className="font-medium">{r.id}</td>
                <td>{r.merchant}</td>
                <td>¥{formatMoney(r.amount)}</td>
                <td className="text-text-secondary">{r.time}</td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && pagedList.length === 0 && (
          <EmptyState title="暂无待结算记录" description="没有符合搜索条件的待结算记录" icon={<Inbox size={24} />} />
        )}

        {!loading && pagedList.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>

      {preview && (
        <div className="card p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold">导出预览</h3>
            <button onClick={handleDownload} className="btn btn-success flex items-center gap-1">
              <Download size={16} /> 下载导出文件
            </button>
          </div>
          <div className="overflow-x-auto">
            <table className="table">
              <thead>
                <tr>
                  <th>alipay_account</th>
                  <th>real_name</th>
                  <th>amount</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((r, i) => (
                  <tr key={i}>
                    <td>{r.account}</td>
                    <td>{r.name}</td>
                    <td>{r.amount}</td>
                  </tr>
                ))}
              </tbody>
            </table>
            {rows.length === 0 && (
              <EmptyState title="未选择记录" description="请先勾选需要导出的结算记录" icon={<Inbox size={24} />} />
            )}
          </div>
        </div>
      )}
    </div>
  );
}
