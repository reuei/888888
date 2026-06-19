import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import {
  ArrowRight,
  BookOpen,
  Users,
  Clock,
  Trophy,
  Sparkles,
  Flame,
  Globe,
  Target,
  Headphones,
} from 'lucide-react';
import CourseCard from '@/components/CourseCard';
import AnimatedCounter from '@/components/AnimatedCounter';
import { useCourseStore } from '@/stores/courseStore';
import { achievements } from '@/data/achievements';

const floatingChars = [
  { char: 'A', x: '10%', y: '20%', delay: 0, size: 'text-4xl' },
  { char: 'あ', x: '85%', y: '15%', delay: 1, size: 'text-5xl' },
  { char: '한', x: '75%', y: '70%', delay: 2, size: 'text-4xl' },
  { char: 'Hello', x: '20%', y: '80%', delay: 0.5, size: 'text-2xl' },
  { char: 'こん', x: '60%', y: '25%', delay: 1.5, size: 'text-3xl' },
  { char: '안녕', x: '40%', y: '60%', delay: 2.5, size: 'text-3xl' },
  { char: 'B', x: '90%', y: '45%', delay: 0.8, size: 'text-3xl' },
  { char: '語', x: '5%', y: '50%', delay: 1.2, size: 'text-4xl' },
];

const achievementIcons: Record<string, React.ReactNode> = {
  Sparkles: <Sparkles className="w-5 h-5" />,
  BookOpen: <BookOpen className="w-5 h-5" />,
  PenTool: <Target className="w-5 h-5" />,
  Mic: <Globe className="w-5 h-5" />,
  Headphones: <BookOpen className="w-5 h-5" />,
  Flame: <Flame className="w-5 h-5" />,
  Trophy: <Trophy className="w-5 h-5" />,
  Globe: <Globe className="w-5 h-5" />,
  MessageCircle: <Users className="w-5 h-5" />,
  Target: <Target className="w-5 h-5" />,
  Sunrise: <Sparkles className="w-5 h-5" />,
  Moon: <Sparkles className="w-5 h-5" />,
};

