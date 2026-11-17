import { useEffect, useState } from 'react';
import type { ApiClient } from '../api';
import { ApiError } from '../api';
import { diffDocuments, type DocumentDiff } from '../diff';
import type { PageDocument, PageRecord } from '../types';
import { Modal } from './Dialogs';

interface RevisionDetail {
  id: string;
  revisionNumber: number;
  createdAt: string;
  createdBy: string;
  document: PageDocument;
}

interface Props {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  api: ApiClient;
  page: PageRecord;
  current: PageDocument;
  busy: boolean;
  onRestore: (revisionId: string) => Promise<void>;
  onRollback?: (revisionId: string) => Promise<void>;
}

const when = (value: string): string => {
  const date = new Date(value.replace(' ', 'T'));
  return Number.isNaN(date.getTime()) ? value : date.toLocaleString('fa-IR', { dateStyle: 'medium', timeStyle: 'medium' });
};

export function HistoryDialog({ open, onOpenChange, api, page, current, busy, onRestore, onRollback }: Props) {
  const [selected, setSelected] = useState<string | null>(null);
  const [detail, setDetail] = useState<RevisionDetail | null>(null);
  const [diff, setDiff] = useState<DocumentDiff | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!open) return;
    setSelected(page.revisions[0]?.id ?? null);
    setDetail(null);
    setDiff(null);
    setError(null);
  }, [open, page.id, page.revisions]);

  useEffect(() => {
    if (!open || !selected) return;
    let alive = true;
    setLoading(true);
    setError(null);
    void api
      .get<{ revision: RevisionDetail }>('revision', { page: page.id, id: selected })
      .then((response) => {
        if (!alive) return;
        setDetail(response.revision);
        setDiff(diffDocuments(response.revision.document, current));
      })
      .catch((reason: unknown) => {
        if (alive) setError(reason instanceof Error ? reason.message : 'بارگذاری نسخه ممکن نشد.');
      })
      .finally(() => {
        if (alive) setLoading(false);
      });
    return () => {
      alive = false;
    };
  }, [api, current, open, page.id, selected]);

  const isCurrentDraft = selected !== null && selected === page.draftRevision;

  return (
    <Modal
      open={open}
      onOpenChange={onOpenChange}
      title="تاریخچهٔ نسخه‌ها"
      description="هر ذخیره یک نسخهٔ تغییرناپذیر می‌سازد. نسخهٔ گذشته را با پیش‌نویس فعلی مقایسه و به‌عنوان نسخهٔ جدید بازیابی کنید؛ تاریخچه هرگز بازنویسی نمی‌شود."
      footer={
        <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenChange(false)}>بستن</button>
      }
    >
      <div className="grid gap-3 sm:grid-cols-[9rem_minmax(0,1fr)]">
        <ol className="max-h-80 space-y-1 overflow-auto" aria-label="نسخه‌ها">
          {page.revisions.map((revision) => (
            <li key={revision.id}>
              <button
                type="button"
                aria-current={selected === revision.id ? 'true' : undefined}
                onClick={() => setSelected(revision.id)}
                className={`w-full rounded-lg px-2 py-1.5 text-right text-xs ${
                  selected === revision.id ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-100'
                }`}
              >
                <span className="block font-semibold">#{revision.revision_number}</span>
                <span className={`block text-[10px] ${selected === revision.id ? 'text-slate-300' : 'text-slate-500'}`}>
                  {when(revision.created_at)}
                </span>
              </button>
            </li>
          ))}
          {page.revisions.length === 0 && <li className="text-xs text-slate-500">هنوز نسخه‌ای وجود ندارد.</li>}
        </ol>

        <div className="min-w-0 rounded-lg border border-slate-200 p-3">
          {loading && <p className="text-sm text-slate-500" role="status">در حال بارگذاری نسخه…</p>}
          {error && <p className="text-sm text-red-700" role="alert">{error}</p>}
          {!loading && !error && detail && diff && (
            <div className="grid gap-3">
              <div>
                <p className="text-sm font-semibold text-slate-900">
                  نسخهٔ #{detail.revisionNumber} {isCurrentDraft && <span className="text-xs font-medium text-emerald-700">(پیش‌نویس فعلی)</span>}
                </p>
                <p className="text-[11px] text-slate-500">
                  {when(detail.createdAt)} · {detail.createdBy.slice(0, 8)}
                </p>
              </div>

              <dl className="grid grid-cols-2 gap-2 text-xs">
                <div className="rounded-md bg-slate-50 p-2">
                  <dt className="text-slate-500">بخش‌ها</dt>
                  <dd className="font-semibold text-slate-900">{diff.sectionsBefore} → {diff.sectionsAfter}</dd>
                </div>
                <div className="rounded-md bg-slate-50 p-2">
                  <dt className="text-slate-500">تغییرات کامپوننت</dt>
                  <dd className="font-semibold text-slate-900">{diff.components.length}</dd>
                </div>
              </dl>

              {diff.changedFields.length > 0 && (
                <p className="text-xs text-slate-600">تغییرات سطح صفحه:<span className="font-medium text-slate-900">{diff.changedFields.join(', ')}</span>
                </p>
              )}

              {diff.components.length > 0 ? (
                <ul className="max-h-40 space-y-1 overflow-auto text-xs" aria-label="تفاوت‌های ساختاری">
                  {diff.components.slice(0, 40).map((change) => (
                    <li key={`${change.kind}-${change.id}`} className="flex items-center justify-between gap-2 rounded bg-slate-50 px-2 py-1">
                      <span className="truncate font-medium text-slate-800">{change.type} · {change.variant}</span>
                      <span
                        className={
                          change.kind === 'added'
                            ? 'font-semibold text-emerald-700'
                            : change.kind === 'removed'
                              ? 'font-semibold text-red-700'
                              : 'font-semibold text-amber-700'
                        }
                      >
                        {change.kind}
                      </span>
                    </li>
                  ))}
                </ul>
              ) : (
                <p className="text-xs text-slate-500">تفاوت ساختاری با پیش‌نویس فعلی وجود ندارد.</p>
              )}

              <button
                type="button"
                disabled={busy || isCurrentDraft || detail.document === null}
                onClick={() => void onRestore(detail.id)}
                className="justify-self-start rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40"
              >
                {busy ? 'در حال بازیابی…' : isCurrentDraft ? 'همین پیش‌نویس فعلی است' : 'بازیابی این نسخه'}
              </button>
              <p className="text-[11px] leading-4 text-slate-500">
                بازگردانی نسخهٔ #{page.revisions[0]?.revision_number ? page.revisions[0].revision_number + 1 : 1} را می‌سازد؛ چیزی حذف نمی‌شود.
              </p>
              {onRollback && (
                <button
                  type="button"
                  disabled={busy || page.status === 'archived' || detail.document === null}
                  onClick={() => {
                    if (window.confirm(`Publish revision #${detail.revisionNumber} to the live site? Current draft revisions are kept.`)) {
                      void onRollback(detail.id);
                    }
                  }}
                  className="justify-self-start rounded-lg border border-emerald-300 px-3 py-2 text-xs font-semibold text-emerald-800 disabled:opacity-40"
                >انتشار این نسخه (بازگردانی)</button>
              )}
            </div>
          )}
        </div>
      </div>
    </Modal>
  );
}
