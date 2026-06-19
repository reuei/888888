import { create } from 'zustand';
import type { Word, GrammarQuestion } from '@/types';
import { vocabularyData } from '@/data/vocabulary';
import { grammarData } from '@/data/grammar';

interface LearnState {
  currentWordIndex: number;
  words: Word[];
  grammarQuestions: GrammarQuestion[];
  currentQuestionIndex: number;
  score: number;
  streak: number;
  selectedLanguage: string;
  setLanguage: (lang: string) => void;
  nextWord: () => void;
  answerQuestion: (answer: string) => boolean;
  reset: () => void;
}

export const useLearnStore = create<LearnState>((set, get) => ({
  currentWordIndex: 0,
  words: vocabularyData['english'],
  grammarQuestions: grammarData['english'],
  currentQuestionIndex: 0,
  score: 0,
  streak: 0,
  selectedLanguage: 'english',
  setLanguage: (lang: string) => {
    set({
      selectedLanguage: lang,
      words: vocabularyData[lang] || vocabularyData['english'],
      grammarQuestions: grammarData[lang] || grammarData['english'],
      currentWordIndex: 0,
      currentQuestionIndex: 0,
      score: 0,
      streak: 0,
    });
  },
  nextWord: () => {
    set((state) => ({
      currentWordIndex: (state.currentWordIndex + 1) % state.words.length,
    }));
  },
  answerQuestion: (answer: string) => {
    const { grammarQuestions, currentQuestionIndex, score, streak } = get();
    const question = grammarQuestions[currentQuestionIndex];
    const isCorrect = answer === question.correctAnswer;
    set({
      score: isCorrect ? score + 10 : score,
      streak: isCorrect ? streak + 1 : 0,
      currentQuestionIndex: (currentQuestionIndex + 1) % grammarQuestions.length,
    });
    return isCorrect;
  },
  reset: () => {
    set({
      currentWordIndex: 0,
      currentQuestionIndex: 0,
      score: 0,
      streak: 0,
    });
  },
}));
