import { useState, useMemo } from 'react';
import PageHeader from '../../components/PageHeader';
import { settlementRecords } from '../../data/mock';
import { formatMoney } from '../../utils/helpers';
import { FileDown, Download } from 'lucide-react';

const alipayMap: Record<string, { account: string; name: string }> = {
  极速云: { account: 'jsyun@example.com', name: '极速云网络' },
  蓝海防护: { account: 'lhshield@example.com', name: '蓝海防护科技' },
  站点卫士: { account: 'zdweishi@example.com', name: '站点卫士' },
};

export default function AlipayExport() {
  const pending = useMemo(
    () => settlementRecords.filter((r) => r.status === 'pending'),
    []
  );
  const [selected, setSelected] = useState<string[]>([]);
  const [preview, setPreview] = useState(false);

  const toggle = (id: string) => {
    setSelected((prev) =>
      prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]
    );
  };

  const toggleAll = () => {
    setSelected(selected.length === pending.length ? [] : pending.map((r) => r.id));
  };

  const rows = useMemo(
    () =>
      pending
        .filter((r) => selected.includes(r.id))
        .map((r) => {
          const info = alipayMap[r.merchant] || { account: '', name: r.merchant };
          return {
            account: info.account,
            name: info.name,
            amount: formatMoney(r.amount),
          };
        }),
    [pending, selected]
  );

  const handleDownload = () => {
    const header = 'alipay_account,real_name,amount';
    const body = rows.map((r) => `${r.account},${r.name},${r.amount}`).join('\n');
    const blob = new Blob([header + '\n' + body], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `alipay_export_${new Date().toISOString().slice(0, 10)}.csv`;
    a.click();
    URL.revokeObjectURL(url);
  };

  return (
    <div>
      <PageHeader title="支付宝打款导出" breadcrumb={['财务管理', '支付宝打款导出']} />

      <div className="card p-5 mb-6">
        <div className="flex items-center justify-between mb-4">
          <h3 className="font-semibold">待结算记录</h3>
          <button
            onClick={() => setPreview(true)}
            disabled={selected.length === 0}
            className="btn btn-primary flex items-center gap-1 disabled:opacity-50"
          >
            <FileDown size={16} /> 生成导出文件
          </button>
        </div>
        <table className="table">
          <thead>
            <tr>
              <th>
                <input
                  type="checkbox"
                  checked={pending.length > 0 && selected.length === pending.length}
                  onChange={toggleAll}
                />
              </th>
              <th>结算单号</th>
              <th>商户</th>
              <th>金额</th>
              <th>时间</th>
            </tr>
          </thead>
          <tbody>
            {pending.map((r) => (
              <tr key={r.id}>
                <td>
                  <input
                    type="checkbox"
                    checked={selected.includes(r.id)}
                    onChange={() => toggle(r.id)}
                  />
                </td>
                <td className="font-medium">{r.id}</td>
                <td>{r.merchant}</td>
                <td>¥{formatMoney(r.amount)}</td>
                <td className="text-text-secondary">{r.time}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {preview && (
        <div className="card p-5">
          <div className="flex items-center justify-between mb-4">
            <h3 className="font-semibold">导出预览</h3>
            <button onClick={handleDownload} className="btn btn-success flex items-center gap-1">
              <Download size={16} /> 下载导出文件
            </button>
          </div>
          <div className="overflow-x-auto">
            <table className="table">
              <thead>
                <tr>
                  <th>alipay_account</th>
                  <th>real_name</th>
                  <th>amount</th>
                </tr>
              </thead>
              <tbody>
                {rows.map((r, i) => (
                  <tr key={i}>
                    <td>{r.account}</td>
                    <td>{r.name}</td>
                    <td>{r.amount}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}
    </div>
  );
}
