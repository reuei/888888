import { useState, useMemo, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import Loading from '../../components/Loading';
import { useToast } from '../../components/Toast';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import * as api from '../../services/api';
import { Play, RotateCcw, Trash2, Database, FileArchive, FileText, Search, Inbox } from 'lucide-react';
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
  const [records, setRecords] = useState<BackupRecord[]>([]);
  const [loading, setLoading] = useState(false);
  const [autoBackup, setAutoBackup] = useState(true);
  const [cycle, setCycle] = useState('daily');
  const [retainCount, setRetainCount] = useState(7);
  const [contents, setContents] = useState<string[]>(['database', 'files']);
  const [runningId, setRunningId] = useState<string | null>(null);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return records.filter((r) => {
      if (!q) return true;
      return [r.id, r.name, r.type, r.status, r.createdAt, r.size].some((v) =>
        String(v).toLowerCase().includes(q)
      );
    });
  }, [records, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<BackupRecord>({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  useEffect(() => {
    let ignore = false;
    setLoading(true);
    api.fetchBackupRecords()
      .then((data) => {
        if (!ignore) setRecords(data);
      })
      .finally(() => {
        if (!ignore) setLoading(false);
      });
    return () => {
      ignore = true;
    };
  }, []);

  const loadData = async () => {
    setLoading(true);
    try {
      const data = await api.fetchBackupRecords();
      setRecords(data);
    } finally {
      setLoading(false);
    }
  };

  const toggleContent = (key: string) => {
    setContents((prev) => (prev.includes(key) ? prev.filter((k) => k !== key) : [...prev, key]));
  };

  const handleBackupNow = async () => {
    show('备份任务已启动', 'info');
    const now = new Date();
    const name = `手动备份-${now.toISOString().slice(0, 10).replace(/-/g, '')}-${String(now.getHours()).padStart(2, '0')}${String(now.getMinutes()).padStart(2, '0')}`;
    const createdAt = now.toLocaleString('zh-CN', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
    }).replace(/\//g, '-');
    const record = await api.createBackupRecord({
      name,
      size: '计算中...',
      type: 'manual',
      status: 'running',
      createdAt,
    });
    await loadData();
    setRunningId(record.id);

    setTimeout(async () => {
      await api.updateBackupRecord(record.id, { status: 'success', size: '1.1GB' });
      await loadData();
      setRunningId(null);
      show('备份任务执行成功', 'success');
    }, 1500);
  };

  const handleRestore = (id: string) => {
    show(`备份 ${id} 恢复任务已提交`, 'success');
  };

  const handleDelete = async (id: string) => {
    await api.deleteBackupRecord(id);
    await loadData();
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
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索备份ID / 名称 / 类型 / 状态 / 大小 / 时间"
              className="input pl-8"
            />
          </div>
        </div>

        {loading ? (
          <Loading />
        ) : (
          <>
            <table className="table">
              <thead>
                <tr>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="备份ID" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="备份名称" sortKey="name" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="大小" sortKey="size" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="类型" sortKey="type" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>
                    <SortableHeader<keyof BackupRecord> label="时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
                  </th>
                  <th>操作</th>
                </tr>
              </thead>
              <tbody>
                {pagedList.map((r) => (
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

            {pagedList.length === 0 && (
              <EmptyState title="暂无备份记录" description="没有符合搜索条件的备份记录" icon={<Inbox size={24} />} />
            )}

            <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
          </>
        )}
      </div>
    </div>
  );
}
