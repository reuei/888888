import type { Article, Coupon, Sku } from '../types';
import * as mock from '../data/mock';

const STORAGE_KEY = 'cdn-admin-data';

interface Store {
  articles: Article[];
  coupons: Coupon[];
  skus: Sku[];
}

function loadStore(): Store {
  if (typeof window === 'undefined') return { articles: mock.articles, coupons: mock.coupons, skus: mock.skus };
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) {
      const parsed = JSON.parse(raw) as Partial<Store>;
      return {
        articles: parsed.articles ?? mock.articles,
        coupons: parsed.coupons ?? mock.coupons,
        skus: parsed.skus ?? mock.skus,
      };
    }
  } catch {
    // ignore
  }
  return { articles: mock.articles, coupons: mock.coupons, skus: mock.skus };
}

function saveStore(store: Store) {
  if (typeof window === 'undefined') return;
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(store));
  } catch {
    // ignore
  }
}

let store = loadStore();

function delay(ms = 300) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export async function fetchArticles(): Promise<Article[]> {
  await delay();
  return [...store.articles];
}

export async function createArticle(payload: Omit<Article, 'id' | 'publishAt'>): Promise<Article> {
  await delay();
  const article: Article = {
    ...payload,
    id: `A${String(store.articles.length + 1).padStart(3, '0')}`,
    publishAt: payload.status === 'published' ? new Date().toLocaleString('zh-CN') : '-',
  };
  store.articles = [article, ...store.articles];
  saveStore(store);
  return article;
}

export async function updateArticle(id: string, payload: Partial<Article>): Promise<Article | null> {
  await delay();
  const index = store.articles.findIndex((a) => a.id === id);
  if (index === -1) return null;
  const updated = { ...store.articles[index], ...payload };
  if (payload.status === 'published' && store.articles[index].status !== 'published') {
    updated.publishAt = new Date().toLocaleString('zh-CN');
  }
  store.articles[index] = updated;
  saveStore(store);
  return updated;
}

export async function deleteArticle(id: string): Promise<boolean> {
  await delay();
  const before = store.articles.length;
  store.articles = store.articles.filter((a) => a.id !== id);
  saveStore(store);
  return store.articles.length < before;
}

export async function fetchCoupons(): Promise<Coupon[]> {
  await delay();
  return [...store.coupons];
}

export async function createCoupon(payload: Omit<Coupon, 'id' | 'received'>): Promise<Coupon> {
  await delay();
  const coupon: Coupon = {
    ...payload,
    id: `CO${String(store.coupons.length + 1).padStart(3, '0')}`,
    received: 0,
  };
  store.coupons = [coupon, ...store.coupons];
  saveStore(store);
  return coupon;
}

export async function fetchSkus(): Promise<Sku[]> {
  await delay();
  return [...store.skus];
}

export async function createSku(payload: Omit<Sku, 'id'>): Promise<Sku> {
  await delay();
  const sku: Sku = {
    ...payload,
    id: `S${String(store.skus.length + 1).padStart(3, '0')}`,
  };
  store.skus = [sku, ...store.skus];
  saveStore(store);
  return sku;
}

export function resetStore() {
  store = { articles: mock.articles, coupons: mock.coupons, skus: mock.skus };
  saveStore(store);
}
