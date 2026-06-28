import type { StatCardData } from '../types';

const colorMap: Record<string, string> = {
  primary: 'bg-blue-50 text-primary border-primary/20',
  success: 'bg-green-50 text-success border-success/20',
  warning: 'bg-orange-50 text-warning border-warning/20',
  danger: 'bg-red-50 text-danger border-danger/20',
};

interface StatCardProps {
  data: StatCardData;
}

export default function StatCard({ data }: StatCardProps) {
  return (
    <div className={`card p-5 border ${colorMap[data.color || 'primary']}`}>
      <div className="text-sm text-text-secondary mb-2">{data.title}</div>
      <div className="text-2xl font-bold tracking-tight">
        {data.value}
        {data.unit && <span className="text-sm font-normal ml-1">{data.unit}</span>}
      </div>
      {data.sub && <div className="text-xs mt-2 opacity-80">{data.sub}</div>}
    </div>
  );
}
