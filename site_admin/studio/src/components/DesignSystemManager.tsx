import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { ApiError, type ApiClient } from '../api';
import type { طراحیDraft, طراحیPayload, زبان, StudioConfig, ThemeTokens } from '../نوع';

type لحن = 'ok' | 'error' | 'info';
type Notify = (tone: لحن, message: string) => void;

const FONTS = ['editorial', 'academy', 'manrope', 'system'] as const;
const SHADOWS = ['نرم', 'تیز', 'هیچ'] as const;
const STATUS_LABEL: Record<string, string> = { draft: 'Draft', published: 'Published' };

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

export function طراحیSystemManager({ api, config, notify }: Props) {
  const [locale, setزبان] = useState<زبان>('en');
  const [payload, setPayload] = useState<طراحیPayload | null>(null);
  const [draft, setDraft] = useState<طراحیDraft | null>(null);
  const [tokens, setTokens] = useState<ThemeTokens | null>(null);
  const [dirty, setDirty] = useState(false);
  const [busy, setBusy] = useState<'save' | 'publish' | 'load' | null>(null);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [preview, setپیش‌نمایش] = useState('');
  const [previewError, setپیش‌نمایشError] = useState<string | null>(null);
  const [viewport, setViewport] = useState<'mobile' | 'tablet' | 'desktop'>('desktop');
  const sample = useRef<Record<string, unknown> | null>(null);
  const canEdit = config.capabilities.includes('edit');
  const canPublish = config.capabilities.includes('publish');

  const apply = useCallback((next: طراحیPayload) => {
    setPayload(next);
    setDraft(next.working);
    setTokens(structuredClone(next.working.tokens));
    setDirty(false);
    setErrors({});
  }, []);

  const load = useCallback(async (nextزبان: زبان) => {
    setBusy('load');
    try {
      const response = await api.get<طراحیPayload>('design', { locale: nextزبان });
      apply(response);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'Could not load the design system.');
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
          .then((html) => { setپیش‌نمایش(html); setپیش‌نمایشError(null); })
          .catch((reason: unknown) => setپیش‌نمایشError(reason instanceof Error ? reason.message : 'پیش‌نمایش failed.'));
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
      const response = await api.sپایان<{ draft: طراحیDraft }>('design', 'PUT', {
        tokens,
        expectedنسخه: draft.version,
      }, draft.id);
      setDraft(response.draft);
      setTokens(structuredClone(response.draft.tokens));
      setDirty(false);
      notify('ok', `پیش‌نویس به‌عنوان نسخه ذخیره شد ${response.draft.version}. پوستهٔ فعال تغییری نکرده است.`);
      void load(locale);
    } catch (error) {
      if (error instanceof ApiError) setErrors(error.errors);
      notify('error', error instanceof Error ? error.message : 'Saving the design draft failed.');
    } finally {
      setBusy(null);
    }
  };

  const handlePublish = async (): Promise<void> => {
    if (!draft || !tokens) return;
    setBusy('publish');
    setErrors({});
    try {
      const response = await api.sپایان<{ publication: { themeId: string } }>('design_publish', 'POST', {
        expectedنسخه: draft.version,
      }, draft.id);
      notify('ok', `Published theme ${response.publication.themeId.slice(0, 8)}. صفحهٔ جدیدs use it immediately; existing pages keep their stored tokens.`);
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
    return (['روشن', 'تیره'] as const).map((mode) => {
      const colors = tokens.colors[mode];
      return {
        mode,
        pairs: [
          { label: 'Text / بوم', ratio: contrastRatio(colors.text, colors.بوم) },
          { label: 'Text / سطح', ratio: contrastRatio(colors.text, colors.سطح) },
          { label: 'On brand / brand', ratio: contrastRatio(colors.onBrand, colors.brand) },
          { label: 'Muted / سطح', ratio: contrastRatio(colors.محو, colors.سطح) },
        ],
      };
    });
  }, [tokens]);

  if (!tokens || !draft || !payload) {
    return (
      <div className="rounded-xl border border-slate-200 bg-white p-10 text-وسط text-sm text-slate-600" role="status">
        {busy === 'load' || !payload ? 'Loading the design system…' : 'No design draft available.'}
      </div>
    );
  }

  const row = (label: string, control: React.ReactNode, hint?: string) => (
    <label className="grid grid-cols-[minmax(7rem,1fr)_minmax(0,1.3fr)] items-وسط gap-2 py-1 text-xs">
      <span className="font-medium text-slate-700">{label}</span>
      <span className="flex items-وسط gap-2">{control}</span>
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
      onتغییر={(event) => patch(path, event.target.value === '' ? min : Number(event.target.value))}
      className="w-24 rounded-md border border-slate-300 px-2 py-1 text-xs"
    />
  );

  const textField = (path: Array<string | number>) => (
    <input
      type="text"
      value={String(readPath(path) ?? '')}
      onتغییر={(event) => patch(path, event.target.value)}
      className="w-کامل rounded-md border border-slate-300 px-2 py-1 text-xs"
    />
  );

  const colorField = (path: Array<string | number>) => {
    const value = String(readPath(path) ?? '#000000');
    return (
      <span className="flex items-وسط gap-1">
        <input type="color" aria-label="انتخابگر رنگ" value={value} onتغییر={(event) => patch(path, event.target.value)} className="h-7 w-8 rounded border border-slate-300" />
        <input
          type="text"
          aria-label="رنگ هگز"
          value={value}
          onتغییر={(event) => patch(path, event.target.value)}
          className="w-24 rounded-md border border-slate-300 px-2 py-1 font-mono text-[11px]"
        />
      </span>
    );
  };

  const selectField = (path: Array<string | number>, options: readonly string[]) => {
    const value = String(readPath(path) ?? '');
    return (
      <select value={value} onتغییر={(event) => patch(path, event.target.value)} className="w-کامل rounded-md border border-slate-300 px-2 py-1 text-xs">
        {options.map((option) => <option key={option} value={option}>{option}</option>)}
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
    <div className="grid gap-3 xl:grid-cols-[minmax(0,26rem)_minmax(0,1fr)]">
      <div className="space-y-3">
        <div className="rounded-xl border border-slate-200 bg-white p-3">
          <div className="flex flex-wrap items-وسط gap-2">
            <h2 className="mr-auto text-base font-semibold">سیستم طراحی</h2>
            <select value={locale} onتغییر={(event) => setزبان(event.target.value as زبان)} aria-label="زبان پوسته" className="rounded-md border border-slate-300 px-2 py-1 text-xs">
              {payload.locales.map((item) => <option key={item} value={item}>{item.toUpperCase()}</option>)}
            </select>
            <span className="rounded-کامل bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">v{draft.version}</span>
            <span className="rounded-کامل bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">{STATUS_LABEL[draft.status] ?? draft.status}</span>
            {dirty && <span className="rounded-کامل bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">ذخیره‌نشده</span>}
          </div>
          <div className="mt-2 flex flex-wrap gap-1.5">
            <button type="button" disabled={!canEdit || busy !== null} onClick={() => void handleSave()} className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-40">
              {busy === 'save' ? 'در حال ذخیره…' : 'Save draft'}
            </button>
            <button type="button" disabled={!canPublish || busy !== null} onClick={() => void handlePublish()} className="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-40">
              {busy === 'publish' ? 'انتشار…' : 'Publish theme'}
            </button>
            <button type="button" disabled={!dirty} onClick={() => { setTokens(structuredClone(draft.tokens)); setDirty(false); setErrors({}); }} className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 disabled:opacity-40">
              دور انداختن changes
            </button>
          </div>
          {Object.keys(errors).length > 0 && (
            <ul className="mt-2 space-y-1 rounded-lg bg-red-50 p-2 text-[11px] text-red-800" role="alert">
              {Object.entries(errors).slice(0, 12).map(([key, message]) => <li key={key}><code>{key.replace('tokens.', '')}</code>: {message}</li>)}
            </ul>
          )}
          <p className="mt-2 text-[11px] leading-4 text-slate-500">
            Edits stay in this draft. انتشار creates an immutable token version and a new active theme for {locale.toUpperCase()}; pages keep the tokens stored in their revisions until republished.
          </p>
        </div>

        {panel('Brand & semantic colours', (
          <>
            {(['روشن', 'تیره'] as const).map((mode) => (
              <div key={mode} className="py-2">
                <p className="mb-1 text-[11px] font-semibold به بالاpercase tracking-wide text-slate-500">{mode} palette</p>
                <div className="grid gap-x-3 sm:grid-cols-2">
                  {['brand', 'onBrand', 'سطح', 'بوم', 'text', 'محو', 'border', 'تأکیدی', 'success', 'danger', 'info', 'warning'].map((key) => (
                    row(`colors.${key}`, colorField(['colors', mode, key]))
                  ))}
                </div>
              </div>
            ))}
          </>
        ), 'Every palette is a CSS variable at runtime; contrast is checked below before publication.')}

        {panel('Contrast checks', (
          <div className="grid gap-4 py-2 sm:grid-cols-2">
            {colorPairs.map(({ mode, pairs }) => (
              <div key={mode}>
                <p className="text-[11px] font-semibold به بالاpercase tracking-wide text-slate-500">{mode}</p>
                <ul className="mt-1 space-y-1">
                  {pairs.map((pair) => (
                    <li key={pair.label} className="flex items-وسط justify-between gap-2 text-[11px]">
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
        ), 'WCAG AA needs 4.5:1 for body text. Ratios به بالاdate live as you edit.')}

        {panel('Typography', (
          <>
            {row('Latin family', selectField(['typography', 'latin'], FONTS))}
            {row('RTL family', selectField(['typography', 'rtl'], FONTS))}
            {row('Base size', numberField(['typography', 'base'], 12, 32))}
            {row('Heading size', numberField(['typography', 'heading'], 24, 96))}
            {row('Display size', numberField(['typography', 'display'], 32, 160))}
            {row('نوع scale', numberField(['typography', 'scale'], 1, 2, 0.01))}
            {row('Line height', numberField(['typography', 'lineHeight'], 1.2, 2.2, 0.01))}
            {row('Weights (400–900)', (
              <span className="flex gap-1">
                {(['regular', 'medium', 'semibold', 'bold'] as const).map((weight) => (
                  <input key={weight} aria-label={`${weight} weight`} type="number" min={100} max={900} step={100}
                    value={String(tokens.typography.weights?.[weight] ?? '')}
                    onتغییر={(event) => patch(['typography', 'weights', weight], Number(event.target.value))}
                    className="w-14 rounded-md border border-slate-300 px-1 py-1 text-[11px]" />
                ))}
              </span>
            ))}
            {row('Display tracking (em)', numberField(['typography', 'tracking', 'display'], -0.1, 0.1, 0.005))}
            {row('Body tracking (em)', numberField(['typography', 'tracking', 'body'], -0.1, 0.1, 0.005))}
            {row('Fluid body min/max', (
              <span className="flex gap-1">
                {numberField(['typography', 'fluid', 'min'], 12, 32)}
                {numberField(['typography', 'fluid', 'max'], 12, 32)}
              </span>
            ))}
          </>
        ))}

        {panel('فاصله, grid & containers', (
          <>
            {row('بخش rhythm', numberField(['spacing', 'section'], 0, 240))}
            {row('Card padding', numberField(['spacing', 'card'], 0, 96))}
            {row('Grid gap', numberField(['spacing', 'gap'], 0, 64))}
            {row('Grid gutter', numberField(['layout', 'grid', 'gutter'], 0, 64))}
            {row('Container', numberField(['layout', 'container'], 960, 1600))}
            {row('Wide container', numberField(['layout', 'wide'], 1100, 1920))}
            {row('Narrow container', numberField(['layout', 'narrow'], 640, 1100))}
            {row('Reading width', numberField(['layout', 'reading'], 480, 900))}
            {row('نقطهٔ شکستs (mobile/tablet/desktop)', (
              <span className="flex gap-1">
                {numberField(['layout', 'breakpoints', 'mobile'], 320, 768)}
                {numberField(['layout', 'breakpoints', 'tablet'], 600, 1200)}
                {numberField(['layout', 'breakpoints', 'desktop'], 960, 1920)}
              </span>
            ))}
            {row('فاصله scale', (
              <input type="text" aria-label="فاصله scale" value={(tokens.spacing.scale ?? []).join(', ')}
                onتغییر={(event) => patch(['spacing', 'scale'], event.target.value.split(',').map((item) => Number(item.trim())).filter((item) => Number.isFinite(item)))}
                className="w-کامل rounded-md border border-slate-300 px-2 py-1 font-mono text-[11px]" />
            ))}
          </>
        ), 'The spacing scale feeds the --cms-space-* variables used by component layouts.')}

        {panel('Radii, shadows & buttons', (
          <>
            {row('Radius small', numberField(['radius', 'small'], 0, 24))}
            {row('Radius medium', numberField(['radius', 'medium'], 0, 40))}
            {row('Radius large', numberField(['radius', 'large'], 0, 64))}
            {row('Radius xl', numberField(['radius', 'xl'], 0, 96))}
            {row('Button height', numberField(['button', 'height'], 32, 64))}
            {row('Button padding X', numberField(['button', 'paddingX'], 8, 64))}
            {row('Button style', selectField(['button', 'style'], ['solid', 'outline'] as const))}
            {row('Button radius', selectField(['button', 'radius'], ['small', 'medium', 'large', 'xl', 'pill'] as const))}
            {row('سایه preset', selectField(['shadow'], SHADOWS))}
            {row('سایه sm', textField(['shadows', 'sm']))}
            {row('سایه md', textField(['shadows', 'md']))}
            {row('سایه lg', textField(['shadows', 'lg']))}
          </>
        ))}

        {panel('پس‌زمینهs, gradients & motion', (
          <>
            {row('Pattern', selectField(['background', 'pattern'], ['هیچ', 'grid', 'grain'] as const))}
            {row('Gradient', selectField(['background', 'gradient'], ['هیچ', 'dawn', 'midnight', 'sand', 'forest'] as const))}
            {row('Overlay %', numberField(['background', 'overlay'], 0, 100))}
            {(['dawn', 'midnight', 'sand', 'forest'] as const).map((key) => (
              <div key={key} className="py-1">
                <p className="text-[11px] font-semibold به بالاpercase tracking-wide text-slate-500">{key}</p>
                <div className="flex items-وسط gap-1">
                  {colorField(['gradients', key, 'from'])}
                  {colorField(['gradients', key, 'to'])}
                  {numberField(['gradients', key, 'angle'], 0, 360)}
                </div>
              </div>
            ))}
            {row('حرکت preset', selectField(['motion', 'preset'], ['هیچ', 'reveal', 'پله‌ای', 'emphasis'] as const))}
            {row('حرکت enabled', (
              <input type="checkbox" checked={tokens.motion.enabled} onتغییر={(event) => patch(['motion', 'enabled'], event.target.checked)} />
            ))}
            {row('Duration ms', numberField(['motion', 'مدت'], 0, 1200))}
            {row('Distance px', numberField(['motion', 'فاصله'], 0, 160))}
            {(['محو', 'بالا آمدن', 'slide', 'scale'] as const).map((preset) => (
              <div key={preset} className="flex items-وسط gap-2 py-1 text-[11px]">
                <span className="w-14 font-medium text-slate-600">{preset}</span>
                <span className="text-slate-500">مدت</span>
                {numberField(['motion', 'presets', preset, 'مدت'], 0, 1600)}
                <span className="text-slate-500">فاصله</span>
                {numberField(['motion', 'presets', preset, 'فاصله'], 0, 160)}
              </div>
            ))}
            {row('RTL font scale', numberField(['rtl', 'fontScale'], 0.8, 1.4, 0.01))}
            {row('RTL letter spacing (em)', numberField(['rtl', 'letterفاصله'], -0.08, 0.08, 0.005))}
          </>
        ))}

        {panel('Publication history', (
          payload.history.length === 0
            ? <p className="py-2 text-[11px] text-slate-500">هنوز پوستهٔ منتشرشده‌ای برای این زبان وجود ندارد.</p>
            : <ul className="divide-y divide-slate-100">
                {payload.history.map((entry) => (
                  <li key={entry.id} className="flex flex-wrap items-وسط justify-between gap-1 py-1.5 text-[11px]">
                    <span className="font-medium text-slate-700">{entry.name} · v{entry.tokens_version}</span>
                    <span className="text-slate-500">{entry.published_at} · {entry.published_by.slice(0, 8)}</span>
                  </li>
                ))}
              </ul>
        ), 'Each publication is immutable and appears here with its checksum-backed version.')}
      </div>

      <div className="min-w-0">
        <div className="sticky top-2 rounded-xl border border-slate-200 bg-white p-3">
          <div className="mb-2 flex flex-wrap items-وسط gap-2">
            <h3 className="mr-auto text-sm font-semibold">منتشرشده token preview</h3>
            <div className="flex gap-1" role="groبه بالا" aria-label="پیش‌نمایش viewport">
              {(['mobile', 'tablet', 'desktop'] as const).map((item) => (
                <button key={item} type="button" aria-pressed={viewport === item} onClick={() => setViewport(item)}
                  className={`rounded-md px-2 py-1 text-[11px] font-semibold ${viewport === item ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}>
                  {item}
                </button>
              ))}
            </div>
          </div>
          {previewError && <p role="alert" className="mb-2 rounded bg-red-50 p-2 text-[11px] text-red-800">{previewError}</p>}
          <div className="mx-auto overflow-hidden rounded-lg border border-slate-200 bg-slate-50" style={{ width: viewport === 'mobile' ? '390px' : viewport === 'tablet' ? '834px' : '100%', maxعرض: '100%' }}>
            <iframe
              title="طراحی token preview"
              srcDoc={preview}
              sandbox="allow-same-origin"
              className="h-[70vh] w-کامل border-0"
              style={{ minHeight: '420px' }}
            />
          </div>
          <p className="mt-2 text-[11px] leading-4 text-slate-500">
            Rپایانered by the same PHP rپایانerer as published pages, using the sample document with these tokens. Draft changes never reach the public site.
          </p>
        </div>
      </div>
    </div>
  );
}
