(function () {
  'use strict';

  function editorHtml(textarea) {
    if (window.tinymce) {
      var editor = textarea.id ? window.tinymce.get(textarea.id) : null;
      if (!editor && window.tinymce.editors) {
        editor = window.tinymce.editors.find(function (item) {
          return item.getElement && item.getElement() === textarea;
        });
      }
      if (editor) return editor.getContent();
    }
    return textarea.value;
  }

  function safePreviewHtml(html) {
    var documentFragment = new DOMParser().parseFromString(html, 'text/html');
    documentFragment.querySelectorAll('script, iframe, object, embed, form, meta, base, link, svg, math, audio, video, source, style').forEach(function (node) {
      node.remove();
    });
    documentFragment.querySelectorAll('*').forEach(function (node) {
      Array.from(node.attributes).forEach(function (attribute) {
        var name = attribute.name.toLowerCase();
        if (name.startsWith('on') || name === 'href' || name === 'srcset' || name === 'formaction') {
          node.removeAttribute(attribute.name);
        }
        if (name === 'src' && !/^data:image\/(?:png|jpeg|gif|webp);base64,/i.test(attribute.value)) {
          node.removeAttribute(attribute.name);
        }
      });
    });
    return documentFragment.body.innerHTML;
  }

  function createDialog() {
    var dialog = document.createElement('dialog');
    dialog.className = 'cms-preview-dialog';

    var header = document.createElement('div');
    header.className = 'cms-preview-dialog__header';
    var title = document.createElement('strong');
    title.textContent = 'پیش‌نمایش متن';
    var close = document.createElement('button');
    close.type = 'button';
    close.className = 'btn btn-outline-secondary';
    close.textContent = 'بستن';
    close.addEventListener('click', function () { dialog.close(); });
    header.append(title, close);

    var body = document.createElement('div');
    body.className = 'cms-preview-dialog__body';
    dialog.append(header, body);
    document.body.append(dialog);
    return { dialog: dialog, body: body };
  }

  function openPreview(form, preview) {
    preview.body.replaceChildren();
    form.querySelectorAll('textarea.editor').forEach(function (textarea) {
      var label = textarea.closest('.form-group')?.querySelector('label');
      var heading = document.createElement('h3');
      heading.textContent = label ? label.textContent.trim() : textarea.name;

      var frame = document.createElement('iframe');
      frame.setAttribute('sandbox', '');
      frame.setAttribute('referrerpolicy', 'no-referrer');
      frame.title = heading.textContent;
      frame.srcdoc = '<!doctype html><html dir="rtl"><head><meta charset="utf-8"><meta http-equiv="Content-Security-Policy" content="default-src \'none\'; img-src data:; style-src \'unsafe-inline\'; base-uri \'none\'; form-action \'none\'"><style>body{font:16px/1.8 sans-serif;padding:20px;overflow-wrap:anywhere;color:#293241}img{max-width:100%;height:auto}table{border-collapse:collapse;max-width:100%}td,th{border:1px solid #ddd;padding:6px}</style></head><body>' + safePreviewHtml(editorHtml(textarea)) + '</body></html>';
      preview.body.append(heading, frame);
    });
    preview.dialog.showModal();
  }

  function init() {
    var forms = Array.from(document.querySelectorAll('form.cms-editor-form')).filter(function (form) {
      return form.querySelector('textarea.editor');
    });
    if (!forms.length || typeof HTMLDialogElement === 'undefined') return;
    var preview = createDialog();

    forms.forEach(function (form) {
      var saveBar = form.querySelector('.cms-save-bar');
      if (!saveBar) return;

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'btn btn-outline-primary';
      button.textContent = 'پیش‌نمایش متن';
      button.addEventListener('click', function () { openPreview(form, preview); });
      saveBar.insertBefore(button, saveBar.firstChild);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
