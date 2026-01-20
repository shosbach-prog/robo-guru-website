<?php
/**
 * Plugin Name: RG ROI Rechner
 * Description: ROI-Rechner (Reinigungs-/Service-/Lieferroboter) inkl. SureForms Lightbox.
 * Version: 1.3.0
 * Author: Robo-Guru
 */

if (!defined('ABSPATH')) exit;

function rg_roi_normalize_sureforms_shortcode($raw, $sureforms_id=''){
    // Preferred: sureforms_id
    if (!empty($sureforms_id)) {
        $id = preg_replace('/[^0-9]/', '', (string)$sureforms_id);
        if ($id !== '') return '[sureforms id="' . $id . '"]';
    }

    $raw = (string)$raw;
    $raw = html_entity_decode($raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $raw = trim($raw);

    if ($raw === '') return '';

    // Common user error: curly braces instead of brackets
    // e.g. {sureforms id="123"}
    if (substr($raw, 0, 1) === '{') {
        $raw = preg_replace('/^\{/', '[', $raw);
        $raw = preg_replace('/\}$/', ']', $raw);
    }

    // If they pass only a number, treat as id
    if (preg_match('/^\d+$/', $raw)) {
        return '[sureforms id="' . $raw . '"]';
    }

    // If missing brackets, wrap it
    if (substr($raw, 0, 1) !== '[') {
        $raw = '[' . $raw;
    }
    if (substr($raw, -1) !== ']') {
        $raw = $raw . ']';
    }

    return $raw;
}

add_shortcode('rg_roi_calculator', function($atts){
    $atts = shortcode_atts([
        'sureforms_id' => '',
        'sureforms'    => '',
        'title'        => 'Ergebnis anfordern',
        'default'      => 'cleaning', // cleaning|service|delivery
    ], $atts, 'rg_roi_calculator');

    $uid = 'rgroi_' . wp_generate_uuid4();

    $sure_sc = rg_roi_normalize_sureforms_shortcode($atts['sureforms'], $atts['sureforms_id']);
    $sure_html = $sure_sc ? do_shortcode($sure_sc) : '<div style="opacity:.7;font-size:13px">Bitte SureForms setzen: <code>sureforms_id</code> oder <code>sureforms</code>.</div>';

    $def = in_array($atts['default'], ['cleaning','service','delivery'], true) ? $atts['default'] : 'cleaning';

    ob_start(); ?>
<div class="rg-roi" id="<?php echo esc_attr($uid); ?>" data-default="<?php echo esc_attr($def); ?>">
  <style>
    .rg-roi{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;max-width:1100px;margin:0 auto}
    .rg-roi .rg-card{border:1px solid rgba(0,0,0,.08);border-radius:18px;box-shadow:0 8px 24px rgba(0,0,0,.06);overflow:hidden;background:#fff}
    .rg-roi .rg-head{padding:18px 18px 14px;background:linear-gradient(90deg,#14b8a6,#2563eb);color:#fff}
    .rg-roi .rg-head h2{margin:0;font-size:22px;line-height:1.2}
    .rg-roi .rg-head p{margin:8px 0 0;opacity:.95}
    .rg-roi .rg-steps{margin-top:12px;display:flex;gap:10px;flex-wrap:wrap}
    .rg-roi .rg-step{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.18);padding:8px 10px;border-radius:999px;font-weight:800;font-size:12px}
    .rg-roi .rg-body{padding:18px;background:#fff}
    .rg-roi .rg-grid{display:grid;grid-template-columns:1fr;gap:14px}
    @media(min-width:980px){.rg-roi .rg-grid{grid-template-columns:1.25fr .75fr}}
    .rg-roi .rg-panel{border:1px solid rgba(0,0,0,.08);border-radius:16px;padding:14px}
    .rg-roi h3{margin:0 0 10px;font-size:16px}
    .rg-roi .rg-choice{display:grid;grid-template-columns:1fr;gap:10px;margin-bottom:12px}
    @media(min-width:720px){.rg-roi .rg-choice{grid-template-columns:1fr 1fr 1fr}}
    .rg-roi .rg-cardbtn{border:1px solid rgba(0,0,0,.10);border-radius:16px;padding:12px;cursor:pointer;background:linear-gradient(180deg,#fff,#f8fafc);transition:.12s;text-align:left}
    .rg-roi .rg-cardbtn:hover{transform:translateY(-1px)}
    .rg-roi .rg-cardbtn[aria-pressed="true"]{outline:3px solid rgba(20,184,166,.25);border-color:rgba(20,184,166,.45)}
    .rg-roi .rg-cardbtn .t{font-weight:900;margin:0 0 6px;font-size:14px}
    .rg-roi .rg-cardbtn .d{opacity:.75;font-size:12.5px;margin:0}
    .rg-roi label{display:block;font-weight:750;margin:10px 0 6px}
    .rg-roi .rg-row{display:grid;grid-template-columns:1fr;gap:10px}
    @media(min-width:720px){.rg-roi .rg-row{grid-template-columns:1fr 1fr}}
    .rg-roi input,.rg-roi select{width:100%;padding:12px;border-radius:12px;border:1px solid rgba(0,0,0,.16);outline:none;transition:.15s;background:#fff}
    .rg-roi input:focus,.rg-roi select:focus{border-color:#14b8a6;box-shadow:0 0 0 4px rgba(20,184,166,.15)}
    .rg-roi .rg-hint{font-size:12.5px;opacity:.75;margin-top:6px}
    .rg-roi .rg-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
    .rg-roi button{border:0;border-radius:999px;padding:12px 14px;font-weight:850;cursor:pointer;background:#14b8a6;color:#062a26;transition:.15s}
    .rg-roi button:hover{transform:translateY(-1px)}
    .rg-roi .rg-secondary{background:#111827;color:#fff}
    .rg-roi .rg-results{display:grid;gap:10px}
    .rg-roi .rg-kpi{border-radius:16px;padding:12px;border:1px solid rgba(0,0,0,.08);background:linear-gradient(180deg,#ffffff,#f8fafc)}
    .rg-roi .rg-kpi strong{display:block;font-size:12px;opacity:.7;margin-bottom:4px}
    .rg-roi .rg-kpi span{font-size:20px;font-weight:950}
    .rg-roi .rg-note{font-size:12.5px;opacity:.75}
    .rg-roi .rg-warn{padding:10px 12px;border-radius:14px;background:#fff7ed;border:1px solid rgba(249,115,22,.25);display:none}
    .rg-roi .rg-warn b{color:#9a3412}
    .rg-roi .rg-eval{border-radius:16px;padding:12px;border:1px solid rgba(20,184,166,.25);background:rgba(20,184,166,.06)}
    .rg-roi .rg-eval.bad{border-color:rgba(239,68,68,.25);background:rgba(239,68,68,.06)}
    .rg-roi .rg-eval.mid{border-color:rgba(245,158,11,.25);background:rgba(245,158,11,.06)}
    .rg-roi .rg-eval .h{font-weight:950;margin:0 0 4px}
    .rg-roi .rg-eval .p{margin:0;opacity:.85}

    /* Modal */
    .rg-roi .rg-modal{position:fixed;inset:0;display:none;z-index:999999}
    .rg-roi .rg-modal.rg-open{display:block}
    .rg-roi .rg-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.58);backdrop-filter:blur(6px)}
    .rg-roi .rg-dialog{position:relative;max-width:980px;margin:6vh auto;background:#fff;border-radius:18px;box-shadow:0 24px 70px rgba(0,0,0,.35);overflow:hidden}
    .rg-roi .rg-dialog-head{display:flex;justify-content:space-between;align-items:center;padding:14px;background:linear-gradient(90deg,#14b8a6,#2563eb);color:#fff}
    .rg-roi .rg-close{border:0;background:rgba(255,255,255,.18);color:#fff;border-radius:999px;width:38px;height:38px;cursor:pointer;font-size:18px;font-weight:900}
    .rg-roi .rg-dialog-body{padding:14px;max-height:74vh;overflow:auto}
    .rg-roi .rg-summary{border:1px solid rgba(0,0,0,.08);border-radius:14px;padding:12px;margin:0 0 12px;background:linear-gradient(180deg,#fff,#f8fafc)}
    .rg-roi .rg-summary-grid{display:grid;gap:8px}
    @media(min-width:720px){.rg-roi .rg-summary-grid{grid-template-columns:1fr 1fr}}
    .rg-roi .rg-pill{display:flex;justify-content:space-between;gap:10px;font-size:13px}
    .rg-roi .rg-pill b{opacity:.7}
    .rg-roi .rg-pill span{font-weight:950}
  </style>

  <div class="rg-card">
    <div class="rg-head">
      <h2>ROI-Rechner für Reinigungs-, Service- & Lieferroboter</h2>
      <p>Berechne in 60 Sekunden, wie viel <b>Personalzeit</b> und <b>Kosten</b> du pro Jahr sparen kannst – inkl. ROI & Amortisation.</p>
      <div class="rg-steps">
        <div class="rg-step">① Anwendungsfall wählen</div>
        <div class="rg-step">② Personal & Kosten eingeben</div>
        <div class="rg-step">③ Ergebnis sofort sehen</div>
      </div>
    </div>

    <div class="rg-body">
      <div class="rg-grid">
        <div class="rg-panel">
          <h3>Wofür soll der ROI berechnet werden?</h3>
          <div class="rg-choice">
            <button type="button" class="rg-cardbtn" data-rg="case" data-case="cleaning" aria-pressed="false">
              <p class="t">🧽 Reinigungsroboter</p>
              <p class="d">Bodenreinigung, Hallen, Märkte, Büros → Fokus: Stunden & Fläche (optional)</p>
            </button>
            <button type="button" class="rg-cardbtn" data-rg="case" data-case="service" aria-pressed="false">
              <p class="t">🛎️ Service-Roboter</p>
              <p class="d">Runner, Abräumen, Servieren → Fokus: eingesparte Personalstunden</p>
            </button>
            <button type="button" class="rg-cardbtn" data-rg="case" data-case="delivery" aria-pressed="false">
              <p class="t">📦 Lieferroboter</p>
              <p class="d">Interne Transporte, Klinik, Logistik → Fokus: Prozesszeit & Personal</p>
            </button>
          </div>

          <div class="rg-row" data-rg="rowArea">
            <div>
              <label>Fläche (m²)</label>
              <input data-rg="area" type="number" min="0" step="10" value="2000" />
              <div class="rg-hint">Optional – nur zur Einordnung der Flächengröße. (Berechnung basiert primär auf Stunden.)</div>
            </div>
            <div>
              <label data-rg="lblHours">Reinigungsstunden pro Woche</label>
              <input data-rg="hoursWeek" type="number" min="0" step="0.5" value="35" />
              <div class="rg-hint" data-rg="hintHours">Nur die relevante Reinigungszeit eintragen.</div>
            </div>
          </div>

          <div class="rg-row">
            <div>
              <label>Stundenlohn (€/h)</label>
              <input data-rg="wage" type="number" min="0" step="0.5" value="17.50" />
            </div>
            <div>
              <label>Overhead-Faktor</label>
              <select data-rg="overhead">
                <option value="1.00">1,00 (nur Lohn)</option>
                <option value="1.25" selected>1,25 (Lohn + NK)</option>
                <option value="1.40">1,40 (Vollkosten)</option>
                <option value="1.60">1,60 (Vollkosten + Admin)</option>
              </select>
              <div class="rg-hint">Berücksichtigt Lohnnebenkosten, Ausfallzeiten & Verwaltung. Empfehlung: 1,25–1,40.</div>
            </div>
          </div>

          <div class="rg-row">
            <div>
              <label>Roboter-Klasse</label>
              <select data-rg="robot">
                <option value="entry" selected>Einstieg (3.000–15.000 €)</option>
                <option value="mid">Mittelklasse (15.000–30.000 €)</option>
                <option value="high">High-End (ab 30.000 €)</option>
                <option value="custom">Custom (manuell)</option>
              </select>
              <div class="rg-hint">Einstieg-Leasing ist auf ~500 €/Monat vorbelegt.</div>
            </div>
            <div>
              <label>Modell</label>
              <select data-rg="mode">
                <option value="lease" selected>Leasing / Miete</option>
                <option value="buy">Kauf</option>
              </select>
            </div>
          </div>

          <div class="rg-row">
            <div>
              <label>Service/Betriebskosten (€/Monat)</label>
              <input data-rg="service" type="number" min="0" step="10" value="90" />
            </div>
            <div>
              <label>Nutzungsdauer (Jahre) – nur Kauf</label>
              <input data-rg="years" type="number" min="1" step="1" value="5" />
            </div>
          </div>

          <div class="rg-row">
            <div>
              <label>Leasingrate (€/Monat)</label>
              <input data-rg="leaseRate" type="number" min="0" step="10" value="500" />
            </div>
            <div>
              <label>Kaufpreis (€)</label>
              <input data-rg="buyPrice" type="number" min="0" step="100" value="9900" />
            </div>
          </div>

          <div class="rg-actions">
            <button type="button" data-rg="calc">ROI berechnen</button>
            <button type="button" class="rg-secondary" data-rg="open"><?php echo esc_html($atts['title']); ?></button>
          </div>

          <div class="rg-warn" data-rg="warn"><b>Hinweis:</b> Ersparnis ≤ 0. Prüfe Vollkosten oder Roboter-Kosten.</div>
        </div>

        <div class="rg-panel">
          <div class="rg-results">
            <div class="rg-kpi"><strong>Manuelle Kosten / Jahr</strong><span data-rg="manualYear">–</span></div>
            <div class="rg-kpi"><strong>Roboter-Kosten / Jahr</strong><span data-rg="robotYear">–</span></div>
            <div class="rg-kpi"><strong>Ersparnis / Jahr</strong><span data-rg="savingsYear">–</span></div>
            <div class="rg-kpi"><strong>ROI</strong><span data-rg="roi">–</span></div>
            <div class="rg-kpi"><strong>Amortisation</strong><span data-rg="payback">–</span></div>

            <div class="rg-eval" data-rg="evalBox">
              <p class="h" data-rg="evalH">Wirtschaftlich sinnvoll</p>
              <p class="p" data-rg="evalP">Du sparst voraussichtlich <b data-rg="evalSave">–</b> pro Jahr. Bei Leasing ist die Wirkung in der Regel sofort sichtbar.</p>
            </div>

            <div class="rg-actions" style="margin-top:10px">
              <button type="button" data-rg="open2">Beratung & Ergebnis anfordern</button>
            </div>

            <div class="rg-note">*Std/Woche × 52 × Lohn × Overhead vs. Leasing/Abschreibung + Service. (ROI basiert auf eingesparter Personalzeit)</div>
          </div>
        </div>
      </div>
    </div>

    <div class="rg-modal" data-rg="modal" aria-hidden="true">
      <div class="rg-backdrop" data-rg="close"></div>
      <div class="rg-dialog" role="dialog" aria-modal="true">
        <div class="rg-dialog-head">
          <strong><?php echo esc_html($atts['title']); ?></strong>
          <button class="rg-close" type="button" data-rg="close" aria-label="Schließen">×</button>
        </div>
        <div class="rg-dialog-body">
          <div class="rg-summary">
            <div class="rg-summary-grid">
              <div class="rg-pill"><b>Anwendungsfall</b><span data-rg="sCase">–</span></div>
              <div class="rg-pill"><b>Std/Woche</b><span data-rg="sHours">–</span></div>
              <div class="rg-pill"><b>Stundenlohn</b><span data-rg="sWage">–</span></div>
              <div class="rg-pill"><b>Modell</b><span data-rg="sMode">–</span></div>
              <div class="rg-pill"><b>Ersparnis/Jahr</b><span data-rg="sSavings">–</span></div>
              <div class="rg-pill"><b>ROI</b><span data-rg="sRoi">–</span></div>
            </div>
            <div class="rg-hint" style="margin-top:8px">Wenn das Formular leer bleibt: nutze <code>sureforms_id</code> im Shortcode.</div>
          </div>

          <div class="rg-form">
            <?php echo $sure_html; ?>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function(){
        const root = document.getElementById(<?php echo json_encode($uid); ?>);
        if(!root) return;
        const $ = (sel)=> root.querySelector(sel);
        const $$ = (sel)=> Array.from(root.querySelectorAll(sel));
        const fmtEUR0 = (n)=> isFinite(n) ? new Intl.NumberFormat('de-DE',{style:'currency',currency:'EUR',maximumFractionDigits:0}).format(n) : '–';
        const fmtNum1 = (n)=> isFinite(n) ? new Intl.NumberFormat('de-DE',{maximumFractionDigits:1}).format(n) : '–';

        const inputs = {
          area:      $('[data-rg="area"]'),
          hoursWeek: $('[data-rg="hoursWeek"]'),
          wage:      $('[data-rg="wage"]'),
          overhead:  $('[data-rg="overhead"]'),
          robot:     $('[data-rg="robot"]'),
          mode:      $('[data-rg="mode"]'),
          service:   $('[data-rg="service"]'),
          years:     $('[data-rg="years"]'),
          leaseRate: $('[data-rg="leaseRate"]'),
          buyPrice:  $('[data-rg="buyPrice"]')
        };

        const ui = {
          rowArea: $('[data-rg="rowArea"]'),
          lblHours: $('[data-rg="lblHours"]'),
          hintHours: $('[data-rg="hintHours"]'),
          manualYear: $('[data-rg="manualYear"]'),
          robotYear:  $('[data-rg="robotYear"]'),
          savingsYear:$('[data-rg="savingsYear"]'),
          roi:        $('[data-rg="roi"]'),
          payback:    $('[data-rg="payback"]'),
          warn:       $('[data-rg="warn"]'),
          evalBox:    $('[data-rg="evalBox"]'),
          evalH:      $('[data-rg="evalH"]'),
          evalP:      $('[data-rg="evalP"]'),
          evalSave:   $('[data-rg="evalSave"]'),
          modal:      $('[data-rg="modal"]'),
          sCase:      $('[data-rg="sCase"]'),
          sHours:     $('[data-rg="sHours"]'),
          sWage:      $('[data-rg="sWage"]'),
          sMode:      $('[data-rg="sMode"]'),
          sSavings:   $('[data-rg="sSavings"]'),
          sRoi:       $('[data-rg="sRoi"]')
        };

        const caseLabels = { cleaning: 'Reinigungsroboter', service: 'Service-Roboter', delivery: 'Lieferroboter' };
        let currentCase = root.getAttribute('data-default') || 'cleaning';
        let last = { savingsYear: 0, roiPct: NaN };

        function setCase(c){
          currentCase = c;
          $$('[data-rg="case"]').forEach(btn=>{
            btn.setAttribute('aria-pressed', btn.getAttribute('data-case') === c ? 'true' : 'false');
          });

          if(c === 'cleaning'){
            ui.rowArea.style.display = '';
            ui.lblHours.textContent = 'Reinigungsstunden pro Woche';
            ui.hintHours.textContent = 'Nur die relevante Reinigungszeit eintragen.';
          } else {
            ui.rowArea.style.display = 'none';
            ui.lblHours.textContent = (c === 'service') ? 'Runner-/Service-Stunden pro Woche' : 'Transport-/Prozess-Stunden pro Woche';
            ui.hintHours.textContent = 'Wie viele Personalstunden pro Woche werden durch den Roboter voraussichtlich eingespart?';
          }
          calculate();
        }

        function applyPreset(){
          const t = inputs.robot.value;
          if(t === 'custom') return;
          const presets = {
            entry: { lease: 500,  buy:  9900, service:  90 },
            mid:   { lease: 900,  buy: 22900, service: 120 },
            high:  { lease: 1500, buy: 39900, service: 160 }
          };
          const p = presets[t] || presets.entry;
          inputs.leaseRate.value = p.lease;
          inputs.buyPrice.value  = p.buy;
          inputs.service.value   = p.service;
        }

        function evaluate(savingsYear, roiPct){
          ui.evalBox.classList.remove('bad','mid');
          if(savingsYear <= 0 || !isFinite(roiPct)){
            ui.evalBox.classList.add('bad');
            ui.evalH.textContent = 'Wirtschaftlich eher nicht sinnvoll';
            ui.evalP.innerHTML = 'Aktuell ist die Ersparnis <b>≤ 0</b>. Passe Vollkosten oder Roboter-Kosten an – oder fordere eine persönliche Einschätzung an.';
            ui.evalSave.textContent = fmtEUR0(savingsYear);
            return;
          }
          if(roiPct < 25){
            ui.evalBox.classList.add('mid');
            ui.evalH.textContent = 'Grenzwertig – genauer prüfen';
            ui.evalP.innerHTML = 'Du sparst voraussichtlich <b data-rg="evalSave"></b> pro Jahr. Wir empfehlen eine kurze Prüfung der Annahmen (Stunden, Vollkosten, Einsatzumfang).';
            ui.evalSave.textContent = fmtEUR0(savingsYear);
            return;
          }
          ui.evalH.textContent = 'Wirtschaftlich sinnvoll';
          ui.evalP.innerHTML = 'Du sparst voraussichtlich <b data-rg="evalSave"></b> pro Jahr. Bei Leasing ist die Wirkung in der Regel sofort sichtbar.';
          ui.evalSave.textContent = fmtEUR0(savingsYear);
        }

        function calculate(){
          const hW = parseFloat(inputs.hoursWeek.value) || 0;
          const w  = parseFloat(inputs.wage.value) || 0;
          const oh = parseFloat(inputs.overhead.value) || 1;

          const manualYear = hW * 52 * w * oh;

          const servM = parseFloat(inputs.service.value) || 0;
          const leaseM = parseFloat(inputs.leaseRate.value) || 0;
          const buy = parseFloat(inputs.buyPrice.value) || 0;
          const y = Math.max(1, parseFloat(inputs.years.value) || 5);

          let robotYear = 0;
          let invest = 0;

          if(inputs.mode.value === 'lease'){
            robotYear = (leaseM + servM) * 12;
            invest = robotYear;
          } else {
            const depreciationYear = buy / y;
            robotYear = depreciationYear + (servM * 12);
            invest = buy;
          }

          const savingsYear = manualYear - robotYear;

          let roiPct = NaN;
          if(invest > 0) roiPct = (savingsYear / invest) * 100;

          let paybackMonths = NaN;
          if(inputs.mode.value === 'buy'){
            if(savingsYear > 0) paybackMonths = (buy / (savingsYear/12));
          } else {
            paybackMonths = (savingsYear > 0) ? 0 : NaN;
          }

          last = { savingsYear, roiPct };

          ui.manualYear.textContent = fmtEUR0(manualYear);
          ui.robotYear.textContent  = fmtEUR0(robotYear);
          ui.savingsYear.textContent = (savingsYear >= 0) ? fmtEUR0(savingsYear) : ('-' + fmtEUR0(Math.abs(savingsYear)));
          ui.roi.textContent = isFinite(roiPct) ? (fmtNum1(roiPct) + ' %') : '–';
          ui.payback.textContent =
            (inputs.mode.value === 'buy')
              ? (isFinite(paybackMonths) ? (fmtNum1(paybackMonths) + ' Monate') : '–')
              : ((savingsYear > 0) ? 'sofort (Leasing)' : '–');

          ui.warn.style.display = (savingsYear <= 0) ? 'block' : 'none';
          evaluate(savingsYear, roiPct);
        }

        function openModal(){
          calculate();
          ui.sCase.textContent = caseLabels[currentCase] || currentCase;
          ui.sHours.textContent = fmtNum1(parseFloat(inputs.hoursWeek.value)||0);
          ui.sWage.textContent = fmtNum1(parseFloat(inputs.wage.value)||0) + ' €/h';
          ui.sMode.textContent = (inputs.mode.value === 'lease') ? 'Leasing/Miete' : 'Kauf';
          ui.sSavings.textContent = (last.savingsYear>=0 ? fmtEUR0(last.savingsYear) : ('-' + fmtEUR0(Math.abs(last.savingsYear))));
          ui.sRoi.textContent  = isFinite(last.roiPct) ? (fmtNum1(last.roiPct) + ' %') : '–';

          ui.modal.classList.add('rg-open');
          ui.modal.setAttribute('aria-hidden','false');
          document.body.style.overflow='hidden';
        }

        function closeModal(){
          ui.modal.classList.remove('rg-open');
          ui.modal.setAttribute('aria-hidden','true');
          document.body.style.overflow='';
        }

        $$('[data-rg="case"]').forEach(btn=>{
          btn.addEventListener('click', ()=> setCase(btn.getAttribute('data-case')));
        });

        $('[data-rg="calc"]').addEventListener('click', calculate);
        $('[data-rg="open"]').addEventListener('click', openModal);
        $('[data-rg="open2"]').addEventListener('click', openModal);

        $$( '[data-rg="close"]' ).forEach(x => x.addEventListener('click', closeModal));
        document.addEventListener('keydown', (e)=>{
          if(e.key === 'Escape' && ui.modal.classList.contains('rg-open')) closeModal();
        });

        inputs.robot.addEventListener('change', ()=>{ applyPreset(); calculate(); });
        ['input','change'].forEach(evt=>{
          Object.values(inputs).forEach(x=> x.addEventListener(evt, calculate));
        });

        applyPreset();
        setCase(currentCase);
      })();
    </script>
  </div>
</div>
<?php
    return ob_get_clean();
});
