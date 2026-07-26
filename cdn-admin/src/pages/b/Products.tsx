import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { formatMoney } from '../../utils/helpers';
import { Edit, KeyRound, ArrowUpDown, Plus, PackageSearch } from 'lucide-react';
import type { CardProduct } from '../../types';

const mockProducts: CardProduct[] = [
  { id: 'CP001', name: 'VPN月卡', categoryId: 'C1', merchantId: 'M001', merchantName: '极速发卡', price: 9.90, stock: 320, soldCount: 1280, deliveryMode: 'auto', status: 'on', template: 'CARD-01' },
  { id: 'CP002', name: '游戏点券100元', categoryId: 'C2', merchantId: 'M001', merchantName: '极速发卡', price: 95.00, stock: 12, soldCount: 856, deliveryMode: 'auto', status: 'on', template: 'CARD-02' },
  { id: 'CP003', name: 'Steam充值卡', categoryId: 'C3', merchantId: 'M001', merchantName: '极速发卡', price: 299.00, stock: 5, soldCount: 432, deliveryMode: 'manual', status: 'off', template: 'CARD-03' },
  { id: 'CP004', name: '话费充值50元', categoryId: 'C2', merchantId: 'M001', merchantName: '极速发卡', price: 49.00, stock: 0, soldCount: 2310, deliveryMode: 'auto', status: 'on', template: 'CARD-01' },
  { id: 'CP005', name: 'Netflix会员月卡', categoryId: 'C4', merchantId: 'M001', merchantName: '极速发卡', price: 39.00, stock: 180, soldCount: 96, deliveryMode: 'auto', status: 'on', template: 'CARD-04' },
  { id: 'CP006', name: 'ChatGPT Plus月卡', categoryId: 'C4', merchantId: 'M001', merchantName: '极速发卡', price: 159.00, stock: 64, soldCount: 312, deliveryMode: 'manual', status: 'off', template: 'CARD-09' },
];

const categoryMap: Record<string, string> = {
  C1: '虚拟商品',
  C2: '充值卡',
  C3: '礼品卡',
  C4: '会员账号',
};

export default function BProducts() {
  const { show } = useToast();
  const [list, setList] = useState<CardProduct[]>(mockProducts);

  const toggleStatus = (id: string) => {
    const target = list.find((p) => p.id === id);
    if (!target) return;
    const next = target.status === 'on' ? 'off' : 'on';
    setList((prev) => prev.map((p) => (p.id === id ? { ...p, status: next } : p)));
    show(`商品「${target.name}」已${next === 'on' ? '上架' : '下架'}`, next === 'on' ? 'success' : 'warning');
  };

  return (
    <div>
      <PageHeader
        title="商品列表"
        breadcrumb={['商品管理', '商品列表']}
        actions={
          <button onClick={() => show('添加商品功能开发中', 'info')} className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 添加商品
          </button>
        }
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>商品名</th>
              <th>分类</th>
              <th>价格</th>
              <th>库存</th>
              <th>已售</th>
              <th>发卡模式</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((p) => (
              <tr key={p.id}>
                <td className="font-medium">{p.name}</td>
                <td>{categoryMap[p.categoryId] || p.categoryId}</td>
                <td>¥{formatMoney(p.price)}</td>
                <td className={p.stock <= 10 ? 'text-danger font-medium' : ''}>{p.stock}</td>
                <td className="text-text-secondary">{p.soldCount}</td>
                <td>{p.deliveryMode === 'auto' ? '自动发卡' : '手动发卡'}</td>
                <td>
                  <span className={`badge ${p.status === 'on' ? 'badge-success' : 'badge-danger'}`}>
                    {p.status === 'on' ? '上架' : '下架'}
                  </span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => show('编辑商品功能开发中', 'info')} className="p-1.5 rounded hover:bg-gray-100 text-primary" title="编辑">
                      <Edit size={16} />
                    </button>
                    <button onClick={() => show('卡密管理功能开发中', 'info')} className="p-1.5 rounded hover:bg-gray-100 text-info" title="卡密管理">
                      <KeyRound size={16} />
                    </button>
                    <button onClick={() => toggleStatus(p.id)} className="p-1.5 rounded hover:bg-gray-100 text-warning" title="上/下架">
                      <ArrowUpDown size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {list.length === 0 && (
          <div className="py-8 text-center text-sm text-text-secondary flex flex-col items-center gap-2">
            <PackageSearch size={24} />
            暂无商品
          </div>
        )}
      </div>
    </div>
  );
}
