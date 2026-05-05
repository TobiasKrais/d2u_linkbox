<?php
$cols_sm = 0 === (int) 'REX_VALUE[20]' ? 12 : (int) 'REX_VALUE[20]'; /** @phpstan-ignore-line */
$cols_md = 0 === (int) 'REX_VALUE[19]' ? 12 : (int) 'REX_VALUE[19]'; /** @phpstan-ignore-line */
$cols_lg = 0 === (int) 'REX_VALUE[18]' ? 12 : (int) 'REX_VALUE[18]'; /** @phpstan-ignore-line */
$offset_lg = (int) 'REX_VALUE[17]' > 0 ? ' me-lg-auto ms-lg-auto ' : ''; /** @phpstan-ignore-line */

$category_id = (int) 'REX_VALUE[1]' > 0 ? (int) 'REX_VALUE[1]' : 0; /** @phpstan-ignore-line */
$category = $category_id > 0 ? new TobiasKrais\D2ULinkbox\Category($category_id, rex_clang::getCurrentId()) : false; /** @phpstan-ignore-line */
$heading = 'REX_VALUE[2]';
$box_per_line = (int) 'REX_VALUE[3]';
$picture_type = 'REX_VALUE[5]' === '' ? 'd2u_helper_sm' : 'REX_VALUE[5]'; /** @phpstan-ignore-line */

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
    <h1 class="d2u-linkbox-module-preview-title">Linkboxen</h1>
	Überschrift: REX_VALUE[2]<br>
	Gewählte Kategorie: <?php echo $category instanceof TobiasKrais\D2ULinkbox\Category ? rex_escape($category->name) : 'keine'; /** @phpstan-ignore-line */ ?><br>
	Anzahl Linkboxen pro Zeile (große Bildschirme): <?= $box_per_line ?><br>
<?php
} else {
    // Ausgabe im FRONTEND
    if ($category instanceof TobiasKrais\D2ULinkbox\Category) { /** @phpstan-ignore-line */
        echo '<div class="col-12 col-sm-'. $cols_sm .' col-md-'. $cols_md .' col-lg-'. $cols_lg . $offset_lg .' abstand">';
            echo '<div class="row">'; /** @phpstan-ignore-line */
            $linkboxes = $category->getLinkboxes(true);

            if ('' !== $heading) { /** @phpstan-ignore-line */
                echo '<div class="col-12">';
                echo '<h1>'. rex_escape($heading) .'</h1>';
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
            
                    $picture = '' !== $linkbox->picture_lang ? $linkbox->picture_lang : $linkbox->picture;
                    $attributes = [];
                    if ('' !== $linkbox->background_color) {
                        $attributes[] = ' data-linkbox-bg-color="'. rex_escape($linkbox->background_color) .'"';
                    }
                    if ('' !== $linkbox->background_color_dark) {
                        $attributes[] = ' data-linkbox-bg-color-dark="'. rex_escape($linkbox->background_color_dark) .'"';
                    }
                    if ('' !== $picture) {
                        $attributes[] = ' data-linkbox-hover-image="'. rex_escape(rex_media_manager::getUrl($picture_type, $picture)) .'"';
                    }
                    echo '<div class="linkbox-module-24-6 d-flex align-items-center justify-content-center text-center"'. implode('', $attributes) .'>'; // Bootstrap-Klassen für zentrierten Inhalt
                        if ($linkbox->getUrl() !== '') {
                            echo '<a href="'. rex_escape($linkbox->getUrl()) .'" class="linkbox-link">';
                        }
                        
                            $media = rex_media::get($picture);
                            if ($media instanceof rex_media) {
                                echo '<div class="linkbox-background w-100 h-100"></div>';
                            }
                            
                            echo '<div class="linkbox-inner w-100 h-100">';
                                echo '<div class="linkbox-content">';
                                    $defaultPictogram = '' !== $linkbox->pictogram ? $linkbox->pictogram : $linkbox->pictogram_dark;
                                    $darkPictogram = '' !== $linkbox->pictogram_dark ? $linkbox->pictogram_dark : '';
                                    if ('' !== $defaultPictogram) {
                                        $defaultPictogramMedia = rex_media::get($defaultPictogram);
                                        $defaultPictogramTitle = $defaultPictogramMedia instanceof rex_media ? $defaultPictogramMedia->getValue('title') : $linkbox->title;
                                        $hasDarkPictogram = '' !== $darkPictogram && $darkPictogram !== $defaultPictogram;
                                        echo '<div class="linkbox-pictogram'. ($hasDarkPictogram ? ' linkbox-pictogram-has-dark' : '') .'">';
                                        echo '<img class="linkbox-pictogram-light" src="'. rex_url::media($defaultPictogram) .'" alt="'. rex_escape((string) $defaultPictogramTitle) .'" title="'. rex_escape((string) $defaultPictogramTitle) .'" loading="lazy">';
                                        if ($hasDarkPictogram) {
                                            $darkPictogramMedia = rex_media::get($darkPictogram);
                                            $darkPictogramTitle = $darkPictogramMedia instanceof rex_media ? $darkPictogramMedia->getValue('title') : $linkbox->title;
                                            echo '<img class="linkbox-pictogram-dark" src="'. rex_url::media($darkPictogram) .'" alt="'. rex_escape((string) $darkPictogramTitle) .'" title="'. rex_escape((string) $darkPictogramTitle) .'" loading="lazy">';
                                        }
                                        echo '</div>';
                                    }
                                    echo '<h2 class="linkbox-title">'. rex_escape($linkbox->title) .'</h2>';
                                    if ('' !== $linkbox->teaser) {
                                        echo '<div class="linkbox-teaser ps-2 pe-2">'. $linkbox->teaser .'</div>';
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