import { beforeEach, describe, expect, it } from 'vitest';
import { findComponent, moveItem, newComponent, useEditor } from './store';
import type { ComponentDefinition, PageDocument, PageRecord } from './types';

const definition: ComponentDefinition = {
  type: 'hero',
  version: 1,
  label: 'Hero',
  category: 'content',
  description: 'Opening statement',
  variants: [{ id: 'editorial', label: 'Editorial' }, { id: 'centered', label: 'Centered' }],
  schema: { heading: { type: 'string', max: 200, required: true } },
  defaultProps: { heading: 'A heading' },
  status: 'stable',
  container: true,
};

const page = (): PageRecord => ({
  id: '11111111-1111-4111-8111-111111111111',
  title: 'Test page',
  slug: null,
  locale: 'en',
  direction: 'ltr',
  legacyCode: null,
  version: 3,
  status: 'draft',
  draftRevision: 'rev-1',
  publishedRevision: null,
  publishedAt: null,
  createdAt: '2026-01-01 00:00:00',
  updatedAt: '2026-01-01 00:00:00',
  revisions: [],
  document: {
    schemaVersion: 1,
    title: 'Test page',
    locale: 'en',
    direction: 'ltr',
    seo: { title: 'Test page', description: '', keywords: [], canonical: '', ogImage: null },
    theme: {
      id: null,
      version: null,
      tokens: {
        schemaVersion: 2,
        name: 'Default',
        mode: 'light',
        colors: { light: { brand: '#1f4d3f', accent: '#e8c07a', canvas: '#fbf8f1', surface: '#ffffff', text: '#15201c', muted: '#5d6b64', border: '#dcd7c9' }, dark: { brand: '#9fd0bd', accent: '#f0d49a', canvas: '#0d1412', surface: '#131c19', text: '#eef5f1', muted: '#a5b6ae', border: '#2a3733' } },
        typography: { latin: 'editorial', rtl: 'editorial', base: 17, display: 80, heading: 48, scale: 1.25, lineHeight: 1.7 },
        spacing: { unit: 8, section: 72, card: 32, gap: 24 },
        layout: { container: 1280, reading: 680, columns: 12, breakpoints: { mobile: 640, tablet: 1024, desktop: 1280 } },
        radius: { small: 6, medium: 14, large: 24 },
        shadow: 'soft',
        button: { height: 48, weight: 600, style: 'solid' },
        background: { pattern: 'none', gradient: 'none', overlay: 0 },
        motion: { preset: 'reveal', enabled: true, duration: 480, distance: 20, easing: 'calm' },
      },
    },
    settings: { container: 1280, background: 'canvas' },
    sections: [
      {
        id: 'sec-a',
        label: 'Introduction',
        layout: { width: 'contained', columns: 12, gap: 24, paddingY: 72, background: 'none' },
        visibility: { devices: ['desktop', 'tablet', 'mobile'] },
        components: [
          newComponent(definition),
        ],
      },
      {
        id: 'sec-b',
        label: 'Second',
        layout: { width: 'contained', columns: 12, gap: 24, paddingY: 72, background: 'none' },
        visibility: { devices: ['desktop'] },
        components: [],
      },
    ],
  } as PageDocument,
});

describe('moveItem', () => {
  it('moves forward and backward without mutating the source', () => {
    const source = ['a', 'b', 'c'];
    expect(moveItem(source, 0, 2)).toEqual(['b', 'c', 'a']);
    expect(moveItem(source, 2, 0)).toEqual(['c', 'a', 'b']);
    expect(source).toEqual(['a', 'b', 'c']);
  });

  it('returns the same list for out-of-range indices', () => {
    const source = ['a', 'b'];
    expect(moveItem(source, -1, 0)).toBe(source);
    expect(moveItem(source, 0, 5)).toBe(source);
    expect(moveItem(source, 1, 1)).toBe(source);
  });
});

