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

/** Direct content editing rendered inside the selected block on the canvas. */
export function InlineEditor({ component, definition, canEdit, media, onUpload, busy }: Props) {
  const updateProps = useEditor((state) => state.updateProps);
  const fields = inlineFields(definition.schema);

  if (fields.length === 0) return null;

  return (
    <div data-testid="inline-editor" className="mt-2 border-t border-indigo-100 bg-indigo-50/50 px-3 py-2">
      <p className="mb-1.5 text-[10px] font-semibold uppercase tracking-wide text-indigo-700">ویرایش مستقیم محتوا</p>
      <div className="grid gap-2">
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
    </div>
  );
}
