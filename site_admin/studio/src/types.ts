/** Shared contracts. These mirror src/قرارداد/*.php and the رجیستری طرح‌واره. */
export const PAGE_SCHEMA_VERSION = 1 as const;

export type زبان = 'fa' | 'en';
export type جهت = 'rtl' | 'ltr';
export type Device = 'desktop' | 'tablet' | 'mobile';

export interface رسانهRef {
  id: string;
  alt?: string;
}

export type Tokenحالت = 'روشن' | 'تیره';

export interface ThemeTokens {
  طرح‌وارهنسخه: 2;
  name: string;
  mode: Tokenحالت;
  colors: Record<Tokenحالت, Record<string, string>>;
  typography: {
    latin: 'editorial' | 'academy' | 'manrope' | 'system';
    rtl: 'editorial' | 'academy' | 'manrope' | 'system';
    base: number;
    display: number;
    heading: number;
    scale: number;
    lineHeight: number;
    weights?: { regular: number; medium: number; semibold: number; bold: number };
    tracking?: { display: number; body: number };
    fluid?: { min: number; max: number };
  };
  spacing: { unit: number; section: number; card: number; gap: number; scale?: number[] };
  layout: {
    container: number;
    reading: number;
    columns: number;
    wide?: number;
    narrow?: number;
    grid?: { gutter: number };
    breakpoints: { mobile: number; tablet: number; desktop: number };
  };
  radius: { small: number; medium: number; large: number; xl?: number; pill?: number };
  shadow: 'نرم' | 'تیز' | 'هیچ';
  shadows?: { sm: string; md: string; lg: string };
  button: {
    height: number;
    weight: number;
    style: 'solid' | 'outline';
    radius?: 'small' | 'medium' | 'large' | 'xl' | 'pill';
    paddingX?: number;
  };
  background: { pattern: 'هیچ' | 'grid' | 'grain'; gradient: 'هیچ' | 'dawn' | 'midnight'; overlay: number };
  gradients?: Record<'dawn' | 'midnight' | 'sand' | 'forest', { from: string; to: string; angle: number }>;
  motion: {
    preset: 'هیچ' | 'reveal' | 'پله‌ای' | 'emphasis';
    enabled: boolean;
    مدت: number;
    فاصله: number;
    easing: 'آرام' | 'snappy';
    presets?: Record<'محو' | 'بالا آمدن' | 'slide' | 'scale', { مدت: number; فاصله: number }>;
  };
  rtl?: { fontScale: number; letterفاصله: number };
}

export interface Seo {
  title: string;
  description: string;
  keywords: string[];
  canonical: string;
  ogImage: رسانهRef | null;
  robots?: 'index,follow' | 'noindex,follow' | 'index,nofollow' | 'noindex,nofollow';
}

export interface بخشLayout {
  width: 'محدود' | 'کامل';
  columns: number;
  gap: number;
  paddingY: number;
  background: 'هیچ' | 'بوم' | 'سطح' | 'تأکیدی';
}

export interface Visibility {
  devices: Device[];
}

export interface طراحیProps {
  align: 'شروع' | 'وسط' | 'پایان';
  tone: 'پیش‌فرض' | 'معکوس' | 'تأکیدی';
  spacing: 'sm' | 'md' | 'lg';
}

export interface واکنش‌گراOverride {
  columns?: number;
  align?: 'شروع' | 'وسط' | 'پایان';
  size?: 'sm' | 'md' | 'lg';
  hide?: boolean;
}

export interface Animation {
  preset: 'هیچ' | 'محو' | 'بالا آمدن' | 'پله‌ای';
  مدت: number;
  delay: number;
  easing: 'استاندارد' | 'آرام';
}

export interface A11y {
  landmark: 'هیچ' | 'ناحیه' | 'تکمیلی';
  label: string;
  headingLevel: 2 | 3 | 4;
}

export interface صفحهComponent {
  id: string;
  type: string;
  version: number;
  variant: string;
  props: Record<string, unknown>;
  design: طراحیProps;
  responsive: Partial<Record<Device, واکنش‌گراOverride>>;
  animation: Animation;
  نمایش: Visibility;
  a11y: A11y;
}

export interface صفحهبخش {
  id: string;
  label: string;
  layout: بخشLayout;
  نمایش: Visibility;
  components: صفحهComponent[];
}

export interface صفحهDocument {
  طرح‌وارهنسخه: typeof PAGE_SCHEMA_VERSION;
  title: string;
  locale: زبان;
  direction: جهت;
  seo: Seo;
  theme: { id: string | null; version: number | null; tokens: ThemeTokens };
  settings: { container: number; background: 'بوم' | 'سطح' | 'محو' };
  بخش: صفحهبخش[];
}

export interface RevisionSummary {
  id: string;
  revision_number: number;
  checksum: string;
  created_by: string;
  created_at: string;
}

export interface صفحهRecord {
  id: string;
  title: string;
  slug: string | null;
  locale: زبان;
  direction: جهت;
  legacyCode: string | null;
  version: number;
  status: 'draft' | 'published' | 'scheduled' | 'unpublished' | 'archived';
  draftRevision: string | null;
  publishedRevision: string | null;
  publishedAt: string | null;
  scheduledAt?: string | null;
  archivedAt?: string | null;
  schedules?: Array<{ id: string; status: string; publish_at: string; attempts: number; last_error: string | null }>;
  createdAt: string;
  به بالاdatedAt: string;
  document: صفحهDocument | null;
  revisions: RevisionSummary[];
}

