import * as Dialog from '@radix-ui/react-dialog';
import { useEffect, useMemo, useState } from 'react';
import type { ComponentDefinition, Locale } from '../types';
import { categoryLabel, variantLabel } from '../i18n';

interface Props {
  component: ComponentDefinition | null;
  canEdit: boolean;
  onClose: () => void;
  onAdd: (definition: ComponentDefinition) => void;
}

/** Real, authenticated PHP preview; does not mutate the editor until confirmation. */
export function ComponentQuickPreview({ component, canEdit, onClose, onAdd }: Props) {
  const [locale, setLocale] = useState<Locale>('fa');
  const [variant, setVariant] = useState('');

  useEffect(() => {
    setVariant(component?.variants[0]?.id ?? '');
    setLocale('fa');
  }, [component]);

  const selectedVariant = component?.variants.some((item) => item.id === variant)
    ? variant
    : (component?.variants[0]?.id ?? '');

  const previewUrl = useMemo(() => {
    if (!component) return '';
    const url = new URL('studio_component_preview.php', window.location.href);
    url.searchParams.set('type', component.type);
    url.searchParams.set('variant', selectedVariant);
    url.searchParams.set('locale', locale);
    return url.toString();
  }, [component, selectedVariant, locale]);

  const insertSelected = (): void => {
    if (!canEdit || !component) return;
    const chosen = component.variants.find((item) => item.id === selectedVariant);
    // Existing store.newComponent() chooses variants[0]. Reorder a *copy* so
    // insertion uses the previewed variant; never mutate the registry object.
    const definition = chosen && component.variants[0]?.id !== chosen.id
      ? { ...component, variants: [chosen, ...component.variants.filter((item) => item.id !== chosen.id)] }
      : component;
    onAdd(definition);
    onClose();
  };

  return (
    <Dialog.Root open={component !== null} onOpenChange={(open) => { if (!open) onClose(); }}>
      {component && (
        <Dialog.Portal>
          <Dialog.Overlay className="fixed inset-0 z-[2700] bg-slate-950/60" />
          <Dialog.Content dir="rtl"
            className="fixed left-1/2 top-1/2 z-[2701] flex max-h-[92vh] w-[min(94vw,64rem)] -translate-x-1/2 -translate-y-1/2 flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white text-right text-slate-900 shadow-2xl focus:outline-none">
            <div className="flex shrink-0 items-start justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
              <div className="min-w-0">
                <Dialog.Title className="text-base font-bold">{component.label}</Dialog.Title>
                <Dialog.Description className="mt-1 text-xs leading-5 text-slate-600">
                  {component.description} · {categoryLabel(component.category)}
                </Dialog.Description>
              </div>
              <Dialog.Close asChild>
                <button type="button" aria-label="بستن پیش‌نمایش"
                  className="shrink-0 rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">بستن ✕</button>
              </Dialog.Close>
            </div>

            <div className="flex shrink-0 flex-wrap items-center gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
              <span className="text-xs font-medium text-slate-600">گونه:</span>
              <div role="group" aria-label={`انتخاب گونهٔ ${component.label}`}
                className="flex min-w-0 flex-1 flex-wrap items-center gap-1.5">
                {component.variants.map((item) => (
                  <button key={item.id} type="button" aria-pressed={selectedVariant === item.id}
                    onClick={() => setVariant(item.id)}
                    className={`rounded-lg border px-3 py-1.5 text-xs font-medium ${selectedVariant === item.id
                      ? 'border-indigo-600 bg-indigo-50 text-indigo-800'
                      : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-300'}`}>{variantLabel(item.id, item.label)}</button>
                ))}
                {component.variants.length === 0 && <span className="text-xs text-slate-500">پیش‌فرض</span>}
              </div>
              <div className="flex shrink-0 items-center gap-1 rounded-lg border border-slate-200 p-1 text-xs" role="group" aria-label="زبان پیش‌نمایش">
                <button type="button" aria-pressed={locale === 'fa'} onClick={() => setLocale('fa')}
                  className={`rounded-md px-2 py-1 ${locale === 'fa' ? 'bg-slate-900 text-white' : 'text-slate-600'}`}>فارسی</button>
                <button type="button" aria-pressed={locale === 'en'} onClick={() => setLocale('en')}
                  className={`rounded-md px-2 py-1 ${locale === 'en' ? 'bg-slate-900 text-white' : 'text-slate-600'}`}>English</button>
              </div>
            </div>

            <div className="min-h-0 flex-1 overflow-auto bg-slate-50 p-3 sm:p-4">
              <div className="h-[min(52vh,490px)] min-h-64 overflow-hidden rounded-xl border border-slate-200 bg-white">
                <iframe key={`${component.type}:${selectedVariant}:${locale}`}
                  title={`پیش‌نمایش ${component.label}، گونه ${selectedVariant}`}
                  src={previewUrl} sandbox="allow-same-origin" loading="lazy" tabIndex={-1}
                  className="pointer-events-none h-full w-full border-0" />
              </div>
              <p className="mt-2 text-[11px] text-slate-500">پیش‌نمایش واقعی با رندر PHP؛ هنوز چیزی به صفحه اضافه نشده است.</p>
            </div>

            <div className="flex shrink-0 flex-wrap items-center justify-between gap-2 border-t border-slate-200 bg-white px-4 py-3 sm:px-5">
              <span className="text-[11px] text-slate-500">{canEdit ? 'افزودن فقط با تأیید شما انجام می‌شود.' : 'این صفحه فقط دسترسی مشاهده دارد.'}</span>
              <div className="flex gap-2">
                <button type="button" onClick={onClose}
                  className="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">انصراف</button>
                <button type="button" disabled={!canEdit} onClick={insertSelected}
                  className="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50">افزودن این گونه به صفحه</button>
              </div>
            </div>
          </Dialog.Content>
        </Dialog.Portal>
      )}
    </Dialog.Root>
  );
}
