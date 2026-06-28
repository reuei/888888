import { useState, useMemo, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { CreditCard, RefreshCw, Shield, Search, Inbox } from 'lucide-react';

interface RiskLog {
  id: string;
  time: string;
  order: string;
  rule: string;
  result: string;
}

export default function SPayments() {
  const { show } = useToast();
  const [tab, setTab] = useState<'channels' | 'risk'>('channels');
  const [testOpen, setTestOpen] = useState(false);
  const [currentChannel, setCurrentChannel] = useState('');

  const channels = [
    { key: 'wechat', name: '微信支付', enabled: true },
    { key: 'alipay', name: '支付宝', enabled: true },
    { key: 'epay', name: '易支付', enabled: false },
    { key: 'codepay', name: '码支付', enabled: false },
    { key: 'usdt', name: 'USDT 支付', enabled: true },
    { key: 'xinpay', name: '信汇', enabled: false },
  ];

  const [riskLogs] = useState<RiskLog[]>([
    { id: 'R001', time: '2026-06-28 09:12', order: 'O202606280099', rule: '金额超过单日上限', result: '拦截' },
    { id: 'R002', time: '2026-06-27 22:30', order: 'O202606270088', rule: '高频下单', result: '人工复核' },
  ]);

  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return riskLogs.filter((r) => {
      if (!q) return true;
      return [r.id, r.time, r.order, r.rule, r.result].some((v) => String(v).toLowerCase().includes(q));
    });
  }, [riskLogs, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<RiskLog>({
    data: filtered,
    initialKey: 'time',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const openTest = (name: string) => {
    setCurrentChannel(name);
    setTestOpen(true);
    show(`${name} 连接测试成功`, 'success');
  };

  return (
    <div>
      <PageHeader title="支付网关管理" breadcrumb={['支付网关管理', '渠道对接']} />

      <div className="flex gap-2 mb-6">
        {[
          { key: 'channels', label: '渠道对接' },
          { key: 'risk', label: '支付风控' },
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

      {tab === 'channels' && (
        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
          {channels.map((c) => (
            <div key={c.key} className="card p-5">
              <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-2">
                  <CreditCard size={18} className="text-primary" />
                  <h3 className="font-semibold">{c.name}</h3>
                </div>
                <div className={`w-2 h-2 rounded-full ${c.enabled ? 'bg-success' : 'bg-gray-300'}`}></div>
              </div>
              <div className="space-y-3 text-sm">
                <div>
                  <label className="block text-text-secondary mb-1">商户号 / AppID</label>
                  <input className="input" placeholder="请输入商户号" defaultValue={c.enabled ? 'M' + Math.random().toString().slice(2, 10) : ''} />
                </div>
                <div>
                  <label className="block text-text-secondary mb-1">密钥</label>
                  <input type="password" className="input" placeholder="请输入密钥" defaultValue={c.enabled ? '************************' : ''} />
                </div>
                <div>
                  <label className="block text-text-secondary mb-1">回调地址</label>
                  <input className="input" placeholder="https://..." defaultValue={c.enabled ? `https://cdn.example.com/callback/${c.key}` : ''} />
                </div>
              </div>
              <div className="flex items-center justify-between mt-4">
                <label className="flex items-center gap-2 text-sm">
                  <input type="checkbox" defaultChecked={c.enabled} />
                  启用
                </label>
                <button onClick={() => openTest(c.name)} className="btn btn-default text-xs flex items-center gap-1">
                  <RefreshCw size={14} /> 测试连接
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {tab === 'risk' && (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <Shield size={18} className="text-warning" /> 单笔限额
            </h3>
            <div className="space-y-4">
              <div>
                <label className="block text-sm mb-1">单笔最小金额（元）</label>
                <input type="number" className="input" defaultValue="0.01" />
              </div>
              <div>
                <label className="block text-sm mb-1">单笔最大金额（元）</label>
                <input type="number" className="input" defaultValue="50000" />
              </div>
              <button onClick={() => show('单笔限额配置保存成功', 'success')} className="btn btn-primary">保存</button>
            </div>
          </div>

          <div className="card p-5">
            <h3 className="font-semibold mb-4 flex items-center gap-2">
              <RefreshCw size={18} className="text-primary" /> 金额随机化
            </h3>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-sm">开启金额随机化</span>
                <input type="checkbox" defaultChecked className="w-4 h-4" />
              </div>
              <div>
                <label className="block text-sm mb-1">随机范围（元）</label>
                <div className="flex items-center gap-2">
                  <input type="number" className="input" defaultValue="0.01" />
                  <span>~</span>
                  <input type="number" className="input" defaultValue="0.99" />
                </div>
              </div>
              <button onClick={() => show('金额随机化配置保存成功', 'success')} className="btn btn-primary">保存</button>
            </div>
          </div>

          <div className="card p-5 lg:col-span-2">
            <h3 className="font-semibold mb-4">风控日志</h3>
            <div className="flex flex-wrap gap-3 mb-4">
              <div className="relative flex-1 min-w-[200px]">
                <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                <input
                  type="text"
                  value={keyword}
                  onChange={(e) => setKeyword(e.target.value)}
                  placeholder="搜索订单号 / 触发规则 / 处理结果 / 时间"
                  className="input pl-8"
                />
              </div>
            </div>

            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof RiskLog> label="时间" sortKey="time" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof RiskLog> label="订单号" sortKey="order" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof RiskLog> label="触发规则" sortKey="rule" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof RiskLog> label="处理结果" sortKey="result" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((r) => (
                  <tr key={r.id}>
                    <td className="text-text-secondary">{r.time}</td>
                    <td>{r.order}</td>
                    <td>{r.rule}</td>
                    <td><span className="badge badge-warning">{r.result}</span></td>
                  </tr>
                ))}
              </tbody>
            </table>

            {pagedList.length === 0 && (
              <EmptyState title="暂无风控日志" description="没有符合搜索条件的风控记录" icon={<Inbox size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
          </div>
        </div>
      )}

      <Modal
        open={testOpen}
        title="测试连接"
        onClose={() => setTestOpen(false)}
        footer={<button onClick={() => setTestOpen(false)} className="btn btn-default">关闭</button>}
      >
        <div className="text-center py-6">
          <div className="w-12 h-12 rounded-full bg-green-50 text-success flex items-center justify-center mx-auto mb-3">
            <RefreshCw size={24} />
          </div>
          <p className="font-medium">{currentChannel} 连接成功</p>
          <p className="text-sm text-text-secondary mt-1">响应延迟 32ms</p>
        </div>
      </Modal>
    </div>
  );
}
