import * as Dialog from '@radix-ui/react-dialog';
import { useEffect, useMemo, useState } from 'react';
import type { ComponentDefinition, Locale } from '../types';
import { categoryLabel, componentMatchesSearch, variantLabel } from '../i18n';

interface Props {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  components: ComponentDefinition[];
  canEdit: boolean;
  onAdd: (definition: ComponentDefinition) => void;
}

const BATCH_SIZE = 8;

// Uses the same authenticated PHP preview renderer already used by Component Studio.
// It is a read-only visual preview; adding a component still invokes Library.onAdd.
function previewSrc(component: ComponentDefinition, variant: string, locale: Locale): string {
  const url = new URL('studio_component_preview.php', window.location.href);
  url.searchParams.set('type', component.type);
  url.searchParams.set('variant', variant);
  url.searchParams.set('locale', locale);
  return url.toString();
}

export function VisualComponentGallery({ open, onOpenChange, components, canEdit, onAdd }: Props) {
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('all');
  const [locale, setLocale] = useState<Locale>('fa');
  const [limit, setLimit] = useState(BATCH_SIZE);
  const [selectedVariants, setSelectedVariants] = useState<Record<string, string>>({});

  const currentVariant = (component: ComponentDefinition): string => {
    const selected = selectedVariants[component.type];
    return component.variants.some((item) => item.id === selected)
      ? selected
      : (component.variants[0]?.id ?? '');
  };

  // stage08.02: the selected variant is inserted, not just previewed.
  // The existing store.newComponent() picks the first variant in the definition.
  // Pass a local copy with the chosen variant first, preserving the original registry.
  const addSelectedVariant = (component: ComponentDefinition): void => {
    if (!canEdit) return;
    const variantId = currentVariant(component);
    const choice = component.variants.find((item) => item.id === variantId);
    const chosenDefinition = choice && component.variants[0]?.id !== choice.id
      ? { ...component, variants: [choice, ...component.variants.filter((item) => item.id !== choice.id)] }
      : component;
    onAdd(chosenDefinition);
    onOpenChange(false);
  };

  const categories = useMemo(
    () => ['all', ...Array.from(new Set(components.map((item) => item.category))).sort()],
    [components],
  );

  const filtered = useMemo(() => {
    return components.filter((component) =>
      (category === 'all' || component.category === category) &&
      componentMatchesSearch(component, query),
    );
  }, [components, query, category]);

  useEffect(() => {
    setLimit(BATCH_SIZE);
  }, [query, category, open]);

  return (
    <Dialog.Root open={open} onOpenChange={onOpenChange}>
      <Dialog.Portal>
        <Dialog.Overlay className="fixed inset-0 z-[2600] bg-slate-950/60" />
        <Dialog.Content
          dir="rtl"
          className="fixed inset-x-3 bottom-[3vh] top-[3vh] z-[2601] mx-auto flex w-auto max-w-6xl flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-right text-slate-900 shadow-2xl focus:outline-none sm:inset-x-6"
        >
          <div className="flex shrink-0 items-start justify-between gap-4 border-b border-slate-200 px-4 py-3 sm:px-5">
            <div>
              <Dialog.Title className="text-base font-bold">گالری بصری کامپوننت‌ها</Dialog.Title>
              <Dialog.Description className="mt-1 text-xs text-slate-600">
                گونه‌ها را انتخاب و پیش‌نمایش کنید؛ با افزودن، همان گونه به صفحه اضافه می‌شود.
              </Dialog.Description>
            </div>
            <Dialog.Close asChild>
              <button type="button" aria-label="بستن گالری" className="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-sm hover:bg-slate-100">بستن ✕</button>
            </Dialog.Close>
          </div>

          <div className="shrink-0 space-y-2 border-b border-slate-100 px-4 py-3 sm:px-5">
            <div className="flex flex-wrap items-center gap-2">
              <label htmlFor="visual-gallery-search" className="sr-only">جست‌وجو در گالری</label>
              <input
                id="visual-gallery-search"
                type="search"
                value={query}
                onChange={(event) => setQuery(event.target.value)}
                placeholder="نام، کاربرد یا واژهٔ مشابه…"
                className="min-w-0 flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm sm:min-w-56"
              />
              <div role="group" aria-label="زبان پیش‌نمایش" className="flex rounded-lg border border-slate-200 p-1 text-xs">
                <button type="button" aria-pressed={locale === 'fa'} onClick={() => setLocale('fa')}
                  className={`rounded-md px-2.5 py-1 ${locale === 'fa' ? 'bg-slate-900 text-white' : 'text-slate-700'}`}>فارسی</button>
                <button type="button" aria-pressed={locale === 'en'} onClick={() => setLocale('en')}
                  className={`rounded-md px-2.5 py-1 ${locale === 'en' ? 'bg-slate-900 text-white' : 'text-slate-700'}`}>English</button>
              </div>
              <span className="text-xs text-slate-500">{filtered.length} کامپوننت</span>
            </div>
            <div role="group" aria-label="دسته‌بندی کامپوننت‌ها" className="flex gap-1.5 overflow-x-auto pb-1">
              {categories.map((item) => (
                <button type="button" key={item} aria-pressed={category === item} onClick={() => setCategory(item)}
                  className={`shrink-0 rounded-full px-3 py-1.5 text-xs ${category === item ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'}`}>
                  {categoryLabel(item)}
                  <span className="mr-1 rounded-full bg-black/10 px-1.5 py-0.5 text-[10px]">
                    {item === 'all' ? components.length : components.filter((entry) => entry.category === item).length}
                  </span>
                </button>
              ))}
            </div>
          </div>

          <div className="min-h-0 flex-1 overflow-y-auto bg-slate-50 p-3 sm:p-5">
            <div className="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
              {filtered.slice(0, limit).map((component) => (
                <article key={component.type} className="flex min-w-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                  <div className="flex min-w-0 items-center justify-between gap-2 px-3 py-2">
                    <h3 className="min-w-0 truncate text-sm font-semibold text-slate-800" title={component.label}>{component.label}</h3>
                    <span className="shrink-0 rounded bg-slate-100 px-2 py-1 text-[10px] text-slate-600">{categoryLabel(component.category)}</span>
                  </div>
                  <div role="group" aria-label={`انتخاب گونهٔ ${component.label}`}
                    className="flex min-h-9 items-center gap-1 overflow-x-auto border-t border-slate-100 px-3 py-2">
                    {component.variants.map((item) => (
                      <button
                        key={item.id}
                        type="button"
                        aria-pressed={currentVariant(component) === item.id}
                        onClick={() => setSelectedVariants((prev) => ({ ...prev, [component.type]: item.id }))}
                        title={`نمایش گونهٔ ${variantLabel(item.id, item.label)}`}
                        className={`shrink-0 rounded-md border px-2 py-1 text-[11px] font-medium transition ${
                          currentVariant(component) === item.id
                            ? 'border-indigo-600 bg-indigo-50 text-indigo-800'
                            : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-300'
                        }`}
                      >{variantLabel(item.id, item.label)}</button>
                    ))}
                    {component.variants.length === 0 && <span className="text-[11px] text-slate-500">گونه پیش‌فرض</span>}
                  </div>
                  <div className="h-52 overflow-hidden border-y border-slate-100 bg-white sm:h-56">
                    <iframe
                      key={`${component.type}:${currentVariant(component)}:${locale}`}
                      title={`پیش‌نمایش ${component.label}، گونهٔ ${currentVariant(component)}`}
                      src={previewSrc(component, currentVariant(component), locale)}
                      loading="lazy"
                      sandbox="allow-same-origin"
                      tabIndex={-1}
                      className="pointer-events-none h-full w-full border-0"
                    />
                  </div>
                  <div className="flex flex-1 flex-col gap-2 p-3">
                    <p className="line-clamp-2 min-h-9 text-xs leading-5 text-slate-600">{component.description}</p>
                    <div className="mt-auto flex items-center justify-between gap-2">
                      <span className="text-[11px] text-slate-500">{component.variants.length} گونه · گونهٔ انتخاب‌شده: {variantLabel(currentVariant(component), component.variants.find((item) => item.id === currentVariant(component))?.label)}</span>
                      <button
                        type="button"
                        disabled={!canEdit}
                        onClick={() => addSelectedVariant(component)}
                        className="shrink-0 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-40"
                      >افزودن به صفحه</button>
                    </div>
                  </div>
                </article>
              ))}
            </div>
            {filtered.length === 0 && <p className="py-10 text-center text-sm text-slate-600">نتیجه‌ای پیدا نشد؛ دستهٔ «همه» یا واژهٔ دیگری را امتحان کنید.</p>}
            {limit < filtered.length && (
              <div className="mt-5 text-center">
                <button type="button" onClick={() => setLimit((old) => old + BATCH_SIZE)}
                  className="rounded-lg border border-slate-300 bg-white px-5 py-2 text-sm font-medium text-slate-700 hover:border-indigo-400">
                  نمایش {Math.min(BATCH_SIZE, filtered.length - limit)} مورد دیگر
                </button>
              </div>
            )}
          </div>
        </Dialog.Content>
      </Dialog.Portal>
    </Dialog.Root>
  );
}
