import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import { fetchSites, fetchProducts, fetchNodes } from '../../services/api';
import type { Site, Product, Node } from '../../types';

export default function SiteConfig() {
  const [sites, setSites] = useState<Site[]>([]);
  const [products, setProducts] = useState<Product[]>([]);
  const [nodes, setNodes] = useState<Node[]>([]);
  const [loading, setLoading] = useState(false);
  const [tab, setTab] = useState('basic');
  const [selectedSite, setSelectedSite] = useState('');
  const [selectedTemplate, setSelectedTemplate] = useState('PC-01');
  const [selectedProducts, setSelectedProducts] = useState<string[]>(['P001', 'P002']);
  const [selectedNodes, setSelectedNodes] = useState<string[]>(['N001', 'N002', 'N004']);

  const loadData = useCallback(async () => {
    setLoading(true);
    try {
      const [sitesData, productsData, nodesData] = await Promise.all([
        fetchSites(),
        fetchProducts(),
        fetchNodes(),
      ]);
      setSites(sitesData);
      setProducts(productsData);
      setNodes(nodesData);
      if (sitesData.length > 0 && !selectedSite) {
        setSelectedSite(sitesData[0].id);
      }
    } finally {
      setLoading(false);
    }
  }, [selectedSite]);

  useEffect(() => {
    loadData();
  }, [loadData]);

  const toggleProduct = (id: string) => {
    setSelectedProducts((prev) => (prev.includes(id) ? prev.filter((p) => p !== id) : [...prev, id]));
  };

  const toggleNode = (id: string) => {
    setSelectedNodes((prev) => (prev.includes(id) ? prev.filter((n) => n !== id) : [...prev, id]));
  };

  const templates = [
    { id: 'PC-01', name: '企业官网风格', type: '电脑端' },
    { id: 'PC-02', name: '科技深蓝风格', type: '电脑端' },
    { id: 'PC-03', name: '极简白风格', type: '电脑端' },
    { id: 'M-01', name: '移动端标准版', type: '手机端' },
    { id: 'M-02', name: '移动端深色版', type: '手机端' },
  ];

  return (
    <div>
      <PageHeader title="站点配置" breadcrumb={['站点管理', '站点配置']} />

      <div className="card p-5">
        {loading && <div className="text-sm text-text-secondary mb-4">加载中...</div>}

        {!loading && (
          <>
            <div className="flex items-center gap-4 mb-6">
              <label className="text-sm text-text-secondary">选择站点</label>
              <select
                value={selectedSite}
                onChange={(e) => setSelectedSite(e.target.value)}
                className="input w-64"
              >
                {sites.map((s) => (
                  <option key={s.id} value={s.id}>{s.name}（{s.domain}）</option>
                ))}
              </select>
            </div>

            <div className="flex gap-2 mb-6 border-b border-border">
              {[
                { key: 'basic', label: '基础信息' },
                { key: 'template', label: '模板设置' },
                { key: 'products', label: '产品服务绑定' },
                { key: 'nodes', label: '节点分配' },
              ].map((t) => (
                <button
                  key={t.key}
                  onClick={() => setTab(t.key)}
                  className={`px-4 py-2 text-sm border-b-2 ${tab === t.key ? 'border-primary text-primary' : 'border-transparent text-text-secondary'}`}
                >
                  {t.label}
                </button>
              ))}
            </div>

            {tab === 'basic' && (
              <div className="max-w-2xl space-y-4">
                <div>
                  <label className="block text-sm mb-1">站点名称</label>
                  <input className="input" defaultValue={sites.find((s) => s.id === selectedSite)?.name} />
                </div>
                <div>
                  <label className="block text-sm mb-1">副标题</label>
                  <input className="input" placeholder="请输入副标题" />
                </div>
                <div>
                  <label className="block text-sm mb-1">Logo</label>
                  <div className="flex items-center gap-3">
                    <div className="w-12 h-12 bg-primary text-white rounded flex items-center justify-center text-lg">S</div>
                    <button className="btn btn-default text-xs">上传 Logo</button>
                  </div>
                </div>
                <div>
                  <label className="block text-sm mb-1">客服联系方式</label>
                  <input className="input" placeholder="QQ / 微信 / 电话" />
                </div>
                <div>
                  <label className="block text-sm mb-1">ICP 备案号</label>
                  <input className="input" placeholder="例如 京ICP备123456号" />
                </div>
                <button className="btn btn-primary">保存基础信息</button>
              </div>
            )}

            {tab === 'template' && (
              <div>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                  {templates.map((t) => (
                    <div
                      key={t.id}
                      onClick={() => setSelectedTemplate(t.id)}
                      className={`card p-4 cursor-pointer border-2 ${selectedTemplate === t.id ? 'border-primary' : 'border-border'}`}
                    >
                      <div className="h-24 bg-gray-100 rounded mb-3 flex items-center justify-center text-text-secondary text-xs">
                        {t.name} 缩略图
                      </div>
                      <div className="font-medium">{t.name}</div>
                      <div className="text-xs text-text-secondary">{t.type} · {t.id}</div>
                    </div>
                  ))}
                </div>
                <button className="btn btn-primary">保存模板设置</button>
              </div>
            )}

            {tab === 'products' && (
              <div>
                <div className="space-y-2 mb-6">
                  {products.map((p) => (
                    <label key={p.id} className="flex items-center gap-3 p-3 border border-border rounded hover:bg-gray-50 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={selectedProducts.includes(p.id)}
                        onChange={() => toggleProduct(p.id)}
                        className="w-4 h-4"
                      />
                      <div className="flex-1">
                        <div className="font-medium">{p.name}</div>
                        <div className="text-xs text-text-secondary">{p.type} · {p.priceRange}</div>
                      </div>
                    </label>
                  ))}
                </div>
                <button className="btn btn-primary">保存绑定</button>
              </div>
            )}

            {tab === 'nodes' && (
              <div>
                <div className="mb-4 text-sm text-text-secondary">已启用节点：{selectedNodes.length} 个</div>
                <div className="space-y-2 mb-6">
                  {nodes.map((n) => (
                    <label key={n.id} className="flex items-center gap-3 p-3 border border-border rounded hover:bg-gray-50 cursor-pointer">
                      <input
                        type="checkbox"
                        checked={selectedNodes.includes(n.id)}
                        onChange={() => toggleNode(n.id)}
                        className="w-4 h-4"
                      />
                      <div className="flex-1">
                        <div className="font-medium">{n.name}</div>
                        <div className="text-xs text-text-secondary">{n.region}</div>
                      </div>
                    </label>
                  ))}
                </div>
                <button className="btn btn-primary">保存节点分配</button>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
