import { templateCategoryLabel, templateDescription, templateName } from '../i18n';
import { useEffect, useMemo, useState } from 'react';
import type { ApiClient } from '../api';
import type { PageRecord, TemplateDetail, TemplateSummary } from '../types';
import { Modal } from './Dialogs';
import { Preview } from './Preview';

interface Props {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  api: ApiClient;
  onCreated: (page: PageRecord) => void;
}

export function TemplatesDialog({ open, onOpenChange, api, onCreated }: Props) {
  const [items, setItems] = useState<TemplateSummary[]>([]);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('all');
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [detail, setDetail] = useState<TemplateDetail | null>(null);
  const [title, setTitle] = useState('');
  const [locale, setLocale] = useState<'fa' | 'en'>('en');
  const [loading, setLoading] = useState(false);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [preview, setPreview] = useState<TemplateDetail | null>(null);

  useEffect(() => {
    if (!open) return;
    setLoading(true);
    setError(null);
    void api
      .get<{ items: TemplateSummary[] }>('templates')
      .then((response) => {
        setItems(response.items);
        setSelectedId(response.items.find((item) => item.favorite)?.id ?? response.items[0]?.id ?? null);
      })
      .catch((reason: unknown) => setError(reason instanceof Error ? reason.message : 'بارگذاری قالب‌ها ممکن نشد.'))
      .finally(() => setLoading(false));
  }, [api, open]);

  useEffect(() => {
    if (!open || !selectedId) return;
    let alive = true;
    setLoading(true);
    setError(null);
    void api
      .get<{ template: TemplateDetail }>('template', { id: selectedId })
      .then((response) => {
        if (!alive) return;
        setDetail(response.template);
        setTitle(response.template.name);
        setLocale('en');
      })
      .catch((reason: unknown) => {
        if (alive) setError(reason instanceof Error ? reason.message : 'بارگذاری قالب ممکن نشد.');
      })
      .finally(() => {
        if (alive) setLoading(false);
      });
    return () => {
      alive = false;
    };
  }, [api, open, selectedId]);

  const categories = useMemo(
    () => ['all', ...Array.from(new Set(items.map((item) => item.category))).sort()],
    [items],
  );

  const visible = useMemo(() => {
    const needle = search.trim().toLowerCase();
    return items.filter((item) => {
      if (category !== 'all' && item.category !== category) return false;
      if (needle === '') return true;
      return `${templateName(item.name)} ${templateDescription(item.name, item.description)} ${item.category}`.toLowerCase().includes(needle);
    });
  }, [category, items, search]);

  const toggleFavorite = async (template: TemplateSummary): Promise<void> => {
    try {
      const response = await api.send<{ favorite: boolean }>('template-favorite', 'POST', {}, template.id);
      setItems((current) =>
        current.map((item) => (item.id === template.id ? { ...item, favorite: response.favorite } : item)),
      );
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : 'به‌روزرسانی علاقه‌مندی ممکن نشد.');
    }
  };

  const create = async (): Promise<void> => {
    if (!detail || title.trim() === '') return;
    setBusy(true);
    setError(null);
    try {
      const response = await api.send<{ page: PageRecord }>('pages', 'POST', {
        title: title.trim(),
        locale,
        template: detail.id,
        templateVersion: detail.version,
      });
      onCreated(response.page);
      onOpenChange(false);
    } catch (reason) {
      setError(reason instanceof Error ? reason.message : 'ساخت صفحه انجام نشد.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <>
      <Modal
        open={open}
        onOpenChange={onOpenChange}
        title="کتابخانهٔ قالب‌ها"
        description="از یک صفحهٔ کامل بر پایهٔ رجیستری فعلی شروع کنید. ساخت صفحه، قالب را در پیش‌نویس جدید کپی می‌کند و تغییرات بعدی قالب روی صفحات موجود اثری ندارد."
        wide
        footer={
          <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenChange(false)}>بستن</button>
        }
      >
        <div className="grid gap-3 sm:grid-cols-[minmax(0,1fr)_18rem]">
          <div className="min-w-0">
            <div className="mb-2 flex flex-wrap items-center gap-2">
              <label className="sr-only" htmlFor="template-search">جست‌وجوی قالب‌ها</label>
              <input
                id="template-search"
                type="search"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="جست‌وجوی قالب‌ها…"
                className="w-44 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
              />
              <div className="flex flex-wrap gap-1" role="group" aria-label="دسته‌های قالب">
                {categories.map((item) => (
                  <button
                    key={item}
                    type="button"
                    aria-pressed={category === item}
                    onClick={() => setCategory(item)}
                    className={`rounded-full px-2 py-1 text-[11px] font-medium ${
                      category === item ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600'
                    }`}
                  >
                    {templateCategoryLabel(item)}
                  </button>
                ))}
              </div>
            </div>

            <ul className="grid max-h-[22rem] gap-2 overflow-auto sm:grid-cols-2" aria-label="قالب‌ها">
              {visible.map((item) => (
                <li key={item.id}>
                  <div
                    className={`h-full rounded-xl border p-3 ${
                      selectedId === item.id ? 'border-indigo-500 ring-1 ring-indigo-200' : 'border-slate-200'
                    }`}
                  >
                    <div className="flex items-start justify-between gap-2">
                      <button
                        type="button"
                        onClick={() => setSelectedId(item.id)}
                        className="text-right text-sm font-semibold text-slate-900"
                      >
                        {templateName(item.name)}
                      </button>
                      <button
                        type="button"
                        aria-label={`${item.favorite ? 'حذف' : 'Add'} ${templateName(item.name)} ${item.favorite ? 'from' : 'to'} favourites`}
                        aria-pressed={item.favorite}
                        onClick={() => void toggleFavorite(item)}
                        className={item.favorite ? 'text-amber-500' : 'text-slate-300 hover:text-amber-500'}
                      >
                        ★
                      </button>
                    </div>
                    <p className="mt-1 text-[11px] leading-4 text-slate-500">{templateDescription(item.name, item.description)}</p>
                    <p className="mt-1 text-[10px] uppercase tracking-wide text-slate-400">
                      {templateCategoryLabel(item.category)} · {item.sectionCount} بخش
                    </p>
                  </div>
                </li>
              ))}
              {visible.length === 0 && <li className="text-xs text-slate-500">قالبی با این جست‌وجو پیدا نشد.</li>}
            </ul>
            {loading && <p className="mt-2 text-xs text-slate-500" role="status">در حال بارگذاری قالب‌ها…</p>}
          </div>

          <div className="rounded-xl border border-slate-200 p-3">
            {error && <p className="mb-2 text-xs text-red-700" role="alert">{error}</p>}
            {detail ? (
              <div className="grid gap-2">
                <p className="text-sm font-semibold text-slate-900">{templateName(detail.name)}</p>
                <p className="text-[11px] leading-4 text-slate-500">{templateDescription(detail.name, detail.description)}</p>
                <button
                  type="button"
                  onClick={() => setPreview(detail)}
                  className="justify-self-start rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700"
                >پیش‌نمایش قالب</button>
                <hr className="border-slate-100" />
                <label className="text-[11px] font-semibold text-slate-600" htmlFor="template-title">عنوان صفحه</label>
                <input
                  id="template-title"
                  value={title}
                  onChange={(event) => setTitle(event.target.value)}
                  className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
                />
                <label className="text-[11px] font-semibold text-slate-600" htmlFor="template-locale">زبان</label>
                <select
                  id="template-locale"
                  value={locale}
                  onChange={(event) => setLocale(event.target.value === 'fa' ? 'fa' : 'en')}
                  className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
                >
                  <option value="en">انگلیسی</option>
                  <option value="fa">فارسی</option>
                </select>
                <button
                  type="button"
                  disabled={busy || title.trim() === ''}
                  onClick={() => void create()}
                  className="mt-1 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40"
                >
                  {busy ? 'در حال ساخت…' : 'ساخت صفحه از قالب'}
                </button>
                <p className="text-[10px] leading-4 text-slate-400">
                  نسخهٔ {detail.version} · {detail.document.sections.length} بخش در پیش‌نویس جدید کپی شد.
                </p>
              </div>
            ) : (
              <p className="text-xs text-slate-500">برای دیدن جزئیات، یک قالب انتخاب کنید.</p>
            )}
          </div>
        </div>
      </Modal>

      {preview && <Preview api={api} document={preview.document} onClose={() => setPreview(null)} />}
    </>
  );
}
