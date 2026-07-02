import type {
  Article,
  Coupon,
  Sku,
  Package,
  Merchant,
  User,
  Order,
  Category,
  AdSlot,
  Complaint,
  Gateway,
  Node,
  Product,
  InviteCode,
  UserGroup,
  UserLevel,
  RealnameRecord,
  RolePermission,
  BackupRecord,
  Site,
  MyPackage,
  BOrder,
  Invoice,
  WhitelistRecord,
  FinanceRecord,
  SettlementRecord,
  CommissionRecord,
  OperationLog,
  ApiDoc,
  Notification,
  DailyStat,
  MerchantStat,
  UserGrowthStat,
  Agent,
  AgentProduct,
  TemplateItem,
  LuckyNumber,
} from '../types';
import * as mock from '../data/mock';

const STORAGE_KEY = 'cdn-admin-data';
const API_BASE = '/api';

let usePhpApi = false;
let phpApiChecked = false;

function detectPhpApi(): Promise<boolean> {
  if (phpApiChecked) return Promise.resolve(usePhpApi);
  if (typeof window === 'undefined') {
    phpApiChecked = true;
    usePhpApi = false;
    return Promise.resolve(false);
  }
  if ((window as unknown as Record<string, unknown>).__CDN_ADMIN_RUNTIME__ === 'php') {
    phpApiChecked = true;
    usePhpApi = true;
    return Promise.resolve(true);
  }
  return fetch(`${API_BASE}/health.php`, { method: 'GET' })
    .then((res) => res.ok)
    .then((ok) => {
      usePhpApi = ok;
      phpApiChecked = true;
      return ok;
    })
    .catch(() => {
      usePhpApi = false;
      phpApiChecked = true;
      return false;
    });
}

async function phpRequest<T>(method: string, resource: string, id?: string, body?: object): Promise<T> {
  const url = new URL(`${API_BASE}/index.php`, window.location.origin);
  url.searchParams.set('resource', resource);
  if (id) url.searchParams.set('id', id);
  const res = await fetch(url.toString(), {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: body ? JSON.stringify(body) : undefined,
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`API error ${res.status}: ${text}`);
  }
  return res.json() as Promise<T>;
}

interface Store {
  articles: Article[];
  coupons: Coupon[];
  skus: Sku[];
  packages: Package[];
  merchants: Merchant[];
  users: User[];
  orders: Order[];
  categories: Category[];
  adSlots: AdSlot[];
  complaints: Complaint[];
  gateways: Gateway[];
  nodes: Node[];
  products: Product[];
  inviteCodes: InviteCode[];
  userGroups: UserGroup[];
  userLevels: UserLevel[];
  realnameRecords: RealnameRecord[];
  roles: RolePermission[];
  backupRecords: BackupRecord[];
  sites: Site[];
  myPackages: MyPackage[];
  bOrders: BOrder[];
  invoices: Invoice[];
  whitelistRecords: WhitelistRecord[];
  financeRecords: FinanceRecord[];
  settlementRecords: SettlementRecord[];
  commissionRecords: CommissionRecord[];
  operationLogs: OperationLog[];
  apiDocs: ApiDoc[];
  notifications: Notification[];
  dailyStats: DailyStat[];
  merchantStats: MerchantStat[];
  userGrowthStats: UserGrowthStat[];
  agents: Agent[];
  agentProducts: AgentProduct[];
  pcTemplates: TemplateItem[];
  mobileTemplates: TemplateItem[];
  cardTemplates: TemplateItem[];
  luckyNumbers: LuckyNumber[];
}

