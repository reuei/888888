import { create } from 'zustand';
import type { User } from '@/types';

interface AuthState {
  user: User | null;
  isLoggedIn: boolean;
  login: (email: string, password: string) => Promise<boolean>;
  register: (email: string, password: string, nickname: string) => Promise<boolean>;
  logout: () => void;
}

const mockUser: User = {
  id: 'u-demo',
  email: 'demo@linguaflow.com',
  nickname: '学习达人',
  avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&auto=format&fit=crop',
  memberType: 'premium',
  nativeLanguage: '中文',
  learningLanguages: ['english', 'japanese', 'korean'],
  createdAt: '2024-01-01T00:00:00Z',
};

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  isLoggedIn: false,
  login: async (email: string, password: string) => {
    await new Promise((resolve) => setTimeout(resolve, 500));
    if (email && password.length >= 6) {
      set({ user: mockUser, isLoggedIn: true });
      localStorage.setItem('linguaflow_user', JSON.stringify(mockUser));
      return true;
    }
    return false;
  },
  register: async (email: string, password: string, nickname: string) => {
    await new Promise((resolve) => setTimeout(resolve, 500));
    if (email && password.length >= 6 && nickname) {
      const newUser: User = {
        ...mockUser,
        id: `u-${Date.now()}`,
        email,
        nickname,
        memberType: 'free',
      };
      set({ user: newUser, isLoggedIn: true });
      localStorage.setItem('linguaflow_user', JSON.stringify(newUser));
      return true;
    }
    return false;
  },
  logout: () => {
    set({ user: null, isLoggedIn: false });
    localStorage.removeItem('linguaflow_user');
  },
}));

// Auto-login from localStorage
const saved = localStorage.getItem('linguaflow_user');
if (saved) {
  try {
    const user = JSON.parse(saved) as User;
    useAuthStore.setState({ user, isLoggedIn: true });
  } catch {
    localStorage.removeItem('linguaflow_user');
  }
}
