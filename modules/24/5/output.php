<?php
$cols_sm = intval("REX_VALUE[20]") === 0 ? 12 : intval("REX_VALUE[20]"); /** @phpstan-ignore-line */
$cols_md = intval("REX_VALUE[19]") === 0 ? 12 : intval("REX_VALUE[19]"); /** @phpstan-ignore-line */
$cols_lg = intval("REX_VALUE[18]") === 0 ? 12 : intval("REX_VALUE[18]"); /** @phpstan-ignore-line */
$offset_lg_cols = intval("REX_VALUE[17]");
$offset = "";
if($offset_lg_cols > 0) { /** @phpstan-ignore-line */
	$offset = " mr-md-auto ml-md-auto ";
}

$category_id = intval("REX_VALUE[1]") > 0 ? intval("REX_VALUE[1]") : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = "REX_VALUE[2]";
$picture_type = "REX_VALUE[5]" === '' ? 'd2u_helper_sm' : "REX_VALUE[5]"; /** @phpstan-ignore-line */
$display_type_1_row = intval('REX_VALUE[3]') !== 2; /** @phpstan-ignore-line */

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category instanceof D2U_Linkbox\Category ? $category->name : 'keine'); /** @phpstan-ignore-line */ ?><br>
	Darstellungsart: <?php print ($display_type_1_row ? 'Eine Box - ganze Breite' : 'Boxen nebeneinander (abhängig von Bildschirmgröße)'); /** @phpstan-ignore-line */ ?><br>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category instanceof D2U_Linkbox\Category) { /** @phpstan-ignore-line */
		print '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset .' abstand">';
		print '<div class="row">';
		$linkboxes = $category->getLinkboxes(true);

		if($heading !== "") { /** @phpstan-ignore-line */
			print '<div class="col-12">';
			print '<h2>'. $heading .'</h2>';
			print '</div>';	
		}

		$counter = 0;
		foreach($linkboxes as $linkbox) {
			$cols = '';
			if(!$display_type_1_row) { /** @phpstan-ignore-line */
				if($cols_lg <= 6) { /** @phpstan-ignore-line */
					$cols .= ' col-lg-12';				
				}
				else if($cols_lg < 12) { /** @phpstan-ignore-line */
					$cols .= ' col-lg-6';				
				}
				else {
					$cols .= ' col-lg-6 col-xl-4';				
				}

				if($cols_md < 12) { /** @phpstan-ignore-line */
					$cols .= ' col-md-12';				
				}
				else {
					$cols .= ' col-md-6';				
				}
			}
 			print '<div class="col-12 linkbox-24-5-spacer linkbox-24-5-wrapper'. $cols .'">';

			$url = $linkbox->getUrl();
			if($url !== "") {
				print '<a href="'. $url .'">';
			}
			
			if($linkbox->picture !== "" || $linkbox->picture_lang !== "") {
				print '<div class="linkbox-24-5-picture'. ($display_type_1_row ? ($counter % 2 > 0 ? '-right' : '-left') : '') .'">'; /** @phpstan-ignore-line */
				$picture = $linkbox->picture_lang !== "" ? $linkbox->picture_lang : $linkbox->picture;
				$media = rex_media::get($picture);
				$title = $media instanceof rex_media ? $media->getValue('title') : '';
				print '<img src="'. rex_media_manager::getUrl($picture_type, $picture) .'" alt="'. $title .'" title="'. $title .'" loading="lazy">';
				print '</div>';
			}

			$bg_color = "";
			if($linkbox->background_color !== "") {
				$bg_color = ' style="background-color: '. $linkbox->background_color .'dd"'; // Add "dd" to generate opacity
			}
			print '<div class="linkbox-24-5-title-box'. ($display_type_1_row ? ($counter % 2 > 0 ? '-left' : '-right') : '') .'"'. $bg_color .'>'; /** @phpstan-ignore-line */
			print '<h3 class="linkbox-24-5-title">'. $linkbox->title .'</h3>';
			if($linkbox->teaser !== '') {
				print '<p class="linkbox-24-5-teaser">'. nl2br($linkbox->teaser) .'</p>';
			}
			print '</div>';

			if($url !== "") {
				print '</a>';
			}
			print '</div>'; // class="col...
			$counter++;
		}
		
		print '</div>';
		print '</div>';
	}
}