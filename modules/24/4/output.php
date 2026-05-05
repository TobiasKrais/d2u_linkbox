<?php
$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new TobiasKrais\D2ULinkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
    <h1 class="d2u-linkbox-module-preview-title">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof TobiasKrais\D2ULinkbox\Category ? rex_escape($category->name) : 'keine'; /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof TobiasKrais\D2ULinkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 abstand">';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h1 class="heading-lb-mod-3">'. rex_escape($heading) .'</h1>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="row">';
        echo '<div class="col-12">';
        $slider_id = random_int(1, 1000);

        // Slider
        echo '<div id="slider'. $slider_id .'" class="carousel slide carousel-lb-mod-4-outer" data-ride="carousel" data-pause="false">';

        // Slider indicators
        echo '<ol class="carousel-indicators">';
        for ($i = 0; $i < count($linkboxes); ++$i) {
            echo '<li data-target="#slider'. $slider_id .'" data-slide-to="'. $i .'"';
            if (0 === $i) {
                echo 'class="active"';
            }
            echo '></li>';
        }
        echo '</ol>';

        // Wrapper for slides
        echo '<div class="carousel-inner">';
        $slide_is_active = true;
        foreach ($linkboxes as $linkbox) {
            echo '<div class="carousel-item linkbox-mod-4-slider';
            if ($slide_is_active) {
                echo ' active';
                $slide_is_active = false;
            }
            $slide_attributes = [];
            if ('' !== $linkbox->picture || '' !== $linkbox->picture_lang) {
                $slide_attributes[] = ' data-linkbox-bg-image="'. rex_escape(rex_url::media('' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture)) .'"';
            }
            echo '"'. implode('', $slide_attributes) .'>';

            $url = $linkbox->getUrl();
            if ('' !== $url) {
                $url = '<a href="'. rex_escape($url) .'">';
            }
            $box_attributes = [];
            if ('' !== $linkbox->background_color) {
                $box_attributes[] = ' data-linkbox-bg-color="'. rex_escape($linkbox->background_color) .'"';
            }
            if ('' !== $linkbox->background_color_dark) {
                $box_attributes[] = ' data-linkbox-bg-color-dark="'. rex_escape($linkbox->background_color_dark) .'"';
            }
            echo $url;
            echo '<div class="linkbox-mod-4"'. implode('', $box_attributes) .'>';
            echo '<div class="linkbox-title-lb-mod-4">'. rex_escape($linkbox->title) .'</div>';
            if ('' !== $linkbox->teaser) {
                echo '<div class="linkbox-teaser-lb-mod-4">'. $linkbox->teaser .'</div>';
            }
            echo '</div>';
            if ('' !== $url) {
                echo '</a>';
            }

            echo '</div>';
        }
        echo '</div>';

        // Left and right controls
        echo '<button class="carousel-control-prev" type="button" data-target="#slider'. $slider_id .'" data-slide="prev">';
        echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Previous</span>';
        echo '</a>';
        echo '<button class="carousel-control-next" type="button" data-target="#slider'. $slider_id .'" data-slide="next">';
        echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        echo '<span class="sr-only">Next</span>';
        echo '</a>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
