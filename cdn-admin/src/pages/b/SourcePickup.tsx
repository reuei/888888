import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { Search, Link2, Package } from 'lucide-react';

interface SourceProduct {
  id: string;
  name: string;
  category: string;
  price: number;
  stock: number;
  supplier: string;
  docked: boolean;
}

const mockSources: SourceProduct[] = [
  { id: 'SP001', name: 'VPN月卡-优质货源', category: '虚拟商品', price: 7.50, stock: 5000, supplier: '源头供货商A', docked: false },
  { id: 'SP002', name: '游戏点券100元-批量', category: '充值卡', price: 92.00, stock: 1200, supplier: '点券中心', docked: false },
  { id: 'SP003', name: 'Steam充值卡-美区', category: '礼品卡', price: 285.00, stock: 320, supplier: '海外直供', docked: true },
  { id: 'SP004', name: '话费充值50元-秒到', category: '充值卡', price: 48.50, stock: 8000, supplier: '话费通道', docked: false },
  { id: 'SP005', name: 'Netflix会员月卡', category: '会员账号', price: 35.00, stock: 640, supplier: '影音账号行', docked: false },
  { id: 'SP006', name: 'ChatGPT Plus月卡', category: '会员账号', price: 145.00, stock: 210, supplier: 'AI 账号铺', docked: false },
];

const categories = ['all', '虚拟商品', '充值卡', '礼品卡', '会员账号'];

export default function SourcePickup() {
  const { show } = useToast();
  const [list, setList] = useState(mockSources);
  const [keyword, setKeyword] = useState('');
  const [category, setCategory] = useState('all');

  const filtered = list.filter((s) => {
    const mk = !keyword || s.name.toLowerCase().includes(keyword.toLowerCase()) || s.supplier.includes(keyword);
    const mc = category === 'all' || s.category === category;
    return mk && mc;
  });

  const dock = (id: string) => {
    const target = list.find((s) => s.id === id);
    if (!target || target.docked) return;
    setList((prev) => prev.map((s) => (s.id === id ? { ...s, docked: true } : s)));
    show(`「${target.name}」对接成功，已加入商品列表`, 'success');
  };

  return (
    <div>
      <PageHeader title="货源采集" breadcrumb={['商品管理', '货源采集']} />

      <div className="card p-4 mb-5">
        <div className="flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索货源名称 / 供货商"
              className="input pl-8"
            />
          </div>
          <select value={category} onChange={(e) => setCategory(e.target.value)} className="input w-40">
            {categories.map((c) => (
              <option key={c} value={c}>
                {c === 'all' ? '全部分类' : c}
              </option>
            ))}
          </select>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {filtered.map((s) => (
          <div key={s.id} className="card p-4">
            <div className="flex items-start justify-between mb-3">
              <div className="w-10 h-10 rounded bg-primary/10 flex items-center justify-center text-primary">
                <Package size={20} />
              </div>
              {s.docked && <span className="badge badge-success">已对接</span>}
            </div>
            <h3 className="font-semibold mb-1">{s.name}</h3>
            <div className="text-xs text-text-secondary mb-3">
              {s.supplier} · {s.category}
            </div>
            <div className="flex items-center justify-between text-sm mb-3">
              <div>
                <span className="text-text-secondary">供货价：</span>
                <span className="text-primary font-semibold">¥{s.price.toFixed(2)}</span>
              </div>
              <div className="text-text-secondary">库存 {s.stock}</div>
            </div>
            <button
              onClick={() => dock(s.id)}
              disabled={s.docked}
              className="btn btn-primary w-full flex items-center justify-center gap-1 disabled:opacity-50"
            >
              <Link2 size={14} /> {s.docked ? '已对接' : '一键对接'}
            </button>
          </div>
        ))}
      </div>

      {filtered.length === 0 && (
        <div className="card p-8 text-center text-sm text-text-secondary">暂无符合条件的货源</div>
      )}
    </div>
  );
}
