import { useState, useMemo, useEffect } from 'react';
import PageHeader from '../../components/PageHeader';
import Modal from '../../components/Modal';
import Pagination from '../../components/Pagination';
import EmptyState from '../../components/EmptyState';
import SortableHeader from '../../components/SortableHeader';
import { usePagination } from '../../hooks/usePagination';
import { useSort } from '../../hooks/useSort';
import { useDebounce } from '../../hooks/useDebounce';
import { complaints } from '../../data/mock';
import { statusBadge, statusText } from '../../utils/helpers';
import { Eye, Search, Inbox } from 'lucide-react';
import type { Complaint } from '../../types';

export default function SComplaints() {
  const [list, setList] = useState<Complaint[]>(complaints);
  const [modalOpen, setModalOpen] = useState(false);
  const [current, setCurrent] = useState<Complaint | null>(null);
  const [keyword, setKeyword] = useState('');
  const debouncedKeyword = useDebounce(keyword);

  const filtered = useMemo(() => {
    const q = debouncedKeyword.toLowerCase();
    return list.filter((c) => {
      if (!q) return true;
      return [c.id, c.orderId, c.plaintiff, c.defendant, c.reason, c.status, c.createdAt].some((v) =>
        String(v).toLowerCase().includes(q)
      );
    });
  }, [list, debouncedKeyword]);

  const { sorted, sortKey, sortDirection, toggle } = useSort<Complaint>({
    data: filtered,
    initialKey: 'createdAt',
    initialDirection: 'desc',
  });

  const { page, pageSize, totalPages, slice, setPage } = usePagination({ total: sorted.length });
  const pagedList = slice(sorted);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeyword, sortKey, setPage]);

  const openDetail = (c: Complaint) => {
    setCurrent(c);
    setModalOpen(true);
  };

  const handleResolve = (result: 'resolved' | 'rejected') => {
    if (!current) return;
    setList((prev) => prev.map((c) => (c.id === current.id ? { ...c, status: result } : c)));
    setModalOpen(false);
  };

  return (
    <div>
      <PageHeader title="投诉管理" breadcrumb={['订单管理', '投诉管理']} />

      <div className="card p-5">
        <div className="flex flex-wrap gap-3 mb-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search size={14} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
            <input
              type="text"
              value={keyword}
              onChange={(e) => setKeyword(e.target.value)}
              placeholder="搜索投诉编号 / 订单 / 投诉人 / 被投诉方 / 原因 / 状态"
              className="input pl-8"
            />
          </div>
        </div>

        <table className="table">
          <thead>
            <tr>
              <th>
                <SortableHeader<keyof Complaint> label="投诉编号" sortKey="id" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="关联订单" sortKey="orderId" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="投诉人" sortKey="plaintiff" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="被投诉方" sortKey="defendant" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="投诉原因" sortKey="reason" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="状态" sortKey="status" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>
                <SortableHeader<keyof Complaint> label="提交时间" sortKey="createdAt" activeKey={sortKey} direction={sortDirection} onSort={toggle} />
              </th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            {pagedList.map((c) => (
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

        {pagedList.length === 0 && (
          <EmptyState title="暂无投诉" description="没有符合搜索条件的投诉记录" icon={<Inbox size={24} />} />
        )}

        <Pagination page={page} totalPages={totalPages} total={sorted.length} pageSize={pageSize} onChange={setPage} />
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
