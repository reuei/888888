import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { agentProducts } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { Search, Eye } from 'lucide-react';

export default function AgentProducts() {
  const [search, setSearch] = useState('');
  const filtered = agentProducts.filter((p) => p.source.toLowerCase().includes(search.trim().toLowerCase()));

  return (
    <div>
      <PageHeader
        title="下级代理商品"
        breadcrumb={['代理/分销管理', '下级代理商品']}
      />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索代理商名称"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="input pl-9"
            />
          </div>
          <button onClick={() => setSearch('')} className="btn btn-default">重置</button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>商品ID</th>
              <th>商品名称</th>
              <th>代理商</th>
              <th>成本价</th>
              <th>零售价</th>
              <th>利润</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((p) => {
              const profit = p.retailPrice - p.costPrice;
              return (
                <tr key={p.id}>
                  <td className="text-text-secondary">{p.id}</td>
                  <td className="font-medium">{p.name}</td>
                  <td>{p.source}</td>
                  <td>¥{formatMoney(p.costPrice)}</td>
                  <td>¥{formatMoney(p.retailPrice)}</td>
                  <td className="text-success">¥{formatMoney(profit)}</td>
                  <td>
                    <span className={`badge ${statusBadge(p.status)}`}>{statusText(p.status)}</span>
                  </td>
                  <td>
                    <button className="p-1.5 rounded hover:bg-gray-100 text-primary" title="查看">
                      <Eye size={16} />
                    </button>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </div>
  );
}
