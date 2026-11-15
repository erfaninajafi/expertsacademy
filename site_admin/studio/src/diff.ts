import type { صفحهComponent, صفحهDocument } from './نوع';

export type ComponentتغییرKind = 'added' | 'removed' | 'changed';

export interface Componentتغییر {
  id: string;
  type: string;
  variant: string;
  kind: ComponentتغییرKind;
}

export interface DocumentDiff {
  بخشBefore: number;
  بخشAfter: number;
  titleتغییرd: boolean;
  changedFields: string[];
  components: Componentتغییر[];
}

function flatten(document: صفحهDocument): Map<string, { component: صفحهComponent; section: string }> {
  const map = new Map<string, { component: صفحهComponent; section: string }>();
  for (const section of document.بخش) {
    for (const component of section.components) {
      map.set(component.id, { component, section: section.id });
    }
  }
  return map;
}

/** Structural comparison between two page documents, ignoring revision ids. */
export function diffDocuments(before: صفحهDocument, after: صفحهDocument): DocumentDiff {
  const previous = flatten(before);
  const next = flatten(after);
  const components: Componentتغییر[] = [];

  for (const [id, entry] of next) {
    const old = previous.get(id);
    if (!old) {
      components.push({ id, type: entry.component.type, variant: entry.component.variant, kind: 'added' });
      continue;
    }
    const changed =
      old.section !== entry.section ||
      JSON.stringify({
        variant: old.component.variant,
        props: old.component.props,
        design: old.component.design,
        animation: old.component.animation,
        نمایش: old.component.نمایش,
      }) !==
        JSON.stringify({
          variant: entry.component.variant,
          props: entry.component.props,
          design: entry.component.design,
          animation: entry.component.animation,
          نمایش: entry.component.نمایش,
        });
    if (changed) {
      components.push({ id, type: entry.component.type, variant: entry.component.variant, kind: 'changed' });
    }
  }

  for (const [id, entry] of previous) {
    if (!next.has(id)) {
      components.push({ id, type: entry.component.type, variant: entry.component.variant, kind: 'removed' });
    }
  }

  const changedFields: string[] = [];
  if (before.title !== after.title) changedFields.push('title');
  if (JSON.stringify(before.seo) !== JSON.stringify(after.seo)) changedFields.push('seo');
  if (JSON.stringify(before.settings) !== JSON.stringify(after.settings)) changedFields.push('settings');
  if (JSON.stringify(before.theme) !== JSON.stringify(after.theme)) changedFields.push('theme');

  return {
    بخشBefore: before.بخش.length,
    بخشAfter: after.بخش.length,
    titleتغییرd: before.title !== after.title,
    changedFields,
    components,
  };
}
