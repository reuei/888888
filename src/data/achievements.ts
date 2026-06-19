import type { Achievement } from '@/types';

export const achievements: Achievement[] = [
  { id: 'ach-1', name: '初出茅庐', description: '完成第一堂课程', icon: 'Sparkles', condition: 'complete_first_course' },
  { id: 'ach-2', name: '单词达人', description: '累计学习 100 个单词', icon: 'BookOpen', condition: 'learn_100_words' },
  { id: 'ach-3', name: '语法专家', description: '完成 50 道语法题', icon: 'PenTool', condition: 'complete_50_grammar' },
  { id: 'ach-4', name: '口语之星', description: '完成 20 次口语练习', icon: 'Mic', condition: 'speak_20_times' },
  { id: 'ach-5', name: '听力高手', description: '累计听力练习 2 小时', icon: 'Headphones', condition: 'listen_2_hours' },
  { id: 'ach-6', name: '坚持不懈', description: '连续学习 7 天', icon: 'Flame', condition: 'streak_7_days' },
  { id: 'ach-7', name: '月度冠军', description: '连续学习 30 天', icon: 'Trophy', condition: 'streak_30_days' },
  { id: 'ach-8', name: '多语言者', description: '学习 3 种语言', icon: 'Globe', condition: 'learn_3_languages' },
  { id: 'ach-9', name: '社区活跃', description: '发布 10 条动态', icon: 'MessageCircle', condition: 'post_10_times' },
  { id: 'ach-10', name: '完美答题', description: '连续答对 20 题', icon: 'Target', condition: 'correct_20_streak' },
  { id: 'ach-11', name: '早起鸟', description: '在早上 6 点前学习', icon: 'Sunrise', condition: 'study_before_6am' },
  { id: 'ach-12', name: '夜猫子', description: '在晚上 11 点后学习', icon: 'Moon', condition: 'study_after_11pm' },
];
