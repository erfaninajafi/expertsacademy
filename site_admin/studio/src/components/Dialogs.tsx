import * as Dialog from '@radix-ui/react-dialog';
import { useEffect, useMemo, useState, type ReactNode } from 'react';
import type { MediaItem } from '../types';
import { permissionLabel, roleLabel, statusLabel } from '../i18n';
import { PublishIssueDetails } from './PublishIssueDetails';

interface ModalProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  title: string;
  description?: string;
  children: ReactNode;
  footer?: ReactNode;
  wide?: boolean;
}

export function Modal({ open, onOpenChange, title, description, children, footer, wide }: ModalProps) {
  return (
    <Dialog.Root open={open} onOpenChange={onOpenChange}>
      <Dialog.Portal>
        <Dialog.Overlay className="ea-studio-modal-overlay fixed inset-0 z-[2100] bg-slate-900/50" />
        <Dialog.Content className={`ea-studio-modal fixed left-1/2 top-1/2 z-[2100] max-h-[90vh] -translate-x-1/2 -translate-y-1/2 overflow-auto rounded-2xl bg-white p-5 shadow-2xl focus:outline-none ${wide ? 'w-[min(94vw,60rem)]' : 'w-[min(92vw,32rem)]'}`}>
          <div className="mb-3 flex items-start justify-between gap-4">
            <div>
              <Dialog.Title className="text-base font-semibold text-slate-900">{title}</Dialog.Title>
              {description && <Dialog.Description className="mt-1 text-sm text-slate-500">{description}</Dialog.Description>}
            </div>
            <Dialog.Close asChild>
              <button type="button" aria-label="بستن پنجره"
                className="rounded p-1 text-slate-600 hover:bg-slate-100 hover:text-slate-700">✕</button>
            </Dialog.Close>
          </div>
          {children}
          {footer && <div className="ea-studio-modal-footer mt-5 flex flex-wrap justify-end gap-2">{footer}</div>}
        </Dialog.Content>
      </Dialog.Portal>
    </Dialog.Root>
  );
}

interface CreateProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  busy: boolean;
  initial?: { title: string; locale: 'fa' | 'en'; legacyCode: string; fullPath: string } | null;
  legacyEditorUrl?: string;
  onCreate: (input: { title: string; locale: 'fa' | 'en'; fullPath: string }) => Promise<void>;
}

export function CreatePageDialog({ open, onOpenChange, busy, onCreate, initial, legacyEditorUrl }: CreateProps) {
  const [title, setTitle] = useState('');
  const [locale, setLocale] = useState<'fa' | 'en'>('en');
  const [fullPath, setFullPath] = useState('');

  useEffect(() => {
    if (!open) return;
    setTitle(initial?.title ?? '');
    setLocale(initial?.locale ?? 'en');
    setFullPath(initial?.fullPath ?? '');
  }, [open, initial?.title, initial?.locale, initial?.legacyCode, initial?.fullPath]);

  return (
    <Modal open={open} onOpenChange={onOpenChange} title="صفحهٔ جدید"
      description="صفحه به‌صورت پیش‌نویس ساخته می‌شود. برای صفحات جدید، آدرس عمومی را وارد کنید. صفحه تا زمان انتشار عمومی، پیش‌نویس باقی می‌ماند."
      footer={
        <>
          <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenChange(false)}>انصراف</button>
           {legacyEditorUrl && (
             <a href={legacyEditorUrl} className="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
               ویرایش با CMS قدیمی
             </a>
           )}
           <button type="button" disabled={busy || title.trim() === ''}
            onClick={() => void onCreate({ title: title.trim(), locale, fullPath: fullPath.trim() })}
            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
            {busy ? 'در حال ساخت…' : 'ساخت پیش‌نویس'}
          </button>
        </>
      }>
      <div className="grid gap-3">
        <label className="text-xs font-medium text-slate-600">عنوان<input className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" maxLength={200}
            value={title} onChange={(event) => setTitle(event.target.value)} autoFocus />
        </label>
        <label className="text-xs font-medium text-slate-600">زبان<select className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value={locale}
            onChange={(event) => setLocale(event.target.value as 'fa' | 'en')}>
            <option value="en">انگلیسی (چپ‌به‌راست)</option>
            <option value="fa">فارسی (راست‌به‌چپ)</option>
          </select>
        </label>
        <label className="text-xs font-medium text-slate-600">
          آدرس عمومی صفحه
          <input
            dir="ltr"
            className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            maxLength={255}
            placeholder={`${locale}/courses/engineering-math`}
            value={fullPath}
            onChange={(event) => setFullPath(event.target.value)}
          />
          <span className="mt-1 block text-[11px] text-slate-500">
            نمونه: /{locale}/courses/engineering-math — برای ذخیرهٔ پیش‌نویس بدون مسیر می‌توانید خالی بگذارید.
          </span>
        </label>
      </div>
    </Modal>
  );
}

