import { BrowserRouter, Routes, Route } from 'react-router-dom';
import Layout from '@/components/Layout';
import Home from '@/pages/Home';
import Courses from '@/pages/Courses';
import CourseDetail from '@/pages/CourseDetail';
import Learn from '@/pages/Learn';
import Vocabulary from '@/pages/Vocabulary';
import Grammar from '@/pages/Grammar';
import Speaking from '@/pages/Speaking';
import Listening from '@/pages/Listening';
import Progress from '@/pages/Progress';
import Community from '@/pages/Community';
import Profile from '@/pages/Profile';
import Login from '@/pages/Login';
import Register from '@/pages/Register';

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route element={<Layout />}>
          <Route path="/" element={<Home />} />
          <Route path="/courses" element={<Courses />} />
          <Route path="/courses/:id" element={<CourseDetail />} />
          <Route path="/learn" element={<Learn />} />
          <Route path="/learn/vocabulary" element={<Vocabulary />} />
          <Route path="/learn/grammar" element={<Grammar />} />
          <Route path="/learn/speaking" element={<Speaking />} />
          <Route path="/learn/listening" element={<Listening />} />
          <Route path="/progress" element={<Progress />} />
          <Route path="/community" element={<Community />} />
          <Route path="/profile" element={<Profile />} />
        </Route>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
