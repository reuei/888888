import { BrowserRouter as Router, Routes, Route, Outlet } from 'react-router-dom'
import Layout from '@/components/Layout'
import Home from '@/pages/Home'
import About from '@/pages/About'
import Company from '@/pages/Company'
import Products from '@/pages/Products'
import Partners from '@/pages/Partners'
import Contact from '@/pages/Contact'
import GlobalRedirect from '@/pages/GlobalRedirect'
import AdminLogin from '@/pages/AdminLogin'
import ProtectedRoute from '@/components/ProtectedRoute'
import AdminLayout from '@/components/AdminLayout'
import Dashboard from '@/pages/admin/Dashboard'
import Settings from '@/pages/admin/Settings'
import Slides from '@/pages/admin/Slides'
import AdminProducts from '@/pages/admin/Products'
import AdminPartners from '@/pages/admin/Partners'
import Links from '@/pages/admin/Links'
import Certificates from '@/pages/admin/Certificates'
import Testimonials from '@/pages/admin/Testimonials'

function FrontLayout() {
  return (
    <Layout>
      <Outlet />
    </Layout>
  )
}

function AdminShell() {
  return (
    <ProtectedRoute>
      <AdminLayout>
        <Outlet />
      </AdminLayout>
    </ProtectedRoute>
  )
}

export default function App() {
  return (
    <Router>
      <Routes>
        <Route element={<FrontLayout />}>
          <Route path="/" element={<Home />} />
          <Route path="/about" element={<About />} />
          <Route path="/company" element={<Company />} />
          <Route path="/products" element={<Products />} />
          <Route path="/partners" element={<Partners />} />
          <Route path="/contact" element={<Contact />} />
          <Route path="/global" element={<GlobalRedirect />} />
        </Route>

        <Route path="/admin/login" element={<AdminLogin />} />
        <Route element={<AdminShell />}>
          <Route path="/admin" element={<Dashboard />} />
          <Route path="/admin/settings" element={<Settings />} />
          <Route path="/admin/slides" element={<Slides />} />
          <Route path="/admin/products" element={<AdminProducts />} />
          <Route path="/admin/partners" element={<AdminPartners />} />
          <Route path="/admin/links" element={<Links />} />
          <Route path="/admin/certificates" element={<Certificates />} />
          <Route path="/admin/testimonials" element={<Testimonials />} />
        </Route>
      </Routes>
    </Router>
  )
}
