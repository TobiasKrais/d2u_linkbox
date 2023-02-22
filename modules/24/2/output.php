<?php
$cols_sm = 0 === (int) 'REX_VALUE[20]' ? 12 : (int) 'REX_VALUE[20]'; /** @phpstan-ignore-line */
$cols_md = 0 === (int) 'REX_VALUE[19]' ? 12 : (int) 'REX_VALUE[19]'; /** @phpstan-ignore-line */
$cols_lg = 0 === (int) 'REX_VALUE[18]' ? 12 : (int) 'REX_VALUE[18]'; /** @phpstan-ignore-line */
$offset_lg = (int) 'REX_VALUE[17]' > 0 ? ' mr-lg-auto ml-lg-auto ' : ''; /** @phpstan-ignore-line */

$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';
$box_per_line = (int) 'REX_VALUE[3]';
$show_teaser = 'REX_VALUE[4]' === 'true' ? true : false; /** @phpstan-ignore-line */
$picture_only = 'REX_VALUE[6]' === 'true' ? true : false; /** @phpstan-ignore-line */
$picture_type = 'REX_VALUE[5]' === '' ? 'd2u_helper_sm' : 'REX_VALUE[5]'; /** @phpstan-ignore-line */

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof D2U_Linkbox\Category ? $category->name : 'keine'; /** @phpstan-ignore-line */ ?><br>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?= $box_per_line ?><br>
	Teaser anzeigen: <?php echo $show_teaser ? 'Ja' : 'Nein'; /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof D2U_Linkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset_lg .'" abstand>';
        echo '<div class="row" data-match-height>';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="col-12">';
            echo '<h1>'. $heading .'</h1>';
            echo '</div>';
        }

        foreach ($linkboxes as $linkbox) {
            echo '<div class="col-'. ($show_teaser ? '12' : '6') .' col-sm-6 ' /** @phpstan-ignore-line */
                . ($box_per_line > 2 ? 'col-md-4 col-lg-'. (4 === $box_per_line ? '3' : '4') : '') /** @phpstan-ignore-line */
                .' linkbox-spacer">';

            $bg_color = '';
            if ('' !== $linkbox->background_color) {
                $bg_color = ' style="background-color: '. $linkbox->background_color .'"';
            }
            echo '<div class="linkbox"'. $bg_color .' data-height-watch>';

            $url = $linkbox->getUrl();
            if ('' !== $url) {
                echo '<a href="'. $url .'">';
            }

            echo '<div class="linkbox-inner">';
            if ('' !== $linkbox->picture || '' !== $linkbox->picture_lang) {
                $picture = '' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture;
                $media = rex_media::get($picture);
                $title = $media instanceof rex_media ? $media->getValue('title') : '';
                echo '<img src="'. rex_media_manager::getUrl($picture_type, $picture) .'" alt="'. $title .'" title="'. $title .'" loading="lazy">';
            }
            if (!$picture_only) { /** @phpstan-ignore-line */
                echo '<div class="linkbox-title"><h2>'. $linkbox->title .'</h2></div>';
                if ($show_teaser && '' !== $linkbox->teaser) { /** @phpstan-ignore-line */
                    echo '<div class="linkbox-teaser">'. nl2br($linkbox->teaser) .'</div>';
                }
            }
            if ('' !== $url) {
                echo '</a>';
            }
            echo '</div>'; // class="linkbox-inner"
            echo '</div>'; // class="linkbox"
            echo '</div>'; // class="col...
        }

        echo '</div>';
        echo '</div>';
    }
}
