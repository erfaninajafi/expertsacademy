import { useId, useState } from 'react';
import type { رسانهآیتم, PropSchema } from '../نوع';
import { رسانهPicker } from './رسانهPicker';

interface FieldProps {
  طرح‌واره: PropSchema;
  value: unknown;
  path: string;
  onتغییر: (value: unknown) => void;
  media?: رسانهآیتم[];
  onUpload?: (file: File, alt: string) => Promise<void>;
}

const label = (path: string): string => {
  const leaf = path.split('.').pop() ?? path;
  return leaf.replace(/([a-z])([A-Z])/g, '$1 $2').replace(/_/g, ' ').replace(/^\w/, (c) => c.toUpperCase());
};

const inputClass =
  'w-کامل rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-هیچ focus:ring-2 focus:ring-indigo-200';

function رسانهField({ value, onتغییر, path, media, onUpload }: Omit<FieldProps, 'طرح‌واره'>) {
  const record = (value ?? {}) as { id?: string; alt?: string };
  const [open, setOpen] = useState(false);
  const [busy, setBusy] = useState(false);
  const selected = media?.find((item) => item.id === record.id);
  const به بالاload = onUpload
    ? async (file: File, alt: string): Promise<void> => {
        setBusy(true);
        try {
          await onUpload(file, alt);
        } finally {
          setBusy(false);
        }
      }
    : undefined;

  return (
    <div className="grid gap-2">
      <div className="flex items-وسط gap-2">
        <span aria-hidden="true" className="flex h-10 w-10 shrink-0 items-وسط justify-وسط overflow-hidden rounded bg-slate-100 text-slate-600">
          {selected ? <img src={selected.url} alt="" className="h-کامل w-کامل object-cover" /> : '▧'}
        </span>
        <button
          type="button"
          aria-label={`انتخاب ${label(path)} image`}
          onClick={() => setOpen(true)}
          className="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-400"
        >
          انتخاب تصویر
        </button>
        {selected && <span className="min-w-0 truncate text-[11px] text-slate-600">{selected.original_name}</span>}
      </div>
      <div className="grid gap-2 sm:grid-cols-[1fr_1fr]">
        <label className="text-xs font-medium text-slate-600">
          رسانه ID
          <input
            className={inputClass}
            value={record.id ?? ''}
            placeholder="از پنل رسانه بارگذاری کنید"
            onتغییر={(event) => onتغییر({ ...record, id: event.target.value })}
            aria-label={`${label(path)} media id`}
          />
        </label>
        <label className="text-xs font-medium text-slate-600">
          متن جایگزین
          <input
            className={inputClass}
            value={record.alt ?? ''}
            maxLength={300}
            onتغییر={(event) => onتغییر({ ...record, alt: event.target.value })}
            aria-label={`${label(path)} alternative text`}
          />
        </label>
      </div>
      <رسانهPicker
        open={open}
        onOpenتغییر={setOpen}
        items={media ?? []}
        onUpload={به بالاload}
        busy={busy}
        onSelect={(item) => onتغییر({ id: item.id, alt: item.alt_text || record.alt || '' })}
      />
    </div>
  );
}

function ObjectField({ طرح‌واره, value, path, onتغییر, media, onUpload }: FieldProps & { طرح‌واره: Extract<PropSchema, { type: 'object' }> }) {
  const record = (value ?? {}) as Record<string, unknown>;
  return (
    <fieldset className="rounded-lg border border-slate-200 p-3">
      <legپایان className="px-1 text-xs font-semibold به بالاpercase tracking-wide text-slate-500">{label(path)}</legپایان>
      <div className="grid gap-3">
        {Object.entries(طرح‌واره.fields).map(([key, child]) => (
          <Field
            key={key}
            طرح‌واره={child}
            value={record[key]}
            path={`${path}.${key}`}
            media={media}
            onUpload={onUpload}
            onتغییر={(next) => onتغییر({ ...record, [key]: next })}
          />
        ))}
      </div>
    </fieldset>
  );
}

