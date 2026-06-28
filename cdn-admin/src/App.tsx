import { useState } from 'react';
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import type { Role } from './types';
import Layout from './components/Layout';
import ErrorBoundary from './components/ErrorBoundary';
import Login from './pages/Login';
import NotFound from './pages/NotFound';
import Notifications from './pages/Notifications';


// S-side pages
import SDashboard from './pages/s/Dashboard';
import STransactionStats from './pages/s/TransactionStats';
import SUserGrowth from './pages/s/UserGrowth';
import SMerchantAnalysis from './pages/s/MerchantAnalysis';
import SSites from './pages/s/Sites';
import SiteConfig from './pages/s/SiteConfig';
import SMerchants from './pages/s/Merchants';
import SMerchantAudit from './pages/s/MerchantAudit';
import SInvites from './pages/s/Invites';
import SProducts from './pages/s/Products';
import SCategories from './pages/s/Categories';
import SNodes from './pages/s/Nodes';
import SSkus from './pages/s/Skus';
import SOrders from './pages/s/Orders';
import SComplaints from './pages/s/Complaints';
import SAbnormalOrders from './pages/s/AbnormalOrders';
import SFinance from './pages/s/SFinance';
import SPayments from './pages/s/Payments';
import STemplates from './pages/s/Templates';
import SArticles from './pages/s/Articles';
import SAds from './pages/s/Ads';
import SCoupons from './pages/s/Coupons';
import SUsers from './pages/s/Users';
import SUserGroups from './pages/s/UserGroups';
import SUserLevels from './pages/s/UserLevels';
import SLuckyNumbers from './pages/s/LuckyNumbers';
import SUserRealname from './pages/s/UserRealname';
import SUserRank from './pages/s/UserRank';
import SAgentDock from './pages/s/AgentDock';
import SAgentProducts from './pages/s/AgentProducts';
import SAgentTree from './pages/s/AgentTree';
import SAgentCommission from './pages/s/AgentCommission';
import SAgentAudit from './pages/s/AgentAudit';
import SSettlementManual from './pages/s/SettlementManual';
import SSettlementAuto from './pages/s/SettlementAuto';
import SAlipayExport from './pages/s/AlipayExport';
import SGatewayConfig from './pages/s/GatewayConfig';
import SOperationLogs from './pages/s/OperationLogs';
import SApiDocs from './pages/s/ApiDocs';
import SRoles from './pages/s/Roles';
import SBackup from './pages/s/Backup';
import SSystem from './pages/s/System';

// B-side pages
import BDashboard from './pages/b/Dashboard';
import BSites from './pages/b/Sites';
import AddSite from './pages/b/AddSite';
import BPackages from './pages/b/Packages';
import MyPackages from './pages/b/MyPackages';
import Renew from './pages/b/Renew';
import BWhitelist from './pages/b/Whitelist';
import BFinance from './pages/b/Finance';
import BSettings from './pages/b/Settings';
import BOrders from './pages/b/MyOrders';
import BInvoice from './pages/b/Invoice';

function App() {
  const [role, setRole] = useState<Role | null>(null);

  const handleLogin = (r: Role) => setRole(r);
  const handleLogout = () => setRole(null);
  const handleSwitchRole = () => setRole(role === 's' ? 'b' : 's');

  if (!role) {
    return <Login onLogin={handleLogin} />;
  }

  return (
    <ErrorBoundary>
      <BrowserRouter>
        <Layout role={role} onSwitchRole={handleSwitchRole} onLogout={handleLogout}>
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
              <Route path="/" element={<Navigate to="/b/dashboard" replace />} />
              <Route path="*" element={<NotFound />} />
            </>
          )}
          </Routes>
        </Layout>
      </BrowserRouter>
    </ErrorBoundary>
  );
}

export default App;
