<div class="row">
	<div class="col-xs-4">Titel (optional):</div>
	<div class="col-xs-8"><input type="text" style="width: 100%" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" /></div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">Linkbox Kategorie</div>
	<div class="col-xs-8">
		<?php
			$categories = D2U_Linkbox\Category::getAll(rex_clang::getCurrentId(), TRUE);
			if (count($categories) > 0) {
				print '<select name="REX_INPUT_VALUE[1]">';
				foreach ($categories as $category) {
					echo '<option value="'. $category->category_id .'" ';

					if ("REX_VALUE[1]" == $category->category_id) {
						echo 'selected="selected" ';
					}
					echo '>'. $category->name .'</option>';
				}
				print '</select>';
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
			print '<select name="REX_INPUT_VALUE[3]">';
			print '<option value="3" '. ("REX_VALUE[3]" == 3 ? 'selected="selected" ' : '') .'>3</option>';
			print '<option value="4" '. ("REX_VALUE[3]" == 4 ? 'selected="selected" ' : '') .'>4</option>';
			print '</select>';
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div>
<div class="row">
	<div class="col-xs-4">
		<input type="checkbox" name="REX_INPUT_VALUE[4]" value="true" <?php echo "REX_VALUE[4]" == 'true' ? ' checked="checked"' : ''; ?> style="float: right;" />
	</div>
	<div class="col-xs-8">
		Teaser / Kurztext unterhalb der Überschriften anzeigen?<br />
	</div>
</div>
<div class="row">
	<div class="col-xs-12">&nbsp;</div>
</div><div class="row">
	<div class="col-xs-12">Alle weiteren inhaltlichen Änderungen bitte im <a href="index.php?page=d2u_linkbox">"Linkbox"</a> Addon vornehmen.</div>
</div>