
(function(){
  function q(sel, root){ return (root||document).querySelector(sel); }
  function qa(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

  function fmtEUR(x){
    return new Intl.NumberFormat('de-DE', { style:'currency', currency:'EUR' }).format(x);
  }
  function fmtNum(x){
    return new Intl.NumberFormat('de-DE', { maximumFractionDigits: 1 }).format(x);
  }

  function drawRoiChart(doc, x, y, w, h, monthlyNet, invest, monthsToShow) {
    doc.setLineWidth(0.2);
    doc.rect(x, y, w, h);

    const pad = 7;
    const cx = x + pad;
    const cw = w - pad * 2;
    const ch = h - pad * 2;

    if (!monthlyNet || monthlyNet <= 0 || !invest || invest <= 0) {
      doc.setFontSize(10);
      doc.text('Keine positive Netto-Ersparnis – Break-even nicht erreichbar.', cx, y + h/2);
      return { breakEvenMonth: null, monthsShown: monthsToShow || 36 };
    }

    const breakEvenMonth = Math.max(1, Math.ceil(invest / monthlyNet));
    const mShow = Math.min(Math.max(monthsToShow || 36, breakEvenMonth + 12), 60);

    const maxCum = monthlyNet * mShow;
    const maxYVal = Math.max(maxCum, invest * 1.2);

    const yFor = (val) => (y + h - pad) - (ch * (val / maxYVal));
    const xForM = (m) => cx + (cw * (m - 1)) / (mShow - 1);

    // x-axis
    doc.setLineWidth(0.2);
    doc.line(cx, y + h - pad, x + w - pad, y + h - pad);

    // Investitionslinie (gestrichelt)
    const yInvest = yFor(invest);
    doc.setLineWidth(0.4);
    doc.setLineDashPattern([2, 2], 0);
    doc.line(cx, yInvest, x + w - pad, yInvest);
    doc.setLineDashPattern([], 0);
    doc.setFontSize(9);
    doc.text('Investition', x + w - pad - 22, yInvest - 2);

    // Kumulierte Ersparnis (Linie)
    const points = [];
    for (let m = 1; m <= mShow; m++) {
      const cum = monthlyNet * m;
      points.push([xForM(m), yFor(cum), cum]);
    }

    doc.setLineWidth(0.7);
    for (let i = 1; i < points.length; i++) {
      doc.line(points[i-1][0], points[i-1][1], points[i][0], points[i][1]);
    }

    // Break-even Markierung (grün)
    const be = Math.min(breakEvenMonth, mShow);
    const beX = xForM(be);
    const beY = yFor(monthlyNet * be);

    doc.setDrawColor(0, 140, 0);
    doc.setLineWidth(0.6);
    doc.line(beX, y + pad, beX, y + h - pad);

    doc.setFillColor(0, 140, 0);
    doc.circle(beX, beY, 1.5, 'F');

    doc.setTextColor(0, 120, 0);
    doc.setFontSize(10);
    doc.text(`Break-even ab Monat ${breakEvenMonth}`, Math.min(beX + 2, x + w - pad - 55), y + pad + 10);

    // Reset
    doc.setDrawColor(0,0,0);
    doc.setTextColor(0,0,0);

    // Labels
    doc.setFontSize(9);
    doc.text('Kumulierte Netto-Ersparnis (Beispielrechnung)', cx, y + pad - 1);
    doc.text(`Monat 1`, cx, y + h - 2);
    doc.text(`Monat ${mShow}`, x + w - pad - 22, y + h - 2);

    return { breakEvenMonth, monthsShown: mShow };
  }

  function addHeaderFooter(doc){
    const pageCount = doc.getNumberOfPages();
    const dateStr = new Date().toLocaleDateString('de-DE');

    for (let i = 1; i <= pageCount; i++) {
      doc.setPage(i);

      // Header
      doc.setFontSize(10);
      doc.text('Robo-Guru | ROI-Berechnung Reinigungsrobotik', 14, 10);
      doc.setFontSize(9);
      doc.text(`Datum: ${dateStr}`, 170, 10);

      // Footer
      doc.setFontSize(8);
      const disclaimer =
        'Hinweis: Vereinfachte Modellrechnung auf Basis Ihrer Angaben. Abweichungen durch Einsatzzeiten, Lohnkosten, Wartung, Energiepreise oder Förderungen möglich.';
      doc.text(disclaimer, 14, 287, { maxWidth: 160 });

      doc.setFontSize(9);
      doc.text(`Seite ${i} / ${pageCount}`, 185, 292);
    }
  }

  function generatePdf(calc){
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ unit: 'mm', format: 'a4' });

    const monthlyNet = calc.net / 12;
    const beMonth = Math.max(1, Math.ceil(calc.invest / monthlyNet));
    const beText = `ab Monat ${beMonth} ist die Investition rechnerisch wieder drin.`;

    doc.setFontSize(18);
    doc.text('ROI-Berechnung – Reinigungsrobotik', 14, 20);
    doc.setFontSize(10);
    doc.text('Beispielrechnung auf Basis der angegebenen Parameter', 14, 26);
    doc.text(`Seite: ${window.location.href}`, 14, 31);

    doc.setFontSize(12);
    doc.text('Zusammenfassung', 14, 40);

    doc.autoTable({
      startY: 44,
      theme: 'grid',
      head: [['Kennzahl', 'Wert']],
      body: [
        ['Investition gesamt', fmtEUR(calc.invest)],
        ['Geschätzte Netto-Ersparnis/Jahr', fmtEUR(calc.net)],
        ['Geschätzte Netto-Ersparnis/Monat', fmtEUR(monthlyNet)],
        ['ROI (vereinfachte Jahresbetrachtung)', `${fmtNum(calc.roi)} %`],
        ['Amortisation (Monate)', `${fmtNum(calc.paybackMonths)} Monate`],
        ['Break-even', beText],
      ],
      styles: { fontSize: 10 },
      headStyles: { fillColor: [0,0,0] },
    });

    const chartY = doc.lastAutoTable.finalY + 10;
    doc.setFontSize(12);
    doc.text('Diagramm', 14, chartY);
    doc.setFontSize(10);
    doc.text('Entwicklung der kumulierten Netto-Ersparnis (Beispielrechnung)', 14, chartY + 6);

    drawRoiChart(doc, 14, chartY + 10, 182, 60, monthlyNet, calc.invest, 36);

    const inputsY = chartY + 78;
    doc.setFontSize(12);
    doc.text('Eingaben', 14, inputsY);

    doc.autoTable({
      startY: inputsY + 4,
      theme: 'grid',
      head: [['Parameter', 'Wert']],
      body: [
        ['Kaufpreis pro Roboter', fmtEUR(calc.price)],
        ['Anzahl Roboter', String(calc.qty)],
        ['Eingesparte Stunden/Tag (pro Roboter)', String(calc.hoursPerDay)],
        ['Lohnkosten pro Stunde', fmtEUR(calc.hourlyRate)],
        ['Arbeitstage pro Jahr', String(calc.daysPerYear)],
        ['Wartung/Service pro Jahr (pro Roboter)', fmtEUR(calc.maintPerYear)],
        ['Stromkosten pro Jahr (pro Roboter)', fmtEUR(calc.powerPerYear)],
      ],
      styles: { fontSize: 10 },
      headStyles: { fillColor: [0,0,0] },
    });

    addHeaderFooter(doc);
    return doc;
  }

  function toCalc(root){
    const get = (key) => Number(q(`[data-rg="${key}"]`, root).value || 0);
    const price = get('price');
    const qty = Math.max(1, get('qty'));
    const hoursPerDay = get('hoursPerDay');
    const hourlyRate = get('hourlyRate');
    const daysPerYear = get('daysPerYear');
    const maintPerYear = get('maintPerYear');
    const powerPerYear = get('powerPerYear');

    const invest = price * qty;
    const grossSavings = hoursPerDay * hourlyRate * daysPerYear * qty;
    const opsCosts = (maintPerYear + powerPerYear) * qty;
    const net = grossSavings - opsCosts;

    const roi = (invest > 0 && net > 0) ? (net / invest) * 100 : null;
    const paybackMonths = (invest > 0 && net > 0) ? (invest / net) * 12 : null;
    const monthlyNet = net / 12;
    const breakEvenMonth = (invest > 0 && net > 0) ? Math.max(1, Math.ceil(invest / monthlyNet)) : null;
    const beText = breakEvenMonth ? `ab Monat ${breakEvenMonth} ist die Investition rechnerisch wieder drin.` : '–';

    return {
      price, qty, hoursPerDay, hourlyRate, daysPerYear, maintPerYear, powerPerYear,
      invest, grossSavings, opsCosts, net, roi, paybackMonths, breakEvenMonth, beText
    };
  }

  function render(root, calc){
    const out = (name) => q(`[data-rg-out="${name}"]`, root);

    out('invest').textContent = fmtEUR(calc.invest);
    out('gross').textContent  = fmtEUR(calc.grossSavings);
    out('ops').textContent    = fmtEUR(calc.opsCosts);
    out('net').textContent    = fmtEUR(calc.net);

    const warnEl = out('warn');
    const hintEl = out('hint');

    const canExport = (calc.invest > 0 && calc.net > 0);

    const pdfBtn = q('[data-rg-btn="pdf"]', root);
    const printBtn = q('[data-rg-btn="print"]', root);
    const mailBtn = q('[data-rg-btn="mail"]', root);

    pdfBtn.disabled = !canExport;
    printBtn.disabled = !canExport;
    mailBtn.disabled = !canExport;

    if (!canExport){
      out('roi').textContent = '–';
      out('payback').textContent = '–';
      out('beText').textContent = '–';
      warnEl.style.display = 'block';
      hintEl.textContent = 'Export ist aktiv, sobald eine positive Netto-Ersparnis berechnet wurde.';
      return { canExport: false };
    }

    warnEl.style.display = 'none';
    out('roi').textContent = fmtNum(calc.roi) + ' %';
    out('payback').textContent = fmtNum(calc.paybackMonths) + ' Monate';
    out('beText').textContent = calc.beText;
    hintEl.textContent = 'Export bereit. Break-even wird im Diagramm markiert.';
    return { canExport: true };
  }

  function bind(root){
    let lastCalc = toCalc(root);
    render(root, lastCalc);

    qa('.rg-in', root).forEach(inp => {
      inp.addEventListener('input', () => {
        lastCalc = toCalc(root);
        render(root, lastCalc);
      });
    });

    q('[data-rg-btn="print"]', root).addEventListener('click', () => {
      lastCalc = toCalc(root);
      if (!(lastCalc.invest > 0 && lastCalc.net > 0)) return;
      window.print();
    });

    q('[data-rg-btn="pdf"]', root).addEventListener('click', () => {
      lastCalc = toCalc(root);
      if (!(lastCalc.invest > 0 && lastCalc.net > 0)) return;
      const doc = generatePdf(lastCalc);
      doc.save(`ROI-Berechnung-Robo-Guru-${new Date().toISOString().slice(0,10)}.pdf`);
    });

    q('[data-rg-btn="mail"]', root).addEventListener('click', async () => {
      lastCalc = toCalc(root);
      if (!(lastCalc.invest > 0 && lastCalc.net > 0)) return;

      const email = (q('[data-rg="email"]', root).value || '').trim();
      if (!email || !email.includes('@')) {
        alert('Bitte eine gültige E-Mail-Adresse eingeben.');
        return;
      }

      const doc = generatePdf(lastCalc);
      const pdfBase64 = doc.output('datauristring'); // data:application/pdf;base64,...

      const ajaxUrl = (window.rgRoi && window.rgRoi.ajaxUrl) ? window.rgRoi.ajaxUrl : '/wp-admin/admin-ajax.php';
      const nonce = (window.rgRoi && window.rgRoi.nonce) ? window.rgRoi.nonce : '';

      const btn = q('[data-rg-btn="mail"]', root);
      const oldText = btn.textContent;
      btn.textContent = 'Sende...';
      btn.disabled = true;

      try {
        const res = await fetch(ajaxUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'rg_send_roi_report',
            nonce,
            email,
            calc: lastCalc,
            pdfBase64
          })
        });
        const json = await res.json();
        const msg = (json && json.data && json.data.message) ? json.data.message :
                    (json && json.message) ? json.message :
                    'Fertig.';
        alert(msg);
      } catch (e) {
        alert('Fehler beim Versand. Bitte später erneut versuchen.');
      } finally {
        btn.textContent = oldText;
        btn.disabled = false;
      }
    });
  }

  document.addEventListener('DOMContentLoaded', () => {
    qa('[data-rg-roi]').forEach(bind);
  });
})();
