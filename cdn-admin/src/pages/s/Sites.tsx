import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../hooks/useToast';
import { fetchSites, createSite, updateSite, deleteSite } from '../../services/api';
import { statusBadge, statusText } from '../../utils/helpers';
import { Trash2, Ban, Plus, Search, Loader2 } from 'lucide-react';
import type { Site } from '../../types';

export default function SSites() {
  const { show } = useToast();
  const [list, setList] = useState<Site[]>([]);
  const [loading, setLoading] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);
  const [form, setForm] = useState({ name: '', domain: '', template: 'PC-01' });

  const load = useCallback(async () => {
    setLoading(true);
    const data = await fetchSites();
    setList(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const handleAdd = async () => {
    if (!form.domain.trim() || loading) return;
    await createSite({
      name: form.name.trim() || form.domain.trim(),
      domain: form.domain.trim(),
      template: form.template,
      products: 0,
      nodes: 0,
      status: 'running',
      createdAt: new Date().toISOString().slice(0, 10),
    });
    await load();
    setModalOpen(false);
    setForm({ name: '', domain: '', template: 'PC-01' });
    show(`站点 ${form.domain} 添加成功`, 'success');
  };

  const handleDelete = async (s: Site) => {
    if (loading) return;
    await deleteSite(s.id);
    await load();
    show(`站点 ${s.domain} 已删除`, 'warning');
  };

  const toggleStatus = async (s: Site) => {
    if (loading) return;
    const next = s.status === 'running' ? 'stopped' : 'running';
    await updateSite(s.id, { status: next });
    await load();
    show(`站点 ${s.domain} 已${next === 'running' ? '启用' : '停用'}`, 'success');
  };

  return (
    <div>
      <PageHeader
        title="站点列表"
        breadcrumb={['站点管理', '站点列表']}
        actions={
          <button onClick={() => setModalOpen(true)} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加站点
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input type="text" placeholder="搜索站点名称 / 域名" className="input pl-8" />
          </div>
          <select className="input w-32">
            <option>全部状态</option>
            <option>运行中</option>
            <option>已停用</option>
            <option>审核中</option>
          </select>
          <button className="btn btn-primary">查询</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>站点名称</th>
              <th>站点域名</th>
              <th>模板类型</th>
              <th>产品数量</th>
              <th>节点数量</th>
              <th>服务状态</th>
              <th>创建时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((s) => (
              <tr key={s.id}>
                <td className="font-medium">{s.name}</td>
                <td className="text-text-secondary">{s.domain}</td>
                <td>{s.template}</td>
                <td>{s.products}</td>
                <td>{s.nodes}</td>
                <td>
                  <span className={`badge ${statusBadge(s.status)}`}>{statusText(s.status)}</span>
                </td>
                <td className="text-text-secondary">{s.createdAt}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => toggleStatus(s)} className="p-1.5 rounded hover:bg-gray-100 text-warning" title={s.status === 'running' ? '停用' : '启用'}>
                      <Ban size={16} />
                    </button>
                    <button onClick={() => handleDelete(s)} className="p-1.5 rounded hover:bg-gray-100 text-danger" title="删除">
                      <Trash2 size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {loading && <div className="py-8 text-center text-sm text-text-secondary">加载中...</div>}
      </div>

      <Modal
        open={modalOpen}
        title="添加站点"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">取消</button>
            <button onClick={handleAdd} disabled={loading || !form.domain.trim()} className="btn btn-primary disabled:opacity-70">
              {loading ? <Loader2 size={16} className="animate-spin" /> : '保存'}
            </button>
          </>
        }
      >
        <div className="space-y-4">
          <div>
            <label className="block text-sm mb-1">站点名称</label>
            <input
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              className="input"
              placeholder="请输入站点名称"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">站点域名</label>
            <input
              value={form.domain}
              onChange={(e) => setForm({ ...form, domain: e.target.value })}
              className="input"
              placeholder="例如 www.example.com"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">模板类型</label>
            <select
              value={form.template}
              onChange={(e) => setForm({ ...form, template: e.target.value })}
              className="input"
            >
              <option>PC-01</option>
              <option>PC-02</option>
              <option>PC-03</option>
              <option>M-01</option>
              <option>M-02</option>
            </select>
          </div>
        </div>
      </Modal>
    </div>
  );
}
