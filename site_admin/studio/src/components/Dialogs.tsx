import * as Dialog from '@radix-ui/react-dialog';
import { useMemo, useState, type ReactNode } from 'react';
import type { رسانهآیتم } from '../نوع';

interface ModalProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  title: string;
  description?: string;
  children: ReactNode;
  footer?: ReactNode;
  wide?: boolean;
}

export function Modal({ open, onOpenتغییر, title, description, children, footer, wide }: ModalProps) {
  return (
    <Dialog.Root open={open} onOpenتغییر={onOpenتغییر}>
      <Dialog.Portal>
        <Dialog.Overlay className="fixed inset-0 z-[2100] bg-slate-900/50" />
        <Dialog.محتوا className={`fixed left-1/2 top-1/2 z-[2100] max-h-[90vh] -translate-x-1/2 -translate-y-1/2 overflow-auto rounded-2xl bg-white p-5 shadow-2xl focus:outline-هیچ ${wide ? 'w-[min(94vw,60rem)]' : 'w-[min(92vw,32rem)]'}`}>
          <div className="mb-3 flex items-شروع justify-between gap-4">
            <div>
              <Dialog.عنوان className="text-base font-semibold text-slate-900">{title}</Dialog.عنوان>
              {description && <Dialog.توضیح className="mt-1 text-sm text-slate-500">{description}</Dialog.توضیح>}
            </div>
            <Dialog.بستن asChild>
              <button type="button" aria-label="بستن پنجره"
                className="rounded p-1 text-slate-600 hover:bg-slate-100 hover:text-slate-700">✕</button>
            </Dialog.بستن>
          </div>
          {children}
          {footer && <div className="mt-5 flex justify-پایان gap-2">{footer}</div>}
        </Dialog.محتوا>
      </Dialog.Portal>
    </Dialog.Root>
  );
}

interface CreateProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  busy: boolean;
  onCreate: (input: { title: string; locale: 'fa' | 'en'; legacyCode: string }) => Promise<void>;
}

export function CreateصفحهDialog({ open, onOpenتغییر, busy, onCreate }: CreateProps) {
  const [title, setعنوان] = useState('');
  const [locale, setزبان] = useState<'fa' | 'en'>('en');
  const [legacyCode, setLegacyCode] = useState('');

  return (
    <Modal open={open} onOpenتغییر={onOpenتغییر} title="صفحهٔ جدید"
      description="صفحهٔ خالی به‌صورت پیش‌نویس ساخته می‌شود و تا زمانی که منتشر نشود و به کد یک صفحهٔ موجود CMS متصل نشود، عمومی نیست."
      footer={
        <>
          <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenتغییر(false)}>انصراف</button>
          <button type="button" disabled={busy || title.trim() === ''}
            onClick={() => void onCreate({ title: title.trim(), locale, legacyCode: legacyCode.trim() })}
            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
            {busy ? 'در حال ساخت…' : 'ساخت پیش‌نویس'}
          </button>
        </>
      }>
      <div className="grid gap-3">
        <label className="text-xs font-medium text-slate-600">عنوان
          <input className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm" maxLength={200}
            value={title} onتغییر={(event) => setعنوان(event.target.value)} autoFocus />
        </label>
        <label className="text-xs font-medium text-slate-600">زبان
          <select className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm" value={locale}
            onتغییر={(event) => setزبان(event.target.value as 'fa' | 'en')}>
            <option value="en">انگلیسی (چپ‌به‌راست)</option>
            <option value="fa">فارسی (راست‌به‌چپ)</option>
          </select>
        </label>
        <label className="text-xs font-medium text-slate-600">کد صفحهٔ موجود CMS (اختیاری)
          <input className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm" maxLength={64}
            placeholder="مثلاً about" value={legacyCode}
            onتغییر={(event) => setLegacyCode(event.target.value)} />
          <span className="mt-1 block text-[11px] text-slate-500">
            اتصال به کد دسته باعث می‌شود نسخهٔ منتشرشده جای محتوای قدیمی آن نشانی را بگیرد.
          </span>
        </label>
      </div>
    </Modal>
  );
}

