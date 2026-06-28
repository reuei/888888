import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { nodes } from '../../data/mock';
import { Activity, Edit, Server } from 'lucide-react';

export default function SNodes() {
  const [tab, setTab] = useState<'list' | 'groups' | 'health'>('list');
  const [list, setList] = useState(nodes);

  const toggleEnabled = (id: string) => {
    setList(list.map((n) => (n.id === id ? { ...n, enabled: !n.enabled } : n)));
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

        {tab === 'list' && (
          <table className="table">
            <thead>
              <tr>
                <th>节点名称</th>
                <th>IP / 域名</th>
                <th>地区</th>
                <th>运营商</th>
                <th>类型</th>
                <th>健康状态</th>
                <th>启用</th>
                <th>操作</th>
              </tr>
            </thead>
            <tbody>
              {list.map((n) => (
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
          <table className="table">
            <thead>
              <tr>
                <th>节点名称</th>
                <th>最近检测时间</th>
                <th>响应延迟</th>
                <th>可用率</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              {list.map((n) => (
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
        )}
      </div>
    </div>
  );
}
