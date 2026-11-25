import type { ComponentDefinition } from './types';

const componentLabels: Record<string, string> = {
  hero: 'معرفی اصلی صفحه', 'rich-text': 'متن و محتوای توضیحی', 'feature-grid': 'کارت‌های معرفی ویژگی‌ها', stats: 'آمار و شاخص‌های کلیدی',
  'media-text': 'تصویر همراه با متن', quote: 'نقل‌قول برجسته', accordion: 'محتوای باز و بسته‌شونده', cta: 'دعوت به اقدام',
  gallery: 'گالری تصاویر', logos: 'لوگوی مشتریان و همکاران', 'course-list': 'فهرست دوره‌های آموزشی', 'hero-cinematic': 'معرفی تصویری ویژه',
  'bento-grid': 'چیدمان کارت‌های متنوع', pricing: 'طرح‌ها و قیمت‌ها', timeline: 'رویدادها و خط زمانی', navbar: 'منوی اصلی سایت',
  breadcrumbs: 'مسیر دسترسی صفحه', footer: 'پاورقی سایت', tabs: 'محتوای زبانه‌ای', team: 'معرفی اعضای تیم', services: 'معرفی خدمات',
  'case-studies': 'نمونه پروژه‌های اجراشده', pathways: 'مسیرهای یادگیری', 'contact-form': 'فرم تماس با ما',
  faq: 'پرسش‌های متداول', video: 'نمایش ویدیو', author: 'معرفی نویسنده', certificates: 'گواهینامه‌ها و اعتبارها',
  reviews: 'نظرات و امتیاز کاربران', 'spec-table': 'جدول مشخصات', announcement: 'نوار اطلاع‌رسانی', newsletter: 'عضویت در خبرنامه',
  checklist: 'فهرست تیک‌دار', 'related-content': 'پیشنهاد مطالب مرتبط', location: 'آدرس و اطلاعات مراجعه', curriculum: 'سرفصل‌های دوره',
  schedule: 'برنامه جلسات', 'application-form': 'فرم ثبت درخواست', 'download-gate': 'دریافت فایل با ایمیل',
  values: 'ارزش‌ها و اصول مجموعه', process: 'مراحل انجام کار', jobs: 'فرصت‌های شغلی', awards: 'جوایز و افتخارات',
  resources: 'کتابخانه منابع', glossary: 'فرهنگ اصطلاحات', 'comparison-matrix': 'جدول مقایسه گزینه‌ها',
  campaign: 'بنر کمپین تبلیغاتی', landing: 'بخش آغازین صفحه فرود', 'feature-showcase': 'معرفی تفصیلی قابلیت‌ها',
  instructor: 'معرفی مدرس', 'booking-form': 'فرم رزرو جلسه', 'team-request-form': 'درخواست آموزش سازمانی',
  'mega-menu': 'منوی چندستونه سایت', press: 'بازتاب در رسانه‌ها', partners: 'معرفی شرکای همکاری', 'content-index': 'نمایه موضوعی مطالب',
  'section-header': 'عنوان و معرفی بخش', 'social-links': 'پیوندهای شبکه‌های اجتماعی', 'cookie-consent': 'اعلان استفاده از کوکی',
  divider: 'جداکننده و فاصله‌گذار',
};

