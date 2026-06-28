import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import PageHeader from '../components/PageHeader';
import { notifications as initialNotifications } from '../data/mock';
import { Check, Trash2, Bell } from 'lucide-react';

interface NotificationsProps {
  role: 's' | 'b';
}

const typeColor: Record<string, string> = {
  system: 'bg-primary',
  order: 'bg-success',
  alert: 'bg-danger',
  finance: 'bg-warning',
};

const typeText: Record<string, string> = {
  system: '系统',
  order: '订单',
  alert: '告警',
  finance: '财务',
};

export default function Notifications({ role }: NotificationsProps) {
  const [notifications, setNotifications] = useState(initialNotifications);
  const [filter, setFilter] = useState<'all' | 'unread'>('all');
  const navigate = useNavigate();

  const filtered = notifications.filter((n) => (filter === 'unread' ? !n.read : true));
  const unreadCount = notifications.filter((n) => !n.read).length;

  const markRead = (id: string) => {
    setNotifications((prev) => prev.map((n) => (n.id === id ? { ...n, read: true } : n)));
  };

  const markAllRead = () => {
    setNotifications((prev) => prev.map((n) => ({ ...n, read: true })));
  };

  const remove = (id: string) => {
    setNotifications((prev) => prev.filter((n) => n.id !== id));
  };

  const handleClick = (n: typeof notifications[0]) => {
    markRead(n.id);
    if (n.link && n.link.startsWith(`/${role}`)) {
      navigate(n.link);
    }
  };

  return (
    <div>
      <PageHeader
        title="消息通知"
        breadcrumb={['消息通知', '全部通知']}
        actions={
          <div className="flex items-center gap-2">
            <button onClick={markAllRead} className="btn btn-default text-xs">
              <Check size={14} /> 全部已读
            </button>
          </div>
        }
      />

      <div className="card p-5">
        <div className="flex items-center justify-between mb-4">
          <div className="flex gap-2">
            {[
              { key: 'all', label: '全部' },
              { key: 'unread', label: `未读 (${unreadCount})` },
            ].map((t) => (
              <button
                key={t.key}
                onClick={() => setFilter(t.key as any)}
                className={`btn text-xs ${filter === t.key ? 'btn-primary' : 'btn-default'}`}
              >
                {t.label}
              </button>
            ))}
          </div>
          <span className="text-sm text-text-secondary">共 {filtered.length} 条</span>
        </div>

        {filtered.length === 0 ? (
          <div className="py-16 text-center text-text-secondary">
            <Bell size={40} className="mx-auto mb-3 opacity-30" />
            <div>暂无通知</div>
          </div>
        ) : (
          <div className="space-y-3">
            {filtered.map((n) => (
              <div
                key={n.id}
                className={`flex items-start gap-3 p-4 border border-border rounded hover:bg-gray-50 ${n.read ? 'opacity-70' : ''}`}
              >
                <span className={`w-2 h-2 rounded-full mt-1.5 shrink-0 ${typeColor[n.type]}`}></span>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2 mb-1">
                    <span className="text-xs px-1.5 py-0.5 rounded bg-gray-100 text-text-secondary">
                      {typeText[n.type]}
                    </span>
                    <span className="text-xs text-text-secondary">{n.createdAt}</span>
                    {!n.read && <span className="w-1.5 h-1.5 rounded-full bg-danger"></span>}
                  </div>
                  <div
                    className="font-medium cursor-pointer hover:text-primary"
                    onClick={() => handleClick(n)}
                  >
                    {n.title}
                  </div>
                  <div className="text-sm text-text-secondary mt-1">{n.content}</div>
                </div>
                <div className="flex items-center gap-1">
                  {!n.read && (
                    <button
                      onClick={() => markRead(n.id)}
                      className="p-1.5 rounded hover:bg-gray-100 text-success"
                      title="标记已读"
                    >
                      <Check size={16} />
                    </button>
                  )}
                  <button
                    onClick={() => remove(n.id)}
                    className="p-1.5 rounded hover:bg-gray-100 text-danger"
                    title="删除"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
