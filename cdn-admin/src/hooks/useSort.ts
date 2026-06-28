import { useMemo, useState } from 'react';

type SortDirection = 'asc' | 'desc';

interface SortState<T> {
  key: keyof T | '';
  direction: SortDirection;
}

interface UseSortOptions<T> {
  data: T[];
  initialKey?: keyof T | '';
  initialDirection?: SortDirection;
}

interface UseSortResult<T> {
  sorted: T[];
  sortKey: keyof T | '';
  sortDirection: SortDirection;
  setSort: (key: keyof T) => void;
  toggle: (key: keyof T) => void;
}

export function useSort<T>({
  data,
  initialKey = '',
  initialDirection = 'asc',
}: UseSortOptions<T>): UseSortResult<T> {
  const [sort, setSort] = useState<SortState<T>>({ key: initialKey, direction: initialDirection });

  const toggle = (key: keyof T) => {
    setSort((prev) => {
      if (prev.key === key) {
        return { key, direction: prev.direction === 'asc' ? 'desc' : 'asc' };
      }
      return { key, direction: 'asc' };
    });
  };

  const setSortKey = (key: keyof T) => {
    setSort({ key, direction: 'asc' });
  };

  const sorted = useMemo(() => {
    if (!sort.key) return data;
    const key = sort.key;
    const multiplier = sort.direction === 'asc' ? 1 : -1;
    return [...data].sort((a, b) => {
      const aValue = a[key];
      const bValue = b[key];
      if (typeof aValue === 'number' && typeof bValue === 'number') {
        return (aValue - bValue) * multiplier;
      }
      if (typeof aValue === 'string' && typeof bValue === 'string') {
        return aValue.localeCompare(bValue, 'zh-CN') * multiplier;
      }
      return String(aValue).localeCompare(String(bValue), 'zh-CN') * multiplier;
    });
  }, [data, sort]);

  return {
    sorted,
    sortKey: sort.key,
    sortDirection: sort.direction,
    setSort: setSortKey,
    toggle,
  };
}
