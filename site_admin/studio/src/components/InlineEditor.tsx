import { useRef, useState } from 'react';
import type { ComponentDefinition, MediaItem, PageComponent } from '../types';
import { inlineFields, isHeadingField, propError, readProp, writeProp, type InlineField } from '../inline';
import { useEditor } from '../store';
import { MediaPicker } from './MediaPicker';

interface Props {
  component: PageComponent;
  definition: ComponentDefinition;
  canEdit: boolean;
  media?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
}

const controlClass =
  'w-full rounded-md border border-transparent bg-transparent px-2 py-1 text-slate-900 outline-none transition hover:border-slate-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-100 disabled:opacity-70';

function MediaControl({
  field,
  value,
  canEdit,
  onChange,
  items,
  onUpload,
  busy,
}: {
  field: InlineField;
  value: unknown;
  canEdit: boolean;
  onChange: (value: unknown) => void;
  items?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
}) {
  const [open, setOpen] = useState(false);
  const media = (value ?? {}) as { id?: string; alt?: string };
  const selected = items?.find((item) => item.id === media.id);

  return (
    <div>
      <span className="mb-0.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-700">{field.label}</span>
      <div className="grid gap-1 sm:grid-cols-[auto_1fr_1fr_auto]">
        <span aria-hidden="true" className="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded bg-slate-200 text-xs text-slate-700">
          {selected ? <img src={selected.url} alt="" className="h-full w-full object-cover" /> : '▧'}
        </span>
        <input
          aria-label={`${field.label} media ID`}
          className={`${controlClass} text-xs`}
          value={media.id ?? ''}
          placeholder="شناسهٔ رسانه"
          disabled={!canEdit}
          onChange={(event) => onChange({ ...media, id: event.target.value })}
        />
        <input
          aria-label={`${field.label} alternative text`}
          className={`${controlClass} text-xs`}
          value={media.alt ?? ''}
          maxLength={300}
          placeholder="متن جایگزین"
          disabled={!canEdit}
          onChange={(event) => onChange({ ...media, alt: event.target.value })}
        />
        <button
          type="button"
          aria-label="انتخاب تصویر"
          disabled={!canEdit}
          onClick={() => setOpen(true)}
          className="rounded-md border border-slate-300 bg-white px-2 py-1 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40"
        >انتخاب</button>
      </div>
      {selected && (
        <p className="mt-0.5 truncate text-[11px] text-slate-600">
          {selected.original_name}
          {selected.width && selected.height ? ` · ${selected.width}×${selected.height}` : ''}
        </p>
      )}
      <MediaPicker
        open={open}
        onOpenChange={setOpen}
        items={items ?? []}
        onUpload={onUpload}
        busy={busy}
        onSelect={(item) => onChange({ id: item.id, alt: item.alt_text || media.alt || '' })}
      />
    </div>
  );
}

