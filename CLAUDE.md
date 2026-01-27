# Robo-Guru Website

## ROI-Rechner (`wp-content/plugins/rg-roi-calculator/`)

### Aktuelle Version: 1.2.0

**Dateien:**
- `rg-roi-calculator.php` – WordPress Plugin, Shortcode `[rg_roi_calculator]`
- `assets/roi.js` – Berechnungslogik, PDF-Export (jsPDF), Druckfunktion
- `assets/roi.css` – Styling mit CSS-Variablen, responsive Layout

**Features:**
- Kauf- und Leasing-Modus
- Berechnung: Netto-Ersparnis, ROI, Amortisationszeit, Break-even
- Service-Pakete: Basic (99€), Standard (179€), Premium (255€)
- PDF-Download und PDF-Druck (öffnet PDF in neuem Tab)
- E-Mail-Versand wurde bewusst entfernt (Missbrauchsschutz)

**Brand-Farben:**
- Primary Cyan: `#16C6E5`
- Dark: `#0F2537`
- Success Green: `#2FBF71`

### Geplant: Roboter-Vorauswahl

Dropdown mit echten Roboter-Modellen, das Standardwerte vorbefüllt (Preis, Leistung, Stromverbrauch).

**Umsetzung:**
1. Roboter-Daten als JS-Objekt in `roi.js` oder separate `robots.json`
2. Dropdown "Roboter-Modell" als erstes Feld in der Finanzierungs-Karte
3. Bei Auswahl werden Felder automatisch befüllt: Kaufpreis, Leasingrate, Stunden/Tag, Stromkosten, Fläche/h
4. Option "Eigene Angaben" = alle Felder manuell editierbar
5. Werte bleiben nach Vorbefüllung manuell änderbar

**Datenstruktur pro Roboter:**
```json
{
  "id": "nilfisk-liberty-sc50",
  "name": "Nilfisk Liberty SC50",
  "brand": "Nilfisk",
  "price": 18500,
  "leaseRate": 750,
  "serviceBasic": 99,
  "serviceStandard": 179,
  "powerPerYear": 320,
  "sqmPerHour": 1800,
  "hoursPerDay": 3
}
```

**Benötigt:** Liste der Roboter-Modelle mit realen Werten von Sebastian.
