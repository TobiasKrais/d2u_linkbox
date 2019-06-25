<?php
// 1.1 teaser update
rex_sql_table::get(rex::getTable('d2u_linkbox_lang'))->ensureColumn(new \rex_sql_column('teaser', 'text', true, null))->alter();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("24-1",
		"D2U Linkbox - Linkboxen mit Überschrift in Bild",
		6);
	$modules[] = new D2UModule("24-2",
		"D2U Linkbox - Linkboxen mit Überschrift unter Bild",
		6);
	$modules[] = new D2UModule("24-3",
		"D2U Linkbox - Farbboxen mit seitlichem Bild",
		4);
	$modules[] = new D2UModule("24-4",
		"D2U Linkbox - Slider",
		4);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_linkbox");
	$d2u_module_manager->autoupdate();
}

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
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'background_color';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD background_color VARCHAR(6) NULL DEFAULT NULL AFTER picture;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'link_type';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD link_type VARCHAR(50) NULL DEFAULT NULL AFTER background_color;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'document';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD document VARCHAR(255) NULL DEFAULT NULL AFTER link_type;");
}

// 1.2.1 Update
$sql->setQuery("ALTER TABLE `". \rex::getTablePrefix() ."d2u_linkbox` CHANGE `background_color` `background_color` VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;");

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}

// Update database to 1.2.2
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox_lang` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_linkbox_categories` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox CHANGE link_type link_type VARCHAR(50) NULL DEFAULT NULL;");
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'link_addon_id';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD link_addon_id INT(10) NULL DEFAULT NULL AFTER document;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'external_url';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD external_url VARCHAR(255) NULL DEFAULT NULL AFTER link_addon_id;");
}