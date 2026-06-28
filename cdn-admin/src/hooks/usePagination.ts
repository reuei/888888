import { useMemo, useState } from 'react';

interface UsePaginationOptions {
  total: number;
  pageSize?: number;
  initialPage?: number;
}

interface UsePaginationResult {
  page: number;
  pageSize: number;
  totalPages: number;
  start: number;
  end: number;
  setPage: (page: number) => void;
  next: () => void;
  prev: () => void;
  slice: <T>(list: T[]) => T[];
}

export function usePagination({
  total,
  pageSize = 10,
  initialPage = 1,
}: UsePaginationOptions): UsePaginationResult {
  const [page, setPage] = useState(initialPage);

  const totalPages = useMemo(() => Math.max(1, Math.ceil(total / pageSize)), [total, pageSize]);

  const clampedPage = Math.min(Math.max(1, page), totalPages);

  const start = (clampedPage - 1) * pageSize;
  const end = Math.min(start + pageSize, total);

  const setClampedPage = (p: number) => setPage(Math.min(Math.max(1, p), totalPages));

  const next = () => setClampedPage(clampedPage + 1);
  const prev = () => setClampedPage(clampedPage - 1);

  const slice = <T,>(list: T[]) => list.slice(start, end);

  return {
    page: clampedPage,
    pageSize,
    totalPages,
    start,
    end,
    setPage: setClampedPage,
    next,
    prev,
    slice,
  };
}
