import { useEffect, useMemo, useState } from 'react';
import type { ApiClient } from '../api';
import { fieldLabel } from '../i18n';
import type { PageComponent, PageDocument, PageRecord, PageSection } from '../types';

interface RevisionDetail {
  id: string;
  revisionNumber: number;
  document: PageDocument;
}

interface Props {
  api: ApiClient;
  page: Pick<PageRecord, 'id' | 'revisions'>;
  selected: RevisionDetail;
  current: PageDocument;
}

type Entry = { label: string; description?: string; tone: 'add' | 'remove' | 'change' };
const equal = (a: unknown, b: unknown) => JSON.stringify(a) === JSON.stringify(b);

function describe(before: PageDocument, after: PageDocument): Entry[] {
  const output: Entry[] = [];
  const change = (label: string, description?: string) => output.push({ label, description, tone: 'change' });
  if (before.title !== after.title) change('عنوان صفحه', `«${before.title}» ← «${after.title}»`);
  if (before.locale !== after.locale) change('زبان صفحه');
  if (before.direction !== after.direction) change('جهت نوشتار');
  if (!equal(before.seo, after.seo)) {
    const keys = Array.from(new Set([...Object.keys(before.seo), ...Object.keys(after.seo)]));
    change('تنظیمات سئو', keys.filter((key) => !equal((before.seo as unknown as Record<string, unknown>)[key], (after.seo as unknown as Record<string, unknown>)[key])).map(fieldLabel).join('، '));
  }
  if (!equal(before.theme, after.theme)) change('پوسته و سیستم طراحی', 'رنگ‌ها، قلم‌ها یا سایر توکن‌های طراحی');
  if (!equal(before.settings, after.settings)) change('تنظیمات کلی صفحه');

  const oldSections = new Map(before.sections.map((section) => [section.id, section]));
  const nextSections = new Map(after.sections.map((section) => [section.id, section]));
  for (const section of after.sections) {
    const old = oldSections.get(section.id);
    if (!old) output.push({ label: `بخش افزوده‌شده: ${section.label}`, tone: 'add' });
    else if (!equal({ label: old.label, layout: old.layout, visibility: old.visibility }, { label: section.label, layout: section.layout, visibility: section.visibility })) {
      change(`تنظیمات بخش «${section.label}»`);
    }
  }
  for (const section of before.sections) {
    if (!nextSections.has(section.id)) output.push({ label: `بخش حذف‌شده: ${section.label}`, tone: 'remove' });
  }
  const sharedOld = before.sections.map((section) => section.id).filter((id) => nextSections.has(id));
  const sharedNew = after.sections.map((section) => section.id).filter((id) => oldSections.has(id));
  if (!equal(sharedOld, sharedNew)) change('ترتیب بخش‌های صفحه');

  const collect = (document: PageDocument) => {
    const result = new Map<string, { component: PageComponent; section: PageSection; index: number }>();
    for (const section of document.sections) {
      section.components.forEach((component, index) => result.set(component.id, { component, section, index }));
    }
    return result;
  };
  const oldBlocks = collect(before);
  const nextBlocks = collect(after);
  for (const [id, next] of nextBlocks) {
    const prev = oldBlocks.get(id);
    if (!prev) {
      output.push({ label: `بلوک افزوده‌شده: ${next.component.type}`, description: next.section.label, tone: 'add' });
      continue;
    }
    const fields: string[] = [];
    if (prev.section.id !== next.section.id) fields.push('جابجایی بین بخش‌ها');
    if (prev.index !== next.index) fields.push('ترتیب نمایش');
    if (prev.component.type !== next.component.type) fields.push('نوع کامپوننت');
    if (prev.component.variant !== next.component.variant) fields.push('گونهٔ نمایش');
    if (!equal(prev.component.props, next.component.props)) {
      const keys = Array.from(new Set([...Object.keys(prev.component.props), ...Object.keys(next.component.props)]));
      const changed = keys.filter((key) => !equal(prev.component.props[key], next.component.props[key]));
      fields.push(`محتوا (${changed.map(fieldLabel).join('، ')})`);
    }
    if (!equal(prev.component.design, next.component.design)) fields.push('طراحی');
    if (!equal(prev.component.responsive, next.component.responsive)) fields.push('نمایش واکنش‌گرا');
    if (!equal(prev.component.animation, next.component.animation)) fields.push('انیمیشن');
    if (!equal(prev.component.visibility, next.component.visibility)) fields.push('نمایش در دستگاه‌ها');
    if (!equal(prev.component.a11y, next.component.a11y)) fields.push('دسترس‌پذیری');
    if (prev.component.version !== next.component.version) fields.push('نسخهٔ کامپوننت');
    if (fields.length) change(`بلوک ویرایش‌شده: ${next.component.type}`, fields.join('، '));
  }
  for (const [id, previous] of oldBlocks) {
    if (!nextBlocks.has(id)) output.push({ label: `بلوک حذف‌شده: ${previous.component.type}`, description: previous.section.label, tone: 'remove' });
  }
  return output;
}

