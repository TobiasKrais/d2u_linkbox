<?php
$cols = 0 === (int) 'REX_VALUE[20]' ? 12 : (int) 'REX_VALUE[20]'; /** @phpstan-ignore-line */

$offset_lg_cols = (int) 'REX_VALUE[17]';
$offset_lg = '';
if ($offset_lg_cols > 0) { /** @phpstan-ignore-line */
    $offset_lg = ' mr-lg-auto ml-lg-auto ';
}

$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new TobiasKrais\D2ULinkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof TobiasKrais\D2ULinkbox\Category ? $category->name : 'keine';  /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof TobiasKrais\D2ULinkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 col-lg-'. $cols . $offset_lg .' abstand">';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo '<h1 class="heading-lb-mod-3">'. $heading .'</h1>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="row">';
        $pic_orientation = 'left';
        foreach ($linkboxes as $linkbox) {
            echo '<div class="col-12">';
            $url = $linkbox->getUrl();
            if ('' !== $url) {
                $url = '<a href="'. $url .'">';
            }
            echo $url;
            echo '<div class="linkbox-mod-3"'. ('' !== $linkbox->background_color ? ' style="background-color:'. $linkbox->background_color .'"' : '') .'>';
            echo '<div class="row">';

            // Picture
            $picture = '<div class="col-12 col-md-6 picbox-'. $pic_orientation .'-outer">';
            if ('' !== $linkbox->picture || '' !== $linkbox->picture_lang) {
                $picture .= '<div class="picbox-'. $pic_orientation .'-inner">';
                $picture .= '<div><img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='. ('' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture)
                    .'"'. ('' !== $linkbox->background_color ? ' style="border: 1px solid '. $linkbox->background_color .'"' : '') .' loading="lazy"></div>';
                $picture .= '<div class="border-lb-mod-3"'. ('' !== $linkbox->background_color ? ' style="border-color:'. $linkbox->background_color .'"' : '') .'></div>';
                $picture .= '</div>';
            }
            $picture .= '</div>';

            // Textbox
            $text = '<div class="col-12 col-md-6">';
            $text .= '<div class="linkbox-content-lb-mod-3">';
            $text .= '<div class="linkbox-title-lb-mod-3">'. $linkbox->title .'</div>';
            if ('' !== $linkbox->teaser) {
                $text .= '<div class="linkbox-teaser-lb-mod-3">'. $linkbox->teaser .'</div>';
            }
            $text .= '</div>';
            $text .= '</div>';

            if ('left' === $pic_orientation) {
                echo $picture. $text;
                $pic_orientation = 'right';
            } else {
                echo $text . $picture;
                $pic_orientation = 'left';
            }

            echo '</div>';
            echo '</div>';
            if ('' !== $url) {
                echo '</a>';
            }
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }
}
