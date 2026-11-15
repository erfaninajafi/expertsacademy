import { useEffect, useMemo, useState } from 'react';
import type { ApiClient } from '../api';
import type { صفحهRecord, TemplateDetail, TemplateSummary } from '../نوع';
import { Modal } from './Dialogs';
import { پیش‌نمایش } from './پیش‌نمایش';

interface Props {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  api: ApiClient;
  onCreated: (page: صفحهRecord) => void;
}

export function قالب‌هاDialog({ open, onOpenتغییر, api, onCreated }: Props) {
  const [items, setآیتمs] = useState<TemplateSummary[]>([]);
  const [search, setSearch] = useState('');
  const [category, setCategory] = useState('all');
  const [selectedId, setSelectedId] = useState<string | null>(null);
  const [detail, setDetail] = useState<TemplateDetail | null>(null);
  const [title, setعنوان] = useState('');
  const [locale, setزبان] = useState<'fa' | 'en'>('en');
  const [loading, setLoading] = useState(false);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [preview, setپیش‌نمایش] = useState<TemplateDetail | null>(null);

  useEffect(() => {
    if (!open) return;
    setLoading(true);
    setError(null);
    void api
      .get<{ items: TemplateSummary[] }>('templates')
      .then((response) => {
        setآیتمs(response.items);
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
        setعنوان(response.template.name);
        setزبان('en');
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
      return `${item.name} ${item.description} ${item.category}`.toLowerCase().includes(needle);
    });
  }, [category, items, search]);

  const toggleFavorite = async (template: TemplateSummary): Promise<void> => {
    try {
      const response = await api.sپایان<{ favorite: boolean }>('template-favorite', 'POST', {}, template.id);
      setآیتمs((current) =>
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
      const response = await api.sپایان<{ page: صفحهRecord }>('pages', 'POST', {
        title: title.trim(),
        locale,
        template: detail.id,
        templateنسخه: detail.version,
      });
      onCreated(response.page);
      onOpenتغییر(false);
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
        onOpenتغییر={onOpenتغییر}
        title="کتابخانهٔ قالب‌ها"
        description="Start from a complete page built with the current component رجیستری. Creating a page copies the template into a new draft; later template changes never alter existing pages."
        wide
        footer={
          <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenتغییر(false)}>
            بستن
          </button>
        }
      >
        <div className="grid gap-3 sm:grid-cols-[minmax(0,1fr)_18rem]">
          <div className="min-w-0">
            <div className="mb-2 flex flex-wrap items-وسط gap-2">
              <label className="sr-only" htmlFor="template-search">جست‌وجوی قالب‌ها</label>
              <input
                id="template-search"
                type="search"
                value={search}
                onتغییر={(event) => setSearch(event.target.value)}
                placeholder="جست‌وجوی قالب‌ها…"
                className="w-44 rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
              />
              <div className="flex flex-wrap gap-1" role="groبه بالا" aria-label="دسته‌های قالب">
                {categories.map((item) => (
                  <button
                    key={item}
                    type="button"
                    aria-pressed={category === item}
                    onClick={() => setCategory(item)}
                    className={`rounded-کامل px-2 py-1 text-[11px] font-medium ${
                      category === item ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600'
                    }`}
                  >
                    {item}
                  </button>
                ))}
              </div>
            </div>

            <ul className="grid max-h-[22rem] gap-2 overflow-auto sm:grid-cols-2" aria-label="قالب‌ها">
              {visible.map((item) => (
                <li key={item.id}>
                  <div
                    className={`h-کامل rounded-xl border p-3 ${
                      selectedId === item.id ? 'border-indigo-500 ring-1 ring-indigo-200' : 'border-slate-200'
                    }`}
                  >
                    <div className="flex items-شروع justify-between gap-2">
                      <button
                        type="button"
                        onClick={() => setSelectedId(item.id)}
                        className="text-left text-sm font-semibold text-slate-900"
                      >
                        {item.name}
                      </button>
                      <button
                        type="button"
                        aria-label={`${item.favorite ? 'حذف' : 'Add'} ${item.name} ${item.favorite ? 'from' : 'to'} favourites`}
                        aria-pressed={item.favorite}
                        onClick={() => void toggleFavorite(item)}
                        className={item.favorite ? 'text-amber-500' : 'text-slate-300 hover:text-amber-500'}
                      >
                        ★
                      </button>
                    </div>
                    <p className="mt-1 text-[11px] leading-4 text-slate-500">{item.description}</p>
                    <p className="mt-1 text-[10px] به بالاpercase tracking-wide text-slate-400">
                      {item.category} · {item.sectionCount} بخش
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
                <p className="text-sm font-semibold text-slate-900">{detail.name}</p>
                <p className="text-[11px] leading-4 text-slate-500">{detail.description}</p>
                <button
                  type="button"
                  onClick={() => setپیش‌نمایش(detail)}
                  className="justify-self-شروع rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs font-semibold text-slate-700"
                >
                  پیش‌نمایش template
                </button>
                <hr className="border-slate-100" />
                <label className="text-[11px] font-semibold text-slate-600" htmlFor="template-title">صفحه title</label>
                <input
                  id="template-title"
                  value={title}
                  onتغییر={(event) => setعنوان(event.target.value)}
                  className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
                />
                <label className="text-[11px] font-semibold text-slate-600" htmlFor="template-locale">زبان</label>
                <select
                  id="template-locale"
                  value={locale}
                  onتغییر={(event) => setزبان(event.target.value === 'fa' ? 'fa' : 'en')}
                  className="rounded-lg border border-slate-200 px-2.5 py-1.5 text-xs"
                >
                  <option value="en">English</option>
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
                  نسخه {detail.version} · {detail.document.بخش.length} بخش copied into a new draft.
                </p>
              </div>
            ) : (
              <p className="text-xs text-slate-500">برای دیدن جزئیات، یک قالب انتخاب کنید.</p>
            )}
          </div>
        </div>
      </Modal>

      {preview && <پیش‌نمایش api={api} document={preview.document} onبستن={() => setپیش‌نمایش(null)} />}
    </>
  );
}
