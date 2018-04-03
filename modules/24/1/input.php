<?php
$categories = D2U_Linkbox\Category::getAll(rex_clang::getCurrentId(), TRUE);
if (count($categories) > 0) {
	print 'Welche Linkbox Kategorie soll angezeigt werden? <select name="REX_INPUT_VALUE[1]">';
	foreach ($categories as $category) {
		echo '<option value="'. $category->category_id .'" ';

		if ("REX_VALUE[1]" == $category->category_id) {
			echo 'selected="selected" ';
		}
		echo '>'. $category->name .'</option>';
	}
	print '</select>';
}
print "<br />";
print "<br />";
?>
<br /><br />
Titel (optional): <input type="text" size="60" name="REX_INPUT_VALUE[2]" value="REX_VALUE[2]" />
<br /><br />
<p>Alle weiteren inhaltlichen Änderungen bitte im <a href="index.php?page=d2u_linkbox">"Linkbox"</a> Addon vornehmen.</p>