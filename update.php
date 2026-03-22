<?php

// Rename dark mode background color column
$sql = rex_sql::factory();
$sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_linkbox LIKE 'dark_background_color';");
$has_old_dark_background_color = $sql->getRows() > 0;

$sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_linkbox LIKE 'background_color_dark';");
$has_background_color_dark = $sql->getRows() > 0;

if ($has_old_dark_background_color && !$has_background_color_dark) {
    $sql->setQuery('ALTER TABLE `'. \rex::getTablePrefix() .'d2u_linkbox` CHANGE `dark_background_color` `background_color_dark` VARCHAR(7) NULL;');
}

// 1.2 Update database
$sql = rex_sql::factory();
$sql->setQuery('SHOW COLUMNS FROM '. \rex::getTablePrefix() ."d2u_linkbox LIKE 'priority';");
if (0 === $sql->getRows()) {
    \rex_sql_table::get(\rex::getTable('d2u_linkbox'))
        ->ensureColumn(new \rex_sql_column('priority', 'INT(10)', true))
        ->alter();

    $sql->setQuery('SELECT box_id FROM `'. \rex::getTablePrefix() .'d2u_linkbox` ORDER BY name;');
    $update_sql = rex_sql::factory();
    for ($i = 1; $i <= $sql->getRows(); ++$i) {
        $update_sql->setQuery('UPDATE `'. \rex::getTablePrefix() .'d2u_linkbox` SET priority = '. $i .' WHERE box_id = '. $sql->getValue('box_id') .';');
        $sql->next();
    }
}

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */
