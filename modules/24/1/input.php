<div class="row">
	<div class="col-xs-4">Titel (optional):</div>
	<div class="col-xs-8"><input type="text" style="width: 100%" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" class="form-control" /></div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">Linkbox Kategorie</div>
	<div class="col-xs-8">
		<?php
            $categories = D2U_Linkbox\Category::getAll(rex_clang::getCurrentId(), false);
            if (count($categories) > 0) {
                echo ' <select name="REX_INPUT_VALUE[1]" class="form-control" >';
                foreach ($categories as $category) {
                    echo '<option value="'. $category->category_id .'" ';

                    if ((int) 'REX_VALUE[1]' === $category->category_id) { /** @phpstan-ignore-line */
                        echo 'selected="selected" ';
                    }
                    echo '>'. $category->name .'</option>';
                }
                echo '</select>';
            }
        ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">Anzahl Linkboxen / Zeile</div>
	<div class="col-xs-8">
		<?php
            echo '<select name="REX_INPUT_VALUE[3]" class="form-control">';
            echo '<option value="3" '. (3 === (int) 'REX_VALUE[3]' ? 'selected="selected" ' : '') .'>3</option>'; /** @phpstan-ignore-line */
            echo '<option value="4" '. (4 === (int) 'REX_VALUE[3]' ? 'selected="selected" ' : '') .'>4</option>'; /** @phpstan-ignore-line */
            echo '</select>';
        ?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<input type="checkbox" name="REX_INPUT_VALUE[4]" value="true" <?= 'REX_VALUE[4]' === 'true' ? ' checked="checked"' : '' /** @phpstan-ignore-line */ ?> class="form-control d2u_helper_toggle" /> <?php /** @phpstan-ignore-line */ ?>
	</div>
	<div class="col-xs-8">
		Teaser / Kurztext unterhalb der Überschriften anzeigen?<br />
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-12">Alle weiteren inhaltlichen Änderungen bitte im <a href="index.php?page=d2u_linkbox">"Linkbox"</a> Addon vornehmen.</div>
</div>