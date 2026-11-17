import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import gsap from 'gsap';
import { animate } from 'motion';
import { ApiError, createApi, validate, type ApiClient } from './api';
import { useEditor } from './store';
import { localizeComponent, statusLabel } from './i18n';
import type {
  ComponentDefinition,
  Device,
  MediaItem,
  PageDocument,
  PageRecord,
  PageSummary,
  QualityChecks,
  RoleUser,
  StudioConfig,
  ThemeRecord,
  ValidationResult,
} from './types';
import { Library } from './components/Library';
import { Canvas } from './components/Canvas';
import { Inspector } from './components/Inspector';
import { Preview } from './components/Preview';
import { CreatePageDialog, MediaDialog, PermissionsDialog, PublishingDialog } from './components/Dialogs';
import { ErrorBoundary } from './components/ErrorBoundary';
import { DesignSystemManager } from './components/DesignSystemManager';
import { ComponentStudio } from './components/ComponentStudio';
import { HistoryDialog } from './components/HistoryDialog';
import { TemplatesDialog } from './components/TemplatesDialog';
import { LayersPanel } from './components/LayersPanel';

type Status = { tone: 'ok' | 'error' | 'info'; message: string };

function readConfig(): StudioConfig {
  const node = document.getElementById('ea-studio-config');
  if (!node?.textContent) throw new Error('تنظیمات Page Studio یافت نشد.');
  return JSON.parse(node.textContent) as StudioConfig;
}

