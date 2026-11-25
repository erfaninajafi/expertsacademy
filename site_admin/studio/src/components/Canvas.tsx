import { useState } from 'react';
import {
  DndContext,
  KeyboardSensor,
  PointerSensor,
  closestCenter,
  useSensor,
  useSensors,
  type DragEndEvent,
} from '@dnd-kit/core';
import {
  SortableContext,
  sortableKeyboardCoordinates,
  useSortable,
  verticalListSortingStrategy,
} from '@dnd-kit/sortable';
import type { ComponentDefinition, Device, MediaItem, PageDocument, PageSection } from '../types';
import { useEditor } from '../store';
import { deviceLabel, sectionLabel, variantLabel } from '../i18n';
import { InlineEditor } from './InlineEditor';

interface Props {
  document: PageDocument;
  definitions: Map<string, ComponentDefinition>;
  canEdit: boolean;
  device: Device;
  media?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
  onAddComponent: (sectionId: string) => void;
  onInspectSection: (section: PageSection) => void;
}

function SectionCard({
  section,
  index,
  total,
  definitions,
  canEdit,
  device,
  media,
  onUpload,
  busy,
  onAddComponent,
  onInspectSection,
}: {
  section: PageSection;
  index: number;
  total: number;
  definitions: Map<string, ComponentDefinition>;
  canEdit: boolean;
  device: Device;
  media?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
  onAddComponent: (sectionId: string) => void;
  onInspectSection: (section: PageSection) => void;
}) {
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({ id: section.id, disabled: !canEdit });
  const selected = useEditor((state) => state.selected);
  const select = useEditor((state) => state.select);
  const removeSection = useEditor((state) => state.removeSection);
  const moveSection = useEditor((state) => state.moveSection);
  const [collapsed, setCollapsed] = useState(false);
  const moveComponent = useEditor((state) => state.moveComponent);
  const duplicateComponent = useEditor((state) => state.duplicateComponent);
  const removeComponent = useEditor((state) => state.removeComponent);

  return (
    <article
      ref={setNodeRef}
      style={{
        transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
        transition,
        opacity: isDragging ? 0.7 : 1,
      }}
      className={`rounded-xl border border-slate-200 bg-white shadow-sm ${section.visibility.devices.includes(device) ? '' : 'opacity-50'}`}
      aria-label={`بخش ${index + 1}: ${sectionLabel(section.label)}`}
    >
      <header className="flex flex-wrap items-center gap-2 border-b border-slate-100 px-3 py-2">
        <button
          type="button"
          className="cursor-grab rounded p-1 text-slate-600 hover:bg-slate-100 hover:text-slate-700"
          aria-label={`کشیدن برای جابه‌جایی بخش ${index + 1}`}
          title="برای جابه‌جایی، دستگیره را بکشید"
          disabled={!canEdit}
          {...attributes}
          {...listeners}
        >
          ⠿
        </button>
        <button
          type="button"
          onClick={() => onInspectSection(section)}
          {...listeners}
          className="flex-1 truncate text-right text-sm font-semibold text-slate-800 hover:text-indigo-700 cursor-grab active:cursor-grabbing select-none touch-none"
         title="برای جابه‌جایی بخش، عنوان را بکشید؛ برای تنظیمات، کلیک کنید"
        >
          {sectionLabel(section.label) || `بخش ${index + 1}`}
        </button>
        <span className="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] uppercase tracking-wide text-slate-600">
          {section.layout.width === 'contained' ? 'محدود' : 'کامل'} · {section.components.length} بلوک
        </span>
        {!section.visibility.devices.includes(device) && (
          <span className="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-medium text-amber-900">پنهان در {deviceLabel(device)}</span>
        )}
        <div className="flex items-center gap-0.5" role="group" aria-label={`ترتیب بخش ${index + 1}`}>
          <button
            type="button"
            aria-label={`انتقال بخش ${index + 1} به بالا`}
            title="انتقال بخش به بالا"
            disabled={!canEdit || index === 0}
            onClick={() => moveSection(section.id, -1)}
            className="rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600 hover:border-indigo-300 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-35"
          >↑</button>
          <button
            type="button"
            aria-label={`انتقال بخش ${index + 1} به پایین`}
            title="انتقال بخش به پایین"
            disabled={!canEdit || index === total - 1}
            onClick={() => moveSection(section.id, 1)}
            className="rounded-md border border-slate-200 px-2 py-1 text-xs text-slate-600 hover:border-indigo-300 hover:text-indigo-700 disabled:cursor-not-allowed disabled:opacity-35"
          >↓</button>
        </div>
        <button
          type="button"
          aria-expanded={!collapsed}
          aria-label={collapsed ? `بازکردن بلوک‌های بخش ${index + 1}` : `جمع‌کردن بلوک‌های بخش ${index + 1}`}
          onClick={() => setCollapsed((value) => !value)}
          className="rounded-md border border-slate-200 px-2 py-1 text-[11px] text-slate-600 hover:border-indigo-300 hover:text-indigo-700"
        >{collapsed ? 'بازکردن' : 'جمع‌کردن'}</button>
        <button
          type="button"
          disabled={!canEdit}
          onClick={() => { setCollapsed(false); onAddComponent(section.id); }}
          className="rounded-md border border-slate-200 px-2 py-1 text-xs font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-40"
        >افزودن بلوک</button>
        <button
          type="button"
          disabled={!canEdit || total <= 1}
          onClick={() => {
            if (window.confirm(`بخش «${sectionLabel(section.label) || `بخش ${index + 1}`}» و ${section.components.length} بلوک آن حذف شود؟`)) {
              removeSection(section.id);
            }
          }}
          className="rounded-md border border-transparent px-2 py-1 text-xs font-medium text-red-700 hover:border-red-200 disabled:opacity-40"
        >حذف</button>
      </header>

      <div data-stage13-section-body className={collapsed ? 'hidden' : ''}>
      {section.components.length === 0 && (
        <p className="px-4 py-6 text-center text-sm text-slate-500">بخش خالی است. یک کامپوننت از کتابخانه اضافه کنید.</p>
      )}

      <ul className="divide-y divide-slate-100">
        {section.components.map((component, position) => {
          const definition = definitions.get(component.type);
          const active = selected === component.id;
          const hidden = !component.visibility.devices.includes(device) || component.responsive[device]?.hide === true;
          return (
            <li key={component.id} className={`${active ? 'bg-indigo-50' : 'hover:bg-slate-50'} ${hidden ? 'opacity-60' : ''}`}>
              <div className="flex items-center gap-2 px-3 py-2">
              <span className="w-6 text-center text-[10px] font-semibold text-slate-600">{position + 1}</span>
              <button
                type="button"
                onClick={() => select(component.id)}
                className="min-w-0 flex-1 text-right"
                aria-current={active ? 'true' : undefined}
              >
                <span className="block truncate text-sm font-medium text-slate-800">
                  {definition?.label ?? component.type}
                </span>
                <span className="block truncate text-[11px] text-slate-600">
                  {variantLabel(component.variant)} · {definition?.label ?? component.type}
                </span>
                {hidden && (
                  <span className="mt-0.5 inline-block rounded bg-slate-200 px-1.5 py-0.5 text-[10px] font-medium text-slate-700">
                    پنهان در {deviceLabel(device)}
                  </span>
                )}
              </button>
              <div className="flex items-center gap-0.5">
                <button
                  type="button"
                  aria-label="جابه‌جایی کامپوننت به بالا"
                  disabled={!canEdit || position === 0}
                  onClick={() => moveComponent(component.id, -1)}
                  className="rounded p-1 text-slate-600 hover:bg-white hover:text-slate-700 disabled:opacity-30"
                >
                  ↑
                </button>
                <button
                  type="button"
                  aria-label="جابه‌جایی کامپوننت به پایین"
                  disabled={!canEdit || position === section.components.length - 1}
                  onClick={() => moveComponent(component.id, 1)}
                  className="rounded p-1 text-slate-600 hover:bg-white hover:text-slate-700 disabled:opacity-30"
                >
                  ↓
                </button>
                <button
                  type="button"
                  aria-label="تکثیر کامپوننت"
                  disabled={!canEdit}
                  onClick={() => duplicateComponent(component.id)}
                  className="rounded p-1 text-slate-600 hover:bg-white hover:text-slate-700 disabled:opacity-30"
                >
                  ⧉
                </button>
                <button
                  type="button"
                  aria-label="حذف کامپوننت"
                  disabled={!canEdit}
                  onClick={() => removeComponent(component.id)}
                  className="rounded p-1 text-slate-600 hover:bg-red-50 hover:text-red-700 disabled:opacity-30"
                >
                  ✕
                </button>
              </div>
              </div>
              {active && definition && (
                <InlineEditor
                  component={component}
                  definition={definition}
                  canEdit={canEdit}
                  media={media}
                  onUpload={onUpload}
                  busy={busy}
                />
              )}
            </li>
          );
        })}
      </ul>
      </div>
    </article>
  );
}

