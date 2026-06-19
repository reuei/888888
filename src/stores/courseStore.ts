import { create } from 'zustand';
import type { Course } from '@/types';
import { courses as allCourses } from '@/data/courses';

interface CourseState {
  courses: Course[];
  filteredCourses: Course[];
  selectedLanguage: string | null;
  selectedLevel: string | null;
  setFilter: (language?: string, level?: string) => void;
  enrollCourse: (courseId: string) => void;
}

export const useCourseStore = create<CourseState>((set, get) => ({
  courses: allCourses,
  filteredCourses: allCourses,
  selectedLanguage: null,
  selectedLevel: null,
  setFilter: (language?: string, level?: string) => {
    const { courses } = get();
    let filtered = courses;
    if (language && language !== 'all') {
      filtered = filtered.filter((c) => c.language === language);
    }
    if (level && level !== 'all') {
      filtered = filtered.filter((c) => c.level === level);
    }
    set({ filteredCourses: filtered, selectedLanguage: language || null, selectedLevel: level || null });
  },
  enrollCourse: (courseId: string) => {
    set((state) => ({
      courses: state.courses.map((c) =>
        c.id === courseId ? { ...c, completedLessons: Math.min(c.completedLessons + 1, c.totalLessons) } : c
      ),
      filteredCourses: state.filteredCourses.map((c) =>
        c.id === courseId ? { ...c, completedLessons: Math.min(c.completedLessons + 1, c.totalLessons) } : c
      ),
    }));
  },
}));
