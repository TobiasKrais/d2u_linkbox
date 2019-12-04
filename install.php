<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_linkbox` (
	`box_id` int(10) unsigned NOT NULL auto_increment,
	`picture` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`background_color` varchar(7) collate utf8mb4_unicode_ci default NULL,
	`link_type` varchar(50) collate utf8mb4_unicode_ci default NULL,
	`article_id` int(10) default NULL,
	`document` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`external_url` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`link_addon_id` int(10) default NULL,
	`category_ids` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`online_status` varchar(10) collate utf8mb4_unicode_ci default 'online',
	`priority` int(10) default NULL,
	PRIMARY KEY (`box_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_linkbox_lang` (
	`box_id` int(10) NOT NULL,
	`clang_id` int(10) NOT NULL,
	`title` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`teaser` text collate utf8mb4_unicode_ci default NULL,
	`picture_lang` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`document_lang` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`external_url_lang` varchar(255) collate utf8mb4_unicode_ci default NULL,
	`translation_needs_update` varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (`box_id`, `clang_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS `". rex::getTablePrefix() ."d2u_linkbox_categories` (
	`category_id` int(10) unsigned NOT NULL auto_increment,
	`name` varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (`category_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}