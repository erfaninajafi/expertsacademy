import type { PageRecord } from '../types';

type Props = {
  page: PageRecord | null;
  dirty: boolean;
  saving: boolean;
  conflict: boolean;
};

// Display the server's timestamp without inventing a time-zone conversion.
function recordedTime(value: string | null | undefined): string {
  if (!value) return 'ثبت نشده';
  return value.replace('T', ' ').replace(/-/g, '/').slice(0, 16);
}

export function PageStatusSummary({ page, dirty, saving, conflict }: Props) {
  const draftStatus = conflict
    ? 'تداخل نسخه؛ نیاز به بررسی'
    : saving
      ? 'در حال ذخیره…'
      : dirty
        ? 'تغییرات ذخیره‌نشده'
        : page?.draftRevision
          ? 'پیش‌نویس ذخیره شده'
          : 'پیش‌نویسی ثبت نشده';

  const draftClass = conflict
    ? 'bg-red-100 text-red-800'
    : saving
      ? 'bg-indigo-100 text-indigo-800'
      : dirty
        ? 'bg-amber-100 text-amber-800'
        : page?.draftRevision
          ? 'bg-emerald-100 text-emerald-800'
          : 'bg-slate-100 text-slate-700';

  const publicationStatus = !page
    ? 'صفحه‌ای انتخاب نشده'
    : page.status === 'published'
      ? 'نسخه‌ای منتشر شده'
      : page.status === 'scheduled'
        ? 'انتشار زمان‌بندی شده'
        : page.status === 'unpublished'
          ? 'از انتشار خارج شده'
          : page.status === 'archived'
            ? 'بایگانی شده'
            : 'هنوز منتشر نشده';

  const publicationClass = page?.status === 'published'
    ? 'bg-emerald-100 text-emerald-800'
    : page?.status === 'scheduled'
      ? 'bg-indigo-100 text-indigo-800'
      : 'bg-slate-100 text-slate-700';

  const draftDiffersFromPublished = Boolean(
    page?.status === 'published' && page.draftRevision &&
    page.draftRevision !== page.publishedRevision,
  );
  const liveVersionBehind = page?.status === 'published' && (dirty || draftDiffersFromPublished);
  const lastDraftSave = page?.revisions?.[0]?.created_at ?? null;

  return (
    <div
      data-ea-save-publish-status="v1"
      aria-label="وضعیت ذخیره و انتشار صفحه"
      className="flex max-w-full flex-wrap items-start gap-2 text-[11px]"
    >
      <div className="min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
        <div className="flex flex-wrap items-center gap-1.5">
          <span className="font-semibold text-slate-700">پیش‌نویس:</span>
          <span data-testid="draft-save-state" className={`rounded-full px-2 py-0.5 font-semibold ${draftClass}`}>
            {draftStatus}
          </span>
        </div>
        {page && (
          <p className="mt-1 text-[10px] text-slate-500" title={lastDraftSave ?? undefined}>
            آخرین ذخیره پیش‌نویس: <span dir="ltr" className="inline-block">{recordedTime(lastDraftSave)}</span>
          </p>
        )}
      </div>

      <div className="min-w-0 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5">
        <div className="flex flex-wrap items-center gap-1.5">
          <span className="font-semibold text-slate-700">انتشار در Studio:</span>
          <span data-testid="publication-state" className={`rounded-full px-2 py-0.5 font-semibold ${publicationClass}`}>
            {publicationStatus}
          </span>
        </div>
        {page?.status === 'scheduled' && page.scheduledAt ? (
          <p className="mt-1 text-[10px] text-slate-500" title={page.scheduledAt}>
            زمان برنامه‌ریزی‌شده: <span dir="ltr" className="inline-block">{recordedTime(page.scheduledAt)}</span>
          </p>
        ) : page && (
          <p className="mt-1 text-[10px] text-slate-500" title={page.publishedAt ?? undefined}>
            آخرین انتشار ثبت‌شده: <span dir="ltr" className="inline-block">{recordedTime(page.publishedAt)}</span>
          </p>
        )}
        {liveVersionBehind && (
          <p className="mt-1 font-medium text-amber-800" data-testid="unpublished-edits">
            {dirty ? 'ویرایش‌های فعلی هنوز ذخیره و منتشر نشده‌اند.' : 'پیش‌نویس جدید هنوز منتشر نشده است.'}
          </p>
        )}
      </div>
    </div>
  );
}
