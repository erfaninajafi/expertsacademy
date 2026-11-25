import { useCallback, useEffect, useState } from 'react';

// EA_UIUX_STAGE20_GUIDED_ONBOARDING_V1
// Pure client-side help: it never submits a form, saves a page, or publishes content.
type StudioView = 'pages' | 'design' | 'components';

type TourTarget =
  | { kind: 'selector'; selector: string }
  | { kind: 'toolbar'; label: string }
  | { kind: 'tab'; position: number };

type TourStep = {
  title: string;
  description: string;
  hint: string;
  view: StudioView;
  target: TourTarget;
  needsDraft?: boolean;
};

const TOUR_KEY = 'ea-studio:guided-tour:v1:finished';

const steps: TourStep[] = [
  {
    title: 'انتخاب صفحه',
    description: 'از این قسمت، صفحهٔ در حال ویرایش را انتخاب کن. می‌توانی نام صفحه را جست‌وجو کنی و وضعیت صفحات فعال یا بایگانی‌شده را ببینی.',
    hint: 'برای آزمایش راهنما لازم نیست صفحه‌ای را تغییر بدهی.',
    view: 'pages',
    target: { kind: 'selector', selector: '#page-select' },
  },
  {
    title: 'شروع با یک قالب آماده',
    description: 'در کتابخانهٔ قالب‌ها می‌توانی ساختار و پیش‌نمایش قالب را ببینی و سپس از آن یک پیش‌نویس مستقل بسازی.',
    hint: 'قالب‌های آماده روی صفحات منتشرشدهٔ قبلی اثری نمی‌گذارند.',
    view: 'pages',
    target: { kind: 'toolbar', label: 'قالب‌ها' },
  },
  {
    title: 'افزودن بلوک',
    description: 'کتابخانهٔ بلوک‌ها امکان جست‌وجو بر اساس نام و کاربرد کامپوننت را دارد. با انتخاب بلوک، آن را به سکشن موردنظر اضافه می‌کنی.',
    hint: 'این بخش فقط پس از بازکردن یک صفحه نمایش داده می‌شود.',
    view: 'pages',
    target: { kind: 'selector', selector: '[aria-label="کتابخانهٔ کامپوننت"]' },
    needsDraft: true,
  },
  {
    title: 'چیدمان و محتوای صفحه',
    description: 'در بوم اصلی می‌توانی سکشن‌ها و بلوک‌ها را انتخاب کنی، ترتیب بخش‌ها را تغییر بدهی و پیش‌نمایش واکنش‌گرا را بررسی کنی.',
    hint: 'هنگام اجرای راهنما هیچ بخشی جابه‌جا یا حذف نمی‌شود.',
    view: 'pages',
    target: { kind: 'selector', selector: 'article[aria-label^="بخش "]' },
    needsDraft: true,
  },
  {
    title: 'تنظیمات و سئوی صفحه',
    description: 'در پنل تنظیمات می‌توانی محتوای بلوک یا سکشن انتخاب‌شده را ویرایش کنی و از زبانهٔ صفحه به تنظیمات عنوان و سئو برسی.',
    hint: 'اگر پنل تنظیماتِ بلوک باز است، برای دیدن فیلدهای سئو زبانهٔ «صفحه» را انتخاب کن.',
    view: 'pages',
    target: { kind: 'selector', selector: '#page-title' },
    needsDraft: true,
  },
  {
    title: 'بررسی پیش‌نمایش',
    description: 'پیش از انتشار، صفحه را روی نمایشگر رومیزی، تبلت و موبایل بررسی کن. پیش‌نمایش به‌تنهایی صفحه را عمومی نمی‌کند.',
    hint: 'توصیه می‌شود ظاهر موبایل و جهت نوشتار فارسی را هم بررسی کنی.',
    view: 'pages',
    target: { kind: 'toolbar', label: 'پیش‌نمایش' },
  },
  {
    title: 'بررسی و انتشار ایمن',
    description: 'در پنجرهٔ «بررسی و انتشار»، خطاها و هشدارهای کیفیت را ببین. بایگانی صفحه نیز از همین قسمت انجام می‌شود.',
    hint: 'این راهنما هیچ عملیات انتشار، لغو انتشار یا بایگانی را اجرا نمی‌کند.',
    view: 'pages',
    target: { kind: 'toolbar', label: 'انتشار' },
  },
  {
    title: 'سیستم طراحی',
    description: 'در سیستم طراحی، رنگ‌ها، تایپوگرافی، فاصله‌ها و تنظیمات هویت بصری را به‌صورت متمرکز مدیریت می‌کنی.',
    hint: 'جابه‌جایی به این زبانه، تغییرات ویرایشگر را ذخیره یا منتشر نمی‌کند.',
    view: 'design',
    target: { kind: 'tab', position: 1 },
  },
  {
    title: 'استودیوی کامپوننت',
    description: 'در این محیط، توسعه‌دهنده کامپوننت‌ها، گونه‌ها و تنظیمات اختصاصی آن‌ها را مدیریت می‌کند. کاربران صفحه‌ساز از نسخه‌های موجود استفاده می‌کنند.',
    hint: 'در پایان می‌توانی راهنما را ببندی یا هر زمان دوباره اجرا کنی.',
    view: 'components',
    target: { kind: 'tab', position: 2 },
  },
];

