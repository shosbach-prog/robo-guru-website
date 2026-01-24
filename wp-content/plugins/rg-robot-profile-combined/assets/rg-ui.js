/* RG Robot Profile + Forum Design Combined v3.0.0 */

(function(){
  const qs=(s,el=document)=>el.querySelector(s);
  const qsa=(s,el=document)=>Array.from(el.querySelectorAll(s));

  // Modal
  const modal = qs('#rg_modal');
  const modalContent = qs('#rg_modal_content');
  const closeBtn = qs('#rg_modal_close');
  function openModal(html, mode){
    if(!modal || !modalContent) return;
    modal.setAttribute('data-mode', mode || 'media');
    modalContent.innerHTML = html;
    modal.classList.add('open');
    document.body.style.overflow='hidden';
  }
  function closeModal(){
    if(!modal || !modalContent) return;
    modal.classList.remove('open');
    // Clear only for media so iframes stop and images are removed.
    // For forms, keep markup so dynamic widgets can keep state.
    if((modal.getAttribute('data-mode')||'media') !== 'form'){
      modalContent.innerHTML = '';
    }
    document.body.style.overflow='';
  }
  if(closeBtn) closeBtn.addEventListener('click', closeModal);
  if(modal) modal.addEventListener('click', (e)=>{ if(e.target===modal) closeModal(); });
  document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeModal(); });

  // SureForms: open shortcode output in modal
  // SureForms: open shortcode output in modal
  // primary CTA fallback: if button is inside .rg-cta-row and form tpl exists, open modal
  qsa('.rg-cta-row .rg-btn.primary, .rgCtarow .rg-btn.primary').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      const tpl = qs('#rg_form_tpl');
      if(!tpl) return; // keep normal link if no form
      ev.preventDefault();
      openModal(tpl.innerHTML, 'form');
    });
  });

  qsa('[data-rg-open-form]').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      ev.preventDefault();
      const tpl = qs('#rg_form_tpl');
      if(!tpl) return;
      openModal(tpl.innerHTML, 'form');
    });
  });

  // FAQ accordion
  qsa('.rg-faq-item').forEach(item=>{
    const q = qs('.rg-faq-q', item);
    if(q) q.addEventListener('click', ()=> item.classList.toggle('open'));
  });

  // Forum actions: move into headbox mount (if available)
  (function(){
    const mount = qs('#rg_forum_mount');
    if(!mount) return;
    // Try common containers
    const forum = document.querySelector('.rf-forum-actions') || document.querySelector('[class*="rf-forum-actions"]');
    if(!forum) return;
    mount.appendChild(forum);
    forum.classList.add('rg-forum-actions');
  })();

  // Enhance forum actions with meta (thread count / last activity) if possible
  (function(){
    const blocks = qsa('.rf-forum-actions, .rg-forum-actions');
    if(!blocks.length) return;
    blocks.forEach(b=>{
      const a = qs('a', b);
      if(!a) return;
      // Create meta container
      let meta = qs('.rg-forum-meta', b);
      if(!meta){
        meta = document.createElement('div');
        meta.className = 'rg-forum-meta';
        b.appendChild(meta);
      }
      // Show placeholder pills
      meta.innerHTML = '<span class="rg-forum-pill">💬 <strong>Diskussion läuft</strong></span>';
      // Fetch meta
      const form = new FormData();
      form.append('action','rg_forum_meta');
      form.append('url', a.href);
      fetch((window.rgAjax && window.rgAjax.ajaxurl) ? window.rgAjax.ajaxurl : '/wp-admin/admin-ajax.php', {method:'POST', credentials:'same-origin', body: form})
        .then(r=>r.json()).then(res=>{
          if(!res || !res.success || !res.data || !res.data.found) return;
          const d = res.data;
          const replies = (typeof d.replies==='number') ? d.replies : null;
          const last = d.last_time ? d.last_time : '';
          const lastTitle = d.last_title ? d.last_title : '';
          meta.innerHTML = '';
          if(replies !== null){
            const p = document.createElement('span');
            p.className='rg-forum-pill';
            p.innerHTML = '🧵 <strong>'+replies+'</strong> Antworten';
            meta.appendChild(p);
          }
          if(last){
            const p2 = document.createElement('span');
            p2.className='rg-forum-pill';
            p2.innerHTML = '🕒 Letzter Beitrag: <strong>'+last+'</strong>';
            meta.appendChild(p2);
          }
          if(lastTitle){
            const p3 = document.createElement('span');
            p3.className='rg-forum-pill';
            p3.textContent = '↪ ' + lastTitle;
            meta.appendChild(p3);
          }
        }).catch(()=>{});
    });
  })();

  // Gallery (single)
  const gal = qs('[data-rg-gallery]');
  if(gal){
    let imgs=[];
    try{ imgs = JSON.parse(gal.getAttribute('data-rg-gallery')||'[]'); }catch(e){ imgs=[]; }
    if(imgs.length){
      let i=0;
      const mainImg = qs('.rg-gallery-main img', gal);
      const thumbs = qsa('.rg-thumbs img', gal);
      const setIdx = (n)=>{
        i=(n+imgs.length)%imgs.length;
        if(mainImg) mainImg.src=imgs[i];
        thumbs.forEach((t,idx)=>t.classList.toggle('active', idx===i));
      };
      const prev=qs('.rg-gbtn.prev', gal);
      const next=qs('.rg-gbtn.next', gal);
      if(prev) prev.addEventListener('click', (ev)=>{ev.preventDefault(); ev.stopPropagation(); setIdx(i-1);});
      if(next) next.addEventListener('click', (ev)=>{ev.preventDefault(); ev.stopPropagation(); setIdx(i+1);});
      thumbs.forEach((t,idx)=>t.addEventListener('click', (ev)=>{ev.preventDefault(); setIdx(idx);} ));
      const mainWrap = qs('.rg-gallery-main', gal);
      if(mainWrap){ mainWrap.addEventListener('click', ()=> openModal('<img src="'+imgs[i]+'" alt="">', 'media')); }
      setIdx(0);
    }
  }

  // Video: DSGVO-konform inline (Poster → Consent → Load iframe)
  function toEmbed(url){
    try{
      const u = new URL(url);
      const host = u.hostname.replace('www.','');
      if(host==='youtube.com' || host==='m.youtube.com'){
        const id = u.searchParams.get('v');
        if(id) return 'https://www.youtube.com/embed/'+id+'?autoplay=1';
      }
      if(host==='youtu.be'){
        const id = u.pathname.replace('/','');
        if(id) return 'https://www.youtube.com/embed/'+id+'?autoplay=1';
      }
      if(host==='vimeo.com'){
        const id = u.pathname.split('/').filter(Boolean)[0];
        if(id) return 'https://player.vimeo.com/video/'+id+'?autoplay=1';
      }
    }catch(e){}
    return null;
  }
  const VID_CONSENT_KEY='rg_video_consent_v1';
  function hasVideoConsent(){
    try{ return localStorage.getItem(VID_CONSENT_KEY)==='1'; }catch(e){ return false; }
  }
  function setVideoConsent(){
    try{ localStorage.setItem(VID_CONSENT_KEY,'1'); }catch(e){}
  }
  function loadInlineVideo(el, embed){
    if(!el || el.classList.contains('is-loaded')) return;
    el.classList.add('is-loaded');
    const iframeWrap = document.createElement('div');
    iframeWrap.className = 'rg-video-iframe';
    iframeWrap.innerHTML = '<iframe src="'+embed+'" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    el.innerHTML = '';
    el.appendChild(iframeWrap);
  }
  qsa('[data-rg-video]').forEach(el=>{
    const url = el.getAttribute('data-rg-video');
    const embedRaw = toEmbed(url);
    if(!embedRaw) return;
    // Ensure autoplay only when user actively loads
    const embed = embedRaw.includes('autoplay=1') ? embedRaw : (embedRaw + (embedRaw.includes('?')?'&':'?') + 'autoplay=1');
    const consentOverlay = qs('.rg-video-consent', el);
    const acceptBtn = qs('[data-rg-video-accept]', el);
    const declineBtn = qs('[data-rg-video-decline]', el);
    function showConsent(){ if(consentOverlay){ consentOverlay.hidden=false; } }
    function hideConsent(){ if(consentOverlay){ consentOverlay.hidden=true; } }
    function handleOpen(){
      if(el.classList.contains('is-loaded')) return;
      if(hasVideoConsent()){
        loadInlineVideo(el, embed);
      } else {
        showConsent();
      }
    }
    el.addEventListener('click', (e)=>{
      // If click is on buttons, ignore (handled below)
      const t = e.target;
      if(t && (t.closest && t.closest('[data-rg-video-accept],[data-rg-video-decline]'))) return;
      handleOpen();
    });
    el.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); handleOpen(); } });
    if(acceptBtn) acceptBtn.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); setVideoConsent(); hideConsent(); loadInlineVideo(el, embed); });
    if(declineBtn) declineBtn.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); hideConsent(); });
  });

  // Beratung anfragen (SureForms shortcode in hidden template)
  // SureForms: open shortcode output in modal
  // primary CTA fallback: if button is inside .rg-cta-row and form tpl exists, open modal
  qsa('.rg-cta-row .rg-btn.primary, .rgCtarow .rg-btn.primary').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      const tpl = qs('#rg_form_tpl');
      if(!tpl) return; // keep normal link if no form
      ev.preventDefault();
      openModal(tpl.innerHTML, 'form');
    });
  });

  qsa('[data-rg-open-form]').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      ev.preventDefault();
      const tpl = qs('#rg_form_tpl');
      if(!tpl) return;
      // Use existing markup (do not clone) so embedded scripts remain connected.
      openModal(tpl.innerHTML, 'form');
    });
  });

  // Archive logic (filters + search + sort + compare)
  const grid = qs('[data-rg-grid]');
  if(!grid) return;

  const cards = qsa('[data-rg-card]', grid);
  const mfgBtns = qsa('[data-rg-filter-mfg]');
  const segBtns = qsa('[data-rg-filter-seg]');
  const searchInput = qs('#rg_search');
  const clearBtn = qs('#rg_clear');
  const countEl = qs('#rg_count');
  const sortSel = qs('#rg_sort');

  const compareBar = qs('#rg_comparebar');
  const compareChips = qs('#rg_comparechips');
  const compareBtn = qs('#rg_compare_btn');
  const compareClear = qs('#rg_compare_clear');

  const LS_KEY='rg_compare_ids_v1';
  const state={mfg:new Set(),seg:new Set(),q:'',sort:'title_asc',compare:new Set()};

  function loadCompare(){ try{ const raw=localStorage.getItem(LS_KEY); if(!raw) return; const arr=JSON.parse(raw); if(Array.isArray(arr)) arr.slice(0,3).forEach(id=>state.compare.add(String(id))); }catch(e){} }
  function saveCompare(){ try{ localStorage.setItem(LS_KEY, JSON.stringify([...state.compare].slice(0,3))); }catch(e){} }
  function setActive(btn,on){ btn.classList.toggle('is-active',!!on); btn.setAttribute('aria-pressed', on?'true':'false'); }

  function initFromUrl(){
    try{
      const url=new URL(window.location.href);
      (url.searchParams.get('mfg')||'').split(',').map(x=>x.trim()).filter(Boolean).forEach(x=>state.mfg.add(x));
      (url.searchParams.get('seg')||'').split(',').map(x=>x.trim()).filter(Boolean).forEach(x=>state.seg.add(x));
      state.q=(url.searchParams.get('q')||'').trim();
      state.sort=(url.searchParams.get('sort')||'').trim()||'title_asc';
      if(searchInput) searchInput.value=state.q;
      if(sortSel) sortSel.value=state.sort;
    }catch(e){}
  }
  function syncUrl(){
    try{
      const url=new URL(window.location.href);
      const m=[...state.mfg], s=[...state.seg];
      if(m.length) url.searchParams.set('mfg', m.join(',')); else url.searchParams.delete('mfg');
      if(s.length) url.searchParams.set('seg', s.join(',')); else url.searchParams.delete('seg');
      if((state.q||'').trim()) url.searchParams.set('q', state.q.trim()); else url.searchParams.delete('q');
      if(state.sort && state.sort!=='title_asc') url.searchParams.set('sort', state.sort); else url.searchParams.delete('sort');
      window.history.replaceState({},'',url.toString());
    }catch(e){}
  }
  function visibleCards(){ return cards.filter(c=>c.style.display!=='none'); }
  function sortCards(){
    const list=visibleCards();
    const get=(c,a)=>(c.getAttribute(a)||'').toLowerCase();
    list.sort((a,b)=>{
      switch(state.sort){
        case 'title_desc': return get(b,'data-title').localeCompare(get(a,'data-title'));
        case 'mfg_asc': return get(a,'data-mfg-label').localeCompare(get(b,'data-mfg-label')) || get(a,'data-title').localeCompare(get(b,'data-title'));
        case 'seg_asc': return get(a,'data-seg-label').localeCompare(get(b,'data-seg-label')) || get(a,'data-title').localeCompare(get(b,'data-title'));
        default: return get(a,'data-title').localeCompare(get(b,'data-title'));
      }
    });
    list.forEach(c=>grid.appendChild(c));
  }
  function renderCompareBar(){
    const ids=[...state.compare];
    if(!compareBar||!compareChips||!compareBtn) return;

    qsa('input[data-rg-compare]', grid).forEach(cb=>{
      const id=cb.getAttribute('data-rg-compare');
      cb.checked = state.compare.has(String(id));
    });

    compareChips.innerHTML='';
    if(ids.length===0){ compareBar.classList.remove('open'); return; }

    ids.forEach(id=>{
      const card=cards.find(c=>String(c.getAttribute('data-id'))===String(id));
      const label=card ? (card.getAttribute('data-title-label')||'Roboter') : 'Roboter';
      const chip=document.createElement('span'); chip.className='rg-cchip'; chip.innerHTML='<span>'+label+'</span>';
      const x=document.createElement('button'); x.type='button'; x.textContent='×';
      x.addEventListener('click', ()=>{ state.compare.delete(String(id)); saveCompare(); renderCompareBar(); });
      chip.appendChild(x);
      compareChips.appendChild(chip);
    });

    compareBtn.href = (window.location.pathname.replace(/\/+$/,'') + '/?compare=' + ids.join(','));
    compareBtn.classList.toggle('primary', ids.length>=2);
    compareBtn.textContent = ids.length>=2 ? ('Vergleichen ('+ids.length+')') : 'Wähle noch 1 Roboter';
    compareBtn.style.pointerEvents = ids.length>=2 ? '' : 'none';
    compareBtn.style.opacity = ids.length>=2 ? '1' : '.6';

    compareBar.classList.add('open');
  }
  function apply(){
    const q=(state.q||'').trim().toLowerCase();
    let shown=0;
    cards.forEach(card=>{
      const mfg=card.getAttribute('data-mfg')||'';
      const seg=card.getAttribute('data-seg')||'';
      const hay=(card.getAttribute('data-hay')||'').toLowerCase();
      const ok = (!state.mfg.size || state.mfg.has(mfg)) && (!state.seg.size || state.seg.has(seg)) && (!q || hay.includes(q));
      card.style.display = ok ? '' : 'none';
      if(ok) shown++;
    });
    if(countEl) countEl.textContent = shown+' Treffer';
    sortCards();
    syncUrl();
  }

  initFromUrl();
  loadCompare();

  mfgBtns.forEach(btn=>{
    const v=btn.getAttribute('data-rg-filter-mfg');
    setActive(btn, state.mfg.has(v));
    btn.addEventListener('click', ()=>{ state.mfg.has(v)?state.mfg.delete(v):state.mfg.add(v); setActive(btn,state.mfg.has(v)); apply(); });
  });
  segBtns.forEach(btn=>{
    const v=btn.getAttribute('data-rg-filter-seg');
    setActive(btn, state.seg.has(v));
    btn.addEventListener('click', ()=>{ state.seg.has(v)?state.seg.delete(v):state.seg.add(v); setActive(btn,state.seg.has(v)); apply(); });
  });

  if(searchInput){
    let t=null;
    searchInput.addEventListener('input', ()=>{ clearTimeout(t); t=setTimeout(()=>{ state.q=searchInput.value||''; apply(); },120); });
  }
  if(sortSel){
    sortSel.addEventListener('change', ()=>{ state.sort=sortSel.value||'title_asc'; apply(); });
  }
  if(clearBtn){
    clearBtn.addEventListener('click', ()=>{
      state.mfg.clear(); state.seg.clear(); state.q=''; state.sort='title_asc';
      if(searchInput) searchInput.value='';
      if(sortSel) sortSel.value='title_asc';
      mfgBtns.forEach(b=>setActive(b,false));
      segBtns.forEach(b=>setActive(b,false));
      apply();
    });
  }

  qsa('input[data-rg-compare]', grid).forEach(cb=>{
    const id=cb.getAttribute('data-rg-compare');
    cb.checked = state.compare.has(String(id));
    cb.addEventListener('change', ()=>{
      const sid=String(id);
      if(cb.checked){
        if(state.compare.size>=3){ cb.checked=false; alert('Maximal 3 Roboter vergleichen.'); return; }
        state.compare.add(sid);
      }else{
        state.compare.delete(sid);
      }
      saveCompare(); renderCompareBar();
    });
  });

  if(compareClear){
    compareClear.addEventListener('click', ()=>{ state.compare.clear(); saveCompare(); renderCompareBar(); });
  }

  apply();
  renderCompareBar();
})();

