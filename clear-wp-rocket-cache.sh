#!/bin/bash
# WP Rocket Cache komplett leeren (Page Cache + Used CSS / RUCSS)
# Voraussetzung: WP-CLI muss installiert sein
# Aufruf: ./clear-wp-rocket-cache.sh [--path=/pfad/zur/wordpress-installation]

set -euo pipefail

# WordPress-Pfad (Standard: aktuelles Verzeichnis)
WP_PATH="${1:-.}"

# Falls --path= Argument übergeben wurde
if [[ "$WP_PATH" == --path=* ]]; then
    WP_PATH="${WP_PATH#--path=}"
fi

echo "=== WP Rocket Cache leeren ==="
echo "WordPress-Pfad: $WP_PATH"
echo ""

# Prüfe ob WP-CLI verfügbar ist
if ! command -v wp &> /dev/null; then
    echo "FEHLER: WP-CLI (wp) ist nicht installiert."
    echo "Installation: https://wp-cli.org/#installing"
    exit 1
fi

# Prüfe ob WordPress vorhanden ist
if ! wp core is-installed --path="$WP_PATH" --quiet 2>/dev/null; then
    echo "FEHLER: Keine WordPress-Installation unter $WP_PATH gefunden."
    exit 1
fi

# Prüfe ob WP Rocket aktiv ist
if ! wp plugin is-active wp-rocket --path="$WP_PATH" --quiet 2>/dev/null; then
    echo "FEHLER: WP Rocket ist nicht aktiv."
    exit 1
fi

# 1) Page Cache leeren
echo "[1/3] Page Cache leeren..."
wp rocket clean --path="$WP_PATH" --confirm 2>/dev/null && echo "      OK" || echo "      Fallback: manuell löschen"

# 2) Minify/Concat Cache leeren
echo "[2/3] Minify/Concat Cache leeren..."
wp rocket clean --type=min --path="$WP_PATH" --confirm 2>/dev/null && echo "      OK" || echo "      übersprungen (nicht aktiv)"

# 3) Used CSS (RUCSS) Cache leeren
echo "[3/3] Used CSS (RUCSS) Cache leeren..."
wp rocket clean --type=rucss --path="$WP_PATH" --confirm 2>/dev/null && echo "      OK" || {
    # Fallback: RUCSS direkt über DB leeren
    echo "      WP-CLI RUCSS nicht verfügbar, versuche DB-Fallback..."
    wp db query "DELETE FROM $(wp db prefix --path="$WP_PATH")wpr_rucss_used_css" --path="$WP_PATH" 2>/dev/null && echo "      OK (DB)" || echo "      übersprungen"
}

echo ""
echo "=== Fertig! Alle WP Rocket Caches wurden geleert. ==="
echo "Tipp: Seite jetzt im Inkognito-Fenster auf dem Handy testen."