interface MediaProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  items: MediaItem[];
  onUpload: (file: File, alt: string) => Promise<void>;
  onMeta?: (id: string, alt: string, focalX: number, focalY: number) => Promise<void>;
  busy: boolean;
}

function FocalControls({ item, busy, onMeta }: { item: MediaItem; busy: boolean; onMeta?: MediaProps['onMeta'] }) {
  const [x, setX] = useState(item.focal_x ?? 50);
  const [y, setY] = useState(item.focal_y ?? 50);
  if (!onMeta) return null;
  return (
    <details className="mt-1 text-[11px] text-slate-600">
      <summary className="cursor-pointer">نقطهٔ کانونی</summary>
      <div className="mt-1 flex items-center gap-1">
        <label>X <input type="number" min={0} max={100} value={x} disabled={busy}
          onChange={(event) => setX(Number(event.target.value))}
          className="w-14 rounded border border-slate-300 px-1 py-0.5" /></label>
        <label>Y <input type="number" min={0} max={100} value={y} disabled={busy}
          onChange={(event) => setY(Number(event.target.value))}
          className="w-14 rounded border border-slate-300 px-1 py-0.5" /></label>
        <button type="button" disabled={busy} onClick={() => void onMeta(item.id, item.alt_text, x, y)}
          className="rounded border border-slate-200 px-1.5 py-0.5 font-medium text-slate-700 disabled:opacity-40">ذخیره</button>
      </div>
    </details>
  );
}

export function MediaDialog({ open, onOpenChange, items, onUpload, onMeta, busy }: MediaProps) {
  const [alt, setAlt] = useState('');
  const [query, setQuery] = useState('');
  const visible = useMemo(() => {
    const needle = query.trim().toLowerCase();
    if (needle === '') return items;
    return items.filter((item) => `${item.original_name} ${item.alt_text}`.toLowerCase().includes(needle));
  }, [items, query]);
  return (
    <Modal open={open} onOpenChange={onOpenChange} title="کتابخانهٔ رسانه"
      description="تصاویر بر اساس نوع MIME بررسی، تغییر نام داده و بیرون از مسیرهای اجرایی ذخیره می‌شوند.">
      <div className="grid gap-4">
        <div className="rounded-xl border border-dashed border-slate-300 p-3">
          <label className="text-xs font-medium text-slate-600">بارگذاری تصویر<input type="file" accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml"
              className="mt-1 block w-full text-xs"
              onChange={(event) => {
                const file = event.target.files?.[0];
                if (file) void onUpload(file, alt);
                event.target.value = '';
              }} />
          </label>
          <label className="mt-2 block text-xs font-medium text-slate-600">متن جایگزین<input className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" maxLength={500}
              value={alt} onChange={(event) => setAlt(event.target.value)} />
          </label>
          {busy && <p className="mt-2 text-xs text-indigo-700" role="status">در حال بارگذاری…</p>}
        </div>
        <label className="text-xs font-medium text-slate-600">جست‌وجوی رسانه<input type="search" className="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            placeholder="نام یا متن جایگزین" value={query} onChange={(event) => setQuery(event.target.value)} />
        </label>
        <ul aria-label="آیتم‌های رسانه" className="max-h-64 divide-y divide-slate-100 overflow-auto">
          {visible.map((item) => (
            <li key={item.id} className="flex items-center justify-between gap-3 py-2">
              <span className="flex min-w-0 items-center gap-2">
                <img src={item.url} alt="" loading="lazy" className="h-10 w-10 shrink-0 rounded bg-slate-100 object-cover" />
                <span className="min-w-0">
                  <span className="block truncate text-sm text-slate-800">{item.original_name}</span>
                  <span className="block truncate text-[11px] text-slate-600">
                    {item.width && item.height ? `${item.width}×${item.height} · ` : ''}{item.mime_type}
                  </span>
                </span>
              </span>
              <span className="flex flex-col items-end gap-1">
                <button type="button" className="rounded border border-slate-200 px-2 py-1 text-[11px] font-medium text-slate-700"
                  onClick={() => void navigator.clipboard?.writeText(item.id)}>کپی شناسه</button>
                <FocalControls item={item} busy={busy} onMeta={onMeta} />
              </span>
            </li>
          ))}
          {visible.length === 0 && (
            <li className="py-3 text-sm text-slate-600">{items.length === 0 ? 'هنوز رسانه‌ای بارگذاری نشده است.' : 'رسانه‌ای با این جست‌وجو پیدا نشد.'}</li>
          )}
        </ul>
      </div>
    </Modal>
  );
}