export interface صفحهSummary {
  id: string;
  title: string;
  locale: زبان;
  slug?: string | null;
  legacyCode: string | null;
  version: number;
  status: 'draft' | 'published' | 'scheduled' | 'unpublished' | 'archived';
  publishedAt: string | null;
  scheduledAt?: string | null;
  به بالاdatedAt: string;
}

export type PropSchema =
  | { type: 'string' | 'text' | 'richtext'; max: number; required?: boolean }
  | { type: 'url'; required?: boolean }
  | { type: 'enum'; values: string[]; required?: boolean }
  | { type: 'media'; required?: boolean }
  | { type: 'object'; fields: Record<string, PropSchema>; required?: boolean }
  | { type: 'list'; min?: number; max?: number; item: Record<string, PropSchema>; required?: boolean };

export interface گونهDefinition {
  id: string;
  label: string;
}

export interface ComponentDefinition {
  type: string;
  version: number;
  label: string;
  category: string;
  description: string;
  گونه: گونهDefinition[];
  طرح‌واره: Record<string, PropSchema>;
  پیش‌فرضProps: Record<string, unknown>;
  status: 'stable' | 'deprecated' | 'experimental';
  container: boolean;
}

export interface ComponentMeta {
  status: 'stable' | 'experimental' | 'deprecated';
  tags: string[];
  docs: string;
  a11y: string;
  animation: { preset: 'هیچ' | 'محو' | 'بالا آمدن' | 'slide' | 'scale' | 'پله‌ای'; مدت?: number };
  به بالاdated_by: string | null;
  به بالاdated_at: string | null;
}

export interface StudioComponent extپایانs ComponentDefinition {
  meta: ComponentMeta;
}

export interface Componentقرارداد {
  id: string;
  version: number;
  طرح‌واره: Record<string, PropSchema>;
  گونه: گونهDefinition[];
  پیش‌فرضs: Record<string, unknown>;
  responsive: { breakpoints: string[] };
  rtl: boolean;
  rپایانerer: 'php';
  assets: { css: string };
  a11y: string;
  animation: ComponentMeta['animation'];
  docs: string;
  status: ComponentMeta['status'];
}

export interface Componentپیش‌تنظیم {
  id: string;
  type_key: string;
  name: string;
  variant: string;
  props: string;
  created_by: string;
  created_at: string;
}

export interface Componentنسخه {
  type_key: string;
  version: number;
  label: string;
  notes: string;
  released_at: string;
}

export interface طراحیDraft {
  id: string;
  name: string;
  locale: زبان;
  base_theme_id: string | null;
  tokens: ThemeTokens;
  checksum: string;
  version: number;
  status: 'draft' | 'published';
  به بالاdated_by: string;
  به بالاdated_at: string;
}

export interface طراحیتاریخچهEntry {
  id: string;
  draft_id: string;
  theme_id: string;
  tokens_version: number;
  locale: زبان;
  checksum: string;
  published_at: string;
  published_by: string;
  name: string;
  document: string;
}

export interface طراحیPayload {
  drafts: Array<Pick<طراحیDraft, 'id' | 'name' | 'locale' | 'version' | 'status' | 'به بالاdated_by' | 'به بالاdated_at'>>;
  working: طراحیDraft;
  history: طراحیتاریخچهEntry[];
  current: { id: string; version: number; name: string; tokens: ThemeTokens } | null;
  locales: زبان[];
}

export interface ThemeRecord {
  id: string;
  version: number;
  name: string;
  locale: زبان;
  is_پیش‌فرض: number | string;
  status: string;
  created_at: string;
  tokens_id: string;
  tokens_version: number;
  document: string;
}

export interface رسانهآیتم {
  id: string;
  original_name: string;
  mime_type: string;
  alt_text: string;
  url: string;
  width: number | null;
  height: number | null;
  focal_x?: number;
  focal_y?: number;
}

export interface رسانهRecord extپایانs رسانهآیتم {
  storage_key: string;
  size_bytes: number;
  created_at: string;
}

export interface Actor {
  id: string;
  name: string;
  capabilities: Array<'read' | 'edit' | 'publish'>;
  role?: string;
  دسترسی?: string[];
}

export interface StudioConfig {
  mode: 'pages';
  api: string;
  preview: string;
  componentپیش‌نمایش: string;
  csrf: string;
  actor: { id: string; name: string; role?: string; دسترسی?: string[] };
  capabilities: Actor['capabilities'];
  role: string;
  دسترسی: string[];
  طرح‌وارهنسخه: number;
  رجیسترینسخه: number;
}

export interface QualityIssue {
  code: string;
  message: string;
  path: string;
}

export interface QualityChecks {
  ok: boolean;
  errorCount: number;
  warningCount: number;
  errors: QualityIssue[];
  warnings: QualityIssue[];
  checkedAt: string;
}

export interface نقشکاربر {
  id: string;
  username: string;
  role: string;
  دسترسی: string[];
}

export interface ValidationResult {
  valid: boolean;
  errors: Record<string, string>;
}

export interface TemplateSummary {
  id: string;
  version: number;
  name: string;
  category: string;
  description: string;
  sectionCount: number;
  favorite: boolean;
  createdAt: string;
}

export interface TemplateDetail extپایانs TemplateSummary {
  document: صفحهDocument;
}
