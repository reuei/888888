import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { merchants } from '../../data/mock';
import { Unlock } from 'lucide-react';

interface BanRecord {
  merchantId: string;
  shopName: string;
  avatar: string;
  phone: string;
  reason: string;
  bannedAt: string;
}

// Build ban records from merchants with status 'banned'
const banRecords: BanRecord[] = merchants
  .filter((m) => m.status === 'banned')
  .map((m, idx) => ({
    merchantId: m.id,
    shopName: m.shopName,
    avatar: m.avatar,
    phone: m.phone,
    reason: ['违规售卖禁售商品', '恶意刷单', '虚假实名信息'][idx % 3],
    bannedAt: m.registerAt,
  }));

// Append additional mock ban records for demonstration
banRecords.push(
  {
    merchantId: 'M005',
    shopName: '违规商家A',
    avatar: '违',
    phone: '135****8888',
    reason: '欺诈用户投诉',
    bannedAt: '2026-05-08',
  },
  {
    merchantId: 'M006',
    shopName: '违规商家B',
    avatar: '违',
    phone: '134****6666',
    reason: '违反平台规则',
    bannedAt: '2026-04-22',
  }
);

export default function SMerchantBan() {
  const { show } = useToast();
  const [list, setList] = useState(banRecords);
  const [keyword, setKeyword] = useState('');

  const filtered = list.filter(
    (r) =>
      !keyword ||
      r.shopName.includes(keyword) ||
      r.merchantId.toLowerCase().includes(keyword.toLowerCase()) ||
      r.phone.includes(keyword)
  );

  const handleUnban = (rec: BanRecord) => {
    if (!confirm(`确定解禁商户「${rec.shopName}」吗？`)) return;
    setList(list.filter((r) => r.merchantId !== rec.merchantId));
    show(`商户「${rec.shopName}」已解禁`, 'success');
  };

  return (
    <div>
      <PageHeader title="封禁/解禁管理" breadcrumb={['商户管理', '封禁/解禁管理']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <input
            type="text"
            placeholder="搜索商户名 / ID / 手机号"
            className="input flex-1 min-w-[200px]"
            value={keyword}
            onChange={(e) => setKeyword(e.target.value)}
          />
          <button onClick={() => show(`筛选结果共 ${filtered.length} 条`, 'info')} className="btn btn-primary">
            查询
          </button>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>商户</th>
              <th>状态</th>
              <th>封禁原因</th>
              <th>封禁时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((r) => (
              <tr key={r.merchantId}>
                <td>
                  <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center text-xs">
                      {r.avatar}
                    </div>
                    <div>
                      <div className="font-medium">{r.shopName}</div>
                      <div className="text-xs text-text-secondary">
                        {r.merchantId} · {r.phone}
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span className="badge badge-danger">已封禁</span>
                </td>
                <td className="text-text-secondary">{r.reason}</td>
                <td className="text-text-secondary">{r.bannedAt}</td>
                <td>
                  <button
                    onClick={() => handleUnban(r)}
                    className="btn btn-default text-xs flex items-center gap-1"
                  >
                    <Unlock size={14} /> 解禁
                  </button>
                </td>
              </tr>
            ))}
            {filtered.length === 0 && (
              <tr>
                <td colSpan={5}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无封禁商户</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
