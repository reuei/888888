import { useState, Suspense, lazy } from 'react';
import { HashRouter, Routes, Route, Navigate } from 'react-router-dom';
import type { Role } from './types';
import Layout from './components/Layout';
import ErrorBoundary from './components/ErrorBoundary';
import Loading from './components/Loading';
import Login from './pages/Login';
import Home from './pages/Home';
import NotFound from './pages/NotFound';
import Notifications from './pages/Notifications';
import Profile from './pages/Profile';

// S-side pages
const SDashboard = lazy(() => import('./pages/s/Dashboard'));
const STransactionStats = lazy(() => import('./pages/s/TransactionStats'));
const SUserGrowth = lazy(() => import('./pages/s/UserGrowth'));
const SMerchantAnalysis = lazy(() => import('./pages/s/MerchantAnalysis'));
const SSites = lazy(() => import('./pages/s/Sites'));
const SiteConfig = lazy(() => import('./pages/s/SiteConfig'));
const SMerchants = lazy(() => import('./pages/s/Merchants'));
const SMerchantAudit = lazy(() => import('./pages/s/MerchantAudit'));
const SInvites = lazy(() => import('./pages/s/Invites'));
const SProducts = lazy(() => import('./pages/s/Products'));
const SCategories = lazy(() => import('./pages/s/Categories'));
const SNodes = lazy(() => import('./pages/s/Nodes'));
const SSkus = lazy(() => import('./pages/s/Skus'));
const SOrders = lazy(() => import('./pages/s/Orders'));
const SComplaints = lazy(() => import('./pages/s/Complaints'));
const SAbnormalOrders = lazy(() => import('./pages/s/AbnormalOrders'));
const SFinance = lazy(() => import('./pages/s/SFinance'));
const SPayments = lazy(() => import('./pages/s/Payments'));
const STemplates = lazy(() => import('./pages/s/Templates'));
const SArticles = lazy(() => import('./pages/s/Articles'));
const SAds = lazy(() => import('./pages/s/Ads'));
const SCoupons = lazy(() => import('./pages/s/Coupons'));
const SUsers = lazy(() => import('./pages/s/Users'));
const SUserGroups = lazy(() => import('./pages/s/UserGroups'));
const SUserLevels = lazy(() => import('./pages/s/UserLevels'));
const SLuckyNumbers = lazy(() => import('./pages/s/LuckyNumbers'));
const SUserRealname = lazy(() => import('./pages/s/UserRealname'));
const SUserRank = lazy(() => import('./pages/s/UserRank'));
const SAgentDock = lazy(() => import('./pages/s/AgentDock'));
const SAgentProducts = lazy(() => import('./pages/s/AgentProducts'));
const SAgentTree = lazy(() => import('./pages/s/AgentTree'));
const SAgentCommission = lazy(() => import('./pages/s/AgentCommission'));
const SAgentAudit = lazy(() => import('./pages/s/AgentAudit'));
const SSettlementManual = lazy(() => import('./pages/s/SettlementManual'));
const SSettlementAuto = lazy(() => import('./pages/s/SettlementAuto'));
const SAlipayExport = lazy(() => import('./pages/s/AlipayExport'));
const SGatewayConfig = lazy(() => import('./pages/s/GatewayConfig'));
const SOperationLogs = lazy(() => import('./pages/s/OperationLogs'));
const SApiDocs = lazy(() => import('./pages/s/ApiDocs'));
const SRoles = lazy(() => import('./pages/s/Roles'));
const SBackup = lazy(() => import('./pages/s/Backup'));
const SSystem = lazy(() => import('./pages/s/System'));

// B-side pages
const BDashboard = lazy(() => import('./pages/b/Dashboard'));
const BSites = lazy(() => import('./pages/b/Sites'));
const AddSite = lazy(() => import('./pages/b/AddSite'));
const BPackages = lazy(() => import('./pages/b/Packages'));
const MyPackages = lazy(() => import('./pages/b/MyPackages'));
const Renew = lazy(() => import('./pages/b/Renew'));
const BWhitelist = lazy(() => import('./pages/b/Whitelist'));
const BFinance = lazy(() => import('./pages/b/Finance'));
const BSettings = lazy(() => import('./pages/b/Settings'));
const BOrders = lazy(() => import('./pages/b/MyOrders'));
const BInvoice = lazy(() => import('./pages/b/Invoice'));

