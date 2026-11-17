import { create } from 'zustand';
import type {
  Animation,
  A11y,
  ComponentDefinition,
  DesignProps,
  Device,
  PageComponent,
  PageDocument,
  PageRecord,
  ResponsiveOverride,
  sectionLayout,
  ThemeTokens,
  Visibility,
} from './types';

export const uid = (): string =>
  typeof crypto !== 'undefined' && 'randomUUID' in crypto
    ? crypto.randomUUID()
    : `${Date.now().toString(16)}-0000-4000-8000-${Math.random().toString(16).slice(2, 14)}`;

export function moveItem<T>(list: T[], from: number, to: number): T[] {
  if (from === to || from < 0 || to < 0 || from >= list.length || to >= list.length) {
    return list;
  }
  const next = [...list];
  const [item] = next.splice(from, 1);
  next.splice(to, 0, item);
  return next;
}

export function findComponent(doc: PageDocument | null, id: string | null): PageComponent | null {
  if (!doc || !id) return null;
  for (const section of doc.sections) {
    const match = section.components.find((component) => component.id === id);
    if (match) return match;
  }
  return null;
}

export function defaultSection(): { id: string; label: string; layout: sectionLayout; visibility: Visibility; components: PageComponent[] } {
  return {
    id: uid(),
    label: 'بخش جدید',
    layout: { width: 'contained', columns: 12, gap: 24, paddingY: 72, background: 'none' },
    visibility: { devices: ['desktop', 'tablet', 'mobile'] },
    components: [],
  };
}

export function newComponent(definition: ComponentDefinition): PageComponent {
  return {
    id: uid(),
    type: definition.type,
    version: definition.version,
    variant: definition.variants[0]?.id ?? 'default',
    props: structuredClone(definition.defaultProps),
    design: { align: 'start', tone: 'default', spacing: 'md' },
    responsive: {},
    animation: { preset: 'none', duration: 480, delay: 0, easing: 'calm' },
    visibility: { devices: ['desktop', 'tablet', 'mobile'] },
    a11y: { landmark: 'none', label: '', headingLevel: 2 },
  };
}

interface EditorState {
  page: PageRecord | null;
  draft: PageDocument | null;
  selected: string | null;
  activeSection: string | null;
  dirty: boolean;
  past: PageDocument[];
  future: PageDocument[];
  lastMergeKey: string | null;
  lastMergeAt: number;
  undo: () => void;
  redo: () => void;
  load: (page: PageRecord) => void;
  reset: () => void;
  select: (id: string | null) => void;
  setTitle: (title: string) => void;
  setSeo: <K extends keyof PageDocument['seo']>(key: K, value: PageDocument['seo'][K]) => void;
  setTheme: (tokens: ThemeTokens) => void;
  setSettings: <K extends keyof PageDocument['settings']>(key: K, value: PageDocument['settings'][K]) => void;
  addSection: () => void;
  removeSection: (id: string) => void;
  moveSection: (id: string, direction: -1 | 1) => void;
  reorderSections: (from: number, to: number) => void;
  updateSection: <K extends keyof PageDocument['sections'][number]>(id: string, key: K, value: PageDocument['sections'][number][K]) => void;
  setSectionLayout: <K extends keyof sectionLayout>(id: string, key: K, value: sectionLayout[K]) => void;
  addComponent: (definition: ComponentDefinition, sectionId?: string) => void;
  removeComponent: (id: string) => void;
  duplicateComponent: (id: string) => void;
  moveComponent: (id: string, direction: -1 | 1) => void;
  moveComponentToSection: (id: string, sectionId: string) => void;
  updateProps: (id: string, props: Record<string, unknown>) => void;
  setvariants: (id: string, variant: string) => void;
  setDesign: <K extends keyof DesignProps>(id: string, key: K, value: DesignProps[K]) => void;
  setResponsive: (id: string, breakpoint: Device, value: ResponsiveOverride | null) => void;
  setAnimation: <K extends keyof Animation>(id: string, key: K, value: Animation[K]) => void;
  setA11y: <K extends keyof A11y>(id: string, key: K, value: A11y[K]) => void;
  setDevices: (id: string, devices: Device[], kind: 'component' | 'section') => void;
}

const HISTORY_LIMIT = 60;
const MERGE_WINDOW_MS = 700;