interface PublishProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  busy: boolean;
  title: string;
  code: string | null;
  onPublish: () => Promise<void>;
}

export function PublishDialog({ open, onOpenتغییر, busy, title, code, onPublish }: PublishProps) {
  return (
    <Modal open={open} onOpenتغییر={onOpenتغییر} title="این نسخه منتشر شود؟"
      description="انتشار makes the current draft the public revision served by PHP. The previous revision stays in history."
      footer={
        <>
          <button type="button" className="rounded-lg px-3 py-2 text-sm text-slate-600" onClick={() => onOpenتغییر(false)}>انصراف</button>
          <button type="button" disabled={busy} onClick={() => void onPublish()}
            className="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
            {busy ? 'انتشار…' : 'انتشار نسخه'}
          </button>
        </>
      }>
      <dl className="grid gap-2 text-sm">
        <div className="flex justify-between gap-4"><dt className="text-slate-500">صفحه</dt><dd className="font-medium text-slate-900">{title}</dd></div>
        <div className="flex justify-between gap-4"><dt className="text-slate-500">نشانی عمومی</dt>
          <dd className="font-medium text-slate-900">{code ? `existing page “${code}”` : 'هنوز به نشانی عمومی متصل نیست'}</dd></div>
      </dl>
      {!code && (
        <p className="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
          این صفحه به کد CMS متصل نیست؛ بنابراین انتشار آن را به‌صورت پیش‌نویس نسخه‌دار نگه می‌دارد. برای جایگزینی یک نشانی موجود، کد را متصل کنید.
        </p>
      )}
    </Modal>
  );
}

interface رسانهProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  items: رسانهآیتم[];
  onUpload: (file: File, alt: string) => Promise<void>;
  onMeta?: (id: string, alt: string, focalX: number, focalY: number) => Promise<void>;
  busy: boolean;
}

function FocalControls({ item, busy, onMeta }: { item: رسانهآیتم; busy: boolean; onMeta?: رسانهProps['onMeta'] }) {
  const [x, setX] = useState(item.focal_x ?? 50);
  const [y, setY] = useState(item.focal_y ?? 50);
  if (!onMeta) return null;
  return (
    <details className="mt-1 text-[11px] text-slate-600">
      <summary className="cursor-pointer">نقطهٔ کانونی</summary>
      <div className="mt-1 flex items-وسط gap-1">
        <label>X <input type="number" min={0} max={100} value={x} disabled={busy}
          onتغییر={(event) => setX(Number(event.target.value))}
          className="w-14 rounded border border-slate-300 px-1 py-0.5" /></label>
        <label>Y <input type="number" min={0} max={100} value={y} disabled={busy}
          onتغییر={(event) => setY(Number(event.target.value))}
          className="w-14 rounded border border-slate-300 px-1 py-0.5" /></label>
        <button type="button" disabled={busy} onClick={() => void onMeta(item.id, item.alt_text, x, y)}
          className="rounded border border-slate-200 px-1.5 py-0.5 font-medium text-slate-700 disabled:opacity-40">Save</button>
      </div>
    </details>
  );
}

