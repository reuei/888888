import { Languages, Github, Twitter, Mail } from 'lucide-react';

export default function Footer() {
  return (
    <footer className="bg-primary-950 text-warm-300">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div className="md:col-span-1">
            <div className="flex items-center gap-2 mb-4">
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                <Languages className="w-4 h-4 text-white" />
              </div>
              <span className="font-heading font-bold text-lg text-white">
                LinguaFlow
              </span>
            </div>
            <p className="text-sm text-warm-400 leading-relaxed">
              沉浸式多语种学习平台，让语言学习变得高效而有趣。
            </p>
          </div>

          <div>
            <h3 className="font-medium text-white mb-4">课程</h3>
            <ul className="space-y-2 text-sm">
              <li><a href="/courses" className="hover:text-accent-400 transition-colors">英语课程</a></li>
              <li><a href="/courses" className="hover:text-accent-400 transition-colors">日语课程</a></li>
              <li><a href="/courses" className="hover:text-accent-400 transition-colors">韩语课程</a></li>
              <li><a href="/learn" className="hover:text-accent-400 transition-colors">互动练习</a></li>
            </ul>
          </div>

          <div>
            <h3 className="font-medium text-white mb-4">社区</h3>
            <ul className="space-y-2 text-sm">
              <li><a href="/community" className="hover:text-accent-400 transition-colors">学习动态</a></li>
              <li><a href="/community" className="hover:text-accent-400 transition-colors">排行榜</a></li>
              <li><a href="/progress" className="hover:text-accent-400 transition-colors">成就系统</a></li>
              <li><a href="/profile" className="hover:text-accent-400 transition-colors">个人中心</a></li>
            </ul>
          </div>

          <div>
            <h3 className="font-medium text-white mb-4">联系我们</h3>
            <div className="flex items-center gap-3">
              <a href="#" className="p-2 rounded-lg bg-primary-900 hover:bg-primary-800 transition-colors">
                <Github className="w-4 h-4" />
              </a>
              <a href="#" className="p-2 rounded-lg bg-primary-900 hover:bg-primary-800 transition-colors">
                <Twitter className="w-4 h-4" />
              </a>
              <a href="#" className="p-2 rounded-lg bg-primary-900 hover:bg-primary-800 transition-colors">
                <Mail className="w-4 h-4" />
              </a>
            </div>
          </div>
        </div>

        <div className="mt-12 pt-8 border-t border-primary-900 text-center text-sm text-warm-500">
          <p> 2024 LinguaFlow. All rights reserved.</p>
        </div>
      </div>
    </footer>
  );
}
