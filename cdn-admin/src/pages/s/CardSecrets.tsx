import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { cardSecrets, products, merchants } from '../../data/mock';
import { Upload, Search } from 'lucide-react';

export default function SCardSecrets() {
  const { show } = useToast();
  const [productFilter, setProductFilter] = useState('all');
  const [statusFilter, setStatusFilter] = useState('all');
  const [keyword, setKeyword] = useState('');

  const productName = (id: string) => products.find((p) => p.id === id)?.name || id;
  const merchantName = (id: string) => merchants.find((m) => m.id === id)?.shopName || id;

  const filtered = cardSecrets.filter((c) => {
    const matchProduct = productFilter === 'all' || c.productId === productFilter;
    const matchStatus = statusFilter === 'all' || c.status === statusFilter;
    const matchKeyword =
      !keyword ||
      c.id.toLowerCase().includes(keyword.toLowerCase()) ||
      c.content.toLowerCase().includes(keyword.toLowerCase()) ||
      (c.orderId || '').toLowerCase().includes(keyword.toLowerCase());
    return matchProduct && matchStatus && matchKeyword;
  });

  const maskContent = (content: string) => {
    if (content.length <= 6) return '****';
    return content.slice(0, 4) + '****' + content.slice(-4);
  };

  const statusBadgeInfo = (status: string) => {
    switch (status) {
      case 'unsold':
        return { cls: 'badge-default', text: '未售' };
      case 'sold':
        return { cls: 'badge-success', text: '已售' };
      case 'locked':
        return { cls: 'badge-warning', text: '锁定中' };
      case 'voided':
        return { cls: 'badge-danger', text: '已作废' };
      default:
        return { cls: 'badge-default', text: status };
    }
  };

  return (
    <div>
      <PageHeader
        title="卡密管理"
        breadcrumb={['商品管理', '卡密管理']}
        actions={
          <button onClick={() => show('打开批量导入弹窗', 'info')} className="btn btn-primary flex items-center gap-1">
            <Upload size={16} /> 批量导入
          </button>
        }
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <select
            className="input w-48"
            value={productFilter}
            onChange={(e) => setProductFilter(e.target.value)}
          >
            <option value="all">全部商品</option>
            {products.map((p) => (
              <option key={p.id} value={p.id}>
                {p.name}
              </option>
            ))}
          </select>
          <select
            className="input w-40"
            value={statusFilter}
            onChange={(e) => setStatusFilter(e.target.value)}
          >
            <option value="all">全部状态</option>
            <option value="unsold">未售</option>
            <option value="sold">已售</option>
            <option value="locked">锁定中</option>
            <option value="voided">已作废</option>
          </select>
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索卡密ID / 内容 / 订单号"
              className="input pl-8"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <button onClick={() => show(`筛选结果共 ${filtered.length} 条`, 'info')} className="btn btn-primary">
            查询
          </button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>卡密ID</th>
              <th>商品</th>
              <th>商户</th>
              <th>卡密内容</th>
              <th>状态</th>
              <th>订单号</th>
              <th>导入批次</th>
              <th>创建时间</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((c) => {
              const info = statusBadgeInfo(c.status);
              return (
                <tr key={c.id}>
                  <td className="text-text-secondary">{c.id}</td>
                  <td className="font-medium">{productName(c.productId)}</td>
                  <td>{merchantName(c.merchantId)}</td>
                  <td className="font-mono text-text-secondary">{maskContent(c.content)}</td>
                  <td>
                    <span className={`badge ${info.cls}`}>{info.text}</span>
                  </td>
                  <td className="text-text-secondary">{c.orderId || '-'}</td>
                  <td className="text-text-secondary">{c.importBatchId}</td>
                  <td className="text-text-secondary">{c.createdAt}</td>
                </tr>
              );
            })}
            {filtered.length === 0 && (
              <tr>
                <td colSpan={8}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无符合条件的卡密</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
