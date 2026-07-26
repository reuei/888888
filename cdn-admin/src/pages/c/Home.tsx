import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Search, ShoppingCart, User, FileSearch } from 'lucide-react';
import { merchants } from '../../data/mock';

interface CardProduct {
  id: string;
  name: string;
  price: number;
  stock: number;
  merchantName: string;
  category: string;
  description: string;
}

const cardProducts: CardProduct[] = [
  { id: 'CP001', name: '腾讯视频VIP月卡', price: 15.0, stock: 1280, merchantName: '极速云', category: '虚拟商品', description: '官方直充，秒到账，全国通用' },
  { id: 'CP002', name: '爱奇艺黄金会员季卡', price: 45.0, stock: 860, merchantName: '蓝海防护', category: '虚拟商品', description: '黄金会员权益，免广告追剧' },
  { id: 'CP003', name: '京东E卡 100元', price: 99.5, stock: 500, merchantName: '站点卫士', category: '礼品卡', description: '京东商城全场通用，无门槛使用' },
  { id: 'CP004', name: '话费充值100元', price: 99.8, stock: 5000, merchantName: '极速云', category: '充值卡', description: '移动联通电信通用，30秒到账' },
  { id: 'CP005', name: '网易云音乐黑胶VIP年卡', price: 128.0, stock: 320, merchantName: '云盾科技', category: '虚拟商品', description: '无损音质，专属曲库权益' },
  { id: 'CP006', name: '天猫超市优惠券50元', price: 35.0, stock: 200, merchantName: '蓝海防护', category: '优惠券', description: '天猫超市全场满200可用' },
  { id: 'CP007', name: 'Steam钱包50美元充值卡', price: 358.0, stock: 80, merchantName: '站点卫士', category: '充值卡', description: 'Steam钱包充值，全球可用' },
  { id: 'CP008', name: '美团外卖红包20元', price: 12.0, stock: 1500, merchantName: '云盾科技', category: '优惠券', description: '美团外卖下单立减，多门店可用' },
];

const categories = ['全部', '虚拟商品', '充值卡', '礼品卡', '优惠券'];

export default function Home() {
  const navigate = useNavigate();
  const [keyword, setKeyword] = useState('');
  const [activeCategory, setActiveCategory] = useState('全部');

  const filtered = cardProducts.filter((p) => {
    const matchCat = activeCategory === '全部' || p.category === activeCategory;
    const matchKw = !keyword || p.name.toLowerCase().includes(keyword.toLowerCase());
    return matchCat && matchKw;
  });

  return (
    <div className="theme-c min-h-screen bg-[#FFF7ED]">
      {/* Top bar */}
      <header className="bg-white border-b border-[#FFE4D1] sticky top-0 z-10">
        <div className="max-w-5xl mx-auto px-4 py-3 flex items-center gap-3">
          <div className="text-[#F97316] font-bold text-lg shrink-0">玄武发卡</div>
          <div className="flex-1 flex items-center bg-[#FFF7ED] rounded-full px-3 py-2 border border-[#FFE4D1]">
            <Search size={16} className="text-[#F97316]" />
            <input
              className="flex-1 bg-transparent outline-none text-sm px-2 text-[#7C2D12] placeholder:text-[#C2410C]/60"
              placeholder="搜索卡密商品"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
            />
          </div>
          <button
            onClick={() => navigate('/c/order-query')}
            className="flex items-center gap-1 text-sm text-[#7C2D12] hover:text-[#F97316] transition-colors"
          >
            <FileSearch size={16} /> 查单
          </button>
          <button
            onClick={() => navigate('/c/user-center')}
            className="flex items-center gap-1 text-sm text-[#7C2D12] hover:text-[#F97316] transition-colors"
          >
            <User size={16} /> 我的
          </button>
        </div>
      </header>

      {/* Category navigation */}
      <div className="bg-white border-b border-[#FFE4D1]">
        <div className="max-w-5xl mx-auto px-4 py-3 flex gap-2 overflow-x-auto">
          {categories.map((c) => (
            <button
              key={c}
              onClick={() => setActiveCategory(c)}
              className={`px-4 py-1.5 rounded-full text-sm whitespace-nowrap transition-colors ${
                activeCategory === c
                  ? 'bg-[#F97316] text-white'
                  : 'bg-[#FFF7ED] text-[#7C2D12] hover:bg-[#FFE4D1]'
              }`}
            >
              {c}
            </button>
          ))}
        </div>
      </div>

      {/* Banner */}
      <div className="max-w-5xl mx-auto px-4 py-4">
        <div className="bg-[#F97316] text-white rounded-xl p-5 flex items-center justify-between">
          <div>
            <div className="text-lg font-bold mb-1">全场虚拟卡密秒发</div>
            <div className="text-sm text-white/80">自动发货 · 安全支付 · 全网最低价</div>
          </div>
          <ShoppingCart size={40} className="text-white/60" />
        </div>
      </div>

      {/* Merchant notice */}
      <div className="max-w-5xl mx-auto px-4 pb-2">
        <div className="text-xs text-[#7C2D12]/70">
          已入驻商户 {merchants.length} 家 · 全部已通过资质审核
        </div>
      </div>

      {/* Product grid */}
      <div className="max-w-5xl mx-auto px-4 pb-8">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {filtered.map((p) => (
            <div
              key={p.id}
              className="bg-white rounded-xl border border-[#FFE4D1] p-4 hover:border-[#F97316] transition-colors"
            >
              <div className="flex items-start justify-between mb-2">
                <h3 className="font-semibold text-[#7C2D12] flex-1">{p.name}</h3>
                <span className="text-xs px-2 py-0.5 rounded bg-[#FFF7ED] text-[#C2410C]">{p.category}</span>
              </div>
              <p className="text-xs text-[#7C2D12]/70 mb-3 line-clamp-2 min-h-[2rem]">{p.description}</p>
              <div className="flex items-end justify-between mb-3">
                <div>
                  <span className="text-xs text-[#7C2D12]/60">¥</span>
                  <span className="text-2xl font-bold text-[#F97316] ml-0.5">{p.price.toFixed(2)}</span>
                </div>
                <div className="text-right text-xs text-[#7C2D12]/70">
                  <div>库存 {p.stock}</div>
                  <div className="text-[#C2410C]">{p.merchantName}</div>
                </div>
              </div>
              <button
                onClick={() => navigate(`/c/buy/${p.id}`)}
                className="w-full py-2 rounded-lg bg-[#F97316] text-white text-sm font-medium hover:bg-[#EA580C] transition-colors"
              >
                立即购买
              </button>
            </div>
          ))}
        </div>

        {filtered.length === 0 && (
          <div className="text-center py-12 text-[#7C2D12]/60">暂无相关商品</div>
        )}
      </div>

      <footer className="max-w-5xl mx-auto px-4 py-6 text-center text-xs text-[#7C2D12]/60">
        玄武发卡 · 安全便捷的虚拟卡密交易平台 · © 2026
      </footer>
    </div>
  );
}
