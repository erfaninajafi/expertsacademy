import { useEffect, useMemo, useState } from 'react';
import type { ComponentDefinition, Device, رسانهآیتم, صفحهDocument, صفحهبخش, ThemeTokens } from '../نوع';
import { findComponent, useEditor } from '../store';
import { Field } from './Fields';
import { رسانهPicker } from './رسانهPicker';

interface Props {
  document: صفحهDocument;
  definitions: Map<string, ComponentDefinition>;
  section: صفحهبخش | null;
  canEdit: boolean;
  media?: رسانهآیتم[];
  onUpload?: (file: File, alt: string) => Promise<void>;
  onحذفبخش: (id: string) => void;
}

type Tab = 'page' | 'section' | 'block';

const inputClass =
  'w-کامل rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:outline-هیچ focus:ring-2 focus:ring-indigo-200';
const labelClass = 'mb-1 block text-xs font-medium text-slate-600';
const cardClass = 'rounded-xl border border-slate-200 bg-white p-3';

const DEVICES: Device[] = ['desktop', 'tablet', 'mobile'];

function Row({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className={cardClass}>
      <h3 className="mb-2 text-xs font-semibold به بالاpercase tracking-wide text-slate-500">{title}</h3>
      <div className="grid gap-3">{children}</div>
    </div>
  );
}

