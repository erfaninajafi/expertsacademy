/** Shared contracts. These mirror src/Contract/*.php and the registry schema. */
export const PAGE_SCHEMA_VERSION = 1 as const;

export type Locale = 'fa' | 'en';
export type Direction = 'rtl' | 'ltr';
export type Device = 'desktop' | 'tablet' | 'mobile';

export interface MediaRef {
  id: string;
  alt?: string;
}

export type TokenMode = 'light' | 'dark';

export interface ThemeTokens {
  schemaVersion: 2;
  name: string;
  mode: TokenMode;
  colors: Record<TokenMode, Record<string, string>>;
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
  shadow: 'soft' | 'crisp' | 'none';
  shadows?: { sm: string; md: string; lg: string };
  button: {
    height: number;
    weight: number;
    style: 'solid' | 'outline';
    radius?: 'small' | 'medium' | 'large' | 'xl' | 'قرصی';
    paddingX?: number;
  };
  background: { pattern: 'none' | 'grid' | 'grain'; gradient: 'none' | 'dawn' | 'midnight'; overlay: number };
  gradients?: Record<'dawn' | 'midnight' | 'sand' | 'forest', { from: string; to: string; angle: number }>;
  motion: {
    preset: 'none' | 'reveal' | 'stagger' | 'emphasis';
    enabled: boolean;
    duration: number;
    spacing: number;
    easing: 'calm' | 'snappy';
    presets?: Record<'muted' | 'rise' | 'slide' | 'scale', { duration: number; spacing: number }>;
  };
  rtl?: { fontScale: number; letterspacing: number };
}

export interface Seo {
  title: string;
  description: string;
  keywords: string[];
  canonical: string;
  ogImage: MediaRef | null;
  robots?: 'index,follow' | 'noindex,follow' | 'index,nofollow' | 'noindex,nofollow';
}

export interface sectionLayout {
  width: 'contained' | 'full';
  columns: number;
  gap: number;
  paddingY: number;
  background: 'none' | 'canvas' | 'surface' | 'accent';
}

export interface Visibility {
  devices: Device[];
}

export interface DesignProps {
  align: 'start' | 'center' | 'end';
  tone: 'default' | 'inverse' | 'accent';
  spacing: 'sm' | 'md' | 'lg';
}

export interface ResponsiveOverride {
  columns?: number;
  align?: 'start' | 'center' | 'end';
  size?: 'sm' | 'md' | 'lg';
  hide?: boolean;
}

export interface Animation {
  preset: 'none' | 'muted' | 'rise' | 'stagger';
  duration: number;
  delay: number;
  easing: 'standard' | 'calm';
}

export interface A11y {
  landmark: 'none' | 'region' | 'complementary';
  label: string;
  headingLevel: 2 | 3 | 4;
}

export interface PageComponent {
  id: string;
  type: string;
  version: number;
  variant: string;
  props: Record<string, unknown>;
  design: DesignProps;
  responsive: Partial<Record<Device, ResponsiveOverride>>;
  animation: Animation;
  visibility: Visibility;
  a11y: A11y;
}

export interface PageSection {
  id: string;
  label: string;
  layout: sectionLayout;
  visibility: Visibility;
  components: PageComponent[];
}

export interface PageDocument {
  schemaVersion: typeof PAGE_SCHEMA_VERSION;
  title: string;
  locale: Locale;
  direction: Direction;
  seo: Seo;
  theme: { id: string | null; version: number | null; tokens: ThemeTokens };
  settings: { container: number; background: 'canvas' | 'surface' | 'muted' };
  sections: PageSection[];
}

export interface RevisionSummary {
  id: string;
  revision_number: number;
  checksum: string;
  created_by: string;
  created_at: string;
}

export interface PageRecord {
  id: string;
  title: string;
  slug: string | null;
  locale: Locale;
  direction: Direction;
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
  updatedAt: string;
  document: PageDocument | null;
  revisions: RevisionSummary[];
}