interface PublishingProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  busy: boolean;
  dirty: boolean;
  title: string;
  status: string;
  slug: string | null;
  fullPath: string | null;
  locale: 'fa' | 'en';
  checks: import('../types').QualityChecks | null;
  document: import('../types').PageDocument | null;
  canPublish: boolean;
  canArchive: boolean;
  canRoute: boolean;
  previewUrl: string | null;
  onPublish: () => Promise<void>;
  onUnpublish: () => Promise<void>;
  onArchive: () => Promise<void>;
  onRoute: (slug: string) => Promise<void>;
  onPublicPath: (path: string | null) => Promise<void>;
  onPreviewLink: () => Promise<void>;
}

export function PublishingDialog(props: PublishingProps) {
  const { open, busy, dirty, title, status, checks } = props;
  const [confirming, setConfirming] = useState(false);
  const [slugValue, setSlugValue] = useState(props.slug ?? '');
  const [fullPathValue, setFullPathValue] = useState(props.fullPath ?? '');
  const ready = props.canPublish && !busy && !dirty && checks?.ok === true && Boolean(props.fullPath?.trim());

  useEffect(() => {
    if (open) {
      setConfirming(false);
      setSlugValue(props.slug ?? '');
      setFullPathValue(props.fullPath ?? '');
    }
  }, [open, props.slug, props.fullPath]);
  useEffect(() => {
    if (dirty || checks?.ok !== true) setConfirming(false);
  }, [dirty, checks?.ok]);

  return (
    <Modal open={open} onOpenChange={props.onOpenChange} title="بررسی و انتشار صفحه" wide
      description="وضعیت صفحه، نشانی عمومی و ایرادهای قابل اصلاح را بررسی کنید؛ سپس انتشار را تأیید کنید.">
      <div data-ea-publish-cleanup="ea-publish-cleanup-v1" className="grid gap-3 text-right">
        <div className="grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)]">
          <section className="grid content-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <h3 className="text-sm font-bold text-slate-900">اطلاعات صفحه</h3>
              <span className="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">{statusLabel(status)}</span>
            </div>
            <div className="rounded-xl bg-slate-50 p-3">
              <p className="text-[11px] text-slate-500">نام صفحه</p>
              <p className="mt-1 break-words text-sm font-semibold text-slate-900">{title}</p>
            </div>
            <div className="rounded-xl bg-slate-50 p-3">
              <p className="text-[11px] text-slate-500">آدرس عمومی</p>
              <p dir="ltr" className="mt-1 break-all text-left text-xs font-semibold text-slate-800">
                {props.fullPath || 'هنوز مشخص نشده است'}
              </p>
            </div>
            {dirty && <p role="status" className="rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-xs leading-6 text-amber-900">
              تغییرات هنوز ذخیره نشده‌اند. ابتدا صفحه را ذخیره کنید تا بررسی کیفیت روی همان نسخه انجام شود.
            </p>}
            {!props.fullPath && <p role="status" className="rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-xs leading-6 text-amber-900">
              برای انتشار، آدرس عمومی صفحه را در قسمت «تنظیمات مسیر» ثبت کنید.
            </p>}
            <div className="flex flex-wrap gap-2 border-t border-slate-100 pt-3">
              {(status === 'published' || status === 'scheduled') && (
                <button type="button" disabled={busy || !props.canPublish} onClick={() => void props.onUnpublish()}
                  className="rounded-lg border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-50 disabled:opacity-40">لغو انتشار</button>
              )}
              {/* EA_STAGE19_ARCHIVE_IN_PUBLISH_V5: controlled archive action within publish review */}
              {status !== 'archived' && (
                <button
                  type="button"
                  disabled={busy || dirty || !props.canArchive}
                  title={!props.canArchive ? 'دسترسی بایگانی صفحه برای نقش شما فعال نیست.' : dirty ? 'ابتدا تغییرات ذخیره‌نشده را ذخیره کنید.' : 'انتقال به بایگانی پس از تأیید عنوان دقیق صفحه'}
                  onClick={() => void props.onArchive()}
                  className="rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-900 hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-50"
                >بایگانی صفحه</button>
              )}
              <button type="button" disabled={busy} onClick={() => void props.onPreviewLink()}
                className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-40">کپی لینک پیش‌نمایش</button>
            </div>
            {props.previewUrl && <p dir="ltr" data-testid="preview-url" className="break-all rounded-lg bg-slate-50 p-2 text-left text-[11px] text-slate-600">{props.previewUrl}</p>}
          </section>
          <section className="grid content-start gap-3 rounded-2xl border border-slate-200 bg-white p-4">
            <div className="flex flex-wrap items-center justify-between gap-2">
              <h3 className="text-sm font-bold text-slate-900">بررسی کیفیت پیش از انتشار</h3>
              {checks && <span className={`rounded-full px-2.5 py-1 text-[11px] font-semibold ${checks.ok ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}`}>
                {checks.ok ? 'آماده انتشار' : 'نیازمند اصلاح'}
              </span>}
            </div>
            {checks === null ? (
              <p role="status" className="rounded-lg bg-slate-50 p-3 text-xs leading-6 text-slate-600">در حال دریافت نتیجه بررسی کیفیت…</p>
            ) : (
              <>
                <div className="grid grid-cols-2 gap-2">
                  <div className={`rounded-xl border p-3 ${checks.errorCount > 0 ? 'border-rose-200 bg-rose-50' : 'border-emerald-100 bg-emerald-50'}`}>
                    <p className={`text-xl font-bold ${checks.errorCount > 0 ? 'text-rose-800' : 'text-emerald-800'}`}>{checks.errorCount.toLocaleString('fa-IR')}</p>
                    <p className="text-[11px] text-slate-700">خطای بازدارنده</p>
                  </div>
                  <div className={`rounded-xl border p-3 ${checks.warningCount > 0 ? 'border-amber-200 bg-amber-50' : 'border-emerald-100 bg-emerald-50'}`}>
                    <p className={`text-xl font-bold ${checks.warningCount > 0 ? 'text-amber-800' : 'text-emerald-800'}`}>{checks.warningCount.toLocaleString('fa-IR')}</p>
                    <p className="text-[11px] text-slate-700">هشدار</p>
                  </div>
                </div>
                <div className="max-h-[54vh] space-y-3 overflow-y-auto pl-1">
                  <PublishIssueDetails kind="error" issues={checks.errors} document={props.document} />
                  <PublishIssueDetails kind="warning" issues={checks.warnings} document={props.document} />
                  {checks.errors.length === 0 && checks.warnings.length === 0 && (
                    <p className="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-xs leading-6 text-emerald-900">هیچ خطا یا هشداری برای این نسخه یافت نشد.</p>
                  )}
                </div>
              </>
            )}
          </section>
        </div>
        {props.canRoute && (
          <details className="rounded-xl border border-slate-200 bg-slate-50/70 p-3">
            <summary className="cursor-pointer text-xs font-semibold text-slate-800">تنظیمات مسیر صفحه</summary>
            <div className="mt-3 grid gap-3 md:grid-cols-2">
              <label className="text-xs font-medium text-slate-700">آدرس عمومی صفحه
                <input dir="ltr" value={fullPathValue} onChange={(event) => setFullPathValue(event.target.value)}
                  maxLength={255} placeholder={`/${props.locale}/courses/example`}
                  className="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm" />
                <span className="mt-1 block text-[11px] font-normal leading-5 text-slate-500">ابتدا ذخیره کنید؛ برای صفحه منتشرشده، تغییر نشانی تا لغو انتشار ممکن نیست.</span>
                <button type="button" disabled={busy || status === 'published' || status === 'scheduled'}
                  onClick={() => void props.onPublicPath(fullPathValue.trim() || null)}
                  className="mt-2 rounded-lg border border-indigo-300 bg-white px-3 py-2 text-xs font-semibold text-indigo-700 disabled:opacity-40">ذخیره آدرس عمومی</button>
              </label>
              <label className="text-xs font-medium text-slate-700">نامک صفحه (اختیاری)
                <input dir="ltr" value={slugValue} onChange={(event) => setSlugValue(event.target.value)}
                  maxLength={120} placeholder="course-launch"
                  className="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm" />
                <span className="mt-1 block text-[11px] font-normal leading-5 text-slate-500">از حروف انگلیسی کوچک، عدد و خط تیره استفاده کنید.</span>
                <button type="button" disabled={busy} onClick={() => void props.onRoute(slugValue.trim())}
                  className="mt-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">ذخیره نامک</button>
              </label>
            </div>
          </details>
        )}
        <div className="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
          <p className="max-w-[34rem] text-xs leading-6 text-slate-600">
            {checks === null ? 'نتیجه بررسی کیفیت هنوز آماده نیست.' :
              checks.errorCount > 0 ? 'خطاهای قرمز را قبل از انتشار برطرف کنید.' :
              dirty ? 'ابتدا تغییرات صفحه را ذخیره کنید.' :
              !props.fullPath ? 'ابتدا آدرس عمومی صفحه را ثبت کنید.' :
              checks.warningCount > 0 ? 'هشدارهای زرد مانع انتشار نیستند؛ پیش از ادامه آن‌ها را بخوانید.' :
              'نسخه ذخیره‌شده برای انتشار آماده است.'}
          </p>
          {!confirming ? (
            <button type="button" disabled={!ready} onClick={() => setConfirming(true)}
              className="rounded-lg bg-indigo-700 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-800 disabled:opacity-40">بررسی نهایی انتشار</button>
          ) : (
            <div className="flex flex-wrap items-center gap-2">
              <span className="text-xs font-semibold text-slate-900">انتشار «{title}» در مسیر «{props.fullPath}»؟</span>
              <button type="button" disabled={busy} onClick={() => setConfirming(false)}
                className="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700">بازگشت</button>
              <button type="button" disabled={!ready} onClick={() => void props.onPublish()}
                className="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white disabled:opacity-40">
                {busy ? 'در حال انتشار…' : 'تأیید و انتشار صفحه'}
              </button>
            </div>
          )}
        </div>
      </div>
    </Modal>
  );
}