// Beratung Modal open/close (scoped)
(function(){
  const qs=(s,el=document)=>el.querySelector(s);
  const qsa=(s,el=document)=>Array.from(el.querySelectorAll(s));

  // Any CTA that should open the SureForms modal (if template exists)
  qsa('.rg-open-modal, .rg-cta-row .rg-btn.primary, .rgCtarow .rg-btn.primary').forEach(btn=>{
    btn.addEventListener('click', (ev)=>{
      const modal = qs('#rg_modal');
      const modalContent = qs('#rg_modal_content');
      const tpl = qs('#rg_form_tpl');
      if(!modal || !modalContent || !tpl) return; // keep default behavior if no modal/template
      ev.preventDefault();
      modal.setAttribute('data-mode','form');
      modalContent.innerHTML = tpl.innerHTML;
      modal.classList.add('open');
      modal.setAttribute('aria-hidden','false');
      document.body.style.overflow='hidden';
    });
  });

  const closeBtn = qs('#rg_modal_close');
  function close(){
    const modal = qs('#rg_modal');
    const modalContent = qs('#rg_modal_content');
    if(!modal || !modalContent) return;
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden','true');
    document.body.style.overflow='';
  }
  if(closeBtn) closeBtn.addEventListener('click', close);

  const modalBg = qs('#rg_modal');
  if(modalBg){
    modalBg.addEventListener('click', (e)=>{
      if(e.target === modalBg) close();
    });
  }
})();
