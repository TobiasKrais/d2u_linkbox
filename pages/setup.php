<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2ULinkboxModules::getModules(), "modules/", "d2u_linkbox");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
	$d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Beispielseiten D2U Linkbox Addon</h2>
<ul>
	<li><a href="https://www.kaltenbach.com/de/" target="_blank">
		www.kaltenbach.com</a></li>
	<li><a href="https://www.inotec-gmbh.com/" target="_blank">
		www.inotec-gmbh.com</a></li>
</ul>
<h2>Support</h2>
<p>Fehlermeldungen bitte über das Kontaktformular unter
	<a href="https://www.design-to-use.de" target="_blank">www.design-to-use.de</a> melden.</p>
<h2>Changelog</h2>
<p>1.2.3-DEV:</p>
<ul>
	<li>Bugfix: Fatal Error bei Medienverlinkung in neuer Linkbox::getUrl() Methode.</li>
	<li>Eingabefelder aller Module in Redaxo Style dargestellt.</li>
	<li>Modul 24-2: Standardhintergrundfarbe statt Schwarz ein leichtes Grau. Standardrahmen um Bild entfernt.</li>
</ul>
<p>1.2.2:</p>
<ul>
	<li>Links können jetzt auch externen URLs und direkt zu Maschinen des D2U Machinery Addons und D2U Immo Addons gesetzt werden.</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>Modul 24-2: Überschrift ist jetzt h2.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
</ul>
<p>1.2.1:</p>
<ul>
	<li>Modul 24-2: div mit Klasse "linkbox" jetzt außerhalb des Links.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Farben werden korrekt gespeichert.</li>
	<li>Bugfix: Prioritäten wurden beim Löschen nicht reorganisiert.</li>
	<li>Updatefehler behoben.</li>
</ul>
<p>1.2:</p>
<ul>
	<li>Link zu Dokumenten aus dem Medienpool möglich.</li>
	<li>Modul hinzugefügt: D2U Linkbox - Slider.</li>
	<li>Modul hinzugefügt: D2U Linkbox - Farbboxen mit seitlichem Bild.</li>
	<li>Bugfix: Speichern Linkbox ohne Artikellink schlug fehl.</li>
	<li>Neues Modul hinzugefügt und bei bestehendem Möglichkeit der Einstellung ob 3 oder 4 Boxen auf großen Bildschirmen angezeigt werden sollen.</li>
	<li>Bugfix: Warnung beim Löschen von Bildern die vom Addon verwendet werden entfernt.</li>
	<li>Sortierung der Boxen nach Priorität möglich.</li>
	<li>Bugfix: Speichern von Kategoriename mit einfachem Anführungszeichen führte zu Fehler.</li>
</ul>
<p>1.1:</p>
<ul>
	<li>Endlich ein Bootstrap 4 Modul.</li>
	<li>Bugfix beim Speichern von Kategorien.</li>
	<li>Teaser hinzugefügt.</li>
</ul>
<p>1.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>