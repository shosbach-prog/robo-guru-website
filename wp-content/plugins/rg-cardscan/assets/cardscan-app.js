/* ── RG CardScan – Frontend App ── */
(function() {
  'use strict';

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => [...(ctx || document).querySelectorAll(sel)];
  const el = (tag, attrs, ...children) => {
    const e = document.createElement(tag);
    if (attrs) Object.entries(attrs).forEach(([k, v]) => {
      if (k === 'className') e.className = v;
      else if (k.startsWith('on')) e.addEventListener(k.slice(2).toLowerCase(), v);
      else if (k === 'html') e.innerHTML = v;
      else if (k === 'style' && typeof v === 'object') Object.assign(e.style, v);
      else e.setAttribute(k, v);
    });
    children.flat().forEach(c => {
      if (c == null) return;
      e.appendChild(typeof c === 'string' ? document.createTextNode(c) : c);
    });
    return e;
  };

  const AJAX = rgcsData.ajaxUrl;
  const NONCE = rgcsData.nonce;
  let token = sessionStorage.getItem('rgcs-token') || '';
  let contacts = [];
  let stream = null;
  let pendingContact = null;
  let facingMode = 'environment'; // 'environment' = back, 'user' = front
  let lastPhotoBase64 = ''; // Store last scanned photo for saving

  const TEAM = ['Pierre Holein', 'Sebastian Hosbach', 'Alexander Kiss', 'Daniel Senkstock', 'Nicht bekannt'];

  const root = $('#rgcs-app');
  if (!root) return;

  /* ── Helpers ── */
  function post(action, data = {}) {
    const fd = new FormData();
    fd.append('action', action);
    fd.append('nonce', NONCE);
    if (token) fd.append('token', token);
    Object.entries(data).forEach(([k, v]) => fd.append(k, v));
    return fetch(AJAX, { method: 'POST', body: fd }).then(r => r.json());
  }

  function icon(svg) { return el('span', { html: svg, style: { display: 'inline-flex', alignItems: 'center' } }); }

  const IC = {
    card: '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><circle cx="8" cy="11" r="2.5"/><path d="M14 9h4M14 13h3"/></svg>',
    camera: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>',
    users: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    logout: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>',
    login: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg>',
    upload: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg>',
    download: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>',
    search: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>',
    back: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 18l-6-6 6-6"/></svg>',
    check: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>',
    edit: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>',
    save: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>',
    right: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"><path d="M9 18l6-6-6-6"/></svg>',
    cardBig: '<svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.5" stroke-linecap="round"><rect x="2" y="4" width="20" height="16" rx="2"/><circle cx="8" cy="11" r="2.5"/><path d="M14 9h4M14 13h3"/></svg>',
    emptyUsers: '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4-4v2"/><circle cx="9" cy="7" r="4"/></svg>',
    flip: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M11 19H4a2 2 0 01-2-2V7a2 2 0 012-2h5M13 5h7a2 2 0 012 2v10a2 2 0 01-2 2h-5"/><path d="M12 1l3 3-3 3"/><path d="M12 23l-3-3 3-3"/></svg>',
    rotate: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 4v6h6"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>',
    person: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4-4v2"/><circle cx="12" cy="7" r="4"/></svg>',
  };

  const FIELDS = [
    { key: 'name', label: 'Name' },
    { key: 'company', label: 'Firma' },
    { key: 'position', label: 'Position' },
    { key: 'email', label: 'E-Mail', type: 'email' },
    { key: 'phone', label: 'Telefon', type: 'tel' },
    { key: 'mobile', label: 'Mobil', type: 'tel' },
    { key: 'website', label: 'Website', type: 'url' },
    { key: 'address', label: 'Adresse' },
    { key: 'notes', label: 'Notizen' },
    { key: 'bemerkung', label: 'Bemerkung', textarea: true },
  ];

  /* ════════════════════════════════════════════
     RENDER: LOGIN
     ════════════════════════════════════════════ */
  function renderLogin(err) {
    root.innerHTML = '';
    const card = el('div', { className: 'rgcs-login-card' },
      el('div', { className: 'rgcs-login-logo' }, icon(IC.card)),
      el('h1', null, 'CardScan'),
      el('p', { className: 'rgcs-sub' }, 'Messe-Kontakterfassung'),
      err ? el('div', { className: 'rgcs-login-error' }, err) : null,
      el('div', { className: 'rgcs-login-field' },
        el('label', null, 'Benutzer'),
        el('input', { type: 'text', id: 'rgcs-user', placeholder: 'Benutzername', autocomplete: 'username' })
      ),
      el('div', { className: 'rgcs-login-field' },
        el('label', null, 'Passwort'),
        el('div', { className: 'rgcs-pass-wrap' },
          el('input', { type: 'password', id: 'rgcs-pass', placeholder: 'Passwort', autocomplete: 'current-password' }),
          el('button', { className: 'rgcs-pass-toggle', type: 'button', onClick: () => {
            const inp = $('#rgcs-pass');
            inp.type = inp.type === 'password' ? 'text' : 'password';
          }}, '👁')
        )
      ),
      el('button', { className: 'rgcs-login-btn', onClick: doLogin }, icon(IC.login), 'Anmelden'),
      el('p', { className: 'rgcs-login-footer' }, 'Geschützt für das RoboPlanet-Team')
    );
    root.appendChild(el('div', { className: 'rgcs-login' }, card));

    // Enter key
    $$('#rgcs-user, #rgcs-pass').forEach(i => i.addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); }));
  }

  async function doLogin() {
    const u = ($('#rgcs-user') || {}).value || '';
    const p = ($('#rgcs-pass') || {}).value || '';
    const res = await post('rgcs_login', { username: u, password: p });
    if (res.success) {
      token = res.data.token;
      sessionStorage.setItem('rgcs-token', token);
      await loadContacts();
      renderApp('scanner');
    } else {
      renderLogin(res.data || 'Login fehlgeschlagen.');
    }
  }

  function doLogout() {
    token = '';
    sessionStorage.removeItem('rgcs-token');
    stopCamera();
    renderLogin();
  }

  /* ════════════════════════════════════════════
     RENDER: MAIN APP
     ════════════════════════════════════════════ */
  let currentView = 'scanner';
  let selectedContact = null;
  let msgTimeout = null;

  async function loadContacts() {
    const res = await post('rgcs_list');
    if (res.success) contacts = res.data;
  }

  function renderApp(view, msg) {
    currentView = view || currentView;
    stopCamera();
    root.innerHTML = '';

    // Header
    const header = el('div', { className: 'rgcs-header' },
      el('div', { className: 'rgcs-header-left' },
        el('div', { className: 'rgcs-header-icon' }, icon(IC.card)),
        el('div', null,
          el('h2', null, 'CardScan'),
          el('p', { className: 'rgcs-sub' }, 'Messe-Kontakterfassung')
        )
      ),
      el('div', { className: 'rgcs-header-right' },
        el('div', { className: 'rgcs-badge' }, contacts.length + ' Kontakte'),
        el('button', { className: 'rgcs-logout-btn', title: 'Abmelden', onClick: doLogout }, icon(IC.logout))
      )
    );
    root.appendChild(header);

    // Tabs
    const tabs = el('div', { className: 'rgcs-tabs' },
      el('button', { className: 'rgcs-tab' + (currentView === 'scanner' ? ' active' : ''), onClick: () => renderApp('scanner') },
        icon(IC.camera), 'Scanner'),
      el('button', { className: 'rgcs-tab' + (currentView !== 'scanner' ? ' active' : ''), onClick: () => { selectedContact = null; renderApp('contacts'); } },
        icon(IC.users), 'Kontakte (' + contacts.length + ')')
    );
    root.appendChild(tabs);

    // Message
    if (msg) {
      const isErr = msg.startsWith('!');
      const text = isErr ? msg.slice(1) : msg;
      root.appendChild(el('div', { className: isErr ? 'rgcs-error' : 'rgcs-success', id: 'rgcs-msg' }, text));
      clearTimeout(msgTimeout);
      msgTimeout = setTimeout(() => { const m = $('#rgcs-msg'); if (m) m.remove(); }, 4000);
    }

    if (currentView === 'scanner') renderScanner();
    else if (selectedContact) renderDetail();
    else renderContactList();
  }

  /* ── Scanner View ── */
  let cameraActive = false;
  let scanning = false;

  function renderScanner() {
    const section = el('div', { className: 'rgcs-scanner' });

    // Camera box
    const camBox = el('div', { className: 'rgcs-camera-box' });
    const videoEl = el('video', { playsinline: '', muted: '' });
    videoEl.setAttribute('playsinline', '');
    videoEl.muted = true;

    camBox.appendChild(el('div', { className: 'rgcs-placeholder' },
      icon(IC.cardBig),
      el('p', null, 'Visitenkarte scannen'),
      el('small', null, 'Karte flach auf den Tisch legen, gerade von oben fotografieren')
    ));
    section.appendChild(camBox);

    // Action buttons
    const actions = el('div', { className: 'rgcs-actions' });
    const startBtn = el('button', { className: 'rgcs-btn-primary', onClick: () => startCamera(camBox, videoEl, actions) },
      icon(IC.camera), 'Kamera starten');
    actions.appendChild(startBtn);

    const fileInput = el('input', { type: 'file', accept: 'image/*', capture: 'environment', style: { display: 'none' } });
    fileInput.addEventListener('change', handleFile);
    const uploadBtn = el('button', { className: 'rgcs-btn-upload', onClick: () => fileInput.click() },
      icon(IC.upload), 'Foto hochladen');
    actions.appendChild(uploadBtn);
    actions.appendChild(fileInput);

    section.appendChild(actions);

    // Hidden canvas
    const canvas = el('canvas', { id: 'rgcs-canvas', style: { display: 'none' } });
    section.appendChild(canvas);

    root.appendChild(section);
  }

  let videoRotation = 0; // 0, 90, 180, 270

  async function startCamera(camBox, videoEl, actions) {
    try {
      stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: facingMode, width: { ideal: 3840 }, height: { ideal: 2160 } }
      });
      videoEl.srcObject = stream;
      await videoEl.play();
      cameraActive = true;

      camBox.innerHTML = '';
      camBox.style.position = 'relative';
      videoEl.style.transform = 'rotate(' + videoRotation + 'deg)';
      camBox.appendChild(videoEl);

      // Overlay
      const overlay = el('div', { className: 'rgcs-scan-overlay' },
        el('div', { className: 'rgcs-scan-frame' },
          el('div', { className: 'rgcs-scan-corner tl' }),
          el('div', { className: 'rgcs-scan-corner tr' }),
          el('div', { className: 'rgcs-scan-corner bl' }),
          el('div', { className: 'rgcs-scan-corner br' })
        )
      );
      camBox.appendChild(overlay);

      // Camera control buttons (top-right corner)
      const camControls = el('div', { className: 'rgcs-cam-controls' },
        el('button', { className: 'rgcs-cam-btn', title: 'Kamera drehen', onClick: () => {
          videoRotation = (videoRotation + 90) % 360;
          videoEl.style.transform = 'rotate(' + videoRotation + 'deg)';
        }}, icon(IC.rotate)),
        el('button', { className: 'rgcs-cam-btn', title: 'Kamera wechseln (Front/Back)', onClick: async () => {
          facingMode = facingMode === 'environment' ? 'user' : 'environment';
          stopCamera();
          videoRotation = 0;
          await startCamera(camBox, videoEl, actions);
        }}, icon(IC.flip))
      );
      camBox.appendChild(camControls);

      // Swap buttons
      actions.innerHTML = '';
      const captureBtn = el('button', { className: 'rgcs-capture-btn', onClick: () => doCapture(videoEl, camBox) },
        el('div', { className: 'rgcs-capture-inner' }));
      actions.appendChild(captureBtn);
      actions.appendChild(el('button', { className: 'rgcs-btn-secondary', onClick: () => { stopCamera(); videoRotation = 0; renderApp('scanner'); } }, 'Kamera stoppen'));

      const fileInput2 = el('input', { type: 'file', accept: 'image/*', capture: 'environment', style: { display: 'none' } });
      fileInput2.addEventListener('change', handleFile);
      actions.appendChild(el('button', { className: 'rgcs-btn-upload', onClick: () => fileInput2.click() }, icon(IC.upload), 'Foto hochladen'));
      actions.appendChild(fileInput2);
    } catch (e) {
      renderApp('scanner', '!Kamera-Zugriff nicht möglich. Bitte Berechtigung erteilen.');
    }
  }

  function stopCamera() {
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    cameraActive = false;
  }

  async function doCapture(videoEl, camBox) {
    if (scanning) return;
    const canvas = $('#rgcs-canvas') || document.createElement('canvas');
    const vw = videoEl.videoWidth;
    const vh = videoEl.videoHeight;
    const rot = videoRotation;

    // Swap canvas dimensions for 90/270 rotation
    if (rot === 90 || rot === 270) {
      canvas.width = vh;
      canvas.height = vw;
    } else {
      canvas.width = vw;
      canvas.height = vh;
    }

    const ctx = canvas.getContext('2d');
    ctx.save();
    ctx.translate(canvas.width / 2, canvas.height / 2);
    ctx.rotate((rot * Math.PI) / 180);
    ctx.drawImage(videoEl, -vw / 2, -vh / 2);
    ctx.restore();

    // Enhance contrast for better OCR
    enhanceImage(ctx, canvas.width, canvas.height);

    // Compress to stay under API limit
    const base64 = await compressToMaxSize(canvas);

    // Show scan line
    const frame = $('.rgcs-scan-frame');
    if (frame && !$('.rgcs-scan-line', frame)) frame.appendChild(el('div', { className: 'rgcs-scan-line' }));

    await scanImage(base64);
  }

  /* Enhance image contrast and sharpness for better OCR */
  function enhanceImage(ctx, w, h) {
    const imageData = ctx.getImageData(0, 0, w, h);
    const d = imageData.data;

    // Increase contrast by 30%
    const contrast = 1.3;
    const intercept = 128 * (1 - contrast);
    for (let i = 0; i < d.length; i += 4) {
      d[i]     = Math.min(255, Math.max(0, d[i]     * contrast + intercept)); // R
      d[i + 1] = Math.min(255, Math.max(0, d[i + 1] * contrast + intercept)); // G
      d[i + 2] = Math.min(255, Math.max(0, d[i + 2] * contrast + intercept)); // B
    }
    ctx.putImageData(imageData, 0, 0);
  }

  /**
   * Compress canvas to base64 JPEG, ensuring result stays under 3.5MB
   * (safe margin for 5MB API limit + base64 overhead + POST overhead).
   * Strategy: first try high quality, then step down quality,
   * then resize if still too large.
   */
  async function compressToMaxSize(canvas) {
    const MAX_BYTES = 3.5 * 1024 * 1024; // 3.5MB in base64 chars ≈ 2.6MB raw

    // Try quality steps first
    const qualities = [0.92, 0.85, 0.75, 0.60, 0.45];
    for (const q of qualities) {
      const dataUrl = canvas.toDataURL('image/jpeg', q);
      const b64 = dataUrl.split(',')[1];
      if (b64.length < MAX_BYTES) return b64;
    }

    // Still too big — downscale progressively
    let scale = 0.75;
    while (scale >= 0.25) {
      const small = document.createElement('canvas');
      small.width = Math.round(canvas.width * scale);
      small.height = Math.round(canvas.height * scale);
      const sCtx = small.getContext('2d');
      sCtx.drawImage(canvas, 0, 0, small.width, small.height);
      enhanceImage(sCtx, small.width, small.height);

      const dataUrl = small.toDataURL('image/jpeg', 0.75);
      const b64 = dataUrl.split(',')[1];
      if (b64.length < MAX_BYTES) return b64;
      scale -= 0.15;
    }

    // Fallback: aggressive resize
    const tiny = document.createElement('canvas');
    tiny.width = 1200;
    tiny.height = Math.round(canvas.height * (1200 / canvas.width));
    tiny.getContext('2d').drawImage(canvas, 0, 0, tiny.width, tiny.height);
    return tiny.toDataURL('image/jpeg', 0.6).split(',')[1];
  }

  function handleFile(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = async () => {
      // Process uploaded image through canvas for enhancement
      const img = new Image();
      img.onload = async () => {
        const canvas = document.createElement('canvas');
        // Limit max dimension to 2400px (good enough for business card OCR)
        const maxDim = 2400;
        let w = img.width, h = img.height;
        if (w > maxDim || h > maxDim) {
          const scale = maxDim / Math.max(w, h);
          w = Math.round(w * scale);
          h = Math.round(h * scale);
        }
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);
        enhanceImage(ctx, w, h);
        const base64 = await compressToMaxSize(canvas);
        await scanImage(base64);
      };
      img.src = reader.result;
    };
    reader.readAsDataURL(file);
    e.target.value = '';
  }

  async function scanImage(base64) {
    if (scanning) return;
    scanning = true;
    lastPhotoBase64 = base64; // Store for saving later

    // Show spinner
    const scanMsg = el('p', { className: 'rgcs-scanning-text', id: 'rgcs-scan-msg' }, 'KI analysiert Visitenkarte…');
    const scanner = $('.rgcs-scanner');
    if (scanner) scanner.appendChild(scanMsg);

    const res = await post('rgcs_scan', { image: base64 });
    scanning = false;
    const msg = $('#rgcs-scan-msg'); if (msg) msg.remove();
    const line = $('.rgcs-scan-line'); if (line) line.remove();

    if (res.success) {
      pendingContact = res.data;
      showRemarkModal();
    } else {
      renderApp('scanner', '!' + (res.data || 'Erkennung fehlgeschlagen.'));
    }
  }

  /* ── Remark Modal ── */
  function showRemarkModal() {
    const c = pendingContact;
    if (!c) return;

    const overlay = el('div', { className: 'rgcs-modal-overlay', id: 'rgcs-remark-modal' });
    const modal = el('div', { className: 'rgcs-modal' },
      el('div', { className: 'rgcs-modal-header' }, icon(IC.check), el('span', null, 'Kontakt erkannt!')),
      lastPhotoBase64 ? (() => {
        const thumb = el('img', { className: 'rgcs-modal-photo', src: 'data:image/jpeg;base64,' + lastPhotoBase64 });
        return thumb;
      })() : null,
      el('div', { className: 'rgcs-modal-preview' },
        el('div', { className: 'rgcs-avatar-sm' }, (c.name || '?')[0].toUpperCase()),
        el('div', null,
          el('div', { className: 'rgcs-name' }, c.name || 'Unbekannt'),
          c.company ? el('div', { className: 'rgcs-company' }, c.company) : null,
          c.email ? el('div', { className: 'rgcs-email' }, c.email) : null
        )
      ),
      el('div', { className: 'rgcs-remark-section' },
        el('label', null, icon(IC.person), ' Zuständig / Kontakt für'),
        (() => {
          const select = el('select', { id: 'rgcs-zustaendig', className: 'rgcs-select' });
          TEAM.forEach((name, i) => {
            const opt = el('option', { value: name }, name);
            if (i === TEAM.length - 1) opt.selected = true; // Default: Nicht bekannt
            select.appendChild(opt);
          });
          return select;
        })()
      ),
      el('div', { className: 'rgcs-remark-section' },
        el('label', null, icon(IC.edit), ' Bemerkung zum Gespräch'),
        el('textarea', {
          id: 'rgcs-remark-input',
          placeholder: 'z.B. Interesse an Gausium Scrubber 50, Follow-up nächste Woche…',
          rows: '3'
        })
      ),
      el('div', { className: 'rgcs-modal-actions' },
        el('button', { className: 'rgcs-modal-save', onClick: saveFromModal }, icon(IC.save), ' Kontakt speichern'),
        el('button', { className: 'rgcs-modal-cancel', onClick: cancelModal }, 'Verwerfen')
      )
    );
    overlay.appendChild(modal);
    root.appendChild(overlay);
  }

  async function saveFromModal() {
    const remark = ($('#rgcs-remark-input') || {}).value || '';
    const zustaendig = ($('#rgcs-zustaendig') || {}).value || 'Nicht bekannt';
    const c = pendingContact;
    if (!c) return;

    // Show saving state
    const saveBtn = $('.rgcs-modal-save');
    if (saveBtn) { saveBtn.textContent = 'Speichert…'; saveBtn.disabled = true; }

    // Compress photo for storage (max 800px wide, good enough for reference)
    let photoForStorage = '';
    if (lastPhotoBase64) {
      try {
        photoForStorage = await resizeBase64(lastPhotoBase64, 1200, 0.80);
      } catch (e) {
        photoForStorage = lastPhotoBase64; // fallback to original
      }
    }

    const res = await post('rgcs_save', {
      name: c.name || '', company: c.company || '', position: c.position || '',
      email: c.email || '', phone: c.phone || '', mobile: c.mobile || '',
      website: c.website || '', address: c.address || '', notes: c.notes || '',
      bemerkung: remark.trim(), zustaendig: zustaendig,
      photo: photoForStorage
    });

    pendingContact = null;
    lastPhotoBase64 = '';
    const modal = $('#rgcs-remark-modal'); if (modal) modal.remove();

    if (res.success) {
      await loadContacts();
      renderApp('scanner', '✓ ' + (c.name || 'Kontakt') + ' erfolgreich erfasst! E-Mail an ' + zustaendig + ' gesendet.');
    } else {
      renderApp('scanner', '!Speichern fehlgeschlagen.');
    }
  }

  /* Resize a base64 image to maxWidth for efficient storage */
  function resizeBase64(b64, maxWidth, quality) {
    return new Promise((resolve) => {
      const img = new Image();
      img.onload = () => {
        let w = img.width, h = img.height;
        if (w > maxWidth) {
          h = Math.round(h * (maxWidth / w));
          w = maxWidth;
        }
        const canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        canvas.getContext('2d').drawImage(img, 0, 0, w, h);
        resolve(canvas.toDataURL('image/jpeg', quality).split(',')[1]);
      };
      img.onerror = () => resolve(b64);
      img.src = 'data:image/jpeg;base64,' + b64;
    });
  }

  function cancelModal() {
    pendingContact = null;
    lastPhotoBase64 = '';
    const modal = $('#rgcs-remark-modal'); if (modal) modal.remove();
  }

  /* ── Contact List ── */
  function renderContactList() {
    const section = el('div', { className: 'rgcs-contacts' });

    // Toolbar
    const searchInput = el('input', { placeholder: 'Suchen…', id: 'rgcs-search' });
    searchInput.addEventListener('input', () => filterList());

    const toolbar = el('div', { className: 'rgcs-toolbar' },
      el('div', { className: 'rgcs-search' }, icon(IC.search), searchInput),
      el('button', { className: 'rgcs-btn-export', onClick: exportCSV }, icon(IC.download), 'CSV Export')
    );
    section.appendChild(toolbar);

    // List container
    section.appendChild(el('div', { id: 'rgcs-list-container' }));
    root.appendChild(section);
    filterList();
  }

  function filterList() {
    const term = ($('#rgcs-search') || {}).value?.toLowerCase() || '';
    const container = $('#rgcs-list-container');
    if (!container) return;
    container.innerHTML = '';

    const filtered = contacts.filter(c => {
      if (!term) return true;
      return [c.name, c.company, c.email, c.position_title, c.zustaendig].some(f => (f || '').toLowerCase().includes(term));
    });

    if (filtered.length === 0) {
      container.appendChild(el('div', { className: 'rgcs-empty' },
        icon(IC.emptyUsers),
        el('p', null, term ? 'Keine Treffer' : 'Noch keine Kontakte erfasst')
      ));
      return;
    }

    const list = el('div', { className: 'rgcs-list' });
    filtered.forEach(c => {
      const avatarEl = c.photo_path
        ? el('img', { className: 'rgcs-avatar rgcs-avatar-photo', src: rgcsData.uploadUrl + '/' + c.photo_path })
        : el('div', { className: 'rgcs-avatar' }, (c.name || '?')[0].toUpperCase());
      const card = el('button', { className: 'rgcs-card', onClick: () => { selectedContact = c; renderApp('contacts'); } },
        avatarEl,
        el('div', { className: 'rgcs-card-info' },
          el('div', { className: 'name' }, c.name || 'Unbekannt'),
          el('div', { className: 'company' }, c.company || '—'),
          c.email ? el('div', { className: 'email' }, c.email) : null,
          c.zustaendig && c.zustaendig !== 'Nicht bekannt' ? el('div', { className: 'zustaendig-badge' }, '👤 ' + c.zustaendig) : null,
          c.bemerkung ? el('div', { className: 'remark' }, '💬 ' + (c.bemerkung.length > 40 ? c.bemerkung.slice(0, 40) + '…' : c.bemerkung)) : null
        ),
        icon(IC.right)
      );
      list.appendChild(card);
    });
    container.appendChild(list);
  }

  function exportCSV() {
    // Trigger server-side CSV download
    const url = AJAX + '?action=rgcs_csv&nonce=' + encodeURIComponent(NONCE) + '&token=' + encodeURIComponent(token);
    window.location.href = url;
  }

  /* ── Detail View ── */
  function renderDetail() {
    const c = selectedContact;
    if (!c) return;

    const section = el('div', { className: 'rgcs-detail' });

    section.appendChild(el('button', { className: 'rgcs-back', onClick: () => { selectedContact = null; renderApp('contacts'); } },
      icon(IC.back), 'Zurück'));

    section.appendChild(el('div', { className: 'rgcs-detail-header' },
      el('div', { className: 'rgcs-avatar-lg' }, (c.name || '?')[0].toUpperCase()),
      el('h2', null, c.name || 'Unbekannt'),
      c.position_title ? el('p', { className: 'pos' }, c.position_title) : null,
      c.company ? el('p', { className: 'comp' }, c.company) : null
    ));

    // Photo preview
    if (c.photo_path) {
      const photoUrl = rgcsData.uploadUrl + '/' + c.photo_path;
      const photoSection = el('div', { className: 'rgcs-photo-section' },
        el('label', null, 'Visitenkarten-Foto'),
        el('a', { href: photoUrl, target: '_blank' },
          el('img', { src: photoUrl, className: 'rgcs-photo-preview' })
        )
      );
      section.appendChild(photoSection);
    }

    // Fields
    const fields = el('div', { className: 'rgcs-fields' });
    const fieldMap = [
      ['name', 'Name'], ['company', 'Firma'], ['position_title', 'Position'],
      ['email', 'E-Mail'], ['phone', 'Telefon'], ['mobile', 'Mobil'],
      ['website', 'Website'], ['address', 'Adresse'], ['notes', 'Notizen'],
      ['zustaendig', 'Zuständig'], ['bemerkung', 'Bemerkung']
    ];
    fieldMap.forEach(([key, label]) => {
      const val = c[key];
      if (!val) return;
      const row = el('div', { className: 'rgcs-field-row' },
        el('span', { className: 'label' }, label)
      );
      if (key === 'email') row.appendChild(el('a', { href: 'mailto:' + val }, val));
      else if (key === 'phone' || key === 'mobile') row.appendChild(el('a', { href: 'tel:' + val }, val));
      else if (key === 'website') row.appendChild(el('a', { href: val.startsWith('http') ? val : 'https://' + val, target: '_blank' }, val));
      else if (key === 'zustaendig') row.appendChild(el('span', { className: 'value rgcs-zustaendig-tag' }, val));
      else row.appendChild(el('span', { className: 'value' }, val));
      fields.appendChild(row);
    });
    section.appendChild(fields);

    // Actions
    section.appendChild(el('div', { className: 'rgcs-detail-actions' },
      el('button', { className: 'rgcs-btn-edit', onClick: () => renderEditForm(c) }, 'Bearbeiten'),
      el('button', { className: 'rgcs-btn-delete', onClick: () => { if (confirm('Kontakt löschen?')) deleteContact(c.id); } }, 'Löschen')
    ));

    section.appendChild(el('div', { className: 'rgcs-timestamp' }, 'Erfasst: ' + c.scanned_at));

    root.appendChild(section);
  }

  async function deleteContact(id) {
    await post('rgcs_delete', { id });
    selectedContact = null;
    await loadContacts();
    renderApp('contacts', '✓ Kontakt gelöscht.');
  }

  /* ── Edit Form ── */
  function renderEditForm(c) {
    // Replace detail with edit form
    const detail = $('.rgcs-detail');
    if (!detail) return;

    const existing = $('.rgcs-fields'); if (existing) existing.remove();
    const existingActions = $('.rgcs-detail-actions'); if (existingActions) existingActions.remove();
    const existingTs = $('.rgcs-timestamp'); if (existingTs) existingTs.remove();

    const form = el('div', { className: 'rgcs-edit-form' });
    const fieldMap = [
      ['name', 'Name'], ['company', 'Firma'], ['position_title', 'Position'],
      ['email', 'E-Mail'], ['phone', 'Telefon'], ['mobile', 'Mobil'],
      ['website', 'Website'], ['address', 'Adresse'], ['notes', 'Notizen'],
      ['zustaendig', 'Zuständig'], ['bemerkung', 'Bemerkung']
    ];
    fieldMap.forEach(([key, label]) => {
      const field = el('div', null, el('label', null, label));
      if (key === 'bemerkung') {
        field.appendChild(el('textarea', { id: 'rgcs-edit-' + key, rows: '3',
          placeholder: 'Gesprächsnotiz, Lead-Qualität, Follow-up…',
          style: { minHeight: '80px', resize: 'vertical' }
        }));
        field.querySelector('textarea').value = c[key] || '';
      } else if (key === 'zustaendig') {
        const select = el('select', { id: 'rgcs-edit-' + key, className: 'rgcs-select' });
        TEAM.forEach(name => {
          const opt = el('option', { value: name }, name);
          if (name === (c[key] || 'Nicht bekannt')) opt.selected = true;
          select.appendChild(opt);
        });
        field.appendChild(select);
      } else {
        field.appendChild(el('input', { id: 'rgcs-edit-' + key, value: c[key] || '' }));
      }
      form.appendChild(field);
    });

    form.appendChild(el('div', { className: 'rgcs-edit-actions' },
      el('button', { className: 'rgcs-btn-save', onClick: () => saveEdit(c.id, fieldMap) }, 'Speichern'),
      el('button', { className: 'rgcs-btn-cancel', onClick: () => renderApp('contacts') }, 'Abbrechen')
    ));
    detail.appendChild(form);
  }

  async function saveEdit(id, fieldMap) {
    const data = { id };
    fieldMap.forEach(([key]) => {
      const inp = $('#rgcs-edit-' + key);
      // Map position_title back to position for the backend
      const postKey = key === 'position_title' ? 'position' : key;
      data[postKey] = inp ? inp.value : '';
    });
    await post('rgcs_update', data);
    await loadContacts();
    selectedContact = contacts.find(c => c.id == id) || null;
    renderApp('contacts', '✓ Kontakt aktualisiert.');
  }

  /* ════════════════════════════════════════════
     INIT
     ════════════════════════════════════════════ */
  async function init() {
    if (token) {
      // Verify token still valid
      const res = await post('rgcs_list');
      if (res.success) {
        contacts = res.data;
        renderApp('scanner');
        return;
      }
      // Token expired
      token = '';
      sessionStorage.removeItem('rgcs-token');
    }
    renderLogin();
  }

  // Load Google Font
  if (!$('link[href*="DM+Sans"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap';
    document.head.appendChild(link);
  }

  init();
})();
