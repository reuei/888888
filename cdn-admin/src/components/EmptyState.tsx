import { Inbox } from 'lucide-react';

interface EmptyStateProps {
  title?: string;
  description?: string;
  icon?: React.ReactNode;
  action?: React.ReactNode;
}

export default function EmptyState({
  title = '暂无数据',
  description = '当前列表为空',
  icon,
  action,
}: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center justify-center py-12 text-center">
      <div className="w-12 h-12 rounded-full bg-black/5 dark:bg-white/5 flex items-center justify-center text-text-secondary mb-3">
        {icon || <Inbox size={24} />}
      </div>
      <div className="text-sm font-medium text-text mb-1">{title}</div>
      <div className="text-xs text-text-secondary mb-3">{description}</div>
      {action && <div>{action}</div>}
    </div>
  );
}
