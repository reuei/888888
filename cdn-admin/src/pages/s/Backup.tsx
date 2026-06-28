import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../components/Toast';
import { backupRecords } from '../../data/mock';
import { Play, RotateCcw, Trash2, Database, FileArchive, FileText } from 'lucide-react';
import type { BackupRecord } from '../../types';

const backupContents = [
  { key: 'database', label: '数据库', icon: Database },
  { key: 'files', label: '文件', icon: FileArchive },
  { key: 'logs', label: '日志', icon: FileText },
];

const statusBadgeClass: Record<BackupRecord['status'], string> = {
  success: 'badge badge-success',
  running: 'badge badge-warning',
  failed: 'badge badge-danger',
};

const statusTextMap: Record<BackupRecord['status'], string> = {
  success: '成功',
  running: '进行中',
  failed: '失败',
};

export default function Backup() {
  const { show } = useToast();
  const [records, setRecords] = useState<BackupRecord[]>(backupRecords);
  const [autoBackup, setAutoBackup] = useState(true);
  const [cycle, setCycle] = useState('daily');
  const [retainCount, setRetainCount] = useState(7);
  const [contents, setContents] = useState<string[]>(['database', 'files']);
  const [runningId, setRunningId] = useState<string | null>(null);

  const toggleContent = (key: string) => {
    setContents((prev) => (prev.includes(key) ? prev.filter((k) => k !== key) : [...prev, key]));
  };

  const handleBackupNow = () => {
    show('备份任务已启动', 'info');
    const now = new Date();
    const id = `B${String(records.length + 1).padStart(3, '0')}`;
    const name = `手动备份-${now.toISOString().slice(0, 10).replace(/-/g, '')}-${String(now.getHours()).padStart(2, '0')}${String(now.getMinutes()).padStart(2, '0')}`;
    const newRecord: BackupRecord = {
      id,
      name,
      size: '计算中...',
      type: 'manual',
      status: 'running',
      createdAt: now.toLocaleString('zh-CN', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
      }).replace(/\//g, '-'),
    };
    setRecords([newRecord, ...records]);
    setRunningId(id);

    setTimeout(() => {
      setRecords((prev) =>
        prev.map((r) => (r.id === id ? { ...r, status: 'success', size: '1.1GB' } : r))
      );
      setRunningId(null);
      show('备份任务执行成功', 'success');
    }, 1500);
  };

  const handleRestore = (id: string) => {
    show(`备份 ${id} 恢复任务已提交`, 'success');
  };

  const handleDelete = (id: string) => {
    setRecords((prev) => prev.filter((r) => r.id !== id));
    show('备份记录已删除', 'warning');
  };

  return (
    <div>
      <PageHeader
        title="数据备份"
        breadcrumb={['系统运维', '数据备份']}
        actions={
          <button
            onClick={handleBackupNow}
            disabled={runningId !== null}
            className="btn btn-success flex items-center gap-1"
          >
            <Play size={16} /> 立即备份
          </button>
        }
      />

      <div className="card p-5 mb-6">
        <h3 className="text-base font-semibold mb-4">备份配置</h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div className="flex items-center gap-3">
            <input
              type="checkbox"
              id="autoBackup"
              checked={autoBackup}
              onChange={(e) => setAutoBackup(e.target.checked)}
              className="w-4 h-4"
            />
            <label htmlFor="autoBackup" className="text-sm">自动备份开关</label>
          </div>
          <div>
            <label className="block text-sm mb-1">备份周期</label>
            <select value={cycle} onChange={(e) => setCycle(e.target.value)} className="input">
              <option value="daily">每天</option>
              <option value="weekly">每周</option>
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">保留份数</label>
            <input
              type="number"
              min={1}
              value={retainCount}
              onChange={(e) => setRetainCount(Number(e.target.value))}
              className="input"
            />
          </div>
          <div>
            <label className="block text-sm mb-2">备份内容</label>
            <div className="flex flex-wrap gap-4">
              {backupContents.map(({ key, label, icon: Icon }) => (
                <label key={key} className="flex items-center gap-2 text-sm">
                  <input
                    type="checkbox"
                    checked={contents.includes(key)}
                    onChange={() => toggleContent(key)}
                    className="w-4 h-4"
                  />
                  <Icon size={14} /> {label}
                </label>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div className="card p-5">
        <h3 className="text-base font-semibold mb-4">备份记录</h3>
        <table className="table">
          <thead>
            <tr>
              <th>备份ID</th>
              <th>备份名称</th>
              <th>大小</th>
              <th>类型</th>
              <th>状态</th>
              <th>时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {records.map((r) => (
              <tr key={r.id}>
                <td className="text-text-secondary">{r.id}</td>
                <td className="font-medium">{r.name}</td>
                <td>{r.size}</td>
                <td>
                  <span className={`badge ${r.type === 'auto' ? 'badge-default' : 'bg-primary/10 text-primary'}`}>
                    {r.type === 'auto' ? '自动' : '手动'}
                  </span>
                </td>
                <td>
                  <span className={statusBadgeClass[r.status]}>{statusTextMap[r.status]}</span>
                </td>
                <td className="text-text-secondary">{r.createdAt}</td>
                <td>
                  <div className="flex items-center gap-1">
                    <button
                      onClick={() => handleRestore(r.id)}
                      className="p-1.5 rounded hover:bg-gray-100 text-primary"
                      title="恢复"
                      disabled={r.status === 'running'}
                    >
                      <RotateCcw size={16} />
                    </button>
                    <button
                      onClick={() => handleDelete(r.id)}
                      className="p-1.5 rounded hover:bg-gray-100 text-danger"
                      title="删除"
                      disabled={r.status === 'running'}
                    >
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
