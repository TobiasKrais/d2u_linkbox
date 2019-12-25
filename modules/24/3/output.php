<?php
$cols = "REX_VALUE[20]";
if($cols == "") {
	$cols = 8;
}

$offset_lg_cols = intval("REX_VALUE[17]");
$offset_lg = "";
if($offset_lg_cols > 0) {
	$offset_lg = " mr-lg-auto ml-lg-auto ";
}

$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : FALSE;
$heading = "REX_VALUE[2]";

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category !== FALSE ? $category->name : 'keine'); ?><br>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category !== FALSE) {
		print '<div class="col-12 col-lg-'. $cols . $offset_lg .' abstand">';
		$linkboxes = $category->getLinkboxes(TRUE);

		if($heading != "") {
			print '<div class="row">';
			print '<div class="col-12">';
			print '<h1 class="heading-lb-mod-3">'. $heading .'</h1>';
			print '</div>';	
			print '</div>';	
		}

		print '<div class="row">';
		$pic_orientation = "left";
		foreach($linkboxes as $linkbox) {
			print '<div class="col-12">';
			$url = $linkbox->getUrl();
			if($url != "") {
				$url = '<a href="'. $url .'">';
			}
			print  $url;
			print '<div class="linkbox-mod-3"'. ($linkbox->background_color != '' ? ' style="background-color:'. $linkbox->background_color .'"' : '') .'>';
			print '<div class="row">';

			// Picture
			$picture = '<div class="col-12 col-md-6 picbox-'. $pic_orientation .'-outer">';
			if($linkbox->picture != "" || $linkbox->picture_lang != "") {
				$picture .= '<div class="picbox-'. $pic_orientation .'-inner">';
				$picture .= '<div><img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. ($linkbox->picture_lang != "" ? $linkbox->picture_lang : $linkbox->picture)
					.'"'. ($linkbox->background_color != '' ? ' style="border: 1px solid '. $linkbox->background_color .'"' : '') .'></div>';
				$picture .= '<div class="border-lb-mod-3"'. ($linkbox->background_color != '' ? ' style="border-color:'. $linkbox->background_color .'"' : '') .'></div>';
				$picture .=  '</div>';
			}
			$picture .=  '</div>';

			// Textbox
			$text = '<div class="col-12 col-md-6">';
			$text .= '<div class="linkbox-content-lb-mod-3">';
			$text .= '<div class="linkbox-title-lb-mod-3">'. $linkbox->title .'</div>';
			if($linkbox->teaser != '') {
				$text .= '<div class="linkbox-teaser-lb-mod-3">'. nl2br($linkbox->teaser) .'</div>';
			}
			$text .= '</div>';
			$text .= '</div>';
			
			if($pic_orientation == 'left') {
				print $picture. $text;
				$pic_orientation = "right";
			}
			else {
				print $text . $picture;
				$pic_orientation = "left";
			}

			print '</div>';
			print '</div>';
			if($url != "") {
				print '</a>';
			}
			print '</div>';
		}
		
		print '</div>';
		print '</div>';
	}
}