export function رسانهDialog({ open, onOpenتغییر, items, onUpload, onMeta, busy }: رسانهProps) {
  const [alt, setAlt] = useState('');
  const [query, setQuery] = useState('');
  const visible = useMemo(() => {
    const needle = query.trim().toLowerCase();
    if (needle === '') return items;
    return items.filter((item) => `${item.original_name} ${item.alt_text}`.toLowerCase().includes(needle));
  }, [items, query]);
  return (
    <Modal open={open} onOpenتغییر={onOpenتغییر} title="رسانه library"
      description="تصاویر بر اساس نوع MIME بررسی، تغییرنام و خارج از مسیرهای اجرایی ذخیره می‌شوند.">
      <div className="grid gap-4">
        <div className="rounded-xl border border-dashed border-slate-300 p-3">
          <label className="text-xs font-medium text-slate-600">بارگذاری تصویر
            <input type="file" accept="image/jpeg,image/png,image/webp,image/avif,image/svg+xml"
              className="mt-1 block w-کامل text-xs"
              onتغییر={(event) => {
                const file = event.target.files?.[0];
                if (file) void onUpload(file, alt);
                event.target.value = '';
              }} />
          </label>
          <label className="mt-2 block text-xs font-medium text-slate-600">متن جایگزین
            <input className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm" maxLength={500}
              value={alt} onتغییر={(event) => setAlt(event.target.value)} />
          </label>
          {busy && <p className="mt-2 text-xs text-indigo-700" role="status">در حال بارگذاری…</p>}
        </div>
        <label className="text-xs font-medium text-slate-600">
          جست‌وجوی رسانه
          <input type="search" className="mt-1 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm"
            placeholder="نام یا متن جایگزین" value={query} onتغییر={(event) => setQuery(event.target.value)} />
        </label>
        <ul aria-label="رسانه items" className="max-h-64 divide-y divide-slate-100 overflow-auto">
          {visible.map((item) => (
            <li key={item.id} className="flex items-وسط justify-between gap-3 py-2">
              <span className="flex min-w-0 items-وسط gap-2">
                <img src={item.url} alt="" loading="lazy" className="h-10 w-10 shrink-0 rounded bg-slate-100 object-cover" />
                <span className="min-w-0">
                  <span className="block truncate text-sm text-slate-800">{item.original_name}</span>
                  <span className="block truncate text-[11px] text-slate-600">
                    {item.width && item.height ? `${item.width}×${item.height} · ` : ''}{item.mime_type}
                  </span>
                </span>
              </span>
              <span className="flex flex-col items-پایان gap-1">
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

interface انتشارProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  busy: boolean;
  title: string;
  status: string;
  code: string | null;
  slug: string | null;
  scheduledAt: string | null;
  checks: import('../نوع').QualityChecks | null;
  canPublish: boolean;
  canزمان‌بندی: boolean;
  canمسیر: boolean;
  previewUrl: string | null;
  onPublish: () => Promise<void>;
  onزمان‌بندی: (publishAt: string, timezone: string) => Promise<void>;
  onانصرافزمان‌بندی: () => Promise<void>;
  onلغو انتشار: () => Promise<void>;
  onآرشیو: () => Promise<void>;
  onمسیر: (slug: string, code: string) => Promise<void>;
  onپیش‌نمایشLink: () => Promise<void>;
}

export function انتشارDialog(props: انتشارProps) {
  const { open, onOpenتغییر, busy, title, status, code, slug, scheduledAt, checks } = props;
  const [publishAt, setPublishAt] = useState(() => {
    const date = new Date(Date.now() + 60 * 60 * 1000);
    date.setSeconds(0, 0);
    return new Date(date.getTime() - date.getمنطقهٔ زمانیOffset() * 60000).toISOString().slice(0, 16);
  });
  const [timezone, setمنطقهٔ زمانی] = useState(() => Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC');
  const [slugValue, setنامکValue] = useState(slug ?? '');
  const [codeValue, setCodeValue] = useState(code ?? '');

  return (
    <Modal open={open} onOpenتغییر={onOpenتغییر} title="انتشار" wide
      description="هر انتشار، بررسی‌های پیش‌ازانتشار را اجرا می‌کند و به‌صورت اتمیک ثبت می‌شود.">
      <div className="grid gap-4 md:grid-cols-2">
        <section className="rounded-xl border border-slate-200 p-3">
          <h3 className="text-sm font-semibold text-slate-800">وضعیت</h3>
          <p className="mt-1 text-sm text-slate-700">
            <span className="rounded-کامل bg-slate-100 px-2 py-0.5 text-xs font-semibold به بالاpercase">{status}</span>
            {scheduledAt && <span className="ml-2 text-xs text-slate-600">زمان‌بندی‌شده برای {scheduledAt} UTC</span>}
          </p>
          <dl className="mt-3 grid gap-1 text-xs text-slate-600">
            <div className="flex justify-between gap-3"><dt>اتصال عمومی</dt><dd className="font-medium text-slate-900">{code || '—'}</dd></div>
            <div className="flex justify-between gap-3"><dt>نامک</dt><dd className="font-medium text-slate-900">{slug || '—'}</dd></div>
          </dl>
          <div className="mt-3 flex flex-wrap gap-2">
            <button type="button" disabled={busy || !props.canPublish || checks?.ok === false} onClick={() => void props.onPublish()}
              className="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">
              {busy ? 'در حال انجام…' : 'انتشار فوری'}
            </button>
            {(status === 'published' || status === 'scheduled') && (
              <button type="button" disabled={busy || !props.canPublish} onClick={() => void props.onلغو انتشار()}
                className="rounded-lg border border-amber-300 px-3 py-2 text-xs font-semibold text-amber-800 disabled:opacity-40">لغو انتشار</button>
            )}
            <button type="button" disabled={busy || !props.canPublish} onClick={() => void props.onآرشیو()}
              className="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">آرشیو</button>
            <button type="button" disabled={busy} onClick={() => void props.onپیش‌نمایشLink()}
              className="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">کپی لینک پیش‌نمایش</button>
          </div>
          {props.previewUrl && (
            <p className="mt-2 break-all rounded bg-slate-50 p-2 text-[11px] text-slate-700" data-testid="preview-url">{props.previewUrl}</p>
          )}
        </section>

        <section className="rounded-xl border border-slate-200 p-3">
          <h3 className="text-sm font-semibold text-slate-800">بررسی‌های پیش‌ازانتشار</h3>
          {checks === null ? <p className="mt-2 text-xs text-slate-500">در حال بارگذاری بررسی‌ها…</p> : (
            <div className="mt-2 grid gap-2 text-xs">
              <p className={checks.ok ? 'font-semibold text-emerald-700' : 'font-semibold text-red-700'}>
                {checks.ok ? 'خطای بازدارنده‌ای وجود ندارد.' : `${checks.errorCount} خطای بازدارنده.`}
                {checks.warningCount > 0 ? ` ${checks.warningCount} هشدار.` : ''}
              </p>
              <ul className="max-h-36 space-y-1 overflow-auto">
                {checks.errors.map((issue) => (
                  <li key={`e-${issue.code}-${issue.path}`} className="rounded bg-red-50 p-1.5 text-red-800">⛔ {issue.message}</li>
                ))}
                {checks.warnings.map((issue) => (
                  <li key={`w-${issue.code}-${issue.path}`} className="rounded bg-amber-50 p-1.5 text-amber-800">⚠ {issue.message}</li>
                ))}
              </ul>
            </div>
          )}
        </section>

        {props.canزمان‌بندی && (
          <section className="rounded-xl border border-slate-200 p-3">
            <h3 className="text-sm font-semibold text-slate-800">زمان‌بندی انتشار</h3>
            <div className="mt-2 grid gap-2">
              <label className="text-xs font-medium text-slate-600">تاریخ و ساعت محلی
                <input type="datetime-local" value={publishAt} onتغییر={(event) => setPublishAt(event.target.value)}
                  className="mt-1 w-کامل rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
              </label>
              <label className="text-xs font-medium text-slate-600">منطقهٔ زمانی
                <input value={timezone} onتغییر={(event) => setمنطقهٔ زمانی(event.target.value)}
                  className="mt-1 w-کامل rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
              </label>
              <div className="flex flex-wrap gap-2">
                <button type="button" disabled={busy || checks?.ok === false || publishAt === ''}
                  onClick={() => void props.onزمان‌بندی(new Date(publishAt).toISOString(), timezone)}
                  className="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">زمان‌بندی</button>
                {status === 'scheduled' && (
                  <button type="button" disabled={busy} onClick={() => void props.onانصرافزمان‌بندی()}
                    className="rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">انصراف schedule</button>
                )}
              </div>
            </div>
          </section>
        )}

        {props.canمسیر && (
          <section className="rounded-xl border border-slate-200 p-3">
            <h3 className="text-sm font-semibold text-slate-800">مسیر</h3>
            <div className="mt-2 grid gap-2">
              <label className="text-xs font-medium text-slate-600">نامک
                <input value={slugValue} onتغییر={(event) => setنامکValue(event.target.value)} maxLength={120}
                  placeholder="course-launch" className="mt-1 w-کامل rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
              </label>
              <label className="text-xs font-medium text-slate-600">کد موجود CMS
                <input value={codeValue} onتغییر={(event) => setCodeValue(event.target.value)} maxLength={64}
                  placeholder="about" className="mt-1 w-کامل rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
              </label>
              <button type="button" disabled={busy} onClick={() => void props.onمسیر(slugValue.trim(), codeValue.trim())}
                className="w-fit rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 disabled:opacity-40">ذخیرهٔ مسیر</button>
            </div>
          </section>
        )}
      </div>
      <p className="mt-3 text-[11px] text-slate-500">صفحه: {title}</p>
    </Modal>
  );
}

interface دسترسیsProps {
  open: boolean;
  onOpenتغییر: (open: boolean) => void;
  users: import('../نوع').نقشکاربر[];
  roles: string[];
  matrix: Record<string, string[]>;
  canManage: boolean;
  busy: boolean;
  onAssign: (userId: string, role: string) => Promise<void>;
}

export function دسترسیsDialog({ open, onOpenتغییر, users, roles, matrix, canManage, busy, onAssign }: دسترسیsProps) {
  return (
    <Modal open={open} onOpenتغییر={onOpenتغییر} title="نقش‌ها and دسترسی" wide
      description="نقش‌ها are enforced by the PHP API. The matrix is read-only documentation of each role.">
      <div className="grid gap-4">
        {canManage ? (
          <table className="w-کامل text-left text-sm">
            <thead><tr className="border-b border-slate-200 text-xs text-slate-500"><th className="py-2">کاربر</th><th>نقش</th><th /></tr></thead>
            <tbody>
              {users.map((user) => (
                <tr key={user.id} className="border-b border-slate-100">
                  <td className="py-2 text-slate-800">{user.username}</td>
                  <td className="py-2">
                    <select aria-label={`نقش for ${user.username}`} value={user.role} disabled={busy}
                      onتغییر={(event) => void onAssign(user.id, event.target.value)}
                      className="rounded-lg border border-slate-300 px-2 py-1 text-xs">
                      {roles.map((role) => <option key={role} value={role}>{role}</option>)}
                    </select>
                  </td>
                  <td className="py-2 text-right text-[11px] text-slate-500">{user.دسترسی.length} دسترسی</td>
                </tr>
              ))}
              {users.length === 0 && <tr><td colSpan={3} className="py-3 text-xs text-slate-500">کاربر CMS پیدا نشد.</td></tr>}
            </tbody>
          </table>
        ) : <p className="text-sm text-slate-600">فقط مدیران می‌توانند نقش‌ها را تغییر دهند.</p>}
        <div className="overflow-auto rounded-xl border border-slate-200">
          <table className="w-کامل text-left text-[11px]">
            <thead><tr className="bg-slate-50 text-slate-600"><th className="p-2">دسترسی</th>{roles.map((role) => <th key={role} className="p-2">{role}</th>)}</tr></thead>
            <tbody>
              {Object.keys(matrix).length > 0 && (matrix[roles[0]] ?? []).concat(
                roles.flatMap((role) => matrix[role] ?? []).filter((permission) => !(matrix[roles[0]] ?? []).includes(permission)),
              ).map((permission) => (
                <tr key={permission} className="border-t border-slate-100">
                  <td className="p-2 font-mono text-slate-700">{permission}</td>
                  {roles.map((role) => <td key={role} className="p-2 text-وسط">{(matrix[role] ?? []).includes(permission) ? '✓' : '—'}</td>)}
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </Modal>
  );
}