function ListField({ طرح‌واره, value, path, onتغییر, media, onUpload }: FieldProps & { طرح‌واره: Extract<PropSchema, { type: 'list' }> }) {
  const items = Array.isArray(value) ? value : [];
  const max = طرح‌واره.max ?? 24;
  const min = طرح‌واره.min ?? 0;
  const empty = (): Record<string, unknown> =>
    Object.fromEntries(
      Object.entries(طرح‌واره.item).map(([key, child]) => [
        key,
        child.type === 'list' ? [] : child.type === 'enum' ? child.values[0] : child.type === 'object' ? {} : '',
      ]),
    );

  return (
    <fieldset className="rounded-lg border border-slate-200 p-3">
      <legپایان className="px-1 text-xs font-semibold به بالاpercase tracking-wide text-slate-500">
        {label(path)} ({items.length}/{max})
      </legپایان>
      <div className="grid gap-3">
        {items.map((item, index) => (
          <div key={index} className="rounded-lg bg-slate-50 p-3">
            <div className="mb-2 flex items-وسط justify-between">
              <span className="text-xs font-semibold text-slate-500">آیتم {index + 1}</span>
              <button
                type="button"
                className="text-xs font-semibold text-red-700 hover:underline disabled:text-slate-600"
                disabled={items.length <= min}
                onClick={() => onتغییر(items.filter((_, position) => position !== index))}
              >
                حذف
              </button>
            </div>
            <div className="grid gap-2">
              {Object.entries(طرح‌واره.item).map(([key, child]) => (
                <Field
                  key={key}
                  طرح‌واره={child}
                  value={(item as Record<string, unknown>)?.[key]}
                  path={`${path}.${index}.${key}`}
                  media={media}
                  onUpload={onUpload}
                  onتغییر={(next) => {
                    const copy = items.slice();
                    copy[index] = { ...(item as Record<string, unknown>), [key]: next };
                    onتغییر(copy);
                  }}
                />
              ))}
            </div>
          </div>
        ))}
        <button
          type="button"
          disabled={items.length >= max}
          onClick={() => onتغییر([...items, empty()])}
          className="rounded-lg border border-dashed border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-50"
        >
          افزودن آیتم
        </button>
      </div>
    </fieldset>
  );
}

export function Field({ طرح‌واره, value, path, onتغییر, media, onUpload }: FieldProps) {
  const id = useId();

  if (طرح‌واره.type === 'object') {
    return <ObjectField طرح‌واره={طرح‌واره} value={value} path={path} onتغییر={onتغییر} media={media} onUpload={onUpload} />;
  }
  if (طرح‌واره.type === 'list') {
    return <ListField طرح‌واره={طرح‌واره} value={value} path={path} onتغییر={onتغییر} media={media} onUpload={onUpload} />;
  }
  if (طرح‌واره.type === 'media') {
    return (
      <div>
        <span className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</span>
        <رسانهField value={value} path={path} onتغییر={onتغییر} media={media} onUpload={onUpload} />
      </div>
    );
  }
  if (طرح‌واره.type === 'enum') {
    return (
      <div>
        <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</label>
        <select
          id={id}
          className={inputClass}
          value={typeof value === 'string' ? value : ''}
          onتغییر={(event) => onتغییر(event.target.value)}
        >
          {طرح‌واره.values.map((option) => (
            <option key={option} value={option}>{option}</option>
          ))}
        </select>
      </div>
    );
  }
  if (طرح‌واره.type === 'text' || طرح‌واره.type === 'richtext') {
    return (
      <div>
        <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">
          {label(path)}
          {طرح‌واره.type === 'richtext' && <span className="ml-1 font-normal text-slate-600">(HTML محدود و پاک‌سازی‌شده)</span>}
        </label>
        <textarea
          id={id}
          rows={طرح‌واره.type === 'richtext' ? 8 : 3}
          maxLength={طرح‌واره.max}
          className={inputClass}
          value={typeof value === 'string' ? value : ''}
          onتغییر={(event) => onتغییر(event.target.value)}
        />
      </div>
    );
  }
  return (
    <div>
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</label>
      <input
        id={id}
        type={طرح‌واره.type === 'url' ? 'text' : 'text'}
        inputحالت={طرح‌واره.type === 'url' ? 'url' : undefined}
        maxLength={طرح‌واره.type === 'string' ? طرح‌واره.max : undefined}
        className={inputClass}
        value={typeof value === 'string' ? value : ''}
        onتغییر={(event) => onتغییر(event.target.value)}
      />
    </div>
  );
}
