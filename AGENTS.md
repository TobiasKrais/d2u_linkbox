# D2U Linkbox - Agent Notes

Nur projektspezifische Regeln, die für KI-Arbeit relevant sind.

## Kernregeln

- Namespace für Addon-Klassen: `TobiasKrais\D2ULinkbox`
- Veralteter Namespace für Rückwärtskompatibilität: `D2U_Linkbox`
- Einrückung: 4 Spaces in PHP-Klassen, Tabs in Moduldateien
- Kommentare nur auf Englisch
- Backend-Labels immer über `rex_i18n::msg()` mit Keys aus `lang/`

## Wichtige Projekthinweise

- Wenn Backend-Translation-Keys hinzugefügt, umbenannt oder entfernt werden, müssen alle Sprachdateien in `lang/` synchron gehalten werden.
- Für `d2u_machinery`-Links die Verfügbarkeit über `\TobiasKrais\D2UHelper\FrontendHelper::isD2UMachineryExtensionActive()` prüfen, nicht über alte Plugin-Checks.
- In BS5-Modulen für Farben bevorzugt `d2u_helper` CSS-Variablen wie `var(--article-color-box)` oder `var(--navi-color-bg)` verwenden. Keine festen Inline-Hintergrundfarben einführen, damit Dark Mode weiter funktioniert.

## Modul-Änderungen

- Wenn ein Modul unter `modules/24/*` geändert wird, Changelog in `pages/help.changelog.php` prüfen oder aktualisieren und die Revisionsnummer in `lib/Module.php` nur einmal pro Release erhöhen.
- Versionshinweise für Module: Wenn die Zielversion im Changelog bereits `-DEV` trägt, innerhalb derselben Entwicklungsphase keine weitere Revisionsnummer für dasselbe Modul hochzählen. Erst mit der nächsten Release-Version wieder erneut erhöhen.
- In Changelog-Dateien, AGENTS.md und README.md echte Umlaute (ä, ö, ü, Ä, Ö, Ü, ß) verwenden und nicht als ae/oe/ue/Ae/Oe/Ue/ss umschreiben.

## Pflege

- Diese Datei kurz und handlungsorientiert halten.
- Neue Einträge nur aufnehmen, wenn sie wiederkehrende Stolperfallen, verbindliche Projektkonventionen oder agentenrelevante Workflows betreffen.