export default function Home() {
  const { courses } = useCourseStore();
  const featuredCourses = courses.slice(0, 3);

  return (
    <div>
      {/* Hero Section */}
      <section className="relative min-h-[90vh] hero-gradient overflow-hidden flex items-center">
        {/* Floating characters */}
        {floatingChars.map((item, i) => (
          <motion.div
            key={i}
            className={`absolute ${item.size} font-heading font-bold text-white/10 float-char`}
            style={{
              left: item.x,
              top: item.y,
              animationDelay: `${item.delay}s`,
            }}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ delay: item.delay + 0.5 }}
          >
            {item.char}
          </motion.div>
        ))}

        <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
          <div className="max-w-3xl">
            <motion.div
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8 }}
            >
              <span className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-accent-300 text-sm font-medium mb-6 backdrop-blur-sm border border-white/10">
                <Sparkles className="w-4 h-4" />
                支持英语、日语、韩语等多种语言
              </span>
            </motion.div>

            <motion.h1
              className="font-heading font-extrabold text-5xl sm:text-6xl lg:text-7xl text-white leading-tight mb-6"
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.2 }}
            >
              开启你的
              <br />
              <span className="gradient-text">语言之旅</span>
            </motion.h1>

            <motion.p
              className="text-lg sm:text-xl text-warm-300 mb-10 max-w-xl leading-relaxed"
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.4 }}
            >
              沉浸式多语种学习平台，通过分级课程、互动练习和个性化路径，
              让语言学习变得高效而有趣。
            </motion.p>

            <motion.div
              className="flex flex-wrap gap-4"
              initial={{ opacity: 0, y: 30 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.8, delay: 0.6 }}
            >
              <Link
                to="/courses"
                className="inline-flex items-center gap-2 px-8 py-4 bg-accent-500 hover:bg-accent-600 text-primary-950 font-bold rounded-xl transition-all hover:scale-105 animate-pulse-glow"
              >
                开始学习
                <ArrowRight className="w-5 h-5" />
              </Link>
              <Link
                to="/learn"
                className="inline-flex items-center gap-2 px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-medium rounded-xl backdrop-blur-sm border border-white/20 transition-all"
              >
                互动练习
              </Link>
            </motion.div>
          </div>
        </div>

        {/* Bottom gradient fade */}
        <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-warm-50 to-transparent" />
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-warm-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {[
              { icon: Users, value: 52800, label: '活跃学习者', suffix: '+' },
              { icon: BookOpen, value: 120, label: '精品课程', suffix: '+' },
              { icon: Clock, value: 3600000, label: '累计学习时长', suffix: ' 分钟' },
            ].map((stat, i) => {
              const Icon = stat.icon;
              return (
                <motion.div
                  key={i}
                  className="bg-white rounded-2xl p-8 text-center shadow-sm border border-warm-200"
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.1 }}
                >
                  <div className="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mx-auto mb-4">
                    <Icon className="w-6 h-6 text-primary-600" />
                  </div>
                  <div className="font-mono font-bold text-4xl text-primary-950 mb-2">
                    <AnimatedCounter end={stat.value} suffix={stat.suffix} />
                  </div>
                  <div className="text-warm-500">{stat.label}</div>
                </motion.div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Featured Courses */}
      <section className="py-20 bg-warm-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            className="text-center mb-12"
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
          >
            <h2 className="font-heading font-bold text-3xl sm:text-4xl text-warm-900 mb-4">
              热门课程
            </h2>
            <p className="text-warm-500 max-w-2xl mx-auto">
              精心设计的分级课程体系，从入门到精通，满足不同阶段的学习需求
            </p>
          </motion.div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {featuredCourses.map((course, i) => (
              <CourseCard key={course.id} course={course} index={i} />
            ))}
          </div>

          <div className="text-center mt-10">
            <Link
              to="/courses"
              className="inline-flex items-center gap-2 px-6 py-3 bg-white border border-warm-200 text-warm-700 font-medium rounded-xl hover:bg-warm-100 transition-colors"
            >
              查看全部课程
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            className="text-center mb-16"
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
          >
            <h2 className="font-heading font-bold text-3xl sm:text-4xl text-warm-900 mb-4">
              沉浸式学习体验
            </h2>
            <p className="text-warm-500 max-w-2xl mx-auto">
              多种互动学习模块，让语言学习不再枯燥
            </p>
          </motion.div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {[
              {
                icon: BookOpen,
                title: '单词记忆',
                desc: '闪卡式学习，支持翻转、发音和例句展示',
                color: 'bg-blue-50 text-blue-600',
              },
              {
                icon: Target,
                title: '语法练习',
                desc: '选择题、填空题等多种互动题型',
                color: 'bg-purple-50 text-purple-600',
              },
              {
                icon: Globe,
                title: '口语跟读',
                desc: '录音对比、发音评分、波形可视化',
                color: 'bg-green-50 text-green-600',
              },
              {
                icon: Headphones,
                title: '听力训练',
                desc: '音频播放、变速调节、听写练习',
                color: 'bg-orange-50 text-orange-600',
              },
            ].map((feature, i) => {
              const Icon = feature.icon;
              return (
                <motion.div
                  key={i}
                  className="bg-warm-50 rounded-2xl p-6 hover:shadow-lg transition-all hover:-translate-y-1 border border-warm-200"
                  initial={{ opacity: 0, y: 30 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.1 }}
                >
                  <div className={`w-12 h-12 rounded-xl ${feature.color} flex items-center justify-center mb-4`}>
                    <Icon className="w-6 h-6" />
                  </div>
                  <h3 className="font-heading font-bold text-lg text-warm-900 mb-2">
                    {feature.title}
                  </h3>
                  <p className="text-sm text-warm-500 leading-relaxed">
                    {feature.desc}
                  </p>
                </motion.div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Achievements Wall */}
      <section className="py-20 bg-primary-950">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <motion.div
            className="text-center mb-12"
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
          >
            <h2 className="font-heading font-bold text-3xl sm:text-4xl text-white mb-4">
              成就激励
            </h2>
            <p className="text-warm-400 max-w-2xl mx-auto">
              完成学习目标，解锁成就徽章，与全球学习者一起成长
            </p>
          </motion.div>

          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            {achievements.slice(0, 6).map((ach, i) => (
              <motion.div
                key={ach.id}
                className="bg-primary-900/50 rounded-xl p-4 text-center border border-primary-800 hover:border-accent-500/50 transition-colors"
                initial={{ opacity: 0, scale: 0.8 }}
                whileInView={{ opacity: 1, scale: 1 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
              >
                <div className="w-10 h-10 rounded-lg bg-accent-500/20 text-accent-400 flex items-center justify-center mx-auto mb-3">
                  {achievementIcons[ach.icon]}
                </div>
                <div className="text-sm font-medium text-white mb-1">{ach.name}</div>
                <div className="text-xs text-warm-500">{ach.description}</div>
              </motion.div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
