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
import { fetchMyPackages, createMyPackage, updateMyPackage, createBOrder } from '../../services/api';
import { packages } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { ShoppingCart, Check, Search, RefreshCcw, Package, Loader2, CreditCard } from 'lucide-react';
import type { Package as PackageType, MyPackage } from '../../types';

export default function BPackages() {
  const { show } = useToast();
  const [activeTab, setActiveTab] = useState<'buy' | 'my' | 'renew'>('buy');
  const [buyOpen, setBuyOpen] = useState(false);
  const [selected, setSelected] = useState<PackageType | null>(null);
  const [myPackages, setMyPackages] = useState<MyPackage[]>([]);
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const openBuy = (p: PackageType) => {
    setSelected(p);
    setBuyOpen(true);
  };

  // 在线订购
  const [buyKeyword, setBuyKeyword] = useState('');
  const debouncedBuyKeyword = useDebounce(buyKeyword);
  const filteredPackages = useMemo(() => {
    const kw = debouncedBuyKeyword.trim().toLowerCase();
    if (!kw) return packages;
    return packages.filter((p) => p.name.toLowerCase().includes(kw) || p.id.toLowerCase().includes(kw));
  }, [debouncedBuyKeyword]);
  const {
    sorted: sortedPackages,
    sortKey: pkgSortKey,
    sortDirection: pkgSortDirection,
    toggle: togglePkgSort,
  } = useSort({ data: filteredPackages, initialKey: 'price', initialDirection: 'asc' });
  const {
    page: buyPage,
    pageSize: buyPageSize,
    totalPages: buyTotalPages,
    slice: buySlice,
    setPage: setBuyPage,
  } = usePagination({ total: sortedPackages.length, pageSize: 8 });
  const pagedPackages = buySlice(sortedPackages);

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchMyPackages();
    setMyPackages(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  // 我的套餐
  const [myKeyword, setMyKeyword] = useState('');
  const debouncedMyKeyword = useDebounce(myKeyword);
  const filteredMy = useMemo(() => {
    const kw = debouncedMyKeyword.trim().toLowerCase();
    if (!kw) return myPackages;
    return myPackages.filter((p) => p.name.toLowerCase().includes(kw) || p.id.toLowerCase().includes(kw));
  }, [debouncedMyKeyword, myPackages]);
  const {
    sorted: sortedMy,
    sortKey: mySortKey,
    sortDirection: mySortDirection,
    toggle: toggleMySort,
  } = useSort({ data: filteredMy, initialKey: 'expireAt', initialDirection: 'asc' });
  const {
    page: myPage,
    pageSize: myPageSize,
    totalPages: myTotalPages,
    slice: mySlice,
    setPage: setMyPage,
  } = usePagination({ total: sortedMy.length });
  const pagedMy = mySlice(sortedMy);

  // 续费
  const [renewPackageId, setRenewPackageId] = useState(myPackages[0]?.id || '');
  const [renewMonths, setRenewMonths] = useState(1);
  const renewItem = myPackages.find((p) => p.id === renewPackageId);
  const renewPrice = useMemo(() => {
    if (!renewItem) return 0;
    return packages.find((p) => p.name === renewItem.name)?.price || 0;
  }, [renewItem]);
  const renewPayable = renewPrice * renewMonths;

  const resetBuy = () => {
    setBuyKeyword('');
    setBuyPage(1);
  };

  const resetMy = () => {
    setMyKeyword('');
    setMyPage(1);
  };

  const handleBuy = async () => {
    if (!selected || submitting) return;
    setSubmitting(true);
    const now = new Date();
    const createdAt = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')} ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    const expireAt = `${now.getFullYear() + 1}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    await Promise.all([
      createBOrder({
        product: selected.name,
        amount: selected.price,
        status: 'paid',
        createdAt,
        paidAt: createdAt,
        packageId: selected.id,
        period: `1${selected.period}`,
      }),
      createMyPackage({
        name: selected.name,
        flow: selected.flow,
        bandwidth: selected.bandwidth,
        domains: selected.domains,
        expireAt,
        status: 'active',
      }),
    ]);
    await load();
    setBuyOpen(false);
    setSubmitting(false);
    setActiveTab('my');
    show(`套餐 ${selected.name} 购买成功`, 'success');
  };

  const handleRenewPay = async () => {
    if (!renewItem || submitting) return;
    setSubmitting(true);
    const [year, month, day] = renewItem.expireAt.split('-').map(Number);
    const date = new Date(year, month - 1, day);
    date.setMonth(date.getMonth() + renewMonths);
    const newExpireAt = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    await updateMyPackage(renewItem.id, { expireAt: newExpireAt, status: 'active' });
    await load();
    setSubmitting(false);
    show(`续费支付成功，到期时间已延长至 ${newExpireAt}`, 'success');
  };

  const statusBadge = (status: MyPackage['status']) => {
    switch (status) {
      case 'active':
        return 'badge-success';
      case 'expired':
        return 'badge-danger';
      case 'pending':
        return 'badge-warning';
      default:
        return 'badge-default';
    }
  };

  const statusText = (status: MyPackage['status']) => {
    const map: Record<string, string> = { active: '生效中', expired: '已过期', pending: '待生效' };
    return map[status] || status;
  };

  return (
    <div>
      <PageHeader title="套餐管理" breadcrumb={['套餐管理', activeTab === 'buy' ? '在线订购套餐' : activeTab === 'my' ? '我的套餐' : '套餐续费']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'buy', label: '在线订购套餐' },
          { key: 'my', label: '我的套餐' },
          { key: 'renew', label: '套餐续费' },
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

      {activeTab === 'buy' && (
        <div className="card p-5">
          <div className="flex flex-wrap gap-3 mb-4">
            <div className="relative flex-1 min-w-[200px]">
              <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
              <input
                type="text"
                placeholder="搜索套餐名称 / 套餐ID"
                className="input pl-8"
                value={buyKeyword}
                onChange={(e) => { setBuyKeyword(e.target.value); setBuyPage(1); }}
              />
            </div>
            <button onClick={resetBuy} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
            <div className="flex items-center gap-2 text-sm text-text-secondary">
              <span>排序：</span>
              <SortableHeader label="价格" sortKey="price" activeKey={pkgSortKey} direction={pkgSortDirection} onSort={togglePkgSort} />
              <SortableHeader label="名称" sortKey="name" activeKey={pkgSortKey} direction={pkgSortDirection} onSort={togglePkgSort} />
            </div>
          </div>

          {pagedPackages.length > 0 ? (
            <>
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                {pagedPackages.map((p) => (
                  <div key={p.id} className="card p-5 flex flex-col">
                    <h3 className="text-lg font-bold">{p.name}</h3>
                    <div className="text-3xl font-bold text-primary my-3">
                      ¥{formatMoney(p.price)}<span className="text-sm text-text-secondary font-normal">/{p.period}</span>
                    </div>
                    <ul className="space-y-2 text-sm text-text-secondary flex-1 mb-4">
                      <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 流量 {p.flow}</li>
                      <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 带宽 {p.bandwidth}</li>
                      <li className="flex items-center gap-2"><Check size={14} className="text-success" /> 域名数 {p.domains} 个</li>
                      <li className="flex items-center gap-2"><Check size={14} className="text-success" /> CC 基础防护</li>
                    </ul>
                    <button onClick={() => openBuy(p)} className="btn btn-primary flex items-center justify-center gap-1">
                      <ShoppingCart size={16} /> 立即购买
                    </button>
                  </div>
                ))}
              </div>
              <Pagination page={buyPage} totalPages={buyTotalPages} total={sortedPackages.length} pageSize={buyPageSize} onChange={setBuyPage} />
            </>
          ) : (
            <EmptyState
              title="暂无套餐"
              description="没有符合搜索条件的套餐"
              icon={<Package size={24} />}
              action={
                <button onClick={resetBuy} className="btn btn-primary text-xs flex items-center gap-1">
                  <RefreshCcw size={14} /> 重置搜索
                </button>
              }
            />
          )}
        </div>
      )}

      {activeTab === 'my' && (
        <div className="card p-5">
          <div className="flex flex-wrap gap-3 mb-4">
            <div className="relative flex-1 min-w-[200px]">
              <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
              <input
                type="text"
                placeholder="搜索套餐名称 / 套餐ID"
                className="input pl-8"
                value={myKeyword}
                onChange={(e) => { setMyKeyword(e.target.value); setMyPage(1); }}
              />
            </div>
            <button onClick={resetMy} className="btn btn-default flex items-center gap-1"><RefreshCcw size={14} /> 重置</button>
          </div>

          <table className="table">
            <thead>
              <tr>
                <th><SortableHeader label="套餐ID" sortKey="id" activeKey={mySortKey} direction={mySortDirection} onSort={toggleMySort} /></th>
                <th><SortableHeader label="套餐名称" sortKey="name" activeKey={mySortKey} direction={mySortDirection} onSort={toggleMySort} /></th>
                <th>流量</th>
                <th>带宽</th>
                <th>域名数</th>
                <th><SortableHeader label="到期时间" sortKey="expireAt" activeKey={mySortKey} direction={mySortDirection} onSort={toggleMySort} /></th>
                <th>状态</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              {pagedMy.map((item) => (
                <tr key={item.id}>
                  <td className="font-medium">{item.id}</td>
                  <td>{item.name}</td>
                  <td>{item.flow}</td>
                  <td>{item.bandwidth}</td>
                  <td>{item.domains}</td>
                  <td>{item.expireAt}</td>
                  <td><span className={`badge ${statusBadge(item.status)}`}>{statusText(item.status)}</span></td>
                  <td>
                    <button onClick={() => { setActiveTab('renew'); setRenewPackageId(item.id); }} className="btn btn-default text-xs">续费</button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}

          {!loading && sortedMy.length === 0 && (
            <EmptyState title="暂无套餐" description="您还没有购买套餐" icon={<Package size={24} />} />
          )}

          {!loading && sortedMy.length > 0 && (
            <Pagination page={myPage} totalPages={myTotalPages} total={sortedMy.length} pageSize={myPageSize} onChange={setMyPage} />
          )}
        </div>
      )}

      {activeTab === 'renew' && (
        <div className="card p-5">
          <div className="space-y-4 max-w-lg">
            <div>
              <label className="block text-sm mb-1">选择套餐</label>
              <select
                className="input"
                value={renewPackageId}
                onChange={(e) => setRenewPackageId(e.target.value)}
              >
                {myPackages.map((p) => (
                  <option key={p.id} value={p.id}>
                    {p.name} - 到期 {p.expireAt}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm mb-1">续费周期</label>
              <select
                className="input"
                value={renewMonths}
                onChange={(e) => setRenewMonths(Number(e.target.value))}
              >
                {[1, 3, 6, 12].map((m) => (
                  <option key={m} value={m}>{m} 个月</option>
                ))}
              </select>
            </div>
            <div className="text-lg font-bold text-primary">应付：¥{formatMoney(renewPayable)}</div>
            <button onClick={handleRenewPay} disabled={submitting || !renewItem} className="btn btn-primary flex items-center gap-1 disabled:opacity-70">
              {submitting ? <Loader2 size={16} className="animate-spin" /> : <CreditCard size={16} />}
              立即支付
            </button>
          </div>
        </div>
      )}

      <Modal
        open={buyOpen}
        title="确认订单"
        onClose={() => setBuyOpen(false)}
        footer={
          <>
            <button onClick={() => setBuyOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleBuy} disabled={submitting || !selected} className="btn btn-primary flex items-center gap-1 disabled:opacity-70">
              {submitting ? <Loader2 size={16} className="animate-spin" /> : <ShoppingCart size={16} />}
              立即支付
            </button>
          </>
        }
      >
        {selected && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between"><span className="text-text-secondary">套餐</span><span>{selected.name}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">流量</span><span>{selected.flow}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">带宽</span><span>{selected.bandwidth}</span></div>
            <div className="flex justify-between"><span className="text-text-secondary">周期</span><span>1{selected.period}</span></div>
            <div className="flex justify-between text-lg font-bold pt-2 border-t border-border">
              <span>应付金额</span>
              <span className="text-primary">¥{formatMoney(selected.price)}</span>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
