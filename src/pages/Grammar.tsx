import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Target, CheckCircle, XCircle, ArrowRight, Sparkles } from 'lucide-react';
import { useLearnStore } from '@/stores/learnStore';
import { useProgressStore } from '@/stores/progressStore';

const languages = [
  { value: 'english', label: '英语', flag: '🇬🇧' },
  { value: 'japanese', label: '日语', flag: '🇯🇵' },
  { value: 'korean', label: '韩语', flag: '🇰🇷' },
];

export default function Grammar() {
  const { grammarQuestions, currentQuestionIndex, selectedLanguage, setLanguage, answerQuestion, score, streak } = useLearnStore();
  const { updateSkill } = useProgressStore();
  const [selectedAnswer, setSelectedAnswer] = useState<string | null>(null);
  const [showResult, setShowResult] = useState(false);
  const [isCorrect, setIsCorrect] = useState(false);
  const [shake, setShake] = useState(false);

  const currentQuestion = grammarQuestions[currentQuestionIndex];

  const handleAnswer = (answer: string) => {
    if (showResult) return;
    setSelectedAnswer(answer);
    const correct = answerQuestion(answer);
    setIsCorrect(correct);
    setShowResult(true);

    if (correct) {
      updateSkill('reading', 2);
    } else {
      setShake(true);
      setTimeout(() => setShake(false), 500);
    }
  };

  const handleNext = () => {
    setSelectedAnswer(null);
    setShowResult(false);
    setIsCorrect(false);
  };

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <div className="flex items-center gap-2 mb-2">
              <Target className="w-5 h-5 text-purple-500" />
              <span className="text-sm font-medium text-purple-600">语法练习</span>
            </div>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              语法挑战
            </h1>
            <p className="text-warm-500">选择正确答案，提升语法能力</p>
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
                setSelectedAnswer(null);
                setShowResult(false);
              }}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                selectedLanguage === lang.value
                  ? 'bg-purple-600 text-white'
                  : 'bg-white text-warm-600 border border-warm-200 hover:bg-warm-50'
              }`}
            >
              {lang.flag} {lang.label}
            </button>
          ))}
        </div>

        {/* Score */}
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-4">
            <div className="text-sm text-warm-500">
              得分: <span className="font-mono font-bold text-purple-600">{score}</span>
            </div>
            {streak > 0 && (
              <div className="flex items-center gap-1 text-sm text-orange-500">
                <Sparkles className="w-4 h-4" />
                连对 {streak} 题
              </div>
            )}
          </div>
          <div className="text-sm text-warm-500">
            第 {currentQuestionIndex + 1} / {grammarQuestions.length} 题
          </div>
        </div>

        {/* Question */}
        <AnimatePresence mode="wait">
          <motion.div
            key={currentQuestionIndex}
            initial={{ opacity: 0, x: 30 }}
            animate={{ opacity: 1, x: 0 }}
            exit={{ opacity: 0, x: -30 }}
            transition={{ duration: 0.2 }}
          >
            <div className={`bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6 ${shake ? 'animate-shake' : ''}`}>
              <div className="text-sm text-warm-400 mb-4">
                {currentQuestion?.type === 'choice' ? '选择题' : '填空题'}
              </div>
              <h3 className="font-medium text-lg text-warm-900 mb-6 leading-relaxed">
                {currentQuestion?.question}
              </h3>

              <div className="space-y-3">
                {currentQuestion?.options.map((option) => {
                  let buttonClass = 'w-full text-left px-4 py-3 rounded-xl border-2 transition-all ';
                  if (showResult) {
                    if (option === currentQuestion.correctAnswer) {
                      buttonClass += 'border-green-500 bg-green-50 text-green-700';
                    } else if (option === selectedAnswer) {
                      buttonClass += 'border-red-500 bg-red-50 text-red-700';
                    } else {
                      buttonClass += 'border-warm-200 text-warm-500';
                    }
                  } else {
                    buttonClass += 'border-warm-200 hover:border-purple-300 hover:bg-purple-50 text-warm-700';
                  }

                  return (
                    <button
                      key={option}
                      onClick={() => handleAnswer(option)}
                      disabled={showResult}
                      className={buttonClass}
                    >
                      <div className="flex items-center justify-between">
                        <span>{option}</span>
                        {showResult && option === currentQuestion.correctAnswer && (
                          <CheckCircle className="w-5 h-5 text-green-500" />
                        )}
                        {showResult && option === selectedAnswer && option !== currentQuestion.correctAnswer && (
                          <XCircle className="w-5 h-5 text-red-500" />
                        )}
                      </div>
                    </button>
                  );
                })}
              </div>

              {/* Explanation */}
              <AnimatePresence>
                {showResult && (
                  <motion.div
                    initial={{ opacity: 0, height: 0 }}
                    animate={{ opacity: 1, height: 'auto' }}
                    exit={{ opacity: 0, height: 0 }}
                    className="mt-4 p-4 rounded-xl bg-warm-50 border border-warm-200"
                  >
                    <div className="flex items-center gap-2 mb-2">
                      {isCorrect ? (
                        <CheckCircle className="w-5 h-5 text-green-500" />
                      ) : (
                        <XCircle className="w-5 h-5 text-red-500" />
                      )}
                      <span className={`font-medium ${isCorrect ? 'text-green-700' : 'text-red-700'}`}>
                        {isCorrect ? '回答正确！' : '回答错误'}
                      </span>
                    </div>
                    <p className="text-sm text-warm-600">{currentQuestion?.explanation}</p>
                  </motion.div>
                )}
              </AnimatePresence>
            </div>
          </motion.div>
        </AnimatePresence>

        {/* Next button */}
        {showResult && (
          <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            className="flex justify-end"
          >
            <button
              onClick={handleNext}
              className="px-6 py-3 rounded-xl bg-purple-600 text-white font-medium hover:bg-purple-700 transition-colors flex items-center gap-2"
            >
              下一题
              <ArrowRight className="w-5 h-5" />
            </button>
          </motion.div>
        )}
      </div>
    </div>
  );
}
