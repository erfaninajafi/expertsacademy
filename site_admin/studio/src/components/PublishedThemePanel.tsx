import { useEffect, useState } from 'react';
import type { ApiClient } from '../api';
import { useEditor } from '../store';
import type { PageDocument, ThemeRecord, ThemeTokens } from '../types';

interface Props {
  api: ApiClient;
  document: PageDocument;
  canEdit: boolean;
}

/** Read-only theme discovery. Applying affects only the current unsaved page document. */
export function PublishedThemePanel({ api, document, canEdit }: Props) {
  const [active, setActive] = useState<ThemeRecord | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [notice, setNotice] = useState<string | null>(null);
  const applyTheme = useEditor((state) => state.applyTheme);

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    setError(null);
    setNotice(null);
    setActive(null);
    void api.get<{ items: ThemeRecord[] }>('themes')
      .then((response) => {
        if (!mounted) return;
        const activeTheme = response.items.find((theme) =>
          theme.locale === document.locale &&
          theme.status === 'active' &&
          (theme.is_default === 1 || theme.is_default === '1'),
        );
        setActive(activeTheme ?? null);
      })
      .catch((reason: unknown) => {
        if (!mounted) return;
        setError(reason instanceof Error ? reason.message : 'دریافت پوسته فعال ممکن نشد.');
      })
      .finally(() => { if (mounted) setLoading(false); });
    return () => { mounted = false; };
  }, [api, document.locale]);

  let parsedTokens: ThemeTokens | null = null;
  if (active) {
    try {
      const parsed: unknown = JSON.parse(active.document);
      if (parsed && typeof parsed === 'object' && !Array.isArray(parsed) &&
          'colors' in parsed && 'typography' in parsed && 'layout' in parsed) {
        parsedTokens = parsed as ThemeTokens;
      }
    } catch {
      // An unreadable token document must never be applied.
    }
  }

  const upToDate = active !== null && parsedTokens !== null &&
    document.theme.id === active.tokens_id &&
    document.theme.version === active.tokens_version &&
    JSON.stringify(document.theme.tokens) === JSON.stringify(parsedTokens);

  const apply = (): void => {
    if (!active || !parsedTokens || !canEdit || upToDate) return;
    const currentDraft = useEditor.getState().draft;
    if (!currentDraft || currentDraft.locale !== active.locale) {
      setError('صفحه در حال ویرایش تغییر کرده است؛ پوسته را دوباره بارگذاری کنید.');
      return;
    }
    if (!window.confirm(
      'رنگ‌ها، فونت‌ها و سایر تنظیمات طراحی این صفحه با پوسته منتشرشده جایگزین می‌شوند. ' +
      'محتوای صفحه تغییر نمی‌کند و سایت عمومی تا انتشار صفحه تغییری نخواهد کرد. ادامه می‌دهید؟',
    )) return;
    applyTheme({
      id: active.tokens_id,
      version: active.tokens_version,
      tokens: structuredClone(parsedTokens),
    });
    setError(null);
    setNotice('پوسته روی پیش‌نویس اعمال شد. تغییرات را ذخیره و پیش از انتشار، پیش‌نمایش کنید.');
  };

  return (
    <section data-ea-active-design-theme="stage15" aria-label="پوسته فعال سیستم طراحی"
      className="mb-2 rounded-lg border border-indigo-100 bg-indigo-50/50 p-2.5">
      <div className="flex flex-wrap items-start justify-between gap-2">
        <div className="min-w-0 flex-1">
          <p className="m-0 font-semibold text-slate-800" style={{ fontSize: 12, lineHeight: '20px' }}>پوسته فعال سیستم طراحی</p>
          <p className="m-0 mt-1 text-slate-600" style={{ fontSize: 11, lineHeight: '18px' }}>
            {loading ? 'در حال بررسی…' : error ? 'بارگذاری ناموفق' : !active ?
              `برای زبان ${document.locale.toUpperCase()} پوسته منتشرشده‌ای موجود نیست.` :
              `${active.name} · نسخه ${active.tokens_version} · ${document.locale.toUpperCase()}`}
          </p>
        </div>
        <button type="button" onClick={apply}
          disabled={!canEdit || loading || !active || !parsedTokens || upToDate}
          title="فقط روی پیش‌نویس این صفحه اعمال می‌شود؛ انتشار جداگانه است."
          className="rounded-md border border-indigo-200 bg-white px-2.5 py-1 font-medium text-indigo-700 hover:bg-indigo-50 disabled:cursor-not-allowed disabled:opacity-50"
          style={{ fontSize: 11, lineHeight: '20px' }}>
          {upToDate ? 'پوسته فعلی اعمال شده' : 'اعمال روی این صفحه'}
        </button>
      </div>
      {active && !parsedTokens && (
        <p role="alert" className="mt-1 text-red-700" style={{ fontSize: 11 }}>سند پوسته معتبر نیست؛ امکان اعمال وجود ندارد.</p>
      )}
      {error && <p role="alert" className="mt-1 text-red-700" style={{ fontSize: 11 }}>{error}</p>}
      {notice && <p role="status" className="mt-1 text-emerald-700" style={{ fontSize: 11 }}>{notice}</p>}
    </section>
  );
}
