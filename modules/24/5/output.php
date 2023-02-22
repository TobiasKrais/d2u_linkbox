<?php
$cols_sm = 0 === (int) 'REX_VALUE[20]' ? 12 : (int) 'REX_VALUE[20]'; /** @phpstan-ignore-line */
$cols_md = 0 === (int) 'REX_VALUE[19]' ? 12 : (int) 'REX_VALUE[19]'; /** @phpstan-ignore-line */
$cols_lg = 0 === (int) 'REX_VALUE[18]' ? 12 : (int) 'REX_VALUE[18]'; /** @phpstan-ignore-line */
$offset_lg_cols = (int) 'REX_VALUE[17]';
$offset = '';
if ($offset_lg_cols > 0) { /** @phpstan-ignore-line */
    $offset = ' mr-md-auto ml-md-auto ';
}

$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';
$picture_type = 'REX_VALUE[5]' === '' ? 'd2u_helper_sm' : 'REX_VALUE[5]'; /** @phpstan-ignore-line */
$display_type_1_row = 2 !== (int) 'REX_VALUE[3]'; /** @phpstan-ignore-line */

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof D2U_Linkbox\Category ? $category->name : 'keine'; /** @phpstan-ignore-line */ ?><br>
	Darstellungsart: <?php echo $display_type_1_row ? 'Eine Box - ganze Breite' : 'Boxen nebeneinander (abhängig von Bildschirmgröße)'; /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof D2U_Linkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset .' abstand">';
        echo '<div class="row">';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="col-12">';
            echo '<h2>'. $heading .'</h2>';
            echo '</div>';
        }

        $counter = 0;
        foreach ($linkboxes as $linkbox) {
            $cols = '';
            if (!$display_type_1_row) { /** @phpstan-ignore-line */
                if ($cols_lg <= 6) { /** @phpstan-ignore-line */
                    $cols .= ' col-lg-12';
                } elseif ($cols_lg < 12) { /** @phpstan-ignore-line */
                    $cols .= ' col-lg-6';
                } else {
                    $cols .= ' col-lg-6 col-xl-4';
                }

                if ($cols_md < 12) { /** @phpstan-ignore-line */
                    $cols .= ' col-md-12';
                } else {
                    $cols .= ' col-md-6';
                }
            }
            echo '<div class="col-12 linkbox-24-5-spacer linkbox-24-5-wrapper'. $cols .'">';

            $url = $linkbox->getUrl();
            if ('' !== $url) {
                echo '<a href="'. $url .'">';
            }

            if ('' !== $linkbox->picture || '' !== $linkbox->picture_lang) {
                echo '<div class="linkbox-24-5-picture'. ($display_type_1_row ? ($counter % 2 > 0 ? '-right' : '-left') : '') .'">'; /** @phpstan-ignore-line */
                $picture = '' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture;
                $media = rex_media::get($picture);
                $title = $media instanceof rex_media ? $media->getValue('title') : '';
                echo '<img src="'. rex_media_manager::getUrl($picture_type, $picture) .'" alt="'. $title .'" title="'. $title .'" loading="lazy">';
                echo '</div>';
            }

            $bg_color = '';
            if ('' !== $linkbox->background_color) {
                $bg_color = ' style="background-color: '. $linkbox->background_color .'dd"'; // Add "dd" to generate opacity
            }
            echo '<div class="linkbox-24-5-title-box'. ($display_type_1_row ? ($counter % 2 > 0 ? '-left' : '-right') : '') .'"'. $bg_color .'>'; /** @phpstan-ignore-line */
            echo '<h3 class="linkbox-24-5-title">'. $linkbox->title .'</h3>';
            if ('' !== $linkbox->teaser) {
                echo '<p class="linkbox-24-5-teaser">'. nl2br($linkbox->teaser) .'</p>';
            }
            echo '</div>';

            if ('' !== $url) {
                echo '</a>';
            }
            echo '</div>'; // class="col...
            ++$counter;
        }

        echo '</div>';
        echo '</div>';
    }
}
