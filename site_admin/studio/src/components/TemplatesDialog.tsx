import { componentLabel, sectionLabel, templateCategoryLabel, templateDescription, templateName } from '../i18n';
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
  const [onlyFavorites, setOnlyFavorites] = useState(false);
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [detail, setDetail] = useState<TemplateDetail | null>(null);
  const [title, setTitle] = useState('');
  const [locale, setLocale] = useState<'fa' | 'en'>('en');
  const [fullPath, setFullPath] = useState('');
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
        setTitle(templateName(response.template.name));
        setLocale(response.template.document.locale === 'fa' ? 'fa' : 'en');
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
      if (onlyFavorites && !item.favorite) return false;
      if (needle === '') return true;
      return `${templateName(item.name)} ${templateDescription(item.name, item.description)} ${item.category}`.toLowerCase().includes(needle);
    });
  }, [category, items, onlyFavorites, search]);

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
    if (!detail || detail.id !== selectedId || title.trim() === '') return;
    setBusy(true);
    setError(null);
    try {
      const response = await api.send<{ page: PageRecord }>('pages', 'POST', {
        title: title.trim(),
        locale,
        template: detail.id,
        templateVersion: detail.version,
        fullPath: fullPath.trim() || null,
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
        description="قالب را بررسی کنید و از آن یک پیش‌نویس مستقل بسازید. محتوای قالب به‌صورت خودکار ترجمه نمی‌شود؛ تغییرات بعدی قالب نیز روی صفحهٔ ساخته‌شده اثری ندارد."
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
            <div data-stage18-template-library className="mb-2 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-600">
              <label className="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
                <input
                  type="checkbox"
                  checked={onlyFavorites}
                  onChange={(event) => setOnlyFavorites(event.target.checked)}
                  className="accent-indigo-600"
                />
                فقط علاقه‌مندی‌ها
              </label>
              <span aria-live="polite">{visible.length.toLocaleString('fa-IR')} قالب از {items.length.toLocaleString('fa-IR')}</span>
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
                        onClick={() => { if (selectedId !== item.id) { setDetail(null); setSelectedId(item.id); } }}
                        className="text-right text-sm font-semibold text-slate-900"
                      >
                        {templateName(item.name)}
                      </button>
                      <button
                        type="button"
                        aria-label={`${item.favorite ? 'حذف از علاقه‌مندی‌ها' : 'افزودن به علاقه‌مندی‌ها'}: ${templateName(item.name)}`}
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
              {visible.length === 0 && <li className="col-span-full rounded-xl border border-dashed border-slate-200 p-5 text-center text-xs text-slate-500">{onlyFavorites ? 'در این دسته یا جست‌وجو قالب علاقه‌مندی وجود ندارد.' : 'قالبی با این جست‌وجو پیدا نشد.'}</li>}
            </ul>
            {loading && <p className="mt-2 text-xs text-slate-500" role="status">در حال بارگذاری قالب‌ها…</p>}
          </div>

          <div className="rounded-xl border border-slate-200 p-3">
            {error && <p className="mb-2 text-xs text-red-700" role="alert">{error}</p>}
            {detail && detail.id === selectedId ? (
              <div className="grid gap-2">
                <p className="text-sm font-semibold text-slate-900">{templateName(detail.name)}</p>
                <p className="text-[11px] leading-4 text-slate-500">{templateDescription(detail.name, detail.description)}</p>
                <button
                  type="button"
                  onClick={() => setPreview(detail)}
                  className="justify-self-start rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700"
                >پیش‌نمایش قالب</button>
                <details className="rounded-lg border border-slate-200 bg-slate-50 p-2.5 text-xs text-slate-700">
                  <summary className="cursor-pointer font-semibold">
                    ساختار قالب · {detail.document.sections.length.toLocaleString('fa-IR')} بخش · {detail.document.sections.reduce((sum, section) => sum + section.components.length, 0).toLocaleString('fa-IR')} بلوک
                  </summary>
                  <ol className="mt-2 max-h-44 space-y-2 overflow-auto">
                    {detail.document.sections.map((section, index) => (
                      <li key={section.id ?? index} className="rounded-md bg-white p-2">
                        <p className="font-semibold">{(index + 1).toLocaleString('fa-IR')}. {sectionLabel(section.label || 'بخش بدون عنوان')}</p>
                        <p className="mt-1 text-[11px] leading-5 text-slate-600">
                          {section.components.length === 0
                            ? 'این بخش هنوز بلوکی ندارد.'
                            : section.components.map((component) => componentLabel(component.type, component.type)).join(' · ')}
                        </p>
                      </li>
                    ))}
                  </ol>
                </details>
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
                <label className="text-[11px] font-semibold text-slate-600" htmlFor="template-full-path">آدرس عمومی صفحه (اختیاری)</label>
                <input
                  id="template-full-path"
                  type="text"
                  dir="ltr"
                  maxLength={255}
                  value={fullPath}
                  onChange={(event) => setFullPath(event.target.value)}
                  placeholder={`/${locale}/about-academy`}
                  className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
                />
                <p className="text-[11px] leading-5 text-slate-500">صفحه به‌صورت پیش‌نویس ساخته می‌شود. آدرس عمومی اختیاری است؛ برای انتشار نیازی به کد CMS ندارید. محتوای قالب خودکار ترجمه نمی‌شود.</p>
                <button
                  type="button"
                  disabled={busy || loading || title.trim() === '' || detail.id !== selectedId}
                  onClick={() => void create()}
                  className="mt-1 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40"
                >
                  {busy ? 'در حال ساخت…' : 'ساخت صفحه از قالب'}
                </button>
                <p className="text-[10px] leading-4 text-slate-400">
                  نسخهٔ {detail.version} · {detail.document.sections.length} بخش هنگام ساخت صفحه در پیش‌نویس جدید کپی می‌شود.
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