const componentDescriptions: Record<string, string> = {
  hero: 'بخش آغازین با تصویر اختیاری و دکمه‌های اقدام.',
  'rich-text': 'متن بلند با نشانه‌گذاری پاک‌سازی‌شده.',
  'feature-grid': 'مجموعه‌ای از ارزش‌های کوتاه در قالب کارت.',
  stats: 'ردیفی کوتاه از اعداد و شاخص‌های معتبر.',
  'media-text': 'تصویر در کنار متن توضیحی.',
  quote: 'نقل‌قول یا نظر یک شرکت‌کننده.',
  accordion: 'پرسش و پاسخ‌های بازشونده.',
  cta: 'نوار متمرکز برای دعوت به اقدام.',
  gallery: 'مجموعه‌ای از تصاویر با توضیح.',
  logos: 'نشان همکاران یا کارفرمایان.',
  'course-list': 'کارت‌های دوره همراه با اطلاعات تکمیلی.',
  pricing: 'انتخاب طرح با گزینهٔ شاخص و جدول مقایسه.',
  timeline: 'مسیر مرحله‌به‌مرحله با نقاط عطف.',
  navbar: 'ناوبری اصلی سایت با برند، پیوندها و اقدام.',
  breadcrumbs: 'مسیر راهنمای صفحه برای صفحات عمیق.',
  footer: 'ناوبری پایانی، اطلاعات تماس و خط حقوقی.',
  tabs: 'جابه‌جایی میان روایت‌های برنامه، روش و نتیجه.',
  team: 'معرفی مدرسان و اعضای تیم.',
  services: 'فهرست روشن خدمات و مخاطبان آن.',
  'case-studies': 'شواهد تغییر با زمینهٔ پروژه و نتایج.',
  pathways: 'مسیر پیشرفت از پایه تا سطح پیشرفته.',
  'contact-form': 'فرم تماس ساده و قابل استفاده بدون جاوااسکریپت.',
  faq: 'پرسش‌های متداول با چیدمان قابل مرور.',
  video: 'ورودی ویدیو با تصویر پوستر.',
  author: 'نام نویسنده و اعتبار محتوای تحریریه.',
  certificates: 'نشان‌های اعتبار و گواهی‌ها.',
  reviews: 'امتیازها و نظرات شرکت‌کنندگان.',
  'spec-table': 'جزئیات فنی در جدول قابل مرور.',
  announcement: 'یک پیام کوتاه و به‌روز با یک اقدام.',
  newsletter: 'فرم عضویت در خبرنامه با وعدهٔ روشن.',
  checklist: 'فهرست روشن از آنچه شامل می‌شود.',
  'related-content': 'پیشنهاد ادامهٔ مسیر از این صفحه.',
  location: 'نشانی، ساعات کاری و نکات دسترسی.',
  curriculum: 'سرفصل ماژول‌به‌ماژول با زمان و دامنه.',
  schedule: 'برنامهٔ جلسات همراه با سخنرانان.',
  'application-form': 'فرم ساختاریافتهٔ درخواست با اهداف و سابقه.',
  'download-gate': 'ارائهٔ راهنما در ازای دریافت ایمیل.',
  values: 'اصول و ارزش‌های حاکم بر مجموعه.',
  process: 'مراحل پیشرفت یک همکاری یا دوره.',
  jobs: 'موقعیت‌های شغلی باز با محل و نوع همکاری.',
  awards: 'افتخارات و نامزدهای دریافت جایزه.',
  resources: 'کتابخانه‌ای از راهنماها، قالب‌ها و مقالات.',
  glossary: 'واژگان مشترک برنامه‌ها و تمرین.',
  'comparison-matrix': 'مقایسهٔ گزینه‌ها بر اساس معیارهای تصمیم.',
  campaign: 'بنر اطلاع‌رسانی یک کمپین.',
  landing: 'بلوک کامل برای صفحهٔ فرود.',
  'feature-showcase': 'نمایش جزئیات یک ویژگی کلیدی.',
  instructor: 'معرفی مدرس و سابقهٔ او.',
  'booking-form': 'فرم رزرو جلسه یا مشاوره.',
  'team-request-form': 'فرم درخواست دورهٔ تیمی.',
  'mega-menu': 'منوی گسترده با دسته‌بندی پیوندها.',
  press: 'اشاره‌های رسانه‌ها به مجموعه.',
  partners: 'نشان و معرفی شرکای همکار.',
  'content-index': 'فهرست صفحه‌ها و مطالب.',
  'section-header': 'عنوان و توضیح آغاز یک بخش.',
  'social-links': 'پیوند به شبکه‌های اجتماعی.',
  'cookie-consent': 'اعلان استفاده از کوکی.',
  divider: 'فاصلهٔ عمودی میان بخش‌ها.',
};

const categoryLabels: Record<string, string> = {
  all: 'همهٔ دسته‌ها', content: 'متن و محتوا', media: 'تصویر و ویدیو',
  'social-proof': 'نظرات و اعتبار', conversion: 'ثبت‌نام و اقدام',
  hero: 'معرفی صفحه', bento: 'چیدمان کارت‌ها', comparison: 'مقایسه',
  timeline: 'مراحل و زمان‌بندی', navigation: 'منو و مسیریابی',
  forms: 'فرم‌ها', layout: 'چیدمان صفحه',
};

