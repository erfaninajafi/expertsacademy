import { useMemo, useState } from 'react';
import type { ComponentDefinition } from '../types';
import { categoryLabel } from '../i18n';

interface Props {
  components: ComponentDefinition[];
  canEdit: boolean;
  onAdd: (definition: ComponentDefinition) => void;
}

export function Library({ components, canEdit, onAdd }: Props) {
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('all');

  const categories = useMemo(
    () => ['all', ...Array.from(new Set(components.map((component) => component.category))).sort()],
    [components],
  );

  const visible = useMemo(() => {
    const needle = query.trim().toLowerCase();
    return components.filter((component) => {
      if (category !== 'all' && component.category !== category) return false;
      if (needle === '') return true;
      return `${component.label} ${component.description} ${component.type}`.toLowerCase().includes(needle);
    });
  }, [components, category, query]);

  return (
    <section aria-label="کتابخانهٔ کامپوننت" className="flex h-full flex-col gap-3">
      <div>
        <label htmlFor="library-search" className="sr-only">جست‌وجوی کامپوننت‌ها</label>
        <input
          id="library-search"
          type="search"
          placeholder="جست‌وجوی کامپوننت‌ها"
          value={query}
          onChange={(event) => setQuery(event.target.value)}
          className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200"
        />
      </div>
      <div className="flex flex-wrap gap-1" role="group" aria-label="فیلتر بر اساس دسته">
        {categories.map((item) => (
          <button
            key={item}
            type="button"
            aria-pressed={category === item}
            onClick={() => setCategory(item)}
            className={`rounded-full px-2.5 py-1 text-xs font-medium capitalize ${
              category === item ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            }`}
          >
            {categoryLabel(item)}
          </button>
        ))}
      </div>
      <ul className="grid gap-2 overflow-y-auto pb-4">
        {visible.map((component) => (
          <li key={component.type}>
            <button
              type="button"
              disabled={!canEdit}
              onClick={() => onAdd(component)}
              className="w-full rounded-lg border border-slate-200 bg-white p-3 text-right transition hover:border-indigo-400 hover:shadow-sm disabled:cursor-not-allowed disabled:opacity-50"
            >
              <span className="flex items-center justify-between gap-2">
                <span className="text-sm font-semibold text-slate-800">{component.label}</span>
                <span className="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] uppercase tracking-wide text-slate-600">
                  {categoryLabel(component.category)}
                </span>
              </span>
              <span className="mt-1 block text-xs leading-5 text-slate-500">{component.description}</span>
              <span className="mt-1 block text-[11px] text-slate-500">{component.variants.length} گونه</span>
            </button>
          </li>
        ))}
        {visible.length === 0 && <li className="p-3 text-sm text-slate-500">کامپوننتی با این جست‌وجو پیدا نشد.</li>}
      </ul>
    </section>
  );
}
