import { useId, useState } from 'react';
import type { MediaItem, PropSchema } from '../types';
import { MediaPicker } from './MediaPicker';
import { fieldLabel } from '../i18n';

interface FieldProps {
  schema: PropSchema;
  value: unknown;
  path: string;
  onChange: (value: unknown) => void;
  media?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
}

const label = (path: string): string => {
  const leaf = path.split('.').pop() ?? path;
  return fieldLabel(leaf.replace(/([a-z])([A-Z])/g, '$1 $2').replace(/_/g, ' '));
};

const inputClass =
  'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200';

function MediaField({ value, onChange, path, media, onUpload }: Omit<FieldProps, 'schema'>) {
  const record = (value ?? {}) as { id?: string; alt?: string };
  const [open, setOpen] = useState(false);
  const [busy, setBusy] = useState(false);
  const selected = media?.find((item) => item.id === record.id);
  const upload = onUpload
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
      <div className="flex items-center gap-2">
        <span aria-hidden="true" className="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded bg-slate-100 text-slate-600">
          {selected ? <img src={selected.url} alt="" className="h-full w-full object-cover" /> : '▧'}
        </span>
        <button
          type="button"
          aria-label={`Choose ${label(path)} image`}
          onClick={() => setOpen(true)}
          className="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-400"
        >انتخاب تصویر</button>
        {selected && <span className="min-w-0 truncate text-[11px] text-slate-600">{selected.original_name}</span>}
      </div>
      <div className="grid gap-2 sm:grid-cols-[1fr_1fr]">
        <label className="text-xs font-medium text-slate-600">شناسهٔ رسانه<input
            className={inputClass}
            value={record.id ?? ''}
            placeholder="از پنل رسانه بارگذاری کنید"
            onChange={(event) => onChange({ ...record, id: event.target.value })}
            aria-label={`${label(path)} media id`}
          />
        </label>
        <label className="text-xs font-medium text-slate-600">متن جایگزین<input
            className={inputClass}
            value={record.alt ?? ''}
            maxLength={300}
            onChange={(event) => onChange({ ...record, alt: event.target.value })}
            aria-label={`${label(path)} alternative text`}
          />
        </label>
      </div>
      <MediaPicker
        open={open}
        onOpenChange={setOpen}
        items={media ?? []}
        onUpload={upload}
        busy={busy}
        onSelect={(item) => onChange({ id: item.id, alt: item.alt_text || record.alt || '' })}
      />
    </div>
  );
}

function ObjectField({ schema, value, path, onChange, media, onUpload }: FieldProps & { schema: Extract<PropSchema, { type: 'object' }> }) {
  const record = (value ?? {}) as Record<string, unknown>;
  return (
    <fieldset className="rounded-lg border border-slate-200 p-3">
      <legend className="px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{label(path)}</legend>
      <div className="grid gap-3">
        {Object.entries(schema.fields).map(([key, child]) => (
          <Field
            key={key}
            schema={child}
            value={record[key]}
            path={`${path}.${key}`}
            media={media}
            onUpload={onUpload}
            onChange={(next) => onChange({ ...record, [key]: next })}
          />
        ))}
      </div>
    </fieldset>
  );
}

function ListField({ schema, value, path, onChange, media, onUpload }: FieldProps & { schema: Extract<PropSchema, { type: 'list' }> }) {
  const items = Array.isArray(value) ? value : [];
  const max = schema.max ?? 24;
  const min = schema.min ?? 0;
  const empty = (): Record<string, unknown> =>
    Object.fromEntries(
      Object.entries(schema.item).map(([key, child]) => [
        key,
        child.type === 'list' ? [] : child.type === 'enum' ? child.values[0] : child.type === 'object' ? {} : '',
      ]),
    );

  return (
    <fieldset className="rounded-lg border border-slate-200 p-3">
      <legend className="px-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
        {label(path)} ({items.length}/{max})
      </legend>
      <div className="grid gap-3">
        {items.map((item, index) => (
          <div key={index} className="rounded-lg bg-slate-50 p-3">
            <div className="mb-2 flex items-center justify-between">
              <span className="text-xs font-semibold text-slate-500">آیتم {index + 1}</span>
              <button
                type="button"
                className="text-xs font-semibold text-red-700 hover:underline disabled:text-slate-600"
                disabled={items.length <= min}
                onClick={() => onChange(items.filter((_, position) => position !== index))}
              >حذف</button>
            </div>
            <div className="grid gap-2">
              {Object.entries(schema.item).map(([key, child]) => (
                <Field
                  key={key}
                  schema={child}
                  value={(item as Record<string, unknown>)?.[key]}
                  path={`${path}.${index}.${key}`}
                  media={media}
                  onUpload={onUpload}
                  onChange={(next) => {
                    const copy = items.slice();
                    copy[index] = { ...(item as Record<string, unknown>), [key]: next };
                    onChange(copy);
                  }}
                />
              ))}
            </div>
          </div>
        ))}
        <button
          type="button"
          disabled={items.length >= max}
          onClick={() => onChange([...items, empty()])}
          className="rounded-lg border border-dashed border-slate-300 px-3 py-2 text-sm font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-50"
        >افزودن آیتم</button>
      </div>
    </fieldset>
  );
}

export function Field({ schema, value, path, onChange, media, onUpload }: FieldProps) {
  const id = useId();

  if (schema.type === 'object') {
    return <ObjectField schema={schema} value={value} path={path} onChange={onChange} media={media} onUpload={onUpload} />;
  }
  if (schema.type === 'list') {
    return <ListField schema={schema} value={value} path={path} onChange={onChange} media={media} onUpload={onUpload} />;
  }
  if (schema.type === 'media') {
    return (
      <div>
        <span className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</span>
        <MediaField value={value} path={path} onChange={onChange} media={media} onUpload={onUpload} />
      </div>
    );
  }
  if (schema.type === 'enum') {
    return (
      <div>
        <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</label>
        <select
          id={id}
          className={inputClass}
          value={typeof value === 'string' ? value : ''}
          onChange={(event) => onChange(event.target.value)}
        >
          {schema.values.map((option) => (
            <option key={option} value={option}>{option}</option>
          ))}
        </select>
      </div>
    );
  }
  if (schema.type === 'text' || schema.type === 'richtext') {
    return (
      <div>
        <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">
          {label(path)}
          {schema.type === 'richtext' && <span className="mr-1 font-normal text-slate-600">(HTML محدود و پاک‌سازی‌شده)</span>}
        </label>
        <textarea
          id={id}
          rows={schema.type === 'richtext' ? 8 : 3}
          maxLength={schema.max}
          className={inputClass}
          value={typeof value === 'string' ? value : ''}
          onChange={(event) => onChange(event.target.value)}
        />
      </div>
    );
  }
  return (
    <div>
      <label htmlFor={id} className="mb-1 block text-xs font-medium text-slate-600">{label(path)}</label>
      <input
        id={id}
        type={schema.type === 'url' ? 'text' : 'text'}
        inputMode={schema.type === 'url' ? 'url' : undefined}
        maxLength={schema.type === 'string' ? schema.max : undefined}
        className={inputClass}
        value={typeof value === 'string' ? value : ''}
        onChange={(event) => onChange(event.target.value)}
      />
    </div>
  );
}
