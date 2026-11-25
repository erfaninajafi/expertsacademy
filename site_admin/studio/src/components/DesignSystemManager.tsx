import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { ApiError, type ApiClient } from '../api';
import type { DesignDraft, DesignPayload, Locale, StudioConfig, ThemeTokens } from '../types';
import { deviceLabel } from '../i18n';

type Tone = 'ok' | 'error' | 'info';
type Notify = (tone: Tone, message: string) => void;

const FONTS = ['editorial', 'academy', 'manrope', 'system'] as const;
const SHADOWS = ['soft', 'crisp', 'none'] as const;
const STATUS_LABEL: Record<string, string> = { draft: 'پیش‌نویس تنظیمات', published: 'تنظیمات منتشرشده' };

function luminance(hex: string): number {
  const value = hex.replace('#', '');
  const channels = [0, 2, 4].map((offset) => {
    const channel = parseInt(value.slice(offset, offset + 2), 16) / 255;
    return channel <= 0.03928 ? channel / 12.92 : ((channel + 0.055) / 1.055) ** 2.4;
  });
  return 0.2126 * channels[0] + 0.7152 * channels[1] + 0.0722 * channels[2];
}

export function contrastRatio(foreground: string, background: string): number {
  if (!/^#[0-9a-f]{6}$/i.test(foreground) || !/^#[0-9a-f]{6}$/i.test(background)) return 0;
  const a = luminance(foreground);
  const b = luminance(background);
  return Math.round(((Math.max(a, b) + 0.05) / (Math.min(a, b) + 0.05)) * 100) / 100;
}

interface Props {
  api: ApiClient;
  config: StudioConfig;
  notify: Notify;
}

const COLOR_LABELS: Record<string, string> = { brand: 'رنگ اصلی برند', onBrand: 'رنگ متن روی زمینهٔ برند', surface: 'پس‌زمینهٔ کارت‌ها', canvas: 'پس‌زمینهٔ اصلی صفحه', text: 'رنگ متن اصلی', muted: 'رنگ متن کم‌اهمیت', border: 'رنگ خط دور و جداکننده', accent: 'رنگ مکمل و تأکیدی', success: 'رنگ پیام موفقیت', danger: 'رنگ پیام خطا', info: 'رنگ پیام اطلاع‌رسانی', warning: 'رنگ پیام هشدار' };
const GRADIENT_LABELS: Record<string, string> = { dawn: 'ترکیب رنگی شماره ۱ (سپیده)', midnight: 'ترکیب رنگی شماره ۲ (تیره)', sand: 'ترکیب رنگی شماره ۳ (شنی)', forest: 'ترکیب رنگی شماره ۴ (سبز)' };
const MOTION_LABELS: Record<string, string> = { muted: 'محو شدن', fade: 'محو شدن', rise: 'حرکت به بالا', slide: 'حرکت از کنار', scale: 'تغییر اندازه' };
const OPTION_LABELS: Record<string, string> = {
  solid: 'دکمهٔ رنگی (توپر)', outline: 'دکمهٔ دورخط‌دار', small: 'گردی کم', medium: 'گردی متوسط', large: 'گردی زیاد', xl: 'گردی خیلی زیاد', pill: 'کاملاً گرد (کپسولی)',
  none: 'بدون', soft: 'سایهٔ نرم', crisp: 'سایهٔ مشخص', grid: 'خطوط شبکه‌ای', grain: 'بافت دانه‌دانه', dawn: 'سپیده (رنگ‌های گرم)', midnight: 'نیمه‌شب (تیره)', sand: 'شنی (روشن)', forest: 'جنگلی (سبز)',
  reveal: 'ظاهرشدن تدریجی', stagger: 'ظاهرشدن پشت‌سرهم', emphasis: 'حرکت تأکیدی', editorial: 'فونت تحریری (Fraunces)', academy: 'قالب فونت آکادمی', manrope: 'Manrope', system: 'فونت پیش‌فرض دستگاه',
};

export function DesignSystemManager({ api, config, notify }: Props) {
  const [locale, setLocale] = useState<Locale>('en');
  const [payload, setPayload] = useState<DesignPayload | null>(null);
  const [draft, setDraft] = useState<DesignDraft | null>(null);
  const [tokens, setTokens] = useState<ThemeTokens | null>(null);
  const [dirty, setDirty] = useState(false);
  const [busy, setBusy] = useState<'save' | 'publish' | 'load' | null>(null);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [preview, setPreview] = useState('');
  const [previewError, setPreviewError] = useState<string | null>(null);
  const [viewport, setViewport] = useState<'mobile' | 'tablet' | 'desktop'>('desktop');
  const sample = useRef<Record<string, unknown> | null>(null);
  const canEdit = config.capabilities.includes('edit');
  const canPublish = config.capabilities.includes('publish');

  const apply = useCallback((next: DesignPayload) => {
    setPayload(next);
    setDraft(next.working);
    setTokens(structuredClone(next.working.tokens));
    setDirty(false);
    setErrors({});
  }, []);

  const load = useCallback(async (nextLocale: Locale) => {
    setBusy('load');
    try {
      const response = await api.get<DesignPayload>('design', { locale: nextLocale });
      apply(response);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'بارگذاری سیستم طراحی ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, apply, notify]);

  useEffect(() => { void load(locale); }, [load, locale]);

  useEffect(() => {
    if (sample.current) return;
    void api.get<{ document: Record<string, unknown> }>('sample', { locale: 'en' })
      .then((response) => { sample.current = response.document; })
      .catch(() => undefined);
  }, [api]);

  useEffect(() => {
    if (!tokens) return;
    const timer = window.setTimeout(() => {
      const timerId = window.setTimeout(() => {
        const base = sample.current;
        if (!base) return;
        const document = structuredClone(base);
        (document as { theme: { tokens: ThemeTokens } }).theme.tokens = tokens;
        (document as { settings: { container: number } }).settings.container = tokens.layout.container;
        api.preview(document)
          .then((html) => { setPreview(html); setPreviewError(null); })
          .catch((reason: unknown) => setPreviewError(reason instanceof Error ? reason.message : 'پیش‌نمایش انجام نشد.'));
      }, 60);
      return () => window.clearTimeout(timerId);
    }, 450);
    return () => window.clearTimeout(timer);
  }, [api, tokens]);

  const patch = (path: Array<string | number>, value: unknown): void => {
    setTokens((current) => {
      if (!current) return current;
      const next = structuredClone(current) as unknown as Record<string, unknown>;
      let cursor: Record<string, unknown> | unknown[] = next;
      for (let index = 0; index < path.length - 1; index += 1) {
        const key = path[index];
        const container = cursor as Record<string | number, unknown>;
        if (container[key] === null || typeof container[key] !== 'object') {
          container[key] = {};
        }
        cursor = container[key] as Record<string, unknown> | unknown[];
      }
      (cursor as Record<string | number, unknown>)[path[path.length - 1]] = value;
      return next as unknown as ThemeTokens;
    });
    setDirty(true);
  };

  const handleSave = async (): Promise<void> => {
    if (!tokens || !draft) return;
    setBusy('save');
    setErrors({});
    try {
      const response = await api.send<{ draft: DesignDraft }>('design', 'PUT', {
        tokens,
        expectedVersion: draft.version,
      }, draft.id);
      setDraft(response.draft);
      setTokens(structuredClone(response.draft.tokens));
      setDirty(false);
      notify('ok', `تنظیمات به‌عنوان پیش‌نویس نسخهٔ ${response.draft.version} ذخیره شد؛ تنظیمات فعال هنوز تغییر نکرده است.`);
      void load(locale);
    } catch (error) {
      if (error instanceof ApiError) setErrors(error.errors);
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ پیش‌نویس طراحی انجام نشد.');
    } finally {
      setBusy(null);
    }
  };

  const handlePublish = async (): Promise<void> => {
    if (!draft || !tokens) return;
    setBusy('publish');
    setErrors({});
    try {
      const response = await api.send<{ publication: { themeId: string } }>('design_publish', 'POST', {
        expectedVersion: draft.version,
      }, draft.id);
      notify('ok', 'تنظیمات ظاهری این زبان منتشر شد. ظاهر صفحات موجود خودکار تغییر نمی‌کند و اتصال صفحات جدید به پوستهٔ فعال هنوز کامل نشده است.');
      await load(locale);
    } catch (error) {
      if (error instanceof ApiError) setErrors(error.errors);
      notify('error', error instanceof Error ? error.message : 'انتشار انجام نشد.');
    } finally {
      setBusy(null);
    }
  };

  const colorPairs = useMemo(() => {
    if (!tokens) return [];
    return (['light', 'dark'] as const).map((mode) => {
      const colors = tokens.colors[mode];
      return {
        mode,
        pairs: [
          { label: 'متن اصلی روی پس‌زمینهٔ صفحه', ratio: contrastRatio(colors.text, colors.canvas) },
          { label: 'متن اصلی روی کارت‌ها', ratio: contrastRatio(colors.text, colors.surface) },
          { label: 'متن روی زمینهٔ رنگ برند', ratio: contrastRatio(colors.onBrand, colors.brand) },
          { label: 'متن کم‌اهمیت روی کارت‌ها', ratio: contrastRatio(colors.muted, colors.surface) },
        ],
      };
    });
  }, [tokens]);

  if (!tokens || !draft || !payload) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-10 text-center text-sm text-slate-600" role="status">
        {busy === 'load' || !payload ? 'در حال بارگذاری سیستم طراحی…' : 'پیش‌نویس طراحی موجود نیست.'}
      </div>
    );
  }

  const row = (label: string, control: React.ReactNode, hint?: string) => (
    <label className="grid grid-cols-[minmax(10rem,1fr)_minmax(0,1.1fr)] items-center gap-2 py-1 text-xs">
      <span className="font-medium text-slate-700">{label}</span>
      <span className="flex items-center gap-2">{control}</span>
      {hint && <span className="col-span-2 text-[11px] leading-4 text-slate-500">{hint}</span>}
    </label>
  );

  const readPath = (path: Array<string | number>): unknown => {
    let value: unknown = tokens;
    for (const key of path) {
      if (value === null || typeof value !== 'object') return undefined;
      value = (value as Record<string | number, unknown>)[key];
    }
    return value;
  };

  const numberField = (path: Array<string | number>, min: number, max: number, step = 1) => (
    <input
      type="number"
      min={min}
      max={max}
      step={step}
      value={String(readPath(path) ?? '')}
      onChange={(event) => patch(path, event.target.value === '' ? min : Number(event.target.value))}
      className="w-24 rounded-md border border-slate-300 px-2 py-1 text-xs"
    />
  );

  const textField = (path: Array<string | number>) => (
    <input
      type="text"
      value={String(readPath(path) ?? '')}
      onChange={(event) => patch(path, event.target.value)}
      className="w-full rounded-md border border-slate-300 px-2 py-1 text-xs"
    />
  );

  const colorField = (path: Array<string | number>) => {
    const value = String(readPath(path) ?? '#000000');
    return (
      <span className="flex items-center gap-1">
        <input type="color" aria-label="انتخابگر رنگ" value={value} onChange={(event) => patch(path, event.target.value)} className="h-7 w-8 rounded border border-slate-300" />
        <input
          type="text"
          aria-label="رنگ هگز"
          value={value}
          onChange={(event) => patch(path, event.target.value)}
          className="w-24 rounded-md border border-slate-300 px-2 py-1 font-mono text-[11px]"
        />
      </span>
    );
  };

  const selectField = (path: Array<string | number>, options: readonly string[]) => {
    const value = String(readPath(path) ?? '');
    return (
      <select value={value} onChange={(event) => patch(path, event.target.value)} className="w-full rounded-md border border-slate-300 px-2 py-1 text-xs">
        {options.map((option) => <option key={option} value={option}>{OPTION_LABELS[option] ?? option}</option>)}
      </select>
    );
  };

  const panel = (title: string, children: React.ReactNode, description?: string) => (
    <details open className="rounded-lg border border-slate-200 bg-white p-3">
      <summary className="cursor-pointer text-sm font-semibold text-slate-800">{title}</summary>
      {description && <p className="mt-1 text-[11px] leading-4 text-slate-500">{description}</p>}
      <div className="mt-2 divide-y divide-slate-100">{children}</div>
    </details>
  );

  return (
    <div className="grid gap-3 xl:grid-cols-[minmax(0,34rem)_minmax(0,1fr)]">
      <div className="space-y-3">
        <div className="rounded-xl border border-slate-200 bg-white p-3">
          <div className="flex flex-wrap items-center gap-2">
            <h2 className="ml-auto text-base font-semibold">تنظیم ظاهر صفحات Page Studio</h2>
            <span className="text-xs font-medium text-slate-600">زبان صفحات:</span>
            <select value={locale} onChange={(event) => setLocale(event.target.value as Locale)} aria-label="زبان تنظیمات ظاهری صفحات" className="rounded-md border border-slate-300 px-2 py-1 text-xs">
              {payload.locales.map((item) => <option key={item} value={item}>{item === 'fa' ? 'فارسی' : 'انگلیسی'}</option>)}
            </select>
            <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">نسخه {draft.version}</span>
            <span className="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">{STATUS_LABEL[draft.status] ?? draft.status}</span>
            {dirty && <span className="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">تغییرات ذخیره‌نشده</span>}
          </div>
          <div className="mt-2 flex flex-wrap gap-1.5">
            <button type="button" disabled={!canEdit || busy !== null} onClick={() => void handleSave()} className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-40">
              {busy === 'save' ? 'در حال ذخیره…' : 'ذخیره تنظیمات (بدون انتشار)'}
            </button>
            <button type="button" disabled={!canPublish || busy !== null} onClick={() => void handlePublish()} className="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-40">
              {busy === 'publish' ? 'در حال انتشار…' : 'انتشار تنظیمات این زبان'}
            </button>
            <button type="button" disabled={!dirty} onClick={() => { setTokens(structuredClone(draft.tokens)); setDirty(false); setErrors({}); }} className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 disabled:opacity-40">لغو تغییرات ذخیره‌نشده</button>
          </div>
          {Object.keys(errors).length > 0 && (
            <ul className="mt-2 space-y-1 rounded-lg bg-red-50 p-2 text-[11px] text-red-800" role="alert">
              {Object.entries(errors).slice(0, 12).map(([key, message]) => <li key={key}><code>{key.replace('tokens.', '')}</code>: {message}</li>)}
            </ul>
          )}
          <p className="mt-2 text-[11px] leading-4 text-slate-500">
            این تنظیمات رنگ‌ها، فونت‌ها، عرض محتوا و ظاهر اجزای صفحات ساخته‌شده با Page Studio را مشخص می‌کنند؛ نه دکمه‌های پنل مدیریت و نه صفحات CMS قدیمی را. «ذخیره» فقط پیش‌نویس را نگه می‌دارد و «انتشار» پوستهٔ فعال همین زبان را ثبت می‌کند. در نسخه فعلی، ظاهر صفحات موجود و حتی صفحات تازه‌ساخته‌شده خودکار با پوسته منتشرشده عوض نمی‌شود.
          </p>
        </div>

        {panel('رنگ نوشته‌ها، کارت‌ها و پس‌زمینهٔ صفحه', (
          <>
            {(['light', 'dark'] as const).map((mode) => (
              <div key={mode} className="py-2">
                <p className="mb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-500">{mode === 'light' ? 'رنگ‌های حالت روشن' : 'رنگ‌های حالت تیره'}</p>
                <div className="grid gap-x-3">
                  {['brand', 'onBrand', 'surface', 'canvas', 'text', 'muted', 'border', 'accent', 'success', 'danger', 'info', 'warning'].map((key) => (
                    row(COLOR_LABELS[key] ?? key, colorField(['colors', mode, key]))
                  ))}
                </div>
              </div>
            ))}
          </>
        ), 'رنگ‌ها برای بخش‌های مختلف صفحه مثل زمینهٔ اصلی، کارت‌ها، متن‌ها و پیام‌ها تعریف می‌شوند. حالت روشن و تیره را جداگانه تنظیم کن.') }

        {panel('بررسی خوانایی رنگ نوشته‌ها', (
          <div className="grid gap-4 py-2 sm:grid-cols-2">
            {colorPairs.map(({ mode, pairs }) => (
              <div key={mode}>
                <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{mode === 'light' ? 'روشن' : 'تیره'}</p>
                <ul className="mt-1 space-y-1">
                  {pairs.map((pair) => (
                    <li key={pair.label} className="flex items-center justify-between gap-2 text-[11px]">
                      <span className="text-slate-600">{pair.label}</span>
                      <span className={`rounded px-1.5 py-0.5 font-semibold ${pair.ratio >= 4.5 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}`}>
                        {pair.ratio.toFixed(2)}:1
                      </span>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        ), 'عدد سبز یعنی خوانایی بهتر و عدد قرمز یعنی خوانایی ناکافی. برای متن معمولی، نسبت رنگ متن به زمینه باید حداقل ۴٫۵ به ۱ باشد.') }

        {panel('فونت‌ها و اندازهٔ نوشته‌های صفحه', (
          <>
            {row('فونت نوشته‌های انگلیسی', selectField(['typography', 'latin'], FONTS))}
            {row('فونت نوشته‌های فارسی (RTL)', selectField(['typography', 'rtl'], FONTS))}
            {row('اندازه متن معمولی (پیکسل)', numberField(['typography', 'base'], 12, 32))}
            {row('اندازه عنوان بخش‌ها (پیکسل)', numberField(['typography', 'heading'], 24, 96))}
            {row('اندازه تیتر خیلی بزرگ (پیکسل)', numberField(['typography', 'display'], 32, 160))}
            {row('ضریب تغییر اندازه نوشته‌ها', numberField(['typography', 'scale'], 1, 2, 0.01), 'این عدد یک ضریب است، نه پیکسل؛ میزان اثر آن به کامپوننت استفاده‌کننده بستگی دارد.') }
            {row('فاصله عمودی بین خطوط نوشته', numberField(['typography', 'lineHeight'], 1.2, 2.2, 0.01), 'مثلاً ۱٫۷ یعنی فاصله خطوط برابر ۱٫۷ برابر اندازه فونت است.') }
            {row('ضخامت نوشته‌ها (۴۰۰ تا ۹۰۰)', (
              <span className="flex flex-wrap gap-1">
                {(['regular', 'medium', 'semibold', 'bold'] as const).map((weight) => (
                  <span key={weight} className="inline-flex flex-col gap-0.5">
                    <span className="text-[10px] text-slate-500">{({ regular: 'عادی', medium: 'متوسط', semibold: 'نیمه‌ضخیم', bold: 'ضخیم' } as const)[weight]}</span>
                    <input aria-label={`ضخامت ${weight}`} type="number" min={100} max={900} step={100}
                      value={String(tokens.typography.weights?.[weight] ?? '')}
                      onChange={(event) => patch(['typography', 'weights', weight], Number(event.target.value))}
                      className="w-14 rounded-md border border-slate-300 px-1 py-1 text-[11px]" />
                  </span>
                ))}
              </span>
            ))}
            {row('فاصله حروف تیترهای بزرگ (em)', numberField(['typography', 'tracking', 'display'], -0.1, 0.1, 0.005), 'مقدار منفی حروف را به هم نزدیک‌تر و مقدار مثبت از هم دورتر می‌کند.') }
            {row('فاصله بین حروف متن (em)', numberField(['typography', 'tracking', 'body'], -0.1, 0.1, 0.005))}
            {row('کمترین و بیشترین اندازه متن واکنش‌گرا (پیکسل)', (
              <span className="flex gap-1">
                <span className="inline-flex flex-col gap-0.5"><span className="text-[10px] text-slate-500">حداقل</span>{numberField(['typography', 'fluid', 'min'], 12, 32)}</span>
                <span className="inline-flex flex-col gap-0.5"><span className="text-[10px] text-slate-500">حداکثر</span>{numberField(['typography', 'fluid', 'max'], 12, 32)}</span>
              </span>
            ))}
          </>
        ), 'اندازه‌ها برحسب پیکسل‌اند، مگر اینکه در کنار فیلد واحد دیگری نوشته شده باشد. این تنظیمات ویژهٔ نوشته‌های داخل صفحات Studio است.') }

        {panel('عرض محتوا و فاصله‌های صفحه', (
          <>
            {row('فاصله عمودی بین بخش‌های صفحه (پیکسل)', numberField(['spacing', 'section'], 0, 240))}
            {row('فاصله متن تا لبه داخلی کارت (پیکسل)', numberField(['spacing', 'card'], 0, 96))}
            {row('فاصله بین کارت‌ها و اجزا (پیکسل)', numberField(['spacing', 'gap'], 0, 64))}
            {row('فاصله بین ستون‌ها (پیکسل)', numberField(['layout', 'grid', 'gutter'], 0, 64))}
            {row('حداکثر عرض بخش‌های معمولی (پیکسل)', numberField(['layout', 'container'], 960, 1600))}
            {row('حداکثر عرض بخش‌های پهن (پیکسل)', numberField(['layout', 'wide'], 1100, 1920))}
            {row('حداکثر عرض بخش‌های باریک (پیکسل)', numberField(['layout', 'narrow'], 640, 1100))}
            {row('عرض پیشنهادی متن طولانی (پیکسل)', numberField(['layout', 'reading'], 480, 900))}
            {row('مرز تغییر چیدمان در اندازه‌های مختلف (پیکسل)', (
              <span className="flex gap-1">
                <span className="inline-flex flex-col gap-0.5"><span className="text-[10px] text-slate-500">موبایل</span>{numberField(['layout', 'breakpoints', 'mobile'], 320, 768)}</span>
                <span className="inline-flex flex-col gap-0.5"><span className="text-[10px] text-slate-500">تبلت</span>{numberField(['layout', 'breakpoints', 'tablet'], 600, 1200)}</span>
                <span className="inline-flex flex-col gap-0.5"><span className="text-[10px] text-slate-500">دسکتاپ</span>{numberField(['layout', 'breakpoints', 'desktop'], 960, 1920)}</span>
              </span>
            ))}
            {row('فاصله‌های آماده برای چیدمان (پیکسل)', (
              <input type="text" aria-label="فاصله‌های آماده (عددهای جداشده با ویرگول)" value={(tokens.spacing.scale ?? []).join(', ')}
                onChange={(event) => patch(['spacing', 'scale'], event.target.value.split(',').map((item) => Number(item.trim())).filter((item) => Number.isFinite(item)))}
                className="w-full rounded-md border border-slate-300 px-2 py-1 font-mono text-[11px]" />
            ))}
          </>
        ), 'عرض‌ها و فاصله‌ها فقط برای چیدمان اجزای Studio هستند و روی چینش پنل مدیریت اثر ندارند. «فاصله‌های آماده» مجموعه عددهایی است که کامپوننت‌ها می‌توانند استفاده کنند.') }

        {panel('ظاهر دکمه‌ها، کارت‌ها و سایه‌ها', (
          <>
            {row('گردی کم برای اجزای کوچک (پیکسل)', numberField(['radius', 'small'], 0, 24))}
            {row('گردی معمولی گوشه‌ها (پیکسل)', numberField(['radius', 'medium'], 0, 40))}
            {row('گردی زیاد گوشه‌ها (پیکسل)', numberField(['radius', 'large'], 0, 64))}
            {row('گردی خیلی زیاد گوشه‌ها (پیکسل)', numberField(['radius', 'xl'], 0, 96))}
            {row('ارتفاع دکمه‌های داخل صفحه (پیکسل)', numberField(['button', 'height'], 32, 64))}
            {row('فاصله متن دکمه تا لبهٔ چپ و راست (پیکسل)', numberField(['button', 'paddingX'], 8, 64))}
            {row('شکل ظاهری دکمه (توپر یا دورخط‌دار)', selectField(['button', 'style'], ['solid', 'outline'] as const))}
            {row('میزان گردی گوشه‌های دکمه', selectField(['button', 'radius'], ['small', 'medium', 'large', 'xl', 'pill'] as const))}
            {row('سبک سایهٔ کارت‌ها و بخش‌ها', selectField(['shadow'], SHADOWS))}
            {row('جزئیات سایهٔ کم (CSS)', textField(['shadows', 'sm']))}
            {row('جزئیات سایهٔ متوسط (CSS)', textField(['shadows', 'md']))}
            {row('جزئیات سایهٔ زیاد (CSS)', textField(['shadows', 'lg']))}
          </>
        ), 'این مقادیر برای کامپوننت‌های ساخته‌شده با Studio هستند؛ دکمه‌های صفحهٔ مدیریت را تغییر نمی‌دهند. فیلدهای CSS برای کاربران فنی‌اند.') }

        {panel('پس‌زمینه و انیمیشن اجزای صفحه', (
          <>
            {row('طرح تزئینی پس‌زمینه', selectField(['background', 'pattern'], ['none', 'grid', 'grain'] as const))}
            {row('رنگ ترکیبی پس‌زمینه (گرادیان)', selectField(['background', 'gradient'], ['none', 'dawn', 'midnight', 'sand', 'forest'] as const))}
            {row('شدت پوشش رنگی پس‌زمینه (درصد)', numberField(['background', 'overlay'], 0, 100))}
            {(['dawn', 'midnight', 'sand', 'forest'] as const).map((key) => (
              <div key={key} className="py-1">
                <p className="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{GRADIENT_LABELS[key] ?? key}</p>
                <div className="flex flex-wrap items-end gap-2">
                    <span className="flex flex-col gap-0.5 text-[10px] text-slate-500">رنگ شروع{colorField(['gradients', key, 'from'])}</span>
                    <span className="flex flex-col gap-0.5 text-[10px] text-slate-500">رنگ پایان{colorField(['gradients', key, 'to'])}</span>
                    <span className="flex flex-col gap-0.5 text-[10px] text-slate-500">زاویه (درجه){numberField(['gradients', key, 'angle'], 0, 360)}</span>
                </div>
              </div>
            ))}
            {row('نوع نمایش متحرک اجزا', selectField(['motion', 'preset'], ['none', 'reveal', 'stagger', 'emphasis'] as const))}
            {row('فعال بودن انیمیشن در صفحه', (
              <input type="checkbox" checked={tokens.motion.enabled} onChange={(event) => patch(['motion', 'enabled'], event.target.checked)} />
            ))}
            {row('زمان اجرای هر انیمیشن (میلی‌ثانیه)', numberField(['motion', 'duration'], 0, 1200))}
            {row('مقدار جابه‌جایی انیمیشن (پیکسل)', numberField(['motion', 'spacing'], 0, 160))}
            {(['muted', 'rise', 'slide', 'scale'] as const).map((preset) => (
              <div key={preset} className="flex items-center gap-2 py-1 text-[11px]">
                <span className="w-24 font-medium text-slate-600">{MOTION_LABELS[preset] ?? preset}</span>
                <span className="text-slate-500">زمان (ms)</span>
                {numberField(['motion', 'presets', preset, 'duration'], 0, 1600)}
                <span className="text-slate-500">جابه‌جایی (px)</span>
                {numberField(['motion', 'presets', preset, 'spacing'], 0, 160)}
              </div>
            ))}
            {row('ضریب اندازه فونت فارسی', numberField(['rtl', 'fontScale'], 0.8, 1.4, 0.01))}
            {row('فاصله حروف متن فارسی (em)', numberField(['rtl', 'letterSpacing'], -0.08, 0.08, 0.005))}
          </>
        ), 'در این بخش رنگ‌ها و حرکت‌های پیش‌فرض کامپوننت‌ها را تنظیم می‌کنی. اگر انیمیشن غیرفعال باشد، تنظیمات حرکت اعمال نمی‌شوند.') }

        {panel('سابقهٔ نسخه‌های منتشرشده', (
          payload.history.length === 0
            ? <p className="py-2 text-[11px] text-slate-500">برای این زبان هنوز هیچ مجموعه تنظیماتی منتشر نشده است.</p>
            : <ul className="divide-y divide-slate-100">
                {payload.history.map((entry) => (
                  <li key={entry.id} className="flex flex-wrap items-center justify-between gap-1 py-1.5 text-[11px]">
                    <span className="font-medium text-slate-700">{entry.name} · v{entry.tokens_version}</span>
                    <span className="text-slate-500">{entry.published_at} · {entry.published_by.slice(0, 8)}</span>
                  </li>
                ))}
              </ul>
        ), 'در اینجا می‌بینی چه زمانی و توسط چه کسی تنظیمات ظاهری این زبان منتشر شده است. انتشار قبلی از سابقه پاک نمی‌شود.') }
      </div>

      <div className="min-w-0">
        <div className="sticky top-2 rounded-xl border border-slate-200 bg-white p-3">
          <div className="mb-2 flex flex-wrap items-center gap-2">
            <h3 className="ml-auto text-sm font-semibold">پیش‌نمایش ظاهر یک صفحهٔ نمونه</h3>
            <div className="flex gap-1" role="group" aria-label="اندازهٔ پیش‌نمایش">
              {(['mobile', 'tablet', 'desktop'] as const).map((item) => (
                <button key={item} type="button" aria-pressed={viewport === item} onClick={() => setViewport(item)}
                  className={`rounded-md px-2 py-1 text-[11px] font-semibold ${viewport === item ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}>
                  {deviceLabel(item)}
                </button>
              ))}
            </div>
          </div>
          {previewError && <p role="alert" className="mb-2 rounded bg-red-50 p-2 text-[11px] text-red-800">{previewError}</p>}
          <div className="mx-auto overflow-hidden rounded-lg border border-slate-200 bg-slate-50" style={{ width: viewport === 'mobile' ? '390px' : viewport === 'tablet' ? '834px' : '100%', maxWidth: '100%' }}>
            <iframe
              title="پیش‌نمایش تنظیمات ظاهر صفحهٔ نمونه"
              srcDoc={preview}
              sandbox="allow-same-origin"
              className="h-[70vh] w-full border-0"
              style={{ minHeight: '420px' }}
            />
          </div>
          <p className="mt-2 text-[11px] leading-4 text-slate-500">این پیش‌نمایش فقط ظاهر یک صفحهٔ آزمایشی را نشان می‌دهد. تا وقتی تنظیمات منتشر نشوند، تغییری در پوستهٔ فعال ایجاد نمی‌شود؛ صفحات موجود هم به‌صورت خودکار تغییر نمی‌کنند.</p>
        </div>
      </div>
    </div>
  );
}
