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