const counter = (doc: PageDocument) => doc.sections.reduce((n, section) => n + section.components.length, 0);

export function RevisionComparisonPanel({ api, page, selected, current }: Props) {
  const [compareId, setCompareId] = useState('current');
  const [compared, setCompared] = useState<RevisionDetail | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => { setCompareId('current'); setCompared(null); setError(null); }, [selected.id]);
  useEffect(() => {
    if (compareId === 'current' || compareId === selected.id) {
      setCompared(null);
      setLoading(false);
      setError(null);
      return;
    }
    let alive = true;
    setLoading(true);
    setError(null);
    void api.get<{ revision: RevisionDetail }>('revision', { page: page.id, id: compareId })
      .then((response) => { if (alive) setCompared(response.revision); })
      .catch((reason: unknown) => {
        if (alive) { setCompared(null); setError(reason instanceof Error ? reason.message : 'دریافت نسخه مقایسه ممکن نشد.'); }
      })
      .finally(() => { if (alive) setLoading(false); });
    return () => { alive = false; };
  }, [api, compareId, page.id, selected.id]);

  const destination = compareId === 'current' ? current : compared?.document ?? null;
  const changes = useMemo(() => destination ? describe(selected.document, destination) : [], [selected.document, destination]);
  const destinationLabel = compareId === 'current' ? 'پیش‌نویس فعلی' : `نسخهٔ #${compared?.revisionNumber ?? '…'}`;

  return (
    <details className="rounded-lg border border-slate-200 bg-white p-2 text-right">
      <summary className="cursor-pointer text-xs font-semibold text-slate-800">مقایسه دقیق با نسخه دیگر</summary>
      <div className="mt-3 grid gap-3">
        <label className="grid gap-1 text-xs text-slate-600">
          مقایسه نسخهٔ #{selected.revisionNumber} با
          <select
            aria-label="نسخه مقصد برای مقایسه"
            className="w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs text-slate-900"
            value={compareId}
            onChange={(event) => setCompareId(event.target.value)}
          >
            <option value="current">پیش‌نویس فعلی (با تغییرات ذخیره‌نشده)</option>
            {page.revisions.filter((revision) => revision.id !== selected.id).map((revision) => (
              <option key={revision.id} value={revision.id}>نسخهٔ #{revision.revision_number} · {revision.created_at}</option>
            ))}
          </select>
        </label>
        {loading && <p role="status" className="text-xs text-slate-500">در حال دریافت نسخه برای مقایسه…</p>}
        {error && <p role="alert" className="text-xs text-red-700">{error}</p>}
        {!loading && !error && destination && (
          <>
            <div className="grid grid-cols-2 gap-2 text-xs">
              <div className="rounded-md bg-slate-50 p-2">
                <span className="block text-slate-500">نسخهٔ #{selected.revisionNumber}</span>
                <strong>{selected.document.sections.length} بخش · {counter(selected.document)} بلوک</strong>
              </div>
              <div className="rounded-md bg-slate-50 p-2">
                <span className="block text-slate-500">{destinationLabel}</span>
                <strong>{destination.sections.length} بخش · {counter(destination)} بلوک</strong>
              </div>
            </div>
            <p className="text-xs font-semibold text-slate-700">{changes.length} مورد اختلاف شناسایی شد</p>
            {changes.length === 0 ? (
              <p className="text-xs text-slate-500">تفاوتی در محتوای قابل بررسی پیدا نشد.</p>
            ) : (
              <ul className="max-h-56 space-y-1 overflow-auto" aria-label="جزئیات مقایسه نسخه‌ها">
                {changes.slice(0, 100).map((entry, index) => (
                  <li key={index} className="rounded-md border border-slate-100 px-2 py-1.5 text-xs">
                    <span className={`font-semibold ${entry.tone === 'add' ? 'text-emerald-700' : entry.tone === 'remove' ? 'text-red-700' : 'text-amber-800'}`}>
                      {entry.tone === 'add' ? 'افزوده‌شده' : entry.tone === 'remove' ? 'حذف‌شده' : 'تغییریافته'}
                    </span>
                    <span className="mr-2 text-slate-800">{entry.label}</span>
                    {entry.description && <p className="mt-0.5 break-words text-[11px] text-slate-500">{entry.description}</p>}
                  </li>
                ))}
              </ul>
            )}
            {changes.length > 100 && <p className="text-[11px] text-slate-500">۱۰۰ مورد اول از {changes.length} اختلاف نمایش داده شده است.</p>}
            <p className="text-[11px] text-slate-500">این مقایسه فقط نمایشی است؛ برای بازگردانی یا انتشار از دکمه‌های اصلی تاریخچه استفاده کنید.</p>
          </>
        )}
      </div>
    </details>
  );
}

