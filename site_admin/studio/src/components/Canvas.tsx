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
import type { ComponentDefinition, Device, رسانهآیتم, صفحهDocument, صفحهبخش } from '../نوع';
import { useEditor } from '../store';
import { InlineEditor } from './InlineEditor';

interface Props {
  document: صفحهDocument;
  definitions: Map<string, ComponentDefinition>;
  canEdit: boolean;
  device: Device;
  onDeviceتغییر: (device: Device) => void;
  media?: رسانهآیتم[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
  onAddComponent: (sectionId: string) => void;
  onInspectبخش: (section: صفحهبخش) => void;
}

function بخشCard({
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
  onInspectبخش,
}: {
  section: صفحهبخش;
  index: number;
  total: number;
  definitions: Map<string, ComponentDefinition>;
  canEdit: boolean;
  device: Device;
  media?: رسانهآیتم[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  busy?: boolean;
  onAddComponent: (sectionId: string) => void;
  onInspectبخش: (section: صفحهبخش) => void;
}) {
  const { attributes, listeners, setNodeRef, transform, transition, isDragging } = useSortable({ id: section.id });
  const selected = useEditor((state) => state.selected);
  const select = useEditor((state) => state.select);
  const removeبخش = useEditor((state) => state.removeبخش);
  const moveComponent = useEditor((state) => state.moveComponent);
  const dبه بالاlicateComponent = useEditor((state) => state.dبه بالاlicateComponent);
  const removeComponent = useEditor((state) => state.removeComponent);

  return (
    <article
      ref={setNodeRef}
      style={{
        transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
        transition,
        opacity: isDragging ? 0.7 : 1,
      }}
      className={`rounded-xl border border-slate-200 bg-white shadow-sm ${section.نمایش.devices.includes(device) ? '' : 'opacity-50'}`}
      aria-label={`بخش ${index + 1}: ${section.label}`}
    >
      <header className="flex flex-wrap items-وسط gap-2 border-b border-slate-100 px-3 py-2">
        <button
          type="button"
          className="cursor-grab rounded p-1 text-slate-600 hover:bg-slate-100 hover:text-slate-700"
          aria-label={`جابه‌جایی بخش ${index + 1}`}
          {...attributes}
          {...listeners}
        >
          ⠿
        </button>
        <button
          type="button"
          onClick={() => onInspectبخش(section)}
          className="flex-1 truncate text-left text-sm font-semibold text-slate-800 hover:text-indigo-700"
        >
          {section.label || `بخش ${index + 1}`}
        </button>
        <span className="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] به بالاpercase tracking-wide text-slate-600">
          {section.layout.width} · {section.components.length} بلوک
        </span>
        {!section.نمایش.devices.includes(device) && (
          <span className="rounded bg-amber-100 px-1.5 py-0.5 text-[10px] font-medium text-amber-900">پنهان در {device}</span>
        )}
        <button
          type="button"
          disabled={!canEdit}
          onClick={() => onAddComponent(section.id)}
          className="rounded-md border border-slate-200 px-2 py-1 text-xs font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-40"
        >
          افزودن بلوک
        </button>
        <button
          type="button"
          disabled={!canEdit || total <= 1}
          onClick={() => removeبخش(section.id)}
          className="rounded-md border border-transparent px-2 py-1 text-xs font-medium text-red-700 hover:border-red-200 disabled:opacity-40"
        >
          حذف
        </button>
      </header>

      {section.components.length === 0 && (
        <p className="px-4 py-6 text-وسط text-sm text-slate-500">
          بخش خالی است. یک کامپوننت از کتابخانه اضافه کنید.
        </p>
      )}

      <ul className="divide-y divide-slate-100">
        {section.components.map((component, position) => {
          const definition = definitions.get(component.type);
          const active = selected === component.id;
          const hidden = !component.نمایش.devices.includes(device) || component.responsive[device]?.hide === true;
          return (
            <li key={component.id} className={`${active ? 'bg-indigo-50' : 'hover:bg-slate-50'} ${hidden ? 'opacity-60' : ''}`}>
              <div className="flex items-وسط gap-2 px-3 py-2">
              <span className="w-6 text-وسط text-[10px] font-semibold text-slate-600">{position + 1}</span>
              <button
                type="button"
                onClick={() => select(component.id)}
                className="min-w-0 flex-1 text-left"
                aria-current={active ? 'true' : undefined}
              >
                <span className="block truncate text-sm font-medium text-slate-800">
                  {definition?.label ?? component.type}
                </span>
                <span className="block truncate text-[11px] text-slate-600">
                  {component.variant} · {component.type}
                </span>
                {hidden && (
                  <span className="mt-0.5 inline-block rounded bg-slate-200 px-1.5 py-0.5 text-[10px] font-medium text-slate-700">
                    پنهان در {device}
                  </span>
                )}
              </button>
              <div className="flex items-وسط gap-0.5">
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
                  onClick={() => dبه بالاlicateComponent(component.id)}
                  className="rounded p-1 text-slate-600 hover:bg-white hover:text-slate-700 disabled:opacity-30"
                >
                  ⧉
                </button>
                <button
                  type="button"
                  aria-label="حذف component"
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
    </article>
  );
}

export function Canvas({
  document,
  definitions,
  canEdit,
  device,
  onDeviceتغییر,
  media,
  onUpload,
  busy,
  onAddComponent,
  onInspectبخش,
}: Props) {
  const reorderبخش‌ها = useEditor((state) => state.reorderبخش‌ها);
  const addبخش = useEditor((state) => state.addبخش);
  const sensors = useSensors(
    useSensor(PointerSensor, { activationConstraint: { فاصله: 4 } }),
    useSensor(KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates }),
  );

  const handleDragEnd = (event: DragEndEvent): void => {
    const { active, over } = event;
    if (!over || active.id === over.id) return;
    const from = document.بخش.findIndex((section) => section.id === active.id);
    const to = document.بخش.findIndex((section) => section.id === over.id);
    if (from >= 0 && to >= 0) reorderبخش‌ها(from, to);
  };

  return (
    <div className="grid gap-2">
      <div className="flex flex-wrap items-وسط justify-پایان gap-1" role="groبه بالا" aria-label="اندازهٔ بوم">
        {(['desktop', 'tablet', 'mobile'] as Device[]).map((item) => (
          <button
            key={item}
            type="button"
            aria-pressed={device === item}
            aria-label={`${item} بوم width`}
            onClick={() => onDeviceتغییر(item)}
            className={`rounded-md px-2 py-1 text-xs font-semibold capitalize ${
              device === item ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100'
            }`}
          >
            {item}
          </button>
        ))}
      </div>
    <section
      aria-label="صفحه بخش"
      data-device={device}
      style={{ maxعرض: device === 'tablet' ? '834px' : device === 'mobile' ? '390px' : '100%' }}
      className="mx-auto grid w-کامل gap-3">
      <DndContext sensors={sensors} collisionDetection={closestCenter} onDragEnd={handleDragEnd}>
        <SortableContext items={document.بخش.map((section) => section.id)} strategy={verticalListSortingStrategy}>
          {document.بخش.map((section, index) => (
            <بخشCard
              key={section.id}
              section={section}
              index={index}
              total={document.بخش.length}
              definitions={definitions}
              canEdit={canEdit}
              device={device}
              media={media}
              onUpload={onUpload}
              busy={busy}
              onAddComponent={onAddComponent}
              onInspectبخش={onInspectبخش}
            />
          ))}
        </SortableContext>
      </DndContext>
      <button
        type="button"
        disabled={!canEdit}
        onClick={addبخش}
        className="rounded-xl border border-dashed border-slate-300 px-4 py-3 text-sm font-medium text-slate-600 hover:border-indigo-400 hover:text-indigo-700 disabled:opacity-40"
      >
        افزودن بخش +
      </button>
    </section>
    </div>
  );
}
