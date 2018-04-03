<?php
$category_id = "REX_VALUE[1]" > 0 ? "REX_VALUE[1]" : 0;
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : FALSE;
$heading = "REX_VALUE[2]";

if(rex::isBackend()) {
	// Ausgabe im BACKEND	
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php print ($category !== FALSE ? $category->name : 'keine'); ?>
<?php
}
else {
	// Ausgabe im FRONTEND
	if($category !== FALSE) {
		print '<div class="col-12">';
		print '<div class="row">';
		$linkboxes = $category->getLinkboxes(TRUE);

		if($heading != "") {
			print '<div class="col-12">';
			print '<h1>'. $heading .'</h1>';
			print '</div>';	
		}

		foreach($linkboxes as $linkbox) {
			print '<div class="col-12 col-sm-6 col-lg-4">';
			if($linkbox->article_id > 0) {
				print '<a href="'. rex_getUrl($linkbox->article_id) .'">';
			}
			print '<div class="linkbox">';
			print '<div class="linkbox-title">'. $linkbox->title .'</div>';
			if($linkbox->picture != "") {
				print '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. $linkbox->picture .'">';
			}
			if($linkbox->article_id > 0) {
				print '</a>';
			}
			if($linkbox->teaser != '') {
				print '<div class="linkbox-teaser">'. $linkbox->teaser .'</div>';
			}
			print '</div>'; // class="linkbox"
			print '</div>'; // class="col...
		}
		
		print '</div>';
		print '</div>';
	}
}
?>