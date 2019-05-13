<?php
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
		print '<div class="col-12 abstand">';
		$linkboxes = $category->getLinkboxes(TRUE);

		if($heading != "") {
			print '<div class="row">';
			print '<div class="col-12">';
			print '<h1 class="heading-lb-mod-3">'. $heading .'</h1>';
			print '</div>';	
			print '</div>';	
		}

		print '<div class="row">';
		print '<div class="col-12">';
		$slider_id = rand(1, 1000);
		
		// Slider
		print '<div id="'. $slider_id .'" class="carousel slide carousel-lb-mod-4-outer" data-pause="false">';

		// Slider indicators
		print '<ol class="carousel-indicators">';
		for($i = 0; $i < count($linkboxes); $i++) {
			print '<li data-target="#'. $slider_id .'" data-slide-to="'. $i .'"';
			if($i == 0) {
				print 'class="active"';
			}
			print '></li>';
		}
		print '</ol>';

		// Wrapper for slides
		print '<div class="carousel-inner">';
		$slide_is_active = TRUE;
		foreach($linkboxes as $linkbox) {
			print '<div class="carousel-item linkbox-mod-4-slider';
			if($slide_is_active) {
				print ' active';
				$slide_is_active = FALSE;
			}
			print '" style="background-image: url('. rex_url::media($linkbox->picture) .')">';
			
			$url = $linkbox->getUrl();
			if($url != "") {
				$url = '<a href="'. $url .'" target="_blank">';
			}
			print  $url;
			print '<div class="linkbox-mod-4"'. ($linkbox->background_color != '' ? ' style="background-color:'. $linkbox->background_color .'"' : '') .'>';
			print '<div class="linkbox-title-lb-mod-4">'. $linkbox->title .'</div>';
			if($linkbox->teaser != '') {
				print '<div class="linkbox-teaser-lb-mod-4">'. nl2br($linkbox->teaser) .'</div>';
			}
			print '</div>';
			if($url != "") {
				print '</a>';
			}
			
			print '</div>';
		}
		print '</div>';

		// Left and right controls
		print '<a class="carousel-control-prev" href="#'. $slider_id .'" role="button" data-slide="prev">';
		print '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
		print '<span class="sr-only">Previous</span>';
		print '</a>';
		print '<a class="carousel-control-next" href="#'. $slider_id .'" role="button" data-slide="next">';
		print '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
		print '<span class="sr-only">Next</span>';
		print '</a>';
		print '</div>';
		
		print '</div>';
		print '</div>';
		print '</div>';
	}
}