<h2>Changelog</h2>
<p>1.6.2-DEV:</p>
<ul>
	<li>Backend/Frontend: Linkboxen haben jetzt ein optionales eigenes Piktogramm für den Dark Mode; die Piktogramm-Ausgabe schaltet im Frontend passend zum Theme um.</li>
	<li>Backend: Die Modul-Inputs 24-1 bis 24-12 sind jetzt frei von Inline-CSS und Inline-JS und nutzen gemeinsame Backend-Assets fuer Formularlogik und Vorschau.</li>
	<li>Backend: Der Medienpool-Löschschutz berücksichtigt jetzt auch die in Linkboxen hinterlegten Piktogramme und Dark-Mode-Piktogramme.</li>
	<li>Modul 24-8 (BS5): Nutzt jetzt vorhandene Piktogramme in der Ausgabe und fällt nur ohne Piktogramm auf das Bild zurück.</li>
	<li>Backend: Einstellungen und Setup sind im Menü jetzt wie bei d2u_immo unter einem gemeinsamen rechten System-Menü gebündelt.</li>
	<li>Backend: Das neue verschachtelte System-Menü löst die Seiten Einstellungen und Setup jetzt wieder korrekt über eigene Wrapper-Dateien auf.</li>
	<li>Backend: CSRF-Schutz fuer Speichern-, Loesch-, Status- und Prioritaetsaktionen der Linkboxverwaltung ergaenzt.</li>
	<li>Backend: CSRF-Schutz fuer Modul-Installation, -Update und -Deinstallation auf der Setup-Seite ergaenzt.</li>
	<li>Sicherheit: Hex-Farben (Linkbox-Hintergrundfarbe Light/Dark) werden vor dem Speichern strikt validiert (#RGB / #RRGGBB / #RRGGBBAA), damit keine CSS-Werte ueber das Backend eingeschleust werden koennen.</li>
	<li>Bugfix: Category::getLinkboxes(...) garantiert jetzt zentral einen Array-Rückgabewert, damit Projektmodule mit foreach/shuffle bei leeren Kategorien nicht auf <code>null</code> laufen.</li>        <li>Security: Die <code>media-is-in-use</code>-Extension-Points in <code>boot.php</code> verwenden jetzt gebundene Parameter statt SQL-String-Konkatenation mit <code>addslashes()</code>.</li>
    <li>Security: Die <code>save()</code>-Methoden in <code>lib/Category.php</code> und <code>lib/Linkbox.php</code> verwenden jetzt gebundene Parameter statt SQL-String-Konkatenation mit <code>addslashes()</code>.</li>
    <li>Security: Modul-Ausgaben (<code>modules/24/1-12/output.php</code>) härten Backend-Eingaben gegen XSS via <code>rex_escape()</code> für Linkbox-Titel, Überschriften, Kategoriename, Bildtitel und URLs in <code>href</code>-Attributen. <code>install.php</code>-Migration verwendet gebundene Parameter beim <code>UPDATE</code> von Linkbox-Teasern.</li>
</ul>
<p>1.6.1:</p>
<ul>
	<li>Niederländische Backend-Sprachdatei hinzugefügt.</li>
	<li>Bugfix: Prioritäten werden bei Linkboxen nach dem Speichern wieder stabil neu durchnummeriert, auch wenn in der Datenbank bereits doppelte Werte vorhanden sind.</li>
	<li>Backend-Listen sortierbar gemacht und Standardsortierungen von SQL-Queries auf <code>rex_list</code>-<code>defaultSort</code> umgestellt.</li>
	<li>Die Priorität von Linkboxen kann in der Backend-Liste jetzt direkt per Hoch-/Runter-Buttons geändert werden.</li>
</ul>
<p>1.6.0:</p>
<ul>
	<li>Neue Module 24-7 bis 24-12 als Bootstrap-5-Varianten der bestehenden Beispielmodule hinzugefügt.</li>
	<li>Module 24-1 bis 24-6 als "(BS4, deprecated)" markiert. Die BS4-Varianten werden im nächsten Major Release entfernt.</li>
	<li>Linkboxen haben nun ein eigenes Light-/Dark-Mode Farbfeld im Backend. BS5-Module verwenden im Dark Mode den individuellen dunklen Linkbox-Farbwert mit Fallback auf den hellen Linkbox-Farbwert.</li>
	<li>Bootstrap-5-Module nutzen die d2u_helper CSS-Farbvariablen jetzt auch als Fallback für Linkbox-Hintergründe, damit Light- und Dark-Mode-Farben aus den d2u_helper Einstellungen im Frontend greifen.</li>
	<li>Benötigt d2u_helper &gt;= 2.1.0.</li>
	<li>Funktion Linkbox::getUrl(...) bekommt neuen Parameter, der erlaubt offline URLs zu ignorieren.</li>
	<li>Modul '24-2 D2U Linkbox - Linkboxen mit Überschrift unter Bild': Link korrekt abgeschlossen.</li>
	<li>Modul '24-6 D2U Linkbox - Linkboxen mit Text und Hoverbild hinzugefügt.</li>
</ul>
<p>1.5.0:</p>
<ul>
	<li>Vorbereitung auf R6: Folgende Klassen wurden umbenannt. Die alten Klassennamen funktionieren weiterhin, sind aber als veraltet markiert:
		<ul>
			<li><code>D2U_Linkbox\Category</code> wird zu <code>TobiasKrais\D2ULinkbox\Category</code>.</li>
			<li><code>D2U_Linkbox\Linkbox</code> wird zu <code>TobiasKrais\D2ULinkbox\Linkbox</code>.</li>
		</ul>
		Folgende interne Klasse wurden wurden ebenfalls umbenannt. Es gibt keinen Übergang, da diese Klasse nur intern verwendet wird:
		<ul>
			<li><code>D2ULinkboxModules</code> wird zu <code>TobiasKrais\D2ULinkbox\Module</code>.</li>
		</ul>
	</li>
	<li>Modul 24-2 "D2U Linkbox - Linkboxen mit Überschrift unter Bild": Jetzt auch 6 Boxen pro Zeile möglich. Externe Links öffnen in einem neuen Tab</li>
	<li>Modul 24-5 "D2U Linkbox - Linkboxen mit Text neben dem Bild": Teaser in div geändert um mit Texteditor kompatibel zu sein.</li>
</ul>
<p>1.4.0:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>Feld Teaser kann nun mit einem WYSIWYG Editor bearbeitet werden. Alle Beispielmodule wurden angepasst.</li>
	<li>Modul '24-2 D2U Linkbox - Linkboxen mit Überschrift unter Bild': Es kann nun auch nur eine Linkbox nebeneinander ausgewählt werden.</li>
</ul>
<p>1.3:</p>
<ul>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
	<li>Ca. 300 rexstan Verbesserungen.</li>
	<li>Modul '24-2 D2U Linkbox - Linkboxen mit Überschrift unter Bild': auf kleinen Bildschirmen werden jetzt 2 Boxen nebeneinander angezeigt, wenn kein Teasertext angezeigt werden soll.</li>
	<li>Modul '24-5 D2U Linkbox - Linkboxen mit Text neben dem Bild': Tippfehler behoben.</li>
	<li>Bugfix: 2 Felder waren beim Speichern einer Linkbox nicht korrekt escaped.</li>
	<li>Bugfix: Prioritäten der Boxen wurde nicht immer korrekt gesetzt.</li>
</ul>
<p>1.2.5:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Bugfix: Link beim Löschen von Artikeln war falsch gesetzt.</li>
	<li>Bugfix: Beim Löschen von Bildern konnte es zu einem Fehler kommen.</li>
	<li>Bugfix: Beim Löschen von Artikeln und Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
	<li>Modul 24-1 und 24-2: bessere Darstellung von Checkboxen im Backend.</li>
	<li>Modul 24-2: Optional kann nun auch nur das Bild ohne Text ausgegeben werden.</li>
	<li>Neues Modul 24-5: "D2U Linkbox - Linkboxen mit Text neben dem Bild".</li>
</ul>
<p>1.2.4:</p>
<ul>
	<li>In der Linkbox Liste werden jetzt die Namen der Kategorien angezeigt.</li>
	<li>Es können nun auch Links zu Veranstaltungskategorien aus dem D2U Veranstaltungen Addon gesetzt werden.</li>
	<li>Modul 24-2: Breite des gesamten Modul Outputs kann nun eingestellt werden.</li>
	<li>Modul 24-3: Für große Bildschirme kann nun die Breite eingestellt werden.</li>
</ul>
<p>1.2.3:</p>
<ul>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
	<li>Bugfix: Fatal Error wenn D2U Maschinen Addon ohne aktiviertem industry_sectors Plugin installiert war.</li>
	<li>Bugfix: Fatal Error bei Medienverlinkung in neuer Linkbox::getUrl() Methode.</li>
	<li>Eingabefelder aller Module in Redaxo Style dargestellt.</li>
	<li>Eingabe sprachspezifischer Bilder, Dokumente und externe URLs für jede Linkbox möglich.</li>
	<li>Alle Module: Links öffnen nicht mehr in neuem Fenster.</li>
	<li>Modul 24-2: Standardhintergrundfarbe statt Schwarz ein leichtes Grau. Standardrahmen um Bild entfernt. Auch nur 2 Boxen pro Reihe möglich.</li>
	<li>Modul 24-2: Media Manager Typ ist jetzt auswählbar.</li>
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