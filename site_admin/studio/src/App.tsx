import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import gsap from 'gsap';
import { animate } from 'motion';
import { ApiError, createApi, validate, type ApiClient } from './api';
import { useEditor } from './store';
import type {
  ComponentDefinition,
  Device,
  رسانهآیتم,
  صفحهDocument,
  صفحهRecord,
  صفحهSummary,
  QualityChecks,
  نقشکاربر,
  StudioConfig,
  ThemeRecord,
  ValidationResult,
} from './نوع';
import { Library } from './components/Library';
import { Canvas } from './components/Canvas';
import { بازرس } from './components/بازرس';
import { پیش‌نمایش } from './components/پیش‌نمایش';
import { CreateصفحهDialog, رسانهDialog, دسترسیsDialog, انتشارDialog } from './components/Dialogs';
import { ErrorBoundary } from './components/ErrorBoundary';
import { طراحیSystemManager } from './components/طراحیSystemManager';
import { ComponentStudio } from './components/ComponentStudio';
import { تاریخچهDialog } from './components/تاریخچهDialog';
import { قالب‌هاDialog } from './components/قالب‌هاDialog';
import { لایه‌هاPanel } from './components/لایه‌هاPanel';

type وضعیت = { tone: 'ok' | 'error' | 'info'; message: string };

function readConfig(): StudioConfig {
  const node = document.getElementById('ea-studio-config');
  if (!node?.textمحتوا) throw new Error('تنظیمات صفحه Studio یافت نشد.');
  return JSON.parse(node.textمحتوا) as StudioConfig;
}