export interface PageSummary {
  id: string;
  title: string;
  locale: Locale;
  slug?: string | null;
  legacyCode: string | null;
  version: number;
  status: 'draft' | 'published' | 'scheduled' | 'unpublished' | 'archived';
  publishedAt: string | null;
  scheduledAt?: string | null;
  updatedAt: string;
}

export type PropSchema =
  | { type: 'string' | 'text' | 'richtext'; max: number; required?: boolean }
  | { type: 'url'; required?: boolean }
  | { type: 'enum'; values: string[]; required?: boolean }
  | { type: 'media'; required?: boolean }
  | { type: 'object'; fields: Record<string, PropSchema>; required?: boolean }
  | { type: 'list'; min?: number; max?: number; item: Record<string, PropSchema>; required?: boolean };

export interface variantsDefinition {
  id: string;
  label: string;
}

export interface ComponentDefinition {
  type: string;
  version: number;
  label: string;
  category: string;
  description: string;
  variants: variantsDefinition[];
  schema: Record<string, PropSchema>;
  defaultProps: Record<string, unknown>;
  status: 'stable' | 'deprecated' | 'experimental';
  container: boolean;
}

export interface ComponentMeta {
  status: 'stable' | 'experimental' | 'deprecated';
  tags: string[];
  docs: string;
  a11y: string;
  animation: { preset: 'none' | 'muted' | 'rise' | 'slide' | 'scale' | 'stagger'; duration?: number };
  updated_by: string | null;
  updated_at: string | null;
}

export interface StudioComponent extends ComponentDefinition {
  meta: ComponentMeta;
}

export interface ComponentContract {
  id: string;
  version: number;
  schema: Record<string, PropSchema>;
  variants: variantsDefinition[];
  defaults: Record<string, unknown>;
  responsive: { breakpoints: string[] };
  rtl: boolean;
  renderer: 'php';
  assets: { css: string };
  a11y: string;
  animation: ComponentMeta['animation'];
  docs: string;
  status: ComponentMeta['status'];
}

export interface ComponentPreset {
  id: string;
  type_key: string;
  name: string;
  variant: string;
  props: string;
  created_by: string;
  created_at: string;
}

export interface ComponentVersion {
  type_key: string;
  version: number;
  label: string;
  notes: string;
  released_at: string;
}

export interface DesignDraft {
  id: string;
  name: string;
  locale: Locale;
  base_theme_id: string | null;
  tokens: ThemeTokens;
  checksum: string;
  version: number;
  status: 'draft' | 'published';
  updated_by: string;
  updated_at: string;
}

export interface DesignHistoryEntry {
  id: string;
  draft_id: string;
  theme_id: string;
  tokens_version: number;
  locale: Locale;
  checksum: string;
  published_at: string;
  published_by: string;
  name: string;
  document: string;
}

export interface DesignPayload {
  drafts: Array<Pick<DesignDraft, 'id' | 'name' | 'locale' | 'version' | 'status' | 'updated_by' | 'updated_at'>>;
  working: DesignDraft;
  history: DesignHistoryEntry[];
  current: { id: string; version: number; name: string; tokens: ThemeTokens } | null;
  locales: Locale[];
}

export interface ThemeRecord {
  id: string;
  version: number;
  name: string;
  locale: Locale;
  is_default: number | string;
  status: string;
  created_at: string;
  tokens_id: string;
  tokens_version: number;
  document: string;
}

export interface MediaItem {
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

export interface MediaRecord extends MediaItem {
  storage_key: string;
  size_bytes: number;
  created_at: string;
}

export interface Actor {
  id: string;
  name: string;
  capabilities: Array<'read' | 'edit' | 'publish'>;
  role?: string;
  permissions?: string[];
}

export interface StudioConfig {
  mode: 'pages';
  api: string;
  preview: string;
  componentPreview: string;
  csrf: string;
  actor: { id: string; name: string; role?: string; permissions?: string[] };
  capabilities: Actor['capabilities'];
  role: string;
  permissions: string[];
  schemaVersion: number;
  registryVersion: number;
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

export interface RoleUser {
  id: string;
  username: string;
  role: string;
  permissions: string[];
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

export interface TemplateDetail extends TemplateSummary {
  document: PageDocument;
}