function صفحهPanel({ document, canEdit, media }: { document: صفحهDocument; canEdit: boolean; media?: رسانهآیتم[] }) {
  const [ogOpen, setOgOpen] = useState(false);
  const setعنوان = useEditor((state) => state.setعنوان);
  const setSeo = useEditor((state) => state.setSeo);
  const setTheme = useEditor((state) => state.setTheme);
  const setSettings = useEditor((state) => state.setSettings);
  const tokens = document.theme?.tokens;
  const mode = tokens?.mode ?? 'روشن';
  const colors = tokens?.colors?.[mode] ?? {};

  if (!tokens?.typography || !tokens.colors) {
    return <p className="text-sm text-slate-500">This page uses an older theme document without the current token groبه بالاs. Create a new draft or apply a theme to edit it.</p>;
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
            onتغییر={(event) => setعنوان(event.target.value)} />
        </div>
        <div className="grid grid-cols-2 gap-2 text-xs text-slate-600">
          <span>زبان <strong className="block text-slate-900">{document.locale}</strong></span>
          <span>جهت <strong className="block text-slate-900">{document.direction}</strong></span>
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-title">عنوان سئو</label>
          <input id="seo-title" className={inputClass} maxLength={120} value={document.seo.title} disabled={!canEdit}
            onتغییر={(event) => setSeo('title', event.target.value)} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-description">توضیحات متا</label>
          <textarea id="seo-description" rows={3} maxLength={320} className={inputClass} value={document.seo.description}
            disabled={!canEdit} onتغییر={(event) => setSeo('description', event.target.value)} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-keywords">کلیدواژه‌ها (با ویرگول جدا شوند)</label>
          <input id="seo-keywords" className={inputClass} value={document.seo.keywords.join(', ')} disabled={!canEdit}
            onتغییر={(event) => setSeo('keywords', event.target.value.split(',').map((item) => item.trim()).filter(Boolean).slice(0, 20))} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-canonical">نشانی کانونیکال</label>
          <input id="seo-canonical" className={inputClass} value={document.seo.canonical} disabled={!canEdit}
            onتغییر={(event) => setSeo('canonical', event.target.value)} />
        </div>
        <div>
          <label className={labelClass}>تصویر اجتماعی (Open Graph)</label>
          {document.seo.ogImage ? (
            <div className="flex items-وسط gap-2">
              <span className="truncate rounded bg-slate-100 px-2 py-1 text-xs text-slate-700">{document.seo.ogImage.id}</span>
              <button type="button" className="text-xs font-medium text-indigo-700" disabled={!canEdit}
                onClick={() => setOgOpen(true)}>تغییر</button>
              <button type="button" className="text-xs font-medium text-red-700" disabled={!canEdit}
                onClick={() => setSeo('ogImage', null)}>حذف</button>
            </div>
          ) : (
            <button type="button" className="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700" disabled={!canEdit}
              onClick={() => setOgOpen(true)}>انتخاب social image</button>
          )}
          <رسانهPicker open={ogOpen} onOpenتغییر={setOgOpen} items={media ?? []} title="انتخاب social image"
            onSelect={(item) => setSeo('ogImage', { id: item.id, alt: item.alt_text })} />
        </div>
        <div>
          <label className={labelClass} htmlFor="seo-robots">ربات‌ها</label>
          <select id="seo-robots" className={inputClass} value={document.seo.robots ?? 'index,follow'} disabled={!canEdit}
            onتغییر={(event) => setSeo('robots', event.target.value as NonNullable<صفحهDocument['seo']['robots']>)}>
            <option value="index,follow">index,follow</option>
            <option value="noindex,follow">noindex,follow</option>
            <option value="index,nofollow">index,nofollow</option>
            <option value="noindex,nofollow">noindex,nofollow</option>
          </select>
        </div>
        <div className="rounded-lg border border-slate-200 bg-slate-50 p-3" aria-label="پیش‌نمایش جست‌وجو">
          <p className="text-[11px] به بالاpercase tracking-wide text-slate-500">پیش‌نمایش جست‌وجو</p>
          <p className="mt-1 truncate text-sm font-medium text-blue-800">{document.seo.title || document.title}</p>
          <p className="truncate text-[11px] text-emerald-700">{document.seo.canonical || 'نشانی کانونیکال تنظیم نشده است'}</p>
          <p className="mt-1 line-clamp-2 text-xs text-slate-600">{document.seo.description || 'هنوز توضیحات متایی وجود ندارد.'}</p>
          <p className="mt-2 text-[11px] text-slate-500">{document.seo.keywords.length} کلیدواژه</p>
        </div>
      </Row>

      <Row title="توکن‌های طراحی">
        <div className="grid grid-cols-2 gap-2">
          <div>
            <label className={labelClass} htmlFor="token-mode">حالت</label>
            <select id="token-mode" className={inputClass} value={mode} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ mode: event.target.value as ThemeTokens['mode'] })}>
              <option value="روشن">روشن</option>
              <option value="تیره">تیره</option>
            </select>
          </div>
          <div>
            <label className={labelClass} htmlFor="token-shadow">سایه</label>
            <select id="token-shadow" className={inputClass} value={tokens.shadow} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ shadow: event.target.value as ThemeTokens['shadow'] })}>
              <option value="نرم">نرم</option>
              <option value="تیز">تیز</option>
              <option value="هیچ">هیچ</option>
            </select>
          </div>
        </div>
        <div className="grid grid-cols-2 gap-2">
          {(['brand', 'تأکیدی', 'بوم', 'سطح', 'text', 'محو', 'border'] as const).map((key) => (
            <label key={key} className="text-xs font-medium capitalize text-slate-600">
              {key}
              <span className="mt-1 flex items-وسط gap-2">
                <input type="color" aria-label={`${key} colour`} value={colors[key] ?? '#000000'} disabled={!canEdit}
                  onتغییر={(event) => patchColors({ [key]: event.target.value })}
                  className="h-9 w-10 rounded border border-slate-300" />
                <input className={inputClass} value={colors[key] ?? ''} disabled={!canEdit}
                  onتغییر={(event) => patchColors({ [key]: event.target.value })} />
              </span>
            </label>
          ))}
        </div>
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">اندازهٔ نمایش (پیکسل)
            <input type="number" min={32} max={140} className={inputClass} value={tokens.typography.display} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ typography: { ...tokens.typography, display: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">اندازهٔ پایه (پیکسل)
            <input type="number" min={13} max={22} className={inputClass} value={tokens.typography.base} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ typography: { ...tokens.typography, base: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">بخش spacing (px)
            <input type="number" min={32} max={160} className={inputClass} value={tokens.spacing.section} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ spacing: { ...tokens.spacing, section: Number(event.target.value) } })} />
          </label>
          <label className="text-xs font-medium text-slate-600">محتوا (پیکسل)
            <input type="number" min={960} max={1600} className={inputClass} value={tokens.layout.container} disabled={!canEdit}
              onتغییر={(event) => patchTokens({ layout: { ...tokens.layout, container: Number(event.target.value) } })} />
          </label>
        </div>
        <label className="flex items-وسط gap-2 text-xs font-medium text-slate-600">
          <input type="checkbox" checked={tokens.motion.enabled} disabled={!canEdit}
            onتغییر={(event) => patchTokens({ motion: { ...tokens.motion, enabled: event.target.checked } })} />
          فعال‌کردن انیمیشن ورود
        </label>
      </Row>

      <Row title="صفحه settings">
        <label className="text-xs font-medium text-slate-600">پس‌زمینه
          <select className={inputClass} value={document.settings.background} disabled={!canEdit}
            onتغییر={(event) => setSettings('background', event.target.value as صفحهDocument['settings']['background'])}>
            <option value="بوم">بوم</option>
            <option value="سطح">سطح</option>
            <option value="محو">محو</option>
          </select>
        </label>
        <label className="text-xs font-medium text-slate-600">عرض محتوا (پیکسل)
          <input type="number" min={960} max={1600} className={inputClass} value={document.settings.container} disabled={!canEdit}
            onتغییر={(event) => setSettings('container', Number(event.target.value))} />
        </label>
      </Row>
    </div>
  );
}