function TextControl({
  field,
  value,
  canEdit,
  onChange,
}: {
  field: InlineField;
  value: unknown;
  canEdit: boolean;
  onChange: (value: unknown) => void;
}) {
  const [error, setError] = useState<string | null>(null);
  const startValue = useRef('');
  const text = typeof value === 'string' ? value : '';
  const schema = field.schema;
  const required = schema.required === true;
  const style = `${controlClass} ${isHeadingField(field.path) ? 'text-base font-semibold leading-tight' : 'text-sm leading-5'}`;
  const maxLength = schema.type === 'string' ? schema.max : undefined;

  const validate = (next: string): void => {
    const problem = propError(schema, next);
    setError(problem);
    if (problem && required && next.trim() === '') onChange(startValue.current);
  };

  return (
    <div>
      <span className="mb-0.5 block text-[10px] font-semibold uppercase tracking-wide text-slate-700">{field.label}</span>
      {schema.type === 'text' ? (
        <textarea
          aria-label={field.label}
          aria-invalid={error !== null}
          className={style}
          rows={2}
          value={text}
          maxLength={'max' in schema ? schema.max : undefined}
          disabled={!canEdit}
          onFocus={() => { startValue.current = text; }}
          onChange={(event) => { setError(null); onChange(event.target.value); }}
          onBlur={(event) => validate(event.target.value)}
        />
      ) : (
        <input
          aria-label={field.label}
          aria-invalid={error !== null}
          className={style}
          value={text}
          maxLength={maxLength}
          disabled={!canEdit}
          inputMode={schema.type === 'url' ? 'url' : undefined}
          onFocus={() => { startValue.current = text; }}
          onChange={(event) => { setError(null); onChange(event.target.value); }}
          onBlur={(event) => validate(event.target.value)}
          onKeyDown={(event) => {
            if (event.key === 'Enter') {
              event.preventDefault();
              event.currentTarget.blur();
            }
          }}
        />
      )}
      {error && (
        <p role="alert" className="mt-0.5 text-[11px] font-medium text-red-700">
          {field.label}: {error}
        </p>
      )}
    </div>
  );
}

/** Compact inline content editing: preview by default, edit on demand. */
export function InlineEditor({ component, definition, canEdit, media, onUpload, busy }: Props) {
  const updateProps = useEditor((state) => state.updateProps);
  const [expanded, setExpanded] = useState(false);
  const fields = inlineFields(definition.schema);

  if (fields.length === 0) return null;

  const summary = fields
    .filter((field) => field.schema.type === 'string' || field.schema.type === 'text')
    .map((field) => ({ label: field.label, text: readProp(component.props, field.path) }))
    .filter((item) => typeof item.text === 'string' && item.text.trim().length > 0)
    .slice(0, 2);

  return (
    <section data-testid="inline-editor" className="border-t border-indigo-100 bg-indigo-50/30 px-3 py-2">
      <button
        type="button"
        aria-expanded={expanded}
        aria-controls={`inline-fields-${component.id}`}
        onClick={() => setExpanded((value) => !value)}
        className="flex w-full items-center justify-between gap-2 rounded-md border border-indigo-100 bg-white px-2.5 py-1.5 text-right text-xs font-medium text-indigo-800 transition hover:border-indigo-300 hover:bg-indigo-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-500"
      >
        <span>ویرایش سریع محتوا</span>
        <span aria-hidden="true" className="shrink-0 text-[11px] text-indigo-600">{expanded ? '− بستن' : '+ باز کردن'}</span>
      </button>
      {!expanded && (
        <div className="mt-1.5 space-y-1" aria-label="خلاصه محتوای بلوک">
          {summary.length > 0 ? summary.map((item) => (
            <p key={item.label} className="flex min-w-0 gap-2 text-[11px] leading-5 text-slate-600">
              <span className="shrink-0 font-medium text-slate-500">{item.label}:</span>
              <span className="min-w-0 flex-1 truncate" dir="auto">{item.text as string}</span>
            </p>
          )) : <p className="text-[11px] text-slate-500">برای ویرایش متن و تصویر، این بخش را باز کنید.</p>}
        </div>
      )}
      <div id={`inline-fields-${component.id}`} className={`mt-2 gap-3 rounded-lg border border-indigo-100 bg-white p-2.5 ${expanded ? 'grid' : 'hidden'}`}>
        {fields.map((field) => {
          const value = readProp(component.props, field.path);
          const change = (next: unknown): void =>
            updateProps(component.id, writeProp(component.props, field.path, next));
          if (field.schema.type === 'media') {
            return (
              <MediaControl
                key={field.path}
                field={field}
                value={value}
                canEdit={canEdit}
                onChange={change}
                items={media}
                onUpload={onUpload}
                busy={busy}
              />
            );
          }
          return <TextControl key={field.path} field={field} value={value} canEdit={canEdit} onChange={change} />;
        })}
      </div>
    </section>
  );
}
