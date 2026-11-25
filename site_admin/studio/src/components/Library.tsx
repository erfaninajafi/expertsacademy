import { useMemo, useState } from 'react';
import type { ComponentDefinition } from '../types';
import { categoryLabel, componentMatchesSearch } from '../i18n';
import { VisualComponentGallery } from './VisualComponentGallery';
import { ComponentQuickPreview } from './ComponentQuickPreview';

interface Props {
  components: ComponentDefinition[];
  canEdit: boolean;
  onAdd: (definition: ComponentDefinition) => void;
}

export function Library({ components, canEdit, onAdd }: Props) {
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('all');
  const [galleryOpen, setGalleryOpen] = useState(false);
  const [previewComponent, setPreviewComponent] = useState<ComponentDefinition | null>(null);
  // stage10: selecting a compact library card opens its preview, not an insertion.

  const categories = useMemo(
    () => ['all', ...Array.from(new Set(components.map((component) => component.category))).sort()],
    [components],
  );

  const visible = useMemo(() => {
    return components.filter((component) => {
      if (category !== 'all' && component.category !== category) return false;
      return componentMatchesSearch(component, query);
    });
  }, [components, category, query]);

  return (
    <section aria-label="کتابخانهٔ کامپوننت" className="flex h-full flex-col gap-3">
      <button type="button" onClick={() => setGalleryOpen(true)}
        className="w-full rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-100"
        title="نمایش پیش‌نمایش واقعی کامپوننت‌ها در پنجره بزرگ"
      >▦ گالری بصری کامپوننت‌ها</button>
      <div>
        <label htmlFor="library-search" className="sr-only">جست‌وجوی کامپوننت‌ها</label>
        <input
          id="library-search"
          type="search"
          placeholder="نام، کاربرد یا واژهٔ مشابه…"
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
        />
      </div>
      <div className="flex items-center gap-2">
        <label htmlFor="library-category" className="shrink-0 text-xs text-slate-600">دسته</label>
        <select
          id="library-category"
          value={category}
          onChange={(event) => setCategory(event.target.value)}
          className="min-w-0 w-full rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs text-slate-700"
        >
          {categories.map((item) => (
            <option key={item} value={item}>
              {categoryLabel(item)} ({item === 'all' ? components.length : components.filter((entry) => entry.category === item).length})
            </option>
          ))}
        </select>
      </div>
      <p aria-live="polite" className="text-[11px] text-slate-500">{visible.length} کامپوننت پیدا شد</p>
      <ul className="grid gap-2 overflow-y-auto pb-4">
        {visible.map((component) => (
          <li key={component.type}>
            <button
              type="button"
              onClick={() => setPreviewComponent(component)}
              title={`پیش‌نمایش ${component.label} و انتخاب گونه`}
              className="w-full rounded-lg border border-slate-200 bg-white p-3 text-right transition hover:border-indigo-400 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
            >
              <span className="flex items-center justify-between gap-2">
                <span className="text-sm font-semibold text-slate-800">{component.label}</span>
                <span className="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] uppercase tracking-wide text-slate-600">
                  {categoryLabel(component.category)}
                </span>
              </span>
              <span className="mt-1 block text-xs leading-5 text-slate-500">{component.description}</span>
              <span className="mt-1 flex items-center justify-between gap-2 text-[11px] text-slate-500"><span>{component.variants.length} گونه</span><span className="font-medium text-indigo-700">مشاهده و انتخاب ←</span></span>
            </button>
          </li>
        ))}
        {visible.length === 0 && <li className="p-3 text-sm text-slate-500">نتیجه‌ای پیدا نشد؛ عبارت کوتاه‌تر یا دستهٔ «همه» را امتحان کنید.</li>}
      </ul>
      <VisualComponentGallery
        open={galleryOpen}
        onOpenChange={setGalleryOpen}
        components={components}
        canEdit={canEdit}
        onAdd={onAdd}
      />
      <ComponentQuickPreview
        component={previewComponent}
        canEdit={canEdit}
        onClose={() => setPreviewComponent(null)}
        onAdd={onAdd}
      />
    </section>
  );
}
