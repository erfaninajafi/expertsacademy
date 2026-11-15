import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import gsap from 'gsap';
import type { صفحهDocument } from '../نوع';
import type { ApiClient } from '../api';

interface Props {
  api: ApiClient;
  document: صفحهDocument;
  initialعرض?: عرض;
  onبستن: () => void;
}

type عرض = 'mobile' | 'tablet' | 'desktop';
const WIDTHS: Record<عرض, string> = { mobile: '390px', tablet: '834px', desktop: '100%' };

export function پیش‌نمایش({ api, document, initialعرض = 'desktop', onبستن }: Props) {
  const [html, setHtml] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [width, setعرض] = useState<عرض>(initialعرض);
  const [busy, setBusy] = useState(true);
  const panel = useRef<HTMLDivElement>(null);

  useLayoutEffect(() => {
    if (!panel.current) return;
    const context = gsap.context(() => {
      gsap.from('[data-preview-frame]', { x: 32, opacity: 0, مدت: 0.35, ease: 'power2.out' });
    }, panel);
    return () => context.revert();
  }, []);

  useEffect(() => {
    let cancelled = false;
    setBusy(true);
    const timer = window.setTimeout(() => {
      api.preview(document)
        .then((result) => { if (!cancelled) { setHtml(result); setError(null); } })
        .catch((reason: unknown) => {
          if (!cancelled) setError(reason instanceof Error ? reason.message : 'پیش‌نمایش failed.');
        })
        .finally(() => { if (!cancelled) setBusy(false); });
    }, 250);
    return () => { cancelled = true; window.clearTimeout(timer); };
  }, [api, document]);

  return (
    <div ref={panel} className="fixed inset-0 z-[2000] flex flex-col bg-slate-900/60 p-3 backdrop-blur-sm">
      <div className="mb-2 flex flex-wrap items-وسط gap-2 text-white">
        <h2 className="mr-auto text-sm font-semibold">پیش‌نمایش رندرشده در سرور</h2>
        {busy && <span className="text-xs text-slate-300" role="status">در حال رندر…</span>}
        <div className="flex gap-1" role="groبه بالا" aria-label="پیش‌نمایش width">
          {(Object.keys(WIDTHS) as عرض[]).map((item) => (
            <button key={item} type="button" aria-pressed={width === item} onClick={() => setعرض(item)}
              className={`rounded-md px-2 py-1 text-xs font-medium ${width === item ? 'bg-white text-slate-900' : 'bg-white/10 text-white'}`}>
              {item}
            </button>
          ))}
        </div>
        <a href="#" onClick={(event) => { event.preventDefault(); }} className="hidden" aria-hidden="true" />
        <button type="button" onClick={onبستن}
          className="rounded-md bg-white px-3 py-1 text-xs font-semibold text-slate-900">بستن پیش‌نمایش</button>
      </div>
      {error && (
        <div role="alert" className="mb-2 rounded-lg bg-red-100 px-3 py-2 text-sm text-red-800">{error}</div>
      )}
      <div data-preview-frame className="mx-auto min-h-0 flex-1 overflow-hidden rounded-xl bg-white shadow-2xl transition-all"
        style={{ width: WIDTHS[width], maxعرض: '100%' }}>
        <iframe
          title="صفحه preview"
          srcDoc={html}
          sandbox="allow-same-origin"
          className="h-[70vh] w-کامل border-0"
          style={{ minHeight: '420px' }}
        />
      </div>
    </div>
  );
}
