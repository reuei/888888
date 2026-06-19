import { Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import {
  BookOpen,
  Target,
  Mic,
  Headphones,
  ArrowRight,
  Sparkles,
} from 'lucide-react';

const modules = [
  {
    id: 'vocabulary',
    title: '单词记忆',
    description: '闪卡式单词学习，支持翻转查看释义、发音和例句',
    icon: BookOpen,
    color: 'from-blue-500 to-blue-700',
    bgColor: 'bg-blue-50',
    iconColor: 'text-blue-600',
    count: '300+',
    label: '单词',
  },
  {
    id: 'grammar',
    title: '语法练习',
    description: '选择题、填空题等多种互动题型，即时反馈',
    icon: Target,
    color: 'from-purple-500 to-purple-700',
    bgColor: 'bg-purple-50',
    iconColor: 'text-purple-600',
    count: '150+',
    label: '题目',
  },
  {
    id: 'speaking',
    title: '口语跟读',
    description: '录音对比、发音评分、波形可视化反馈',
    icon: Mic,
    color: 'from-green-500 to-green-700',
    bgColor: 'bg-green-50',
    iconColor: 'text-green-600',
    count: '50+',
    label: '练习',
  },
  {
    id: 'listening',
    title: '听力训练',
    description: '音频播放、变速调节、听写练习',
    icon: Headphones,
    color: 'from-orange-500 to-orange-700',
    bgColor: 'bg-orange-50',
    iconColor: 'text-orange-600',
    count: '80+',
    label: '音频',
  },
];

export default function Learn() {
  return (
    <div className="min-h-screen bg-warm-50">
      {/* Header */}
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
          >
            <div className="flex items-center gap-2 mb-2">
              <Sparkles className="w-5 h-5 text-accent-500" />
              <span className="text-sm font-medium text-accent-600">互动学习</span>
            </div>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              选择学习模块
            </h1>
            <p className="text-warm-500">
              多种互动学习方式，全面提升听、说、读、写能力
            </p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {modules.map((module, i) => {
            const Icon = module.icon;
            return (
              <motion.div
                key={module.id}
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: i * 0.1 }}
              >
                <Link
                  to={`/learn/${module.id}`}
                  className="group block bg-white rounded-2xl p-6 shadow-sm border border-warm-200 hover:shadow-xl transition-all hover:-translate-y-1"
                >
                  <div className="flex items-start justify-between mb-4">
                    <div className={`w-14 h-14 rounded-xl ${module.bgColor} flex items-center justify-center`}>
                      <Icon className={`w-7 h-7 ${module.iconColor}`} />
                    </div>
                    <div className="text-right">
                      <div className="font-mono font-bold text-2xl text-warm-900">
                        {module.count}
                      </div>
                      <div className="text-xs text-warm-500">{module.label}</div>
                    </div>
                  </div>

                  <h3 className="font-heading font-bold text-xl text-warm-900 mb-2 group-hover:text-primary-700 transition-colors">
                    {module.title}
                  </h3>
                  <p className="text-sm text-warm-500 mb-4 leading-relaxed">
                    {module.description}
                  </p>

                  <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r ${module.color} text-white text-sm font-medium opacity-90 group-hover:opacity-100 transition-opacity`}>
                    开始学习
                    <ArrowRight className="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                  </div>
                </Link>
              </motion.div>
            );
          })}
        </div>
      </div>
    </div>
  );
}
