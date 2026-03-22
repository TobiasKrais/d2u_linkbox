<?php
$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new TobiasKrais\D2ULinkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof TobiasKrais\D2ULinkbox\Category ? $category->name : 'keine'; /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof TobiasKrais\D2ULinkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 abstand">';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h1 class="heading-lb-mod-3">'. $heading .'</h1>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="row">';
        echo '<div class="col-12">';
        $slider_id = random_int(1, 1000);

        // Slider
        echo '<div id="slider'. $slider_id .'" class="carousel slide carousel-lb-mod-4-outer" data-bs-ride="carousel" data-bs-pause="false">';

        // Slider indicators
        echo '<div class="carousel-indicators">';
        for ($i = 0; $i < count($linkboxes); ++$i) {
            echo '<button type="button" data-bs-target="#slider'. $slider_id .'" data-bs-slide-to="'. $i .'"';
            if (0 === $i) {
                echo ' class="active" aria-current="true"';
            }
            echo ' aria-label="Slide '. ($i + 1) .'"></button>';
        }
        echo '</div>';

        // Wrapper for slides
        echo '<div class="carousel-inner">';
        $slide_is_active = true;
        foreach ($linkboxes as $linkbox) {
            echo '<div class="carousel-item linkbox-mod-4-slider';
            if ($slide_is_active) {
                echo ' active';
                $slide_is_active = false;
            }
            echo '" style="background-image: url('. rex_url::media('' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture) .')">';

            $url = $linkbox->getUrl();
            if ('' !== $url) {
                $url = '<a href="'. $url .'">';
            }
            $style_vars = [];
            if ('' !== $linkbox->background_color) {
                $style_vars[] = '--linkbox-bg-color: '. $linkbox->background_color;
            }
            if ('' !== $linkbox->background_color_dark) {
                $style_vars[] = '--linkbox-bg-color-dark: '. $linkbox->background_color_dark;
            }
            $box_style = count($style_vars) > 0 ? ' style="'. implode('; ', $style_vars) .';"' : '';
            echo $url;
            echo '<div class="linkbox-mod-4"'. $box_style .'>';
            echo '<div class="linkbox-title-lb-mod-4">'. $linkbox->title .'</div>';
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
        echo '<button class="carousel-control-prev" type="button" data-bs-target="#slider'. $slider_id .'" data-bs-slide="prev">';
        echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
        echo '<span class="visually-hidden">Previous</span>';
        echo '</button>';
        echo '<button class="carousel-control-next" type="button" data-bs-target="#slider'. $slider_id .'" data-bs-slide="next">';
        echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
        echo '<span class="visually-hidden">Next</span>';
        echo '</button>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
