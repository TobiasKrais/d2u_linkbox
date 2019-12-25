<?php
// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("24-1",
		"D2U Linkbox - Linkboxen mit Überschrift in Bild",
		6);
	$modules[] = new D2UModule("24-2",
		"D2U Linkbox - Linkboxen mit Überschrift unter Bild",
		7);
	$modules[] = new D2UModule("24-3",
		"D2U Linkbox - Farbboxen mit seitlichem Bild",
		5);
	$modules[] = new D2UModule("24-4",
		"D2U Linkbox - Slider",
		4);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_linkbox");
	$d2u_module_manager->autoupdate();
}

// database update
\rex_sql_table::get(\rex::getTable('d2u_linkbox_lang'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('picture_lang', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('document_lang', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('external_url_lang', 'VARCHAR(255)'))
    ->alter();

\rex_sql_table::get(\rex::getTable('d2u_linkbox'))
    ->ensureColumn(new \rex_sql_column('background_color', 'VARCHAR(7)'))
    ->ensureColumn(new \rex_sql_column('link_type', 'VARCHAR(50)'))
    ->ensureColumn(new \rex_sql_column('link_addon_id', 'INT(10)'))
    ->ensureColumn(new \rex_sql_column('external_url', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('document', 'VARCHAR(255)'))
    ->alter();

// 1.2 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox ADD priority INT(10) NULL DEFAULT NULL AFTER online_status;");
	$sql->setQuery("SELECT box_id FROM `". \rex::getTablePrefix() ."d2u_linkbox` ORDER BY name;");
	$update_sql = rex_sql::factory();
	for($i = 1; $i <= $sql->getRows(); $i++) {
		$update_sql->setQuery("UPDATE `". \rex::getTablePrefix() ."d2u_linkbox` SET priority = ". $i ." WHERE box_id = ". $sql->getValue('box_id') .";");
		$sql->next();
	}

}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}

// Update database to 1.2.2
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox CHANGE link_type link_type VARCHAR(50) NULL DEFAULT NULL;");