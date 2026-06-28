import { ArrowUp, ArrowDown } from 'lucide-react';

interface SortableHeaderProps<T> {
  label: string;
  sortKey: T;
  activeKey: T | '';
  direction: 'asc' | 'desc';
  onSort: (key: T) => void;
}

export default function SortableHeader<T extends string | number | symbol>({
  label,
  sortKey,
  activeKey,
  direction,
  onSort,
}: SortableHeaderProps<T>) {
  const active = activeKey === sortKey;

  return (
    <button
      onClick={() => onSort(sortKey)}
      className="flex items-center gap-1 hover:text-primary transition-colors"
    >
      {label}
      <span className="inline-flex flex-col">
        <ArrowUp
          size={10}
          className={active && direction === 'asc' ? 'text-primary' : 'text-text-secondary/40'}
        />
        <ArrowDown
          size={10}
          className={`-mt-1 ${active && direction === 'desc' ? 'text-primary' : 'text-text-secondary/40'}`}
        />
      </span>
    </button>
  );
}
