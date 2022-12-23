<?php
$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false;
$heading = "REX_VALUE[2]";
$box_per_line = "REX_VALUE[3]";
$show_teaser = "REX_VALUE[4]" == 'true' ? true : false;

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category !== false ? $category->name : 'keine'); ?>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?php print $box_per_line; ?><br>
	Teaser anzeigen: <?php print ($show_teaser ? 'Ja' : 'Nein'); ?><br>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category !== false) {
		print '<div class="col-12">';
		print '<div class="row">';
		$linkboxes = $category->getLinkboxes(true);

		if($heading != "") {
			print '<div class="col-12">';
			print '<h1>'. $heading .'</h1>';
			print '</div>';	
		}

		foreach($linkboxes as $linkbox) {
			print '<div class="col-12 col-sm-6 col-lg-'. ($box_per_line == 4 ? '3' : '4') .' linkbox-spacer">';
			print '<div class="linkbox-outer">';
			print '<div class="linkbox">';
			$url = $linkbox->getUrl();
			if($url != "") {
				$url = '<a href="'. $url .'">';
			}
			print  $url;
			print '<div class="linkbox-title-lk-mod-1">'. $linkbox->title .'</div>';
			if($linkbox->picture != "" || $linkbox->picture_lang != "") {
				print '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
					($linkbox->picture_lang != "" ? $linkbox->picture_lang : $linkbox->picture) .'" alt="'. $linkbox->title .'">';
			}
			if($url != "") {
				print '</a>';
			}
			if($show_teaser && $linkbox->teaser != '') {
				print '<div class="linkbox-teaser same-height">'. nl2br($linkbox->teaser) .'</div>';
			}
			print '</div>'; // class="linkbox"
			print '</div>'; // class="linkbox-outer"
			print '</div>'; // class="col...
		}
		
		print '</div>';
		print '</div>';
	}
}