function findTarget(target: TourTarget): HTMLElement | null {
  if (target.kind === 'selector') {
    return document.querySelector<HTMLElement>(target.selector);
  }
  if (target.kind === 'toolbar') {
    const buttons = document.querySelectorAll<HTMLButtonElement>('[data-toolbar-item] button');
    return Array.from(buttons).find((button) =>
      (button.textContent ?? '').replace(/\s+/g, ' ').trim().includes(target.label)
    ) ?? null;
  }
  const navigation = document.querySelector<HTMLElement>('nav[aria-label="بخش‌های Page Studio"]');
  return navigation?.querySelectorAll<HTMLButtonElement>('button')[target.position] ?? null;
}

function shouldOpenAutomatically(): boolean {
  if (typeof window === 'undefined') return false;
  try {
    return window.localStorage.getItem(TOUR_KEY) !== 'yes';
  } catch {
    return false;
  }
}

function markAsSeen(): void {
  try {
    window.localStorage.setItem(TOUR_KEY, 'yes');
  } catch {
    // In restricted browsers the guide remains usable without persistence.
  }
}

interface Props {
  currentView: StudioView;
  onNavigate: (view: StudioView) => void;
  hasDraft: boolean;
}

export function StudioTour({ currentView, onNavigate, hasDraft }: Props) {
  const [open, setOpen] = useState(shouldOpenAutomatically);
  const [index, setIndex] = useState(0);
  const [targetVisible, setTargetVisible] = useState(false);
  const active = steps[index];

  const close = useCallback((): void => {
    setOpen(false);
    markAsSeen();
  }, []);

  const restart = (): void => {
    setIndex(0);
    setOpen(true);
  };

  // Navigation is a view-only change. It never touches page content or invokes APIs.
  useEffect(() => {
    if (!open) return;
    onNavigate(steps[index].view);
  }, [index, onNavigate, open]);

  useEffect(() => {
    if (!open) {
      setTargetVisible(false);
      return;
    }
    if (currentView !== active.view) {
      setTargetVisible(false);
      return;
    }
    const element = findTarget(active.target);
    if (!element || element.getClientRects().length === 0) {
      setTargetVisible(false);
      return;
    }
    setTargetVisible(true);
    const originalOutline = element.style.outline;
    const originalOutlineOffset = element.style.outlineOffset;
    const originalScrollMargin = element.style.scrollMargin;
    element.style.outline = '3px solid #4f46e5';
    element.style.outlineOffset = '5px';
    element.style.scrollMargin = '90px';
    element.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
    return () => {
      element.style.outline = originalOutline;
      element.style.outlineOffset = originalOutlineOffset;
      element.style.scrollMargin = originalScrollMargin;
    };
  }, [active, currentView, hasDraft, open]);

  useEffect(() => {
    if (!open) return;
    const onKeyDown = (event: KeyboardEvent): void => {
      if (event.key === 'Escape') close();
    };
    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [close, open]);

  const missingText = active.needsDraft && !hasDraft
    ? 'برای مشاهدهٔ این قسمت باید ابتدا یک صفحه را انتخاب یا ایجاد کنی.'
    : 'در چیدمان فعلی، محل این ابزار قابل مشخص‌کردن نیست؛ می‌توانی توضیح این مرحله را مطالعه کنی.';

  return (
    <div data-ea-studio-tour="v1" dir="rtl" className="fixed bottom-4 left-4 z-[1800] text-right font-sans sm:bottom-6 sm:left-6">
      {open ? (
        <section
          id="ea-studio-tour-panel"
          role="dialog"
          aria-modal="false"
          aria-label="راهنمای تعاملی Page Studio"
          className="max-h-[min(82vh,38rem)] w-[min(92vw,23rem)] overflow-y-auto rounded-2xl border border-indigo-200 bg-white p-4 shadow-2xl shadow-slate-900/20 sm:p-5"
        >
          <div className="flex items-start justify-between gap-3">
            <div>
              <p className="text-[11px] font-semibold tracking-wide text-indigo-700">راهنمای شروع با Page Studio</p>
              <h2 className="mt-1 text-base font-bold text-slate-900" aria-live="polite">{active.title}</h2>
            </div>
            <button type="button" onClick={close} aria-label="بستن راهنما" title="بستن راهنما (Esc)"
              className="rounded-lg p-1 text-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900">×</button>
          </div>
          <div className="mt-3 flex items-center justify-between gap-3 text-[11px] text-slate-500">
            <span>مرحله {index + 1} از {steps.length}</span>
            <span>{Math.round(((index + 1) / steps.length) * 100)}٪</span>
          </div>
          <div role="progressbar" aria-label="پیشرفت راهنما" aria-valuenow={index + 1} aria-valuemin={0} aria-valuemax={steps.length}
            className="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
            <div className="h-full rounded-full bg-indigo-600 transition-all duration-200"
              style={{ width: `${((index + 1) / steps.length) * 100}%` }} />
          </div>
          <p className="mt-3 text-sm leading-7 text-slate-700">{active.description}</p>
          <div className="mt-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs leading-6 text-slate-600">
            {targetVisible ? 'بخش مربوط به این مرحله با کادر بنفش مشخص شده است.' : missingText}
          </div>
          <p className="mt-2 text-xs leading-6 text-slate-500">{active.hint}</p>
          <div className="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3">
            <button type="button" onClick={close} className="rounded-lg px-2 py-2 text-xs text-slate-500 hover:bg-slate-100">
              ردکردن راهنما
            </button>
            <div className="flex items-center gap-2">
              <button type="button" disabled={index === 0} onClick={() => setIndex((current) => Math.max(0, current - 1))}
                className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 disabled:cursor-not-allowed disabled:opacity-40">
                قبلی
              </button>
              <button type="button" onClick={() => index === steps.length - 1 ? close() : setIndex((current) => Math.min(steps.length - 1, current + 1))}
                className="rounded-lg bg-indigo-700 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-800">
                {index === steps.length - 1 ? 'پایان راهنما' : 'بعدی'}
              </button>
            </div>
          </div>
        </section>
      ) : (
        <button type="button" aria-controls="ea-studio-tour-panel" aria-expanded="false" onClick={restart}
          className="flex items-center gap-2 rounded-full border border-indigo-200 bg-white px-4 py-2.5 text-xs font-semibold text-indigo-800 shadow-lg shadow-slate-900/10 hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-600">
          <span aria-hidden="true" className="flex size-5 items-center justify-center rounded-full bg-indigo-700 text-xs font-bold text-white">؟</span>
          راهنمای محیط
        </button>
      )}
    </div>
  );
}
