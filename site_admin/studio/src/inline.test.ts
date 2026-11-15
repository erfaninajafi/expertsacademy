import { describe, expect, it } from 'vitest';
import { fieldLabel, inlineFields, isSafeUrl, propError, readProp, writeProp } from './inline';
import type { PropSchema } from './types';

const schema: Record<string, PropSchema> = {
  eyebrow: { type: 'string', max: 80 },
  heading: { type: 'string', max: 200, required: true },
  lead: { type: 'text', max: 600 },
  body: { type: 'richtext', max: 2000 },
  primaryCta: {
    type: 'object',
    fields: {
      label: { type: 'string', max: 60, required: true },
      href: { type: 'url', required: true },
    },
  },
  image: { type: 'media' },
  items: { type: 'list', max: 6, item: { title: { type: 'string', max: 60 } } },
};

describe('inline field discovery', () => {
  it('lists text, links, media and flat CTA fields', () => {
    expect(inlineFields(schema).map((field) => field.path)).toEqual([
      'eyebrow',
      'heading',
      'lead',
      'primaryCta.label',
      'primaryCta.href',
      'image',
    ]);
  });

  it('labels nested paths for accessible controls', () => {
    expect(fieldLabel('primaryCta.href')).toBe('Primary CTA href');
    expect(fieldLabel('heading')).toBe('Heading');
  });
});

describe('inline prop validation', () => {
  it('reports required and length failures like the server', () => {
    expect(propError(schema.heading, '')).toBe('This field is required.');
    expect(propError(schema.heading, 'A'.repeat(201))).toBe('Must be at most 200 characters.');
    expect(propError({ type: 'string', max: 20 }, 'two\nlines')).toBe('Line breaks are not allowed here.');
    expect(propError({ type: 'string', max: 20 }, 'fine')).toBeNull();
  });

  it('accepts only the URLs the PHP sanitizer accepts', () => {
    expect(isSafeUrl('/courses/design')).toBe(true);
    expect(isSafeUrl('#admissions')).toBe(true);
    expect(isSafeUrl('https://example.com/apply')).toBe(true);
    expect(isSafeUrl('mailto:hello@example.com')).toBe(true);
    expect(isSafeUrl('javascript:alert(1)')).toBe(false);
    expect(isSafeUrl('//evil.example')).toBe(false);
    expect(propError({ type: 'url', required: true }, 'javascript:alert(1)')).toBe('Use a relative path, anchor or http(s) URL.');
  });

  it('requires an image id only when the schema does', () => {
    expect(propError({ type: 'media', required: true }, { id: '' })).toBe('Choose an image.');
    expect(propError({ type: 'media' }, null)).toBeNull();
  });
});

describe('inline prop writes', () => {
  it('reads and writes nested values without mutating the source props', () => {
    const props = { primaryCta: { label: 'Apply', href: '/apply' }, heading: 'Hello' };
    const next = writeProp(props, 'primaryCta.label', 'Enrol');
    expect(readProp(next, 'primaryCta.label')).toBe('Enrol');
    expect(readProp(next, 'primaryCta.href')).toBe('/apply');
    expect(props.primaryCta.label).toBe('Apply');
    expect(next).not.toBe(props);
  });

  it('creates missing containers for a new path', () => {
    const next = writeProp({}, 'secondaryCta.label', 'Read more');
    expect(next).toEqual({ secondaryCta: { label: 'Read more' } });
  });
});
