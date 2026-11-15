import { create } from 'zustand';
import type {
  Animation,
  A11y,
  ComponentDefinition,
  طراحیProps,
  Device,
  صفحهComponent,
  صفحهDocument,
  صفحهRecord,
  واکنش‌گراOverride,
  بخشLayout,
  ThemeTokens,
  Visibility,
} from './نوع';

export const uid = (): string =>
  typeof crypto !== 'undefined' && 'randomUUID' in crypto
    ? crypto.randomUUID()
    : `${Date.now().toString(16)}-0000-4000-8000-${Math.random().toString(16).slice(2, 14)}`;

export function moveآیتم<T>(list: T[], from: number, to: number): T[] {
  if (from === to || from < 0 || to < 0 || from >= list.length || to >= list.length) {
    return list;
  }
  const next = [...list];
  const [item] = next.splice(from, 1);
  next.splice(to, 0, item);
  return next;
}

export function findComponent(doc: صفحهDocument | null, id: string | null): صفحهComponent | null {
  if (!doc || !id) return null;
  for (const section of doc.بخش) {
    const match = section.components.find((component) => component.id === id);
    if (match) return match;
  }
  return null;
}

export function پیش‌فرضبخش(): { id: string; label: string; layout: بخشLayout; نمایش: Visibility; components: صفحهComponent[] } {
  return {
    id: uid(),
    label: 'New section',
    layout: { width: 'محدود', columns: 12, gap: 24, paddingY: 72, background: 'هیچ' },
    نمایش: { devices: ['desktop', 'tablet', 'mobile'] },
    components: [],
  };
}

export function newComponent(definition: ComponentDefinition): صفحهComponent {
  return {
    id: uid(),
    type: definition.type,
    version: definition.version,
    variant: definition.گونه[0]?.id ?? 'پیش‌فرض',
    props: structuredClone(definition.پیش‌فرضProps),
    design: { align: 'شروع', tone: 'پیش‌فرض', spacing: 'md' },
    responsive: {},
    animation: { preset: 'هیچ', مدت: 480, delay: 0, easing: 'آرام' },
    نمایش: { devices: ['desktop', 'tablet', 'mobile'] },
    a11y: { landmark: 'هیچ', label: '', headingLevel: 2 },
  };
}

interface EditorState {
  page: صفحهRecord | null;
  draft: صفحهDocument | null;
  selected: string | null;
  activeبخش: string | null;
  dirty: boolean;
  past: صفحهDocument[];
  future: صفحهDocument[];
  lastMergeKey: string | null;
  lastMergeAt: number;
  undo: () => void;
  redo: () => void;
  load: (page: صفحهRecord) => void;
  reset: () => void;
  select: (id: string | null) => void;
  setعنوان: (title: string) => void;
  setSeo: <K extپایانs keyof صفحهDocument['seo']>(key: K, value: صفحهDocument['seo'][K]) => void;
  setTheme: (tokens: ThemeTokens) => void;
  setSettings: <K extپایانs keyof صفحهDocument['settings']>(key: K, value: صفحهDocument['settings'][K]) => void;
  addبخش: () => void;
  removeبخش: (id: string) => void;
  moveبخش: (id: string, direction: -1 | 1) => void;
  reorderبخش‌ها: (from: number, to: number) => void;
  به بالاdateبخش: <K extپایانs keyof صفحهDocument['بخش'][number]>(id: string, key: K, value: صفحهDocument['بخش'][number][K]) => void;
  setبخشLayout: <K extپایانs keyof بخشLayout>(id: string, key: K, value: بخشLayout[K]) => void;
  addComponent: (definition: ComponentDefinition, sectionId?: string) => void;
  removeComponent: (id: string) => void;
  dبه بالاlicateComponent: (id: string) => void;
  moveComponent: (id: string, direction: -1 | 1) => void;
  moveComponentToبخش: (id: string, sectionId: string) => void;
  به بالاdateProps: (id: string, props: Record<string, unknown>) => void;
  setگونه: (id: string, variant: string) => void;
  setطراحی: <K extپایانs keyof طراحیProps>(id: string, key: K, value: طراحیProps[K]) => void;
  setواکنش‌گرا: (id: string, breakpoint: Device, value: واکنش‌گراOverride | null) => void;
  setAnimation: <K extپایانs keyof Animation>(id: string, key: K, value: Animation[K]) => void;
  setA11y: <K extپایانs keyof A11y>(id: string, key: K, value: A11y[K]) => void;
  setDevices: (id: string, devices: Device[], kind: 'component' | 'section') => void;
}

