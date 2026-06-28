import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import { whitelistRecords } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Plus } from 'lucide-react';

export default function BWhitelist() {
  const [list] = useState(whitelistRecords);

  return (
    <div>
      <PageHeader title="域名过白管理" breadcrumb={['域名过白管理', '申请记录']} />

      <div className="card p-5 mb-6">
        <h3 className="font-semibold mb-4">域名过白申请</h3>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          <input className="input" placeholder="请输入域名" />
          <input className="input" placeholder="用途说明" />
          <input className="input" placeholder="备案号（如适用）" />
        </div>
        <div className="mt-4 flex justify-end">
          <button className="btn btn-primary flex items-center gap-1">
            <Plus size={16} /> 提交申请
          </button>
        </div>
      </div>

      <div className="card p-5">
        <h3 className="font-semibold mb-4">申请记录</h3>
        <table className="table">
          <thead>
            <tr>
              <th>域名</th>
              <th>用途</th>
              <th>备案号</th>
              <th>状态</th>
              <th>申请时间</th>
              <th>备注</th>
            </tr>
          </thead>
          <tbody>
            {list.map((w) => (
              <tr key={w.id}>
                <td className="font-medium">{w.domain}</td>
                <td>{w.purpose}</td>
                <td className="text-text-secondary">{w.icp}</td>
                <td>
                  <span className={`badge ${statusBadge(w.status)}`}>{statusText(w.status)}</span>
                </td>
                <td className="text-text-secondary">{w.createdAt}</td>
                <td className="text-text-secondary">{w.reason || '-'}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