function App() {
  const [role, setRole] = useState<Role | null>(() => {
    if (typeof window === 'undefined') return null;
    const saved = localStorage.getItem('role') as Role | null;
    return saved === 's' || saved === 'b' ? saved : null;
  });

  const handleLogin = (r: Role, remember = true) => {
    if (remember) {
      localStorage.setItem('role', r);
    } else {
      localStorage.removeItem('role');
    }
    setRole(r);
  };
  const handleLogout = () => {
    localStorage.removeItem('role');
    setRole(null);
  };
  const handleSwitchRole = () => {
    const next = role === 's' ? 'b' : 's';
    localStorage.setItem('role', next);
    setRole(next);
  };

  if (!role) {
    return (
      <ErrorBoundary>
        <HashRouter>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/login" element={<Login onLogin={handleLogin} />} />
            <Route path="*" element={<Navigate to="/" replace />} />
          </Routes>
        </HashRouter>
      </ErrorBoundary>
    );
  }

  return (
    <ErrorBoundary>
      <HashRouter>
        <Layout role={role} onSwitchRole={handleSwitchRole} onLogout={handleLogout}>
          <Suspense fallback={<Loading />}>
            <Routes>
              {role === 's' ? (
                <>
                  <Route path="/s/dashboard" element={<SDashboard />} />
                  <Route path="/s/transaction-stats" element={<STransactionStats />} />
                  <Route path="/s/user-growth" element={<SUserGrowth />} />
                  <Route path="/s/merchant-analysis" element={<SMerchantAnalysis />} />
                  <Route path="/s/sites" element={<SSites />} />
                  <Route path="/s/site-config" element={<SiteConfig />} />
                  <Route path="/s/merchants" element={<SMerchants />} />
                  <Route path="/s/merchant-audit" element={<SMerchantAudit />} />
                  <Route path="/s/invites" element={<SInvites />} />
                  <Route path="/s/products" element={<SProducts />} />
                  <Route path="/s/categories" element={<SCategories />} />
                  <Route path="/s/nodes" element={<SNodes />} />
                  <Route path="/s/skus" element={<SSkus />} />
                  <Route path="/s/orders" element={<SOrders />} />
                  <Route path="/s/complaints" element={<SComplaints />} />
                  <Route path="/s/abnormal-orders" element={<SAbnormalOrders />} />
                  <Route path="/s/finance" element={<SFinance />} />
                  <Route path="/s/payments" element={<SPayments />} />
                  <Route path="/s/templates" element={<STemplates />} />
                  <Route path="/s/articles" element={<SArticles />} />
                  <Route path="/s/ads" element={<SAds />} />
                  <Route path="/s/coupons" element={<SCoupons />} />
                  <Route path="/s/users" element={<SUsers />} />
                  <Route path="/s/user-groups" element={<SUserGroups />} />
                  <Route path="/s/user-levels" element={<SUserLevels />} />
                  <Route path="/s/lucky-numbers" element={<SLuckyNumbers />} />
                  <Route path="/s/user-realname" element={<SUserRealname />} />
                  <Route path="/s/user-rank" element={<SUserRank />} />
                  <Route path="/s/agent-dock" element={<SAgentDock />} />
                  <Route path="/s/agent-products" element={<SAgentProducts />} />
                  <Route path="/s/agent-tree" element={<SAgentTree />} />
                  <Route path="/s/agent-commission" element={<SAgentCommission />} />
                  <Route path="/s/agent-audit" element={<SAgentAudit />} />
                  <Route path="/s/settlement-manual" element={<SSettlementManual />} />
                  <Route path="/s/settlement-auto" element={<SSettlementAuto />} />
                  <Route path="/s/alipay-export" element={<SAlipayExport />} />
                  <Route path="/s/gateway-config" element={<SGatewayConfig />} />
                  <Route path="/s/operation-logs" element={<SOperationLogs />} />
                  <Route path="/s/api-docs" element={<SApiDocs />} />
                  <Route path="/s/roles" element={<SRoles />} />
                  <Route path="/s/backup" element={<SBackup />} />
                  <Route path="/s/system" element={<SSystem />} />
                  <Route path="/s/notifications" element={<Notifications role="s" />} />
                  <Route path="/s/profile" element={<Profile role="s" />} />
                  <Route path="/" element={<Navigate to="/s/dashboard" replace />} />
                  <Route path="*" element={<NotFound />} />
                </>
              ) : (
                <>
                  <Route path="/b/dashboard" element={<BDashboard />} />
                  <Route path="/b/sites" element={<BSites />} />
                  <Route path="/b/add-site" element={<AddSite />} />
                  <Route path="/b/packages" element={<BPackages />} />
                  <Route path="/b/my-packages" element={<MyPackages />} />
                  <Route path="/b/renew" element={<Renew />} />
                  <Route path="/b/orders" element={<BOrders />} />
                  <Route path="/b/invoice" element={<BInvoice />} />
                  <Route path="/b/whitelist" element={<BWhitelist />} />
                  <Route path="/b/finance" element={<BFinance />} />
                  <Route path="/b/settings" element={<BSettings />} />
                  <Route path="/b/notifications" element={<Notifications role="b" />} />
                  <Route path="/b/profile" element={<Profile role="b" />} />
                  <Route path="/" element={<Navigate to="/b/dashboard" replace />} />
                  <Route path="*" element={<NotFound />} />
                </>
              )}
            </Routes>
          </Suspense>
        </Layout>
      </HashRouter>
    </ErrorBoundary>
  );
}

export default App;
