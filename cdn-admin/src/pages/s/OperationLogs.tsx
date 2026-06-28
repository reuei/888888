import { useState, useMemo } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../components/Toast';
import { operationLogs } from '../../data/mock';
import { Search } from 'lucide-react';

export default function OperationLogs() {
  const { show } = useToast();
  const [keyword, setKeyword] = useState('');
  const [moduleFilter, setModuleFilter] = useState('');
  const [actionFilter, setActionFilter] = useState('');

  const modules = useMemo(() => Array.from(new Set(operationLogs.map((l) => l.module))), []);
  const actions = useMemo(() => Array.from(new Set(operationLogs.map((l) => l.action))), []);

  const filtered = useMemo(() => {
    return operationLogs.filter((l) => {
      const matchKeyword =
        !keyword ||
        l.id.toLowerCase().includes(keyword.toLowerCase()) ||
        l.operator.toLowerCase().includes(keyword.toLowerCase()) ||
        l.detail.toLowerCase().includes(keyword.toLowerCase()) ||
        l.ip.includes(keyword);
      const matchModule = !moduleFilter || l.module === moduleFilter;
      const matchAction = !actionFilter || l.action === actionFilter;
      return matchKeyword && matchModule && matchAction;
    });
  }, [keyword, moduleFilter, actionFilter]);

  return (
    <div>
      <PageHeader title="操作日志" breadcrumb={['系统运维', '操作日志']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索日志ID / 操作人 / 详情 / IP"
              className="input pl-8"
            />
          </div>
          <select
            value={moduleFilter}
            onChange={(e) => setModuleFilter(e.target.value)}
            className="input w-40"
          >
            <option value="">全部模块</option>
            {modules.map((m) => (
              <option key={m} value={m}>
                {m}
              </option>
            ))}
          </select>
          <select
            value={actionFilter}
            onChange={(e) => setActionFilter(e.target.value)}
            className="input w-40"
          >
            <option value="">全部操作</option>
            {actions.map((a) => (
              <option key={a} value={a}>
                {a}
              </option>
            ))}
          </select>
          <button
            onClick={() => {
              setKeyword('');
              setModuleFilter('');
              setActionFilter('');
              show('筛选条件已重置', 'info');
            }}
            className="btn btn-default"
          >
            重置
          </button>
        </div>

        <div className="text-sm text-text-secondary mb-3">共 {filtered.length} 条记录</div>

        <table className="table">
          <thead>
            <tr>
              <th>日志ID</th>
              <th>操作人</th>
              <th>模块</th>
              <th>操作</th>
              <th>详情</th>
              <th>IP</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((l) => (
              <tr key={l.id}>
                <td className="text-text-secondary">{l.id}</td>
                <td className="font-medium">{l.operator}</td>
                <td>{l.module}</td>
                <td>
                  <span className="badge badge-default">{l.action}</span>
                </td>
                <td>{l.detail}</td>
                <td className="font-mono text-text-secondary">{l.ip}</td>
                <td className="text-text-secondary">{l.createdAt}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
