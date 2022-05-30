<?php
$cols_sm = "REX_VALUE[20]";
if($cols_sm == "") {
	$cols_sm = 12;
}
$cols_md = "REX_VALUE[19]";
if($cols_md == "") {
	$cols_md = 12;
}
$cols_lg = "REX_VALUE[18]";
if($cols_lg == "") {
	$cols_lg = 12;
}
$offset_lg_cols = intval("REX_VALUE[17]");
$offset_lg = "";
if($offset_lg_cols > 0) {
	$offset_lg = " mr-lg-auto ml-lg-auto ";
}

$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : FALSE;
$heading = "REX_VALUE[2]";
$box_per_line = "REX_VALUE[3]";
$show_teaser = "REX_VALUE[4]" == 'true' ? TRUE : FALSE;
$picture_only = "REX_VALUE[6]" == 'true' ? TRUE : FALSE;
$picture_type = "REX_VALUE[5]" == '' ? 'd2u_helper_sm' : "REX_VALUE[5]";

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
		print '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset_lg .'" abstand>';
		print '<div class="row" data-match-height>';
		$linkboxes = $category->getLinkboxes(TRUE);

		if($heading != "") {
			print '<div class="col-12">';
			print '<h1>'. $heading .'</h1>';
			print '</div>';	
		}

		foreach($linkboxes as $linkbox) {
			print '<div class="col-'. ($show_teaser ? '12' : '6') .' col-sm-6 '
				. ($box_per_line > 2 ? 'col-md-4 col-lg-'. ($box_per_line == 4 ? '3' : '4') : '')
				.' linkbox-spacer">';

			$bg_color = "";
			if($linkbox->background_color != "") {
				$bg_color = ' style="background-color: '. $linkbox->background_color .'"';
			}
			print '<div class="linkbox"'. $bg_color .' data-height-watch>';

			$url = $linkbox->getUrl();
			if($url != "") {
				print '<a href="'. $url .'">';
			}
			
			print '<div class="linkbox-inner">';
			if($linkbox->picture != "" || $linkbox->picture_lang != "") {
				$picture = $linkbox->picture_lang != "" ? $linkbox->picture_lang : $linkbox->picture;
				$media = rex_media::get($picture);
				$html_picture = '<img src="';
				if($picture_type == "") {
					$html_picture .= rex_url::media($picture);
				}
				else {
					$html_picture .= 'index.php?rex_media_type='. $picture_type .'&rex_media_file='. $picture;
				}
				$html_picture .= '" alt="'. $media->getValue('title') .'" title="'. $media->getValue('title') .'">';
				print $html_picture;
			}
			if(!$picture_only) {
				print '<div class="linkbox-title"><h2>'. $linkbox->title .'</h2></div>';
				if($show_teaser && $linkbox->teaser != '') {
					print '<div class="linkbox-teaser">'. nl2br($linkbox->teaser) .'</div>';
				}
			}
			if($url != "") {
				print '</a>';
			}
			print '</div>'; // class="linkbox-inner"
			print '</div>'; // class="linkbox"
			print '</div>'; // class="col...
		}
		
		print '</div>';
		print '</div>';
	}
}