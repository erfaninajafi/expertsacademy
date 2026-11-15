(function () {
  'use strict';

  var root = document.querySelector('.cms-builder[data-page-code]');
  if (!root || !window.fetch) return;

  var code = root.getAttribute('data-page-code');
  var state = null;
  var locale = 'fa';
  var selectedId = null;
  var workingData = null;
  var dirty = false;
  var busy = false;
  var fieldSerial = 0;

  var els = {
    status: document.getElementById('cms-builder-status'),
    message: document.getElementById('cms-builder-message'),
    legacy: document.getElementById('cms-builder-legacy'),
    activate: document.getElementById('cms-builder-activate'),
    workspace: document.getElementById('cms-builder-workspace'),
    structure: document.getElementById('cms-builder-structure'),
    structureStatus: document.getElementById('cms-builder-structure-status'),
    share: document.getElementById('cms-builder-share'),
    search: document.getElementById('cms-builder-search'),
    catalog: document.getElementById('cms-builder-catalog-list'),
    catalogEmpty: document.getElementById('cms-builder-catalog-empty'),
    instances: document.getElementById('cms-builder-instances'),
    count: document.getElementById('cms-builder-count'),
    canvasEmpty: document.getElementById('cms-builder-canvas-empty'),
    inspectorEmpty: document.getElementById('cms-builder-inspector-empty'),
    inspector: document.getElementById('cms-builder-inspector-content')
  };

  function node(tag, className, content) {
    var el = document.createElement(tag);
    if (className) el.className = className;
    if (content !== undefined) el.textContent = String(content);
    return el;
  }

  function setStatus(message, kind) {
    els.status.textContent = message;
    els.status.setAttribute('data-state', kind || 'idle');
  }

  function showMessage(message, tone) {
    els.message.textContent = message;
    els.message.setAttribute('data-tone', tone || 'info');
    els.message.hidden = false;
  }

  function clearMessage() {
    els.message.hidden = true;
    els.message.textContent = '';
  }

  function normalize(value) {
    return String(value || '').toLowerCase().replace(/ي/g, 'ی').replace(/ك/g, 'ک').trim();
  }

  function clone(value) {
    return JSON.parse(JSON.stringify(value || {}));
  }

  function entries(value) {
    if (!value || typeof value !== 'object') return [];
    if (Array.isArray(value)) {
      return value.map(function (item, index) { return [String(index), item]; });
    }
    return Object.keys(value).map(function (key) { return [key, value[key]]; });
  }

  function catalogEntry(key) {
    var catalog = state && state.catalog;
    if (!catalog) return null;
    if (Array.isArray(catalog)) {
      return catalog.filter(function (item) { return item.key === key; })[0] || null;
    }
    return catalog[key] || null;
  }

  function currentInstances() {
    var items = state && state.instances && state.instances[locale];
    return Array.isArray(items) ? items : [];
  }

  function selectedInstance() {
    return currentInstances().filter(function (item) { return String(item.id) === String(selectedId); })[0] || null;
  }

  function currentLayout() {
    return state && state.layouts && state.layouts[locale] ? state.layouts[locale] : { mode: 'legacy', revision: 0 };
  }

  function structuresMatch() {
    if (!state || !state.layouts || state.layouts.fa.mode !== 'builder' || state.layouts.en.mode !== 'builder') return false;
    var fa = state.instances.fa || [];
    var en = state.instances.en || [];
    if (fa.length !== en.length) return false;
    return fa.every(function (item, index) {
      var translation = en[index];
      return translation && item.key === translation.key && item.marker === translation.marker && item.enabled === translation.enabled;
    });
  }

  function setBusy(value) {
    busy = value;
    root.classList.toggle('is-busy', value);
  }

  function confirmDiscard() {
    if (!dirty) return true;
    return window.confirm('تغییرات این نمونه هنوز ذخیره نشده‌اند. بدون ذخیره ادامه می‌دهید؟');
  }

  function requestJson(url, options) {
    return fetch(url, options).then(function (response) {
      return response.json().then(function (body) {
        if (!response.ok || !body.ok) {
          var error = new Error(body.error || body.message || 'درخواست انجام نشد.');
          error.status = response.status;
          error.body = body;
          throw error;
        }
        return body;
      });
    });
  }

  function useResponse(body) {
    var next = body.state && body.state.page ? body.state : body;
    if (!next.page || !next.layouts || !next.instances) return false;
    if (!next.csrf && state) next.csrf = state.csrf;
    state = next;
    if (Array.isArray(next.page.available_locales) && next.page.available_locales.indexOf(locale) === -1) {
      locale = next.page.available_locales[0] || 'fa';
    }
    if (!selectedInstance()) selectedId = null;
    workingData = selectedInstance() ? clone(selectedInstance().data) : null;
    dirty = false;
    render();
    return true;
  }

  function load() {
    setStatus('در حال بارگذاری…', 'saving');
    return requestJson('page_builder_api.php?code=' + encodeURIComponent(code), {
      credentials: 'same-origin', cache: 'no-store'
    }).then(function (body) {
      useResponse(body);
      setStatus('آمادهٔ ویرایش', 'saved');
    }).catch(function (error) {
      showMessage(error.message, 'error');
      setStatus('خطا در بارگذاری', 'error');
    });
  }

  function mutation(action, data, successMessage) {
    if (busy || !state) return Promise.resolve();
    clearMessage();
    var knownIds = currentInstances().map(function (item) { return String(item.id); });
    var request = { action: action, code: code, csrf: state.csrf };
    if (action !== 'activate') request.revision = currentLayout().revision;
    Object.keys(data || {}).forEach(function (key) { request[key] = data[key]; });
    setBusy(true);
    setStatus('در حال ذخیره…', 'saving');
    return requestJson('page_builder_api.php', {
      method: 'POST', credentials: 'same-origin', cache: 'no-store',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(request)
    }).then(function (body) {
      if ((action === 'add' || action === 'duplicate') && body.instances && Array.isArray(body.instances[locale])) {
        var created = body.instances[locale].filter(function (item) { return knownIds.indexOf(String(item.id)) === -1; });
        if (created.length) selectedId = created[0].id;
      }
      if (!useResponse(body)) {
        return load();
      }
      setStatus('تغییرات ذخیره شد', 'saved');
      if (successMessage) showMessage(successMessage, 'success');
    }).catch(function (error) {
      if (error.status === 409) {
        showMessage('این صفحه در جای دیگری تغییر کرده است. برای جلوگیری از بازنویسی، نسخهٔ فعلی ذخیره نشد. پس از نگه‌داشتن متن خود، صفحه را دوباره بارگذاری کنید.', 'error');
      } else {
        showMessage(error.message, 'error');
      }
      setStatus('ذخیره نشد', 'error');
    }).then(function () { setBusy(false); });
  }

  function uploadImage(file, target, picker) {
    if (!file || !state || busy) return;
    clearMessage();
    var form = new FormData();
    form.append('action', 'upload');
    form.append('code', code);
    form.append('locale', locale);
    form.append('csrf', state.csrf);
    form.append('file', file);
    setBusy(true);
    setStatus('در حال بارگذاری تصویر…', 'saving');
    requestJson('page_builder_api.php', {
      method: 'POST', credentials: 'same-origin', body: form
    }).then(function (body) {
      if (!body.file) throw new Error('نام فایل بارگذاری‌شده دریافت نشد.');
      var existing = target.value.trim();
      target.value = target.hasAttribute('data-companion') && existing ? existing + ',' + body.file : body.file;
      dirty = true;
      setStatus('تغییرات ذخیره نشده', 'saving');
      showMessage('تصویر بارگذاری شد. برای ثبت آن در نمونه، دکمهٔ ذخیرهٔ محتوا را انتخاب کنید.', 'success');
    }).catch(function (error) {
      setStatus('بارگذاری تصویر ناموفق بود', 'error');
      showMessage(error.message, 'error');
    }).then(function () {
      picker.value = '';
      setBusy(false);
    });
  }

  function renderCatalog() {
    els.catalog.replaceChildren();
    var term = normalize(els.search.value);
    var groups = {};
    var catalog = Array.isArray(state.catalog) ? state.catalog : Object.keys(state.catalog || {}).map(function (key) { return state.catalog[key]; });
    catalog.forEach(function (item) {
      if (!item || !item.key) return;
      if (item.hidden || item.builder_hidden) return;
      if (term && normalize([item.label, item.description, item.category, item.key].join(' ')).indexOf(term) === -1) return;
      var labels = { content: 'محتوا', media: 'رسانه', actions: 'دعوت به اقدام', lists: 'فهرست‌ها', composites: 'ترکیبی', forms: 'فرم‌ها' };
      var category = labels[item.category] || item.category || 'سایر';
      if (!groups[category]) groups[category] = [];
      groups[category].push(item);
    });
    var categories = Object.keys(groups);
    els.catalogEmpty.hidden = categories.length > 0;
    categories.forEach(function (category) {
      var group = node('div', 'cms-builder__catalog-group');
      group.appendChild(node('h3', '', category));
      groups[category].forEach(function (item) {
        var button = node('button', 'cms-builder__catalog-item');
        button.type = 'button';
        button.setAttribute('data-add-key', item.key);
        button.setAttribute('aria-label', 'افزودن ' + item.label);
        var icon = node('span', 'cms-builder__catalog-icon');
        icon.appendChild(node('i', 'bx bx-plus'));
        icon.setAttribute('aria-hidden', 'true');
        var copy = node('span', 'cms-builder__catalog-copy');
        copy.appendChild(node('strong', '', item.label || item.key));
        if (item.description) copy.appendChild(node('small', '', item.description));
        button.appendChild(icon);
        button.appendChild(copy);
        button.appendChild(node('span', 'cms-builder__catalog-add', '+'));
        group.appendChild(button);
      });
      els.catalog.appendChild(group);
    });
  }

  function actionButton(label, action, id, disabled, danger) {
    var button = node('button', danger ? 'is-danger' : '', label);
    button.type = 'button';
    button.setAttribute('data-instance-action', action);
    button.setAttribute('data-instance-id', id);
    button.disabled = !!disabled;
    return button;
  }

  function renderInstances() {
    els.instances.replaceChildren();
    var instances = currentInstances();
    els.count.textContent = instances.length + ' سکشن';
    els.canvasEmpty.hidden = instances.length > 0;
    instances.forEach(function (item, index) {
      var type = catalogEntry(item.key) || {};
      var li = node('li', 'cms-builder__instance');
      if (String(item.id) === String(selectedId)) li.classList.add('is-selected');
      if (item.enabled === false || item.enabled === 0 || item.enabled === '0') li.classList.add('is-disabled');
      var main = node('button', 'cms-builder__instance-main');
      main.type = 'button';
      main.setAttribute('data-select-id', item.id);
      main.setAttribute('aria-pressed', String(item.id) === String(selectedId) ? 'true' : 'false');
      main.appendChild(node('span', 'cms-builder__instance-index', index + 1));
      var copy = node('span', 'cms-builder__instance-copy');
      copy.appendChild(node('strong', '', type.label || item.marker || item.key));
      copy.appendChild(node('small', '', 'شناسه: ' + String(item.id).slice(0, 12) + ' · ' + (item.marker || item.key)));
      main.appendChild(copy);
      var disabled = item.enabled === false || item.enabled === 0 || item.enabled === '0';
      var incomplete = item.status === 'untranslated' || item.status === 'incomplete';
      var badgeText = disabled ? 'غیرفعال' : type.storage === 'none' ? 'آماده' : incomplete ? 'محتوا ناقص' : 'فعال';
      var badge = node('span', 'cms-builder__instance-badge' + (disabled ? ' is-disabled' : incomplete ? ' is-draft' : ''), badgeText);
      main.appendChild(badge);
      li.appendChild(main);
      var actions = node('div', 'cms-builder__instance-actions');
      actions.appendChild(actionButton('↑ بالا', 'move-up', item.id, index === 0));
      actions.appendChild(actionButton('↓ پایین', 'move-down', item.id, index === instances.length - 1));
      actions.appendChild(actionButton('تکرار', 'duplicate', item.id));
      actions.appendChild(actionButton(disabled ? 'فعال‌کردن' : 'غیرفعال‌کردن', 'toggle', item.id));
      actions.appendChild(actionButton('حذف', 'delete', item.id, false, true));
      li.appendChild(actions);
      els.instances.appendChild(li);
    });
  }

  function removeEditors() {
    if (!window.tinymce) return;
    Array.prototype.forEach.call(els.inspector.querySelectorAll('textarea.cms-builder-richtext'), function (textarea) {
      var editor = window.tinymce.get(textarea.id);
      if (editor) editor.remove();
    });
  }

  function createField(name, definition, value, itemIndex, companion) {
    definition = definition || {};
    var type = definition.type || 'text';
    var wrap = node('div', 'cms-builder__field');
    var id = 'cms-builder-field-' + (++fieldSerial);
    var label = node('label', '', definition.label || name);
    label.htmlFor = id;
    if (definition.required) label.appendChild(node('span', 'required', ' *'));
    wrap.appendChild(label);
    var control;
    if (type === 'textarea' || type === 'richtext' || type === 'html') {
      control = node('textarea', 'form-control' + ((type === 'richtext' || type === 'html') ? ' cms-builder-richtext' : ''));
      control.rows = type === 'textarea' ? 4 : 8;
      control.value = value == null ? '' : String(value);
    } else if (type === 'select') {
      control = node('select', 'form-control');
      var options = definition.options || [];
      entries(options).forEach(function (pair) {
        var optionData = pair[1];
        var option = node('option');
        option.value = typeof optionData === 'object' ? String(optionData.value) : (Array.isArray(options) ? String(optionData) : pair[0]);
        option.textContent = typeof optionData === 'object' ? String(optionData.label || optionData.value) : String(optionData);
        option.selected = String(value == null ? '' : value) === option.value;
        control.appendChild(option);
      });
    } else if (type === 'checkbox' || type === 'boolean') {
      control = node('input', 'form-check-input');
      control.type = 'checkbox';
      control.checked = value === true || value === 1 || value === '1';
    } else {
      control = node('input', 'form-control');
      control.type = ['url', 'color', 'number', 'email'].indexOf(type) !== -1 ? type : 'text';
      control.value = value == null ? '' : String(value);
      if (type === 'image' || type === 'file') control.placeholder = 'مسیر فایل یا تصویر';
    }
    control.id = id;
    control.setAttribute('data-field', name);
    if (itemIndex !== null && itemIndex !== undefined) control.setAttribute('data-item-index', String(itemIndex));
    if (companion) control.setAttribute('data-companion', '1');
    if (definition.required && type !== 'richtext' && type !== 'html') control.required = true;
    if (locale === 'en' && ['url', 'image', 'file', 'color', 'number'].indexOf(type) === -1) control.dir = 'ltr';
    if (['url', 'image', 'file'].indexOf(type) !== -1) control.dir = 'ltr';
    wrap.appendChild(control);
    if (type === 'image') {
      var upload = node('input', 'form-control cms-builder__upload');
      upload.type = 'file';
      upload.accept = 'image/jpeg,image/png,image/webp,image/gif';
      upload.setAttribute('data-upload-for', id);
      upload.setAttribute('aria-label', 'بارگذاری تصویر برای ' + (definition.label || name));
      wrap.appendChild(upload);
      wrap.appendChild(node('small', 'cms-builder__field-note', 'JPEG، PNG، WebP یا GIF تا ۸ مگابایت. نام فایل پس از بارگذاری در فیلد بالا قرار می‌گیرد.'));
    } else if (type === 'file') {
      wrap.appendChild(node('small', 'cms-builder__field-note', 'نام فایل موجود در سایت را وارد کنید.'));
    }
    return wrap;
  }

  function initEditors() {
    if (!window.tinymce) return;
    Array.prototype.forEach.call(els.inspector.querySelectorAll('textarea.cms-builder-richtext'), function (textarea) {
      if (window.tinymce.get(textarea.id)) return;
      window.tinymce.init({
        selector: '#' + textarea.id,
        language: locale === 'fa' ? 'fa' : 'en',
        directionality: locale === 'fa' ? 'rtl' : 'ltr',
        height: 230,
        menubar: false,
        convert_urls: false,
        plugins: 'lists link preview paste directionality',
        toolbar: 'undo redo | formatselect | bold italic | bullist numlist | link unlink | removeformat | preview',
        setup: function (editor) {
          editor.on('change keyup undo redo', function () {
            dirty = true;
            setStatus('تغییرات ذخیره نشده', 'saving');
          });
        }
      });
    });
  }

  function renderInspector() {
    removeEditors();
    els.inspector.replaceChildren();
    var instance = selectedInstance();
    els.inspectorEmpty.hidden = !!instance;
    els.inspector.hidden = !instance;
    if (!instance) return;
    if (!workingData) workingData = clone(instance.data);
    var type = catalogEntry(instance.key) || {};
    var header = node('div', 'cms-builder__inspector-header');
    header.appendChild(node('h3', '', type.label || instance.key));
    header.appendChild(node('code', '', String(instance.id)));
    els.inspector.appendChild(header);
    var form = node('form');
    form.id = 'cms-builder-content-form';
    form.noValidate = false;
    var fields = entries(type.fields);
    if (type.companion_fields && entries(type.companion_fields).length) {
      form.appendChild(node('h4', 'cms-builder__item-title', 'عنوان و تنظیمات بخش'));
      entries(type.companion_fields).forEach(function (pair) {
        var companion = workingData.companion || {};
        form.appendChild(createField(pair[0], pair[1], companion[pair[0]], null, true));
      });
    }
    if (type.multiple || type.storage === 'content') {
      form.appendChild(node('h4', 'cms-builder__item-title', 'آیتم‌های این نمونه'));
      var items = Array.isArray(workingData.items) ? workingData.items : [];
      items.forEach(function (item, index) {
        var card = node('div', 'cms-builder__item');
        var row = node('div', 'cms-builder__item-header');
        row.appendChild(node('strong', '', 'آیتم ' + (index + 1)));
        var buttons = node('span');
        [['↑', 'up'], ['↓', 'down'], ['حذف', 'remove']].forEach(function (action) {
          var button = node('button', '', action[0]);
          button.type = 'button';
          button.setAttribute('data-item-action', action[1]);
          button.setAttribute('data-item-index', String(index));
          if (action[1] === 'up' && index === 0 || action[1] === 'down' && index === items.length - 1) button.disabled = true;
          buttons.appendChild(button);
        });
        row.appendChild(buttons);
        card.appendChild(row);
        fields.forEach(function (pair) {
          card.appendChild(createField(pair[0], pair[1], item[pair[0]], index, false));
        });
        form.appendChild(card);
      });
      var addItem = node('button', 'btn btn-outline-primary', 'افزودن آیتم');
      addItem.type = 'button';
      addItem.setAttribute('data-add-item', '1');
      form.appendChild(addItem);
    } else {
      fields.forEach(function (pair) {
        form.appendChild(createField(pair[0], pair[1], workingData[pair[0]], null, false));
      });
    }
    if (!fields.length && !entries(type.companion_fields).length) {
      form.appendChild(node('p', 'cms-builder__empty-small', 'این کامپوننت تنظیمات محتوایی ندارد.'));
    }
    var actions = node('div', 'cms-builder__inspector-actions');
    var save = node('button', 'btn btn-primary', 'ذخیره محتوای نمونه');
    save.type = 'submit';
    if (!fields.length && !entries(type.companion_fields).length) save.hidden = true;
    actions.appendChild(save);
    form.appendChild(actions);
    els.inspector.appendChild(form);
    initEditors();
  }

  function collectEditorData() {
    if (!workingData) workingData = {};
    if (window.tinymce && window.tinymce.triggerSave) window.tinymce.triggerSave();
    Array.prototype.forEach.call(els.inspector.querySelectorAll('[data-field]'), function (control) {
      var name = control.getAttribute('data-field');
      var value = control.type === 'checkbox' ? control.checked : control.value;
      var itemIndex = control.getAttribute('data-item-index');
      if (itemIndex !== null) {
        if (!Array.isArray(workingData.items)) workingData.items = [];
        if (!workingData.items[Number(itemIndex)]) workingData.items[Number(itemIndex)] = {};
        workingData.items[Number(itemIndex)][name] = value;
      } else if (control.hasAttribute('data-companion')) {
        if (!workingData.companion) workingData.companion = {};
        workingData.companion[name] = value;
      } else {
        workingData[name] = value;
      }
    });
    return workingData;
  }

  function render() {
    if (!state) return;
    Array.prototype.forEach.call(root.querySelectorAll('[data-locale]'), function (button) {
      var active = button.getAttribute('data-locale') === locale;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
      button.disabled = Array.isArray(state.page.available_locales) && state.page.available_locales.indexOf(button.getAttribute('data-locale')) === -1;
    });
    var legacy = currentLayout().mode === 'legacy';
    els.legacy.hidden = !legacy;
    els.workspace.hidden = legacy;
    if (legacy) {
      removeEditors();
      return;
    }
    var shared = currentLayout().structure_mode === 'shared';
    var bothReady = state.layouts.fa.mode === 'builder' && state.layouts.en.mode === 'builder';
    els.structure.hidden = !bothReady;
    if (bothReady) {
      els.structureStatus.textContent = shared ? 'ساختار دو زبان مشترک است؛ محتوا برای هر زبان جداگانه ذخیره می‌شود.' :
        (structuresMatch() ? 'چیدمان دو زبان یکسان است و می‌توان آن را مشترک کرد.' : 'چیدمان دو زبان متفاوت است؛ هر زبان ساختار مستقل دارد.');
      els.share.hidden = shared || !structuresMatch();
    }
    renderCatalog();
    renderInstances();
    renderInspector();
  }

  root.addEventListener('click', function (event) {
    var button = event.target.closest('button');
    if (!button || busy || !state) return;
    if (button.hasAttribute('data-locale')) {
      var nextLocale = button.getAttribute('data-locale');
      if (nextLocale === locale || !confirmDiscard()) return;
      locale = nextLocale;
      selectedId = null;
      workingData = null;
      dirty = false;
      clearMessage();
      render();
      setStatus('آمادهٔ ویرایش', 'saved');
      return;
    }
    if (button === els.activate) {
      if (!window.confirm('چیدمان قدیمی این زبان به ساختار نمونه‌ها منتقل می‌شود و روش نمایش عمومی آن تغییر می‌کند. پیش از ادامه، از داده‌ها نسخهٔ پشتیبان داشته باشید. ادامه می‌دهید؟')) return;
      mutation('activate', { locale: locale }, 'ساختار جدید برای این زبان فعال شد.');
      return;
    }
    if (button === els.share) {
      if (!confirmDiscard()) return;
      if (!window.confirm('ساختار یکسان دو زبان به یک ترتیب مشترک تبدیل شود؟ محتوای هر زبان جدا باقی می‌ماند و نمونه‌های قبلی برای بازگردانی نگهداری می‌شوند.')) return;
      mutation('share_structure', { locale: locale, revision_fa: state.layouts.fa.revision, revision_en: state.layouts.en.revision }, 'ساختار دو زبان مشترک شد.');
      return;
    }
    if (button.hasAttribute('data-add-key')) {
      if (!confirmDiscard()) return;
      selectedId = null;
      mutation('add', { key: button.getAttribute('data-add-key'), locale: locale }, 'کامپوننت به صفحه افزوده شد.');
      return;
    }
    if (button.hasAttribute('data-select-id')) {
      var id = button.getAttribute('data-select-id');
      if (String(id) === String(selectedId) || !confirmDiscard()) return;
      selectedId = id;
      workingData = clone(selectedInstance().data);
      dirty = false;
      renderInstances();
      renderInspector();
      setStatus('آمادهٔ ویرایش', 'saved');
      return;
    }
    if (button.hasAttribute('data-instance-action')) {
      if (!confirmDiscard()) return;
      var action = button.getAttribute('data-instance-action');
      var instanceId = button.getAttribute('data-instance-id');
      if (action === 'delete' && !window.confirm('این نمونه و محتوای مستقل آن حذف شود؟')) return;
      if (action === 'move-up' || action === 'move-down') {
        mutation('move', { id: instanceId, locale: locale, direction: action === 'move-up' ? 'up' : 'down' });
      } else {
        mutation(action, { id: instanceId, locale: locale });
      }
      return;
    }
    if (button.hasAttribute('data-add-item')) {
      collectEditorData();
      if (!Array.isArray(workingData.items)) workingData.items = [];
      workingData.items.push({});
      dirty = true;
      renderInspector();
      setStatus('تغییرات ذخیره نشده', 'saving');
      return;
    }
    if (button.hasAttribute('data-item-action')) {
      collectEditorData();
      var index = Number(button.getAttribute('data-item-index'));
      var itemAction = button.getAttribute('data-item-action');
      var items = workingData.items || [];
      if (itemAction === 'remove') {
        if (!window.confirm('این آیتم از نمونه حذف شود؟')) return;
        items.splice(index, 1);
      } else {
        var target = itemAction === 'up' ? index - 1 : index + 1;
        if (target < 0 || target >= items.length) return;
        var temp = items[index];
        items[index] = items[target];
        items[target] = temp;
      }
      dirty = true;
      renderInspector();
      setStatus('تغییرات ذخیره نشده', 'saving');
    }
  });

  root.addEventListener('input', function (event) {
    if (event.target.closest('#cms-builder-content-form')) {
      dirty = true;
      setStatus('تغییرات ذخیره نشده', 'saving');
    }
  });

  root.addEventListener('change', function (event) {
    var picker = event.target;
    if (!picker.hasAttribute('data-upload-for')) return;
    var target = document.getElementById(picker.getAttribute('data-upload-for'));
    if (target && picker.files && picker.files[0]) uploadImage(picker.files[0], target, picker);
  });

  root.addEventListener('submit', function (event) {
    if (event.target.id !== 'cms-builder-content-form') return;
    event.preventDefault();
    if (busy || !selectedId) return;
    var form = event.target;
    if (form.reportValidity && !form.reportValidity()) return;
    var data = clone(collectEditorData());
    mutation('save_content', { id: selectedId, locale: locale, data: data }, 'محتوای نمونه ذخیره شد.');
  });

  els.search.addEventListener('input', function () { if (state) renderCatalog(); });
  window.addEventListener('beforeunload', function (event) {
    if (!dirty) return;
    event.preventDefault();
    event.returnValue = '';
  });
  load();
})();
