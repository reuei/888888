import { useState, useEffect } from 'react';

export function useLocalStorage<T>(key: string, initialValue: T | (() => T)): [T, (value: T | ((prev: T) => T)) => void] {
  const [stored, setStored] = useState<T>(() => {
    const resolved = typeof initialValue === 'function' ? (initialValue as () => T)() : initialValue;
    if (typeof window === 'undefined') return resolved;
    try {
      const item = window.localStorage.getItem(key);
      return item ? (JSON.parse(item) as T) : resolved;
    } catch {
      return resolved;
    }
  });

  const setValue = (value: T | ((prev: T) => T)) => {
    setStored((prev) => {
      const next = typeof value === 'function' ? (value as (prev: T) => T)(prev) : value;
      try {
        window.localStorage.setItem(key, JSON.stringify(next));
      } catch {
        // ignore storage errors
      }
      return next;
    });
  };

  useEffect(() => {
    const handle = (e: StorageEvent) => {
      if (e.key === key && e.newValue !== null) {
        try {
          setStored(JSON.parse(e.newValue) as T);
        } catch {
          // ignore parse errors
        }
      }
    };
    window.addEventListener('storage', handle);
    return () => window.removeEventListener('storage', handle);
  }, [key]);

  return [stored, setValue];
}
