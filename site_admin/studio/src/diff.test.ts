import { describe, expect, it } from 'vitest';
import { diffDocuments } from './diff';
import type { PageComponent, PageDocument } from './types';

const component = (id: string, type: string, variant: string, heading: string): PageComponent => ({
  id,
  type,
  version: 1,
  variant,
  props: { heading },
  design: { align: 'start', tone: 'default', spacing: 'md' },
  responsive: {},
  animation: { preset: 'none', duration: 480, delay: 0, easing: 'calm' },
  visibility: { devices: ['desktop', 'tablet', 'mobile'] },
  a11y: { landmark: 'none', label: '', headingLevel: 2 },
});

const document = (title: string, sections: Array<{ id: string; components: PageComponent[] }>): PageDocument => ({
  schemaVersion: 1,
  title,
  locale: 'en',
  direction: 'ltr',
  seo: { title, description: '', keywords: [], canonical: '', ogImage: null },
  theme: { id: null, version: null, tokens: {} as PageDocument['theme']['tokens'] },
  settings: { container: 1280, background: 'canvas' },
  sections: sections.map((section) => ({
    id: section.id,
    label: section.id,
    layout: { width: 'contained', columns: 12, gap: 24, paddingY: 72, background: 'none' },
    visibility: { devices: ['desktop', 'tablet', 'mobile'] },
    components: section.components,
  })),
});

describe('diffDocuments', () => {
  it('reports added, removed and changed components', () => {
    const before = document('Before', [
      { id: 's1', components: [component('a', 'hero', 'editorial', 'One'), component('b', 'cta', 'band', 'Two')] },
    ]);
    const after = document('After', [
      { id: 's1', components: [component('a', 'hero', 'split', 'One'), component('c', 'stats', 'inline', 'Three')] },
    ]);

    const diff = diffDocuments(before, after);
    expect(diff.titleChanged).toBe(true);
    expect(diff.changedFields).toContain('title');
    expect(diff.components).toEqual(
      expect.arrayContaining([
        expect.objectContaining({ id: 'a', kind: 'changed' }),
        expect.objectContaining({ id: 'c', kind: 'added' }),
        expect.objectContaining({ id: 'b', kind: 'removed' }),
      ]),
    );
    expect(diff.components).toHaveLength(3);
  });

  it('returns an empty component diff for identical documents', () => {
    const doc = document('Same', [{ id: 's1', components: [component('a', 'hero', 'editorial', 'One')] }]);
    const diff = diffDocuments(doc, structuredClone(doc));
    expect(diff.components).toHaveLength(0);
    expect(diff.titleChanged).toBe(false);
    expect(diff.sectionsBefore).toBe(1);
    expect(diff.sectionsAfter).toBe(1);
  });

  it('detects a component moved between sections', () => {
    const before = document('Move', [
      { id: 's1', components: [component('a', 'hero', 'editorial', 'One')] },
      { id: 's2', components: [] },
    ]);
    const after = document('Move', [
      { id: 's1', components: [] },
      { id: 's2', components: [component('a', 'hero', 'editorial', 'One')] },
    ]);
    const diff = diffDocuments(before, after);
    expect(diff.components).toEqual([expect.objectContaining({ id: 'a', kind: 'changed' })]);
  });
});