function loadStore(): Store {
  if (typeof window === 'undefined') return getDefaultStore();
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (raw) {
      const parsed = JSON.parse(raw) as Partial<Store>;
      return {
        articles: parsed.articles ?? mock.articles,
        coupons: parsed.coupons ?? mock.coupons,
        skus: parsed.skus ?? mock.skus,
        packages: parsed.packages ?? mock.packages,
        merchants: parsed.merchants ?? mock.merchants,
        users: parsed.users ?? mock.users,
        orders: parsed.orders ?? mock.orders,
        categories: parsed.categories ?? mock.categories,
        adSlots: parsed.adSlots ?? mock.adSlots,
        complaints: parsed.complaints ?? mock.complaints,
        gateways: parsed.gateways ?? mock.gateways,
        nodes: parsed.nodes ?? mock.nodes,
        products: parsed.products ?? mock.products,
        inviteCodes: parsed.inviteCodes ?? mock.inviteCodes,
        userGroups: parsed.userGroups ?? mock.userGroups,
        userLevels: parsed.userLevels ?? mock.userLevels,
        realnameRecords: parsed.realnameRecords ?? mock.realnameRecords,
        roles: parsed.roles ?? mock.roles,
        backupRecords: parsed.backupRecords ?? mock.backupRecords,
        sites: parsed.sites ?? mock.sites,
        myPackages: parsed.myPackages ?? mock.myPackages,
        bOrders: parsed.bOrders ?? mock.bOrders,
        invoices: parsed.invoices ?? mock.invoices,
        whitelistRecords: parsed.whitelistRecords ?? mock.whitelistRecords,
        financeRecords: parsed.financeRecords ?? mock.financeRecords,
        settlementRecords: parsed.settlementRecords ?? mock.settlementRecords,
        commissionRecords: parsed.commissionRecords ?? mock.commissionRecords,
        operationLogs: parsed.operationLogs ?? mock.operationLogs,
        apiDocs: parsed.apiDocs ?? mock.apiDocs,
        notifications: parsed.notifications ?? mock.notifications,
        dailyStats: parsed.dailyStats ?? mock.dailyStats,
        merchantStats: parsed.merchantStats ?? mock.merchantStats,
        userGrowthStats: parsed.userGrowthStats ?? mock.userGrowthStats,
        agents: parsed.agents ?? mock.agents,
        agentProducts: parsed.agentProducts ?? mock.agentProducts,
        pcTemplates: parsed.pcTemplates ?? mock.pcTemplates,
        mobileTemplates: parsed.mobileTemplates ?? mock.mobileTemplates,
        cardTemplates: parsed.cardTemplates ?? mock.cardTemplates,
        luckyNumbers: parsed.luckyNumbers ?? mock.luckyNumbers,
      };
    }
  } catch {
    // ignore
  }
  return getDefaultStore();
}