const variantLabels: Record<string, string> = {
  editorial: 'مقاله‌ای', split: 'دوتایی', centered: 'وسط‌چین', prose: 'متن', 'two-column': 'دو ستونه',
  cards: 'کارت', compact: 'فشرده', inline: 'درجا', 'image-start': 'تصویر اول', 'image-end': 'متن اول',
  statement: 'بیانیه', testimonial: 'نظر', faq: 'سؤالات متداول', plain: 'ساده', band: 'نوار', card: 'کارت',
  grid: 'شبکه', strip: 'نوار', row: 'ردیف', list: 'فهرست', overlay: 'پوشش', minimal: 'مینیمال',
  mosaic: 'موزاییک', metrics: 'شاخص‌ها', tiers: 'سطح‌ها', spotlight: 'ویژه', vertical: 'عمودی',
  alternating: 'یک‌درمیان', horizontal: 'افقی', classic: 'کلاسیک', columns: 'ستون‌ها', underline: 'زیرخط',
  pills: 'قرصی', steps: 'گام‌ها', tracks: 'مسیرها', roadmap: 'نقشهٔ راه', simple: 'ساده', divided: 'جدا',
  cinematic: 'سینمایی', ribbon: 'روبان', summary: 'خلاصه', wall: 'دیوار', table: 'جدول', rows: 'ردیف‌ها',
  bar: 'نوار', stacked: 'انباشته', numbered: 'شماره‌دار', featured: 'ویژه', outline: 'طرح کلی',
  modules: 'ماژول‌ها', agenda: 'دستور جلسه', days: 'روزها', manifesto: 'مانیفست', panel: 'پنل',
  'image-first': 'تصویر اول', 'text-first': 'متن اول',
};

const fieldLabels: Record<string, string> = {
  heading: 'عنوان', title: 'عنوان', eyebrow: 'پیش‌عنوان', lead: 'متن مقدمه', body: 'متن',
  intro: 'معرفی', items: 'آیتم‌ها', image: 'تصویر', avatar: 'تصویر پروفایل', quote: 'نقل‌قول',
  attribution: 'نام گوینده', role: 'نقش', question: 'پرسش', answer: 'پاسخ', caption: 'توضیح تصویر',
  name: 'نام', value: 'مقدار', label: 'برچسب', href: 'پیوند', primaryCta: 'دکمهٔ اصلی',
  secondaryCta: 'دکمهٔ دوم', 'primary cta': 'دکمهٔ اصلی', 'secondary cta': 'دکمهٔ دوم', cta: 'دکمهٔ اقدام', summary: 'خلاصه', meta: 'اطلاعات تکمیلی',
  features: 'ویژگی‌ها', price: 'قیمت', period: 'دوره', featured: 'شاخص', tag: 'برچسب', size: 'اندازه',
  badge: 'نشان', stats: 'آمار', links: 'پیوندها', columns: 'ستون‌ها', address: 'نشانی', hours: 'ساعات',
  note: 'یادداشت', description: 'توضیح', keywords: 'کلیدواژه‌ها', canonical: 'نشانی کانونیکال',
  ogImage: 'تصویر اجتماعی', robots: 'ربات‌ها', alt: 'متن جایگزین', url: 'نشانی', file: 'فایل',
  color: 'رنگ', order: 'ترتیب', position: 'جایگاه', email: 'ایمیل', phone: 'تلفن', date: 'تاریخ',
  time: 'زمان', location: 'مکان', duration: 'مدت', level: 'سطح', instructor: 'مدرس',
};

const statusLabels: Record<string, string> = { draft: 'پیش‌نویس', published: 'منتشرشده', scheduled: 'زمان‌بندی‌شده', unpublished: 'لغو انتشار', archived: 'آرشیو' };
const deviceLabels: Record<string, string> = { desktop: 'دسکتاپ', tablet: 'تبلت', mobile: 'موبایل' };
export const statusLabel = (value: string): string => statusLabels[value] ?? value;
export const deviceLabel = (value: string): string => deviceLabels[value] ?? value;
export const categoryLabel = (value: string): string => categoryLabels[value] ?? value;

