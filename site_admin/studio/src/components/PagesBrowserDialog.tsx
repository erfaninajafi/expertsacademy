import { useEffect, useMemo, useState } from 'react';
import type { ApiClient } from '../api';
import { statusLabel } from '../i18n';
import type { PageSummary } from '../types';
import { Modal } from './Dialogs';

interface Props {
  api: ApiClient;
  open: boolean;
  onOpenChange: (open: boolean) => void;
  activeId: string | null;
  onSelect: (page: PageSummary) => void;
}

type PageSort = 'recent' | 'oldest' | 'title_asc' | 'title_desc';

const STATUS_OPTIONS = [
  ['all', 'صفحات فعال'],
  ['everything', 'همه صفحات'],
  ['draft', 'پیش‌نویس'],
  ['published', 'منتشرشده'],
  ['scheduled', 'زمان‌بندی‌شده'],
  ['unpublished', 'منتشرنشده'],
  ['archived', 'بایگانی‌شده'],
] as const;

const SORT_OPTIONS: Array<{ value: PageSort; label: string }> = [
  { value: 'recent', label: 'آخرین ویرایش (جدیدترین)' },
  { value: 'oldest', label: 'آخرین ویرایش (قدیمی‌ترین)' },
  { value: 'title_asc', label: 'عنوان (الف تا ی)' },
  { value: 'title_desc', label: 'عنوان (ی تا الف)' },
];

function searchText(value: string): string {
  return value
    .normalize('NFKC')
    .toLocaleLowerCase()
    .replace(/ي/g, 'ی')
    .replace(/ك/g, 'ک')
    .trim();
}