export function Canvas({
  document,
  definitions,
  canEdit,
  device,
  media,
  onUpload,
  busy,
  onAddComponent,
  onInspectSection,
}: Props) {
  const reorderSections = useEditor((state) => state.reorderSections);
  const addSection = useEditor((state) => state.addSection);
  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { distance: 4 } }),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates }),
  );

  const handleDragEnd = (event: DragEndEvent): void => {
    const { active, over } = event;
    if (!canEdit || !over || active.id === over.id) return;
    const from = document.sections.findIndex((section) => section.id === active.id);
    const to = document.sections.findIndex((section) => section.id === over.id);
    if (from >= 0 && to >= 0) reorderSections(from, to);
  };

  return (
    <div className="grid gap-2">
    <section
      aria-label="بخش‌های صفحه"
      data-device={device}
      style={{ maxWidth: device === 'tablet' ? '834px' : device === 'mobile' ? '390px' : '100%' }}
      className="mx-auto grid w-full gap-3">
      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <SortableContext items={document.sections.map((section) => section.id)} strategy={verticalListSortingStrategy}>
          {document.sections.map((section, index) => (
            <SectionCard
              key={section.id}
              section={section}
              index={index}
              total={document.sections.length}
              definitions={definitions}
              canEdit={canEdit}
              device={device}
              media={media}
              onUpload={onUpload}
              busy={busy}
              onAddComponent={onAddComponent}
              onInspectSection={onInspectSection}
            />
          ))}
        </SortableContext>
      </DndContext>
      <button
        type="button"
        disabled={!canEdit}
        onClick={addSection}
        className="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-40"
      >افزودن بخش +</button>
    </section>
    </div>
  );
}
