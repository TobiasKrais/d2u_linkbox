<?php
$categories = D2U_Linkbox\Category::getAll(rex_clang::getCurrentId(), TRUE);
if (count($categories) > 0) {
	print 'Welche Linkbox Katgeorie soll angezeigt werden? <select name="VALUE[1]">';
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

// Art der Anzeige
$anzeige = ["greenbox" => "Greenbox", "greybox" => "Greybox"];
print 'Wie soll die Ausgabe angezeigt werden?';
$select = new rex_select(); 
$select->setName('VALUE[2]'); 
$select->setSize(1);
// Daten
foreach($anzeige as $key => $val)  {
	$select->addOption($val, $key); 
}
// Vorselektierung
$select->setSelected("REX_VALUE[2]"); 
echo $select->show();
?>
<br /><br />
Titel (optional): <input type="text" size="60" name="VALUE[3]" value="REX_VALUE[3]" />
<br /><br />
<p>Alle weiteren inhaltlichen Änderungen bitte im <a href="index.php?page=d2u_linkbox">"Linkbox"</a> Addon vornehmen.</p>