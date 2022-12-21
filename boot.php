<?php
if(rex::isBackend() && is_object(rex::getUser())) {
	rex_perm::register('d2u_linkbox[]', rex_i18n::msg('d2u_linkbox_rights_all'));
	rex_perm::register('d2u_linkbox[edit_lang]', rex_i18n::msg('d2u_linkbox_rights_edit_lang'), rex_perm::OPTIONS);
	rex_perm::register('d2u_linkbox[edit_data]', rex_i18n::msg('d2u_linkbox_rights_edit_data'), rex_perm::OPTIONS);
	rex_perm::register('d2u_linkbox[settings]', rex_i18n::msg('d2u_linkbox_rights_settings'), rex_perm::OPTIONS);	
}

if(rex::isBackend()) {
	rex_extension::register('ART_PRE_DELETED', 'rex_d2u_linkbox_article_is_in_use');
	rex_extension::register('CLANG_DELETED', 'rex_d2u_linkbox_clang_deleted');
	rex_extension::register('MEDIA_IS_IN_USE', 'rex_d2u_linkbox_media_is_in_use');
}

/**
 * Checks if article is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 * @throws rex_api_exception If article is used
 */
function rex_d2u_linkbox_article_is_in_use(rex_extension_point $ep) {
	$warning = [];
	$params = $ep->getParams();
	$article_id = $params['id'];
	
	// Linkbox
	$sql = rex_sql::factory();
	$sql->setQuery('SELECT lang.box_id, title FROM `' . rex::getTablePrefix() . 'd2u_linkbox_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_linkbox` AS linkbox '
			. 'ON lang.box_id = linkbox.box_id '
		.'WHERE article_id = '. $article_id .' AND clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
		.'GROUP BY box_id, title');

	// Linkbox Warnings
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message = '<a href="'. rex_url::backendPage('d2u_linkbox', ['func' => 'edit', 'entry_id' => $sql->getValue('box_id')]) .'">'. rex_i18n::msg('d2u_linkbox') .': '. $sql->getValue('title') .'</a>';
		$warning[] = $message;
		$sql->next();
    }
	
	if(count($warning) > 0) {
		throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') ."<ul><li>". implode("</li><li>", $warning) ."</li></ul>");
	}
	else {
		return "";
	}
}

/**
 * Deletes language specific configurations and objects
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_linkbox_clang_deleted(rex_extension_point $ep) {
	/** @var string[] $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$clang_id = $params['id'];

	// Delete
	$linkboxes = D2U_Linkbox\Linkbox::getAll($clang_id, 0, FALSE);
	foreach ($linkboxes as $linkbox) {
		$linkbox->delete(FALSE);
	}
	
	return $warning;
}

/**
 * Checks if media is used by this addon
 * @param rex_extension_point $ep Redaxo extension point
 * @return string[] Warning message as array
 */
function rex_d2u_linkbox_media_is_in_use(rex_extension_point $ep) {
	/** @var string[] $warning */
	$warning = $ep->getSubject();
	$params = $ep->getParams();
	$filename = addslashes($params['filename']);

	// Linkbox
	$sql = rex_sql::factory();
	$sql->setQuery('SELECT lang.box_id, title FROM `' . rex::getTablePrefix() . 'd2u_linkbox_lang` AS lang '
		.'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_linkbox` AS linkbox '
			. 'ON lang.box_id = linkbox.box_id '
		.'WHERE picture = "'. $filename .'" AND clang_id = '. rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()) .' '
		.'GROUP BY box_id, title');

	// Linkbox Warnings
	for($i = 0; $i < $sql->getRows(); $i++) {
		$message = '<a href="javascript:openPage(\''. rex_url::backendPage('d2u_linkbox', ['entry_id' => $sql->getValue('box_id'), 'func' => 'edit']) .'\')">'. rex_i18n::msg('d2u_linkbox') .': '. $sql->getValue('title') .'</a>';
		$warning[] = $message;
		$sql->next();
    }

	return $warning;
}