describe('editor store', () => {
  beforeEach(() => {
    useEditor.setState({ page: null, draft: null, selected: null, activeSection: null, dirty: false });
    useEditor.getState().load(page());
  });

  it('loads a page and starts clean', () => {
    const state = useEditor.getState();
    expect(state.draft?.title).toBe('Test page');
    expect(state.dirty).toBe(false);
    expect(state.selected).not.toBeNull();
  });

  it('adds a component to the active section and selects it', () => {
    const before = useEditor.getState().draft!.sections[0].components.length;
    useEditor.getState().addComponent(definition, 'sec-a');
    const state = useEditor.getState();
    expect(state.draft!.sections[0].components).toHaveLength(before + 1);
    expect(state.selected).toBe(state.draft!.sections[0].components.at(-1)!.id);
    expect(state.dirty).toBe(true);
  });

  it('removes a component and clears the selection', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().removeComponent(id);
    expect(findComponent(useEditor.getState().draft, id)).toBeNull();
    expect(useEditor.getState().selected).toBeNull();
  });

  it('duplicates a component with a new id', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().duplicateComponent(id);
    const components = useEditor.getState().draft!.sections[0].components;
    expect(components).toHaveLength(2);
    expect(components[1].id).not.toBe(id);
    expect(components[1].props).toEqual(components[0].props);
  });

  it('reorders sections', () => {
    useEditor.getState().reorderSections(0, 1);
    expect(useEditor.getState().draft!.sections.map((section) => section.id)).toEqual(['sec-b', 'sec-a']);
  });

  it('refuses to delete the final section', () => {
    useEditor.getState().removeSection('sec-b');
    useEditor.getState().removeSection('sec-a');
    expect(useEditor.getState().draft!.sections).toHaveLength(1);
  });

  it('edits nested props and marks the draft dirty', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().updateProps(id, { heading: 'New heading' });
    expect(findComponent(useEditor.getState().draft, id)?.props.heading).toBe('New heading');
    expect(useEditor.getState().dirty).toBe(true);
  });

  it('keeps at least one visible device per component', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().setDevices(id, [], 'component');
    expect(findComponent(useEditor.getState().draft, id)?.visibility.devices.length).toBe(3);
    useEditor.getState().setDevices(id, ['desktop'], 'component');
    expect(findComponent(useEditor.getState().draft, id)?.visibility.devices).toEqual(['desktop']);
  });

  it('stores responsive overrides per breakpoint and removes them', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().setResponsive(id, 'mobile', { align: 'center', hide: true });
    expect(findComponent(useEditor.getState().draft, id)?.responsive.mobile).toEqual({ align: 'center', hide: true });
    useEditor.getState().setResponsive(id, 'mobile', null);
    expect(findComponent(useEditor.getState().draft, id)?.responsive.mobile).toBeUndefined();
  });

  it('moves a component between sections', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().moveComponentToSection(id, 'sec-b');
    const draft = useEditor.getState().draft!;
    expect(findComponent(draft, id)).not.toBeNull();
    expect(draft.sections[1].components.map((component) => component.id)).toContain(id);
  });

  it('undoes and redoes a structural change', () => {
    const before = useEditor.getState().draft!.sections[0].components.length;
    useEditor.getState().addComponent(definition, 'sec-a');
    expect(useEditor.getState().draft!.sections[0].components).toHaveLength(before + 1);

    useEditor.getState().undo();
    expect(useEditor.getState().draft!.sections[0].components).toHaveLength(before);
    expect(useEditor.getState().dirty).toBe(true);

    useEditor.getState().redo();
    expect(useEditor.getState().draft!.sections[0].components).toHaveLength(before + 1);
  });

  it('drops the redo stack after a new edit', () => {
    useEditor.getState().updateProps(useEditor.getState().selected!, { heading: 'First' });
    useEditor.getState().undo();
    expect(useEditor.getState().future).toHaveLength(1);
    useEditor.getState().updateProps(useEditor.getState().selected!, { heading: 'Second' });
    expect(useEditor.getState().future).toHaveLength(0);
  });

  it('coalesces rapid edits of the same field into one undo step', () => {
    const id = useEditor.getState().selected!;
    useEditor.getState().updateProps(id, { heading: 'One' });
    useEditor.getState().updateProps(id, { heading: 'One two' });
    expect(useEditor.getState().past).toHaveLength(1);
    useEditor.getState().undo();
    expect(findComponent(useEditor.getState().draft, id)?.props.heading).toBe('A heading');
  });

  it('resets edit history when another page is loaded', () => {
    useEditor.getState().addComponent(definition, 'sec-a');
    expect(useEditor.getState().past.length).toBeGreaterThan(0);
    useEditor.getState().load(page());
    expect(useEditor.getState().past).toHaveLength(0);
    expect(useEditor.getState().future).toHaveLength(0);
  });
});