// stage09: searchable Persian aliases for component discovery.
// Display labels and internal component IDs are intentionally unchanged.
const componentSearchAliases: Record<string, string> = {
  hero: 'بنر اصلی سربرگ کاور معرفی خوش آمدگویی',
  'hero-cinematic': 'کاور بزرگ تصویر زمینه بنر تمام صفحه',
  'rich-text': 'مقاله پاراگراف توضیحات متن بلند محتوای متنی',
  'feature-grid': 'مزایا قابلیت ها ویژگی ها کارت امکانات',
  stats: 'عدد شمارنده ارقام شاخص کلیدی',
  'media-text': 'عکس تصویر کنار متن دو ستونه',
  quote: 'سخن نقل قول شهادت مشتری',
  accordion: 'سوال جواب بازشونده کشویی',
  cta: 'دکمه اقدام ثبت نام شروع درخواست',
  gallery: 'تصاویر آلبوم عکس گالری',
  logos: 'لوگو برند مشتری سازمان همکار',
  'course-list': 'کلاس درس دوره آموزش فهرست',
  'bento-grid': 'چیدمان موزاییک کارت های نامتقارن',
  pricing: 'تعرفه هزینه پلن بسته قیمت',
  timeline: 'مراحل تاریخ رویداد روند',
  navbar: 'منو هدر ناوبری سربرگ سایت',
  breadcrumbs: 'مسیر دسترسی راهنما لینک بازگشت',
  footer: 'فوتر پاصفحه پایین صفحه اطلاعات تماس',
  tabs: 'زبانه تب تغییر محتوا',
  team: 'اعضا پرسنل همکاران تیم',
  services: 'خدمات سرویس',
  'case-studies': 'نمونه کار پروژه موفقیت تجربه مشتری',
  pathways: 'نقشه راه یادگیری رشد مهارت',
  'contact-form': 'تماس ارتباط پیام ارسال',
  faq: 'پرسش و پاسخ سوال متداول',
  video: 'ویدئو فیلم کلیپ پخش',
  author: 'مولف پدیدآورنده نویسنده',
  certificates: 'مدرک گواهی نامه اعتبار مجوز',
  reviews: 'دیدگاه بازخورد امتیاز رضایت',
  'spec-table': 'مشخصات جزئیات جدول',
  announcement: 'اطلاعیه پیام اعلان بنر اطلاع رسانی',
  newsletter: 'عضویت ایمیل خبرنامه',
  checklist: 'چک لیست موارد ویژگی های تیک دار',
  'related-content': 'پیشنهاد مطالب مرتبط بیشتر بخوانید',
  location: 'آدرس نقشه دفتر شعبه نشانی',
  curriculum: 'سرفصل برنامه آموزشی جلسات درس',
  schedule: 'تقویم زمان کلاس برنامه جلسات',
  'application-form': 'ثبت نام تقاضا فرم درخواست',
  'download-gate': 'دانلود فایل دریافت ایمیل',
  values: 'فرهنگ سازمان اصول ارزش',
  process: 'مراحل فرایند گردش کار قدم ها',
  jobs: 'استخدام همکاری شغل فرصت',
  awards: 'افتخار تقدیر جایزه',
  resources: 'کتابخانه دانلود مقاله ابزار منبع',
  glossary: 'واژه نامه اصطلاح تعریف لغت',
  'comparison-matrix': 'مقایسه جدول ویژگی تفاوت گزینه',
  campaign: 'تبلیغ کمپین بنر رویداد',
  landing: 'لندینگ فرود آغاز کمپین',
  'feature-showcase': 'امکانات مزایا توضیح تفصیلی',
  instructor: 'استاد مدرس معلم پروفایل',
  'booking-form': 'رزرو نوبت جلسه مشاوره',
  'team-request-form': 'درخواست آموزش سازمانی تیم گروه',
  'mega-menu': 'منوی بزرگ چند ستونه ناوبری',
  press: 'خبر رسانه روابط عمومی نشریه',
  partners: 'شریک همکار همکاری تجاری',
  'content-index': 'فهرست مطالب نمایه دسته بندی مقاله',
  'section-header': 'تیتر عنوان شروع بخش سرصفحه',
  'social-links': 'اینستاگرام تلگرام لینکدین شبکه اجتماعی',
  'cookie-consent': 'کوکی حریم خصوصی موافقت',
  divider: 'فاصله خط جداکننده فضای خالی',
};

