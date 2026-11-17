import type { ComponentDefinition, Device, PageDocument } from '../types';
import { useEditor } from '../store';

interface Props {
  document: PageDocument;
  definitions: Map<string, ComponentDefinition>;
  selected: string | null;
  activeSection: string | null;
  canEdit: boolean;
  onSelectComponent: (id: string, sectionId: string) => void;
  onSelectSection: (id: string) => void;
}

const DEVICES: Device[] = ['desktop', 'tablet', 'mobile'];

function DeviceToggles({
  name,
  devices,
  canEdit,
  onChange,
}: {
  name: string;
  devices: Device[];
  canEdit: boolean;
  onChange: (devices: Device[]) => void;
}) {
  return (
    <span className="flex items-center gap-0.5" role="group" aria-label={`${name} visibility`}>
      {DEVICES.map((device) => {
        const visible = devices.includes(device);
        const last = visible && devices.length === 1;
        return (
          <button
            key={device}
            type="button"
            aria-pressed={visible}
            aria-label={`${name}: visible on ${device}`}
            title={last ? 'حداقل یک دستگاه باید قابل مشاهده بماند' : `${visible ? 'Hide' : 'visibility'} on ${device}`}
            disabled={!canEdit || last}
            onClick={() => onChange(visible ? devices.filter((item) => item !== device) : [...devices, device])}
            className={`rounded px-1 py-0.5 text-[10px] font-bold uppercase ${
              visible ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
            } disabled:cursor-not-allowed disabled:opacity-40`}
          >
            {device.charAt(0)}
          </button>
        );
      })}
    </span>
  );
}

function MoveButtons({
  name,
  canEdit,
  up,
  down,
  onMove,
}: {
  name: string;
  canEdit: boolean;
  up: boolean;
  down: boolean;
  onMove: (direction: -1 | 1) => void;
}) {
  return (
    <span className="flex items-center">
      <button
        type="button"
        aria-label={`Move ${name} up`}
        disabled={!canEdit || !up}
        onClick={() => onMove(-1)}
        className="rounded px-1 text-xs text-slate-500 hover:bg-white hover:text-slate-900 disabled:opacity-25"
      >
        ↑
      </button>
      <button
        type="button"
        aria-label={`Move ${name} down`}
        disabled={!canEdit || !down}
        onClick={() => onMove(1)}
        className="rounded px-1 text-xs text-slate-500 hover:bg-white hover:text-slate-900 disabled:opacity-25"
      >
        ↓
      </button>
    </span>
  );
}

/** Structure tree: select, reorder and control per-device visibility of layers. */
export function LayersPanel({ document, definitions, selected, activeSection, canEdit, onSelectComponent, onSelectSection }: Props) {
  const moveSection = useEditor((state) => state.moveSection);
  const moveComponent = useEditor((state) => state.moveComponent);
  const setDevices = useEditor((state) => state.setDevices);

  return (
    <section aria-label="لایه‌ها" className="flex h-full flex-col gap-2">
      <div className="flex items-center justify-between">
        <h2 className="text-sm font-semibold text-slate-800">لایه‌ها</h2>
        <span className="text-[11px] text-slate-600">{document.sections.length} sections</span>
      </div>
      <p className="text-[11px] leading-4 text-slate-600">یک لایه انتخاب کنید، ترتیب آن را تغییر دهید یا نمایش آن را برای هر دستگاه کنترل کنید.</p>
      <ul aria-label="لایه‌های صفحه" className="overflow-y-auto pb-4 text-sm">
        {document.sections.map((section, sectionIndex) => (
          <li key={section.id} data-testid="layer-section" className="mb-2 rounded-lg border border-slate-200 bg-white">
            <div className={`flex items-center gap-1 rounded-t-lg px-2 py-1.5 ${activeSection === section.id ? 'bg-indigo-50' : ''}`}>
              <button
                type="button"
                aria-current={activeSection === section.id ? 'true' : undefined}
                onClick={() => onSelectSection(section.id)}
                className="min-w-0 flex-1 truncate text-right text-xs font-semibold text-slate-800 hover:text-indigo-700"
              >
                {section.label || `sections ${sectionIndex + 1}`}
              </button>
              <span className="rounded bg-slate-100 px-1 text-[10px] text-slate-600">{section.components.length}</span>
              <DeviceToggles
                name={`sections ${section.label || sectionIndex + 1}`}
                devices={section.visibility.devices}
                canEdit={canEdit}
                onChange={(devices) => setDevices(section.id, devices, 'section')}
              />
              <span className="flex items-center" aria-label={`Reorder section ${sectionIndex + 1}`} role="group">
                <MoveButtons
                  name={`section ${section.label || sectionIndex + 1}`}
                  canEdit={canEdit}
                  up={sectionIndex > 0}
                  down={sectionIndex < document.sections.length - 1}
                  onMove={(direction) => moveSection(section.id, direction)}
                />
              </span>
            </div>
            {section.components.length === 0 ? (
              <p className="px-3 py-2 text-[11px] text-slate-600">بلوکی در این بخش نیست.</p>
            ) : (
              <ul aria-label={`${section.label || `sections ${sectionIndex + 1}`} blocks`} className="divide-y divide-slate-100">
                {section.components.map((component, position) => (
                  <li
                    key={component.id}
                    data-testid="layer-component"
                    className={`flex items-center gap-1 py-1 pr-4 pl-2 ${selected === component.id ? 'bg-indigo-50' : 'hover:bg-slate-50'}`}
                  >
                    <button
                      type="button"
                      aria-current={selected === component.id ? 'true' : undefined}
                      onClick={() => onSelectComponent(component.id, section.id)}
                      className="min-w-0 flex-1 truncate text-right"
                    >
                      <span className="block truncate text-xs font-medium text-slate-700">
                        {definitions.get(component.type)?.label ?? component.type} · {component.variant}
                      </span>
                    </button>
                    <DeviceToggles
                      name={`${definitions.get(component.type)?.label ?? component.type} ${component.variant}`}
                      devices={component.visibility.devices}
                      canEdit={canEdit}
                      onChange={(devices) => setDevices(component.id, devices, 'component')}
                    />
                    <MoveButtons
                      name={`${definitions.get(component.type)?.label ?? component.type} ${component.variant}`}
                      canEdit={canEdit}
                      up={position > 0}
                      down={position < section.components.length - 1}
                      onMove={(direction) => moveComponent(component.id, direction)}
                    />
                  </li>
                ))}
              </ul>
            )}
          </li>
        ))}
      </ul>
    </section>
  );
}
