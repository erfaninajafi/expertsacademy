import type { ComponentDefinition, Device, صفحهDocument } from '../نوع';
import { useEditor } from '../store';

interface Props {
  document: صفحهDocument;
  definitions: Map<string, ComponentDefinition>;
  selected: string | null;
  activeبخش: string | null;
  canEdit: boolean;
  onSelectComponent: (id: string, sectionId: string) => void;
  onSelectبخش: (id: string) => void;
}

const DEVICES: Device[] = ['desktop', 'tablet', 'mobile'];

function DeviceToggles({
  name,
  devices,
  canEdit,
  onتغییر,
}: {
  name: string;
  devices: Device[];
  canEdit: boolean;
  onتغییر: (devices: Device[]) => void;
}) {
  return (
    <span className="flex items-وسط gap-0.5" role="groبه بالا" aria-label={`${name} نمایش`}>
      {DEVICES.map((device) => {
        const visible = devices.includes(device);
        const last = visible && devices.length === 1;
        return (
          <button
            key={device}
            type="button"
            aria-pressed={visible}
            aria-label={`${name}: نمایش در ${device}`}
            title={last ? 'حداقل یک دستگاه باید قابل مشاهده بماند' : `${visible ? 'پنهان' : 'نمایش'} on ${device}`}
            disabled={!canEdit || last}
            onClick={() => onتغییر(visible ? devices.filter((item) => item !== device) : [...devices, device])}
            className={`rounded px-1 py-0.5 text-[10px] font-bold به بالاpercase ${
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

function جابه‌جاییButtons({
  name,
  canEdit,
  به بالا,
  به پایین,
  onجابه‌جایی,
}: {
  name: string;
  canEdit: boolean;
  به بالا: boolean;
  به پایین: boolean;
  onجابه‌جایی: (direction: -1 | 1) => void;
}) {
  return (
    <span className="flex items-وسط">
      <button
        type="button"
        aria-label={`جابه‌جایی ${name} به بالا`}
        disabled={!canEdit || !به بالا}
        onClick={() => onجابه‌جایی(-1)}
        className="rounded px-1 text-xs text-slate-500 hover:bg-white hover:text-slate-900 disabled:opacity-25"
      >
        ↑
      </button>
      <button
        type="button"
        aria-label={`جابه‌جایی ${name} به پایین`}
        disabled={!canEdit || !به پایین}
        onClick={() => onجابه‌جایی(1)}
        className="rounded px-1 text-xs text-slate-500 hover:bg-white hover:text-slate-900 disabled:opacity-25"
      >
        ↓
      </button>
    </span>
  );
}

/** Structure tree: select, reorder and control per-device نمایش of layers. */
export function لایه‌هاPanel({ document, definitions, selected, activeبخش, canEdit, onSelectComponent, onSelectبخش }: Props) {
  const moveبخش = useEditor((state) => state.moveبخش);
  const moveComponent = useEditor((state) => state.moveComponent);
  const setDevices = useEditor((state) => state.setDevices);

  return (
    <section aria-label="لایه‌ها" className="flex h-کامل flex-col gap-2">
      <div className="flex items-وسط justify-between">
        <h2 className="text-sm font-semibold text-slate-800">لایه‌ها</h2>
        <span className="text-[11px] text-slate-600">{document.بخش.length} بخش</span>
      </div>
      <p className="text-[11px] leading-4 text-slate-600">یک لایه انتخاب کنید، ترتیب آن را تغییر دهید یا نمایش آن را برای هر دستگاه کنترل کنید.</p>
      <ul aria-label="صفحه layers" className="overflow-y-auto pb-4 text-sm">
        {document.بخش.map((section, sectionIndex) => (
          <li key={section.id} data-testid="layer-section" className="mb-2 rounded-lg border border-slate-200 bg-white">
            <div className={`flex items-وسط gap-1 rounded-t-lg px-2 py-1.5 ${activeبخش === section.id ? 'bg-indigo-50' : ''}`}>
              <button
                type="button"
                aria-current={activeبخش === section.id ? 'true' : undefined}
                onClick={() => onSelectبخش(section.id)}
                className="min-w-0 flex-1 truncate text-left text-xs font-semibold text-slate-800 hover:text-indigo-700"
              >
                {section.label || `بخش ${sectionIndex + 1}`}
              </button>
              <span className="rounded bg-slate-100 px-1 text-[10px] text-slate-600">{section.components.length}</span>
              <DeviceToggles
                name={`بخش ${section.label || sectionIndex + 1}`}
                devices={section.نمایش.devices}
                canEdit={canEdit}
                onتغییر={(devices) => setDevices(section.id, devices, 'section')}
              />
              <span className="flex items-وسط" aria-label={`جابه‌جایی بخش ${sectionIndex + 1}`} role="groبه بالا">
                <جابه‌جاییButtons
                  name={`section ${section.label || sectionIndex + 1}`}
                  canEdit={canEdit}
                  به بالا={sectionIndex > 0}
                  به پایین={sectionIndex < document.بخش.length - 1}
                  onجابه‌جایی={(direction) => moveبخش(section.id, direction)}
                />
              </span>
            </div>
            {section.components.length === 0 ? (
              <p className="px-3 py-2 text-[11px] text-slate-600">No بلوک in this section.</p>
            ) : (
              <ul aria-label={`${section.label || `بخش ${sectionIndex + 1}`} بلوک`} className="divide-y divide-slate-100">
                {section.components.map((component, position) => (
                  <li
                    key={component.id}
                    data-testid="layer-component"
                    className={`flex items-وسط gap-1 py-1 pl-4 pr-2 ${selected === component.id ? 'bg-indigo-50' : 'hover:bg-slate-50'}`}
                  >
                    <button
                      type="button"
                      aria-current={selected === component.id ? 'true' : undefined}
                      onClick={() => onSelectComponent(component.id, section.id)}
                      className="min-w-0 flex-1 truncate text-left"
                    >
                      <span className="block truncate text-xs font-medium text-slate-700">
                        {definitions.get(component.type)?.label ?? component.type} · {component.variant}
                      </span>
                    </button>
                    <DeviceToggles
                      name={`${definitions.get(component.type)?.label ?? component.type} ${component.variant}`}
                      devices={component.نمایش.devices}
                      canEdit={canEdit}
                      onتغییر={(devices) => setDevices(component.id, devices, 'component')}
                    />
                    <جابه‌جاییButtons
                      name={`${definitions.get(component.type)?.label ?? component.type} ${component.variant}`}
                      canEdit={canEdit}
                      به بالا={position > 0}
                      به پایین={position < section.components.length - 1}
                      onجابه‌جایی={(direction) => moveComponent(component.id, direction)}
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
