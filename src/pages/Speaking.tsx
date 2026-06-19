import { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Mic, Play, Square, RotateCcw, Volume2, MicOff } from 'lucide-react';

const sampleTexts: Record<string, string[]> = {
  english: [
    'Hello, how are you doing today?',
    'The quick brown fox jumps over the lazy dog.',
    'Practice makes perfect.',
  ],
  japanese: [
    'こんにちは、お元気ですか。',
    '今日はいい天気ですね。',
    '頑張ってください。',
  ],
  korean: [
    '안녕하세요, 어떻게 지내세요?',
    '오늘 날씨가 좋네요.',
    '화이팅!',
  ],
};

const languages = [
  { value: 'english', label: '英语', flag: '🇬🇧' },
  { value: 'japanese', label: '日语', flag: '🇯🇵' },
  { value: 'korean', label: '韩语', flag: '🇰🇷' },
];

export default function Speaking() {
  const [selectedLanguage, setSelectedLanguage] = useState('english');
  const [currentIndex, setCurrentIndex] = useState(0);
  const [isRecording, setIsRecording] = useState(false);
  const [isPlaying, setIsPlaying] = useState(false);
  const [score, setScore] = useState<number | null>(null);
  const [waveform, setWaveform] = useState<number[]>(Array(20).fill(4));

  const texts = sampleTexts[selectedLanguage];
  const currentText = texts[currentIndex];

  // Simulate waveform animation when recording
  useEffect(() => {
    let interval: ReturnType<typeof setInterval>;
    if (isRecording) {
      interval = setInterval(() => {
        setWaveform(Array(20).fill(0).map(() => Math.random() * 32 + 4));
      }, 100);
    } else {
      setWaveform(Array(20).fill(4));
    }
    return () => clearInterval(interval);
  }, [isRecording]);

  const handleRecord = () => {
    if (isRecording) {
      setIsRecording(false);
      // Simulate scoring
      setScore(Math.floor(Math.random() * 30) + 70);
    } else {
      setIsRecording(true);
      setScore(null);
    }
  };

  const handlePlay = () => {
    setIsPlaying(true);
    setTimeout(() => setIsPlaying(false), 2000);
  };

  const handleNext = () => {
    setCurrentIndex((prev) => (prev + 1) % texts.length);
    setScore(null);
    setIsRecording(false);
  };

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <div className="flex items-center gap-2 mb-2">
              <Mic className="w-5 h-5 text-green-500" />
              <span className="text-sm font-medium text-green-600">口语跟读</span>
            </div>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              发音练习
            </h1>
            <p className="text-warm-500">跟随原音朗读，获得发音评分</p>
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
                setScore(null);
              }}
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                selectedLanguage === lang.value
                  ? 'bg-green-600 text-white'
                  : 'bg-white text-warm-600 border border-warm-200 hover:bg-warm-50'
              }`}
            >
              {lang.flag} {lang.label}
            </button>
          ))}
        </div>

        {/* Text display */}
        <motion.div
          className="bg-white rounded-2xl p-8 shadow-sm border border-warm-200 mb-6 text-center"
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
        >
          <div className="text-sm text-warm-400 mb-4">请朗读以下句子</div>
          <h2 className="font-heading font-bold text-2xl sm:text-3xl text-warm-900 mb-6 leading-relaxed">
            {currentText}
          </h2>
          <button
            onClick={handlePlay}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
          >
            {isPlaying ? <Volume2 className="w-5 h-5 animate-pulse" /> : <Play className="w-5 h-5" />}
            播放原音
          </button>
        </motion.div>

        {/* Waveform */}
        <div className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6">
          <div className="flex items-center justify-center gap-1 h-16">
            {waveform.map((height, i) => (
              <motion.div
                key={i}
                className="w-2 bg-green-500 rounded-full"
                animate={{ height }}
                transition={{ duration: 0.1 }}
              />
            ))}
          </div>
          <div className="text-center mt-2 text-sm text-warm-500">
            {isRecording ? '正在录音...' : '点击麦克风开始录音'}
          </div>
        </div>

        {/* Score */}
        {score !== null && (
          <motion.div
            className="bg-white rounded-2xl p-6 shadow-sm border border-warm-200 mb-6"
            initial={{ opacity: 0, scale: 0.9 }}
            animate={{ opacity: 1, scale: 1 }}
          >
            <div className="flex items-center justify-between">
              <div>
                <div className="text-sm text-warm-500 mb-1">发音评分</div>
                <div className="flex items-center gap-2">
                  <span className="font-mono font-bold text-4xl text-warm-900">{score}</span>
                  <span className="text-warm-400">/ 100</span>
                </div>
              </div>
              <div className="w-20 h-20 relative">
                <svg className="w-full h-full -rotate-90">
                  <circle
                    cx="40"
                    cy="40"
                    r="36"
                    fill="none"
                    stroke="#e7e5e4"
                    strokeWidth="6"
                  />
                  <circle
                    cx="40"
                    cy="40"
                    r="36"
                    fill="none"
                    stroke={score >= 80 ? '#22c55e' : score >= 60 ? '#f59e0b' : '#ef4444'}
                    strokeWidth="6"
                    strokeLinecap="round"
                    strokeDasharray={`${(score / 100) * 226} 226`}
                    className="transition-all duration-1000"
                  />
                </svg>
                <div className="absolute inset-0 flex items-center justify-center">
                  <span className="text-xs font-medium text-warm-600">
                    {score >= 80 ? '优秀' : score >= 60 ? '良好' : '需努力'}
                  </span>
                </div>
              </div>
            </div>
          </motion.div>
        )}

        {/* Controls */}
        <div className="flex items-center justify-center gap-4">
          <button
            onClick={() => {
              setCurrentIndex(0);
              setScore(null);
              setIsRecording(false);
            }}
            className="p-4 rounded-xl bg-white border border-warm-200 text-warm-600 hover:bg-warm-50 transition-colors"
          >
            <RotateCcw className="w-5 h-5" />
          </button>
          <button
            onClick={handleRecord}
            className={`p-5 rounded-full transition-all ${
              isRecording
                ? 'bg-red-500 text-white animate-pulse'
                : 'bg-green-600 text-white hover:bg-green-700'
            }`}
          >
            {isRecording ? <Square className="w-6 h-6" /> : <Mic className="w-6 h-6" />}
          </button>
          <button
            onClick={handleNext}
            className="p-4 rounded-xl bg-white border border-warm-200 text-warm-600 hover:bg-warm-50 transition-colors"
          >
            <MicOff className="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>
  );
}
