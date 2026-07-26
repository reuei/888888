import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { useToast } from '../../hooks/useToast';
import { merchants, feeGroups } from '../../data/mock';
import { Search, Save } from 'lucide-react';

interface Row {
  id: string;
  shopName: string;
  avatar: string;
  currentRate: number;
  customRate: string;
  status: 'normal' | 'pending' | 'banned';
  saved: boolean;
}

// Assign each merchant a default fee group rate
const initialRows: Row[] = merchants.map((m, i) => {
  const group = feeGroups[i % feeGroups.length];
  return {
    id: m.id,
    shopName: m.shopName,
    avatar: m.avatar,
    currentRate: group.rate,
    customRate: (group.rate * 100).toFixed(2),
    status: m.status,
    saved: true,
  };
});

const statusBadge = (s: string) => {
  switch (s) {
    case 'normal':
      return { cls: 'badge-success', text: '正常' };
    case 'pending':
      return { cls: 'badge-warning', text: '审核中' };
    case 'banned':
      return { cls: 'badge-danger', text: '已封禁' };
    default:
      return { cls: 'badge-default', text: s };
  }
};

export default function SMerchantFee() {
  const { show } = useToast();
  const [rows, setRows] = useState(initialRows);
  const [keyword, setKeyword] = useState('');

  const filtered = rows.filter(
    (r) =>
      !keyword ||
      r.shopName.includes(keyword) ||
      r.id.toLowerCase().includes(keyword.toLowerCase())
  );

  const updateCustomRate = (id: string, value: string) => {
    setRows(rows.map((r) => (r.id === id ? { ...r, customRate: value, saved: false } : r)));
  };

  const handleSave = (id: string) => {
    const row = rows.find((r) => r.id === id);
    if (!row) return;
    const rate = parseFloat(row.customRate);
    if (isNaN(rate) || rate < 0 || rate > 100) {
      show('自定义费率应在 0~100 之间', 'warning');
      return;
    }
    setRows(
      rows.map((r) =>
        r.id === id ? { ...r, currentRate: rate / 100, saved: true } : r
      )
    );
    show(`商户「${row.shopName}」自定义费率已保存为 ${rate.toFixed(2)}%`, 'success');
  };

  return (
    <div>
      <PageHeader title="单商户自定义费率" breadcrumb={['财务管理', '单商户自定义费率']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              placeholder="搜索商户名 / ID"
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
              <th>商户</th>
              <th>当前费率</th>
              <th>自定义费率(%)</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((r) => {
              const info = statusBadge(r.status);
              return (
                <tr key={r.id}>
                  <td>
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs">
                        {r.avatar}
                      </div>
                      <div>
                        <div className="font-medium">{r.shopName}</div>
                        <div className="text-xs text-text-secondary">{r.id}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span className="text-primary font-semibold">
                      {(r.currentRate * 100).toFixed(2)}%
                    </span>
                  </td>
                  <td>
                    <input
                      type="number"
                      step="0.01"
                      min="0"
                      max="100"
                      className="input w-28"
                      value={r.customRate}
                      onChange={(e) => updateCustomRate(r.id, e.target.value)}
                    />
                    {!r.saved && (
                      <span className="text-xs text-warning ml-2">未保存</span>
                    )}
                  </td>
                  <td>
                    <span className={`badge ${info.cls}`}>{info.text}</span>
                  </td>
                  <td>
                    <button
                      onClick={() => handleSave(r.id)}
                      className="btn btn-default text-xs flex items-center gap-1"
                      disabled={r.saved}
                    >
                      <Save size={14} /> 保存
                    </button>
                  </td>
                </tr>
              );
            })}
            {filtered.length === 0 && (
              <tr>
                <td colSpan={5}>
                  <div className="py-8 text-center text-sm text-text-secondary">暂无符合条件的商户</div>
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
