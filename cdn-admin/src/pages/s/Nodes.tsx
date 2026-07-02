import { useState, useMemo, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { useToast } from '../../hooks/useToast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import * as api from '../../services/api';
import { Activity, Edit, Server, Search, Inbox } from 'lucide-react';
import type { Node } from '../../types';

export default function SNodes() {
  const { show } = useToast();
  const [tab, setTab] = useState<'list' | 'groups' | 'health'>('list');
  const [list, setList] = useState<Node[]>([]);
  const [loading, setLoading] = useState(true);

  const [listKeyword, setListKeyword] = useState('');
  const listDebounced = useDebounce(listKeyword);

  const loadData = useCallback(async () => {
    setLoading(true);
    const data = await api.fetchNodes();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const listFiltered = useMemo(() => {
    const q = listDebounced.toLowerCase();
    return list.filter((n) => {
      if (!q) return true;
      return [n.name, n.ip, n.region, n.isp, n.type, n.health].some((v) =>
        String(v).toLowerCase().includes(q)
      );
    });
  }, [list, listDebounced]);

  const {
    sorted: listSorted,
    sortKey: listSortKey,
    sortDirection: listSortDirection,
    toggle: listToggle,
  } = useSort<Node>({ data: listFiltered, initialKey: 'name' });

  const {
    page: listPage,
    pageSize,
    totalPages: listTotalPages,
    slice: listSlice,
    setPage: setListPage,
  } = usePagination({ total: listSorted.length });
  const listPaged = listSlice(listSorted);

  useEffect(() => {
    setListPage(1);
  }, [listDebounced, listSortKey, setListPage]);

  const [healthKeyword, setHealthKeyword] = useState('');
  const healthDebounced = useDebounce(healthKeyword);

  const healthFiltered = useMemo(() => {
    const q = healthDebounced.toLowerCase();
    return list.filter((n) => {
      if (!q) return true;
      return [n.name, n.health].some((v) => String(v).toLowerCase().includes(q));
    });
  }, [list, healthDebounced]);

  const {
    sorted: healthSorted,
    sortKey: healthSortKey,
    sortDirection: healthSortDirection,
    toggle: healthToggle,
  } = useSort<Node>({ data: healthFiltered, initialKey: 'name' });

  const {
    page: healthPage,
    totalPages: healthTotalPages,
    slice: healthSlice,
    setPage: setHealthPage,
  } = usePagination({ total: healthSorted.length });
  const healthPaged = healthSlice(healthSorted);

  useEffect(() => {
    setHealthPage(1);
  }, [healthDebounced, healthSortKey, setHealthPage]);

  const toggleEnabled = async (id: string) => {
    const target = list.find((n) => n.id === id);
    if (!target) return;
    await api.updateNode(id, { enabled: !target.enabled });
    await loadData();
    show('状态已更新', 'success');
  };

  const healthBadge = (h: string) => {
    switch (h) {
      case 'healthy':
        return 'badge-success';
      case 'warning':
        return 'badge-warning';
      case 'offline':
        return 'badge-danger';
      default:
        return 'badge-default';
    }
  };

  const healthText = (h: string) => {
    const map: Record<string, string> = { healthy: '健康', warning: '告警', offline: '离线' };
    return map[h] || h;
  };

  return (
    <div>
      <PageHeader title="CDN 节点管理" breadcrumb={['商品管理', 'CDN节点管理']} />

      <div className="card p-5">
        <div className="flex gap-2 mb-6 border-b border-border">
          {[
            { key: 'list', label: '节点列表' },
            { key: 'groups', label: '节点分组' },
            { key: 'health', label: '健康检测' },
          ].map((t) => (
            <button
              key={t.key}
              onClick={() => setTab(t.key as any)}
              className={`px-4 py-2 text-sm border-b-2 ${tab === t.key ? 'border-primary text-primary' : 'border-transparent text-text-secondary'}`}
            >
              {t.label}
            </button>
          ))}
        </div>

        {loading ? (
          <Loading />
        ) : (
          <>
            {tab === 'list' && (
          <>
            <div className="flex flex-wrap gap-3 mb-4">
              <div className="relative flex-1 min-w-[200px]">
                <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                <input
                  type="text"
                  value={listKeyword}
                  onChange={(e) => setListKeyword(e.target.value)}
                  placeholder="搜索节点名称 / IP / 地区 / 运营商 / 类型 / 状态"
                  className="input pl-8"
                />
              </div>
            </div>

            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof Node> label="节点名称" sortKey="name" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="IP / 域名" sortKey="ip" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="地区" sortKey="region" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="运营商" sortKey="isp" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="类型" sortKey="type" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="健康状态" sortKey="health" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="启用" sortKey="enabled" activeKey={listSortKey} direction={listSortDirection} onSort={listToggle} />
                  </th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {listPaged.map((n) => (
                  <tr key={n.id}>
                    <td className="font-medium">{n.name}</td>
                    <td className="text-text-secondary">{n.ip}</td>
                    <td>{n.region}</td>
                    <td>{n.isp}</td>
                    <td>{n.type}</td>
                    <td>
                      <span className={`badge ${healthBadge(n.health)}`}>{healthText(n.health)}</span>
                    </td>
                    <td>
                      <button
                        onClick={() => toggleEnabled(n.id)}
                        className={`relative inline-flex h-5 w-9 rounded-full transition-colors ${n.enabled ? 'bg-primary' : 'bg-gray-300'}`}
                      >
                        <span className={`inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform ${n.enabled ? 'translate-x-5' : 'translate-x-1'} mt-0.5`} />
                      </button>
                    </td>
                    <td>
                      <div className="flex items-center gap-2">
                        <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                          <Edit size={16} />
                        </button>
                        <button className="p-1.5 rounded hover:bg-gray-100 text-success" title="检测">
                          <Activity size={16} />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {listPaged.length === 0 && <EmptyState title="暂无节点" description="没有符合搜索条件的节点" icon={<Inbox size={24} />} />}

            <Pagination page={listPage} totalPages={listTotalPages} total={listSorted.length} pageSize={pageSize} onChange={setListPage} />
          </>
        )}

        {tab === 'groups' && (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            {[
              { name: '按地区', items: ['华北', '华东', '华南', '西南', '海外'] },
              { name: '按运营商', items: ['电信', '联通', '移动', 'BGP'] },
              { name: '按用途', items: ['高防节点池', '公开节点池', 'Cloudflare 池', '游戏专用池'] },
            ].map((g, i) => (
              <div key={i} className="card p-4">
                <h4 className="font-semibold mb-3 flex items-center gap-2">
                  <Server size={16} className="text-primary" /> {g.name}
                </h4>
                <div className="flex flex-wrap gap-2">
                  {g.items.map((item, j) => (
                    <span key={j} className="badge badge-default">{item}</span>
                  ))}
                </div>
              </div>
            ))}
          </div>
        )}

        {tab === 'health' && (
          <>
            <div className="flex flex-wrap gap-3 mb-4">
              <div className="relative flex-1 min-w-[200px]">
                <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                <input
                  type="text"
                  value={healthKeyword}
                  onChange={(e) => setHealthKeyword(e.target.value)}
                  placeholder="搜索节点名称 / 健康状态"
                  className="input pl-8"
                />
              </div>
            </div>

            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof Node> label="节点名称" sortKey="name" activeKey={healthSortKey} direction={healthSortDirection} onSort={healthToggle} />
                  </th>
                  <th>最近检测时间</th>
                  <th>
                    <SortableHeader<keyof Node> label="响应延迟" sortKey="latency" activeKey={healthSortKey} direction={healthSortDirection} onSort={healthToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="可用率" sortKey="uptime" activeKey={healthSortKey} direction={healthSortDirection} onSort={healthToggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof Node> label="状态" sortKey="health" activeKey={healthSortKey} direction={healthSortDirection} onSort={healthToggle} />
                  </th>
                </tr>
              </thead>
              <tbody>
                {healthPaged.map((n) => (
                  <tr key={n.id}>
                    <td className="font-medium">{n.name}</td>
                    <td className="text-text-secondary">2026-06-28 10:00:00</td>
                    <td>{n.latency > 0 ? `${n.latency}ms` : '-'}</td>
                    <td>{n.uptime}</td>
                    <td>
                      <span className={`badge ${healthBadge(n.health)}`}>{healthText(n.health)}</span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>

            {healthPaged.length === 0 && <EmptyState title="暂无节点" description="没有符合搜索条件的节点" icon={<Inbox size={24} />} />}

            <Pagination page={healthPage} totalPages={healthTotalPages} total={healthSorted.length} pageSize={pageSize} onChange={setHealthPage} />
          </>
        )}
          </>
        )}
      </div>
    </div>
  );
}
