import { ChevronLeft, ChevronRight } from 'lucide-react';

interface PaginationProps {
  page: number;
  totalPages: number;
  total: number;
  pageSize?: number;
  onChange: (page: number) => void;
}

export default function Pagination({
  page,
  totalPages,
  total,
  pageSize = 10,
  onChange,
}: PaginationProps) {
  if (total <= 0) return null;

  const start = (page - 1) * pageSize + 1;
  const end = Math.min(page * pageSize, total);

  const pages = Array.from({ length: totalPages }, (_, i) => i + 1);

  const visiblePages = pages.filter((p) => {
    if (totalPages <= 7) return true;
    if (p === 1 || p === totalPages) return true;
    if (p >= page - 1 && p <= page + 1) return true;
    return false;
  });

  const items: (number | string)[] = [];
  visiblePages.forEach((p, index) => {
    if (index > 0 && p - visiblePages[index - 1] > 1) {
      items.push('...');
    }
    items.push(p);
  });

  return (
    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4 text-sm text-text-secondary">
      <div>
        共 {total} 条，第 {start}-{end} 条
      </div>
      <div className="flex items-center gap-1.5">
        <button
          onClick={() => onChange(page - 1)}
          disabled={page <= 1}
          className="btn btn-default text-xs px-2 py-1.5 flex items-center gap-0.5 disabled:opacity-40"
        >
          <ChevronLeft size={14} /> 上一页
        </button>
        {items.map((item, index) =>
          typeof item === 'number' ? (
            <button
              key={index}
              onClick={() => onChange(item)}
              className={`btn text-xs px-2.5 py-1.5 min-w-[2rem] ${
                item === page ? 'btn-primary' : 'btn-default'
              }`}
            >
              {item}
            </button>
          ) : (
            <span key={index} className="px-1 text-text-secondary">
              {item}
            </span>
          )
        )}
        <button
          onClick={() => onChange(page + 1)}
          disabled={page >= totalPages}
          className="btn btn-default text-xs px-2 py-1.5 flex items-center gap-0.5 disabled:opacity-40"
        >
          下一页 <ChevronRight size={14} />
        </button>
      </div>
    </div>
  );
}
