import { useEffect, useMemo, useState } from 'react';
import type { ComponentDefinition, Device, MediaItem, PageDocument, PageSection, ThemeTokens } from '../types';
import { findComponent, useEditor } from '../store';
import { Field } from './Fields';
import { MediaPicker } from './MediaPicker';
import { deviceLabel } from '../i18n';

interface Props {
  document: PageDocument;
  definitions: Map<string, ComponentDefinition>;
  section: PageSection | null;
  canEdit: boolean;
  media?: MediaItem[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  onDeleteSection: (id: string) => void;
}

type Tab = 'page' | 'section' | 'block';

const inputClass =
  'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200';
const labelClass = 'mb-1 block text-xs font-medium text-slate-600';
const cardClass = 'rounded-xl border border-slate-200 bg-white p-3';

const DEVICES: Device[] = ['desktop', 'tablet', 'mobile'];

function Row({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className={cardClass}>
      <h3 className="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">{title}</h3>
      <div className="grid gap-3">{children}</div>
    </div>
  );
}

function PagePanel({ document, canEdit, media }: { document: PageDocument; canEdit: boolean; media?: MediaItem[] }) {
  const [ogOpen, setOgOpen] = useState(false);
  const setTitle = useEditor((state) => state.setTitle);
  const setSeo = useEditor((state) => state.setSeo);
  const setTheme = useEditor((state) => state.setTheme);
  const setSettings = useEditor((state) => state.setSettings);
  const tokens = document.theme?.tokens;
  const mode = tokens?.mode ?? 'light';
  const colors = tokens?.colors?.[mode] ?? {};

  if (!tokens?.typography || !tokens.colors) {
    return <p className="text-sm text-slate-500">این صفحه از سند پوستهٔ قدیمی بدون گروه‌های توکن فعلی استفاده می‌کند. برای ویرایش، پیش‌نویس جدید بسازید یا پوسته اعمال کنید.</p>;
  }

  const patchTokens = (patch: Partial<ThemeTokens>): void => setTheme({ ...tokens, ...patch });
  const patchColors = (patch: Record<string, string>): void =>
    patchTokens({ colors: { ...tokens.colors, [mode]: { ...colors, ...patch } } });

  return (
    <div className="grid gap-3">
      <Row title="صفحه">
        <div>
          <label className={labelClass} htmlFor="page-title">عنوان</label>
          <input id="page-title" className={inputClass} maxLength={200} value={document.title} disabled={!canEdit}
            onChange={(event) => setTitle(event.target.value)} />
        </div>
        <div className="grid grid-cols-2 gap-2 text-xs text-slate-600">
          <span>زبان <strong className="block text-slate-900">{document.locale}</strong></span>
          <span>جهت <strong className="block text-slate-900">{document.direction}</strong></span>
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-title">عنوان سئو</label>
          <input id="seo-title" className={inputClass} maxLength={120} value={document.seo.title} disabled={!canEdit}
            onChange={(event) => setSeo('title', event.target.value)} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-description">توضیحات متا</label>
          <textarea id="seo-description" rows={3} maxLength={320} className={inputClass} value={document.seo.description}
            disabled={!canEdit} onChange={(event) => setSeo('description', event.target.value)} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-keywords">کلیدواژه‌ها (با ویرگول جدا شوند)</label>
          <input id="seo-keywords" className={inputClass} value={document.seo.keywords.join(', ')} disabled={!canEdit}
            onChange={(event) => setSeo('keywords', event.target.value.split(',').map((item) => item.trim()).filter(Boolean).slice(0, 20))} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-canonical">نشانی کانونیکال</label>
          <input id="seo-canonical" className={inputClass} value={document.seo.canonical} disabled={!canEdit}
            onChange={(event) => setSeo('canonical', event.target.value)} />
        </div>
        <div>
          <label className={labelClass}>تصویر اجتماعی (Open Graph)</label>
          {document.seo.ogImage ? (
            <div className="flex items-center gap-2">
              <span className="truncate rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">{document.seo.ogImage.id}</span>
              <button type="button" className="text-xs font-medium text-indigo-700" disabled={!canEdit}
                onClick={() => setOgOpen(true)}>تغییر</button>
              <button type="button" className="text-xs font-medium text-red-700" disabled={!canEdit}
                onClick={() => setSeo('ogImage', null)}>حذف</button>
            </div>
          ) : (
            <button type="button" className="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700" disabled={!canEdit}
              onClick={() => setOgOpen(true)}>انتخاب تصویر اجتماعی</button>
          )}
          <MediaPicker open={ogOpen} onOpenChange={setOgOpen} items={media ?? []} title="انتخاب تصویر اجتماعی"
            onSelect={(item) => setSeo('ogImage', { id: item.id, alt: item.alt_text })} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-robots">ربات‌ها</label>
          <select id="seo-robots" className={inputClass} value={document.seo.robots ?? 'index,follow'} disabled={!canEdit}
            onChange={(event) => setSeo('robots', event.target.value as NonNullable<PageDocument['seo']['robots']>)}>
            <option value="index,follow">index,follow</option>
            <option value="noindex,follow">noindex,follow</option>
            <option value="index,nofollow">index,nofollow</option>
            <option value="noindex,nofollow">noindex,nofollow</option>
          </select>
        </div>
        <div className="rounded-lg border border-slate-200 bg-slate-50 p-3" aria-label="پیش‌نمایش جست‌وجو">
          <p className="text-[11px] uppercase tracking-wide text-slate-500">پیش‌نمایش جست‌وجو</p>
          <p className="mt-1 truncate text-sm font-medium text-blue-800">{document.seo.title || document.title}</p>
          <p className="truncate text-[11px] text-emerald-700">{document.seo.canonical || 'نشانی کانونیکال تنظیم نشده است'}</p>
          <p className="mt-1 line-clamp-2 text-xs text-slate-600">{document.seo.description || 'هنوز توضیحات متایی وجود ندارد.'}</p>
          <p className="mt-2 text-[11px] text-slate-500">{document.seo.keywords.length} keyword(s)</p>
        </div>
      </Row>

      <Row title="توکن‌های طراحی">
        <div className="grid grid-cols-2 gap-2">
          <div>
            <label className={labelClass} htmlFor="token-mode">حالت</label>
            <select id="token-mode" className={inputClass} value={mode} disabled={!canEdit}
              onChange={(event) => patchTokens({ mode: event.target.value as ThemeTokens['mode'] })}>
              <option value="light">روشن</option>
              <option value="dark">تیره</option>
            </select>
          </div>
          <div>
            <label className={labelClass} htmlFor="token-shadow">سایه</label>
            <select id="token-shadow" className={inputClass} value={tokens.shadow} disabled={!canEdit}
              onChange={(event) => patchTokens({ shadow: event.target.value as ThemeTokens['shadow'] })}>
              <option value="soft">نرم</option>
              <option value="crisp">تیز</option>
              <option value="none">هیچ</option>
            </select>
          </div>
        </div>
        <div className="grid grid-cols-2 gap-2">
          {(['brand', 'accent', 'canvas', 'surface', 'text', 'muted', 'border'] as const).map((key) => (
            <label key={key} className="text-xs font-medium capitalize text-slate-600">
              {key}
              <span className="mt-1 flex items-center gap-2">
                <input type="color" aria-label={`${key} colour`} value={colors[key] ?? '#000000'} disabled={!canEdit}
                  onChange={(event) => patchColors({ [key]: event.target.value })}
                  className="h-9 w-10 rounded border border-slate-300" />
                <input className={inputClass} value={colors[key] ?? ''} disabled={!canEdit}
                  onChange={(event) => patchColors({ [key]: event.target.value })} />
              </span>
            </label>
          ))}
        </div>
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">اندازهٔ نمایش (پیکسل)<input type="number" min={32} max={140} className={inputClass} value={tokens.typography.display} disabled={!canEdit}
              onChange={(event) => patchTokens({ typography: { ...tokens.typography, display: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">اندازهٔ پایه (پیکسل)<input type="number" min={13} max={22} className={inputClass} value={tokens.typography.base} disabled={!canEdit}
              onChange={(event) => patchTokens({ typography: { ...tokens.typography, base: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">sections spacing (px)
            <input type="number" min={32} max={160} className={inputClass} value={tokens.spacing.section} disabled={!canEdit}
              onChange={(event) => patchTokens({ spacing: { ...tokens.spacing, section: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">محتوا (پیکسل)<input type="number" min={960} max={1600} className={inputClass} value={tokens.layout.container} disabled={!canEdit}
              onChange={(event) => patchTokens({ layout: { ...tokens.layout, container: Number(event.target.value) } })} />
          </label>
        </div>
        <label className="flex items-center gap-2 text-xs font-medium text-slate-600">
          <input type="checkbox" checked={tokens.motion.enabled} disabled={!canEdit}
            onChange={(event) => patchTokens({ motion: { ...tokens.motion, enabled: event.target.checked } })} />فعال‌کردن انیمیشن ورود</label>
      </Row>

      <Row title="تنظیمات صفحه">
        <label className="text-xs font-medium text-slate-600">پس‌زمینه<select className={inputClass} value={document.settings.background} disabled={!canEdit}
            onChange={(event) => setSettings('background', event.target.value as PageDocument['settings']['background'])}>
            <option value="canvas">بوم</option>
            <option value="surface">سطح</option>
            <option value="muted">محو</option>
          </select>
        </label>
        <label className="text-xs font-medium text-slate-600">عرض محتوا (پیکسل)<input type="number" min={960} max={1600} className={inputClass} value={document.settings.container} disabled={!canEdit}
            onChange={(event) => setSettings('container', Number(event.target.value))} />
        </label>
      </Row>
    </div>
  );
}

function SectionPanel({ section, canEdit, onDeleteSection }: { section: PageSection; canEdit: boolean; onDeleteSection: (id: string) => void }) {
  const updateSection = useEditor((state) => state.updateSection);
  const setLayout = useEditor((state) => state.setSectionLayout);
  const setDevices = useEditor((state) => state.setDevices);
  return (
    <div className="grid gap-3">
      <Row title="sections">
        <label className="text-xs font-medium text-slate-600">برچسب<input className={inputClass} maxLength={80} value={section.label} disabled={!canEdit}
            onChange={(event) => updateSection(section.id, 'label', event.target.value)} />
        </label>
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">عرض<select className={inputClass} value={section.layout.width} disabled={!canEdit}
              onChange={(event) => setLayout(section.id, 'width', event.target.value as PageSection['layout']['width'])}>
              <option value="contained">محدود</option>
              <option value="full">کامل</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">پس‌زمینه<select className={inputClass} value={section.layout.background} disabled={!canEdit}
              onChange={(event) => setLayout(section.id, 'background', event.target.value as PageSection['layout']['background'])}>
              <option value="none">هیچ</option>
              <option value="canvas">بوم</option>
              <option value="surface">سطح</option>
              <option value="accent">تأکیدی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">ستون‌ها<input type="number" min={1} max={12} className={inputClass} value={section.layout.columns} disabled={!canEdit}
              onChange={(event) => setLayout(section.id, 'columns', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">فاصلهٔ عمودی (پیکسل)<input type="number" min={0} max={200} className={inputClass} value={section.layout.paddingY} disabled={!canEdit}
              onChange={(event) => setLayout(section.id, 'paddingY', Number(event.target.value))} />
          </label>
        </div>
        <fieldset>
          <legend className={labelClass}>قابل مشاهده در</legend>
          <div className="flex gap-3">
            {DEVICES.map((device) => (
              <label key={device} className="flex items-center gap-1.5 text-xs text-slate-700">
                <input type="checkbox" checked={section.visibility.devices.includes(device)} disabled={!canEdit}
                  onChange={(event) => {
                    const next = event.target.checked
                      ? [...section.visibility.devices, device]
                      : section.visibility.devices.filter((item) => item !== device);
                    if (next.length > 0) setDevices(section.id, next, 'section');
                  }} />
                {deviceLabel(device)}
              </label>
            ))}
          </div>
        </fieldset>
        <button type="button" disabled={!canEdit} onClick={() => onDeleteSection(section.id)}
          className="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 disabled:opacity-40">حذف بخش</button>
      </Row>
    </div>
  );
}

function BlockPanel({ document, definition, canEdit, media, onUpload }: { document: PageDocument; definition: ComponentDefinition; canEdit: boolean; media?: MediaItem[]; onUpload?: (file: File, alt: string) => Promise<void> }) {
  const selected = useEditor((state) => state.selected);
  const component = findComponent(document, selected);
  const setvariants = useEditor((state) => state.setvariants);
  const updateProps = useEditor((state) => state.updateProps);
  const setDesign = useEditor((state) => state.setDesign);
  const setResponsive = useEditor((state) => state.setResponsive);
  const setAnimation = useEditor((state) => state.setAnimation);
  const setA11y = useEditor((state) => state.setA11y);
  const setDevices = useEditor((state) => state.setDevices);
  const [breakpoint, setBreakpoint] = useState<Device>('desktop');

  if (!component) return <p className="text-sm text-slate-500">یک بلوک را روی بوم انتخاب کنید.</p>;
  const override = component.responsive[breakpoint] ?? {};

  return (
    <div className="grid gap-3">
      <Row title={definition.label}>
        <label className="text-xs font-medium text-slate-600">گونه
          <select className={inputClass} value={component.variant} disabled={!canEdit}
            onChange={(event) => setvariants(component.id, event.target.value)}>
            {definition.variants.map((variant) => (
              <option key={variant.id} value={variant.id}>{variant.label}</option>
            ))}
          </select>
        </label>
        <p className="text-[11px] leading-5 text-slate-500">{definition.description}</p>
      </Row>

      <Row title="محتوا">
        {Object.entries(definition.schema).map(([key, schema]) => (
          <Field key={key} schema={schema} path={key} value={component.props[key]} media={media} onUpload={onUpload}
            onChange={(value) => updateProps(component.id, { ...component.props, [key]: value })} />
        ))}
        {Object.keys(definition.schema).length === 0 && <p className="text-xs text-slate-500">این بلوک فیلد محتوایی ندارد.</p>}
      </Row>

      <Row title="طراحی">
        <div className="grid grid-cols-3 gap-2">
          <label className="text-xs font-medium text-slate-600">تراز<select className={inputClass} value={component.design.align} disabled={!canEdit}
              onChange={(event) => setDesign(component.id, 'align', event.target.value as typeof component.design.align)}>
              <option value="start">شروع</option>
              <option value="center">وسط</option>
              <option value="end">پایان</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">لحن<select className={inputClass} value={component.design.tone} disabled={!canEdit}
              onChange={(event) => setDesign(component.id, 'tone', event.target.value as typeof component.design.tone)}>
              <option value="default">پیش‌فرض</option>
              <option value="inverse">معکوس</option>
              <option value="accent">تأکیدی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">فاصله<select className={inputClass} value={component.design.spacing} disabled={!canEdit}
              onChange={(event) => setDesign(component.id, 'spacing', event.target.value as typeof component.design.spacing)}>
              <option value="sm">کوچک</option>
              <option value="md">متوسط</option>
              <option value="lg">بزرگ</option>
            </select>
          </label>
        </div>
      </Row>

      <Row title="واکنش‌گرا">
        <div className="flex gap-1" role="group" aria-label="نقطهٔ شکست">
          {DEVICES.map((device) => (
            <button key={device} type="button" aria-pressed={breakpoint === device} onClick={() => setBreakpoint(device)}
              className={`rounded-md px-2 py-1 text-xs font-medium ${breakpoint === device ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'}`}>
              {deviceLabel(device)}
            </button>
          ))}
        </div>
        <label className="text-xs font-medium text-slate-600">بازنویسی تراز<select className={inputClass} value={override.align ?? ''} disabled={!canEdit}
            onChange={(event) => setResponsive(component.id, breakpoint, event.target.value ? { ...override, align: event.target.value as 'start' | 'center' | 'end' } : { ...override, align: undefined })}>
            <option value="">ارث‌بری</option>
            <option value="start">شروع</option>
            <option value="center">وسط</option>
            <option value="end">پایان</option>
          </select>
        </label>
        <label className="flex items-center gap-2 text-xs font-medium text-slate-600">
          <input type="checkbox" checked={override.hide === true} disabled={!canEdit}
            onChange={(event) => setResponsive(component.id, breakpoint, { ...override, hide: event.target.checked })} />
          پنهان در {deviceLabel(breakpoint)}
        </label>
      </Row>

      <Row title="حرکت">
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">پیش‌تنظیم<select className={inputClass} value={component.animation.preset} disabled={!canEdit}
              onChange={(event) => setAnimation(component.id, 'preset', event.target.value as typeof component.animation.preset)}>
              <option value="none">هیچ</option>
              <option value="muted">محو</option>
              <option value="rise">بالا آمدن</option>
              <option value="stagger">پله‌ای</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">مدت (میلی‌ثانیه)<input type="number" min={0} max={2000} step={20} className={inputClass} value={component.animation.duration} disabled={!canEdit}
              onChange={(event) => setAnimation(component.id, 'duration', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">تأخیر (میلی‌ثانیه)<input type="number" min={0} max={2000} step={20} className={inputClass} value={component.animation.delay} disabled={!canEdit}
              onChange={(event) => setAnimation(component.id, 'delay', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">شتاب حرکت<select className={inputClass} value={component.animation.easing} disabled={!canEdit}
              onChange={(event) => setAnimation(component.id, 'easing', event.target.value as typeof component.animation.easing)}>
              <option value="calm">آرام</option>
              <option value="standard">استاندارد</option>
            </select>
          </label>
        </div>
      </Row>

      <Row title="دسترس‌پذیری">
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">نشانه<select className={inputClass} value={component.a11y.landmark} disabled={!canEdit}
              onChange={(event) => setA11y(component.id, 'landmark', event.target.value as typeof component.a11y.landmark)}>
              <option value="none">هیچ</option>
              <option value="region">ناحیه</option>
              <option value="complementary">تکمیلی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">سطح عنوان<select className={inputClass} value={component.a11y.headingLevel} disabled={!canEdit}
              onChange={(event) => setA11y(component.id, 'headingLevel', Number(event.target.value) as 2 | 3 | 4)}>
              <option value={2}>h2</option>
              <option value={3}>h3</option>
              <option value={4}>h4</option>
            </select>
          </label>
        </div>
        <label className="text-xs font-medium text-slate-600">نام دسترس‌پذیر<input className={inputClass} maxLength={120} value={component.a11y.label} disabled={!canEdit}
            onChange={(event) => setA11y(component.id, 'label', event.target.value)} />
        </label>
        <fieldset>
          <legend className={labelClass}>قابل مشاهده در</legend>
          <div className="flex gap-3">
            {DEVICES.map((device) => (
              <label key={device} className="flex items-center gap-1.5 text-xs text-slate-700">
                <input type="checkbox" checked={component.visibility.devices.includes(device)} disabled={!canEdit}
                  onChange={(event) => {
                    const next = event.target.checked
                      ? [...component.visibility.devices, device]
                      : component.visibility.devices.filter((item) => item !== device);
                    if (next.length > 0) setDevices(component.id, next, 'component');
                  }} />
                {deviceLabel(device)}
              </label>
            ))}
          </div>
        </fieldset>
      </Row>
    </div>
  );
}

export function Inspector({ document, definitions, section, canEdit, media, onUpload, onDeleteSection }: Props) {
  const selected = useEditor((state) => state.selected);
  const component = findComponent(document, selected);
  const definition = component ? definitions.get(component.type) ?? null : null;
  const [tab, setTab] = useState<Tab>('page');

  useEffect(() => {
    setTab(component ? 'block' : section ? 'section' : 'page');
  }, [component?.id, section?.id]);

  const visibleSection = useMemo(
    () => document.sections.find((item) => item.id === section?.id) ?? null,
    [document.sections, section?.id],
  );

  const tabs: Array<{ id: Tab; label: string }> = [
    { id: 'page', label: 'صفحه' },
    { id: 'section', label: 'بخش' },
    { id: 'block', label: 'بلوک' },
  ];

  return (
    <aside aria-label="بازرس" className="flex h-full flex-col">
      <div role="tablist" aria-label="بخش‌های بازرس" className="mb-2 flex gap-1 rounded-lg bg-slate-100 p-1">
        {tabs.map((item) => (
          <button key={item.id} type="button" role="tab" aria-selected={tab === item.id}
            disabled={item.id === 'block' && !component}
            onClick={() => setTab(item.id)}
            className={`flex-1 rounded-md px-2 py-1.5 text-xs font-semibold disabled:opacity-40 ${
              tab === item.id ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'
            }`}>
            {item.label}
          </button>
        ))}
      </div>
      <div className="overflow-y-auto pb-6">
        {tab === 'page' && <PagePanel document={document} canEdit={canEdit} media={media} />}
        {tab === 'section' && visibleSection && (
          <SectionPanel section={visibleSection} canEdit={canEdit} onDeleteSection={onDeleteSection} />
        )}
        {tab === 'section' && !visibleSection && <p className="text-sm text-slate-500">برای ویرایش، سرصفحهٔ یک بخش را انتخاب کنید.</p>}
        {tab === 'block' && component && definition && (
          <BlockPanel document={document} definition={definition} canEdit={canEdit} media={media} onUpload={onUpload} />
        )}
        {tab === 'block' && (!component || !definition) && (
          <p className="text-sm text-slate-500">
            {component ? `Unknown component type: ${component.type}` : 'یک بلوک را روی بوم انتخاب کنید.'}
          </p>
        )}
      </div>
    </aside>
  );
}
