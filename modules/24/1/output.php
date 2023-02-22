<?php
$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new D2U_Linkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';
$box_per_line = (int) 'REX_VALUE[3]'; /** @phpstan-ignore-line */
$show_teaser = 'REX_VALUE[4]' === 'true' ? true : false; /** @phpstan-ignore-line */

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof D2U_Linkbox\Category ? $category->name : 'keine'; /** @phpstan-ignore-line */ ?>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?= $box_per_line ?><br>
	Teaser anzeigen: <?php echo $show_teaser ? 'Ja' : 'Nein'; /** @phpstan-ignore-line */ ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof \D2U_Linkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12">';
        echo '<div class="row">';
        $linkboxes = $category->getLinkboxes(true);

        if ('' !== $heading) { /** @phpstan-ignore-line */
            echo '<div class="col-12">';
            echo '<h1>'. $heading .'</h1>';
            echo '</div>';
        }

        foreach ($linkboxes as $linkbox) {
            echo '<div class="col-12 col-sm-6 col-lg-'. (4 === $box_per_line ? '3' : '4') .' linkbox-spacer">'; /** @phpstan-ignore-line */
            echo '<div class="linkbox-outer">';
            echo '<div class="linkbox-mod-1">';
            $url = $linkbox->getUrl();
            if ('' !== $url) {
                $url = '<a href="'. $url .'">';
            }
            echo $url;
            $bg_color = '';
            if ('' !== $linkbox->background_color) {
                $bg_color = ' style="background-color: '. $linkbox->background_color .'"';
            }
            echo '<div class="linkbox-title-lk-mod-1"'. $bg_color .'>'. $linkbox->title .'</div>';
            if ('' !== $linkbox->picture || '' !== $linkbox->picture_lang) {
                echo '<img src="index.php?rex_media_type=d2u_helper_sm&rex_media_file='.
                    ('' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture) .'" alt="'. $linkbox->title .'" loading="lazy">';
            }
            if ('' !== $url) {
                echo '</a>';
            }
            if ($show_teaser && '' !== $linkbox->teaser) {/** @phpstan-ignore-line */
                echo '<div class="linkbox-teaser same-height">'. nl2br($linkbox->teaser) .'</div>';
            }
            echo '</div>'; // class="linkbox"
            echo '</div>'; // class="linkbox-outer"
            echo '</div>'; // class="col...
        }

        echo '</div>';
        echo '</div>';
    }
}
