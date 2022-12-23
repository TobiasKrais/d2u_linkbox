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
$offset = "";
if($offset_lg_cols > 0) {
	$offset = " mr-md-auto ml-md-auto ";
}

$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false;
$heading = "REX_VALUE[2]";
$picture_type = "REX_VALUE[5]" == '' ? 'd2u_helper_sm' : "REX_VALUE[5]";
$display_type_1_row = intval('REX_VALUE[3]') !== 2;

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category !== false ? $category->name : 'keine'); ?><br>
	Darstellungsart: <?php print ($display_type_1_row ? 'Eine Box - ganze Breite' : 'Boxen nebeneinander (abhängig von Bildschirmgröße)'); ?><br>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category !== false) {
		print '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset .' abstand">';
		print '<div class="row">';
		$linkboxes = $category->getLinkboxes(true);

		if($heading != "") {
			print '<div class="col-12">';
			print '<h2>'. $heading .'</h2>';
			print '</div>';	
		}

		$counter = 0;
		foreach($linkboxes as $linkbox) {
			$cols = '';
			if(!$display_type_1_row) {
				if($cols_lg <= 6) {
					$cols .= ' col-lg-12';				
				}
				else if($cols_lg < 12) {
					$cols .= ' col-lg-6';				
				}
				else {
					$cols .= ' col-lg-6 col-xl-4';				
				}

				if($cols_md < 12) {
					$cols .= ' col-md-12';				
				}
				else {
					$cols .= ' col-md-6';				
				}
			}
 			print '<div class="col-12 linkbox-24-5-spacer linkbox-24-5-wrapper'. $cols .'">';

			$url = $linkbox->getUrl();
			if($url != "") {
				print '<a href="'. $url .'">';
			}
			
			if($linkbox->picture != "" || $linkbox->picture_lang != "") {
				print '<div class="linkbox-24-5-picture'. ($display_type_1_row ? ($counter % 2 > 0 ? '-right' : '-left') : '') .'">';
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
				print '</div>';
			}

			$bg_color = "";
			if($linkbox->background_color != "") {
				$bg_color = ' style="background-color: '. $linkbox->background_color .'dd"'; // Add "dd" to generate opacity
			}
			print '<div class="linkbox-24-5-title-box'. ($display_type_1_row ? ($counter % 2 > 0 ? '-left' : '-right') : '') .'"'. $bg_color .'>';
			print '<h3 class="linkbox-24-5-title">'. $linkbox->title .'</h3>';
			if($linkbox->teaser != '') {
				print '<p class="linkbox-24-5-teaser">'. nl2br($linkbox->teaser) .'</p>';
			}
			print '</div>';

			if($url != "") {
				print '</a>';
			}
			print '</div>'; // class="col...
			$counter++;
		}
		
		print '</div>';
		print '</div>';
	}
}