export function PagesBrowserDialog({ api, open, onOpenChange, activeId, onSelect }: Props) {
  const [pages, setPages] = useState<PageSummary[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [query, setQuery] = useState('');
  const [locale, setLocale] = useState<'all' | 'fa' | 'en'>('all');
  const [status, setStatus] = useState('all');
  const [sort, setSort] = useState<PageSort>('recent');

  useEffect(() => {
    if (!open) return;
    let cancelled = false;
    setLoading(true);
    setError(null);
    setPages([]);

    const loadAll = async () => {
      let items: PageSummary[] = [];
      let offset = 0;
      while (!cancelled) {
        // The existing API caps each request at 100, so fetch all pages in batches.
        const batch = await api.get<{ pages: PageSummary[]; total: number }>('pages', {
          limit: '100',
          offset: String(offset),
        });
        if (batch.pages.length === 0) break;
        items = [...items, ...batch.pages];
        offset += batch.pages.length;
        if (offset >= batch.total) break;
      }
      if (!cancelled) setPages(items);
    };

    void loadAll()
      .catch((reason: unknown) => {
        if (!cancelled) setError(reason instanceof Error ? reason.message : 'دریافت فهرست صفحات انجام نشد.');
      })
      .finally(() => { if (!cancelled) setLoading(false); });

    return () => { cancelled = true; };
  }, [api, open]);

  const shown = useMemo(() => {
    const needle = searchText(query);
    const matched = pages.filter((page) => {
      if (locale !== 'all' && page.locale !== locale) return false;
      if (status === 'all' && page.status === 'archived') return false;
      if (status !== 'all' && status !== 'everything' && page.status !== status) return false;
      if (needle) {
        const haystack = searchText([
          page.title,
          page.slug ?? '',
          page.fullPath ?? '',
        ].join(' '));
        if (!haystack.includes(needle)) return false;
      }
      return true;
    });

    const collator = new Intl.Collator('fa', { numeric: true, sensitivity: 'base' });
    return matched.sort((left, right) => {
      if (sort === 'title_asc') return collator.compare(left.title, right.title);
      if (sort === 'title_desc') return collator.compare(right.title, left.title);
      const byDate = left.updatedAt.localeCompare(right.updatedAt);
      if (byDate !== 0) return sort === 'recent' ? -byDate : byDate;
      return left.id.localeCompare(right.id);
    });
  }, [pages, query, locale, status, sort]);

  const field = 'w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800';

  return (
    <Modal
      open={open}
      onOpenChange={onOpenChange}
      title="فهرست صفحات Page Studio"
      description="صفحه را بر اساس عنوان، آدرس، زبان یا وضعیت پیدا و برای ویرایش انتخاب کنید."
      wide
    >
      <div dir="rtl" className="space-y-3">
        <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
          <label className="space-y-1 lg:col-span-2">
            <span className="text-xs font-semibold text-slate-700">جست‌وجوی صفحه</span>
            <input
              type="search"
              className={field}
              placeholder="عنوان یا آدرس صفحه…"
              value={query}
              onChange={(event) => setQuery(event.target.value)}
              autoComplete="off"
            />
          </label>
          <label className="space-y-1">
            <span className="text-xs font-semibold text-slate-700">زبان صفحه</span>
            <select className={field} value={locale} onChange={(event) => setLocale(event.target.value as typeof locale)}>
              <option value="all">هر دو زبان</option>
              <option value="fa">فارسی</option>
              <option value="en">انگلیسی</option>
            </select>
          </label>
          <label className="space-y-1">
            <span className="text-xs font-semibold text-slate-700">وضعیت انتشار</span>
            <select className={field} value={status} onChange={(event) => setStatus(event.target.value)}>
              {STATUS_OPTIONS.map(([value, label]) => <option key={value} value={value}>{label}</option>)}
            </select>
          </label>
        </div>

        <div className="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-2">
          <span className="text-xs font-medium text-slate-600" role="status" aria-live="polite">
            {loading ? 'در حال دریافت همه صفحات…' : error ? 'بارگذاری ناموفق' : `${shown.length} صفحه از مجموع ${pages.length} صفحه`}
          </span>
          <label className="flex items-center gap-2 text-xs text-slate-700">
            <span>مرتب‌سازی</span>
            <select className="rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-xs" value={sort}
              onChange={(event) => setSort(event.target.value as PageSort)}>
              {SORT_OPTIONS.map((item) => <option key={item.value} value={item.value}>{item.label}</option>)}
            </select>
          </label>
        </div>

        {error && <p role="alert" className="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800">{error}</p>}
        {!loading && !error && shown.length === 0 && (
          <div className="rounded-xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-600">
            صفحه‌ای مطابق جست‌وجو و فیلترهای انتخاب‌شده یافت نشد.
          </div>
        )}
        {!error && shown.length > 0 && (
          <div className="max-h-[52vh] space-y-2 overflow-y-auto pb-1" role="list" aria-label="صفحات قابل ویرایش">
            {shown.map((item) => (
              <div role="listitem" key={item.id}>
                <button
                  type="button"
                  onClick={() => onSelect(item)}
                  aria-current={item.id === activeId ? 'page' : undefined}
                  className={`w-full rounded-xl border p-3 text-right transition-colors hover:border-indigo-400 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500 ${
                    item.id === activeId ? 'border-indigo-400 bg-indigo-50' : 'border-slate-200 bg-white'
                  }`}
                >
                  <span className="flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                    <span className="min-w-0 text-sm font-semibold text-slate-900">{item.title}</span>
                    <span className="flex flex-wrap items-center gap-1.5 text-[11px]">
                      <span className="rounded-full bg-slate-100 px-2 py-0.5 font-medium text-slate-700">{item.locale === 'fa' ? 'فارسی' : 'انگلیسی'}</span>
                      <span className="rounded-full bg-emerald-50 px-2 py-0.5 font-medium text-emerald-800">{statusLabel(item.status)}</span>
                      {item.id === activeId && <span className="rounded-full bg-indigo-100 px-2 py-0.5 font-medium text-indigo-800">در حال ویرایش</span>}
                    </span>
                  </span>
                  <span className="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-[11px] text-slate-500">
                    <span dir="ltr" className="break-all">{item.fullPath || item.slug || 'بدون آدرس عمومی'}</span>
                    <span>آخرین ویرایش: {item.updatedAt.replace('T', ' ').slice(0, 16)}</span>
                  </span>
                </button>
              </div>
            ))}
          </div>
        )}
        <p className="text-[11px] text-slate-500">انتخاب یک صفحه، همان صفحه را در ویرایشگر فعلی باز می‌کند. اگر تغییرات ذخیره‌نشده داشته باشید، قبل از تغییر صفحه تأیید گرفته می‌شود.</p>
      </div>
    </Modal>
  );
}