interface permissionssProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  users: import('../types').RoleUser[];
  roles: string[];
  matrix: Record<string, string[]>;
  canManage: boolean;
  busy: boolean;
  onAssign: (userId: string, role: string) => Promise<void>;
}

export function PermissionsDialog({ open, onOpenChange, users, roles, matrix, canManage, busy, onAssign }: permissionssProps) {
  return (
    <Modal open={open} onOpenChange={onOpenChange} title="نقش‌ها و دسترسی‌ها" wide
      description="نقش‌ها توسط API PHP اعمال می‌شوند. این ماتریس فقط مستندات خواندنی هر نقش است.">
      <div className="grid gap-4">
        {canManage ? (
          <table className="w-full text-right text-sm">
            <thead><tr className="border-b border-slate-200 text-xs text-slate-500"><th className="py-2">کاربر</th><th>نقش</th><th /></tr></thead>
            <tbody>
              {users.map((user) => (
                <tr key={user.id} className="border-b border-slate-100">
                  <td className="py-2 text-slate-800">{user.username}</td>
                  <td className="py-2">
                    <select aria-label={`نقش ${user.username}`} value={user.role} disabled={busy}
                      onChange={(event) => void onAssign(user.id, event.target.value)}
                      className="rounded-lg border border-slate-300 px-2 py-1 text-xs">
                      {roles.map((role) => <option key={role} value={role}>{roleLabel(role)}</option>)}
                    </select>
                  </td>
                  <td className="py-2 text-left text-[11px] text-slate-500">{user.permissions.length} دسترسی</td>
                </tr>
              ))}
              {users.length === 0 && <tr><td colSpan={3} className="py-3 text-xs text-slate-500">کاربر CMS پیدا نشد.</td></tr>}
            </tbody>
          </table>
        ) : <p className="text-sm text-slate-600">فقط مدیران می‌توانند نقش‌ها را تغییر دهند.</p>}
        <div className="overflow-auto rounded-xl border border-slate-200">
          <table className="w-full text-right text-[11px]">
            <thead><tr className="bg-slate-50 text-slate-600"><th className="p-2">دسترسی</th>{roles.map((role) => <th key={role} className="p-2">{roleLabel(role)}</th>)}</tr></thead>
            <tbody>
              {Object.keys(matrix).length > 0 && (matrix[roles[0]] ?? []).concat(
                roles.flatMap((role) => matrix[role] ?? []).filter((permission) => !(matrix[roles[0]] ?? []).includes(permission)),
              ).map((permission) => (
                <tr key={permission} className="border-t border-slate-100">
                  <td className="p-2 text-slate-700">{permissionLabel(permission)}</td>
                  {roles.map((role) => <td key={role} className="p-2 text-center">{(matrix[role] ?? []).includes(permission) ? '✓' : '—'}</td>)}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  );
}
