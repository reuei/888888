import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import EmptyState from '../../components/EmptyState';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { fetchMyPackages, updateMyPackage } from '../../services/api';
import { packages } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { RefreshCw, CreditCard, Search, RefreshCcw, Package } from 'lucide-react';
import type { MyPackage } from '../../types';

const periods = [1, 3, 6, 12];

export default function MyPackages() {
  const { show } = useToast();
  const [list, setList] = useState<MyPackage[]>([]);
  const [loading, setLoading] = useState(false);
  const [renewItem, setRenewItem] = useState<MyPackage | null>(null);
  const [months, setMonths] = useState(1);

  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const priceMap = useMemo(() => {
    const map: Record<string, number> = {};
    packages.forEach((p) => { map[p.name] = p.price; });
    return map;
  }, []);

  const filtered = useMemo(() => {
    const kw = debouncedKeyword.trim().toLowerCase();
    if (!kw) return list;
    return list.filter((item) =>
      item.id.toLowerCase().includes(kw) ||
      item.name.toLowerCase().includes(kw) ||
      item.flow.toLowerCase().includes(kw) ||
      item.bandwidth.toLowerCase().includes(kw)
    );
  }, [list, debouncedKeyword]);

  const enriched = useMemo(() =>
    filtered.map((item) => ({ ...item, price: priceMap[item.name] || 0 })),
    [filtered, priceMap]
  );

  const { sorted, sortKey, sortDirection, toggle } = useSort({
    data: enriched,
    initialKey: 'expireAt',
    initialDirection: 'asc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchMyPackages();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const openRenew = (item: MyPackage) => {
    setRenewItem(item);
    setMonths(1);
  };

  const payable = renewItem ? (priceMap[renewItem.name] || 0) * months : 0;

  const handleRenew = async () => {
    if (!renewItem || loading) return;
    const [year, month, day] = renewItem.expireAt.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    date.setMonth(date.getMonth() + months);
    const newExpireAt = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    await updateMyPackage(renewItem.id, { expireAt: newExpireAt, status: 'active' });
    await load();
    setRenewItem(null);
    show(`套餐续费成功，到期时间已延长至 ${newExpireAt}`, 'success');
  };

  const reset = () => {
    setKeyword('');
    setPage(1);
  };

  return (
    <div>
      <PageHeader title="我的套餐" breadcrumb={['套餐管理', '我的套餐']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索套餐ID / 名称 / 流量 / 带宽"
              className="input pl-8"
              value={keyword}
              onChange={(e) => { setKeyword(e.target.value); setPage(1); }}
            />
          </div>
          <button onClick={reset} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th><SortableHeader label="套餐ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th><SortableHeader label="套餐名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>流量</th>
              <th>带宽</th>
              <th>域名数</th>
              <th><SortableHeader label="到期时间" sortKey="expireAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} /></th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((item) => (
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

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

        {!loading && sorted.length === 0 && (
          <EmptyState title="暂无套餐" description="没有符合筛选条件的套餐" icon={<Package size={24} />} />
        )}

        {!loading && sorted.length > 0 && (
          <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
        )}
      </div>

      <Modal
        open={!!renewItem}
        title="套餐续费"
        onClose={() => setRenewItem(null)}
        footer={
          <>
            <button onClick={() => setRenewItem(null)} className="btn btn-default">取消</button>
            <button onClick={handleRenew} disabled={!renewItem || loading} className="btn btn-primary flex items-center gap-1 disabled:opacity-70">
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
