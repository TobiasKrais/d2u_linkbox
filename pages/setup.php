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
<p>1.2-DEV:</p>
<ul>
	<li>Link zu Dokumenten aus dem Medienpool möglich.</li>
	<li>Modul hinzugefügt: D2U Linkbox - Slider.</li>
	<li>Modul hinzugefügt: D2U Linkbox - Farbboxen mit seitlichem Bild.</li>
	<li>Bugfix: Speichern Linkbox ohne Artikellink schlug fehl.</li>
	<li>Neues Modul hinzugefügt und bei bestehendem Möglichkeit der Einstellung ob 3 oder 4 Boxen auf großen Bildschirmen angezeigt werden sollen.</li>
	<li>Bugfix: Warnung beim löschen von Bildern die vom Addon verwendet werden entfernt.</li>
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