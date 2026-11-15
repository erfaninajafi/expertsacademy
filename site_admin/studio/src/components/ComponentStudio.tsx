import { useCallback, useEffect, useMemo, useState } from 'react';
import { ApiError, type ApiClient } from '../api';
import type {
  Componentقرارداد,
  Componentپیش‌تنظیم,
  Componentنسخه,
  زبان,
  StudioComponent,
  StudioConfig,
} from '../نوع';

type لحن = 'ok' | 'error' | 'info';
type Notify = (tone: لحن, message: string) => void;

interface Props {
  api: ApiClient;
  config: StudioConfig;
  notify: Notify;
}

const PREVIEW_WIDTH = { mobile: '390px', tablet: '834px', desktop: '100%' } as const;

export function ComponentStudio({ api, config, notify }: Props) {
  const [catalogue, setCatalogue] = useState<StudioComponent[]>([]);
  const [categories, setCategories] = useState<Array<{ id: string; label: string }>>([]);
  const [query, setQuery] = useState('');
  const [category, setCategory] = useState('all');
  const [status, setوضعیت] = useState('all');
  const [selected, setSelected] = useState<string | null>(null);
  const [contract, setقرارداد] = useState<Componentقرارداد | null>(null);
  const [presets, setپیش‌تنظیم‌ها] = useState<Componentپیش‌تنظیم[]>([]);
  const [versions, setنسخهs] = useState<Componentنسخه[]>([]);
  const [variant, setگونه] = useState<string>('');
  const [locale, setزبان] = useState<زبان>('en');
  const [viewport, setViewport] = useState<keyof typeof PREVIEW_WIDTH>('desktop');
  const [reload, setبارگذاری مجدد] = useState(0);
  const [docs, setDocs] = useState('');
  const [a11y, setA11y] = useState('');
  const [tags, setتگ‌ها] = useState('');
  const [lifecycle, setچرخهٔ عمر] = useState<'stable' | 'experimental' | 'deprecated'>('stable');
  const [animation, setAnimation] = useState('بالا آمدن');
  const [presetName, setپیش‌تنظیمName] = useState('');
  const [presetProps, setپیش‌تنظیمProps] = useState('');
  const [busy, setBusy] = useState<'meta' | 'preset' | 'load' | 'delete' | null>(null);
  const canPublish = config.capabilities.includes('publish');

  const component = useMemo(() => catalogue.find((item) => item.type === selected) ?? null, [catalogue, selected]);

  const load = useCallback(async () => {
    setBusy('load');
    try {
      const response = await api.get<{ components: StudioComponent[]; categories: Array<{ id: string; label: string }> }>('components');
      setCatalogue(response.components);
      setCategories(response.categories);
      const first = selected ?? response.components[0]?.type ?? null;
      setSelected(first);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'Could not load استودیوی کامپوننت.');
    } finally {
      setBusy(null);
    }
  }, [api, notify, selected]);

  useEffect(() => { void load(); }, []); // eslint-disable-line react-hooks/exhaustive-deps

  const open = useCallback(async (type: string) => {
    setSelected(type);
    setBusy('load');
    try {
      const response = await api.get<{ contract: Componentقرارداد; presets: Componentپیش‌تنظیم[]; versions: Componentنسخه[] }>('component', { type });
      setقرارداد(response.contract);
      setپیش‌تنظیم‌ها(response.presets);
      setنسخهs(response.versions);
      setگونه(response.contract.گونه[0]?.id ?? '');
      setDocs(response.contract.docs);
      setA11y(response.contract.a11y);
      setچرخهٔ عمر(response.contract.status);
      setAnimation(response.contract.animation.preset);
      setپیش‌تنظیمProps(JSON.stringify(response.contract.پیش‌فرضs, null, 2));
      setپیش‌تنظیمName('');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'بارگذاری قرارداد کامپوننت ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, notify]);

  useEffect(() => {
    if (!selected) return;
    const item = catalogue.find((entry) => entry.type === selected);
    if (!item) return;
    setDocs(item.meta.docs);
    setA11y(item.meta.a11y);
    setتگ‌ها(item.meta.tags.join(', '));
    setچرخهٔ عمر(item.meta.status);
    setAnimation(item.meta.animation.preset);
    void open(selected);
  }, [selected]); // eslint-disable-line react-hooks/exhaustive-deps

  const visible = useMemo(() => {
    const needle = query.trim().toLowerCase();
    return catalogue.filter((item) => {
      if (category !== 'all' && item.category !== category) return false;
      if (status !== 'all' && item.meta.status !== status) return false;
      if (needle === '') return true;
      return `${item.label} ${item.type} ${item.description} ${item.meta.tags.join(' ')}`.toLowerCase().includes(needle);
    });
  }, [catalogue, category, query, status]);

  const previewUrl = useMemo(() => {
    if (!selected) return '';
    const target = new URL(config.componentپیش‌نمایش, window.location.href);
    target.searchParams.set('type', selected);
    target.searchParams.set('variant', variant);
    target.searchParams.set('locale', locale);
    target.searchParams.set('reload', String(reload));
    return target.toString();
  }, [config.componentپیش‌نمایش, locale, reload, selected, variant]);

  const saveMeta = async (): Promise<void> => {
    if (!selected) return;
    setBusy('meta');
    try {
      await api.sپایان('component_meta', 'PUT', {
        type: selected,
        status: lifecycle,
        docs,
        a11y,
        tags: tags.split(',').map((tag) => tag.trim()).filter(Boolean),
        animation: { preset: animation, مدت: 480 },
      });
      notify('ok', `${selected} اطلاعات کامپوننت ذخیره شد.`);
      await load();
    } catch (error) {
      if (error instanceof ApiError) notify('error', error.message);
      else notify('error', 'ذخیرهٔ اطلاعات کامپوننت انجام نشد.');
    } finally {
      setBusy(null);
    }
  };

  const createپیش‌تنظیم = async (): Promise<void> => {
    if (!selected) return;
    let props: unknown;
    try {
      props = JSON.parse(presetProps);
    } catch {
      notify('error', 'ویژگی‌های پیش‌تنظیم باید JSON معتبر باشد.');
      return;
    }
    setBusy('preset');
    try {
      await api.sپایان('preset', 'POST', { type: selected, name: presetName, variant, props });
      setپیش‌تنظیمName('');
      const response = await api.get<{ presets: Componentپیش‌تنظیم[] }>('presets', { type: selected });
      setپیش‌تنظیم‌ها(response.presets);
      notify('ok', 'پیش‌تنظیم ذخیره شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ پیش‌تنظیم انجام نشد.');
    } finally {
      setBusy(null);
    }
  };

  const deleteپیش‌تنظیم = async (id: string): Promise<void> => {
    setBusy('delete');
    try {
      await api.sپایان('preset_delete', 'POST', { id });
      setپیش‌تنظیم‌ها((items) => items.filter((item) => item.id !== id));
      notify('info', 'پیش‌تنظیم حذف شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'حذف پیش‌تنظیم انجام نشد.');
    } finally {
      setBusy(null);
    }
  };

  return (
    <div className="grid gap-3 xl:grid-cols-[19rem_minmax(0,1fr)]">
      <div className="rounded-xl border border-slate-200 bg-white p-3">
        <div className="flex items-وسط justify-between">
          <h2 className="text-base font-semibold">استودیوی کامپوننت</h2>
          <span className="text-[11px] text-slate-500">{catalogue.length} نوع</span>
        </div>
        <input type="search" aria-label="جست‌وجوی کامپوننت‌ها" placeholder="جست‌وجوی نوع، برچسب یا تگ" value={query} onتغییر={(event) => setQuery(event.target.value)}
          className="mt-2 w-کامل rounded-lg border border-slate-300 px-3 py-2 text-sm" />
        <div className="mt-2 grid grid-cols-2 gap-1">
          <select aria-label="فیلتر بر اساس دسته" value={category} onتغییر={(event) => setCategory(event.target.value)} className="rounded-md border border-slate-300 px-2 py-1 text-xs">
            <option value="all">همهٔ دسته‌ها</option>
            {categories.map((item) => <option key={item.id} value={item.id}>{item.label}</option>)}
          </select>
          <select aria-label="فیلتر بر اساس چرخهٔ عمر" value={status} onتغییر={(event) => setوضعیت(event.target.value)} className="rounded-md border border-slate-300 px-2 py-1 text-xs">
            {['all', 'stable', 'experimental', 'deprecated'].map((item) => <option key={item} value={item}>{item}</option>)}
          </select>
        </div>
        <ul className="mt-2 max-h-[64vh] divide-y divide-slate-100 overflow-y-auto">
          {visible.map((item) => (
            <li key={item.type}>
              <button type="button" onClick={() => setSelected(item.type)} aria-current={selected === item.type}
                className={`w-کامل px-1 py-2 text-left ${selected === item.type ? 'bg-indigo-50' : 'hover:bg-slate-50'}`}>
                <span className="flex items-وسط justify-between gap-1">
                  <span className="text-xs font-semibold text-slate-800">{item.label}</span>
                  <span className="rounded bg-slate-100 px-1 py-0.5 text-[9px] به بالاpercase tracking-wide text-slate-600">{item.meta.status}</span>
                </span>
                <span className="mt-0.5 block truncate text-[10px] text-slate-500">{item.category} · {item.گونه.length} گونه</span>
              </button>
            </li>
          ))}
          {visible.length === 0 && <li className="p-3 text-xs text-slate-500">کامپوننتی مطابقت ندارد.</li>}
        </ul>
      </div>

      <div className="min-w-0 space-y-3">
        {!component ? (
          <div className="rounded-xl border border-dashed border-slate-300 bg-white p-10 text-وسط text-sm text-slate-500" role="status">
            {busy === 'load' ? 'Loading…' : 'Select a component.'}
          </div>
        ) : (
          <>
            <div className="rounded-xl border border-slate-200 bg-white p-3">
              <div className="flex flex-wrap items-شروع gap-2">
                <div className="mr-auto">
                  <h3 className="text-lg font-semibold">{component.label}</h3>
                  <p className="text-xs text-slate-500">{component.type} · v{component.version} · {component.category}</p>
                  <p className="mt-1 max-w-2xl text-xs leading-5 text-slate-600">{component.description}</p>
                </div>
                <a href={`studio_gallery.php?type=${encodeURIComponent(component.type)}`} className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-indigo-400">
                  Open in gallery
                </a>
              </div>
              <div className="mt-3 flex flex-wrap items-وسط gap-1">
                {component.گونه.map((item) => (
                  <button key={item.id} type="button" aria-pressed={variant === item.id} onClick={() => setگونه(item.id)}
                    className={`rounded-کامل px-2.5 py-1 text-[11px] font-semibold ${variant === item.id ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}>
                    {item.label}
                  </button>
                ))}
                <span className="mx-1 h-4 w-px bg-slate-200" />
                <button type="button" aria-pressed={locale === 'en'} onClick={() => setزبان('en')} className={`rounded-کامل px-2 py-1 text-[11px] ${locale === 'en' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'}`}>LTR</button>
                <button type="button" aria-pressed={locale === 'fa'} onClick={() => setزبان('fa')} className={`rounded-کامل px-2 py-1 text-[11px] ${locale === 'fa' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700'}`}>RTL</button>
                <button type="button" onClick={() => setبارگذاری مجدد((value) => value + 1)} className="rounded-کامل bg-slate-100 px-2 py-1 text-[11px] text-slate-700">بارگذاری مجدد</button>
                <span className="mx-1 h-4 w-px bg-slate-200" />
                {(['mobile', 'tablet', 'desktop'] as const).map((item) => (
                  <button key={item} type="button" aria-pressed={viewport === item} onClick={() => setViewport(item)}
                    className={`rounded-کامل px-2 py-1 text-[11px] ${viewport === item ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700'}`}>{item}</button>
                ))}
              </div>
              <div className="mt-2 overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                <iframe key={`${previewUrl}-${reload}`} title="پیش‌نمایش کامپوننت" src={previewUrl} sandbox="allow-same-origin"
                  className="h-[52vh] w-کامل border-0" style={{ minHeight: '360px' }} />
              </div>
              <p className="mt-1 text-[11px] text-slate-500">Same PHP rپایانerer and stylesheet as the published site; {locale === 'fa' ? 'RTL' : 'LTR'} preview at {viewport} width.</p>
            </div>

            <div className="grid gap-3 lg:grid-cols-2">
              <div className="rounded-xl border border-slate-200 bg-white p-3">
                <h4 className="text-sm font-semibold">فراداده</h4>
                <div className="mt-2 space-y-2 text-xs">
                  <label className="block">
                    <span className="font-medium text-slate-700">چرخهٔ عمر</span>
                    <select value={lifecycle} onتغییر={(event) => setچرخهٔ عمر(event.target.value as typeof lifecycle)} className="mt-1 w-کامل rounded-md border border-slate-300 px-2 py-1">
                      {['stable', 'experimental', 'deprecated'].map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                  </label>
                  <label className="block">
                    <span className="font-medium text-slate-700">تگ‌ها</span>
                    <input value={tags} onتغییر={(event) => setتگ‌ها(event.target.value)} className="mt-1 w-کامل rounded-md border border-slate-300 px-2 py-1" />
                  </label>
                  <label className="block">
                    <span className="font-medium text-slate-700">پیش‌تنظیم انیمیشن</span>
                    <select value={animation} onتغییر={(event) => setAnimation(event.target.value)} className="mt-1 w-کامل rounded-md border border-slate-300 px-2 py-1">
                      {['هیچ', 'محو', 'بالا آمدن', 'slide', 'scale', 'پله‌ای'].map((item) => <option key={item} value={item}>{item}</option>)}
                    </select>
                  </label>
                  <label className="block">
                    <span className="font-medium text-slate-700">مستندات</span>
                    <textarea value={docs} onتغییر={(event) => setDocs(event.target.value)} rows={5} className="mt-1 w-کامل rounded-md border border-slate-300 px-2 py-1 font-mono text-[11px]" />
                  </label>
                  <label className="block">
                    <span className="font-medium text-slate-700">نکات دسترس‌پذیری</span>
                    <textarea value={a11y} onتغییر={(event) => setA11y(event.target.value)} rows={4} className="mt-1 w-کامل rounded-md border border-slate-300 px-2 py-1 text-[11px]" />
                  </label>
                  <button type="button" disabled={!canPublish || busy !== null} onClick={() => void saveMeta()} className="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-40">
                    {busy === 'meta' ? 'در حال ذخیره…' : 'ذخیرهٔ فراداده'}
                  </button>
                  {!canPublish && <p className="text-[11px] text-slate-500">انتشار capability is required to change component metadata.</p>}
                </div>
              </div>

              <div className="space-y-3">
                <div className="rounded-xl border border-slate-200 bg-white p-3">
                  <h4 className="text-sm font-semibold">قرارداد</h4>
                  {contract && (
                    <dl className="mt-2 grid grid-cols-2 gap-1 text-[11px]">
                      <dt className="text-slate-500">رندرکننده</dt><dd className="font-medium">PHP · {contract.rپایانerer}</dd>
                      <dt className="text-slate-500">دارایی‌ها</dt><dd className="font-medium">{contract.assets.css}</dd>
                      <dt className="text-slate-500">واکنش‌گرا</dt><dd className="font-medium">{contract.responsive.breakpoints.join(' · ')}</dd>
                      <dt className="text-slate-500">RTL</dt><dd className="font-medium">{contract.rtl ? 'sبه بالاported' : 'not sبه بالاported'}</dd>
                      <dt className="text-slate-500">نسخه</dt><dd className="font-medium">v{contract.version}</dd>
                      <dt className="text-slate-500">وضعیت</dt><dd className="font-medium">{contract.status}</dd>
                    </dl>
                  )}
                  <div className="mt-2 max-h-40 overflow-y-auto rounded border border-slate-100">
                    <table className="w-کامل text-left text-[11px]">
                      <thead className="bg-slate-50 text-slate-500"><tr><th className="p-1">ویژگی</th><th className="p-1">نوع</th></tr></thead>
                      <tbody>
                        {contract && Object.entries(contract.طرح‌واره).map(([key, rule]) => (
                          <tr key={key} className="border-t border-slate-100">
                            <td className="p-1 font-mono">{key}{'required' in rule && rule.required ? ' *' : ''}</td>
                            <td className="p-1 text-slate-600">{rule.type}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>

                <div className="rounded-xl border border-slate-200 bg-white p-3">
                  <h4 className="text-sm font-semibold">پیش‌تنظیم‌ها</h4>
                  <div className="mt-2 space-y-1">
                    <input value={presetName} onتغییر={(event) => setپیش‌تنظیمName(event.target.value)} placeholder="نام پیش‌تنظیم" className="w-کامل rounded-md border border-slate-300 px-2 py-1 text-xs" />
                    <textarea value={presetProps} onتغییر={(event) => setپیش‌تنظیمProps(event.target.value)} rows={4} aria-label="ویژگی‌های JSON پیش‌تنظیم" className="w-کامل rounded-md border border-slate-300 px-2 py-1 font-mono text-[10px]" />
                    <button type="button" disabled={!canPublish || busy !== null || presetName.trim() === ''} onClick={() => void createپیش‌تنظیم()} className="rounded-lg bg-slate-900 px-3 py-1 text-[11px] font-semibold text-white disabled:opacity-40">
                      {busy === 'preset' ? 'در حال ذخیره…' : 'Save preset'}
                    </button>
                  </div>
                  <ul className="mt-2 divide-y divide-slate-100">
                    {presets.map((preset) => (
                      <li key={preset.id} className="flex items-وسط justify-between gap-2 py-1 text-[11px]">
                        <span className="min-w-0"><span className="block truncate font-medium">{preset.name}</span><span className="text-slate-500">{preset.variant}</span></span>
                        <button type="button" disabled={!canPublish || busy !== null} onClick={() => void deleteپیش‌تنظیم(preset.id)} className="rounded px-1.5 py-0.5 text-red-700 hover:bg-red-50 disabled:opacity-40">حذف</button>
                      </li>
                    ))}
                    {presets.length === 0 && <li className="py-1 text-[11px] text-slate-500">هنوز پیش‌تنظیمی وجود ندارد.</li>}
                  </ul>
                </div>

                <div className="rounded-xl border border-slate-200 bg-white p-3">
                  <h4 className="text-sm font-semibold">نسخه history</h4>
                  <ul className="mt-1 divide-y divide-slate-100">
                    {versions.map((version) => (
                      <li key={`${version.type_key}-${version.version}`} className="py-1 text-[11px]">
                        <span className="font-medium">v{version.version}</span> · {version.label}
                        <span className="block text-slate-500">{version.released_at}{version.notes ? ` · ${version.notes}` : ''}</span>
                      </li>
                    ))}
                    {versions.length === 0 && <li className="py-1 text-[11px] text-slate-500">نسخهٔ منتشرشده‌ای ثبت نشده است.</li>}
                  </ul>
                </div>
              </div>
            </div>
          </>
        )}
      </div>
    </div>
  );
}
