import { Component, type ErrorInfo, type ReactNode } from 'react';

interface Props { children: ReactNode }
interface State { message: string | null }

export class ErrorBoundary extends Component<Props, State> {
  state: State = { message: null };

  static getDerivedStateFromError(error: unknown): State {
    return { message: error instanceof Error ? error.message : 'خطای غیرمنتظره' };
  }

  componentDidCatch(error: Error, info: ErrorInfo): void {
    console.error('[page-studio]', error, info.componentStack);
  }

  render(): ReactNode {
    if (this.state.message) {
      return (
        <div role="alert" className="rounded-xl border border-red-300 bg-red-50 p-6 text-red-900">
          <h2 className="text-lg font-semibold">ویرایشگر با خطای غیرمنتظره مواجه شد</h2>
          <p className="mt-2 text-sm">تغییرات ذخیره‌نشدهٔ شما ممکن است هنوز در این تب باشند. برای ادامه دوباره بارگذاری کنید؛ آخرین نسخهٔ ذخیره‌شده در سرور باقی می‌ماند.</p>
          <pre className="mt-3 overflow-auto rounded bg-white/70 p-3 text-xs">{this.state.message}</pre>
          <button
            type="button"
            onClick={() => window.location.reload()}
            className="mt-4 rounded-lg bg-red-700 px-4 py-2 text-sm font-semibold text-white"
          >بارگذاری مجدد Page Studio</button>
        </div>
      );
    }
    return this.props.children;
  }
}