function getDefaultStore(): Store {
  return {
    articles: mock.articles,
    coupons: mock.coupons,
    skus: mock.skus,
    packages: mock.packages,
    merchants: mock.merchants,
    users: mock.users,
    orders: mock.orders,
    categories: mock.categories,
    adSlots: mock.adSlots,
    complaints: mock.complaints,
    gateways: mock.gateways,
    nodes: mock.nodes,
    products: mock.products,
    inviteCodes: mock.inviteCodes,
    userGroups: mock.userGroups,
    userLevels: mock.userLevels,
    realnameRecords: mock.realnameRecords,
    roles: mock.roles,
    backupRecords: mock.backupRecords,
    sites: mock.sites,
    myPackages: mock.myPackages,
    bOrders: mock.bOrders,
    invoices: mock.invoices,
    whitelistRecords: mock.whitelistRecords,
    financeRecords: mock.financeRecords,
    settlementRecords: mock.settlementRecords,
    commissionRecords: mock.commissionRecords,
    operationLogs: mock.operationLogs,
    apiDocs: mock.apiDocs,
    notifications: mock.notifications,
    dailyStats: mock.dailyStats,
    merchantStats: mock.merchantStats,
    userGrowthStats: mock.userGrowthStats,
    agents: mock.agents,
    agentProducts: mock.agentProducts,
    pcTemplates: mock.pcTemplates,
    mobileTemplates: mock.mobileTemplates,
    cardTemplates: mock.cardTemplates,
    luckyNumbers: mock.luckyNumbers,
  };
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

function createCrud<T extends { id: string }>(key: keyof Store, prefix: string) {
  return {
    fetch: async (): Promise<T[]> => {
      if (await detectPhpApi()) {
        return phpRequest<T[]>('GET', key as string);
      }
      await delay();
      return [...(store[key] as unknown as T[])];
    },
    create: async (payload: Omit<T, 'id'>): Promise<T> => {
      if (await detectPhpApi()) {
        return phpRequest<T>('POST', key as string, undefined, payload as object);
      }
      await delay();
      const items = store[key] as unknown as T[];
      const id = `${prefix}${String(items.length + 1).padStart(3, '0')}`;
      const item = { ...payload, id } as unknown as T;
      (store[key] as unknown as T[]) = [item, ...items];
      saveStore(store);
      return item;
    },
    update: async (id: string, payload: Partial<T>): Promise<T | null> => {
      if (await detectPhpApi()) {
        return phpRequest<T>('PUT', key as string, id, payload as object);
      }
      await delay();
      const items = store[key] as unknown as T[];
      const index = items.findIndex((i) => i.id === id);
      if (index === -1) return null;
      const updated = { ...items[index], ...payload };
      items[index] = updated;
      saveStore(store);
      return updated;
    },
    delete: async (id: string): Promise<boolean> => {
      if (await detectPhpApi()) {
        const res = await phpRequest<{ success: boolean }>('DELETE', key as string, id);
        return res.success;
      }
      await delay();
      const items = store[key] as unknown as T[];
      const before = items.length;
      (store[key] as unknown as T[]) = items.filter((i) => i.id !== id);
      saveStore(store);
      return (store[key] as unknown as T[]).length < before;
    },
  };
}

// Articles
export async function fetchArticles(): Promise<Article[]> {
  if (await detectPhpApi()) {
    return phpRequest<Article[]>('GET', 'articles');
  }
  await delay();
  return [...store.articles];
}

export async function createArticle(payload: Omit<Article, 'id' | 'publishAt'>): Promise<Article> {
  const articlePayload = {
    ...payload,
    publishAt: payload.status === 'published' ? new Date().toLocaleString('zh-CN') : '-',
  };
  if (await detectPhpApi()) {
    return phpRequest<Article>('POST', 'articles', undefined, articlePayload);
  }
  await delay();
  const article: Article = {
    ...articlePayload,
    id: `A${String(store.articles.length + 1).padStart(3, '0')}`,
  };
  store.articles = [article, ...store.articles];
  saveStore(store);
  return article;
}

export async function updateArticle(id: string, payload: Partial<Article>): Promise<Article | null> {
  if (await detectPhpApi()) {
    const body: Partial<Article> = { ...payload };
    if (payload.status === 'published') {
      body.publishAt = new Date().toLocaleString('zh-CN');
    }
    return phpRequest<Article>('PUT', 'articles', id, body);
  }
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
  if (await detectPhpApi()) {
    const res = await phpRequest<{ success: boolean }>('DELETE', 'articles', id);
    return res.success;
  }
  await delay();
  const before = store.articles.length;
  store.articles = store.articles.filter((a) => a.id !== id);
  saveStore(store);
  return store.articles.length < before;
}

// Coupons
export async function fetchCoupons(): Promise<Coupon[]> {
  if (await detectPhpApi()) {
    return phpRequest<Coupon[]>('GET', 'coupons');
  }
  await delay();
  return [...store.coupons];
}

export async function createCoupon(payload: Omit<Coupon, 'id' | 'received'>): Promise<Coupon> {
  const couponPayload = { ...payload, received: 0 };
  if (await detectPhpApi()) {
    return phpRequest<Coupon>('POST', 'coupons', undefined, couponPayload);
  }
  await delay();
  const coupon: Coupon = {
    ...couponPayload,
    id: `CO${String(store.coupons.length + 1).padStart(3, '0')}`,
  };
  store.coupons = [coupon, ...store.coupons];
  saveStore(store);
  return coupon;
}

// Skus
export async function fetchSkus(): Promise<Sku[]> {
  if (await detectPhpApi()) {
    return phpRequest<Sku[]>('GET', 'skus');
  }
  await delay();
  return [...store.skus];
}

export async function createSku(payload: Omit<Sku, 'id'>): Promise<Sku> {
  if (await detectPhpApi()) {
    return phpRequest<Sku>('POST', 'skus', undefined, payload);
  }
  await delay();
  const sku: Sku = {
    ...payload,
    id: `S${String(store.skus.length + 1).padStart(3, '0')}`,
  };
  store.skus = [sku, ...store.skus];
  saveStore(store);
  return sku;
}

// Packages
export const packagesCrud = createCrud<Package>('packages', 'PKG');
export const fetchPackages = packagesCrud.fetch;
export const createPackage = packagesCrud.create;
export const updatePackage = packagesCrud.update;
export const deletePackage = packagesCrud.delete;

// Merchants
export const merchantsCrud = createCrud<Merchant>('merchants', 'M');
export const fetchMerchants = merchantsCrud.fetch;
export const createMerchant = merchantsCrud.create;
export const updateMerchant = merchantsCrud.update;
export const deleteMerchant = merchantsCrud.delete;

// Users
export const usersCrud = createCrud<User>('users', 'U');
export const fetchUsers = usersCrud.fetch;
export const createUser = usersCrud.create;
export const updateUser = usersCrud.update;
export const deleteUser = usersCrud.delete;

// Orders
export const ordersCrud = createCrud<Order>('orders', 'O');
export const fetchOrders = ordersCrud.fetch;
export const createOrder = ordersCrud.create;
export const updateOrder = ordersCrud.update;
export const deleteOrder = ordersCrud.delete;

// Categories
export const categoriesCrud = createCrud<Category>('categories', 'C');
export const fetchCategories = categoriesCrud.fetch;
export const createCategory = categoriesCrud.create;
export const updateCategory = categoriesCrud.update;
export const deleteCategory = categoriesCrud.delete;

// AdSlots
export const adSlotsCrud = createCrud<AdSlot>('adSlots', 'AD');
export const fetchAdSlots = adSlotsCrud.fetch;
export const createAdSlot = adSlotsCrud.create;
export const updateAdSlot = adSlotsCrud.update;
export const deleteAdSlot = adSlotsCrud.delete;

// Complaints
export const complaintsCrud = createCrud<Complaint>('complaints', 'CP');
export const fetchComplaints = complaintsCrud.fetch;
export const createComplaint = complaintsCrud.create;
export const updateComplaint = complaintsCrud.update;
export const deleteComplaint = complaintsCrud.delete;

// Gateways
export const gatewaysCrud = createCrud<Gateway>('gateways', 'GW');
export const fetchGateways = gatewaysCrud.fetch;
export const createGateway = gatewaysCrud.create;
export const updateGateway = gatewaysCrud.update;
export const deleteGateway = gatewaysCrud.delete;

// Nodes
export const nodesCrud = createCrud<Node>('nodes', 'N');
export const fetchNodes = nodesCrud.fetch;
export const createNode = nodesCrud.create;
export const updateNode = nodesCrud.update;
export const deleteNode = nodesCrud.delete;

// Products
export const productsCrud = createCrud<Product>('products', 'P');
export const fetchProducts = productsCrud.fetch;
export const createProduct = productsCrud.create;
export const updateProduct = productsCrud.update;
export const deleteProduct = productsCrud.delete;

// InviteCodes
export const inviteCodesCrud = createCrud<InviteCode>('inviteCodes', 'I');
export const fetchInviteCodes = inviteCodesCrud.fetch;
export const createInviteCode = inviteCodesCrud.create;
export const updateInviteCode = inviteCodesCrud.update;
export const deleteInviteCode = inviteCodesCrud.delete;

// UserGroups
export const userGroupsCrud = createCrud<UserGroup>('userGroups', 'G');
export const fetchUserGroups = userGroupsCrud.fetch;
export const createUserGroup = userGroupsCrud.create;
export const updateUserGroup = userGroupsCrud.update;
export const deleteUserGroup = userGroupsCrud.delete;

// UserLevels
export const userLevelsCrud = createCrud<UserLevel>('userLevels', 'L');
export const fetchUserLevels = userLevelsCrud.fetch;
export const createUserLevel = userLevelsCrud.create;
export const updateUserLevel = userLevelsCrud.update;
export const deleteUserLevel = userLevelsCrud.delete;

// RealnameRecords
export const realnameRecordsCrud = createCrud<RealnameRecord>('realnameRecords', 'R');
export const fetchRealnameRecords = realnameRecordsCrud.fetch;
export const createRealnameRecord = realnameRecordsCrud.create;
export const updateRealnameRecord = realnameRecordsCrud.update;
export const deleteRealnameRecord = realnameRecordsCrud.delete;

// Roles
export const rolesCrud = createCrud<RolePermission>('roles', 'R');
export const fetchRoles = rolesCrud.fetch;
export const createRole = rolesCrud.create;
export const updateRole = rolesCrud.update;
export const deleteRole = rolesCrud.delete;

// BackupRecords
export const backupRecordsCrud = createCrud<BackupRecord>('backupRecords', 'B');
export const fetchBackupRecords = backupRecordsCrud.fetch;
export const createBackupRecord = backupRecordsCrud.create;
export const updateBackupRecord = backupRecordsCrud.update;
export const deleteBackupRecord = backupRecordsCrud.delete;

// Sites
export const sitesCrud = createCrud<Site>('sites', 'ST');
export const fetchSites = sitesCrud.fetch;
export const createSite = sitesCrud.create;
export const updateSite = sitesCrud.update;
export const deleteSite = sitesCrud.delete;

// MyPackages
export const myPackagesCrud = createCrud<MyPackage>('myPackages', 'MP');
export const fetchMyPackages = myPackagesCrud.fetch;
export const createMyPackage = myPackagesCrud.create;
export const updateMyPackage = myPackagesCrud.update;
export const deleteMyPackage = myPackagesCrud.delete;

// BOrders
export const bOrdersCrud = createCrud<BOrder>('bOrders', 'BO');
export const fetchBOrders = bOrdersCrud.fetch;
export const createBOrder = bOrdersCrud.create;
export const updateBOrder = bOrdersCrud.update;
export const deleteBOrder = bOrdersCrud.delete;

// Invoices
export const invoicesCrud = createCrud<Invoice>('invoices', 'INV');
export const fetchInvoices = invoicesCrud.fetch;
export const createInvoice = invoicesCrud.create;
export const updateInvoice = invoicesCrud.update;
export const deleteInvoice = invoicesCrud.delete;

// WhitelistRecords
export const whitelistRecordsCrud = createCrud<WhitelistRecord>('whitelistRecords', 'W');
export const fetchWhitelistRecords = whitelistRecordsCrud.fetch;
export const createWhitelistRecord = whitelistRecordsCrud.create;
export const updateWhitelistRecord = whitelistRecordsCrud.update;
export const deleteWhitelistRecord = whitelistRecordsCrud.delete;

// FinanceRecords
export const financeRecordsCrud = createCrud<FinanceRecord>('financeRecords', 'F');
export const fetchFinanceRecords = financeRecordsCrud.fetch;
export const createFinanceRecord = financeRecordsCrud.create;
export const updateFinanceRecord = financeRecordsCrud.update;
export const deleteFinanceRecord = financeRecordsCrud.delete;

// SettlementRecords
export const settlementRecordsCrud = createCrud<SettlementRecord>('settlementRecords', 'SET');
export const fetchSettlementRecords = settlementRecordsCrud.fetch;
export const createSettlementRecord = settlementRecordsCrud.create;
export const updateSettlementRecord = settlementRecordsCrud.update;
export const deleteSettlementRecord = settlementRecordsCrud.delete;

// CommissionRecords
export const commissionRecordsCrud = createCrud<CommissionRecord>('commissionRecords', 'CM');
export const fetchCommissionRecords = commissionRecordsCrud.fetch;
export const createCommissionRecord = commissionRecordsCrud.create;
export const updateCommissionRecord = commissionRecordsCrud.update;
export const deleteCommissionRecord = commissionRecordsCrud.delete;

// OperationLogs
export const operationLogsCrud = createCrud<OperationLog>('operationLogs', 'L');
export const fetchOperationLogs = operationLogsCrud.fetch;
export const createOperationLog = operationLogsCrud.create;
export const updateOperationLog = operationLogsCrud.update;
export const deleteOperationLog = operationLogsCrud.delete;

// ApiDocs
export const apiDocsCrud = createCrud<ApiDoc>('apiDocs', 'A');
export const fetchApiDocs = apiDocsCrud.fetch;
export const createApiDoc = apiDocsCrud.create;
export const updateApiDoc = apiDocsCrud.update;
export const deleteApiDoc = apiDocsCrud.delete;

// Notifications
export const notificationsCrud = createCrud<Notification>('notifications', 'NT');
export const fetchNotifications = notificationsCrud.fetch;
export const createNotification = notificationsCrud.create;
export const updateNotification = notificationsCrud.update;
export const deleteNotification = notificationsCrud.delete;

// Agents
export const agentsCrud = createCrud<Agent>('agents', 'AG');
export const fetchAgents = agentsCrud.fetch;
export const createAgent = agentsCrud.create;
export const updateAgent = agentsCrud.update;
export const deleteAgent = agentsCrud.delete;

// AgentProducts
export const agentProductsCrud = createCrud<AgentProduct>('agentProducts', 'AP');
export const fetchAgentProducts = agentProductsCrud.fetch;
export const createAgentProduct = agentProductsCrud.create;
export const updateAgentProduct = agentProductsCrud.update;
export const deleteAgentProduct = agentProductsCrud.delete;

// Templates
export const pcTemplatesCrud = createCrud<TemplateItem>('pcTemplates', 'TPC');
export const fetchPcTemplates = pcTemplatesCrud.fetch;
export const createPcTemplate = pcTemplatesCrud.create;
export const updatePcTemplate = pcTemplatesCrud.update;
export const deletePcTemplate = pcTemplatesCrud.delete;

export const mobileTemplatesCrud = createCrud<TemplateItem>('mobileTemplates', 'TPM');
export const fetchMobileTemplates = mobileTemplatesCrud.fetch;
export const createMobileTemplate = mobileTemplatesCrud.create;
export const updateMobileTemplate = mobileTemplatesCrud.update;
export const deleteMobileTemplate = mobileTemplatesCrud.delete;

export const cardTemplatesCrud = createCrud<TemplateItem>('cardTemplates', 'TC');
export const fetchCardTemplates = cardTemplatesCrud.fetch;
export const createCardTemplate = cardTemplatesCrud.create;
export const updateCardTemplate = cardTemplatesCrud.update;
export const deleteCardTemplate = cardTemplatesCrud.delete;

// LuckyNumbers
export const luckyNumbersCrud = createCrud<LuckyNumber>('luckyNumbers', 'LN');
export const fetchLuckyNumbers = luckyNumbersCrud.fetch;
export const createLuckyNumber = luckyNumbersCrud.create;
export const updateLuckyNumber = luckyNumbersCrud.update;
export const deleteLuckyNumber = luckyNumbersCrud.delete;

// Stats (readonly)
export async function fetchDailyStats(): Promise<DailyStat[]> {
  if (await detectPhpApi()) {
    return phpRequest<DailyStat[]>('GET', 'dailyStats');
  }
  await delay();
  return [...store.dailyStats];
}

export async function fetchMerchantStats(): Promise<MerchantStat[]> {
  if (await detectPhpApi()) {
    return phpRequest<MerchantStat[]>('GET', 'merchantStats');
  }
  await delay();
  return [...store.merchantStats];
}

export async function fetchUserGrowthStats(): Promise<UserGrowthStat[]> {
  if (await detectPhpApi()) {
    return phpRequest<UserGrowthStat[]>('GET', 'userGrowthStats');
  }
  await delay();
  return [...store.userGrowthStats];
}

export async function login(payload: { account: string; password: string; role: 's' | 'b' }): Promise<{ success: boolean; role?: 's' | 'b'; error?: string }> {
  if (await detectPhpApi()) {
    const res = await fetch(`${API_BASE}/login.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = (await res.json()) as { success?: boolean; role?: 's' | 'b'; error?: string };
    if (!res.ok) {
      return { success: false, error: data.error || '登录失败' };
    }
    return { success: data.success ?? true, role: data.role };
  }
  // 本地开发模式：前端模拟验证
  await delay();
  if (payload.password.length < 6) {
    return { success: false, error: '密码长度不能少于 6 位' };
  }
  return { success: true, role: payload.role };
}

export function resetStore() {
  store = getDefaultStore();
  saveStore(store);
}
