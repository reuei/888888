import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { createSite, fetchProducts } from '../../services/api';
import { statusBadge, statusText } from '../../utils/helpers';
import { Plus, CheckCircle, Loader2 } from 'lucide-react';
import type { Product } from '../../types';

interface PreviewSite {
  id: string;
  name: string;
  domain: string;
  template: string;
  nodePool: string;
  remark: string;
  status: 'pending';
  createdAt: string;
}

const templates = ['PC-01', 'PC-02', 'PC-03', 'M-01', 'M-02'];

export default function AddSite() {
  const { show } = useToast();
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(false);
  const nodePools = Array.from(new Set(products.map((p) => p.nodePool)));
  const [form, setForm] = useState({
    name: '',
    domain: '',
    template: templates[0],
    nodePool: '',
    remark: '',
  });
  const [list, setList] = useState<PreviewSite[]>([]);
  const [success, setSuccess] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const loadProducts = useCallback(async () => {
    setLoading(true);
    const data = await fetchProducts();
    setProducts(data);
    if (data.length > 0) {
      const pools = Array.from(new Set(data.map((p) => p.nodePool)));
      setForm((prev) => ({ ...prev, nodePool: pools[0] || '' }));
    }
    setLoading(false);
  }, []);

  useEffect(() => {
    loadProducts();
  }, [loadProducts]);

  const handleSubmit = async () => {
    if (!form.domain.trim() || submitting) return;
    setSubmitting(true);
    await createSite({
      name: form.name.trim() || form.domain.trim(),
      domain: form.domain.trim(),
      template: form.template,
      products: 0,
      nodes: 0,
      status: 'pending',
      createdAt: new Date().toISOString().slice(0, 10),
    });
    const newSite: PreviewSite = {
      id: Date.now().toString(),
      name: form.name.trim() || form.domain.trim(),
      domain: form.domain.trim(),
      template: form.template,
      nodePool: form.nodePool,
      remark: form.remark,
      status: 'pending',
      createdAt: new Date().toISOString().slice(0, 10),
    };
    setList([newSite, ...list]);
    setSuccess(true);
    setForm({ name: '', domain: '', template: templates[0], nodePool: nodePools[0] || '', remark: '' });
    setSubmitting(false);
    show(`站点 ${newSite.domain} 创建成功`, 'success');
  };

  useEffect(() => {
    if (nodePools.length > 0 && !nodePools.includes(form.nodePool)) {
      setForm((prev) => ({ ...prev, nodePool: nodePools[0] }));
    }
  }, [nodePools, form.nodePool]);

  return (
    <div>
      <PageHeader title="添加站点" breadcrumb={['站点管理', '添加站点']} />

      <div className="card p-5 mb-6">
        <div className="space-y-4 max-w-lg">
          <div>
            <label className="block text-sm mb-1">站点名称</label>
            <input
              type="text"
              value={form.name}
              onChange={(e) => setForm({ ...form, name: e.target.value })}
              className="input"
              placeholder="请输入站点名称"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">域名</label>
            <input
              type="text"
              value={form.domain}
              onChange={(e) => setForm({ ...form, domain: e.target.value })}
              className="input"
              placeholder="例如 www.example.com"
            />
          </div>
          <div>
            <label className="block text-sm mb-1">选择模板</label>
            <select
              value={form.template}
              onChange={(e) => setForm({ ...form, template: e.target.value })}
              className="input"
            >
              {templates.map((t) => (
                <option key={t} value={t}>
                  {t}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">所属节点池</label>
            <select
              value={form.nodePool}
              onChange={(e) => setForm({ ...form, nodePool: e.target.value })}
              className="input"
              disabled={loading}
            >
              {loading ? (
                <option>加载中...</option>
              ) : (
                nodePools.map((pool) => (
                  <option key={pool} value={pool}>
                    {pool}
                  </option>
                ))
              )}
            </select>
          </div>
          <div>
            <label className="block text-sm mb-1">备注</label>
            <textarea
              value={form.remark}
              onChange={(e) => setForm({ ...form, remark: e.target.value })}
              className="input"
              rows={3}
              placeholder="请输入备注信息"
            />
          </div>

          {success && (
            <div className="flex items-center gap-2 text-sm text-success">
              <CheckCircle size={16} />
              站点创建成功
            </div>
          )}

          <button
            onClick={handleSubmit}
            disabled={submitting || !form.domain.trim()}
            className="btn btn-primary flex items-center gap-1 disabled:opacity-70"
          >
            {submitting ? <Loader2 size={16} className="animate-spin" /> : <Plus size={16} />}
            提交创建
          </button>
        </div>
      </div>

      {list.length > 0 && (
        <div className="card p-5">
          <h3 className="text-sm font-medium mb-3">创建预览</h3>
          <table className="table">
            <thead>
              <tr>
                <th>站点名称</th>
                <th>域名</th>
                <th>模板</th>
                <th>所属节点池</th>
                <th>备注</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              {list.map((site) => (
                <tr key={site.id}>
                  <td className="font-medium">{site.name}</td>
                  <td>{site.domain}</td>
                  <td>{site.template}</td>
                  <td>{site.nodePool}</td>
                  <td className="text-text-secondary">{site.remark || '-'}</td>
                  <td>
                    <span className={`badge ${statusBadge(site.status)}`}>
                      {statusText(site.status)}
                    </span>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}
