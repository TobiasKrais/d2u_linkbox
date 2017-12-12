<?php
$ausgabe_art = "REX_VALUE[2]" == "" ? "greenbox" : "REX_VALUE[2]";
$category_id = "REX_VALUE[1]";
$category = new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId());
$heading = "REX_VALUE[3]";

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Ausgabeart: REX_VALUE[2]<br />
	Überschrift: REX_VALUE[3]<br>
	Gewählte Kategorie: <?php print $category->name; ?>
<?php
}
else {
	// Ausgabe im FRONTEND
	$linkboxes = $category->getLinkboxes(TRUE);

	if($heading != "") {
		print '<div class="large-12 columns">';
		print '<h1>'. $heading .'</h1>';
		print '</div>';	
	}
	if($ausgabe_art == "greybox") {
		$css = "";
		print '<div class="large-12 columns">';	
		print '<div class="box-links-with-fading-pictures">';
		print '<div class="produktwahl">';
		$numberOfBoxes = count($linkboxes);
		$counter = 0;
		print '<ul class="hyphens">';
		foreach($linkboxes as $linkbox) {
			print '<li>';
			if($linkbox->article_id > 0) {
				print '<a href="'. rex_getUrl($linkbox->article_id) .'" class="showpic-kat'. $linkbox->box_id .' arrow">';
			}
			print $linkbox->title;
			if($linkbox->article_id > 0) {
				print '</a>';
			}
			print '</li>';
			$counter++;
			if(round($numberOfBoxes / 2) == $counter) {
				print '</ul><ul class="hyphens">';
			}

			// Anpassungen Stylesheets
			if($counter == 1) {
			$css .= '.box-links-with-fading-pictures .produktbild {
					background-image: url("index.php?rex_media_type=355x230&rex_media_file='. $linkbox->picture .'");
				}';
			}
			$css .= '.box-links-with-fading-pictures .produktbild.kat'. $linkbox->box_id
					.' {background-image: url("index.php?rex_media_type=355x230&rex_media_file='. $linkbox->picture .'") !important;}';
		}
		print '</ul>';
		print '</div>';
		print '<div class="produktbild"></div>';
		print '<div class="clear"></div>';
		print '</div>';
		print '</div>';

		print '<style type="text/css">';
		print $css;
		print '</style>';

		print '<script>';
		print '$(function() {';
		foreach($linkboxes as $linkbox) {
			print "$('.showpic-kat". $linkbox->box_id ."').hover(function() {";
			print "$('.produktbild').addClass('kat". $linkbox->box_id ."')";
			print "}, function() {";
			print "$('.produktbild').removeClass('kat". $linkbox->box_id ."')";
			print "});";
		}	
		print '});';
		print '</script>';
	}
	else {
		foreach($linkboxes as $linkbox) {
			print '<div class="large-4 medium-6 small-12 columns end thumb-ident-3el" data-height-watch>';
			print '<div class="holder" data-height-watch>';
			if($linkbox->article_id > 0) {
				print '<a href="'. rex_getUrl($linkbox->article_id) .'">';
			}
			print '<div class="view"><img src="index.php?rex_media_type=379x189&rex_media_file='. $linkbox->picture .'" alt="'. $linkbox->title .'"></div>';
			print '<div class="title" style="color: white">'. $linkbox->title .'</div>';
			if($linkbox->article_id > 0) {
				print '</a>';
			}
			print '</div>';
			print '</div>';
		}
	}
	print '<div class="sp sections-less"></div>';
}
?>