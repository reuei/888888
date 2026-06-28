import { Component, type ReactNode } from 'react';
import { AlertTriangle, RefreshCcw } from 'lucide-react';

interface Props {
  children: ReactNode;
}

interface State {
  hasError: boolean;
  error?: Error;
}

export default class ErrorBoundary extends Component<Props, State> {
  state: State = { hasError: false };

  static getDerivedStateFromError(error: Error): State {
    return { hasError: true, error };
  }

  componentDidCatch(error: Error, errorInfo: React.ErrorInfo) {
    // eslint-disable-next-line no-console
    console.error('ErrorBoundary caught an error:', error, errorInfo);
  }

  handleReset = () => {
    this.setState({ hasError: false, error: undefined });
  };

  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-bg p-4">
          <div className="card p-8 w-full max-w-md text-center">
            <div className="w-14 h-14 bg-danger/10 text-danger rounded flex items-center justify-center mx-auto mb-4">
              <AlertTriangle size={28} />
            </div>
            <h1 className="text-xl font-bold mb-2">页面发生错误</h1>
            <p className="text-sm text-text-secondary mb-6">
              系统运行过程中出现异常，请尝试刷新页面或返回首页。
            </p>
            {this.state.error && (
              <div className="text-left text-xs bg-black/5 dark:bg-white/5 p-3 rounded mb-6 font-mono break-all text-text-secondary">
                {this.state.error.message}
              </div>
            )}
            <div className="flex gap-3 justify-center">
              <button
                onClick={() => window.location.reload()}
                className="btn btn-primary flex items-center gap-1"
              >
                <RefreshCcw size={16} /> 刷新页面
              </button>
              <button
                onClick={this.handleReset}
                className="btn btn-default"
              >
                重试
              </button>
            </div>
          </div>
        </div>
      );
    }

    return this.props.children;
  }
}