export const useEditor = create<EditorState>((set) => {
  const commit = (
    state: EditorState,
    draft: PageDocument,
    extra: Partial<EditorState> = {},
    mergeKey?: string,
  ): Partial<EditorState> => {
    const now = Date.now();
    const merge =
      mergeKey !== undefined && state.lastMergeKey === mergeKey && now - state.lastMergeAt < MERGE_WINDOW_MS;
    return {
      draft,
      dirty: true,
      past: merge ? state.past : [...state.past, state.draft as PageDocument].slice(-HISTORY_LIMIT),
      future: [],
      lastMergeKey: mergeKey ?? null,
      lastMergeAt: now,
      ...extra,
    };
  };

  const mutate = (fn: (doc: PageDocument) => PageDocument, mergeKey?: string) =>
    set((state) => {
      if (!state.draft) return {};
      const draft = fn(state.draft);
      return draft === state.draft ? {} : commit(state, draft, {}, mergeKey);
    });

  const mapComponent = (id: string, fn: (component: PageComponent) => PageComponent) =>
    mutate((doc) => ({
      ...doc,
      sections: doc.sections.map((section) => ({
        ...section,
        components: section.components.map((component) => (component.id === id ? fn(component) : component)),
      })),
    }));

  return {
    page: null,
    draft: null,
    selected: null,
    activeSection: null,
    dirty: false,
    past: [],
    future: [],
    lastMergeKey: null,
    lastMergeAt: 0,

    load: (page) =>
      set({
        page,
        draft: page.document ? structuredClone(page.document) : null,
        selected: page.document?.sections[0]?.components[0]?.id ?? null,
        activeSection: page.document?.sections[0]?.id ?? null,
        dirty: false,
        past: [],
        future: [],
        lastMergeKey: null,
        lastMergeAt: 0,
      }),

    reset: () => set({ page: null, draft: null, selected: null, activeSection: null, dirty: false, past: [], future: [], lastMergeKey: null, lastMergeAt: 0 }),
    select: (id) => set({ selected: id }),

    undo: () =>
      set((state) => {
        if (!state.draft || state.past.length === 0) return {};
        const previous = state.past[state.past.length - 1];
        const stillExists = previous.sections.some((section) =>
          section.components.some((component) => component.id === state.selected),
        );
        return {
          draft: previous,
          past: state.past.slice(0, -1),
          future: [state.draft, ...state.future].slice(0, HISTORY_LIMIT),
          selected: stillExists ? state.selected : null,
          dirty: true,
          lastMergeKey: null,
          lastMergeAt: 0,
        };
      }),

    redo: () =>
      set((state) => {
        if (!state.draft || state.future.length === 0) return {};
        const next = state.future[0];
        const stillExists = next.sections.some((section) =>
          section.components.some((component) => component.id === state.selected),
        );
        return {
          draft: next,
          future: state.future.slice(1),
          past: [...state.past, state.draft].slice(-HISTORY_LIMIT),
          selected: stillExists ? state.selected : null,
          dirty: true,
          lastMergeKey: null,
          lastMergeAt: 0,
        };
      }),

    setTitle: (title) =>
      mutate((doc) => ({ ...doc, title, seo: { ...doc.seo, title: doc.seo.title === doc.title ? title : doc.seo.title } }), 'title'),
    setSeo: (key, value) => mutate((doc) => ({ ...doc, seo: { ...doc.seo, [key]: value } }), `seo:${key}`),
    setTheme: (tokens) => mutate((doc) => ({ ...doc, theme: { ...doc.theme, tokens } }), 'theme'),
    setSettings: (key, value) => mutate((doc) => ({ ...doc, settings: { ...doc.settings, [key]: value } }), `settings:${key}`),

    addSection: () =>
      mutate((doc) => ({ ...doc, sections: [...doc.sections, defaultSection()] })),

    removeSection: (id) =>
      set((state) => {
        if (!state.draft || state.draft.sections.length <= 1) return {};
        return commit(state, { ...state.draft, sections: state.draft.sections.filter((section) => section.id !== id) }, {
          activeSection: state.activeSection === id ? null : state.activeSection,
        });
      }),

    moveSection: (id, direction) =>
      mutate((doc) => {
        const index = doc.sections.findIndex((section) => section.id === id);
        return { ...doc, sections: moveItem(doc.sections, index, index + direction) };
      }),

    reorderSections: (from, to) =>
      mutate((doc) => ({ ...doc, sections: moveItem(doc.sections, from, to) })),

    updateSection: (id, key, value) =>
      mutate((doc) => ({
        ...doc,
        sections: doc.sections.map((section) => (section.id === id ? { ...section, [key]: value } : section)),
      }), `section:${id}:${String(key)}`),

    setSectionLayout: (id, key, value) =>
      mutate((doc) => ({
        ...doc,
        sections: doc.sections.map((section) =>
          section.id === id ? { ...section, layout: { ...section.layout, [key]: value } } : section,
        ),
      })),

    addComponent: (definition, sectionId) =>
      set((state) => {
        if (!state.draft) return {};
        const targetId =
          sectionId ??
          state.draft.sections.find((section) => section.components.some((c) => c.id === state.selected))?.id ??
          state.draft.sections[0]?.id;
        if (!targetId) return {};
        const component = newComponent(definition);
        return commit(
          state,
          {
            ...state.draft,
            sections: state.draft.sections.map((section) =>
              section.id === targetId ? { ...section, components: [...section.components, component] } : section,
            ),
          },
          { selected: component.id, activeSection: targetId },
        );
      }),

    removeComponent: (id) =>
      set((state) => {
        if (!state.draft) return {};
        return commit(
          state,
          {
            ...state.draft,
            sections: state.draft.sections.map((section) => ({
              ...section,
              components: section.components.filter((component) => component.id !== id),
            })),
          },
          { selected: state.selected === id ? null : state.selected },
        );
      }),

    duplicateComponent: (id) =>
      set((state) => {
        if (!state.draft) return {};
        const copies: PageComponent[] = [];
        const sections = state.draft.sections.map((section) => {
          const index = section.components.findIndex((component) => component.id === id);
          if (index < 0 || copies.length > 0) return section;
          const copy: PageComponent = { ...structuredClone(section.components[index]), id: uid() };
          const components = [...section.components];
          components.splice(index + 1, 0, copy);
          copies.push(copy);
          return { ...section, components };
        });
        return commit(state, { ...state.draft, sections }, { selected: copies[0]?.id ?? state.selected });
      }),

    moveComponent: (id, direction) =>
      mutate((doc) => ({
        ...doc,
        sections: doc.sections.map((section) => {
          const index = section.components.findIndex((component) => component.id === id);
          return index < 0
            ? section
            : { ...section, components: moveItem(section.components, index, index + direction) };
        }),
      })),

    moveComponentToSection: (id, sectionId) =>
      set((state) => {
        if (!state.draft) return {};
        let moved: PageComponent | null = null;
        const stripped = state.draft.sections.map((section) => {
          const found = section.components.find((component) => component.id === id);
          if (found && !moved) moved = found;
          return { ...section, components: section.components.filter((component) => component.id !== id) };
        });
        if (!moved) return {};
        const sections = stripped.map((section) =>
          section.id === sectionId ? { ...section, components: [...section.components, moved as PageComponent] } : section,
        );
        return commit(state, { ...state.draft, sections }, { activeSection: sectionId });
      }),

    updateProps: (id, props) =>
      set((state) => {
        if (!state.draft) return {};
        const sections = state.draft.sections.map((section) => ({
          ...section,
          components: section.components.map((component) =>
            component.id === id ? { ...component, props } : component,
          ),
        }));
        return commit(state, { ...state.draft, sections }, {}, `props:${id}`);
      }),
    setvariants: (id, variant) => mapComponent(id, (component) => ({ ...component, variant })),
    setDesign: (id, key, value) =>
      mapComponent(id, (component) => ({ ...component, design: { ...component.design, [key]: value } })),
    setResponsive: (id, breakpoint, value) =>
      mapComponent(id, (component) => {
        const responsive = { ...component.responsive };
        if (value === null) delete responsive[breakpoint];
        else responsive[breakpoint] = value;
        return { ...component, responsive };
      }),
    setAnimation: (id, key, value) =>
      mapComponent(id, (component) => ({ ...component, animation: { ...component.animation, [key]: value } })),
    setA11y: (id, key, value) =>
      mapComponent(id, (component) => ({ ...component, a11y: { ...component.a11y, [key]: value } })),
    setDevices: (id, devices, kind) => {
      if (devices.length === 0) return;
      if (kind === 'section') {
        mutate((doc) => ({
          ...doc,
          sections: doc.sections.map((section) =>
            section.id === id ? { ...section, visibility: { devices } } : section,
          ),
        }));
        return;
      }
      mapComponent(id, (component) => ({ ...component, visibility: { devices } }));
    },
  };
});
