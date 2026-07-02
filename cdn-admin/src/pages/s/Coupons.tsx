import { useState, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import EmptyState from '../../components/EmptyState';
import Pagination from '../../components/Pagination';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { useToast } from '../../hooks/useToast';
import * as api from '../../services/api';
import { couponRecords } from '../../data/mock';
import type { Coupon } from '../../types';
import { Plus, Search, Ticket, Tag } from 'lucide-react';

export default function SCoupons() {
  const { show } = useToast();
  const [tab, setTab] = useState<'generate' | 'records' | 'stats'>('generate');
  const [list, setList] = useState<Coupon[]>([]);
  const [records] = useState(couponRecords);
  const [loading, setLoading] = useState(true);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ batch: '', type: 'fixed', value: 0, threshold: 0, total: 100, limitPerUser: 1 });

  const load = async () => {
    setLoading(true);
    const data = await api.fetchCoupons();
    setList(data);
    setLoading(false);
  };

  useEffect(() => {
    load();
  }, []);

  const filteredCoupons = list.filter((c) => {
    const q = debouncedKeyword.trim().toLowerCase();
    if (!q || tab !== 'generate') return true;
    return c.batch.toLowerCase().includes(q) || c.type.toLowerCase().includes(q);
  });

  const filteredRecords = records.filter((r) => {
    const q = debouncedKeyword.trim().toLowerCase();
    if (!q || tab !== 'records') return true;
    return (
      r.code.toLowerCase().includes(q) ||
      r.user.toLowerCase().includes(q) ||
      r.order.toLowerCase().includes(q)
    );
  });

  const {
    sorted: sortedCoupons,
    sortKey: couponSortKey,
    sortDirection: couponSortDirection,
    toggle: toggleCouponSort,
  } = useSort({ data: filteredCoupons, initialKey: 'value' });

  const {
    sorted: sortedRecords,
    sortKey: recordSortKey,
    sortDirection: recordSortDirection,
    toggle: toggleRecordSort,
  } = useSort({ data: filteredRecords, initialKey: 'usedAt', initialDirection: 'desc' });

  const couponPagination = usePagination({ total: sortedCoupons.length });
  const recordPagination = usePagination({ total: sortedRecords.length });

  const pagedCoupons = couponPagination.slice(sortedCoupons);
  const pagedRecords = recordPagination.slice(sortedRecords);

  const handleAdd = async () => {
    await api.createCoupon({
      batch: form.batch,
      type: form.type as 'fixed' | 'percent',
      value: form.value,
      threshold: form.threshold,
      total: form.total,
      status: 'active',
    });
    await load();
    show('优惠券生成成功', 'success');
    setModalOpen(false);
    setForm({ batch: '', type: 'fixed', value: 0, threshold: 0, total: 100, limitPerUser: 1 });
  };

  if (loading) return <Loading />;

  return (
    <div>
      <PageHeader
        title="优惠券生成"
        breadcrumb={['优惠券/营销管理', '优惠券生成']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 生成优惠券
          </button>
        }
      />

      <div className="flex flex-wrap gap-2 mb-6">
        {[
          { key: 'generate', label: '优惠券生成' },
          { key: 'records', label: '发放/核销记录' },
          { key: 'stats', label: '营销效果统计' },
        ].map((t) => (
          <button
            key={t.key}
            onClick={() => {
              setTab(t.key as any);
              setKeyword('');
            }}
            className={`btn text-xs ${tab === t.key ? 'btn-primary' : 'btn-default'}`}
          >
            {t.label}
          </button>
        ))}
      </div>

      {tab !== 'stats' && (
        <div className="card p-5 mb-4">
          <div className="relative max-w-md">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder={tab === 'generate' ? '搜索批次号' : '搜索券码 / 用户 / 订单'}
              className="input pl-9 w-full"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
        </div>
      )}

      {tab === 'generate' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th><SortableHeader label="批次号" sortKey="batch" activeKey={couponSortKey} direction={couponSortDirection} onSort={toggleCouponSort} /></th>
                <th>类型</th>
                <th><SortableHeader label="面额 / 折扣" sortKey="value" activeKey={couponSortKey} direction={couponSortDirection} onSort={toggleCouponSort} /></th>
                <th><SortableHeader label="使用门槛" sortKey="threshold" activeKey={couponSortKey} direction={couponSortDirection} onSort={toggleCouponSort} /></th>
                <th><SortableHeader label="总量" sortKey="total" activeKey={couponSortKey} direction={couponSortDirection} onSort={toggleCouponSort} /></th>
                <th><SortableHeader label="已领取" sortKey="received" activeKey={couponSortKey} direction={couponSortDirection} onSort={toggleCouponSort} /></th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              {pagedCoupons.map((c) => (
                <tr key={c.id}>
                  <td className="font-medium">{c.batch}</td>
                  <td>{c.type === 'fixed' ? '固定金额' : '百分比折扣'}</td>
                  <td>{c.type === 'fixed' ? `¥${c.value}` : `${c.value}%`}</td>
                  <td>满 ¥{c.threshold}</td>
                  <td>{c.total}</td>
                  <td>{c.received}</td>
                  <td>
                    <span className={`badge ${c.status === 'active' ? 'badge-success' : 'badge-default'}`}>
                      {c.status === 'active' ? '进行中' : '已过期'}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>

          {filteredCoupons.length === 0 && (
            <EmptyState title="暂无优惠券" description="没有符合搜索条件的优惠券批次" icon={<Ticket size={24} />} />
          )}

          <Pagination
            page={couponPagination.page}
            totalPages={couponPagination.totalPages}
            total={sortedCoupons.length}
            pageSize={couponPagination.pageSize}
            onChange={couponPagination.setPage}
          />
        </div>
      )}

      {tab === 'records' && (
        <div className="card p-5">
          <table className="table">
            <thead>
              <tr>
                <th><SortableHeader label="优惠券码" sortKey="code" activeKey={recordSortKey} direction={recordSortDirection} onSort={toggleRecordSort} /></th>
                <th>用户</th>
                <th>关联订单</th>
                <th><SortableHeader label="使用时间" sortKey="usedAt" activeKey={recordSortKey} direction={recordSortDirection} onSort={toggleRecordSort} /></th>
              </tr>
            </thead>
            <tbody>
              {pagedRecords.map((r) => (
                <tr key={r.id}>
                  <td className="font-medium">{r.code}</td>
                  <td>{r.user}</td>
                  <td>{r.order}</td>
                  <td className="text-text-secondary">{r.usedAt}</td>
                </tr>
              ))}
            </tbody>
          </table>

          {filteredRecords.length === 0 && (
            <EmptyState title="暂无核销记录" description="没有符合搜索条件的优惠券记录" icon={<Tag size={24} />} />
          )}

          <Pagination
            page={recordPagination.page}
            totalPages={recordPagination.totalPages}
            total={sortedRecords.length}
            pageSize={recordPagination.pageSize}
            onChange={recordPagination.setPage}
          />
        </div>
      )}

      {tab === 'stats' && (
        <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
          {[
            { label: '领取率', value: '85.6%', sub: '856 / 1000' },
            { label: '使用率', value: '62.3%', sub: '533 / 856' },
            { label: '带动交易额', value: '¥128,450.00', sub: '较上期 +12%' },
          ].map((s, i) => (
            <div key={i} className="card p-5 text-center">
              <div className="text-sm text-text-secondary mb-2">{s.label}</div>
              <div className="text-2xl font-bold text-primary">{s.value}</div>
              <div className="text-xs text-text-secondary mt-1">{s.sub}</div>
            </div>
          ))}
        </div>
      )}

      <Modal
        open={modalOpen}
        title="生成优惠券"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-primary">生成</button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">批次号</label>
            <input value={form.batch} onChange={(e) => setForm({ ...form, batch: e.target.value })} className="input" placeholder="例如 BATCH0628" />
          </div>
          <div>
            <label className="block text-sm mb-1">优惠券类型</label>
            <select value={form.type} onChange={(e) => setForm({ ...form, type: e.target.value })} className="input">
              <option value="fixed">固定面额</option>
              <option value="percent">百分比折扣</option>
            </select>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">{form.type === 'fixed' ? '面额' : '折扣'}（{form.type === 'fixed' ? '元' : '%'}）</label>
              <input type="number" value={form.value} onChange={(e) => setForm({ ...form, value: parseFloat(e.target.value) || 0 })} className="input" />
            </div>
            <div>
              <label className="block text-sm mb-1">使用门槛（元）</label>
              <input type="number" value={form.threshold} onChange={(e) => setForm({ ...form, threshold: parseFloat(e.target.value) || 0 })} className="input" />
            </div>
          </div>
          <div className="grid grid-cols-2 gap-3">
            <div>
              <label className="block text-sm mb-1">总量</label>
              <input type="number" value={form.total} onChange={(e) => setForm({ ...form, total: parseInt(e.target.value) || 0 })} className="input" />
            </div>
            <div>
              <label className="block text-sm mb-1">每人限领</label>
              <input type="number" value={form.limitPerUser} onChange={(e) => setForm({ ...form, limitPerUser: parseInt(e.target.value) || 0 })} className="input" />
            </div>
          </div>
        </div>
      </Modal>
    </div>
  );
}
