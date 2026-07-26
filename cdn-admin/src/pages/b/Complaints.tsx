import { useState, useEffect, useCallback } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { useToast } from '../../hooks/useToast';
import { fetchComplaints, updateComplaint } from '../../services/api';
import { statusBadge, statusText } from '../../utils/helpers';
import type { Complaint } from '../../types';

const statusFilters = [
  { key: 'all', label: '全部' },
  { key: 'pending', label: '待处理' },
  { key: 'resolved', label: '已解决' },
  { key: 'rejected', label: '已驳回' },
];

export default function BComplaints() {
  const { show } = useToast();
  const [list, setList] = useState<Complaint[]>([]);
  const [filter, setFilter] = useState('all');
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<Complaint | null>(null);
  const [reply, setReply] = useState('');

  const load = useCallback(async () => {
    const data = await fetchComplaints();
    setList(data);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const filtered = list.filter((c) => filter === 'all' || c.status === filter);

  const open = (c: Complaint) => {
    setCurrent(c);
    setReply('');
    setModalOpen(true);
  };

  const handle = async (result: 'resolved' | 'rejected') => {
    if (!current) return;
    await updateComplaint(current.id, { status: result });
    await load();
    show(result === 'resolved' ? '投诉已处理' : '投诉已驳回', 'success');
    setModalOpen(false);
  };

  const sendReply = () => {
    if (!reply.trim()) {
      show('请输入回复内容', 'warning');
      return;
    }
    show('回复已发送给买家', 'success');
    setReply('');
  };

  return (
    <div>
      <PageHeader title="投诉处理" breadcrumb={['订单管理', '投诉处理']} />

      <div className="card p-5">
        <div className="flex gap-2 mb-4">
          {statusFilters.map((f) => (
            <button
              key={f.key}
              onClick={() => setFilter(f.key)}
              className={`btn text-xs ${filter === f.key ? 'btn-primary' : 'btn-default'}`}
            >
              {f.label}
            </button>
          ))}
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>订单号</th>
              <th>买家</th>
              <th>投诉原因</th>
              <th>时间</th>
              <th>状态</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {filtered.map((c) => (
              <tr key={c.id}>
                <td className="font-medium">{c.orderId}</td>
                <td>{c.plaintiff}</td>
                <td>{c.reason}</td>
                <td className="text-text-secondary">{c.createdAt}</td>
                <td>
                  <span className={`badge ${statusBadge(c.status)}`}>{statusText(c.status)}</span>
                </td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => open(c)} className="btn btn-default text-xs">
                      回复/处理
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {filtered.length === 0 && <div className="py-8 text-center text-sm text-text-secondary">暂无投诉</div>}
      </div>

      <Modal
        open={modalOpen}
        title="投诉处理"
        onClose={() => setModalOpen(false)}
        footer={
          <>
            <button onClick={() => setModalOpen(false)} className="btn btn-default">
              关闭
            </button>
            <button onClick={() => handle('rejected')} className="btn btn-danger">
              驳回
            </button>
            <button onClick={() => handle('resolved')} className="btn btn-success">
              标记已解决
            </button>
          </>
        }
      >
        {current && (
          <div className="space-y-4 text-sm">
            <div className="grid grid-cols-2 gap-3">
              <div>
                <span className="text-text-secondary">订单号：</span>
                {current.orderId}
              </div>
              <div>
                <span className="text-text-secondary">买家：</span>
                {current.plaintiff}
              </div>
              <div>
                <span className="text-text-secondary">时间：</span>
                {current.createdAt}
              </div>
              <div>
                <span className="text-text-secondary">状态：</span>
                <span className={`badge ${statusBadge(current.status)}`}>{statusText(current.status)}</span>
              </div>
            </div>
            <div>
              <span className="text-text-secondary">投诉原因：</span>
              {current.reason}
            </div>
            <div>
              <label className="block text-text-secondary mb-1">回复买家</label>
              <textarea
                value={reply}
                onChange={(e) => setReply(e.target.value)}
                rows={3}
                className="input"
                placeholder="请输入回复内容"
              />
            </div>
            <button onClick={sendReply} className="btn btn-primary text-xs">
              发送回复
            </button>
          </div>
        )}
      </Modal>
    </div>
  );
}
