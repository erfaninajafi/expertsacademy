import type { PageDocument, QualityIssue } from '../types';
import { componentLabel, fieldLabel, sectionLabel } from '../i18n';

const reasons: Record<string, [string, string]> = {
  'title.missing': ['عنوان صفحه خالی است.', 'در تنظیمات صفحه، عنوان را وارد کنید.'],
  'route.missing': ['آدرس عمومی برای صفحه ثبت نشده است.', 'در قسمت «آدرس عمومی»، مسیر صفحه را ثبت کنید.'],
  'seo.title.missing': ['عنوان مخصوص موتورهای جست‌وجو وارد نشده است.', 'در تنظیمات سئو، عنوان سئو را تکمیل کنید.'],
  'seo.title.long': ['عنوان سئو بیش از ۶۰ نویسه است و ممکن است در نتایج جست‌وجو کوتاه شود.', 'عنوان سئو را کوتاه‌تر کنید.'],
  'seo.description.missing': ['توضیحات سئو خالی است.', 'برای صفحه یک توضیح کوتاه و مفید بنویسید.'],
  'seo.description.length': ['طول توضیحات سئو خارج از بازه پیشنهادی ۵۰ تا ۱۶۰ نویسه است.', 'متن توضیحات سئو را در این بازه تنظیم کنید.'],
  'seo.canonical.invalid': ['آدرس اصلی صفحه معتبر یا کامل نیست.', 'نشانی کامل با https:// وارد کنید یا فیلد را خالی بگذارید.'],
  'seo.image.missing': ['تصویر اشتراک‌گذاری اجتماعی در کتابخانه رسانه پیدا نشد.', 'تصویر موجود دیگری انتخاب کنید.'],
  'seo.image.alt': ['تصویر اشتراک‌گذاری متن جایگزین ندارد.', 'برای تصویر، متن جایگزین توصیفی بنویسید.'],
  'seo.title.duplicate': ['عنوان سئو با یکی دیگر از صفحات یکسان است.', 'عنوانی منحصربه‌فرد برای این صفحه تعیین کنید.'],
  'seo.description.duplicate': ['توضیحات سئو با صفحه دیگری یکسان است.', 'توضیحات اختصاصی برای این صفحه بنویسید.'],
  'schema.unsupported': ['نسخه ساختار سند صفحه با سامانه فعلی سازگار نیست.', 'ساختار این صفحه باید به نسخه پشتیبانی‌شده ارتقا یابد؛ بدون بررسی، آن را منتشر نکنید.'],
  'content.empty': ['هیچ بلوکی در صفحه وجود ندارد.', 'حداقل یک بلوک محتوایی به یکی از بخش‌ها اضافه کنید.'],
  'component.unsupported': ['نوع این بلوک در فهرست کامپوننت‌های سامانه پشتیبانی نمی‌شود.', 'بلوک را با یک کامپوننت معتبر جایگزین کنید.'],
  'component.version': ['نسخه ذخیره‌شده این کامپوننت با نسخه فعلی آن یکسان نیست.', 'کامپوننت را به نسخه جدید ارتقا دهید یا آن را دوباره ایجاد کنید.'],
  'component.variant': ['مدل نمایشی این کامپوننت معتبر نیست.', 'در تنظیمات بلوک، یک مدل نمایشی معتبر انتخاب کنید.'],
  'component.duplicate': ['شناسه این کامپوننت با بلوک دیگری تکراری است.', 'بلوک معیوب را بازسازی کنید تا شناسه یکتا بگیرد.'],
  'component.invisible': ['این بلوک در همه دستگاه‌ها پنهان شده است.', 'در تنظیمات نمایش بلوک، حداقل یک دستگاه را فعال کنید.'],
  'media.missing': ['رسانه انتخاب‌شده پیدا نمی‌شود و در صفحه جای خالی نمایش داده خواهد شد.', 'در همین فیلد، یک تصویر موجود انتخاب کنید.'],
  'media.alt.missing': ['تصویر متن جایگزین ندارد.', 'متن جایگزین مناسب برای تصویر وارد کنید.'],
  'media.oversized': ['حجم تصویر بیشتر از ۲ مگابایت است.', 'تصویر را فشرده و فایل بهینه‌شده را انتخاب کنید.'],
  'heading.hierarchy': ['ترتیب سطح تیترها رعایت نشده و یکی از سطوح عنوان رد شده است.', 'سطح این تیتر را متناسب با عنوان قبلی اصلاح کنید.'],
  'link.unresolved': ['پیوند داخلی به هیچ مسیر منتشرشده‌ای اشاره نمی‌کند.', 'آدرس پیوند را بررسی کنید یا ابتدا صفحه مقصد را منتشر کنید.'],
};

