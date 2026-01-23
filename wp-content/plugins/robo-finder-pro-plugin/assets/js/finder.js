/* Robo Finder Pro – Frontend Wizard Logic
 * Fixes:
 * - single, valid JS bundle (no truncated IIFEs)
 * - step navigation works (1..6)
 * - multiselect tiles + barrier cards clickable
 * - summary renders on step 5 + step 6
 * - SureForms hidden fields are synced right before submit
 * - restore last step/state after reload (sessionStorage)
 */

(function(factory){
  if (typeof window !== 'undefined' && window.jQuery) {
    factory(window.jQuery);
  } else {
    var tries = 0;
    var t = setInterval(function(){
      tries++;
      if (typeof window !== 'undefined' && window.jQuery) {
        clearInterval(t);
        factory(window.jQuery);
      } else if (tries > 200) {
        clearInterval(t);
      }
    }, 50);
  }
})(function($){
  'use strict';

  var STORAGE_KEY = 'rfp_state_v1';

  // Escape helper (missing previously -> broke renderMiniSummary, which stopped progress/topbar/summary updates)
  function esc(s){
    return String(s == null ? '' : s)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function getWrap($ctx){
    var $w = $ctx && $ctx.length ? $ctx.closest('.rf-wrap') : $('.rf-wrap').first();
    if(!$w.length) $w = $('.rf-wrap').first();
    return $w;
  }

  function getTotalSteps($wrap){
    var n = parseInt($wrap.attr('data-rf-steps') || '6', 10);
    return (isFinite(n) && n >= 1) ? n : 6;
  }

  // Determine whether the current selection requires a manual feasibility check.
  // Lightweight (no computeState dependency), so it can be used early in showStep().
  function manualRequiredNow($wrap){
    try{
      $wrap = getWrap($wrap);
      var rawB = ($wrap.find('input[name="rf_barrierefreiheit"]').val() || '').trim();
      if(!rawB) return false;
      var b = rawB.split(',').map(function(s){return (s||'').trim();}).filter(Boolean);

      var hasStairs = b.indexOf('stufen_treppen') !== -1;
      var hasLift   = (b.indexOf('aufzug') !== -1) || (b.indexOf('fahrstuhl') !== -1);
      var hasTight  = b.indexOf('enge_tueren') !== -1;
      var hasRamps  = b.indexOf('rampen_schwellen') !== -1;
      var hasGate   = (b.indexOf('tore') !== -1) || (b.indexOf('rolltor') !== -1) || (b.indexOf('tor') !== -1);

      return (hasStairs && !hasLift) || hasTight || hasRamps || (hasGate && hasLift);
    }catch(e){
      return false;
    }
  }

  // Display meta for progress/step indicator.
  // IMPORTANT: Step 6 in Robo Finder is the SureForms form (not a thank-you page),
  // therefore we must keep showing 6/6 and allow navigation to step 6 even when
  // a manual feasibility check is required.
  function getDisplayStepMeta($wrap, step){
    $wrap = getWrap($wrap);
    var total = getTotalSteps($wrap);
    return { step: step, total: total, isManual: manualRequiredNow($wrap) };
  }

  // Backwards-compat: older builds called this from showStep().
  // We no longer change the internal total step count (to avoid breaking the
  // post-submit confirmation step), so this is intentionally a no-op.
  function applyEffectiveTotalSteps($wrap){
    return;
  }

  function clampStep($wrap, step){
    var total = getTotalSteps($wrap);
    step = parseInt(step || 1, 10);
    if(!isFinite(step)) step = 1;
    if(step < 1) step = 1;
    if(step > total) step = total;
    return step;
  }

  function setProgress($wrap, step){
    var meta = getDisplayStepMeta($wrap, step);
    var total = meta.total;
    step = meta.step;
    var pct = (total <= 1) ? 100 : ((step-1) / (total-1)) * 100;
    pct = Math.max(0, Math.min(100, pct));
    // Support multiple possible classnames (theme/plugin variations)
    $wrap.find('.rf-progress-bar, .rf-progress__bar, .rf-progress-inner').css('width', pct + '%');
  }

  // Small helper bar (step indicator + microcopy) injected once per finder instance.
  function ensureTopbar($wrap){
    if($wrap.find('.rf-topbar').length) return;
    var $card = $wrap.find('.rf-card').first();
    if(!$card.length) return;
    var $progress = $card.find('.rf-progress').first();
    var html = ''+
      '<div class="rf-topbar">'+
        '<div class="rf-topbar-left">'+
          '<span class="rf-step-ind">Step <span data-rf-step-n>1</span>/<span data-rf-step-total>6</span></span>'+
        '</div>'+
        '<div class="rf-topbar-right" data-rf-step-copy></div>'+
      '</div>';
    if($progress.length){
      $progress.after(html);
    } else {
      $card.prepend(html);
    }
  }

  function updateTopbar($wrap, step){
    ensureTopbar($wrap);
    var meta = getDisplayStepMeta($wrap, step);
    $wrap.find('[data-rf-step-n]').text(String(meta.step));
    $wrap.find('[data-rf-step-total]').text(String(meta.total));

    var copyMap = {
      1: 'Wähle den Robotertyp – damit wir direkt richtig filtern.',
      2: 'Wähle 1–3 Umfelder – das bestimmt die Anforderungen.',
      3: 'Hürden entscheiden über Sensorik & Rampenfähigkeit.',
      4: 'm² grob reicht – wir arbeiten mit Stufen.',
      5: 'Fast geschafft – optional noch Hinweise, dann Kurz-Check.',
      6: 'Kontaktdaten – wir melden uns mit einer passenden Empfehlung.'
    };
    // If we're on the post-submit confirmation page but in manual-check mode,
    // show a short confirmation copy.
    if(meta.isManual && step >= 6){
      $wrap.find('[data-rf-step-copy]').text('Danke! Wir prüfen die Machbarkeit und melden uns zeitnah.');
    } else {
      $wrap.find('[data-rf-step-copy]').text(copyMap[step] || '');
    }
  }

  function syncTileVisual($tile){
    var $inp = $tile.find('input').first();
    if(!$inp.length) return;
    var checked = $inp.prop('checked');
    // Finder CSS uses .is-on (legacy) – keep both for compatibility.
    $tile.toggleClass('is-on', !!checked);
    $tile.toggleClass('is-active', !!checked);
  }

  function textFromTileInput($inp){
    var v = ($inp.val() || '').trim();
    if(!v || v === 'on'){
      v = ($inp.closest('.rf-tile').find('.rf-name').text() || $inp.closest('.rf-tile').text() || '').replace(/\s+/g,' ').trim();
    }
    return v;
  }

  function computeState($wrap){
    var $form = $wrap.find('.rf-form').first();
    var state = {
      step: clampStep($wrap, $wrap.data('rf-step') || $form.data('rf-step') || 1),
      aufgabe: '',
      einsatzgebiet: [],
      barrierefreiheit: [],
      flaeche_qm: '',
      notes: ($wrap.find('input[name="rf_notes"]').val()||'').trim(),
      critical_notes: ($wrap.find('input[name="rf_critical_notes"]').val()||'').trim(),
      manual_check_required: ($wrap.find('input[name="rf_manual_check_required"]').val()||'0') === '1'
    };

    // If Step 5 textareas exist, prefer them (live typing) over hidden values
    try{
      var $notes = $wrap.find('[data-rf-notes]').first();
      if($notes.length) state.notes = String($notes.val() || '').trim();
      if(!$notes.length){
        var $notes2 = $wrap.find('textarea[name="rf_notes"], input[name="rf_notes"]').not('input[type="hidden"]').first();
        if($notes2.length) state.notes = String($notes2.val() || '').trim();
      }

      var $cnotes = $wrap.find('[data-rf-critical-notes]').first();
      if($cnotes.length) state.critical_notes = String($cnotes.val() || '').trim();
      if(!$cnotes.length){
        var $cnotes2 = $wrap.find('textarea[name="rf_critical_notes"], input[name="rf_critical_notes"]').not('input[type="hidden"]').first();
        if($cnotes2.length) state.critical_notes = String($cnotes2.val() || '').trim();
      }
    }catch(e){}// step 1: radio rf_task
    var $s1 = $wrap.find('.rf-step[data-step="1"]');
    var $task = $s1.find('input[type="radio"]:checked').first();
    if($task.length) state.aufgabe = textFromTileInput($task);

    // step 2: checkbox rf_env[]
    var $s2 = $wrap.find('.rf-step[data-step="2"]');
    $s2.find('input[type="checkbox"]:checked').each(function(){
      var t = textFromTileInput($(this));
      if(t) state.einsatzgebiet.push(t);
    });
    // de-dup
    state.einsatzgebiet = Array.from(new Set(state.einsatzgebiet));

    // step 3: barrier cards
    var rawB = ($wrap.find('input[name="rf_barrierefreiheit"]').val() || '').trim();
    if(rawB) state.barrierefreiheit = rawB.split(',').map(function(s){return (s||'').trim();}).filter(Boolean);

    // step 4: area bucket stored as human readable text
    state.flaeche_qm = ($wrap.find('input[name="rf_flaeche_qm"]').val() || '').trim();

    // derive manual_check_required (special cases) from selected barriers etc.
    try{
      var b = state.barrierefreiheit || [];
      var hasStairs = b.indexOf('stufen_treppen') !== -1;
      var hasLift   = (b.indexOf('aufzug') !== -1) || (b.indexOf('fahrstuhl') !== -1);
      var hasTight  = b.indexOf('enge_tueren') !== -1;
      var hasRamps  = b.indexOf('rampen_schwellen') !== -1;
      var hasGate   = (b.indexOf('tore') !== -1) || (b.indexOf('rolltor') !== -1) || (b.indexOf('tor') !== -1);

      // Special check required if stairs without lift, very tight doors, or ramps/schwellen
      state.manual_check_required = (hasStairs && !hasLift) || hasTight || hasRamps || (hasGate && hasLift);
    }catch(e){}
    // sync hidden flag for SureForms
    $wrap.find('input[name="rf_manual_check_required"]').val(state.manual_check_required ? '1' : '0');

    return state;
  }

  function persistState($wrap){
    try{
      var st = computeState($wrap);
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(st));
    }catch(e){}
  }

  function restoreState($wrap){
    try{
      var raw = sessionStorage.getItem(STORAGE_KEY);
      if(!raw) return null;
      var st = JSON.parse(raw);
      if(!st || typeof st !== 'object') return null;

      // restore step1
      if(st.aufgabe){
        $wrap.find('.rf-step[data-step="1"] input[type="radio"]').each(function(){
          var t = textFromTileInput($(this));
          if(t === st.aufgabe){ $(this).prop('checked', true); }
        });
      }

      // restore step2
      if(Array.isArray(st.einsatzgebiet) && st.einsatzgebiet.length){
        $wrap.find('.rf-step[data-step="2"] input[type="checkbox"]').each(function(){
          var t = textFromTileInput($(this));
          if(st.einsatzgebiet.indexOf(t) !== -1){ $(this).prop('checked', true); }
        });
      }

      // restore barriers
      if(Array.isArray(st.barrierefreiheit)){
        $wrap.find('input[name="rf_barrierefreiheit"]').val(st.barrierefreiheit.join(','));
        $wrap.find('.rf-step[data-step="3"] .rf-terra-card').each(function(){
          var k = ($(this).data('key') || '').toString();
          $(this).toggleClass('is-active', st.barrierefreiheit.indexOf(k) !== -1);
          $(this).attr('aria-pressed', st.barrierefreiheit.indexOf(k) !== -1 ? 'true' : 'false');
        });
      }

      // restore area
      if(st.flaeche_qm){
        $wrap.find('input[name="rf_flaeche_qm"]').val(st.flaeche_qm);
        $wrap.find('[data-rf-area-val]').text(st.flaeche_qm);
      }


      // restore notes (Step 5)
      if(typeof st.notes === 'string'){
        $wrap.find('input[name="rf_notes"]').val(st.notes);
        $wrap.find('[data-rf-notes]').val(st.notes);
      }
      if(typeof st.critical_notes === 'string'){
        $wrap.find('input[name="rf_critical_notes"]').val(st.critical_notes);
        $wrap.find('[data-rf-critical-notes]').val(st.critical_notes);
      }
      if(typeof st.manual_check_required !== 'undefined'){
        $wrap.find('input[name="rf_manual_check_required"]').val(st.manual_check_required ? '1' : '0');
      }

      // refresh tile visuals
      $wrap.find('.rf-tile').each(function(){ syncTileVisual($(this)); });

      return st;
    }catch(e){
      return null;
    }
  }

  function setHidden($scope, name, val){
  var $el = $scope.find('input[name="'+name+'"]');
  if(!$el.length){
    $el = $('<input type="hidden" />').attr('name', name).appendTo($scope);
  }
  $el.val(val);
}

function syncToSureForms($wrap, st){
  try{
    // SureForms renders its own <form> inside step 6 container
    var form = $wrap.find('.rf-terra-final-form form').first();
    if(!form.length) return;

    // Set any field inside SureForms (hidden or visible) without overwriting user input
    function setField(name, val){
      if(val === undefined || val === null) val = '';
      val = String(val);
      var $els = form.find('[name="'+name+'"]');
      if(!$els.length) return;
      $els.each(function(){
        var $el = $(this);
        // don't overwrite if user already typed something
        if(($el.val() || '').toString().trim().length) return;
        $el.val(val);
        try{ $el.trigger('input'); $el.trigger('change'); }catch(e){}
      });
    }

    // Ensure required hidden fields exist (for setups that only read hidden inputs)
    function ensureHidden(name, val){
      if(val === undefined || val === null) val = '';
      val = String(val);
      var $h = form.find('input[type="hidden"][name="'+name+'"]');
      if(!$h.length){
        $h = $('<input type="hidden" />').attr('name', name).appendTo(form);
      }
      $h.val(val);
    }

    var env = (st.einsatzgebiet || []).join(', ');
    var barr = (st.barrierefreiheit || []).join(', ');

    // Main mapping (matches your SureForms field keys)
    setField('rf_aufgabe', st.aufgabe || '');
    setField('rf_task', st.aufgabe || '');

    setField('rf_einsatzgebiet', env);
    setField('rf_environment', env);
    setField('rf_environments', env);

    setField('rf_barrierefreiheit', barr);
    setField('rf_flaeche_qm', st.flaeche_qm || '');

    // Notes fields
    setField('rf_notes', st.notes || '');
    setField('rf_critical_notes', st.critical_notes || '');

    // Special: some SureForms builds label the critical field as
    // "Wichtiger Hinweis zu deinem Objekt" without a stable name.
    // Try to locate the textarea by label text and fill it with critical_notes.
    try{
      if(st.critical_notes){
        var $labels = form.find('label');
        $labels.each(function(){
          var txt = ($(this).text() || '').replace(/\s+/g,' ').trim();
          if(!txt) return;
          if(txt.indexOf('Wichtiger Hinweis') !== -1 || txt.indexOf('besondere Anforderungen') !== -1){
            var fid = $(this).attr('for');
            if(fid){
              var $t = form.find('#'+fid);
              if($t.length && !String($t.val()||'').trim().length){
                $t.val(String(st.critical_notes));
                try{ $t.trigger('input'); $t.trigger('change'); }catch(e){}
              }
            }
          }
        });
      }
    }catch(e){}

    // Meta
    setField('rf_manual_check_required', st.manual_check_required ? '1' : '0');
    setField('rf_page_url', window.location.href);
    setField('rf_session_id', st.session_id || '');
    setField('rf_timestamp', (new Date()).toISOString());

    // Hidden mirrors for maximum compatibility
    ensureHidden('rf_notes', st.notes || '');
    ensureHidden('rf_critical_notes', st.critical_notes || '');
    ensureHidden('rf_manual_check_required', st.manual_check_required ? '1' : '0');
    ensureHidden('rf_task', st.aufgabe || '');
    ensureHidden('rf_environment', env);
    ensureHidden('rf_environments', env);
    ensureHidden('rf_barrierefreiheit', barr);
    ensureHidden('rf_aufgabe', st.aufgabe || '');
    ensureHidden('rf_einsatzgebiet', env);
  }catch(e){}
}

function syncHiddenFields($wrap){
  var st = computeState($wrap);
  // internal hidden fields (RF wrapper scope)
  setHidden($wrap, 'rf_aufgabe', st.aufgabe || '');
  setHidden($wrap, 'rf_einsatzgebiet', (st.einsatzgebiet || []).join(','));
  // NEW: keep barrier selection in sync (prevents lingering value="" in hidden field)
  setHidden($wrap, 'rf_barrierefreiheit', (st.barrierefreiheit || []).join(','));
  setHidden($wrap, 'rf_flaeche_qm', st.flaeche_qm || '');
  setHidden($wrap, 'rf_notes', st.notes || '');
  setHidden($wrap, 'rf_critical_notes', st.critical_notes || '');
  setHidden($wrap, 'rf_manual_check_required', st.manual_check_required ? '1' : '0');
  // aliases expected by SureForms setups
  setHidden($wrap, 'rf_task', st.aufgabe || '');
  setHidden($wrap, 'rf_environment', (st.einsatzgebiet || []).join(','));

  // push values into SureForms form (step 6)
  syncToSureForms($wrap, st);
}


  function isStepValid($wrap, step){
    step = parseInt(step || 1, 10);
    if(step === 1){
      return $wrap.find('.rf-step[data-step="1"] input[type="radio"]:checked').length > 0;
    }
    if(step === 2){
      return $wrap.find('.rf-step[data-step="2"] input[type="checkbox"]:checked').length > 0;
    }
    if(step === 3){
      return (($wrap.find('input[name="rf_barrierefreiheit"]').val() || '').trim().length > 0);
    }
    if(step === 4){
      return (($wrap.find('input[name="rf_flaeche_qm"]').val() || '').trim().length > 0);
    }
    if(step === 5){
      var st = computeState($wrap);
      if(st.manual_check_required){
        return (String(st.critical_notes || '').trim().length >= 15);
      }
      return true;
    }
    return true;
  }

  function updateNextButtonState($wrap, step){
    step = parseInt(step || 1, 10);
    var ok = isStepValid($wrap, step);
    var $btn = $wrap.find('.rf-step[data-step="'+step+'"] .rf-next').first();
    $btn.toggleClass('is-disabled', !ok)
        .attr('aria-disabled', ok ? 'false' : 'true')
        .prop('disabled', !ok);
  }

  // =====================
  // Step 5 special-case UI
  // =====================
  // If selected barriers require a manual feasibility check, Step 5 asks for a
  // short mandatory hint. The template contains this field but keeps it hidden
  // by default (data-rf-critical-wrap). If we don't unhide it, users can end up
  // with a disabled CTA and no visible way to satisfy the requirement.
  function updateStep5SpecialUI($wrap){
    try{
      $wrap = getWrap($wrap);
      var step = parseInt($wrap.data('rf-step') || 1, 10);
      if(step !== 5) return;

      var st = computeState($wrap);
      var $critWrap = $wrap.find('[data-rf-critical-wrap]').first();
      if(!$critWrap.length) return;

      // Show/hide the mandatory field based on current selection.
      $critWrap.prop('hidden', !st.manual_check_required);

      // Toggle validation hint + CTA state
      var ok5 = isStepValid($wrap, 5);
      $wrap.find('[data-rf-critical-hint]').prop('hidden', ok5);
      updateNextButtonState($wrap, 5);
    }catch(e){}
  }

  // =====================
  // Step switching
  // =====================
  // Several flows call `showStep(...)` (restore, next/back, direct step nav).
  // In v4.8.18.7 this function was accidentally removed during a merge, which caused
  // a hard runtime error and made the UI non-interactive.
  function showStep($wrap, step){
    $wrap = getWrap($wrap);
    // If the current selection triggers a manual feasibility check, Step 5 becomes
    // the final step. Adjust the total step count BEFORE clamping.
    applyEffectiveTotalSteps($wrap);
    step = clampStep($wrap, step);

    // Switch visible step
    $wrap.find('.rf-step').hide().removeClass('is-active');
    var $target = $wrap.find('.rf-step[data-step="'+step+'"]');
    if(!$target.length){
      step = 1;
      $target = $wrap.find('.rf-step[data-step="1"]');
    }
    $target.show().addClass('is-active');
    $wrap.data('rf-step', step);

    // Keep UI in sync
    setProgress($wrap, step);
    updateTopbar($wrap, step);
    updateLayoutForStep($wrap, step);
    updateCtaCopy($wrap);
    syncHiddenFields($wrap);
    updateNextButtonState($wrap, step);
    // Ensure Step 5 mandatory hint field becomes visible when required
    updateStep5SpecialUI($wrap);

    // Render summary when user reaches the recap/final steps
    if(step === 5 || step === 6){
      renderMiniSummary($wrap);
    }

    if(step === 6){
      // Prefill/append hidden fields to SureForms as early as possible (not only on submit)
      try{
        var $sf = $wrap.find('.rf-step[data-step="6"] form').first();
        if($sf.length){
          syncSureFormsBeforeSubmit($wrap, $sf.get(0));
          // Ensure raw hidden fields exist (for setups without SureForms hidden mapping)
          var st = computeState($wrap);
          var payload = {
            rf_aufgabe: st.aufgabe || '',
            rf_task: st.aufgabe || '',
            rf_einsatzgebiet: (st.einsatzgebiet||[]).join(','),
            rf_environment: (st.einsatzgebiet||[]).join(','),
            rf_barrierefreiheit: ($wrap.find('input[name="rf_barrierefreiheit"]').val() || ''),
            rf_flaeche_qm: st.flaeche_qm || '',
            rf_notes: st.notes || '',
            rf_critical_notes: st.critical_notes || '',
            rf_manual_check_required: st.manual_check_required ? '1' : '0',
            rf_page_url: window.location.href,
            rf_session_id: (sessionStorage.getItem('rf_session_id') || ''),
            rf_timestamp: (new Date()).toISOString()
          };
          // Prefill visible/hidden SureForms fields using robust mapping
          rfFillSureFormsMapped($sf, payload);

          Object.keys(payload).forEach(function(k){
            if($sf.find('[name="'+k+'"]').length) return;
            $sf.append('<input type="hidden" name="'+k+'" value="'+esc(String(payload[k]||''))+'">');
          });
        }
      }catch(e){}
    }

    // Persist so refresh keeps the state (unless cleared on entry page)
    persistState($wrap);

    // Small UX improvement: on mobile, keep the card header in view
    try{
      var top = ($wrap.offset() ? $wrap.offset().top : 0) - 18;
      if(top > 0) $('html,body').stop(true).animate({scrollTop: top}, 180);
    }catch(e){}
  }

  function renderSummary($wrap){
    var st = computeState($wrap);

    // human readable barrier labels (keys -> short text)
    var bMap = {
      stufen_treppen: 'Stufen / Treppen',
      aufzug: 'Fahrstuhl / Aufzug',
      rampen_schwellen: 'Rampen / Schwellen',
      enge_tueren: 'Enge Türen',
      tore: 'Tore / Rolltore',
      alles_ebenerdig: 'Alles ebenerdig'
    };
    var barriers = (st.barrierefreiheit || []).map(function(k){ return bMap[k] || k; }).join(', ');

    function row(label, val, goStep){
      if(!val) return '';
      var edit = (goStep ? '<button type="button" class="rf-sum-edit" data-rf-goto="'+goStep+'">Ändern</button>' : '');
      return '<div class="rf-sum-row">'+
        '<div class="rf-sum-k">'+label+'</div>'+
        '<div class="rf-sum-v">'+val+edit+'</div>'+
      '</div>';
    }

    var html = '<div class="rf-sum-card"><div class="rf-sum-title">Deine Angaben</div>' +
      row('Aufgabe / Typ', st.aufgabe, 1) +
      row('Einsatzumfeld', (st.einsatzgebiet||[]).join(', '), 2) +
      row('Barrierefreiheit', barriers, 3) +
      row('Fläche', st.flaeche_qm, 4) +
      row('Wünsche / Hinweise', st.notes, 5) +
      row('Wichtiger Hinweis', st.critical_notes, 5) +
      '</div>';

    $wrap.find('[data-rf-summary]').html(html);
  }

  
  function renderMiniSummary($wrap){
    var st = computeState($wrap);
    var bMap = {
      stufen_treppen: 'Stufen/Treppen',
      aufzug: 'Fahrstuhl/Aufzug',
      rampen_schwellen: 'Rampen/Schwellen',
      enge_tueren: 'Enge Türen',
      tore: 'Tore',
      alles_ebenerdig: 'Alles ebenerdig'
    };
    var barriers = (st.barrierefreiheit || []).map(function(k){ return bMap[k] || k; }).join(', ');

    function item(label, val, goStep){
      if(!val) return '';
      var edit = goStep ? '<button type="button" class="rf-mini-edit" data-rf-goto="'+goStep+'">Ändern</button>' : '';
      return '<div class="rf-mini-row">' +
               '<div class="rf-mini-k">' + esc(label) + '</div>' +
               '<div class="rf-mini-v">' + esc(String(val)) + '</div>' +
               edit +
             '</div>';
    }

    var html = '' +
      item('Typ', st.aufgabe, 1) +
      item('Umfeld', (st.einsatzgebiet||[]).join(', '), 2) +
      item('Barrieren', barriers, 3) +
      item('Fläche', st.flaeche_qm, 4);

    if(!html){
      html = '<div class="rf-mini-empty">Noch keine Angaben.</div>';
    }
    $wrap.find('[data-rf-mini-summary]').html(html);
  }

  function updateLayoutForStep($wrap, step){
    var $layout = $wrap.find('[data-rf-layout]').first();
    var $sidebar = $wrap.find('[data-rf-sidebar]').first();
    if(!$layout.length) return;

    if(step >= 2){
      $layout.removeClass('rf-layout--single');
      $sidebar.removeClass('is-hidden').attr('aria-hidden','false');
    } else {
      $layout.addClass('rf-layout--single');
      $sidebar.addClass('is-hidden').attr('aria-hidden','true');
    }
  }

  
  function barrierLabels(keys){
    var map = {
      stufen_treppen:'Stufen / Treppen',
      aufzug:'Fahrstuhl / Aufzug',
      rampen_schwellen:'Rampen / Schwellen',
      enge_tueren:'Enge Türen',
      tore:'Tore / Rolltore',
      alles_ebenerdig:'Alles ebenerdig'
    };
    return (keys||[]).map(function(k){ return map[k] || k; });
  }

  function updateSpecialHint($wrap){
    var st = computeState($wrap);
    var $hint = $wrap.find('[data-rf-special-hint]').first();
    if(!$hint.length) return;

    if(!st.manual_check_required){
      $hint.prop('hidden', true).empty();
      return;
    }

    var labels = barrierLabels(st.barrierefreiheit || []);
    var why = labels.length ? ('Aufgrund von <strong>' + esc(labels.join(', ')) + '</strong> prüfen wir die Machbarkeit individuell.') : 'Wir prüfen die Machbarkeit individuell.';
    var html =
      '<div class="rf-special-hint__card">' +
        '<div class="rf-special-hint__icon">🔍</div>' +
        '<div class="rf-special-hint__body">' +
          '<div class="rf-special-hint__title">Machbarkeit statt Fehlkauf</div>' +
          '<div class="rf-special-hint__text">' + why + ' <span>Im nächsten Schritt öffnet sich das Formular zur Machbarkeitsprüfung.</span></div>' +
          '<div class="rf-special-hint__sub"><strong>Warum wir prüfen:</strong> Bei Stufen, Toren oder Rampen unterscheiden sich Roboter stark – eine kurze Prüfung verhindert teure Fehlkäufe.</div>' +
        '</div>' +
      '</div>';

    $hint.html(html).prop('hidden', false);
  }

// Adjust CTA copy depending on selected task (Reinigung vs Service/Lieferung)
  function updateCtaCopy($wrap){
    var st = computeState($wrap);
    updateSpecialHint($wrap);
    var t = (st.aufgabe || '').toLowerCase();
    var isServiceOrDelivery = /transport|liefer|service|abräum|servicerobot/.test(t);
    var isSpecial = !!st.manual_check_required;

    // CTA copy (Step 5 button + Step 6 label)
    var cta = isSpecial ? 'Machbarkeit prüfen lassen' : (isServiceOrDelivery ? 'Passende Lösung anfragen' : 'Empfehlung erhalten');
    $wrap.find('.rf-to-form').text(cta);

    // Step 6 headline/subline + teaser
    var $s6 = $wrap.find('.rf-step[data-step="6"]');
    if($s6.length){
      ($s6.find('[data-rf-s6-label]').first().length ? $s6.find('[data-rf-s6-label]').first() : $s6.find('.rf-label').first()).text(cta);

      var sub = isSpecial
        ? 'Trag deine Kontaktdaten ein – wir prüfen die Machbarkeit und melden uns mit einer passenden Empfehlung.'
        : (isServiceOrDelivery
            ? 'Trag deine Kontaktdaten ein – wir melden uns mit einer passenden Lösung.'
            : 'Trag deine Kontaktdaten ein – wir melden uns mit einer passenden Empfehlung.');
      ($s6.find('[data-rf-s6-sub]').first().length ? $s6.find('[data-rf-s6-sub]').first() : $s6.find('.rf-sub').first()).text(sub);

      // Final form title (inside the form box)
      var $ft = $s6.find('[data-rf-final-title]').first();
      if($ft.length){
        $ft.text(isSpecial ? 'Machbarkeitsprüfung anfordern' : 'Kontakt & Angebot');
      }

      // Next steps box – show extra note only for special cases
      var $fehl = $s6.find('[data-rf-fehlkauf]').first();
      if($fehl.length){
        $fehl.prop('hidden', !isSpecial);
      }

      // Result teaser (conversion booster) – no specific model names
      var $teaser = $s6.find('[data-rf-teaser]').first();
      if($teaser.length){
        var envTxt = st.einsatzgebiet && st.einsatzgebiet.length ? st.einsatzgebiet.join(', ') : 'deinem Umfeld';
        var specialPart = isSpecial ? ' (Sonderfall – wir prüfen Übergänge & Navigation)' : '';
        var teaserHtml =
          '<div class="rf-teaser-card">' +
            '<div class="rf-teaser-title">Zwischenergebnis</div>' +
            '<div class="rf-teaser-text">Basierend auf deinen Angaben prüfen wir Lösungen für <strong>' + esc(envTxt) + '</strong>' + specialPart + '.</div>' +
            '<div class="rf-teaser-sub">Im nächsten Schritt erhältst du eine konkrete Empfehlung – keine Werbung.</div>' +
          '</div>';
        $teaser.html(teaserHtml).prop('hidden', false);
      }
    }
  }

  // Barrier cards selection
  function toggleBarrier($wrap, $card){
    var key = ($card.data('key') || '').toString();
    if(!key) return;

    // "alles_ebenerdig" is exclusive
    if(key === 'alles_ebenerdig'){
      $wrap.find('.rf-step[data-step="3"] .rf-terra-card').removeClass('is-active').attr('aria-pressed','false');
      $card.addClass('is-active').attr('aria-pressed','true');
    } else {
      $wrap.find('.rf-step[data-step="3"] .rf-terra-card[data-key="alles_ebenerdig"]').removeClass('is-active').attr('aria-pressed','false');
      var on = !$card.hasClass('is-active');
      $card.toggleClass('is-active', on).attr('aria-pressed', on ? 'true' : 'false');
    }

    var keys = [];
    $wrap.find('.rf-step[data-step="3"] .rf-terra-card.is-active').each(function(){
      keys.push(($(this).data('key') || '').toString());
    });
    $wrap.find('input[name="rf_barrierefreiheit"]').val(keys.join(','));
    syncHiddenFields($wrap);
    updateNextButtonState($wrap, 3);
    persistState($wrap);
  }

  // SureForms hidden fields: best-effort mapping (your existing base64 label codes)
  function syncSureFormsHidden($sfForm, map){
    try{
      if(!$sfForm || !$sfForm.length) return;
      var codeMap = {
        rf_aufgabe:'cmZfYXVmZ2FiZQ',
        rf_einsatzgebiet:'cmZfZWluc2F0emdlYmlldA',
        rf_barrierefreiheit:'cmZfYmFycmllcmVmcmVpaGVpdA',
        rf_flaeche_qm:'cmZfZmxhZWNoZV9xbQ',
        rf_page_url:'cmZfcGFnZV91cmw',
        rf_session_id:'cmZfc2Vzc2lvbl9pZA',
        rf_environment:'cmZfZW52aXJvbm1lbnQ',
        rf_environments:'cmZfZW52aXJvbm1lbnRz',
        rf_notes:'cmZfbm90ZXM',
        rf_critical_notes:'cmZfY3JpdGljYWxfbm90ZXM',
        rf_timestamp:'cmZfdGltZXN0YW1w'
      };
      Object.keys(map).forEach(function(k){
        var code = codeMap[k];
        if(!code) return;
        var $inp = $sfForm.find('input[type="hidden"][name^="srfm-hidden"][name*="lbl-' + code + '"]');
        if($inp.length) $inp.val(map[k]);
      });
    }catch(e){}
  }

  function syncSureFormsBeforeSubmit($wrap, formEl){
    var $sf = $(formEl);
    var st = computeState($wrap);
    var payload = {
      rf_aufgabe: st.aufgabe || '',
      rf_task: st.aufgabe || '',
      rf_einsatzgebiet: (st.einsatzgebiet||[]).join(','),
            rf_environment: (st.einsatzgebiet||[]).join(','),
      rf_environment: (st.einsatzgebiet||[]).join(','),
      rf_environments: (st.einsatzgebiet||[]).join(','),
      rf_barrierefreiheit: ($wrap.find('input[name="rf_barrierefreiheit"]').val() || ''),
      rf_flaeche_qm: st.flaeche_qm || '',
      rf_notes: st.notes || '',
      rf_critical_notes: st.critical_notes || '',
      rf_manual_check_required: st.manual_check_required ? '1' : '0',
      rf_page_url: window.location.href,
      rf_session_id: (sessionStorage.getItem('rf_session_id') || ''),
      rf_timestamp: (new Date()).toISOString()
    };
    syncSureFormsHidden($sf, payload);

    // Fallback mapping: if the user created hidden/text fields with readable names,
    // fill them too (works across SureForms variations).
    try{
      Object.keys(payload).forEach(function(k){
        var v = payload[k];
        $sf.find('input[name*="'+k+'"], textarea[name*="'+k+'"], input[id*="'+k+'"], textarea[id*="'+k+'"]').each(function(){
          $(this).val(v);
        });
      });
    }catch(e){}
  }

  

// Robust prefill for SureForms fields by exact name, partial match, or label text.
function rfFillSureFormsMapped($sf, payload){
  try{
    var setIfEmpty = function($el, val){
      if(!$el || !$el.length) return;
      $el.each(function(){
        var el = this;
        // don't override user input
        if(typeof el.value !== 'undefined' && String(el.value||'').trim() === ''){
          el.value = val;
          // trigger change for any listeners
          try{ $(el).trigger('input').trigger('change'); }catch(e){}
        }
      });
    };

    var findByLabelText = function(key){
      // Try: <label>rf_aufgabe</label> then nearest input/textarea
      var $labels = $sf.find('label').filter(function(){
        return String($(this).text()||'').trim() === key;
      });
      if(!$labels.length){
        // also allow partial match
        $labels = $sf.find('label').filter(function(){
          return String($(this).text()||'').indexOf(key) !== -1;
        });
      }
      if(!$labels.length) return $();
      var $l = $labels.first();
      var forId = $l.attr('for');
      if(forId){
        var safeId = String(forId).replace(/([ #;?%&,.+*~\':"!^$\[\]()=>|\/])/g,'\\$1');
        var $t = $sf.find('#'+safeId);
        if($t.length) return $t;
      }
      // common SureForms structure: label + input in same wrapper
      var $wrap = $l.closest('.sureforms-field, .sf-field, .srf-field, .form-field, .field, .sf-field-wrap');
      if($wrap.length){
        var $in = $wrap.find('input, textarea, select').not('[type="hidden"]').first();
        if($in.length) return $in;
      }
      // fallback: next input/textarea in DOM
      var $next = $l.nextAll('input, textarea, select').first();
      return $next;
    };

    Object.keys(payload||{}).forEach(function(key){
      var val = payload[key];
      if(val == null) val = '';
      // 1) exact name/id
      setIfEmpty($sf.find('[name="'+key+'"], #'+key), val);
      // 2) partial name/id (for field arrays or prefixed names)
      setIfEmpty($sf.find('input[name*="'+key+'"], textarea[name*="'+key+'"], select[name*="'+key+'"], input[id*="'+key+'"], textarea[id*="'+key+'"], select[id*="'+key+'"]'), val);
      // 3) label text match
      setIfEmpty(findByLabelText(key), val);
    });
  }catch(e){}
}
// =====================
  // Init
  // =====================
  function initOne($wrap){
    // ensure a session id
    try{
      if(!sessionStorage.getItem('rf_session_id')){
        sessionStorage.setItem('rf_session_id', 'rf_' + Math.random().toString(16).slice(2) + '_' + Date.now());
      }
    }catch(e){}

    // hide all steps, show first
    $wrap.find('.rf-step').hide().removeClass('is-active');
    $wrap.find('.rf-step[data-step="1"]').show().addClass('is-active');
    $wrap.data('rf-step', 1);

    // initial visuals
    $wrap.find('.rf-tile').each(function(){ syncTileVisual($(this)); });

    // init area display
    var $checkedArea = $wrap.find('.rf-step[data-step="4"] input[name="rf_area_bucket"]:checked').first();
    if($checkedArea.length){
      var label = $checkedArea.val();
      var map = {
        '50-500':'50–500 m²',
        '500-1000':'500–1.000 m²',
        '1000-2000':'1.000–2.000 m²',
        '2000-5000':'2.000–5.000 m²',
        '5000-10000':'5.000–10.000 m²',
        '10000-50000':'10.000–50.000 m²',
        '>50000':'Über 50.000 m²'
      };
      var hr = map[label] || label;
      $wrap.find('input[name="rf_flaeche_qm"]').val(hr);
      $wrap.find('[data-rf-area-val]').text(hr);
    }

    syncHiddenFields($wrap);
    setProgress($wrap, 1);
    updateTopbar($wrap, 1);
    updateNextButtonState($wrap, 1);

    // If user lands on the Robo Finder entry page, always start at Step 1 (fresh)
    // unless explicitly requested to resume via ?rf_resume=1
    var shouldResume = false;
    try{
      var qs = (window.location.search || '');
      shouldResume = /(^|[?&])rf_resume=1(&|$)/.test(qs);
      var path = (window.location.pathname || '').replace(/\/+$/,'');
      // common slugs: /robo-finder or /robo-finder/
      var isEntry = /\/robo-finder$/i.test(path);
      if(isEntry && !shouldResume){
        sessionStorage.removeItem(STORAGE_KEY);
      }
    }catch(e){}

    // restore previous state if present (unless cleared above)
    var restored = restoreState($wrap);
    if(restored && restored.step){
      showStep($wrap, restored.step);
    } else {
      showStep($wrap, 1);
    }
  }

  $(function(){
    $('.rf-wrap[data-rf="finder"]').each(function(){ initOne($(this)); });
  });

  // Tile input change
  $(document).on('change', '.rf-wrap[data-rf="finder"] .rf-tile input', function(){
    var $wrap = getWrap($(this));
    syncTileVisual($(this).closest('.rf-tile'));
    syncHiddenFields($wrap);
    updateNextButtonState($wrap, clampStep($wrap, $wrap.data('rf-step') || 1));
    persistState($wrap);
  });

    
// Tiles: robust click handling (forces toggle to avoid theme/overlay interference)
// - radio tiles: select one, click again to unselect
// - checkbox tiles: toggle on/off
$(document).on('click', '.rf-wrap[data-rf="finder"] .rf-tile', function(e){
  var $tile = $(this);
  var $inp = $tile.find('input').first();
  if(!$inp.length) return;

  // Prevent double toggling (label default + manual toggle)
  e.preventDefault();

  var type = String($inp.attr('type') || '').toLowerCase();
  var name = $inp.attr('name') || '';

  if(type === 'radio'){
    if($inp.prop('checked')){
      // unselect on second click
      if(name){
        $tile.closest('.rf-tiles').find('input[type="radio"][name="'+name+'"]').prop('checked', false);
      } else {
        $inp.prop('checked', false);
      }
      $inp.trigger('change');
      return;
    }

    // select this one
    if(name){
      $tile.closest('.rf-tiles').find('input[type="radio"][name="'+name+'"]').prop('checked', false);
    }
    $inp.prop('checked', true).trigger('change');
    return;
  }

  // checkbox
  var next = !$inp.prop('checked');
  $inp.prop('checked', next).trigger('change');
});

// Next
  $(document).on('click', '.rf-wrap[data-rf="finder"] .rf-next', function(e){
    var $wrap = getWrap($(this));
    var step = clampStep($wrap, $wrap.data('rf-step') || 1);
    if(!isStepValid($wrap, step)){
      e.preventDefault();
      e.stopPropagation();
      updateNextButtonState($wrap, step);
    renderMiniSummary($wrap);
      return;
    }
    showStep($wrap, step + 1);
  });

  // Step 5 CTA ("Empfehlung erhalten" / "Machbarkeit prüfen lassen")
  // In this Robo Finder, Step 6 contains the embedded SureForms form.
  // The CTA on Step 5 must therefore navigate to Step 6 (instead of being a dead end).
  $(document).on('click', '.rf-wrap[data-rf="finder"] .rf-to-form', function(e){
    var $wrap = getWrap($(this));
    var step = clampStep($wrap, $wrap.data('rf-step') || 1);
    // Only meaningful on step 5
    if(step !== 5){
      // fall back to next behaviour
      showStep($wrap, step + 1);
      return;
    }
    if(!isStepValid($wrap, 5)){
      e.preventDefault();
      e.stopPropagation();
      updateNextButtonState($wrap, 5);
      updateStep5SpecialUI($wrap);
      return;
    }
    // Ensure hidden fields are synced before showing the form.
    syncHiddenFields($wrap);
    showStep($wrap, 6);
  });

  // Summary: jump back to a specific step
  $(document).on('click', '.rf-wrap[data-rf="finder"] [data-rf-goto]', function(e){
    e.preventDefault();
    var $wrap = getWrap($(this));
    var target = parseInt($(this).attr('data-rf-goto') || '1', 10);
    if(!isFinite(target) || target < 1) target = 1;
    showStep($wrap, target);
    // bring user back to the wizard top
    var $card = $wrap.find('.rf-card').first();
    if($card.length){
      window.scrollTo({ top: Math.max(0, $card.offset().top - 24), behavior: 'smooth' });
    }
  });

  // Back
  $(document).on('click', '.rf-wrap[data-rf="finder"] .rf-prev, .rf-wrap[data-rf="finder"] .rf-back', function(e){
    e.preventDefault();
    var $wrap = getWrap($(this));
    var step = clampStep($wrap, $wrap.data('rf-step') || 1);
    showStep($wrap, step - 1);
  });

  // Barrier cards
  $(document).on('click', '.rf-wrap[data-rf="finder"] .rf-step[data-step="3"] .rf-terra-card', function(){
    var $wrap = getWrap($(this));
    toggleBarrier($wrap, $(this));
  });
  $(document).on('keydown', '.rf-wrap[data-rf="finder"] .rf-step[data-step="3"] .rf-terra-card', function(e){
    if(e.key !== 'Enter' && e.key !== ' ') return;
    e.preventDefault();
    var $wrap = getWrap($(this));
    toggleBarrier($wrap, $(this));
  });

  // Area bucket
  $(document).on('change', '.rf-wrap[data-rf="finder"] .rf-step[data-step="4"] input[name="rf_area_bucket"]', function(){
    var $wrap = getWrap($(this));
    var label = $(this).val();
    var map = {
      '50-500':'50–500 m²',
      '500-1000':'500–1.000 m²',
      '1000-2000':'1.000–2.000 m²',
      '2000-5000':'2.000–5.000 m²',
      '5000-10000':'5.000–10.000 m²',
      '10000-50000':'10.000–50.000 m²',
      '>50000':'Über 50.000 m²'
    };
    var hr = map[label] || label;
    $wrap.find('input[name="rf_flaeche_qm"]').val(hr);
    $wrap.find('[data-rf-area-val]').text(hr);
    syncHiddenFields($wrap);
    updateNextButtonState($wrap, 4);
    persistState($wrap);
  });


  // Auto-grow helper for textareas
  function autoGrow(el){
    try{
      el.style.height = 'auto';
      el.style.height = Math.min(el.scrollHeight, 260) + 'px';
    }catch(e){}
  }

  function updateCounters($wrap){
    try{
      var $nEl = $wrap.find('[data-rf-notes]').first();
      if(!$nEl.length){
        $nEl = $wrap.find('textarea[name="rf_notes"], input[name="rf_notes"]').not('input[type="hidden"]').first();
      }
      var n = String(($nEl.val()||'')).length;
      $wrap.find('[data-rf-notes-count]').text(String(n));

      var $cEl = $wrap.find('[data-rf-critical-notes]').first();
      if(!$cEl.length){
        $cEl = $wrap.find('textarea[name="rf_critical_notes"], input[name="rf_critical_notes"]').not('input[type="hidden"]').first();
      }
      var c = String(($cEl.val()||'')).length;
      $wrap.find('[data-rf-critical-count]').text(String(c));
    }catch(e){}
  }

  // Notes (Step 5): sync textarea -> hidden fields + summaries
  $(document).on('input', '.rf-wrap[data-rf="finder"] [data-rf-notes]', function(){
    var $wrap = $(this).closest('.rf-wrap');
    var val = String($(this).val() || '');
    autoGrow(this);
    $wrap.find('input[name="rf_notes"]').val(val);
    updateCounters($wrap);
    persistState($wrap);
    if(parseInt($wrap.data('rf-step')||$wrap.attr('data-rf-step')||0,10) === 5){
      renderSummary($wrap);
      renderMiniSummary($wrap);
    }
  });

  // Notes fallback (Step 5): if templates use name="rf_notes" without data-rf-notes
  $(document).on('input', '.rf-wrap[data-rf="finder"] textarea[name="rf_notes"]:not([data-rf-notes]), .rf-wrap[data-rf="finder"] input[name="rf_notes"]:not([type="hidden"]):not([data-rf-notes])', function(){
    var $wrap = $(this).closest('.rf-wrap');
    var val = String($(this).val() || '');
    autoGrow(this);
    // keep hidden + (if present) mirrored textarea in sync
    $wrap.find('input[name="rf_notes"]').val(val);
    var $mirror = $wrap.find('[data-rf-notes]').first();
    if($mirror.length && $mirror.get(0) !== this){ $mirror.val(val); }
    updateCounters($wrap);
    persistState($wrap);
    if(parseInt($wrap.data('rf-step')||$wrap.attr('data-rf-step')||0,10) === 5){
      renderSummary($wrap);
      renderMiniSummary($wrap);
    }
  });


  $(document).on('input', '.rf-wrap[data-rf="finder"] [data-rf-critical-notes]', function(){
    var $wrap = $(this).closest('.rf-wrap');
    var val = String($(this).val() || '');
    autoGrow(this);
    $wrap.find('input[name="rf_critical_notes"]').val(val);
    updateCounters($wrap);
    persistState($wrap);
    // validate Step 5
    var ok5 = isStepValid($wrap, 5);
    $wrap.find('[data-rf-critical-hint]').prop('hidden', ok5);
    updateNextButtonState($wrap, 5);
    renderSummary($wrap);
    renderMiniSummary($wrap);
  });

  // Critical notes fallback (Step 5): if templates use name="rf_critical_notes" without data-rf-critical-notes
  $(document).on('input', '.rf-wrap[data-rf="finder"] textarea[name="rf_critical_notes"]:not([data-rf-critical-notes]), .rf-wrap[data-rf="finder"] input[name="rf_critical_notes"]:not([type="hidden"]):not([data-rf-critical-notes])', function(){
    var $wrap = $(this).closest('.rf-wrap');
    var val = String($(this).val() || '');
    autoGrow(this);
    $wrap.find('input[name="rf_critical_notes"]').val(val);
    var $mirror = $wrap.find('[data-rf-critical-notes]').first();
    if($mirror.length && $mirror.get(0) !== this){ $mirror.val(val); }
    updateCounters($wrap);
    persistState($wrap);
    // validate Step 5
    var ok5 = isStepValid($wrap, 5);
    $wrap.find('[data-rf-critical-hint]').prop('hidden', ok5);
    updateNextButtonState($wrap, 5);
    renderSummary($wrap);
    renderMiniSummary($wrap);
  });


  // Quick tags: toggle bullet in notes
  $(document).on('click', '.rf-wrap[data-rf="finder"] [data-rf-add-note]', function(e){
    e.preventDefault();
    var $btn = $(this);
    var add = String($btn.attr('data-rf-add-note') || '').trim();
    if(!add) return;
    var $wrap = $btn.closest('.rf-wrap');
    var $ta = $wrap.find('[data-rf-notes]').first();
    if(!$ta.length) return;

    var current = String($ta.val() || '');
    var bullet = '• ' + add;
    var lines = current.split(/\r?\n/);
    var idx = lines.findIndex(function(l){ return l.trim() === bullet; });
    if(idx !== -1){
      // remove
      lines.splice(idx, 1);
      $btn.removeClass('is-active');
    } else {
      // add
      lines = lines.filter(function(l){ return String(l).trim() !== ''; });
      lines.push(bullet);
      $btn.addClass('is-active');
    }
    $ta.val(lines.join("\n")).trigger('input');
  });

  // Clear notes + tag states
  $(document).on('click', '.rf-wrap[data-rf="finder"] [data-rf-clear-notes]', function(e){
    e.preventDefault();
    var $wrap = $(this).closest('.rf-wrap');
    $wrap.find('[data-rf-add-note]').removeClass('is-active');
    var $ta = $wrap.find('[data-rf-notes]').first();
    if($ta.length){ $ta.val('').trigger('input'); }
  });

  // SureForms: sync right before submit
  document.addEventListener('submit', function(ev){
    var formEl = ev.target;
    if(!formEl || !formEl.closest) return;
    // only for forms inside our Step 6 container
    var wrapEl = formEl.closest('.rf-wrap');
    var finalEl = formEl.closest('.rf-terra-final-form');
    if(!wrapEl || !finalEl) return;
    var $wrap = $(wrapEl);
    syncHiddenFields($wrap);
    syncSureFormsBeforeSubmit($wrap, formEl);
    persistState($wrap);
  }, true);

});
