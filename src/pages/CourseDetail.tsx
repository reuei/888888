import { useParams, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import {
  ArrowLeft,
  Clock,
  Users,
  Star,
  BookOpen,
  Play,
  CheckCircle,
  Lock,
} from 'lucide-react';
import { useCourseStore } from '@/stores/courseStore';

const levelLabels: Record<string, string> = {
  beginner: '初级',
  intermediate: '中级',
  advanced: '高级',
};

const languageLabels: Record<string, { name: string; flag: string }> = {
  english: { name: '英语', flag: '🇬🇧' },
  japanese: { name: '日语', flag: '🇯🇵' },
  korean: { name: '韩语', flag: '🇰🇷' },
};

export default function CourseDetail() {
  const { id } = useParams<{ id: string }>();
  const { courses } = useCourseStore();
  const course = courses.find((c) => c.id === id);

  if (!course) {
    return (
      <div className="min-h-screen bg-warm-50 flex items-center justify-center">
        <div className="text-warm-500">课程未找到</div>
      </div>
    );
  }

  const lang = languageLabels[course.language];
  const progress = (course.completedLessons / course.totalLessons) * 100;

  // Generate mock lessons
  const lessons = Array.from({ length: course.totalLessons }, (_, i) => ({
    id: i + 1,
    title: `第 ${i + 1} 课: ${
      i === 0
        ? '课程介绍'
        : i === 1
        ? '基础概念'
        : i === 2
        ? '核心语法'
        : i === 3
        ? '实用对话'
        : `进阶内容 ${i - 3}`
    }`,
    duration: 15,
    completed: i < course.completedLessons,
    locked: i > course.completedLessons,
  }));

  return (
    <div className="min-h-screen bg-warm-50">
      {/* Header Image */}
      <div className="relative h-64 sm:h-80">
        <img
          src={course.coverImage}
          alt={course.title}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent" />
        <div className="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
          <div className="max-w-7xl mx-auto">
            <Link
              to="/courses"
              className="inline-flex items-center gap-1 text-white/80 hover:text-white text-sm mb-4 transition-colors"
            >
              <ArrowLeft className="w-4 h-4" />
              返回课程列表
            </Link>
            <div className="flex flex-wrap items-center gap-2 mb-3">
              <span className="px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-medium backdrop-blur-sm">
                {lang.flag} {lang.name}
              </span>
              <span className="px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-medium backdrop-blur-sm">
                {levelLabels[course.level]}
              </span>
            </div>
            <h1 className="font-heading font-bold text-3xl sm:text-4xl text-white mb-2">
              {course.title}
            </h1>
            <p className="text-white/80 max-w-2xl">{course.description}</p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Main Content */}
          <div className="lg:col-span-2">
            {/* Stats */}
            <motion.div
              className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
            >
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div className="text-center">
                  <div className="flex items-center justify-center gap-1 text-warm-400 text-sm mb-1">
                    <BookOpen className="w-4 h-4" />
                    课时
                  </div>
                  <div className="font-mono font-bold text-xl text-warm-900">
                    {course.totalLessons}
                  </div>
                </div>
                <div className="text-center">
                  <div className="flex items-center justify-center gap-1 text-warm-400 text-sm mb-1">
                    <Clock className="w-4 h-4" />
                    时长
                  </div>
                  <div className="font-mono font-bold text-xl text-warm-900">
                    {Math.round(course.duration / 60)}h
                  </div>
                </div>
                <div className="text-center">
                  <div className="flex items-center justify-center gap-1 text-warm-400 text-sm mb-1">
                    <Users className="w-4 h-4" />
                    学员
                  </div>
                  <div className="font-mono font-bold text-xl text-warm-900">
                    {course.studentsCount.toLocaleString()}
                  </div>
                </div>
                <div className="text-center">
                  <div className="flex items-center justify-center gap-1 text-warm-400 text-sm mb-1">
                    <Star className="w-4 h-4" />
                    评分
                  </div>
                  <div className="font-mono font-bold text-xl text-warm-900">
                    {course.rating}
                  </div>
                </div>
              </div>
            </motion.div>

            {/* Progress */}
            <motion.div
              className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1 }}
            >
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-heading font-bold text-lg text-warm-900">
                  学习进度
                </h3>
                <span className="font-mono font-bold text-primary-600">
                  {Math.round(progress)}%
                </span>
              </div>
              <div className="h-3 bg-warm-100 rounded-full overflow-hidden">
                <motion.div
                  className="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full"
                  initial={{ width: 0 }}
                  animate={{ width: `${progress}%` }}
                  transition={{ duration: 1, ease: 'easeOut' }}
                />
              </div>
              <div className="mt-2 text-sm text-warm-500">
                已完成 {course.completedLessons} / {course.totalLessons} 课时
              </div>
            </motion.div>

            {/* Lessons */}
            <motion.div
              className="bg-white rounded-2xl shadow-sm border border-warm-200 overflow-hidden"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.2 }}
            >
              <div className="p-6 border-b border-warm-200">
                <h3 className="font-heading font-bold text-lg text-warm-900">
                  课程大纲
                </h3>
              </div>
              <div className="divide-y divide-warm-100">
                {lessons.map((lesson, i) => (
                  <div
                    key={lesson.id}
                    className={`flex items-center gap-4 p-4 hover:bg-warm-50 transition-colors ${
                      lesson.locked ? 'opacity-60' : ''
                    }`}
                  >
                    <div className="w-8 text-center">
                      {lesson.completed ? (
                        <CheckCircle className="w-5 h-5 text-green-500 mx-auto" />
                      ) : lesson.locked ? (
                        <Lock className="w-5 h-5 text-warm-400 mx-auto" />
                      ) : (
                        <div className="w-5 h-5 rounded-full border-2 border-primary-300 mx-auto" />
                      )}
                    </div>
                    <div className="flex-1">
                      <div className="font-medium text-warm-900 text-sm">
                        {lesson.title}
                      </div>
                      <div className="text-xs text-warm-400 mt-0.5">
                        {lesson.duration} 分钟
                      </div>
                    </div>
                    {!lesson.locked && (
                      <button className="p-2 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition-colors">
                        <Play className="w-4 h-4" />
                      </button>
                    )}
                  </div>
                ))}
              </div>
            </motion.div>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            <motion.div
              className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.3 }}
            >
              <h3 className="font-heading font-bold text-lg text-warm-900 mb-4">
                课程标签
              </h3>
              <div className="flex flex-wrap gap-2">
                {course.tags.map((tag) => (
                  <span
                    key={tag}
                    className="px-3 py-1.5 rounded-lg bg-warm-100 text-warm-600 text-sm"
                  >
                    {tag}
                  </span>
                ))}
              </div>
            </motion.div>

            <motion.div
              className="bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl p-6 text-white"
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.4 }}
            >
              <h3 className="font-heading font-bold text-lg mb-2">
                开始学习
              </h3>
              <p className="text-primary-200 text-sm mb-4">
                继续你的学习之旅，每天进步一点点
              </p>
              <Link
                to="/learn"
                className="block w-full py-3 rounded-xl bg-accent-500 text-primary-950 font-bold text-center hover:bg-accent-400 transition-colors"
              >
                进入学习
              </Link>
            </motion.div>
          </div>
        </div>
      </div>
    </div>
  );
}
