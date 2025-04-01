<?php
$cols_sm = 0 === (int) 'REX_VALUE[20]' ? 12 : (int) 'REX_VALUE[20]'; /** @phpstan-ignore-line */
$cols_md = 0 === (int) 'REX_VALUE[19]' ? 12 : (int) 'REX_VALUE[19]'; /** @phpstan-ignore-line */
$cols_lg = 0 === (int) 'REX_VALUE[18]' ? 12 : (int) 'REX_VALUE[18]'; /** @phpstan-ignore-line */
$offset_lg = (int) 'REX_VALUE[17]' > 0 ? ' mr-lg-auto ml-lg-auto ' : ''; /** @phpstan-ignore-line */

$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new TobiasKrais\D2ULinkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';
$box_per_line = (int) 'REX_VALUE[3]';
$picture_type = 'REX_VALUE[5]' === '' ? 'd2u_helper_sm' : 'REX_VALUE[5]'; /** @phpstan-ignore-line */

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof TobiasKrais\D2ULinkbox\Category ? $category->name : 'keine'; /** @phpstan-ignore-line */ ?><br>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?= $box_per_line ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof TobiasKrais\D2ULinkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset_lg .'" abstand>';
            echo '<div class="row">'; /** @phpstan-ignore-line */
            $linkboxes = $category->getLinkboxes(true);

            if ('' !== $heading) { /** @phpstan-ignore-line */
                echo '<div class="col-12">';
                echo '<h1>'. $heading .'</h1>';
                echo '</div>';
            }

            foreach ($linkboxes as $linkbox) {
                if (1 === $box_per_line) { /** @phpstan-ignore-line */
                    echo '<div class="col-12 linkbox-spacer">';
                }
                else if (6 === $box_per_line) { /** @phpstan-ignore-line */
                    echo '<div class="col-6 col-md-4 col-lg-2 linkbox-spacer">';
                }
                else {
                    echo '<div class="col-12 col-sm-6 ' /** @phpstan-ignore-line */
                        . ($box_per_line > 2 ? 'col-md-4 col-lg-'. (4 === $box_per_line ? '3' : '4') : '') /** @phpstan-ignore-line */
                        .' linkbox-spacer">';
                }
            
                    $bg_color = '';
                    if ('' !== $linkbox->background_color) {
                        $bg_color = ' style="background-color: '. $linkbox->background_color .'"';
                    }
                    echo '<div class="linkbox-module-24-6 d-flex align-items-center justify-content-center text-center"'. $bg_color .'>'; // Bootstrap-Klassen für zentrierten Inhalt
                        if ($linkbox->getUrl() !== '') {
                            echo '<a href="'. $linkbox->getUrl() .'" class="linkbox-link"">';
                        }
                        
                            $picture = '' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture;
                            $media = rex_media::get($picture);
                            if ($media instanceof rex_media) {
                                echo '<div class="linkbox-background w-100 h-100" style="background-image: url(\''. rex_media_manager::getUrl($picture_type, $picture) .'\');"></div>';
                            }
                            
                            echo '<div class="linkbox-inner w-100 h-100">';
                                echo '<div class="linkbox-content">';
                                    if ('' !== $linkbox->pictogram) {
                                        echo '<div class="linkbox-pictogram"><img src="'. rex_url::media($linkbox->pictogram) .'"></div>';
                                    }
                                    echo '<h2 class="linkbox-title">'. $linkbox->title .'</h2>';
                                    if ('' !== $linkbox->teaser) {
                                        echo '<div class="linkbox-teaser pl-2 pr-2">'. $linkbox->teaser .'</div>'; // Bootstrap-Klasse "p" für Absätze
                                    }
                                echo '</div>';
                            echo '</div>'; // class="linkbox-inner"
                        echo '</a>';
                    echo '</div>'; // class="linkbox"
                echo '</div>'; // class="col..."
            }
            echo '</div>';
        echo '</div>';
    }
}