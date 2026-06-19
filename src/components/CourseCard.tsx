import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import { Clock, Users, Star, BookOpen } from 'lucide-react';
import type { Course } from '@/types';

interface Props {
  course: Course;
  index?: number;
}

const languageLabels: Record<string, { name: string; flag: string; color: string }> = {
  english: { name: '英语', flag: '🇬🇧', color: 'bg-blue-100 text-blue-700' },
  japanese: { name: '日语', flag: '🇯🇵', color: 'bg-red-100 text-red-700' },
  korean: { name: '韩语', flag: '🇰🇷', color: 'bg-pink-100 text-pink-700' },
};

const levelLabels: Record<string, string> = {
  beginner: '初级',
  intermediate: '中级',
  advanced: '高级',
};

export default function CourseCard({ course, index = 0 }: Props) {
  const lang = languageLabels[course.language];
  const progress = (course.completedLessons / course.totalLessons) * 100;

  return (
    <motion.div
      initial={{ opacity: 0, y: 30 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ duration: 0.5, delay: index * 0.1 }}
    >
      <Link to={`/courses/${course.id}`} className="group block">
        <div className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-2 border border-warm-200">
          {/* Cover */}
          <div className="relative h-48 overflow-hidden">
            <img
              src={course.coverImage}
              alt={course.title}
              className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent" />
            <div className="absolute top-3 left-3 flex gap-2">
              <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${lang.color}`}>
                {lang.flag} {lang.name}
              </span>
              <span className="px-2.5 py-1 rounded-full text-xs font-medium bg-white/90 text-warm-700">
                {levelLabels[course.level]}
              </span>
            </div>
            <div className="absolute bottom-3 right-3 flex items-center gap-1 px-2 py-1 rounded-lg bg-black/50 text-white text-xs">
              <Star className="w-3 h-3 fill-yellow-400 text-yellow-400" />
              {course.rating}
            </div>
          </div>

          {/* Content */}
          <div className="p-5">
            <h3 className="font-heading font-bold text-lg text-warm-900 mb-2 line-clamp-1 group-hover:text-primary-700 transition-colors">
              {course.title}
            </h3>
            <p className="text-sm text-warm-500 mb-4 line-clamp-2 leading-relaxed">
              {course.description}
            </p>

            {/* Meta */}
            <div className="flex items-center gap-4 text-xs text-warm-500 mb-4">
              <span className="flex items-center gap-1">
                <BookOpen className="w-3.5 h-3.5" />
                {course.totalLessons} 课时
              </span>
              <span className="flex items-center gap-1">
                <Clock className="w-3.5 h-3.5" />
                {Math.round(course.duration / 60)} 小时
              </span>
              <span className="flex items-center gap-1">
                <Users className="w-3.5 h-3.5" />
                {course.studentsCount.toLocaleString()}
              </span>
            </div>

            {/* Progress */}
            {course.completedLessons > 0 && (
              <div className="space-y-1.5">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-warm-500">学习进度</span>
                  <span className="font-medium text-primary-700">{Math.round(progress)}%</span>
                </div>
                <div className="h-2 bg-warm-100 rounded-full overflow-hidden">
                  <div
                    className="h-full bg-gradient-to-r from-primary-500 to-primary-600 rounded-full transition-all duration-500"
                    style={{ width: `${progress}%` }}
                  />
                </div>
              </div>
            )}

            {/* Tags */}
            <div className="flex flex-wrap gap-1.5 mt-4">
              {course.tags.slice(0, 3).map((tag) => (
                <span
                  key={tag}
                  className="px-2 py-0.5 rounded-md bg-warm-100 text-warm-600 text-xs"
                >
                  {tag}
                </span>
              ))}
            </div>
          </div>
        </div>
      </Link>
    </motion.div>
  );
}
