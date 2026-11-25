import { useEffect, useLayoutEffect, useRef, useState } from 'react';
import gsap from 'gsap';
import type { PageDocument } from '../types';
import type { ApiClient } from '../api';
import { deviceLabel } from '../i18n';

interface Props {
  api: ApiClient;
  document: PageDocument;
  initialWidth?: Width;
  onClose: () => void;
}

type Width = 'mobile' | 'tablet' | 'desktop';
const WIDTHS: Record<Width, string> = { mobile: '390px', tablet: '834px', desktop: '100%' };

export function Preview({ api, document, initialWidth = 'desktop', onClose }: Props) {
  const [html, setHtml] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [width, setWidth] = useState<Width>(initialWidth);
  const [busy, setBusy] = useState(true);
  const panel = useRef<HTMLDivElement>(null);
  const iframeRef = useRef<HTMLIFrameElement>(null);

  useLayoutEffect(() => {
    if (!panel.current) return;
    const context = gsap.context(() => {
      gsap.from('[data-preview-frame]', { x: 32, opacity: 0, duration: 0.35, ease: 'power2.out' });
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
          if (!cancelled) setError(reason instanceof Error ? reason.message : 'پیش‌نمایش انجام نشد.');
        })
        .finally(() => { if (!cancelled) setBusy(false); });
    }, 250);
    return () => { cancelled = true; window.clearTimeout(timer); };
  }, [api, document]);

  // EA_STUDIO_PREVIEW_ESCAPE_V1: close even when keyboard focus is inside srcDoc iframe.
  useEffect(() => {
    let focusedFrameWindow: Window | null = null;
    const closeOnEscape = (event: KeyboardEvent): void => {
      if (event.key !== 'Escape') return;
      event.preventDefault();
      event.stopPropagation();
      onClose();
    };

    const iframe = iframeRef.current;
    const attachFrameListener = (): void => {
      focusedFrameWindow?.removeEventListener('keydown', closeOnEscape, true);
      focusedFrameWindow = null;
      try {
        // This iframe uses srcDoc with allow-same-origin, so its window is accessible.
        focusedFrameWindow = iframe?.contentWindow ?? null;
        focusedFrameWindow?.addEventListener('keydown', closeOnEscape, true);
      } catch {
        // If a future preview becomes cross-origin, host-window Esc still works.
        focusedFrameWindow = null;
      }
    };

    window.addEventListener('keydown', closeOnEscape, true);
    iframe?.addEventListener('load', attachFrameListener);
    attachFrameListener();
    return () => {
      window.removeEventListener('keydown', closeOnEscape, true);
      iframe?.removeEventListener('load', attachFrameListener);
      focusedFrameWindow?.removeEventListener('keydown', closeOnEscape, true);
    };
  }, [onClose]);

  return (
    <div ref={panel} className="fixed inset-0 z-[2000] flex flex-col bg-slate-900/60 p-3 backdrop-blur-sm">
      <div className="mb-2 flex flex-wrap items-center gap-2 text-white">
        <h2 className="ml-auto text-sm font-semibold">پیش‌نمایش رندرشده در سرور</h2>
        {busy && <span className="text-xs text-slate-300" role="status">در حال رندر…</span>}
        <div className="flex gap-1" role="group" aria-label="عرض پیش‌نمایش">
          {(Object.keys(WIDTHS) as Width[]).map((item) => (
            <button key={item} type="button" aria-pressed={width === item} onClick={() => setWidth(item)}
              className={`rounded-md px-2 py-1 text-xs font-medium ${width === item ? 'bg-white text-slate-900' : 'bg-white/10 text-white'}`}>
              {deviceLabel(item)}
            </button>
          ))}
        </div>
        <a href="#" onClick={(event) => { event.preventDefault(); }} className="hidden" aria-hidden="true" />
        <button type="button" onClick={onClose}
          className="rounded-md bg-white px-3 py-1 text-xs font-semibold text-slate-900">بستن پیش‌نمایش</button>
      </div>
      {error && (
        <div role="alert" className="mb-2 rounded-lg bg-red-100 px-3 py-2 text-sm text-red-800">{error}</div>
      )}
      <div data-preview-frame className="mx-auto min-h-0 flex-1 overflow-hidden rounded-xl bg-white shadow-2xl transition-all"
        style={{ width: WIDTHS[width], maxWidth: '100%' }}>
        <iframe
          title="پیش‌نمایش صفحه"
          ref={iframeRef}
          srcDoc={html}
          sandbox="allow-same-origin"
          className="h-[70vh] w-full border-0"
          style={{ minHeight: '420px' }}
        />
      </div>
    </div>
  );
}