function AppShell() {
  const config = useMemo(readConfig, []);
  const api: ApiClient = useMemo(() => createApi(config), [config]);
  const permissions = config.permissions ?? [];
  const can = (permission: string): boolean => permissions.length > 0
    ? permissions.includes(permission)
    : config.capabilities.includes('publish');
  const canEdit = can('pages.edit') || (permissions.length === 0 && config.capabilities.includes('edit'));
  const canPublish = can('pages.publish');
  const canSchedule = can('pages.schedule');
  const canRoute = can('pages.route');
  const canManageUsers = can('users.manage');

  const { draft, page, selected, dirty, load, reset, select, past, future, undo, redo } = useEditor();
  const [registry, setRegistry] = useState<ComponentDefinition[]>([]);
  const [pages, setPages] = useState<PageSummary[]>([]);
  const [activePage, setActivePage] = useState<string | null>(null);
  const [sectionId, setSectionId] = useState<string | null>(null);
  const [status, setStatus] = useState<Status | null>(null);
  const [busy, setBusy] = useState<'save' | 'publish' | 'create' | 'upload' | null>(null);
  const [preview, setPreview] = useState(false);
  const [createOpen, setCreateOpen] = useState(false);
  const [publishOpen, setPublishOpen] = useState(false);
  const [qualityChecks, setQualityChecks] = useState<QualityChecks | null>(null);
  const [previewUrl, setPreviewUrl] = useState<string | null>(null);
  const [permissionsOpen, setpermissionsOpen] = useState(false);
  const [permissionData, setpermissionsData] = useState<{ users: RoleUser[]; roles: string[]; matrix: Record<string, string[]> }>({
    users: [], roles: [], matrix: {},
  });
  const [historyOpen, setHistoryOpen] = useState(false);
  const [templatesOpen, setTemplatesOpen] = useState(false);
  const [mediaOpen, setMediaOpen] = useState(false);
  const [media, setMedia] = useState<MediaItem[]>([]);
  const [validation, setValidation] = useState<ValidationResult | null>(null);
  const [conflict, setConflict] = useState(false);
  const [recovery, setRecovery] = useState<{ document: PageDocument; at: number } | null>(null);
  const saveRef = useRef<(silent?: boolean) => Promise<void>>(async () => {});
  const [view, setView] = useState<'pages' | 'design' | 'components'>('pages');
  const [leftTab, setLeftTab] = useState<'blocks' | 'layers'>('blocks');
  const [device, setDevice] = useState<Device>('desktop');
  const [themes, setThemes] = useState<ThemeRecord[]>([]);
  const headerRef = useRef<HTMLDivElement>(null);
  const statusRef = useRef<HTMLDivElement>(null);

  const definitions = useMemo(() => new Map(registry.map((item) => [item.type, item])), [registry]);
  const activeSection = useMemo(
    () => draft?.sections.find((section) => section.id === sectionId) ?? null,
    [draft, sectionId],
  );

  const notify = (tone: Status['tone'], message: string): void => setStatus({ tone, message });

  useEffect(() => {
    if (!statusRef.current) return;
    const controls = animate(statusRef.current, { opacity: [0.4, 1], y: [4, 0] }, { duration: 0.2 });
    return () => controls.stop();
  }, [status]);

  useEffect(() => {
    if (!headerRef.current) return;
    const context = gsap.context(() => {
      gsap.from('[data-toolbar-item]', { y: -6, opacity: 0, duration: 0.3, stagger: 0.04, ease: 'power2.out' });
    }, headerRef);
    return () => context.revert();
  }, []);

  const refreshPages = useCallback(async () => {
    const response = await api.get<{ pages: PageSummary[]; total: number }>('pages');
    setPages(response.pages);
    return response.pages;
  }, [api]);

  useEffect(() => {
    void (async () => {
      try {
        const [registryResponse, mediaResponse, themesResponse] = await Promise.all([
          api.get<{ components: ComponentDefinition[] }>('registry'),
          api.get<{ items: MediaItem[] }>('media'),
          api.get<{ items: ThemeRecord[] }>('themes'),
        ]);
        setRegistry(registryResponse.components.map(localizeComponent));
        setMedia(mediaResponse.items);
        setThemes(themesResponse.items);
        await refreshPages();
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بارگذاری Page Studio ممکن نشد.');
      }
    })();
  }, [api, refreshPages]);

  const openPage = useCallback(
    async (id: string) => {
      try {
        const response = await api.get<{ page: PageRecord }>('pages', { id });
        load(response.page);
        setActivePage(id);
        setSectionId(response.page.document?.sections[0]?.id ?? null);
        setValidation(null);
        setConflict(false);
        const backup = window.localStorage.getItem(`ea-studio:backup:${id}`);
        if (backup && response.page.document) {
          try {
            const parsed = JSON.parse(backup) as { document: PageDocument; at: number };
            if (parsed.document && JSON.stringify(parsed.document) !== JSON.stringify(response.page.document)) {
              setRecovery(parsed);
            }
          } catch {
            window.localStorage.removeItem(`ea-studio:backup:${id}`);
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
    if (activePage || pages.length === 0) return;
    void openPage(pages[0].id);
  }, [activePage, openPage, pages]);

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
      const response = await api.send<{ page: PageRecord }>('pages', 'PUT', {
        document: draft,
        expectedVersion: page.version,
      }, page.id);
      useEditor.setState({ page: response.page, dirty: false });
      setActivePage(response.page.id);
      window.localStorage.removeItem(`ea-studio:backup:${response.page.id}`);
      setConflict(false);
      await refreshPages();
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
  }, [api, config, draft, page, refreshPages]);

  const loadChecks = useCallback(async (id: string) => {
    try {
      const response = await api.get<{ checks: QualityChecks }>('quality', { id });
      setQualityChecks(response.checks);
    } catch {
      setQualityChecks(null);
    }
  }, [api]);

  const openPublishing = useCallback(() => {
    setPreviewUrl(null);
    setPublishOpen(true);
    setQualityChecks(null);
    if (page) void loadChecks(page.id);
  }, [loadChecks, page]);

  const applyPage = useCallback(async (updated: PageRecord, message: string) => {
    useEditor.setState({ page: updated, dirty: false });
    setActivePage(updated.id);
    await refreshPages();
    await loadChecks(updated.id);
    notify('ok', message);
  }, [loadChecks, refreshPages]);

  const handlePublish = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord; checks?: QualityChecks }>('publish', 'POST', {
        expectedVersion: page.version,
      }, page.id);
      await applyPage(response.page, `نسخهٔ ${response.page.version} منتشر شد.`);
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
  }, [api, applyPage, loadChecks, page]);

  const handleSchedule = useCallback(async (publishAt: string, timezone: string) => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord }>('schedule', 'POST', {
        expectedVersion: page.version, publishAt, timezone,
      }, page.id);
      await applyPage(response.page, `زمان‌بندی برای ${response.page.scheduledAt ?? publishAt} (UTC) ثبت شد.`);
    } catch (error) {
      if (error instanceof ApiError && error.status === 422 && page) await loadChecks(page.id);
      notify('error', error instanceof Error ? error.message : 'زمان‌بندی انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyPage, loadChecks, page]);

  const handleCancelSchedule = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord }>('schedule_cancel', 'POST', {
        expectedVersion: page.version,
      }, page.id);
      await applyPage(response.page, 'زمان‌بندی لغو شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'لغو زمان‌بندی ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyPage, page]);

  const handleUnpublish = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord }>('unpublish', 'POST', {
        expectedVersion: page.version,
      }, page.id);
      await applyPage(response.page, 'انتشار صفحه لغو شد. بازدیدکنندگان دیگر این نسخه را نمی‌بینند.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'لغو انتشار انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyPage, page]);

  const handleArchive = useCallback(async () => {
    if (!page || !window.confirm('این صفحه آرشیو شود؟ از سایت عمومی و صف زمان‌بندی خارج می‌شود.')) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord }>('archive', 'POST', {
        expectedVersion: page.version,
      }, page.id);
      await applyPage(response.page, 'صفحه آرشیو شد.');
      setPublishOpen(false);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'آرشیو کردن انجام نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyPage, page]);

  const handleRoute = useCallback(async (slug: string, code: string) => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ page: PageRecord }>('route', 'PUT', {
        slug, legacyCode: code, expectedVersion: page.version,
      }, page.id);
      await applyPage(response.page, 'مسیر ذخیره شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ مسیر ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, applyPage, page]);

  const handlePreviewLink = useCallback(async () => {
    if (!page) return;
    setBusy('publish');
    try {
      const response = await api.send<{ url: string; expiresAt: string }>('preview-token', 'POST', {}, page.id);
      const absolute = new URL(response.url, window.location.href).toString();
      setPreviewUrl(absolute);
      await navigator.clipboard?.writeText(absolute);
      notify('ok', `لینک پیش‌نمایش کپی شد. اعتبار آن تا ${response.expiresAt} است.`);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ساخت لینک پیش‌نمایش ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api, page]);

  const openPermissions = useCallback(async () => {
    try {
      const response = await api.get<{ users: RoleUser[]; roles: string[]; matrix: Record<string, string[]> }>('permissions');
      setpermissionsData(response);
      setpermissionsOpen(true);
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'بارگذاری دسترسی‌ها ممکن نشد.');
    }
  }, [api]);

  const handleAssignRole = useCallback(async (userId: string, role: string) => {
    setBusy('save');
    try {
      const response = await api.send<{ users: RoleUser[] }>('role', 'POST', { userId, role });
      setpermissionsData((current) => ({ ...current, users: response.users }));
      notify('ok', 'نقش به‌روزرسانی شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'به‌روزرسانی نقش ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api]);

  const handleRestore = useCallback(
    async (revisionId: string) => {
      if (!page) return;
      setBusy('save');
      try {
        const response = await api.send<{ page: PageRecord }>('restore', 'POST', {
          revisionId,
          expectedVersion: page.version,
        }, page.id);
        load(response.page);
        setActivePage(response.page.id);
        window.localStorage.removeItem(`ea-studio:backup:${response.page.id}`);
        setConflict(false);
        setHistoryOpen(false);
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
        const response = await api.send<{ page: PageRecord }>('rollback', 'POST', {
          revisionId,
          expectedVersion: page.version,
        }, page.id);
        await applyPage(response.page, `بازگردانی شد: نسخهٔ ${revisionId.slice(0, 8)} اکنون منتشر است.`);
        setHistoryOpen(false);
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'بازگردانی انجام نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api, applyPage, page],
  );

  const handleCreate = useCallback(
    async (input: { title: string; locale: 'fa' | 'en'; legacyCode: string }) => {
      setBusy('create');
      try {
        const response = await api.send<{ page: PageRecord }>('pages', 'POST', {
          title: input.title,
          locale: input.locale,
          legacyCode: input.legacyCode || null,
        });
        setCreateOpen(false);
        await refreshPages();
        load(response.page);
        setActivePage(response.page.id);
        setSectionId(response.page.document?.sections[0]?.id ?? null);
        notify('ok', 'پیش‌نویس ساخته شد.');
      } catch (error) {
        notify('error', error instanceof Error ? error.message : 'ساخت صفحه ممکن نشد.');
      } finally {
        setBusy(null);
      }
    },
    [api, load, refreshPages],
  );

  const handleMediaMeta = useCallback(async (id: string, alt: string, focalX: number, focalY: number) => {
    setBusy('upload');
    try {
      await api.send('media', 'PUT', { alt, focalX, focalY }, id);
      const response = await api.get<{ items: MediaItem[] }>('media');
      setMedia(response.items);
      notify('ok', 'اطلاعات تصویر ذخیره شد.');
    } catch (error) {
      notify('error', error instanceof Error ? error.message : 'ذخیرهٔ اطلاعات تصویر ممکن نشد.');
    } finally {
      setBusy(null);
    }
  }, [api]);

  const handleUpload = useCallback(
    async (file: File, alt: string) => {
      setBusy('upload');
      try {
        await api.upload(file, alt);
        const response = await api.get<{ items: MediaItem[] }>('media');
        setMedia(response.items);
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
      window.localStorage.setItem(`ea-studio:backup:${page.id}`, JSON.stringify({ document: draft, at: Date.now() }));
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
    window.addEventListener('keydown', onKeyDown);
    return () => window.removeEventListener('keydown', onKeyDown);
  }, [busy, canEdit, handleSave, redo, undo]);

  const errorCount = validation ? Object.keys(validation.errors).length : 0;

  return (
    <div dir="rtl" className="rounded-2xl bg-slate-50 p-3 text-right text-slate-900">
      <nav aria-label="بخش‌های Page Studio" className="mb-3 flex flex-wrap gap-1 rounded-xl border border-slate-200 bg-white p-1">
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
      <div ref={headerRef} className="mb-3 flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
        <div data-toolbar-item className="ml-auto flex min-w-0 max-w-full items-center gap-2">
          <label className="text-xs font-medium text-slate-500" htmlFor="page-select">صفحه</label>
          <select
            id="page-select"
            className="min-w-0 max-w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            value={activePage ?? ''}
            onChange={(event) => {
              const value = event.target.value;
              if (value === '') { reset(); setActivePage(null); return; }
              if (dirty && !window.confirm('تغییرات ذخیره‌نشده دور انداخته شود؟')) return;
              void openPage(value);
            }}
          >
            <option value="">— انتخاب کنید —</option>
            {pages.map((item) => (
              <option key={item.id} value={item.id}>
                {item.title} · {item.locale.toUpperCase()} · {statusLabel(item.status)}{item.legacyCode ? ` · ${item.legacyCode}` : ''}
              </option>
            ))}
          </select>
          {dirty && <span className="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800">ذخیره‌نشده</span>}
          {page && <span data-testid="page-status" className={`rounded-full px-2 py-0.5 text-[11px] font-semibold ${
            page.status === 'published' ? 'bg-emerald-100 text-emerald-800'
              : page.status === 'scheduled' ? 'bg-indigo-100 text-indigo-800'
                : page.status === 'archived' ? 'bg-slate-200 text-slate-700'
                  : page.status === 'unpublished' ? 'bg-amber-100 text-amber-800'
                    : 'bg-slate-100 text-slate-700'
          }`}>{statusLabel(page.status)}</span>}
        </div>
        <div data-toolbar-item className="flex flex-wrap items-center gap-1.5">
          <button type="button" aria-label="واگرد" title="واگرد (Ctrl/Cmd+Z)" disabled={!canEdit || past.length === 0} onClick={undo} className="rounded-lg border border-slate-200 px-2.5 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">↶</button>
          <button type="button" aria-label="ازنو" title="ازنو (Ctrl/Cmd+Shift+Z)" disabled={!canEdit || future.length === 0} onClick={redo} className="rounded-lg border border-slate-200 px-2.5 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">↷</button>
          <button type="button" onClick={() => setTemplatesOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">قالب‌ها</button>
          <button type="button" onClick={() => setCreateOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">صفحهٔ جدید</button>
          <button type="button" onClick={() => setMediaOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">رسانه</button>
          <button type="button" disabled={!page} onClick={() => setHistoryOpen(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">تاریخچه</button>
          <button type="button" disabled={!draft} onClick={() => setPreview(true)} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400 disabled:opacity-40">پیش‌نمایش</button>
          <button type="button" disabled={!canEdit || !draft || busy !== null} onClick={() => void handleSave()} className="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">
            {busy === 'save' ? 'در حال ذخیره…' : dirty ? 'ذخیرهٔ تغییرات' : 'ذخیره شد'}
          </button>
          <button type="button" disabled={!page || busy !== null} onClick={openPublishing} className="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-semibold text-white disabled:opacity-40">انتشار</button>
          {canManageUsers && <button type="button" onClick={() => void openPermissions()} className="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-indigo-400">نقش‌ها</button>}
        </div>
      </div>

      {conflict && (
        <div role="alert" className="mb-2 flex flex-wrap items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-800">
          <span className="font-semibold">تضاد:</span>این صفحه جای دیگری ذخیره شده است. پیش از ادامه، آخرین نسخه را بارگذاری کنید.<button type="button" className="rounded bg-red-700 px-2 py-1 font-semibold text-white" onClick={() => activePage && void openPage(activePage)}>بارگذاری آخرین نسخه</button>
        </div>
      )}
      {recovery && (
        <div role="alert" className="mb-2 flex flex-wrap items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
          <span className="font-semibold">ویرایش بازیابی‌شده:</span> یک نسخهٔ محلی ذخیره‌نشده از {new Date(recovery.at).toLocaleTimeString()} با پیش‌نویس سرور تفاوت دارد.
          <button type="button" className="rounded bg-amber-700 px-2 py-1 font-semibold text-white" onClick={() => { useEditor.setState({ draft: recovery.document, dirty: true }); setRecovery(null); }}>بازیابی</button>
          <button type="button" className="rounded border border-amber-300 px-2 py-1 font-semibold" onClick={() => { if (page) window.localStorage.removeItem(`ea-studio:backup:${page.id}`); setRecovery(null); }}>دور انداختن</button>
        </div>
      )}
      <div ref={statusRef} role="status" data-testid="studio-status" aria-live="polite" className="min-h-6 px-1 text-xs">
        {status && (
          <span className={status.tone === 'error' ? 'font-medium text-red-700' : status.tone === 'ok' ? 'font-medium text-emerald-700' : 'text-slate-600'}>
            {status.message}
          </span>
        )}
        {errorCount > 0 && <span className="mr-2 font-semibold text-red-700">{errorCount} خطای اعتبارسنجی</span>}
      </div>

      {!draft && (
        <div className="mt-3 rounded-xl border border-dashed border-slate-300 bg-white p-10 text-center">
          <h2 className="text-base font-semibold">صفحه‌ای انتخاب نشده است</h2>
          <p className="mt-1 text-sm text-slate-500">یک صفحهٔ موجود را از بالا انتخاب کنید یا برای شروع، پیش‌نویس بسازید.</p>
          <button type="button" onClick={() => setCreateOpen(true)} className="mt-4 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">ساخت صفحه</button>
        </div>
      )}

      {draft && (
        <div className="grid gap-3 lg:grid-cols-[16rem_minmax(0,1fr)_20rem]">
          <div className="flex flex-col rounded-xl border border-slate-200 bg-white p-3 lg:max-h-[70vh]">
            <div role="tablist" aria-label="پنل چپ" className="mb-2 flex gap-1 rounded-lg bg-slate-100 p-1">
              {([['blocks', 'بلوک‌ها'], ['layers', 'لایه‌ها']] as const).map(([id, label]) => (
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
              {leftTab === 'blocks' ? (
                <Library components={registry} canEdit={canEdit} onAdd={(definition) => useEditor.getState().addComponent(definition, sectionId ?? undefined)} />
              ) : (
                <LayersPanel
                  document={draft}
                  definitions={definitions}
                  selected={selected}
                  activeSection={sectionId}
                  canEdit={canEdit}
                  onSelectComponent={(id, idSection) => { select(id); setSectionId(idSection); }}
                  onSelectSection={(id) => { setSectionId(id); select(null); }}
                />
              )}
            </div>
          </div>
          <div className="lg:max-h-[70vh] lg:overflow-y-auto lg:pl-1">
            <Canvas
              document={draft}
              definitions={definitions}
              canEdit={canEdit}
              device={device}
              onDeviceChange={setDevice}
              media={media}
              onUpload={handleUpload}
              busy={busy !== null}
              onAddComponent={(id) => { setSectionId(id); select(null); }}
              onInspectSection={(section) => { setSectionId(section.id); select(null); }}
            />
          </div>
          <div className="rounded-xl border border-slate-200 bg-white p-3 lg:max-h-[70vh]">
            <Inspector
              document={draft}
              definitions={definitions}
              section={activeSection}
              canEdit={canEdit}
              media={media}
              onUpload={handleUpload}
              onDeleteSection={(id) => { useEditor.getState().removeSection(id); setSectionId(null); }}
            />
          </div>
        </div>
      )}

      {mediaOpen && (
        <MediaDialog open={mediaOpen} onOpenChange={setMediaOpen} items={media} onUpload={handleUpload} onMeta={handleMediaMeta} busy={busy === 'upload'} />
      )}
      <CreatePageDialog open={createOpen} onOpenChange={setCreateOpen} busy={busy === 'create'} onCreate={handleCreate} />
      {page && (
        <PublishingDialog
          open={publishOpen}
          onOpenChange={setPublishOpen}
          busy={busy === 'publish'}
          title={draft?.title ?? page.title}
          status={page.status}
          code={page.legacyCode}
          slug={page.slug}
          scheduledAt={page.scheduledAt ?? null}
          checks={qualityChecks}
          canPublish={canPublish}
          canSchedule={canSchedule}
          canRoute={canRoute}
          previewUrl={previewUrl}
          onPublish={handlePublish}
          onSchedule={handleSchedule}
          onCancelSchedule={handleCancelSchedule}
          onUnpublish={handleUnpublish}
          onArchive={handleArchive}
          onRoute={handleRoute}
          onPreviewLink={handlePreviewLink}
        />
      )}
      <PermissionsDialog
        open={permissionsOpen}
        onOpenChange={setpermissionsOpen}
        users={permissionData.users}
        roles={permissionData.roles}
        matrix={permissionData.matrix}
        canManage={canManageUsers}
        busy={busy !== null}
        onAssign={handleAssignRole}
      />
      <TemplatesDialog
        open={templatesOpen}
        onOpenChange={setTemplatesOpen}
        api={api}
        onCreated={(created) => {
          load(created);
          setActivePage(created.id);
          notify('ok', 'صفحه از قالب ساخته شد.');
        }}
      />
      {page && draft && (
        <HistoryDialog
          open={historyOpen}
          onOpenChange={setHistoryOpen}
          api={api}
          page={page}
          current={draft}
          busy={busy !== null}
          onRestore={handleRestore}
          onRollback={handleRollback}
        />
      )}
      {preview && draft && <Preview api={api} document={draft} initialWidth={device} onClose={() => setPreview(false)} />}
      </div>
      {view === 'design' && <DesignSystemManager api={api} config={config} notify={notify} />}
      {view === 'components' && <ComponentStudio api={api} config={config} notify={notify} />}
      <p className="mt-3 text-xs text-slate-500">
        {themes.length} نسخهٔ پوسته · رجیستری {config.registryVersion} · طرح‌واره {config.schemaVersion}
      </p>
    </div>
  );
}

export default function App() {
  return (
    <ErrorBoundary>
      <AppShell />
    </ErrorBoundary>
  );
}
