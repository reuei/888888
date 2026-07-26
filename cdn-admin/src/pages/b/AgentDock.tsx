import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { formatMoney } from '../../utils/helpers';
import { Edit, ArrowUpDown } from 'lucide-react';

interface DockedProduct {
  id: string;
  source: string;
  costPrice: number;
  salePrice: number;
  auditStatus: 'pending' | 'approved' | 'rejected';
  status: 'on' | 'off';
}

const mockDocked: DockedProduct[] = [
  { id: 'AD001', source: '基础CDN加速-代理版', costPrice: 8.00, salePrice: 12.00, auditStatus: 'approved', status: 'on' },
  { id: 'AD002', source: '企业高防CDN-代理版', costPrice: 250.00, salePrice: 299.00, auditStatus: 'approved', status: 'on' },
  { id: 'AD003', source: '全球加速Pro-代理版', costPrice: 180.00, salePrice: 199.00, auditStatus: 'pending', status: 'off' },
  { id: 'AD004', source: '游戏盾标准版-代理版', costPrice: 120.00, salePrice: 159.00, auditStatus: 'rejected', status: 'off' },
  { id: 'AD005', source: '会员账号包月-代理版', costPrice: 35.00, salePrice: 49.00, auditStatus: 'approved', status: 'off' },
];

function auditBadge(s: DockedProduct['auditStatus']) {
  switch (s) {
    case 'approved':
      return <span className="badge badge-success">已通过</span>;
    case 'pending':
      return <span className="badge badge-warning">审核中</span>;
    case 'rejected':
      return <span className="badge badge-danger">已驳回</span>;
  }
}

export default function BAgentDock() {
  const { show } = useToast();
  const [list, setList] = useState(mockDocked);

  const toggle = (id: string) => {
    const target = list.find((p) => p.id === id);
    if (!target) return;
    if (target.auditStatus !== 'approved') {
      show('审核通过后才能上架', 'warning');
      return;
    }
    const next = target.status === 'on' ? 'off' : 'on';
    setList((prev) => prev.map((p) => (p.id === id ? { ...p, status: next } : p)));
    show(`商品已${next === 'on' ? '上架' : '下架'}`, next === 'on' ? 'success' : 'warning');
  };

  return (
    <div>
      <PageHeader title="代理对接" breadcrumb={['商品管理', '代理对接']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>源商品</th>
              <th>成本价</th>
              <th>售价</th>
              <th>审核状态</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((p) => (
              <tr key={p.id}>
                <td className="font-medium">{p.source}</td>
                <td>¥{formatMoney(p.costPrice)}</td>
                <td>¥{formatMoney(p.salePrice)}</td>
                <td>{auditBadge(p.auditStatus)}</td>
                <td>
                  <span className={`badge ${p.status === 'on' ? 'badge-success' : 'badge-danger'}`}>
                    {p.status === 'on' ? '上架' : '下架'}
                  </span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => show('编辑代理商品功能开发中', 'info')}
                      className="p-1.5 rounded hover:bg-gray-100 text-primary"
                      title="编辑"
                    >
                      <Edit size={16} />
                    </button>
                    <button
                      onClick={() => toggle(p.id)}
                      className="p-1.5 rounded hover:bg-gray-100 text-warning"
                      title="上/下架"
                    >
                      <ArrowUpDown size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {list.length === 0 && <div className="py-8 text-center text-sm text-text-secondary">暂无代理对接商品</div>}
      </div>
    </div>
  );
}
