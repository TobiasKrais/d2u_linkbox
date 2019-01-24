<?php
$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : FALSE;
$heading = "REX_VALUE[2]";
$box_per_line = "REX_VALUE[3]";
$show_teaser = "REX_VALUE[4]" == 'true' ? TRUE : FALSE;

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category !== FALSE ? $category->name : 'keine'); ?><br>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?php print $box_per_line; ?><br>
	Teaser anzeigen: <?php print ($show_teaser ? 'Ja' : 'Nein'); ?><br>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category !== FALSE) {
		print '<div class="col-12 abstand">';
		print '<div class="row" data-match-height>';
		$linkboxes = $category->getLinkboxes(TRUE);

		if($heading != "") {
			print '<div class="col-12">';
			print '<h1>'. $heading .'</h1>';
			print '</div>';	
		}

		foreach($linkboxes as $linkbox) {
			print '<div class="col-12 col-sm-6 col-md-4 col-lg-'. ($box_per_line == 4 ? '3' : '4') .' linkbox-spacer">';

			$bg_color = "";
			if($linkbox->background_color != "") {
				$bg_color = ' style="background-color: '. $linkbox->background_color .'"';
			}
			print '<div class="linkbox"'. $bg_color .'  data-height-watch>';

			$url = "";
			if($linkbox->link_type == "document" && $linkbox->document != "") {
				$url = '<a href="'. rex_url::media($linkbox->document) .'" target="_blank">';
			}
			else if($linkbox->article_id > 0) {
				$url = '<a href="'. rex_getUrl($linkbox->article_id) .'">';
			}
			print  $url;
			
			print '<div class="linkbox-inner">';
			if($linkbox->picture != "") {
				print '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. $linkbox->picture .'">';
			}
			print '<div class="linkbox-title">'. $linkbox->title .'</div>';
			if($show_teaser && $linkbox->teaser != '') {
				print '<div class="linkbox-teaser">'. nl2br($linkbox->teaser) .'</div>';
			}
			if($url != "") {
				print '</div></a>';
			}
			print '</div>'; // class="linkbox"
			print '</div>'; // class="col...
		}
		
		print '</div>';
		print '</div>';
	}
}
?>