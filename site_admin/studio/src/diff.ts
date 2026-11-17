import type { PageComponent, PageDocument } from './types';

export type ComponentChangeKind = 'added' | 'removed' | 'changed';

export interface ComponentChange {
  id: string;
  type: string;
  variant: string;
  kind: ComponentChangeKind;
}

export interface DocumentDiff {
  sectionsBefore: number;
  sectionsAfter: number;
  titleChanged: boolean;
  changedFields: string[];
  components: ComponentChange[];
}

function flatten(document: PageDocument): Map<string, { component: PageComponent; section: string }> {
  const map = new Map<string, { component: PageComponent; section: string }>();
  for (const section of document.sections) {
    for (const component of section.components) {
      map.set(component.id, { component, section: section.id });
    }
  }
  return map;
}

/** Structural comparison between two page documents, ignoring revision ids. */
export function diffDocuments(before: PageDocument, after: PageDocument): DocumentDiff {
  const previous = flatten(before);
  const next = flatten(after);
  const components: ComponentChange[] = [];

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
        visibility: old.component.visibility,
      }) !==
        JSON.stringify({
          variant: entry.component.variant,
          props: entry.component.props,
          design: entry.component.design,
          animation: entry.component.animation,
          visibility: entry.component.visibility,
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
    sectionsBefore: before.sections.length,
    sectionsAfter: after.sections.length,
    titleChanged: before.title !== after.title,
    changedFields,
    components,
  };
}
