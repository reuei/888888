import { motion } from 'framer-motion';
import {
  Flame,
  Clock,
  BookOpen,
  Trophy,
  TrendingUp,
  Calendar,
} from 'lucide-react';
import RadarChart from '@/components/RadarChart';
import HeatmapCalendar from '@/components/HeatmapCalendar';
import AnimatedCounter from '@/components/AnimatedCounter';
import { useProgressStore } from '@/stores/progressStore';
import { achievements } from '@/data/achievements';

const achievementIcons: Record<string, React.ReactNode> = {
  Sparkles: <Trophy className="w-5 h-5" />,
  BookOpen: <BookOpen className="w-5 h-5" />,
  PenTool: <TrendingUp className="w-5 h-5" />,
  Mic: <Flame className="w-5 h-5" />,
  Headphones: <Clock className="w-5 h-5" />,
  Flame: <Flame className="w-5 h-5" />,
  Trophy: <Trophy className="w-5 h-5" />,
  Globe: <TrendingUp className="w-5 h-5" />,
  MessageCircle: <Calendar className="w-5 h-5" />,
  Target: <TrendingUp className="w-5 h-5" />,
  Sunrise: <Flame className="w-5 h-5" />,
  Moon: <Clock className="w-5 h-5" />,
};

export default function Progress() {
  const { progress, achievements: userAchievements } = useProgressStore();

  const radarData = [
    { label: '听力', value: progress.skills.listening },
    { label: '口语', value: progress.skills.speaking },
    { label: '阅读', value: progress.skills.reading },
    { label: '写作', value: progress.skills.writing },
    { label: '词汇', value: progress.skills.vocabulary },
  ];

  const unlockedCount = userAchievements.filter((a) => a.unlockedAt).length;

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              学习进度
            </h1>
            <p className="text-warm-500">追踪你的学习成果，见证每一步成长</p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Stats Cards */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
          {[
            { icon: Flame, label: '连续学习', value: progress.streakDays, suffix: ' 天', color: 'text-orange-500 bg-orange-50' },
            { icon: Clock, label: '总学习时长', value: Math.round(progress.totalStudyTime / 60), suffix: ' 小时', color: 'text-blue-500 bg-blue-50' },
            { icon: BookOpen, label: '完成课程', value: progress.completedCourses.length, suffix: ' 门', color: 'text-green-500 bg-green-50' },
            { icon: Trophy, label: '成就解锁', value: unlockedCount, suffix: ` / ${achievements.length}`, color: 'text-purple-500 bg-purple-50' },
          ].map((stat, i) => {
            const Icon = stat.icon;
            return (
              <motion.div
                key={i}
                className="bg-white rounded-2xl p-5 shadow-sm border border-warm-200"
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: i * 0.1 }}
              >
                <div className={`w-10 h-10 rounded-lg ${stat.color} flex items-center justify-center mb-3`}>
                  <Icon className="w-5 h-5" />
                </div>
                <div className="font-mono font-bold text-2xl text-warm-900 mb-1">
                  <AnimatedCounter end={stat.value} suffix={stat.suffix} />
                </div>
                <div className="text-xs text-warm-500">{stat.label}</div>
              </motion.div>
            );
          })}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          {/* Radar Chart */}
          <motion.div
            className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
          >
            <h3 className="font-heading font-bold text-lg text-warm-900 mb-6">
              能力雷达图
            </h3>
            <div className="flex justify-center">
              <RadarChart data={radarData} size={320} />
            </div>
          </motion.div>

          {/* Heatmap */}
          <motion.div
            className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
          >
            <h3 className="font-heading font-bold text-lg text-warm-900 mb-6">
              学习日历
            </h3>
            <HeatmapCalendar data={progress.dailyLog} />
          </motion.div>
        </div>

        {/* Achievements */}
        <motion.div
          className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ delay: 0.4 }}
        >
          <h3 className="font-heading font-bold text-lg text-warm-900 mb-6">
            成就徽章
          </h3>
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            {userAchievements.map((ach, i) => {
              const isUnlocked = !!ach.unlockedAt;
              return (
                <motion.div
                  key={ach.id}
                  className={`relative rounded-xl p-4 text-center border transition-all ${
                    isUnlocked
                      ? 'bg-gradient-to-br from-primary-50 to-primary-100 border-primary-200'
                      : 'bg-warm-50 border-warm-200 opacity-60'
                  }`}
                  initial={{ opacity: 0, scale: 0.8 }}
                  animate={{ opacity: isUnlocked ? 1 : 0.6, scale: 1 }}
                  transition={{ delay: i * 0.05 }}
                >
                  {isUnlocked && (
                    <div className="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-accent-500 flex items-center justify-center">
                      <Trophy className="w-3 h-3 text-white" />
                    </div>
                  )}
                  <div className={`w-10 h-10 rounded-lg flex items-center justify-center mx-auto mb-2 ${
                    isUnlocked ? 'bg-primary-200 text-primary-700' : 'bg-warm-200 text-warm-400'
                  }`}>
                    {achievementIcons[ach.icon]}
                  </div>
                  <div className={`text-sm font-medium mb-1 ${isUnlocked ? 'text-warm-900' : 'text-warm-500'}`}>
                    {ach.name}
                  </div>
                  <div className="text-xs text-warm-400">{ach.description}</div>
                </motion.div>
              );
            })}
          </div>
        </motion.div>
      </div>
    </div>
  );
}
