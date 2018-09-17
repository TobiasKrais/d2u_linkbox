<?php
// 1.1 teaser update
rex_sql_table::get(rex::getTable('d2u_linkbox_lang'))->ensureColumn(new \rex_sql_column('teaser', 'text', true, null))->alter();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("24-1",
		"D2U Linkbox - Linkboxen mit Überschrift in Bild",
		3);
	$modules[] = new D2UModule("24-2",
		"D2U Linkbox - Linkboxen mit Überschrift unter Bild",
		2);
	$modules[] = new D2UModule("24-3",
		"D2U Linkbox - Farbboxen mit seitlichem Bild",
		1);
	$modules[] = new D2UModule("24-4",
		"D2U Linkbox - Slider",
		1);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_linkbox");
	$d2u_module_manager->autoupdate();
}

// 1.2 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD priority INT(10) NULL DEFAULT NULL AFTER online_status;");
	
	$sql->setQuery("DROP FUNCTION IF EXISTS `getPriority`;");
	$sql->setQuery("delimiter $$
		CREATE FUNCTION `getPriority`() RETURNS int(11) DETERMINISTIC
		begin
			return if(@myPrio, @myPrio:=@myPrio+1, @myPrio:=1);
		end$$
		delimiter ;");
	$sql->setQuery("UPDATE ". \rex::getTablePrefix() ."d2u_linkbox SET priority = getPriority() ORDER BY `box_id`;");
	$sql->setQuery("DROP FUNCTION IF EXISTS `getPriority`;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'background_color';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD background_color VARCHAR(6) NULL DEFAULT NULL AFTER picture;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'link_type';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD link_type VARCHAR(10) NULL DEFAULT NULL AFTER background_color;");
}
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'document';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD document VARCHAR(255) NULL DEFAULT NULL AFTER link_type;");
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}