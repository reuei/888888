import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Headphones, Play, Pause, RotateCcw, SkipForward, Volume2 } from 'lucide-react';

const sampleTexts: Record<string, { text: string; hint: string }[]> = {
  english: [
    { text: 'The weather is beautiful today', hint: '天气今天很...' },
    { text: 'I would like a cup of coffee', hint: '我想要一杯...' },
    { text: 'Where is the train station', hint: '火车站在...' },
  ],
  japanese: [
    { text: '今日はいい天気ですね', hint: '今天天气很...' },
    { text: 'コーヒーを一杯ください', hint: '请给我一杯...' },
    { text: '駅はどこですか', hint: '车站在...' },
  ],
  korean: [
    { text: '오늘 날씨가 좋네요', hint: '今天天气...' },
    { text: '커피 한 잔 주세요', hint: '请给我一杯...' },
    { text: '역이 어디에 있어요', hint: '车站在...' },
  ],
};

const languages = [
  { value: 'english', label: '英语', flag: '🇬🇧' },
  { value: 'japanese', label: '日语', flag: '🇯🇵' },
  { value: 'korean', label: '韩语', flag: '🇰🇷' },
];

export default function Listening() {
  const [selectedLanguage, setSelectedLanguage] = useState('english');
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);
  const [userInput, setUserInput] = useState('');
  const [showResult, setShowResult] = useState(false);
  const [playbackSpeed, setPlaybackSpeed] = useState(1);
  const [waveform, setWaveform] = useState<number[]>(Array(30).fill(4));

  const texts = sampleTexts[selectedLanguage];
  const currentItem = texts[currentIndex];

  useEffect(() => {
    let interval: ReturnType<typeof setInterval>;
    if (isPlaying) {
      interval = setInterval(() => {
        setWaveform(Array(30).fill(0).map(() => Math.random() * 24 + 4));
      }, 80);
    } else {
      setWaveform(Array(30).fill(4));
    }
    return () => clearInterval(interval);
  }, [isPlaying]);

  const handlePlay = () => {
    if (isPlaying) {
      setIsPlaying(false);
    } else {
      setIsPlaying(true);
      setTimeout(() => setIsPlaying(false), 3000);
    }
  };

  const handleCheck = () => {
    setShowResult(true);
  };

  const handleNext = () => {
    setCurrentIndex((prev) => (prev + 1) % texts.length);
    setUserInput('');
    setShowResult(false);
  };

  const isCorrect = userInput.trim().toLowerCase() === currentItem.text.toLowerCase();

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <div className="flex items-center gap-2 mb-2">
              <Headphones className="w-5 h-5 text-orange-500" />
              <span className="text-sm font-medium text-orange-600">听力训练</span>
            </div>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              听写练习
            </h1>
            <p className="text-warm-500">听音频并输入你听到的内容</p>
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
                setSelectedLanguage(lang.value);
                setCurrentIndex(0);
                setUserInput('');
                setShowResult(false);
              }}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                selectedLanguage === lang.value
                  ? 'bg-orange-600 text-white'
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
            第 {currentIndex + 1} / {texts.length} 题
          </span>
          <div className="flex-1 mx-4 h-2 bg-warm-200 rounded-full overflow-hidden">
            <div
              className="h-full bg-orange-500 rounded-full transition-all duration-300"
              style={{ width: `${((currentIndex + 1) / texts.length) * 100}%` }}
            />
          </div>
        </div>

        {/* Player */}
        <div className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6">
          {/* Waveform */}
          <div className="flex items-center justify-center gap-0.5 h-20 mb-4">
            {waveform.map((height, i) => (
              <motion.div
                key={i}
                className="w-1.5 bg-orange-400 rounded-full"
                animate={{ height }}
                transition={{ duration: 0.08 }}
              />
            ))}
          </div>

          {/* Controls */}
          <div className="flex items-center justify-center gap-4">
            <button
              onClick={handlePlay}
              className={`p-4 rounded-full transition-all ${
                isPlaying
                  ? 'bg-orange-100 text-orange-600'
                  : 'bg-orange-600 text-white hover:bg-orange-700'
              }`}
            >
              {isPlaying ? <Pause className="w-6 h-6" /> : <Play className="w-6 h-6" />}
            </button>

            {/* Speed controls */}
            <div className="flex items-center gap-1 bg-warm-100 rounded-lg p-1">
              {[0.5, 0.75, 1, 1.25, 1.5].map((speed) => (
                <button
                  key={speed}
                  onClick={() => setPlaybackSpeed(speed)}
                  className={`px-2 py-1 rounded-md text-xs font-medium transition-all ${
                    playbackSpeed === speed
                      ? 'bg-white text-orange-600 shadow-sm'
                      : 'text-warm-500 hover:text-warm-700'
                  }`}
                >
                  {speed}x
                </button>
              ))}
            </div>

            <button
              onClick={() => {
                setUserInput('');
                setShowResult(false);
              }}
              className="p-3 rounded-xl bg-warm-100 text-warm-600 hover:bg-warm-200 transition-colors"
            >
              <RotateCcw className="w-5 h-5" />
            </button>
          </div>
        </div>

        {/* Input */}
        <div className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6">
          <div className="text-sm text-warm-400 mb-3">
            提示: {currentItem.hint}
          </div>
          <textarea
            value={userInput}
            onChange={(e) => {
              setUserInput(e.target.value);
              setShowResult(false);
            }}
            placeholder="输入你听到的内容..."
            className="w-full p-4 rounded-xl border border-warm-200 text-warm-900 placeholder-warm-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
            rows={3}
          />

          {showResult && (
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              className={`mt-4 p-4 rounded-xl ${
                isCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'
              }`}
            >
              <div className="flex items-center gap-2 mb-2">
                {isCorrect ? (
                  <Volume2 className="w-5 h-5 text-green-500" />
                ) : (
                  <Volume2 className="w-5 h-5 text-red-500" />
                )}
                <span className={`font-medium ${isCorrect ? 'text-green-700' : 'text-red-700'}`}>
                  {isCorrect ? '回答正确！' : '回答错误'}
                </span>
              </div>
              {!isCorrect && (
                <p className="text-sm text-warm-600">
                  正确答案: <span className="font-medium text-warm-900">{currentItem.text}</span>
                </p>
              )}
            </motion.div>
          )}
        </div>

        {/* Actions */}
        <div className="flex items-center justify-end gap-4">
          {!showResult ? (
            <button
              onClick={handleCheck}
              disabled={!userInput.trim()}
              className="px-6 py-3 rounded-xl bg-orange-600 text-white font-medium hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              检查答案
            </button>
          ) : (
            <button
              onClick={handleNext}
              className="px-6 py-3 rounded-xl bg-orange-600 text-white font-medium hover:bg-orange-700 transition-colors flex items-center gap-2"
            >
              下一题
              <SkipForward className="w-5 h-5" />
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
