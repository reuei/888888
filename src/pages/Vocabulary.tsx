import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Volume2, RotateCcw, ChevronRight, ChevronLeft, BookOpen } from 'lucide-react';
import { useLearnStore } from '@/stores/learnStore';

const languages = [
  { value: 'english', label: '英语', flag: '🇬🇧' },
  { value: 'japanese', label: '日语', flag: '🇯🇵' },
  { value: 'korean', label: '韩语', flag: '🇰🇷' },
];

export default function Vocabulary() {
  const { words, currentWordIndex, selectedLanguage, setLanguage, nextWord } = useLearnStore();
  const [flipped, setFlipped] = useState(false);
  const [direction, setDirection] = useState(0);

  const currentWord = words[currentWordIndex];

  const handleNext = () => {
    setDirection(1);
    setFlipped(false);
    setTimeout(() => nextWord(), 150);
  };

  const handlePrev = () => {
    setDirection(-1);
    setFlipped(false);
    // Simple prev - in real app would track prev index
    setTimeout(() => nextWord(), 150);
  };

  const handleFlip = () => {
    setFlipped(!flipped);
  };

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <div className="flex items-center gap-2 mb-2">
              <BookOpen className="w-5 h-5 text-blue-500" />
              <span className="text-sm font-medium text-blue-600">单词记忆</span>
            </div>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              闪卡学习
            </h1>
            <p className="text-warm-500">点击卡片翻转查看释义和例句</p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Language selector */}
        <div className="flex justify-center gap-2 mb-8">
          {languages.map((lang) => (
            <button
              key={lang.value}
              onClick={() => {
                setLanguage(lang.value);
                setFlipped(false);
              }}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                selectedLanguage === lang.value
                  ? 'bg-blue-600 text-white'
                  : 'bg-white text-warm-600 border border-warm-200 hover:bg-warm-50'
              }`}
            >
              {lang.flag} {lang.label}
            </button>
          ))}
        </div>

        {/* Progress */}
        <div className="flex items-center justify-between mb-6">
          <span className="text-sm text-warm-500">
            第 {currentWordIndex + 1} / {words.length} 张
          </span>
          <div className="flex-1 mx-4 h-2 bg-warm-200 rounded-full overflow-hidden">
            <div
              className="h-full bg-blue-500 rounded-full transition-all duration-300"
              style={{ width: `${((currentWordIndex + 1) / words.length) * 100}%` }}
            />
          </div>
        </div>

        {/* Flashcard */}
        <AnimatePresence mode="wait">
          <motion.div
            key={currentWordIndex}
            initial={{ opacity: 0, x: direction * 50 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: direction * -50 }}
            transition={{ duration: 0.2 }}
          >
            <div
              className={`flip-card cursor-pointer ${flipped ? 'flipped' : ''}`}
              onClick={handleFlip}
            >
              <div className="flip-card-inner relative w-full aspect-[4/3]">
                {/* Front */}
                <div className="flip-card-front absolute inset-0 bg-white rounded-2xl shadow-lg border border-warm-200 flex flex-col items-center justify-center p-8">
                  <div className="text-sm text-warm-400 mb-4">
                    {languages.find((l) => l.value === selectedLanguage)?.flag} 点击翻转
                  </div>
                  <h2 className="font-heading font-bold text-4xl text-warm-900 mb-3">
                    {currentWord?.word}
                  </h2>
                  <p className="text-lg text-warm-500 font-mono">{currentWord?.phonetic}</p>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      // Mock pronunciation
                    }}
                    className="mt-6 p-3 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                  >
                    <Volume2 className="w-5 h-5" />
                  </button>
                </div>

                {/* Back */}
                <div className="flip-card-back absolute inset-0 bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl shadow-lg flex flex-col items-center justify-center p-8 text-white">
                  <div className="text-sm text-primary-200 mb-4">释义</div>
                  <h3 className="font-bold text-2xl mb-4 text-center">
                    {currentWord?.translation}
                  </h3>
                  <div className="w-full max-w-md">
                    <p className="text-primary-100 text-sm mb-2">例句：</p>
                    <p className="text-white mb-2">{currentWord?.example}</p>
                    <p className="text-primary-200 text-sm">{currentWord?.exampleTranslation}</p>
                  </div>
                </div>
              </div>
            </div>
          </motion.div>
        </AnimatePresence>

        {/* Controls */}
        <div className="flex items-center justify-center gap-4 mt-8">
          <button
            onClick={handlePrev}
            className="p-3 rounded-xl bg-white border border-warm-200 text-warm-600 hover:bg-warm-50 transition-colors"
          >
            <ChevronLeft className="w-5 h-5" />
          </button>
          <button
            onClick={() => {
              setFlipped(false);
              setLanguage(selectedLanguage);
            }}
            className="p-3 rounded-xl bg-white border border-warm-200 text-warm-600 hover:bg-warm-50 transition-colors"
          >
            <RotateCcw className="w-5 h-5" />
          </button>
          <button
            onClick={handleNext}
            className="px-6 py-3 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700 transition-colors flex items-center gap-2"
          >
            下一张
            <ChevronRight className="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>
  );
}
