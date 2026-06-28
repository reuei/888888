import { useState } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import { complaints } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Eye } from 'lucide-react';

export default function SComplaints() {
  const [list, setList] = useState(complaints);
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<typeof complaints[0] | null>(null);

  const openDetail = (c: typeof complaints[0]) => {
    setCurrent(c);
    setModalOpen(true);
  };

  const handleResolve = (result: 'resolved' | 'rejected') => {
    if (!current) return;
    setList(list.map((c) => (c.id === current.id ? { ...c, status: result } : c)));
    setModalOpen(false);
  };

  return (
    <div>
      <PageHeader title="投诉管理" breadcrumb={['订单管理', '投诉管理']} />

      <div className="card p-5">
        <table className="table">
          <thead>
            <tr>
              <th>投诉编号</th>
              <th>关联订单</th>
              <th>投诉人</th>
              <th>被投诉方</th>
              <th>投诉原因</th>
              <th>状态</th>
              <th>提交时间</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {list.map((c) => (
              <tr key={c.id}>
                <td className="font-medium">{c.id}</td>
                <td>{c.orderId}</td>
                <td>{c.plaintiff}</td>
                <td>{c.defendant}</td>
                <td>{c.reason}</td>
                <td>
                  <span className={`badge ${statusBadge(c.status)}`}>{statusText(c.status)}</span>
                </td>
                <td className="text-text-secondary">{c.createdAt}</td>
                <td>
                  <div className="flex items-center gap-2">
                    <button onClick={() => openDetail(c)} className="p-1.5 rounded hover:bg-gray-100 text-primary" title="详情">
                      <Eye size={16} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        open={modalOpen}
        title="投诉详情 / 仲裁"
        onClose={() => setModalOpen(false)}
        footer={
          current?.status === 'pending' ? (
            <>
              <button onClick={() => setModalOpen(false)} className="btn btn-default">关闭</button>
              <button onClick={() => handleResolve('rejected')} className="btn btn-danger">驳回投诉</button>
              <button onClick={() => handleResolve('resolved')} className="btn btn-success">仲裁通过</button>
            </>
          ) : (
            <button onClick={() => setModalOpen(false)} className="btn btn-default">关闭</button>
          )
        }
      >
        {current && (
          <div className="space-y-3 text-sm">
            <div><span className="text-text-secondary">投诉编号：</span>{current.id}</div>
            <div><span className="text-text-secondary">关联订单：</span>{current.orderId}</div>
            <div><span className="text-text-secondary">投诉人：</span>{current.plaintiff}</div>
            <div><span className="text-text-secondary">被投诉方：</span>{current.defendant}</div>
            <div><span className="text-text-secondary">投诉原因：</span>{current.reason}</div>
            <div>
              <label className="block text-text-secondary mb-1">投诉图片</label>
              <div className="grid grid-cols-3 gap-2">
                {[1, 2, 3].map((i) => (
                  <div key={i} className="h-20 bg-gray-100 rounded flex items-center justify-center text-xs text-text-secondary">图片{i}</div>
                ))}
              </div>
            </div>
            <div>
              <label className="block text-text-secondary mb-1">双方留言</label>
              <div className="space-y-2 max-h-32 overflow-y-auto p-2 bg-gray-50 rounded">
                <div className="text-xs"><span className="text-primary">{current.plaintiff}：</span>请处理我的投诉。</div>
                <div className="text-xs"><span className="text-warning">{current.defendant}：</span>已核实，正在处理。</div>
              </div>
            </div>
            <div>
              <label className="block text-text-secondary mb-1">仲裁备注</label>
              <textarea className="input" rows={3} placeholder="填写处理结果与原因"></textarea>
            </div>
            <div className="flex items-center gap-3 pt-2">
              <label className="flex items-center gap-1.5 text-sm">
                <input type="checkbox" /> 给用户弹窗提醒
              </label>
              <label className="flex items-center gap-1.5 text-sm">
                <input type="checkbox" /> 给商户弹窗提醒
              </label>
            </div>
          </div>
        )}
      </Modal>
    </div>
  );
}
