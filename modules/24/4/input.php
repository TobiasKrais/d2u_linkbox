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
            $categories = TobiasKrais\D2ULinkbox\Category::getAll(rex_clang::getCurrentId(), false);
            if (count($categories) > 0) {
                echo '<select name="REX_INPUT_VALUE[1]" class="form-control">';
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
</div><div class="row">
	<div class="col-xs-12">Alle weiteren inhaltlichen Änderungen bitte im <a href="index.php?page=d2u_linkbox">"Linkbox"</a> Addon vornehmen.</div>
</div>