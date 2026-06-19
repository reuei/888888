export interface User {
  id: string;
  email: string;
  nickname: string;
  avatar: string;
  memberType: 'free' | 'premium';
  nativeLanguage: string;
  learningLanguages: string[];
  createdAt: string;
}

export interface Course {
  id: string;
  title: string;
  description: string;
  language: 'english' | 'japanese' | 'korean';
  level: 'beginner' | 'intermediate' | 'advanced';
  coverImage: string;
  totalLessons: number;
  completedLessons: number;
  duration: number;
  rating: number;
  studentsCount: number;
  tags: string[];
}

export interface Word {
  id: string;
  word: string;
  phonetic: string;
  translation: string;
  example: string;
  exampleTranslation: string;
  language: string;
  difficulty: number;
}

export interface GrammarQuestion {
  id: string;
  type: 'choice' | 'fill';
  question: string;
  options: string[];
  correctAnswer: string;
  explanation: string;
  difficulty: number;
}

export interface LearningProgress {
  userId: string;
  language: string;
  skills: {
    listening: number;
    speaking: number;
    reading: number;
    writing: number;
    vocabulary: number;
  };
  streakDays: number;
  totalStudyTime: number;
  completedCourses: string[];
  dailyLog: {
    date: string;
    minutes: number;
  }[];
}

export interface Achievement {
  id: string;
  name: string;
  description: string;
  icon: string;
  condition: string;
  unlockedAt?: string;
}

export interface Post {
  id: string;
  userId: string;
  userName: string;
  userAvatar: string;
  content: string;
  likes: number;
  comments: number;
  createdAt: string;
}

export interface LeaderboardEntry {
  rank: number;
  userId: string;
  userName: string;
  userAvatar: string;
  score: number;
  streakDays: number;
  change: number;
}