function AppShell() {
  const config = useMemo(readConfig, []);
  const api: ApiClient = useMemo(() => createApi(config), [config]);
  const دسترسی = config.دسترسی ?? [];
  const can = (permission: string): boolean => دسترسی.length > 0
    ? دسترسی.includes(permission)
    : config.capabilities.includes('publish');
  const canEdit = can('pages.edit') || (دسترسی.length === 0 && config.capabilities.includes('edit'));
  const canPublish = can('pages.publish');
  const canزمان‌بندی = can('pages.schedule');
  const canمسیر = can('pages.route');
  const canManageکاربرs = can('users.manage');

  const { draft, page, selected, dirty, load, reset, select, past, future, undo, redo } = useEditor();
  const [رجیستری, setRegistry] = useState<ComponentDefinition[]>([]);
  const [pages, setصفحه‌ها] = useState<صفحهSummary[]>([]);
  const [activeصفحه, setActiveصفحه] = useState<string | null>(null);
  const [sectionId, setبخشId] = useState<string | null>(null);
  const [status, setوضعیت] = useState<وضعیت | null>(null);
  const [busy, setBusy] = useState<'save' | 'publish' | 'create' | 'به بالاload' | null>(null);
  const [preview, setپیش‌نمایش] = useState(false);
  const [createOpen, setCreateOpen] = useState(false);
  const [publishOpen, setPublishOpen] = useState(false);
  const [qualityChecks, setQualityChecks] = useState<QualityChecks | null>(null);
  const [previewUrl, setپیش‌نمایشUrl] = useState<string | null>(null);
  const [دسترسیOpen, setدسترسیsOpen] = useState(false);
  const [permissionData, setدسترسیData] = useState<{ users: نقشکاربر[]; roles: string[]; matrix: Record<string, string[]> }>({
    users: [], roles: [], matrix: {},
  });
  const [historyOpen, setتاریخچهOpen] = useState(false);
  const [templatesOpen, setقالب‌هاOpen] = useState(false);
  const [mediaOpen, setرسانهOpen] = useState(false);
  const [media, setرسانه] = useState<رسانهآیتم[]>([]);
  const [validation, setValidation] = useState<ValidationResult | null>(null);
  const [conflict, setConflict] = useState(false);
  const [recovery, setRecovery] = useState<{ document: صفحهDocument; at: number } | null>(null);
  const saveRef = useRef<(silent?: boolean) => Promise<void>>(async () => {});
  const [view, setView] = useState<'pages' | 'design' | 'components'>('pages');
  const [leftTab, setLeftTab] = useState<'بلوک' | 'layers'>('بلوک');
  const [device, setDevice] = useState<Device>('desktop');
  const [themes, setThemes] = useState<ThemeRecord[]>([]);
  const headerRef = useRef<HTMLDivElement>(null);
  const statusRef = useRef<HTMLDivElement>(null);

  const definitions = useMemo(() => new Map(رجیستری.map((item) => [item.type, item])), [رجیستری]);
  const activeبخش = useMemo(
    () => draft?.بخش.find((section) => section.id === sectionId) ?? null,
    [draft, sectionId],
  );

  const notify = (tone: وضعیت['tone'], message: string): void => setوضعیت({ tone, message });

  useEffect(() => {
    if (!statusRef.current) return;
    const controls = animate(statusRef.current, { opacity: [0.4, 1], y: [4, 0] }, { مدت: 0.2 });
    return () => controls.stop();
  }, [status]);

  useEffect(() => {
    if (!headerRef.current) return;
    const context = gsap.context(() => {
      gsap.from('[data-toolbar-item]', { y: -6, opacity: 0, مدت: 0.3, پله‌ای: 0.04, ease: 'power2.out' });
    }, headerRef);
    return () => context.revert();
  }, []);

  const refreshصفحه‌ها = useCallback(async () => {
    const response = await api.get<{ pages: صفحهSummary[]; total: number }>('pages');
    setصفحه‌ها(response.pages);
    return response.pages;
  }, [api]);

  useEffect(() => {
    void (async () => {
      try {
        const [رجیستریResponse, mediaResponse, themesResponse] = await Promise.all([
          api.get<{ components: ComponentDefinition[] }>('رجیستری'),
          api.get<{ items: رسانهآیتم[] }>('media'),
          api.get<{ items: ThemeRecord[] }>('themes'),
        ]);
        setRegistry(رجیستریResponse.components);
        setرسانه(mediaResponse.items);
        setThemes(themesResponse.items);
        await refreshصفحه‌ها();
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بارگذاری صفحه Studio ممکن نشد.');
      }
    })();
  }, [api, refreshصفحه‌ها]);

  const openصفحه = useCallback(
    async (id: string) => {
      try {
        const response = await api.get<{ page: صفحهRecord }>('pages', { id });
        load(response.page);
        setActiveصفحه(id);
        setبخشId(response.page.document?.بخش[0]?.id ?? null);
        setValidation(null);
        setConflict(false);
        const backبه بالا = window.localStorage.getآیتم(`ea-studio:backبه بالا:${id}`);
        if (backبه بالا && response.page.document) {
          try {
            const parsed = JSON.parse(backبه بالا) as { document: صفحهDocument; at: number };
            if (parsed.document && JSON.stringify(parsed.document) !== JSON.stringify(response.page.document)) {
              setRecovery(parsed);
            }
          } catch {
            window.localStorage.removeآیتم(`ea-studio:backبه بالا:${id}`);
          }
        }
        notify('info', `نسخهٔ ${response.page.version} از «${response.page.title}» بارگذاری شد.`);
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بارگذاری صفحه ممکن نشد.');
      }
    },
    [api, load],
  );

  useEffect(() => {
    if (activeصفحه || pages.length === 0) return;
    void openصفحه(pages[0].id);
  }, [activeصفحه, openصفحه, pages]);

  const handleSave = useCallback(async (silent = false) => {
    if (!page || !draft) return;
    setBusy('save');
    try {
      const result = await validate(config, draft);
      setValidation(result);
      if (!result.valid) {
        notify('error', 'سند اعتبارسنجی نشد. پیش از ذخیره، فیلدهای مشخص‌شده را اصلاح کنید.');
        return;
      }
      const response = await api.sپایان<{ page: صفحهRecord }>('pages', 'PUT', {
        document: draft,
        expectedنسخه: page.version,
      }, page.id);
      useEditor.setState({ page: response.page, dirty: false });
      setActiveصفحه(response.page.id);
      window.localStorage.removeآیتم(`ea-studio:backبه بالا:${response.page.id}`);
      setConflict(false);
      await refreshصفحه‌ها();
      notify('ok', silent
        ? `نسخهٔ ${response.page.version} به‌صورت خودکار ذخیره شد.`
        : `نسخهٔ ${response.page.version} ذخیره شد. صفحهٔ عمومی تا زمان انتشار تغییری نمی‌کند.`);
    } catch (error) {
      if (error instanceof ApiError) {
        if (error.status === 409) {
          setConflict(true);
          notify('error', 'این صفحه در تب یا نشست دیگری تغییر کرده است. پیش از ادامه، نسخهٔ تازه را بارگذاری کنید.');
        } else if (!silent) {
          setValidation({ valid: false, errors: error.errors });
          notify('error', error.message);
        }
      } else if (!silent) {
        notify('error', 'ذخیره انجام نشد.');
      }
    } finally {
      setBusy(null);
    }
  }, [api, config, draft, page, refreshصفحه‌ها]);

  const loadChecks = useCallback(async (id: string) => {
    try {
      const response = await api.get<{ checks: QualityChecks }>('quality', { id });
      setQualityChecks(response.checks);
    } catch {
      setQualityChecks(null);
    }
  }, [api]);

  const openانتشار = useCallback(() => {
    setپیش‌نمایشUrl(null);
    setPublishOpen(true);
    setQualityChecks(null);
    if (page) void loadChecks(page.id);
  }, [loadChecks, page]);

  const applyصفحه = useCallback(async (به بالاdated: صفحهRecord, message: string) => {
    useEditor.setState({ page: به بالاdated, dirty: false });
    setActiveصفحه(به بالاdated.id);
    await refreshصفحه‌ها();
    await loadChecks(به بالاdated.id);
    notify('ok', message);
  }, [loadChecks, refreshصفحه‌ها]);

  const handlePublish = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord; checks?: QualityChecks }>('publish', 'POST', {
        expectedنسخه: page.version,
      }, page.id);
      await applyصفحه(response.page, `نسخهٔ ${response.page.version} منتشر شد.`);
      setPublishOpen(false);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422) {
        await loadChecks(page.id);
        notify('error', error.message);
      } else {
        notify('error', error instanceof Error ? error.message : 'انتشار انجام نشد.');
      }
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, loadChecks, page]);

  const handleزمان‌بندی = useCallback(async (publishAt: string, timezone: string) => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord }>('schedule', 'POST', {
        expectedنسخه: page.version, publishAt, timezone,
      }, page.id);
      await applyصفحه(response.page, `زمان‌بندی برای ${response.page.scheduledAt ?? publishAt} (UTC) ثبت شد.`);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422 && page) await loadChecks(page.id);
      notify('error', error instanceof Error ? error.message : 'زمان‌بندی انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, loadChecks, page]);

  const handleانصرافزمان‌بندی = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord }>('schedule_cancel', 'POST', {
        expectedنسخه: page.version,
      }, page.id);
      await applyصفحه(response.page, 'زمان‌بندی لغو شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'لغو زمان‌بندی ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, page]);

  const handleلغو انتشار = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord }>('unpublish', 'POST', {
        expectedنسخه: page.version,
      }, page.id);
      await applyصفحه(response.page, 'انتشار صفحه لغو شد. بازدیدکنندگان دیگر این نسخه را نمی‌بینند.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'لغو انتشار انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, page]);

  const handleآرشیو = useCallback(async () => {
    if (!page || !window.confirm('این صفحه آرشیو شود؟ از سایت عمومی و صف زمان‌بندی خارج می‌شود.')) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord }>('archive', 'POST', {
        expectedنسخه: page.version,
      }, page.id);
      await applyصفحه(response.page, 'صفحه آرشیو شد.');
      setPublishOpen(false);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'آرشیو کردن انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, page]);

  const handleمسیر = useCallback(async (slug: string, code: string) => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ page: صفحهRecord }>('route', 'PUT', {
        slug, legacyCode: code, expectedنسخه: page.version,
      }, page.id);
      await applyصفحه(response.page, 'مسیر ذخیره شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ مسیر ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyصفحه, page]);

  const handleپیش‌نمایشLink = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.sپایان<{ url: string; expiresAt: string }>('preview-token', 'POST', {}, page.id);
      const absolute = new URL(response.url, window.location.href).toString();
      setپیش‌نمایشUrl(absolute);
      await navigator.clipboard?.writeText(absolute);
      notify('ok', `لینک پیش‌نمایش کپی شد. اعتبار آن تا ${response.expiresAt} است.`);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ساخت لینک پیش‌نمایش ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, page]);

  const openدسترسیs = useCallback(async () => {
    try {
      const response = await api.get<{ users: نقشکاربر[]; roles: string[]; matrix: Record<string, string[]> }>('دسترسی');
      setدسترسیData(response);
      setدسترسیsOpen(true);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'بارگذاری دسترسی‌ها ممکن نشد.');
    }
  }, [api]);

  const handleAssignنقش = useCallback(async (userId: string, role: string) => {
    setBusy('save');
    try {
      const response = await api.sپایان<{ users: نقشکاربر[] }>('role', 'POST', { userId, role });
      setدسترسیData((current) => ({ ...current, users: response.users }));
      notify('ok', 'نقش به‌روزرسانی شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'به‌روزرسانی نقش ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api]);

  const handleبازیابی = useCallback(
    async (revisionId: string) => {
      if (!page) return;
      setBusy('save');
      try {
        const response = await api.sپایان<{ page: صفحهRecord }>('restore', 'POST', {
          revisionId,
          expectedنسخه: page.version,
        }, page.id);
        load(response.page);
        setActiveصفحه(response.page.id);
        window.localStorage.removeآیتم(`ea-studio:backبه بالا:${response.page.id}`);
        setConflict(false);
        setتاریخچهOpen(false);
        notify('ok', `نسخهٔ انتخاب‌شده به‌عنوان نسخهٔ جدید بازیابی شد (نسخهٔ صفحه ${response.page.version}).`);
      } catch (error) {
        if (error instanceof ApiError && error.status === 409) setConflict(true);
        notify('error', error instanceof Error ? error.message : 'بازیابی نسخه انجام نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api, load, page],
  );

  const handleRollback = useCallback(
    async (revisionId: string) => {
      if (!page) return;
      setBusy('publish');
      try {
        const response = await api.sپایان<{ page: صفحهRecord }>('rollback', 'POST', {
          revisionId,
          expectedنسخه: page.version,
        }, page.id);
        await applyصفحه(response.page, `بازگردانی شد: نسخهٔ ${revisionId.slice(0, 8)} اکنون منتشر است.`);
        setتاریخچهOpen(false);
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بازگردانی انجام نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api, applyصفحه, page],
  );

  const handleCreate = useCallback(
    async (input: { title: string; locale: 'fa' | 'en'; legacyCode: string }) => {
      setBusy('create');
      try {
        const response = await api.sپایان<{ page: صفحهRecord }>('pages', 'POST', {
          title: input.title,
          locale: input.locale,
          legacyCode: input.legacyCode || null,
        });
        setCreateOpen(false);
        await refreshصفحه‌ها();
        load(response.page);
        setActiveصفحه(response.page.id);
        setبخشId(response.page.document?.بخش[0]?.id ?? null);
        notify('ok', 'پیش‌نویس ساخته شد.');
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'ساخت صفحه ممکن نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api, load, refreshصفحه‌ها],
  );

  const handleرسانهMeta = useCallback(async (id: string, alt: string, focalX: number, focalY: number) => {
    setBusy('به بالاload');
    try {
      await api.sپایان('media', 'PUT', { alt, focalX, focalY }, id);
      const response = await api.get<{ items: رسانهآیتم[] }>('media');
      setرسانه(response.items);
      notify('ok', 'اطلاعات تصویر ذخیره شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ اطلاعات تصویر ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api]);

  const handleUpload = useCallback(
    async (file: File, alt: string) => {
      setBusy('به بالاload');
      try {
        await api.به بالاload(file, alt);
        const response = await api.get<{ items: رسانهآیتم[] }>('media');
        setرسانه(response.items);
        notify('ok', 'تصویر بارگذاری شد. شناسهٔ آن را در فیلد تصویر قرار دهید.');
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بارگذاری انجام نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api],
  );

  saveRef.current = handleSave;

  useEffect(() => {
    if (!dirty || !page || !draft || !canEdit || busy !== null || conflict || recovery) return;
    const timer = window.setTimeout(() => void saveRef.current(true), 3000);
    return () => window.clearTimeout(timer);
  }, [busy, canEdit, conflict, draft, dirty, page, recovery]);

  useEffect(() => {
    if (!dirty || !page || !draft) return;
    try {
      window.localStorage.setآیتم(`ea-studio:backبه بالا:${page.id}`, JSON.stringify({ document: draft, at: Date.now() }));
    } catch {
      // Storage can be unavailable; autosave still protects the draft server-side.
    }
  }, [dirty, draft, page]);

  useEffect(() => {
    if (!dirty) return;
    const warn = (event: BeforeUnloadEvent): void => { event.preventDefault(); event.returnValue = ''; };
    window.addEventListener('beforeunload', warn);
    return () => window.removeEventListener('beforeunload', warn);
  }, [dirty]);

  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent): void => {
      const target = event.target as HTMLElement | null;
      const typing = target?.closest('input, textarea, select, [contenteditable="true"]') != null;
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 's') {
        event.preventDefault();
        if (canEdit && !busy) void handleSave();
        return;
      }
      if (typing || !canEdit) return;
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'z') {
        event.preventDefault();
        if (event.shiftKey) redo(); else undo();
      }
      if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'y') {
        event.preventDefault();
        redo();
      }
    };
    window.addEventListener('keyبه پایین', onKeyDown);
    return () => window.removeEventListener('keyبه پایین', onKeyDown);
  }, [busy, canEdit, handleSave, redo, undo]);

  const errorCount = validation ? Object.keys(validation.errors).length : 0;

  return (
    <div className="rounded-2xl bg-slate-50 p-3 text-slate-900">
      <nav aria-label="بخش‌های صفحه Studio" className="mb-3 flex flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1">
        {([['pages', 'صفحه‌ها'], ['design', 'سیستم طراحی'], ['components', 'استودیوی کامپوننت']] as const).map(([id, label]) => (
          <button
            key={id}
            type="button"
            aria-pressed={view === id}
            onClick={() => setView(id)}
            className={`rounded-lg px-3 py-2 text-xs font-semibold ${view === id ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100'}`}
          >
            {label}
          </button>
        ))}
      </nav>
      <div className={view === 'pages' ? '' : 'hidden'}>
      <div ref={headerRef} className="mb-3 flex flex-wrap items-وسط gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <div data-toolbar-item className="mr-auto flex items-وسط gap-2">
          <label className="text-xs font-medium text-slate-500" htmlFor="page-select">صفحه</label>
          <select
            id="page-select"
            className="rounded-lg border border-slate-300 px-3 py-2 text-sm"
            value={activeصفحه ?? ''}
            onتغییر={(event) => {
              const value = event.target.value;
              if (value === '') { reset(); setActiveصفحه(null); return; }
              if (dirty && !window.confirm('دور انداختن unsaved changes?')) return;
              void openصفحه(value);
            }}
          >
            <option value="">— انتخاب کنید —</option>
            {pages.map((item) => (
              <option key={item.id} value={item.id}>
                {item.title} · {item.locale.toUpperCase()} · {item.status}{item.legacyCode ? ` · ${item.legacyCode}` : ''}
              </option>
            ))}
          </select>
          {dirty && <span className="rounded-کامل bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">ذخیره‌نشده</span>}
          {page && <span data-testid="page-status" className={`rounded-کامل px-2 py-0.5 text-[11px] font-semibold ${
            page.status === 'published' ? 'bg-emerald-100 text-emerald-800'
              : page.status === 'scheduled' ? 'bg-indigo-100 text-indigo-800'
                : page.status === 'archived' ? 'bg-slate-200 text-slate-700'
                  : page.status === 'unpublished' ? 'bg-amber-100 text-amber-800'
                    : 'bg-slate-100 text-slate-700'
          }`}>{page.status === 'published' ? 'منتشرشده' : page.status}</span>}
        </div>
        <div data-toolbar-item className="flex flex-wrap items-وسط gap-1.5">
          <button type="button" aria-label="واگرد" title="واگرد (Ctrl/Cmd+Z)" disabled={!canEdit || past.length === 0} onClick={undo} className="rounded-lg border border-slate-200 px-2.5 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">↶</button>
          <button type="button" aria-label="ازنو" title="ازنو (Ctrl/Cmd+Shift+Z)" disabled={!canEdit || future.length === 0} onClick={redo} className="rounded-lg border border-slate-200 px-2.5 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">↷</button>
          <button type="button" onClick={() => setقالب‌هاOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">قالب‌ها</button>
          <button type="button" onClick={() => setCreateOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">صفحهٔ جدید</button>
          <button type="button" onClick={() => setرسانهOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">رسانه</button>
          <button type="button" disabled={!page} onClick={() => setتاریخچهOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">تاریخچه</button>
          <button type="button" disabled={!draft} onClick={() => setپیش‌نمایش(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">پیش‌نمایش</button>
          <button type="button" disabled={!canEdit || !draft || busy !== null} onClick={() => void handleSave()} className="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">
            {busy === 'save' ? 'در حال ذخیره…' : dirty ? 'ذخیرهٔ تغییرات' : 'ذخیره شد'}
          </button>
          <button type="button" disabled={!page || busy !== null} onClick={openانتشار} className="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">انتشار</button>
          {canManageکاربرs && <button type="button" onClick={() => void openدسترسیs()} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">نقش‌ها</button>}
        </div>
      </div>

      {conflict && (
        <div role="alert" className="mb-2 flex flex-wrap items-وسط gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-800">
          <span className="font-semibold">تضاد:</span> این صفحه جای دیگری ذخیره شده است. پیش از ادامه، آخرین نسخه را بارگذاری کنید.
          <button type="button" className="rounded bg-red-700 px-2 py-1 font-semibold text-white" onClick={() => activeصفحه && void openصفحه(activeصفحه)}>بارگذاری آخرین نسخه</button>
        </div>
      )}
      {recovery && (
        <div role="alert" className="mb-2 flex flex-wrap items-وسط gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
          <span className="font-semibold">ویرایش بازیابی‌شده:</span> یک نسخهٔ محلی ذخیره‌نشده از {new Date(recovery.at).toزبانTimeString()} با پیش‌نویس سرور تفاوت دارد.
          <button type="button" className="rounded bg-amber-700 px-2 py-1 font-semibold text-white" onClick={() => { useEditor.setState({ draft: recovery.document, dirty: true }); setRecovery(null); }}>بازیابی</button>
          <button type="button" className="rounded border border-amber-300 px-2 py-1 font-semibold" onClick={() => { if (page) window.localStorage.removeآیتم(`ea-studio:backبه بالا:${page.id}`); setRecovery(null); }}>دور انداختن</button>
        </div>
      )}
      <div ref={statusRef} role="status" data-testid="studio-status" aria-live="polite" className="min-h-6 px-1 text-xs">
        {status && (
          <span className={status.tone === 'error' ? 'font-medium text-red-700' : status.tone === 'ok' ? 'font-medium text-emerald-700' : 'text-slate-600'}>
            {status.message}
          </span>
        )}
        {errorCount > 0 && <span className="ml-2 font-semibold text-red-700">{errorCount} خطای اعتبارسنجی</span>}
      </div>

      {!draft && (
        <div className="mt-3 rounded-xl border border-dashed border-slate-300 bg-white p-10 text-وسط">
          <h2 className="text-base font-semibold">صفحه‌ای انتخاب نشده است</h2>
          <p className="mt-1 text-sm text-slate-500">یک صفحهٔ موجود را از بالا انتخاب کنید یا برای شروع، پیش‌نویس بسازید.</p>
          <button type="button" onClick={() => setCreateOpen(true)} className="mt-4 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">ساخت صفحه</button>
        </div>
      )}

      {draft && (
        <div className="grid gap-3 lg:grid-cols-[16rem_minmax(0,1fr)_20rem]">
          <div className="flex flex-col rounded-xl border border-slate-200 bg-white p-3 lg:max-h-[70vh]">
            <div role="tablist" aria-label="پنل چپ" className="mb-2 flex gap-1 rounded-lg bg-slate-100 p-1">
              {([['بلوک', 'بلوک‌ها'], ['layers', 'لایه‌ها']] as const).map(([id, label]) => (
                <button
                  key={id}
                  type="button"
                  role="tab"
                  aria-selected={leftTab === id}
                  onClick={() => setLeftTab(id)}
                  className={`flex-1 rounded-md px-2 py-1.5 text-xs font-semibold ${leftTab === id ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'}`}
                >
                  {label}
                </button>
              ))}
            </div>
            <div className="min-h-0 flex-1">
              {leftTab === 'بلوک' ? (
                <Library components={رجیستری} canEdit={canEdit} onAdd={(definition) => useEditor.getState().addComponent(definition, sectionId ?? undefined)} />
              ) : (
                <لایه‌هاPanel
                  document={draft}
                  definitions={definitions}
                  selected={selected}
                  activeبخش={sectionId}
                  canEdit={canEdit}
                  onSelectComponent={(id, idبخش) => { select(id); setبخشId(idبخش); }}
                  onSelectبخش={(id) => { setبخشId(id); select(null); }}
                />
              )}
            </div>
          </div>
          <div className="lg:max-h-[70vh] lg:overflow-y-auto lg:pr-1">
            <Canvas
              document={draft}
              definitions={definitions}
              canEdit={canEdit}
              device={device}
              onDeviceتغییر={setDevice}
              media={media}
              onUpload={handleUpload}
              busy={busy !== null}
              onAddComponent={(id) => { setبخشId(id); select(null); }}
              onInspectبخش={(section) => { setبخشId(section.id); select(null); }}
            />
          </div>
          <div className="rounded-xl border border-slate-200 bg-white p-3 lg:max-h-[70vh]">
            <بازرس
              document={draft}
              definitions={definitions}
              section={activeبخش}
              canEdit={canEdit}
              media={media}
              onUpload={handleUpload}
              onحذفبخش={(id) => { useEditor.getState().removeبخش(id); setبخشId(null); }}
            />
          </div>
        </div>
      )}

      {mediaOpen && (
        <رسانهDialog open={mediaOpen} onOpenتغییر={setرسانهOpen} items={media} onUpload={handleUpload} onMeta={handleرسانهMeta} busy={busy === 'به بالاload'} />
      )}
      <CreateصفحهDialog open={createOpen} onOpenتغییر={setCreateOpen} busy={busy === 'create'} onCreate={handleCreate} />
      {page && (
        <انتشارDialog
          open={publishOpen}
          onOpenتغییر={setPublishOpen}
          busy={busy === 'publish'}
          title={draft?.title ?? page.title}
          status={page.status}
          code={page.legacyCode}
          slug={page.slug}
          scheduledAt={page.scheduledAt ?? null}
          checks={qualityChecks}
          canPublish={canPublish}
          canزمان‌بندی={canزمان‌بندی}
          canمسیر={canمسیر}
          previewUrl={previewUrl}
          onPublish={handlePublish}
          onزمان‌بندی={handleزمان‌بندی}
          onانصرافزمان‌بندی={handleانصرافزمان‌بندی}
          onلغو انتشار={handleلغو انتشار}
          onآرشیو={handleآرشیو}
          onمسیر={handleمسیر}
          onپیش‌نمایشLink={handleپیش‌نمایشLink}
        />
      )}
      <دسترسیsDialog
        open={دسترسیOpen}
        onOpenتغییر={setدسترسیsOpen}
        users={permissionData.users}
        roles={permissionData.roles}
        matrix={permissionData.matrix}
        canManage={canManageکاربرs}
        busy={busy !== null}
        onAssign={handleAssignنقش}
      />
      <قالب‌هاDialog
        open={templatesOpen}
        onOpenتغییر={setقالب‌هاOpen}
        api={api}
        onCreated={(created) => {
          load(created);
          setActiveصفحه(created.id);
          notify('ok', 'صفحه از قالب ساخته شد.');
        }}
      />
      {page && draft && (
        <تاریخچهDialog
          open={historyOpen}
          onOpenتغییر={setتاریخچهOpen}
          api={api}
          page={page}
          current={draft}
          busy={busy !== null}
          onبازیابی={handleبازیابی}
          onRollback={handleRollback}
        />
      )}
      {preview && draft && <پیش‌نمایش api={api} document={draft} initialعرض={device} onبستن={() => setپیش‌نمایش(false)} />}
      </div>
      {view === 'design' && <طراحیSystemManager api={api} config={config} notify={notify} />}
      {view === 'components' && <ComponentStudio api={api} config={config} notify={notify} />}
      <p className="mt-3 text-xs text-slate-500">
        {themes.length} نسخهٔ پوسته · رجیستری {config.رجیسترینسخه} · طرح‌واره {config.طرح‌وارهنسخه}
      </p>
    </div>
  );
}

export پیش‌فرض function App() {
  return (
    <ErrorBoundary>
      <AppShell />
    </ErrorBoundary>
  );
}