const HISTORY_LIMIT = 60;
const MERGE_WINDOW_MS = 700;

export const useEditor = create<EditorState>((set) => {
  const commit = (
    state: EditorState,
    draft: صفحهDocument,
    extra: Partial<EditorState> = {},
    mergeKey?: string,
  ): Partial<EditorState> => {
    const now = Date.now();
    const merge =
      mergeKey !== undefined && state.lastMergeKey === mergeKey && now - state.lastMergeAt < MERGE_WINDOW_MS;
    return {
      draft,
      dirty: true,
      past: merge ? state.past : [...state.past, state.draft as صفحهDocument].slice(-HISTORY_LIMIT),
      future: [],
      lastMergeKey: mergeKey ?? null,
      lastMergeAt: now,
      ...extra,
    };
  };

  const mutate = (fn: (doc: صفحهDocument) => صفحهDocument, mergeKey?: string) =>
    set((state) => {
      if (!state.draft) return {};
      const draft = fn(state.draft);
      return draft === state.draft ? {} : commit(state, draft, {}, mergeKey);
    });

  const mapComponent = (id: string, fn: (component: صفحهComponent) => صفحهComponent) =>
    mutate((doc) => ({
      ...doc,
      بخش: doc.بخش.map((section) => ({
        ...section,
        components: section.components.map((component) => (component.id === id ? fn(component) : component)),
      })),
    }));

  return {
    page: null,
    draft: null,
    selected: null,
    activeبخش: null,
    dirty: false,
    past: [],
    future: [],
    lastMergeKey: null,
    lastMergeAt: 0,

    load: (page) =>
      set({
        page,
        draft: page.document ? structuredClone(page.document) : null,
        selected: page.document?.بخش[0]?.components[0]?.id ?? null,
        activeبخش: page.document?.بخش[0]?.id ?? null,
        dirty: false,
        past: [],
        future: [],
        lastMergeKey: null,
        lastMergeAt: 0,
      }),

    reset: () => set({ page: null, draft: null, selected: null, activeبخش: null, dirty: false, past: [], future: [], lastMergeKey: null, lastMergeAt: 0 }),
    select: (id) => set({ selected: id }),

    undo: () =>
      set((state) => {
        if (!state.draft || state.past.length === 0) return {};
        const previous = state.past[state.past.length - 1];
        const stillExists = previous.بخش.some((section) =>
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
        const stillExists = next.بخش.some((section) =>
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

    setعنوان: (title) =>
      mutate((doc) => ({ ...doc, title, seo: { ...doc.seo, title: doc.seo.title === doc.title ? title : doc.seo.title } }), 'title'),
    setSeo: (key, value) => mutate((doc) => ({ ...doc, seo: { ...doc.seo, [key]: value } }), `seo:${key}`),
    setTheme: (tokens) => mutate((doc) => ({ ...doc, theme: { ...doc.theme, tokens } }), 'theme'),
    setSettings: (key, value) => mutate((doc) => ({ ...doc, settings: { ...doc.settings, [key]: value } }), `settings:${key}`),

    addبخش: () =>
      mutate((doc) => ({ ...doc, بخش: [...doc.بخش, پیش‌فرضبخش()] })),

    removeبخش: (id) =>
      set((state) => {
        if (!state.draft || state.draft.بخش.length <= 1) return {};
        return commit(state, { ...state.draft, بخش: state.draft.بخش.filter((section) => section.id !== id) }, {
          activeبخش: state.activeبخش === id ? null : state.activeبخش,
        });
      }),

    moveبخش: (id, direction) =>
      mutate((doc) => {
        const index = doc.بخش.findIndex((section) => section.id === id);
        return { ...doc, بخش: moveآیتم(doc.بخش, index, index + direction) };
      }),

    reorderبخش‌ها: (from, to) =>
      mutate((doc) => ({ ...doc, بخش: moveآیتم(doc.بخش, from, to) })),

    به بالاdateبخش: (id, key, value) =>
      mutate((doc) => ({
        ...doc,
        بخش: doc.بخش.map((section) => (section.id === id ? { ...section, [key]: value } : section)),
      }), `section:${id}:${String(key)}`),

    setبخشLayout: (id, key, value) =>
      mutate((doc) => ({
        ...doc,
        بخش: doc.بخش.map((section) =>
          section.id === id ? { ...section, layout: { ...section.layout, [key]: value } } : section,
        ),
      })),

    addComponent: (definition, sectionId) =>
      set((state) => {
        if (!state.draft) return {};
        const targetId =
          sectionId ??
          state.draft.بخش.find((section) => section.components.some((c) => c.id === state.selected))?.id ??
          state.draft.بخش[0]?.id;
        if (!targetId) return {};
        const component = newComponent(definition);
        return commit(
          state,
          {
            ...state.draft,
            بخش: state.draft.بخش.map((section) =>
              section.id === targetId ? { ...section, components: [...section.components, component] } : section,
            ),
          },
          { selected: component.id, activeبخش: targetId },
        );
      }),

    removeComponent: (id) =>
      set((state) => {
        if (!state.draft) return {};
        return commit(
          state,
          {
            ...state.draft,
            بخش: state.draft.بخش.map((section) => ({
              ...section,
              components: section.components.filter((component) => component.id !== id),
            })),
          },
          { selected: state.selected === id ? null : state.selected },
        );
      }),

    dبه بالاlicateComponent: (id) =>
      set((state) => {
        if (!state.draft) return {};
        const copies: صفحهComponent[] = [];
        const بخش = state.draft.بخش.map((section) => {
          const index = section.components.findIndex((component) => component.id === id);
          if (index < 0 || copies.length > 0) return section;
          const copy: صفحهComponent = { ...structuredClone(section.components[index]), id: uid() };
          const components = [...section.components];
          components.splice(index + 1, 0, copy);
          copies.push(copy);
          return { ...section, components };
        });
        return commit(state, { ...state.draft, بخش }, { selected: copies[0]?.id ?? state.selected });
      }),

    moveComponent: (id, direction) =>
      mutate((doc) => ({
        ...doc,
        بخش: doc.بخش.map((section) => {
          const index = section.components.findIndex((component) => component.id === id);
          return index < 0
            ? section
            : { ...section, components: moveآیتم(section.components, index, index + direction) };
        }),
      })),

    moveComponentToبخش: (id, sectionId) =>
      set((state) => {
        if (!state.draft) return {};
        let moved: صفحهComponent | null = null;
        const stripped = state.draft.بخش.map((section) => {
          const found = section.components.find((component) => component.id === id);
          if (found && !moved) moved = found;
          return { ...section, components: section.components.filter((component) => component.id !== id) };
        });
        if (!moved) return {};
        const بخش = stripped.map((section) =>
          section.id === sectionId ? { ...section, components: [...section.components, moved as صفحهComponent] } : section,
        );
        return commit(state, { ...state.draft, بخش }, { activeبخش: sectionId });
      }),

    به بالاdateProps: (id, props) =>
      set((state) => {
        if (!state.draft) return {};
        const بخش = state.draft.بخش.map((section) => ({
          ...section,
          components: section.components.map((component) =>
            component.id === id ? { ...component, props } : component,
          ),
        }));
        return commit(state, { ...state.draft, بخش }, {}, `props:${id}`);
      }),
    setگونه: (id, variant) => mapComponent(id, (component) => ({ ...component, variant })),
    setطراحی: (id, key, value) =>
      mapComponent(id, (component) => ({ ...component, design: { ...component.design, [key]: value } })),
    setواکنش‌گرا: (id, breakpoint, value) =>
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
          بخش: doc.بخش.map((section) =>
            section.id === id ? { ...section, نمایش: { devices } } : section,
          ),
        }));
        return;
      }
      mapComponent(id, (component) => ({ ...component, نمایش: { devices } }));
    },
  };
});
