import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import * as api from '../../services/api';
import { Plus, Trash2, CheckCircle, Search, Inbox } from 'lucide-react';
import type { Gateway } from '../../types';

export default function GatewayConfig() {
  const { show } = useToast();
  const [list, setList] = useState<Gateway[]>([]);
  const [loading, setLoading] = useState(true);
  const [open, setOpen] = useState(false);
  const [name, setName] = useState('');
  const [channel, setChannel] = useState('alipay');
  const [fee, setFee] = useState('');
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const loadData = useCallback(async () => {
    setLoading(true);
    const data = await api.fetchGateways();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return list.filter((g) => {
      if (!q) return true;
      return [g.id, g.name, g.channel].some((v) => String(v).toLowerCase().includes(q));
    });
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<Gateway>({
    data: filtered,
    initialKey: 'id',
    initialDirection: 'asc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const toggleEnabled = async (id: string) => {
    const target = list.find((g) => g.id === id);
    if (!target) return;
    await api.updateGateway(id, { enabled: !target.enabled });
    await loadData();
    show('状态已更新', 'success');
  };

  const setDefault = async (id: string) => {
    await Promise.all(
      list.filter((g) => g.isDefault).map((g) => api.updateGateway(g.id, { isDefault: false }))
    );
    await api.updateGateway(id, { isDefault: true });
    await loadData();
    show('默认网关已设置', 'success');
  };

  const remove = async (id: string) => {
    await api.deleteGateway(id);
    await loadData();
    show('网关已删除', 'success');
  };

  const handleAdd = async () => {
    const feeValue = parseFloat(fee);
    if (!name || Number.isNaN(feeValue)) return;
    await api.createGateway({ name, channel, fee: feeValue, enabled: true, isDefault: false });
    await loadData();
    show('网关已添加', 'success');
    setName('');
    setChannel('alipay');
    setFee('');
    setOpen(false);
  };

  return (
    <div>
      <PageHeader
        title="网关配置"
        breadcrumb={['财务管理', '网关配置']}
        actions={
          <button onClick={() => setOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加网关
          </button>
        }
      />

      <div className="card p-5">
        {loading ? (
          <Loading />
        ) : (
          <>
            <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索网关ID / 名称 / 通道编码"
              className="input pl-8"
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>
                <SortableHeader<keyof Gateway> label="网关ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Gateway> label="网关名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Gateway> label="通道编码" sortKey="channel" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Gateway> label="费率(%)" sortKey="fee" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Gateway> label="启用状态" sortKey="enabled" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Gateway> label="默认" sortKey="isDefault" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((g) => (
              <tr key={g.id}>
                <td className="font-medium">{g.id}</td>
                <td>{g.name}</td>
                <td>{g.channel}</td>
                <td>{g.fee}%</td>
                <td>
                  <label className="flex items-center gap-2 text-sm cursor-pointer">
                    <input
                      type="checkbox"
                      checked={g.enabled}
                      onChange={() => toggleEnabled(g.id)}
                    />
                    <span className={g.enabled ? 'text-success' : 'text-text-secondary'}>
                      {g.enabled ? '已启用' : '已停用'}
                    </span>
                  </label>
                </td>
                <td>
                  <input
                    type="radio"
                    name="defaultGateway"
                    checked={g.isDefault}
                    onChange={() => setDefault(g.id)}
                  />
                </td>
                <td>
                  <button
                    onClick={() => remove(g.id)}
                    className="btn btn-default text-xs flex items-center gap-1"
                  >
                    <Trash2 size={14} /> 删除
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {pagedList.length === 0 && (
          <EmptyState title="暂无网关" description="没有符合搜索条件的网关配置" icon={<Inbox size={24} />} />
        )}

            <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>

      <Modal
        open={open}
        title="添加网关"
        onClose={() => setOpen(false)}
        footer={
          <>
            <button onClick={() => setOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} className="btn btn-success flex items-center gap-1">
              <CheckCircle size={16} /> 确认添加
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">网关名称</label>
            <input
              className="input"
              placeholder="例如：支付宝官方"
              value={name}
              onChange={(e) => setName(e.target.value)}
            />
          </div>
          <div>
            <label className="block text-sm mb-1">通道编码</label>
            <select className="input" value={channel} onChange={(e) => setChannel(e.target.value)}>
              <option value="alipay">alipay</option>
              <option value="wxpay">wxpay</option>
              <option value="epay">epay</option>
              <option value="usdt">usdt</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">费率（%）</label>
            <input
              type="number"
              step="0.01"
              className="input"
              placeholder="例如：0.6"
              value={fee}
              onChange={(e) => setFee(e.target.value)}
            />
          </div>
        </div>
      </Modal>
    </div>
  );
}
