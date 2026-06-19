import { useState } from 'react';
import { motion } from 'framer-motion';
import { Search, SlidersHorizontal } from 'lucide-react';
import CourseCard from '@/components/CourseCard';
import { useCourseStore } from '@/stores/courseStore';

const languages = [
  { value: 'all', label: '全部语言', flag: '🌍' },
  { value: 'english', label: '英语', flag: '🇬🇧' },
  { value: 'japanese', label: '日语', flag: '🇯🇵' },
  { value: 'korean', label: '韩语', flag: '🇰🇷' },
];

const levels = [
  { value: 'all', label: '全部等级' },
  { value: 'beginner', label: '初级' },
  { value: 'intermediate', label: '中级' },
  { value: 'advanced', label: '高级' },
];

export default function Courses() {
  const { filteredCourses, selectedLanguage, selectedLevel, setFilter } = useCourseStore();
  const [searchQuery, setSearchQuery] = useState('');

  const displayedCourses = filteredCourses.filter((c) =>
    c.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
    c.description.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-warm-50">
      {/* Header */}
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              课程中心
            </h1>
            <p className="text-warm-500">
              探索适合你的语言课程，从入门到精通
            </p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Filters */}
        <motion.div
          className="bg-white rounded-2xl p-4 mb-8 shadow-sm border border-warm-200"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.1 }}
        >
          <div className="flex flex-col lg:flex-row gap-4">
            {/* Search */}
            <div className="relative flex-1">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-warm-400" />
              <input
                type="text"
                placeholder="搜索课程..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="w-full pl-10 pr-4 py-2.5 rounded-xl border border-warm-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
              />
            </div>

            {/* Language filter */}
            <div className="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
              <SlidersHorizontal className="w-4 h-4 text-warm-400 shrink-0" />
              {languages.map((lang) => (
                <button
                  key={lang.value}
                  onClick={() => setFilter(lang.value, selectedLevel || undefined)}
                  className={`px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-all ${
                    selectedLanguage === lang.value || (lang.value === 'all' && !selectedLanguage)
                      ? 'bg-primary-600 text-white'
                      : 'bg-warm-100 text-warm-600 hover:bg-warm-200'
                  }`}
                >
                  {lang.flag} {lang.label}
                </button>
              ))}
            </div>

            {/* Level filter */}
            <div className="flex items-center gap-2 overflow-x-auto pb-1 lg:pb-0">
              {levels.map((level) => (
                <button
                  key={level.value}
                  onClick={() => setFilter(selectedLanguage || undefined, level.value)}
                  className={`px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-all ${
                    selectedLevel === level.value || (level.value === 'all' && !selectedLevel)
                      ? 'bg-accent-500 text-primary-950'
                      : 'bg-warm-100 text-warm-600 hover:bg-warm-200'
                  }`}
                >
                  {level.label}
                </button>
              ))}
            </div>
          </div>
        </motion.div>

        {/* Course Grid */}
        {displayedCourses.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {displayedCourses.map((course, i) => (
              <CourseCard key={course.id} course={course} index={i} />
            ))}
          </div>
        ) : (
          <div className="text-center py-20">
            <div className="text-warm-400 text-lg mb-2">没有找到匹配的课程</div>
            <p className="text-warm-500 text-sm">尝试调整筛选条件或搜索关键词</p>
          </div>
        )}
      </div>
    </div>
  );
}
