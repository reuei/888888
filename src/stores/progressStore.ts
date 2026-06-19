import { create } from 'zustand';
import type { LearningProgress, Achievement } from '@/types';
import { achievements as allAchievements } from '@/data/achievements';

interface ProgressState {
  progress: LearningProgress;
  achievements: Achievement[];
  updateSkill: (skill: string, value: number) => void;
  addStudyTime: (minutes: number) => void;
  unlockAchievement: (achievementId: string) => void;
}

const defaultProgress: LearningProgress = {
  userId: 'u-demo',
  language: 'english',
  skills: {
    listening: 65,
    speaking: 45,
    reading: 78,
    writing: 52,
    vocabulary: 70,
  },
  streakDays: 12,
  totalStudyTime: 1860,
  completedCourses: ['en-1', 'jp-1'],
  dailyLog: [
    { date: '2024-01-15', minutes: 45 },
    { date: '2024-01-14', minutes: 60 },
    { date: '2024-01-13', minutes: 30 },
    { date: '2024-01-12', minutes: 90 },
    { date: '2024-01-11', minutes: 45 },
    { date: '2024-01-10', minutes: 30 },
    { date: '2024-01-09', minutes: 60 },
    { date: '2024-01-08', minutes: 45 },
    { date: '2024-01-07', minutes: 30 },
    { date: '2024-01-06', minutes: 75 },
    { date: '2024-01-05', minutes: 60 },
    { date: '2024-01-04', minutes: 45 },
    { date: '2024-01-03', minutes: 30 },
    { date: '2024-01-02', minutes: 90 },
    { date: '2024-01-01', minutes: 60 },
  ],
};

const savedProgress = localStorage.getItem('linguaflow_progress');
const savedAchievements = localStorage.getItem('linguaflow_achievements');

export const useProgressStore = create<ProgressState>((set, get) => ({
  progress: savedProgress ? JSON.parse(savedProgress) : defaultProgress,
  achievements: savedAchievements ? JSON.parse(savedAchievements) : allAchievements.map((a) => ({ ...a, unlockedAt: undefined })),
  updateSkill: (skill: string, value: number) => {
    set((state) => {
      const newProgress = {
        ...state.progress,
        skills: {
          ...state.progress.skills,
          [skill]: Math.min(100, state.progress.skills[skill as keyof typeof state.progress.skills] + value),
        },
      };
      localStorage.setItem('linguaflow_progress', JSON.stringify(newProgress));
      return { progress: newProgress };
    });
  },
  addStudyTime: (minutes: number) => {
    set((state) => {
      const today = new Date().toISOString().split('T')[0];
      const existingLog = state.progress.dailyLog.find((d) => d.date === today);
      let newDailyLog;
      if (existingLog) {
        newDailyLog = state.progress.dailyLog.map((d) =>
          d.date === today ? { ...d, minutes: d.minutes + minutes } : d
        );
      } else {
        newDailyLog = [{ date: today, minutes }, ...state.progress.dailyLog];
      }
      const newProgress = {
        ...state.progress,
        totalStudyTime: state.progress.totalStudyTime + minutes,
        dailyLog: newDailyLog,
      };
      localStorage.setItem('linguaflow_progress', JSON.stringify(newProgress));
      return { progress: newProgress };
    });
  },
  unlockAchievement: (achievementId: string) => {
    set((state) => {
      const newAchievements = state.achievements.map((a) =>
        a.id === achievementId && !a.unlockedAt
          ? { ...a, unlockedAt: new Date().toISOString() }
          : a
      );
      localStorage.setItem('linguaflow_achievements', JSON.stringify(newAchievements));
      return { achievements: newAchievements };
    });
  },
}));
