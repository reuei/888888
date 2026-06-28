import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { agentProducts } from '../../data/mock';
import { formatMoney, statusBadge, statusText } from '../../utils/helpers';
import { CheckCircle, XCircle, PackageSearch } from 'lucide-react';
import EmptyState from '../../components/EmptyState';
import type { AgentProduct } from '../../types';

export default function AgentAudit() {
  const [list, setList] = useState<AgentProduct[]>(agentProducts);
  const [activeId, setActiveId] = useState<string | null>(null);

  const pendingList = list.filter((p) => p.status === 'pending');
  const active = list.find((p) => p.id === activeId);

  const updateStatus = (id: string, status: AgentProduct['status']) => {
    setList(list.map((p) => (p.id === id ? { ...p, status } : p)));
    setActiveId(null);
  };

  return (
    <div>
      <PageHeader
        title="代理商品审核"
        breadcrumb={['代理/分销管理', '代理商品审核']}
      />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>商品ID</th>
              <th>商品名称</th>
              <th>来源商户</th>
              <th>成本价</th>
              <th>零售价</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pendingList.map((p) => (
              <tr key={p.id}>
                <td className="text-text-secondary">{p.id}</td>
                <td className="font-medium">{p.name}</td>
                <td>{p.source}</td>
                <td>¥{formatMoney(p.costPrice)}</td>
                <td>¥{formatMoney(p.retailPrice)}</td>
                <td>
                  <span className={`badge ${statusBadge(p.status)}`}>{statusText(p.status)}</span>
                </td>
                <td>
                  <button onClick={() => setActiveId(p.id)} className="btn btn-primary py-1 px-2 text-xs">
                    审核
                  </button>
                </td>
              </tr>
            ))}
            {pendingList.length === 0 && (
              <tr>
                <td colSpan={7}>
                  <EmptyState title="暂无待审核商品" description="当前没有需要审核的代理商品" icon={<PackageSearch size={24} />} />
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <Modal
        open={activeId !== null}
        title="代理商品审核"
        onClose={() => setActiveId(null)}
        footer={
          <>
            <button onClick={() => setActiveId(null)} className="btn btn-default">取消</button>
            <button
              onClick={() => active && updateStatus(active.id, 'off')}
              className="btn btn-danger flex items-center gap-1"
            >
              <XCircle size={16} /> 驳回
            </button>
            <button
              onClick={() => active && updateStatus(active.id, 'on')}
              className="btn btn-success flex items-center gap-1"
            >
              <CheckCircle size={16} /> 通过
            </button>
          </>
        }
      >
        {active && (
          <div className="space-y-3 text-sm">
            <div className="flex justify-between">
              <span className="text-text-secondary">商品名称</span>
              <span className="font-medium">{active.name}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">来源商户</span>
              <span>{active.source}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">成本价</span>
              <span>¥{formatMoney(active.costPrice)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">零售价</span>
              <span>¥{formatMoney(active.retailPrice)}</span>
            </div>
            <div className="flex justify-between">
              <span className="text-text-secondary">当前状态</span>
              <span className={`badge ${statusBadge(active.status)}`}>{statusText(active.status)}</span>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
