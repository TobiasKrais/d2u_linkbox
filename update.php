<?php
// 1.1 teaser update
rex_sql_table::get(rex::getTable('d2u_linkbox_lang'))->ensureColumn(new \rex_sql_column('teaser', 'text', true, null))->alter();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("24-1",
		"D2U Linkbox - Linkboxen",
		1);

	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_linkbox");
	$d2u_module_manager->autoupdate();
}

// 1.1.1 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_linkbox LIKE 'priority';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_linkbox "
		. "ADD priority INT(10) NULL DEFAULT NULL AFTER online_status;");
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}