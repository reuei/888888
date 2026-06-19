import { useState } from 'react';
import { motion } from 'framer-motion';
import {
  Heart,
  MessageCircle,
  Trophy,
  TrendingUp,
  TrendingDown,
  Minus,
  Flame,
} from 'lucide-react';
import { posts, leaderboard } from '@/data/community';

export default function Community() {
  const [activeTab, setActiveTab] = useState<'feed' | 'leaderboard'>('feed');
  const [likedPosts, setLikedPosts] = useState<Set<string>>(new Set());

  const toggleLike = (postId: string) => {
    setLikedPosts((prev) => {
      const next = new Set(prev);
      if (next.has(postId)) {
        next.delete(postId);
      } else {
        next.add(postId);
      }
      return next;
    });
  };

  const formatDate = (dateStr: string) => {
    const date = new Date(dateStr);
    return date.toLocaleDateString('zh-CN', { month: 'short', day: 'numeric' });
  };

  return (
    <div className="min-h-screen bg-warm-50">
      <div className="bg-white border-b border-warm-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }}>
            <h1 className="font-heading font-bold text-3xl text-warm-900 mb-2">
              学习社区
            </h1>
            <p className="text-warm-500">与全球学习者交流，分享你的学习心得</p>
          </motion.div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Tabs */}
        <div className="flex gap-2 mb-8">
          <button
            onClick={() => setActiveTab('feed')}
            className={`px-6 py-2.5 rounded-xl text-sm font-medium transition-all ${
              activeTab === 'feed'
                ? 'bg-primary-600 text-white'
                : 'bg-white text-warm-600 border border-warm-200 hover:bg-warm-50'
            }`}
          >
            学习动态
          </button>
          <button
            onClick={() => setActiveTab('leaderboard')}
            className={`px-6 py-2.5 rounded-xl text-sm font-medium transition-all ${
              activeTab === 'leaderboard'
                ? 'bg-primary-600 text-white'
                : 'bg-white text-warm-600 border border-warm-200 hover:bg-warm-50'
            }`}
          >
            排行榜
          </button>
        </div>

        {activeTab === 'feed' ? (
          <div className="max-w-2xl">
            <div className="space-y-4">
              {posts.map((post, i) => (
                <motion.div
                  key={post.id}
                  className="bg-white rounded-2xl p-5 shadow-sm border border-warm-200"
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: i * 0.05 }}
                >
                  <div className="flex items-center gap-3 mb-4">
                    <img
                      src={post.userAvatar}
                      alt={post.userName}
                      className="w-10 h-10 rounded-full object-cover"
                    />
                    <div>
                      <div className="font-medium text-warm-900">{post.userName}</div>
                      <div className="text-xs text-warm-400">{formatDate(post.createdAt)}</div>
                    </div>
                  </div>
                  <p className="text-warm-700 leading-relaxed mb-4">{post.content}</p>
                  <div className="flex items-center gap-6">
                    <button
                      onClick={() => toggleLike(post.id)}
                      className={`flex items-center gap-1.5 text-sm transition-colors ${
                        likedPosts.has(post.id)
                          ? 'text-red-500'
                          : 'text-warm-400 hover:text-red-500'
                      }`}
                    >
                      <Heart
                        className={`w-4 h-4 ${likedPosts.has(post.id) ? 'fill-red-500' : ''}`}
                      />
                      {post.likes + (likedPosts.has(post.id) ? 1 : 0)}
                    </button>
                    <button className="flex items-center gap-1.5 text-sm text-warm-400 hover:text-primary-600 transition-colors">
                      <MessageCircle className="w-4 h-4" />
                      {post.comments}
                    </button>
                  </div>
                </motion.div>
              ))}
            </div>
          </div>
        ) : (
          <div className="max-w-2xl">
            <div className="bg-white rounded-2xl shadow-sm border border-warm-200 overflow-hidden">
              {/* Top 3 */}
              <div className="p-6 bg-gradient-to-br from-primary-600 to-primary-800">
                <div className="flex items-center justify-center gap-4">
                  {leaderboard.slice(0, 3).map((entry, i) => (
                    <motion.div
                      key={entry.userId}
                      className={`text-center ${i === 0 ? 'order-2 scale-110' : i === 1 ? 'order-1' : 'order-3'}`}
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      transition={{ delay: i * 0.1 }}
                    >
                      <div className="relative inline-block">
                        <img
                          src={entry.userAvatar}
                          alt={entry.userName}
                          className={`rounded-full object-cover border-4 ${
                            i === 0 ? 'w-16 h-16 border-yellow-400' : i === 1 ? 'w-14 h-14 border-gray-300' : 'w-14 h-14 border-amber-600'
                          }`}
                        />
                        <div className={`absolute -bottom-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold ${
                          i === 0 ? 'bg-yellow-400 text-yellow-900' : i === 1 ? 'bg-gray-300 text-gray-700' : 'bg-amber-600 text-white'
                        }`}>
                          {i + 1}
                        </div>
                      </div>
                      <div className="mt-2 text-white font-medium text-sm">{entry.userName}</div>
                      <div className="text-primary-200 text-xs">{entry.score.toLocaleString()} 分</div>
                    </motion.div>
                  ))}
                </div>
              </div>

              {/* List */}
              <div className="divide-y divide-warm-100">
                {leaderboard.map((entry, i) => (
                  <motion.div
                    key={entry.userId}
                    className="flex items-center gap-4 p-4 hover:bg-warm-50 transition-colors"
                    initial={{ opacity: 0, x: -20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: i * 0.05 }}
                  >
                    <div className="w-8 text-center font-mono font-bold text-warm-400">
                      {entry.rank}
                    </div>
                    <img
                      src={entry.userAvatar}
                      alt={entry.userName}
                      className="w-10 h-10 rounded-full object-cover"
                    />
                    <div className="flex-1">
                      <div className="font-medium text-warm-900">{entry.userName}</div>
                      <div className="flex items-center gap-1 text-xs text-warm-500">
                        <Flame className="w-3 h-3 text-orange-500" />
                        连续 {entry.streakDays} 天
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="font-mono font-bold text-warm-900">
                        {entry.score.toLocaleString()}
                      </div>
                      <div className="flex items-center justify-end gap-1">
                        {entry.change > 0 ? (
                          <TrendingUp className="w-3 h-3 text-green-500" />
                        ) : entry.change < 0 ? (
                          <TrendingDown className="w-3 h-3 text-red-500" />
                        ) : (
                          <Minus className="w-3 h-3 text-warm-400" />
                        )}
                        <span className={`text-xs ${
                          entry.change > 0 ? 'text-green-500' : entry.change < 0 ? 'text-red-500' : 'text-warm-400'
                        }`}>
                          {entry.change !== 0 ? Math.abs(entry.change) : ''}
                        </span>
                      </div>
                    </div>
                  </motion.div>
                ))}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
