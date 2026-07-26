import type { StatCardData } from '../types';

const colorMap: Record<string, string> = {
  primary: 'bg-blue-50 text-primary border-l-4 border-l-primary',
  success: 'bg-green-50 text-success border-l-4 border-l-success',
  warning: 'bg-orange-50 text-warning border-l-4 border-l-warning',
  danger: 'bg-red-50 text-danger border-l-4 border-l-danger',
};

interface StatCardProps {
  data: StatCardData;
}

export default function StatCard({ data }: StatCardProps) {
  return (
    <div className={`card p-5 border border-border ${colorMap[data.color || 'primary']}`}>
      <div className="text-sm text-text-secondary mb-2">{data.title}</div>
      <div className="text-2xl font-bold tracking-tight">
        {data.value}
        {data.unit && <span className="text-sm font-normal ml-1">{data.unit}</span>}
      </div>
      {data.sub && <div className="text-xs mt-2 opacity-80">{data.sub}</div>}
    </div>
  );
}
