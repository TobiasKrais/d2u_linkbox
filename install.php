<?php

\rex_sql_table::get(\rex::getTable('d2u_linkbox'))
    ->ensureColumn(new rex_sql_column('box_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('box_id')
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('background_color', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('link_type', 'VARCHAR(50)', true))
    ->ensureColumn(new \rex_sql_column('article_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('document', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('external_url', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('link_addon_id', 'INT(11)', true))
    ->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(7)', true))
    ->ensureColumn(new \rex_sql_column('priority', 'INT(10)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_linkbox_lang'))
    ->ensureColumn(new rex_sql_column('box_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false))
    ->setPrimaryKey(['box_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('title', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT', true))
    ->ensureColumn(new \rex_sql_column('picture_lang', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('document_lang', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('external_url_lang', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)', true))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_linkbox_categories'))
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('category_id')
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)', false))
    ->ensure();

// Update modules
if (class_exists('D2UModuleManager')) {
    $modules = [];
    $modules[] = new D2UModule('24-1',
        'D2U Linkbox - Linkboxen mit Überschrift in Bild',
        9);
    $modules[] = new D2UModule('24-2',
        'D2U Linkbox - Linkboxen mit Überschrift unter Bild',
        10);
    $modules[] = new D2UModule('24-3',
        'D2U Linkbox - Farbboxen mit seitlichem Bild',
        7);
    $modules[] = new D2UModule('24-4',
        'D2U Linkbox - Slider',
        6);
    $modules[] = new D2UModule('24-5',
        'D2U Linkbox - Linkboxen mit Text neben dem Bild',
        3);
    $d2u_module_manager = new D2UModuleManager($modules, '', 'd2u_linkbox');
    $d2u_module_manager->autoupdate();
}

// 1.4 Update database to fit wysiwyg editor
if (rex_version::compare($this->getVersion(), '1.4.0', '<')) {
    $result = rex_sql::factory();
    $result->setQuery('SELECT * FROM '. \rex::getTablePrefix() .'d2u_linkbox_lang;');

    for ($i = 0; $i < $result->getRows(); ++$i) {
        $update_sql = rex_sql::factory();
        $update_sql->setQuery('UPDATE `'. \rex::getTablePrefix() .'d2u_linkbox_lang` SET teaser = "'. addslashes(nl2br(stripslashes($result->getValue('teaser')))) .'" '
            .'WHERE box_id = '. $result->getValue('box_id') .' AND clang_id = '. $result->getValue('clang_id') .';');
        $result->next();
    }
}
