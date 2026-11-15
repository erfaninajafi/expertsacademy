import type { PropSchema } from './نوع';

export interface InlineField {
  path: string;
  label: string;
  طرح‌واره: PropSchema;
}

const LABELS: Record<string, string> = { cta: 'CTA', seo: 'SEO', url: 'URL', id: 'ID', href: 'href' };

/** Human label for a dotted prop path, e.g. `primaryCta.label` → `Primary CTA label`. */
export function fieldبرچسب(path: string): string {
  const parts = path.split('.');
  const leaf = parts[parts.length - 1] ?? path;
  const human = (value: string): string => {
    const spaced = value.replace(/([a-z0-9])([A-Z])/g, '$1 $2').replace(/_/g, ' ');
    return spaced
      .split(' ')
      .map((word) => LABELS[word.toLowerCase()] ?? word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  };
  return parts.length > 1 ? `${human(parts[parts.length - 2] ?? '')} ${human(leaf)}` : human(leaf);
}

/** Mirrors Sanitizer::isSafeUrl in PHP so inline editing fails at the same boundary. */
export function isSafeUrl(value: string): boolean {
  if (value === '') return false;
  if (/^\/(?!\/)[A-Za-z0-9._~!$&'()*+,;=:@%/?#-]*$/.test(value)) return true;
  if (/^#[A-Za-z0-9_-]{1,80}$/.test(value)) return true;
  if (/^https?:\/\/[^\s"'<>]{1,2000}$/i.test(value)) return true;
  return /^(mailto|tel):[^\s"'<>]{1,200}$/i.test(value);
}

/** The same rules the server applies to string, text, URL and media props. */
export function propError(طرح‌واره: PropSchema, value: unknown): string | null {
  const required = طرح‌واره.required === true;
  if (طرح‌واره.type === 'string' || طرح‌واره.type === 'text' || طرح‌واره.type === 'richtext') {
    if (typeof value !== 'string') return 'Expected a string.';
    if (required && value.trim() === '') return 'This field is required.';
    if (value.length > طرح‌واره.max) return `Must be at most ${طرح‌واره.max} characters.`;
    if (طرح‌واره.type === 'string' && /[\r\n]/.test(value)) return 'Line breaks are not allowed here.';
    return null;
  }
  if (طرح‌واره.type === 'url') {
    if (typeof value !== 'string' || value === '') return required ? 'This field is required.' : null;
    return isSafeUrl(value) ? null : 'Use a relative path, anchor or http(s) URL.';
  }
  if (طرح‌واره.type === 'media') {
    const id = (value as { id?: unknown } | null)?.id;
    if (required && (typeof id !== 'string' || id === '')) return 'انتخاب تصویر.';
    return null;
  }
  return null;
}

export function isHeadingField(path: string): boolean {
  return /(heading|headline|title|eyebrow|display)/i.test(path.split('.').pop() ?? path);
}

/** Fields that can be edited directly on the بوم, including flat object children (CTAs). */
export function inlineFields(طرح‌واره: Record<string, PropSchema>): InlineField[] {
  const fields: InlineField[] = [];
  for (const [key, rule] of Object.entries(طرح‌واره)) {
    if (rule.type === 'string' || rule.type === 'text' || rule.type === 'url' || rule.type === 'media') {
      fields.push({ path: key, label: fieldبرچسب(key), طرح‌واره: rule });
      continue;
    }
    if (rule.type === 'object') {
      for (const [childKey, child] of Object.entries(rule.fields)) {
        if (child.type === 'string' || child.type === 'text' || child.type === 'url') {
          const path = `${key}.${childKey}`;
          fields.push({ path, label: fieldبرچسب(path), طرح‌واره: child });
        }
      }
    }
  }
  return fields;
}

export function readProp(props: Record<string, unknown>, path: string): unknown {
  return path.split('.').reduce<unknown>((value, key) => {
    if (value === null || typeof value !== 'object') return undefined;
    return (value as Record<string, unknown>)[key];
  }, props);
}

/** Immutably writes a dotted path, copying each container along the way. */
export function writeProp(props: Record<string, unknown>, path: string, value: unknown): Record<string, unknown> {
  const keys = path.split('.');
  const copy: Record<string, unknown> = { ...props };
  let cursor = copy;
  keys.forEach((key, index) => {
    if (index === keys.length - 1) {
      cursor[key] = value;
      return;
    }
    const current = cursor[key];
    const child = current !== null && typeof current === 'object' ? { ...(current as Record<string, unknown>) } : {};
    cursor[key] = child;
    cursor = child;
  });
  return copy;
}
