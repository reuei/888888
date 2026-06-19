import { motion } from 'framer-motion';
import {
  Crown,
  Mail,
  Calendar,
  Globe,
  Award,
  Settings,
} from 'lucide-react';
import { useAuthStore } from '@/stores/authStore';
import { useProgressStore } from '@/stores/progressStore';

export default function Profile() {
  const { user } = useAuthStore();
  const { progress } = useProgressStore();

  if (!user) {
    return (
      <div className="min-h-screen bg-warm-50 flex items-center justify-center">
        <div className="text-warm-500">请先登录</div>
      </div>
    );
  }

  const languageNames: Record<string, string> = {
    english: '英语',
    japanese: '日语',
    korean: '韩语',
  };

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              个人中心
            </h1>
            <p className="text-warm-500">管理你的学习资料和偏好设置</p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Profile Card */}
        <motion.div
          className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="flex flex-col sm:flex-row items-center gap-6">
            <img
              src={user.avatar}
              alt={user.nickname}
              className="w-24 h-24 rounded-full object-cover ring-4 ring-primary-100"
            />
            <div className="text-center sm:text-left flex-1">
              <div className="flex items-center justify-center sm:justify-start gap-2 mb-1">
                <h2 className="font-heading font-bold text-2xl text-warm-900">
                  {user.nickname}
                </h2>
                {user.memberType === 'premium' && (
                  <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700 text-xs font-medium">
                    <Crown className="w-3 h-3" />
                    会员
                  </span>
                )}
              </div>
              <div className="flex flex-wrap items-center justify-center sm:justify-start gap-4 text-sm text-warm-500 mt-2">
                <span className="flex items-center gap-1">
                  <Mail className="w-4 h-4" />
                  {user.email}
                </span>
                <span className="flex items-center gap-1">
                  <Calendar className="w-4 h-4" />
                  加入于 {new Date(user.createdAt).toLocaleDateString('zh-CN')}
                </span>
              </div>
            </div>
            <button className="p-2 rounded-lg bg-warm-100 text-warm-600 hover:bg-warm-200 transition-colors">
              <Settings className="w-5 h-5" />
            </button>
          </div>
        </motion.div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {/* Learning Languages */}
          <motion.div
            className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
          >
            <div className="flex items-center gap-2 mb-4">
              <Globe className="w-5 h-5 text-primary-600" />
              <h3 className="font-heading font-bold text-lg text-warm-900">
                学习语言
              </h3>
            </div>
            <div className="space-y-3">
              {user.learningLanguages.map((lang) => (
                <div
                  key={lang}
                  className="flex items-center justify-between p-3 rounded-xl bg-warm-50"
                >
                  <span className="font-medium text-warm-700">
                    {languageNames[lang] || lang}
                  </span>
                  <span className="text-xs text-warm-400">学习中</span>
                </div>
              ))}
            </div>
          </motion.div>

          {/* Quick Stats */}
          <motion.div
            className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.2 }}
          >
            <div className="flex items-center gap-2 mb-4">
              <Award className="w-5 h-5 text-accent-600" />
              <h3 className="font-heading font-bold text-lg text-warm-900">
                学习数据
              </h3>
            </div>
            <div className="grid grid-cols-2 gap-4">
              <div className="p-4 rounded-xl bg-warm-50 text-center">
                <div className="font-mono font-bold text-2xl text-warm-900">
                  {progress.streakDays}
                </div>
                <div className="text-xs text-warm-500 mt-1">连续学习天数</div>
              </div>
              <div className="p-4 rounded-xl bg-warm-50 text-center">
                <div className="font-mono font-bold text-2xl text-warm-900">
                  {Math.round(progress.totalStudyTime / 60)}
                </div>
                <div className="text-xs text-warm-500 mt-1">总学习小时</div>
              </div>
              <div className="p-4 rounded-xl bg-warm-50 text-center">
                <div className="font-mono font-bold text-2xl text-warm-900">
                  {progress.completedCourses.length}
                </div>
                <div className="text-xs text-warm-500 mt-1">完成课程</div>
              </div>
              <div className="p-4 rounded-xl bg-warm-50 text-center">
                <div className="font-mono font-bold text-2xl text-warm-900">
                  {Math.round(
                    (progress.skills.listening +
                      progress.skills.speaking +
                      progress.skills.reading +
                      progress.skills.writing +
                      progress.skills.vocabulary) /
                      5
                  )}
                </div>
                <div className="text-xs text-warm-500 mt-1">平均能力分</div>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </div>
  );
}
