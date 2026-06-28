import PageHeader from '../../components/PageHeader';
import { agents } from '../../data/mock';
import { Network, Eye } from 'lucide-react';

export default function AgentTree() {
  const sortedAgents = [...agents].sort((a, b) => {
    if (a.level !== b.level) return a.level - b.level;
    if ((a.parent ?? '') !== (b.parent ?? '')) return (a.parent ?? '').localeCompare(b.parent ?? '');
    return a.id.localeCompare(b.id);
  });

  return (
    <div>
      <PageHeader
        title="代理关系树"
        breadcrumb={['代理/分销管理', '代理关系树']}
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>代理ID</th>
              <th>代理名称</th>
              <th>上级代理</th>
              <th>等级</th>
              <th>佣金比例(%)</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {sortedAgents.map((a) => {
              const parentAgent = agents.find((x) => x.id === a.parent);
              return (
                <tr key={a.id}>
                  <td className="text-text-secondary">{a.id}</td>
                  <td>
                    <div className="flex items-center gap-2" style={{ paddingLeft: `${(a.level - 1) * 1.5}rem` }}>
                      <Network size={16} className="text-primary" />
                      <span className="font-medium">{a.name}</span>
                    </div>
                  </td>
                  <td>{parentAgent ? parentAgent.name : '-'}</td>
                  <td>{a.level}</td>
                  <td>{a.commission}%</td>
                  <td>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                      <Eye size={16} />
                    </button>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