function issueReason(issue: QualityIssue): string {
  const reason = reasons[issue.code]?.[0];
  if (!reason) return `برای این مورد بررسی بیشتری لازم است (شناسه بررسی: ${issue.code}).`;
  if (issue.code === 'link.unresolved') {
    const match = /^Internal link does not match a published route: (.*)$/.exec(issue.message);
    if (match) return `پیوند «${match[1]}» به صفحه منتشرشده معتبری اشاره نمی‌کند.`;
  }
  if (issue.code === 'heading.hierarchy') {
    const match = /h(\d+) to h(\d+)/.exec(issue.message);
    if (match) return `سطح تیتر از ${match[1]} به ${match[2]} پریده است و ترتیب رعایت نشده است.`;
  }
  if (issue.code === 'component.version') {
    const match = /needs version (\d+)/.exec(issue.message);
    if (match) return `این کامپوننت باید از نسخه ${match[1]} استفاده کند.`;
  }
  if (issue.code === 'seo.title.duplicate' || issue.code === 'seo.description.duplicate') {
    const match = /:\s*(.+)$/.exec(issue.message);
    if (match) return `${reason} صفحه مشابه: «${match[1]}».`;
  }
  return reason;
}

function where(issue: QualityIssue, document: PageDocument | null): string {
  const path = issue.path || '';
  const sectionMatch = /sections\[(\d+)\]/.exec(path);
  const blockMatch = /\.components\[(\d+)\]/.exec(path);
  const parts: string[] = [];
  if (sectionMatch) {
    const n = Number(sectionMatch[1]);
    const section = document?.sections[n];
    parts.push(`بخش ${n + 1}${section ? ` «${sectionLabel(section.label)}»` : ''}`);
    if (blockMatch) {
      const k = Number(blockMatch[1]);
      const component = section?.components[k];
      const title = component ? componentLabel(component.type, component.type) : 'نامشخص';
      parts.push(`بلوک ${k + 1} «${title}»`);
    }
  } else if (path === 'sections') return 'محتوای صفحه ← بخش‌ها و بلوک‌ها';
  else if (path.startsWith('seo.')) parts.push('تنظیمات صفحه', 'سئو');
  else if (path === 'title') return 'تنظیمات صفحه ← عنوان صفحه';
  else if (path === 'route') return 'بررسی و انتشار ← آدرس عمومی';
  else if (path === 'schemaVersion') return 'ساختار فنی سند صفحه';
  else parts.push('تنظیمات صفحه');

  let suffix = path;
  if (sectionMatch && blockMatch) suffix = path.replace(/^sections\[\d+\]\.components\[\d+\]\.?/, '');
  else if (sectionMatch) suffix = path.replace(/^sections\[\d+\]\.?/, '');
  else if (path.startsWith('seo.')) suffix = path.slice(4);
  if (suffix === 'props') suffix = '';
  const special: Record<string, string> = {
    props: 'محتوا', seo: 'سئو', version: 'نسخه کامپوننت', variant: 'مدل نمایش',
    visibility: 'تنظیمات نمایش', devices: 'دستگاه‌ها', a11y: 'دسترس‌پذیری',
    headingLevel: 'سطح تیتر', id: 'شناسه', settings: 'تنظیمات صفحه',
    href: 'پیوند', ogImage: 'تصویر اشتراک‌گذاری', alt: 'متن جایگزین',
  };
  const tokens = suffix.replace(/\[(\d+)\]/g, '.$1').split('.').filter(Boolean);
  for (const token of tokens) {
    if (token === 'props') continue;
    if (/^\d+$/.test(token)) parts.push(`آیتم ${Number(token) + 1}`);
    else parts.push(special[token] ?? fieldLabel(token));
  }
  return parts.join(' ← ');
}

export function PublishIssueDetails({ issues, document, kind }: {
  issues: QualityIssue[];
  document: PageDocument | null;
  kind: 'error' | 'warning';
}) {
  if (issues.length === 0) return null;
  const error = kind === 'error';
  return (
    <section aria-label={error ? 'خطاهای بازدارنده' : 'هشدارهای انتشار'} className="grid gap-2">
      <h4 className={`text-xs font-bold ${error ? 'text-rose-800' : 'text-amber-900'}`}>
        {error ? `خطاهای بازدارنده (${issues.length})` : `هشدارهای قابل بررسی (${issues.length})`}
      </h4>
      {issues.map((issue, index) => (
        <article key={`${issue.code}-${issue.path}-${index}`} className={`rounded-xl border p-3 ${error ? 'border-rose-200 bg-rose-50 text-rose-950' : 'border-amber-200 bg-amber-50 text-amber-950'}`}>
          <div className="flex items-start gap-2">
            <span aria-hidden="true" className={`flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[11px] font-bold ${error ? 'bg-rose-200 text-rose-900' : 'bg-amber-200 text-amber-900'}`}>
              {error ? '×' : '!'}
            </span>
            <div className="min-w-0 flex-1 space-y-1.5">
              <p className="text-xs font-semibold leading-6">{where(issue, document)}</p>
              <p className="text-xs leading-6"><span className="font-bold">علت: </span>{issueReason(issue)}</p>
              <p className="text-xs leading-6"><span className="font-bold">راه‌حل: </span>{reasons[issue.code]?.[1] ?? 'تنظیمات این قسمت را بررسی کنید؛ در صورت نیاز پیام فنی را در اختیار توسعه‌دهنده قرار دهید.'}</p>
              {!reasons[issue.code] && issue.message && (
                <details className="text-[11px]"><summary className="cursor-pointer">جزئیات فنی برای پشتیبانی</summary>
                  <code dir="ltr" className="mt-1 block break-all text-left">{issue.message}</code>
                </details>
              )}
            </div>
          </div>
        </article>
      ))}
    </section>
  );
}
