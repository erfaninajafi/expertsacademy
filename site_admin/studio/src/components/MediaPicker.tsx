import { useMemo, useState } from 'react';
import type { رسانهآیتم } from '../نوع';
import { Modal } from './Dialogs';

interface Props {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  items: رسانهآیتم[];
  onSelect: (item: رسانهآیتم) => void;
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
  title?: string;
}

/** Searchable image library with previews and به بالاload, used by image fields. */
export function رسانهPicker({ open, onOpenتغییر, items, onSelect, onUpload, busy, title = 'انتخاب تصویر' }: Props) {
  const [query, setQuery] = useState('');
  const [alt, setAlt] = useState('');

  const visible = useMemo(() => {
    const needle = query.trim().toLowerCase();
    if (needle === '') return items;
    return items.filter((item) => `${item.original_name} ${item.alt_text}`.toLowerCase().includes(needle));
  }, [items, query]);

  return (
    <Modal
      open={open}
      onOpenتغییر={onOpenتغییر}
      title={title}
      description="یک تصویر بارگذاری‌شده انتخاب کنید. متن جایگزین همراه ارجاع می‌ماند و قابل ویرایش است."
      wide
    >
      <div className="grid gap-4">
        <label className="text-xs font-medium text-slate-600">
          جست‌وجوی تصاویر
          <input
            type="search"
            className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm"
            placeholder="نام یا متن جایگزین"
            value={query}
            onتغییر={(event) => setQuery(event.target.value)}
          />
        </label>

        <ul aria-label="تصاویر موجود" className="grid max-h-72 grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3">
          {visible.map((item) => (
            <li key={item.id}>
              <button
                type="button"
                aria-label={`Use ${item.original_name}`}
                onClick={() => {
                  onSelect(item);
                  onOpenتغییر(false);
                }}
                className="w-کامل overflow-hidden rounded-lg border border-slate-200 bg-white text-left transition hover:border-indigo-400 hover:shadow-sm"
              >
                <img src={item.url} alt="" loading="lazy" className="h-24 w-کامل bg-slate-100 object-cover" />
                <span className="block truncate px-2 pt-1.5 text-xs font-medium text-slate-800">{item.original_name}</span>
                <span className="block truncate px-2 pb-1.5 text-[11px] text-slate-600">
                  {item.width && item.height ? `${item.width}×${item.height}` : item.mime_type}
                </span>
              </button>
            </li>
          ))}
          {visible.length === 0 && (
            <li className="col-span-کامل py-4 text-sm text-slate-600">
              {items.length === 0 ? 'هنوز تصویری بارگذاری نشده است.' : 'تصویری با این جست‌وجو پیدا نشد.'}
            </li>
          )}
        </ul>

        {onUpload && (
          <div className="rounded-xl border border-dashed border-slate-300 p-3">
            <label className="text-xs font-medium text-slate-600">
              بارگذاری تصویر
              <input
                type="file"
                accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml"
                className="mt-1 block w-کامل text-xs"
                onتغییر={(event) => {
                  const file = event.target.files?.[0];
                  if (file) void onUpload(file, alt);
                  event.target.value = '';
                }}
              />
            </label>
            <label className="mt-2 block text-xs font-medium text-slate-600">
              متن جایگزین
              <input
                className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm"
                maxLength={300}
                value={alt}
                onتغییر={(event) => setAlt(event.target.value)}
              />
            </label>
            {busy && <p role="status" className="mt-2 text-xs text-indigo-700">در حال بارگذاری…</p>}
          </div>
        )}
      </div>
    </Modal>
  );
}