function بخشPanel({ section, canEdit, onحذفبخش }: { section: صفحهبخش; canEdit: boolean; onحذفبخش: (id: string) => void }) {
  const به بالاdateبخش = useEditor((state) => state.به بالاdateبخش);
  const setLayout = useEditor((state) => state.setبخشLayout);
  const setDevices = useEditor((state) => state.setDevices);
  return (
    <div className="grid gap-3">
      <Row title="بخش">
        <label className="text-xs font-medium text-slate-600">برچسب
          <input className={inputClass} maxLength={80} value={section.label} disabled={!canEdit}
            onتغییر={(event) => به بالاdateبخش(section.id, 'label', event.target.value)} />
        </label>
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">عرض
            <select className={inputClass} value={section.layout.width} disabled={!canEdit}
              onتغییر={(event) => setLayout(section.id, 'width', event.target.value as صفحهبخش['layout']['width'])}>
              <option value="محدود">محدود</option>
              <option value="کامل">کامل</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">پس‌زمینه
            <select className={inputClass} value={section.layout.background} disabled={!canEdit}
              onتغییر={(event) => setLayout(section.id, 'background', event.target.value as صفحهبخش['layout']['background'])}>
              <option value="هیچ">هیچ</option>
              <option value="بوم">بوم</option>
              <option value="سطح">سطح</option>
              <option value="تأکیدی">تأکیدی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">ستون‌ها
            <input type="number" min={1} max={12} className={inputClass} value={section.layout.columns} disabled={!canEdit}
              onتغییر={(event) => setLayout(section.id, 'columns', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">فاصلهٔ عمودی (پیکسل)
            <input type="number" min={0} max={200} className={inputClass} value={section.layout.paddingY} disabled={!canEdit}
              onتغییر={(event) => setLayout(section.id, 'paddingY', Number(event.target.value))} />
          </label>
        </div>
        <fieldset>
          <legپایان className={labelClass}>قابل مشاهده در</legپایان>
          <div className="flex gap-3">
            {DEVICES.map((device) => (
              <label key={device} className="flex items-وسط gap-1.5 text-xs text-slate-700">
                <input type="checkbox" checked={section.نمایش.devices.includes(device)} disabled={!canEdit}
                  onتغییر={(event) => {
                    const next = event.target.checked
                      ? [...section.نمایش.devices, device]
                      : section.نمایش.devices.filter((item) => item !== device);
                    if (next.length > 0) setDevices(section.id, next, 'section');
                  }} />
                {device}
              </label>
            ))}
          </div>
        </fieldset>
        <button type="button" disabled={!canEdit} onClick={() => onحذفبخش(section.id)}
          className="rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 disabled:opacity-40">
          حذف section
        </button>
      </Row>
    </div>
  );
}

function بلوکPanel({ document, definition, canEdit, media, onUpload }: { document: صفحهDocument; definition: ComponentDefinition; canEdit: boolean; media?: رسانهآیتم[]; onUpload?: (file: File, alt: string) => Promise<void> }) {
  const selected = useEditor((state) => state.selected);
  const component = findComponent(document, selected);
  const setگونه = useEditor((state) => state.setگونه);
  const به بالاdateProps = useEditor((state) => state.به بالاdateProps);
  const setطراحی = useEditor((state) => state.setطراحی);
  const setواکنش‌گرا = useEditor((state) => state.setواکنش‌گرا);
  const setAnimation = useEditor((state) => state.setAnimation);
  const setA11y = useEditor((state) => state.setA11y);
  const setDevices = useEditor((state) => state.setDevices);
  const [breakpoint, setنقطهٔ شکست] = useState<Device>('desktop');

  if (!component) return <p className="text-sm text-slate-500">یک بلوک را روی بوم انتخاب کنید.</p>;
  const override = component.responsive[breakpoint] ?? {};

  return (
    <div className="grid gap-3">
      <Row title={`${definition.label} · ${component.type}`}>
        <label className="text-xs font-medium text-slate-600">گونه
          <select className={inputClass} value={component.variant} disabled={!canEdit}
            onتغییر={(event) => setگونه(component.id, event.target.value)}>
            {definition.گونه.map((variant) => (
              <option key={variant.id} value={variant.id}>{variant.label}</option>
            ))}
          </select>
        </label>
        <p className="text-[11px] leading-5 text-slate-500">{definition.description}</p>
      </Row>

      <Row title="محتوا">
        {Object.entries(definition.طرح‌واره).map(([key, طرح‌واره]) => (
          <Field key={key} طرح‌واره={طرح‌واره} path={key} value={component.props[key]} media={media} onUpload={onUpload}
            onتغییر={(value) => به بالاdateProps(component.id, { ...component.props, [key]: value })} />
        ))}
        {Object.keys(definition.طرح‌واره).length === 0 && <p className="text-xs text-slate-500">این بلوک فیلد محتوایی ندارد.</p>}
      </Row>

      <Row title="طراحی">
        <div className="grid grid-cols-3 gap-2">
          <label className="text-xs font-medium text-slate-600">تراز
            <select className={inputClass} value={component.design.align} disabled={!canEdit}
              onتغییر={(event) => setطراحی(component.id, 'align', event.target.value as typeof component.design.align)}>
              <option value="شروع">شروع</option>
              <option value="وسط">وسط</option>
              <option value="پایان">پایان</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">لحن
            <select className={inputClass} value={component.design.tone} disabled={!canEdit}
              onتغییر={(event) => setطراحی(component.id, 'tone', event.target.value as typeof component.design.tone)}>
              <option value="پیش‌فرض">پیش‌فرض</option>
              <option value="معکوس">معکوس</option>
              <option value="تأکیدی">تأکیدی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">فاصله
            <select className={inputClass} value={component.design.spacing} disabled={!canEdit}
              onتغییر={(event) => setطراحی(component.id, 'spacing', event.target.value as typeof component.design.spacing)}>
              <option value="sm">sm</option>
              <option value="md">md</option>
              <option value="lg">lg</option>
            </select>
          </label>
        </div>
      </Row>

      <Row title="واکنش‌گرا">
        <div className="flex gap-1" role="groبه بالا" aria-label="نقطهٔ شکست">
          {DEVICES.map((device) => (
            <button key={device} type="button" aria-pressed={breakpoint === device} onClick={() => setنقطهٔ شکست(device)}
              className={`rounded-md px-2 py-1 text-xs font-medium ${breakpoint === device ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600'}`}>
              {device}
            </button>
          ))}
        </div>
        <label className="text-xs font-medium text-slate-600">ترازment override
          <select className={inputClass} value={override.align ?? ''} disabled={!canEdit}
            onتغییر={(event) => setواکنش‌گرا(component.id, breakpoint, event.target.value ? { ...override, align: event.target.value as 'شروع' | 'وسط' | 'پایان' } : { ...override, align: undefined })}>
            <option value="">ارث‌بری</option>
            <option value="شروع">شروع</option>
            <option value="وسط">وسط</option>
            <option value="پایان">پایان</option>
          </select>
        </label>
        <label className="flex items-وسط gap-2 text-xs font-medium text-slate-600">
          <input type="checkbox" checked={override.hide === true} disabled={!canEdit}
            onتغییر={(event) => setواکنش‌گرا(component.id, breakpoint, { ...override, hide: event.target.checked })} />
          پنهان on {breakpoint}
        </label>
      </Row>

      <Row title="حرکت">
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">پیش‌تنظیم
            <select className={inputClass} value={component.animation.preset} disabled={!canEdit}
              onتغییر={(event) => setAnimation(component.id, 'preset', event.target.value as typeof component.animation.preset)}>
              <option value="هیچ">هیچ</option>
              <option value="محو">محو</option>
              <option value="بالا آمدن">بالا آمدن</option>
              <option value="پله‌ای">پله‌ای</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">مدت (میلی‌ثانیه)
            <input type="number" min={0} max={2000} step={20} className={inputClass} value={component.animation.مدت} disabled={!canEdit}
              onتغییر={(event) => setAnimation(component.id, 'مدت', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">تأخیر (میلی‌ثانیه)
            <input type="number" min={0} max={2000} step={20} className={inputClass} value={component.animation.delay} disabled={!canEdit}
              onتغییر={(event) => setAnimation(component.id, 'delay', Number(event.target.value))} />
          </label>
          <label className="text-xs font-medium text-slate-600">شتاب حرکت
            <select className={inputClass} value={component.animation.easing} disabled={!canEdit}
              onتغییر={(event) => setAnimation(component.id, 'easing', event.target.value as typeof component.animation.easing)}>
              <option value="آرام">آرام</option>
              <option value="استاندارد">استاندارد</option>
            </select>
          </label>
        </div>
      </Row>

      <Row title="دسترس‌پذیری">
        <div className="grid grid-cols-2 gap-2">
          <label className="text-xs font-medium text-slate-600">نشانه
            <select className={inputClass} value={component.a11y.landmark} disabled={!canEdit}
              onتغییر={(event) => setA11y(component.id, 'landmark', event.target.value as typeof component.a11y.landmark)}>
              <option value="هیچ">هیچ</option>
              <option value="ناحیه">ناحیه</option>
              <option value="تکمیلی">تکمیلی</option>
            </select>
          </label>
          <label className="text-xs font-medium text-slate-600">سطح عنوان
            <select className={inputClass} value={component.a11y.headingLevel} disabled={!canEdit}
              onتغییر={(event) => setA11y(component.id, 'headingLevel', Number(event.target.value) as 2 | 3 | 4)}>
              <option value={2}>h2</option>
              <option value={3}>h3</option>
              <option value={4}>h4</option>
            </select>
          </label>
        </div>
        <label className="text-xs font-medium text-slate-600">نام دسترس‌پذیر
          <input className={inputClass} maxLength={120} value={component.a11y.label} disabled={!canEdit}
            onتغییر={(event) => setA11y(component.id, 'label', event.target.value)} />
        </label>
        <fieldset>
          <legپایان className={labelClass}>قابل مشاهده در</legپایان>
          <div className="flex gap-3">
            {DEVICES.map((device) => (
              <label key={device} className="flex items-وسط gap-1.5 text-xs text-slate-700">
                <input type="checkbox" checked={component.نمایش.devices.includes(device)} disabled={!canEdit}
                  onتغییر={(event) => {
                    const next = event.target.checked
                      ? [...component.نمایش.devices, device]
                      : component.نمایش.devices.filter((item) => item !== device);
                    if (next.length > 0) setDevices(component.id, next, 'component');
                  }} />
                {device}
              </label>
            ))}
          </div>
        </fieldset>
      </Row>
    </div>
  );
}

export function بازرس({ document, definitions, section, canEdit, media, onUpload, onحذفبخش }: Props) {
  const selected = useEditor((state) => state.selected);
  const component = findComponent(document, selected);
  const definition = component ? definitions.get(component.type) ?? null : null;
  const [tab, setTab] = useState<Tab>('page');

  useEffect(() => {
    setTab(component ? 'block' : section ? 'section' : 'page');
  }, [component?.id, section?.id]);

  const visibleبخش = useMemo(
    () => document.بخش.find((item) => item.id === section?.id) ?? null,
    [document.بخش, section?.id],
  );

  const tabs: Array<{ id: Tab; label: string }> = [
    { id: 'page', label: 'صفحه' },
    { id: 'section', label: 'بخش' },
    { id: 'block', label: 'بلوک' },
  ];

  return (
    <aside aria-label="بازرس" className="flex h-کامل flex-col">
      <div role="tablist" aria-label="بازرس بخش" className="mb-2 flex gap-1 rounded-lg bg-slate-100 p-1">
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
        {tab === 'page' && <صفحهPanel document={document} canEdit={canEdit} media={media} />}
        {tab === 'section' && visibleبخش && (
          <بخشPanel section={visibleبخش} canEdit={canEdit} onحذفبخش={onحذفبخش} />
        )}
        {tab === 'section' && !visibleبخش && <p className="text-sm text-slate-500">برای ویرایش، سرصفحهٔ یک بخش را انتخاب کنید.</p>}
        {tab === 'block' && component && definition && (
          <بلوکPanel document={document} definition={definition} canEdit={canEdit} media={media} onUpload={onUpload} />
        )}
        {tab === 'block' && (!component || !definition) && (
          <p className="text-sm text-slate-500">
            {component ? `نوع کامپوننت ناشناخته: ${component.type}` : 'یک بلوک را روی بوم انتخاب کنید.'}
          </p>
        )}
      </div>
    </aside>
  );
}