function normalizeComponentQuery(value: string): string {
  return value.toLocaleLowerCase()
    .replace(/[يى]/g, 'ی')
    .replace(/ك/g, 'ک')
    .replace(/[\u064B-\u065F\u0670\u200c\u200d]/g, ' ')
    .replace(/[^a-z0-9\u0600-\u06ff]+/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}

/** Match words across title, summary, category, variant names and useful aliases. */
export function componentMatchesSearch(component: ComponentDefinition, query: string): boolean {
  const words = normalizeComponentQuery(query).split(' ').filter(Boolean);
  if (words.length === 0) return true;
  const haystack = normalizeComponentQuery([
    component.label,
    component.description,
    component.type,
    categoryLabel(component.category),
    ...component.variants.map((variant) => variant.label),
    componentSearchAliases[component.type] ?? '',
  ].join(' '));
  return words.every((word) => haystack.includes(word));
}

export const variantLabel = (value: string, fallback = value): string => variantLabels[value] ?? fallback;
export const fieldLabel = (leaf: string): string => {
  const key = leaf.replace(/([a-z0-9])([A-Z])/g, '$1 $2').replace(/_/g, ' ').toLowerCase();
  return fieldLabels[key] ?? leaf;
};

export const componentLabel = (value: string, fallback: string): string => componentLabels[value] ?? componentLabels[value.toLowerCase()] ?? fallback;

export function localizeComponent(definition: ComponentDefinition): ComponentDefinition {
  return {
    ...definition,
    label: componentLabels[definition.type] ?? definition.label,
    description: componentDescriptions[definition.type] ?? 'کامپوننت آماده برای ساخت این بخش.',
    variants: definition.variants.map((variant) => ({ ...variant, label: variantLabel(variant.id, variant.label) })),
  };
}


const templateNames: Record<string, string> = {
  'About the academy': 'دربارهٔ آکادمی',
  'Academy landing': 'صفحهٔ فرود آکادمی',
  Admissions: 'پذیرش',
  'Content hub': 'مرکز محتوا',
  'Course launch': 'راه‌اندازی دوره',
  'Editorial starter': 'شروع تحریریه',
  'Workshop detail': 'جزئیات کارگاه',
};

const templateDescriptions: Record<string, string> = {
  'About the academy': 'داستان، تیم و آمار یک صفحهٔ «دربارهٔ ما»ی سازمانی.',
  'Academy landing': 'صفحهٔ فرود متمرکز بر تبدیل با خدمات و پرسش‌های متداول.',
  Admissions: 'قیمت‌گذاری، شرایط و مسیر روشن درخواست.',
  'Content hub': 'صفحهٔ فهرست تحریریه برای راهنماها و منابع بلند.',
  'Course launch': 'شروع یک دوره: هیرو، اعتماد، برنامهٔ درسی، مدرس و نظرات.',
  'Editorial starter': 'شروعی تحریریه برای صفحه‌های محتوایی.',
  'Workshop detail': 'صفحهٔ یک کارگاه با برنامه، مدرس و اعتماد اجتماعی.',
};

const templateCategories: Record<string, string> = {
  all: 'همه', company: 'سازمانی', content: 'محتوا', conversion: 'تبدیل',
  event: 'رویداد', general: 'عمومی', marketing: 'بازاریابی',
};

export const templateName = (value: string): string => templateNames[value] ?? value;
export const templateDescription = (name: string, fallback: string): string => templateDescriptions[name] ?? fallback;
export const templateCategoryLabel = (value: string): string => templateCategories[value] ?? value;


const roleLabels: Record<string, string> = { admin: 'مدیر', administrator: 'مدیر', editor: 'ویرایشگر', viewer: 'بیننده', developer: 'توسعه‌دهنده', designer: 'طراح', reviewer: 'بازبین', publisher: 'منتشرکننده' };
const permissionLabels: Record<string, string> = {
  read: 'مشاهده', edit: 'ویرایش', publish: 'انتشار', manage_users: 'مدیریت کاربران', manage: 'مدیریت',
  'pages.create': 'ساخت صفحه', 'pages.edit': 'ویرایش صفحه', 'pages.restore': 'بازگردانی صفحه',
  'pages.route': 'مسیر صفحه', 'pages.publish': 'انتشار صفحه',
  'pages.unpublish': 'لغو انتشار صفحه', 'pages.archive': 'آرشیو صفحه', 'design.edit': 'ویرایش طراحی',
  'components.manage': 'مدیریت کامپوننت‌ها', 'templates.manage': 'مدیریت قالب‌ها',
  'media.upload': 'بارگذاری رسانه', 'media.manage': 'مدیریت رسانه', 'preview.draft': 'پیش‌نمایش پیش‌نویس',
  'seo.manage': 'مدیریت سئو', 'redirects.manage': 'مدیریت تغییرمسیرها', 'users.manage': 'مدیریت کاربران',
  'audit.read': 'مشاهدهٔ گزارش رویدادها',
};
export const roleLabel = (value: string): string => roleLabels[value] ?? value;
export const permissionLabel = (value: string): string => permissionLabels[value] ?? value;


const sectionLabels: Record<string, string> = { Introduction: 'مقدمه', 'New section': 'بخش جدید' };
export const sectionLabel = (value: string): string => sectionLabels[value] ?? value;


const issueMessages: Record<string, string> = {
  'Internal link does not match a published route: /contact': 'پیوند داخلی با مسیر منتشرشده‌ای مطابقت ندارد: /contact',
};
export const issueMessage = (value: string): string => {
  if (issueMessages[value]) return issueMessages[value];
  const internal = /^Internal link does not match a published route: (.+)$/.exec(value);
  if (internal) return `پیوند داخلی با مسیر منتشرشده‌ای مطابقت ندارد: ${internal[1]}`;
  return value;
};
