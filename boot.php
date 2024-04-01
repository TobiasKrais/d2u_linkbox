<?php

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('d2u_linkbox[]', rex_i18n::msg('d2u_linkbox_rights_all'));
    rex_perm::register('d2u_linkbox[edit_lang]', rex_i18n::msg('d2u_linkbox_rights_edit_lang'), rex_perm::OPTIONS);
    rex_perm::register('d2u_linkbox[edit_data]', rex_i18n::msg('d2u_linkbox_rights_edit_data'), rex_perm::OPTIONS);
    rex_perm::register('d2u_linkbox[settings]', rex_i18n::msg('d2u_linkbox_rights_settings'), rex_perm::OPTIONS);
}

if (rex::isBackend()) {
    rex_extension::register('ART_PRE_DELETED', rex_d2u_linkbox_article_is_in_use(...));
    rex_extension::register('CLANG_DELETED', rex_d2u_linkbox_clang_deleted(...));
    rex_extension::register('D2U_HELPER_TRANSLATION_LIST', rex_d2u_linkbox_translation_list(...));
    rex_extension::register('MEDIA_IS_IN_USE', rex_d2u_linkbox_media_is_in_use(...));
}

/**
 * Checks if article is used by this addon.
 * @param rex_extension_point<string> $ep Redaxo extension point
 * @throws rex_api_exception If article is used
 * @return string Warning message as array
 */
function rex_d2u_linkbox_article_is_in_use(rex_extension_point $ep)
{
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
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="'. rex_url::backendPage('d2u_linkbox', ['func' => 'edit', 'entry_id' => $sql->getValue('box_id')]) .'">'. rex_i18n::msg('d2u_linkbox') .': '. $sql->getValue('title') .'</a>';
        $warning[] = $message;
        $sql->next();
    }

    if (count($warning) > 0) {
        throw new rex_api_exception(rex_i18n::msg('d2u_helper_rex_article_cannot_delete') .'<ul><li>'. implode('</li><li>', $warning) .'</li></ul>');
    }

    return '';
}

/**
 * Deletes language specific configurations and objects.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_linkbox_clang_deleted(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $clang_id = (int) $params['id'];

    // Delete
    $linkboxes = TobiasKrais\D2ULinkbox\Linkbox::getAll($clang_id, 0, false);
    foreach ($linkboxes as $linkbox) {
        $linkbox->delete(false);
    }

    return $warning;
}

/**
 * Checks if media is used by this addon.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<string> Warning message as array
 */
function rex_d2u_linkbox_media_is_in_use(rex_extension_point $ep)
{
    $warning = $ep->getSubject();
    $params = $ep->getParams();
    $filename = addslashes((string) $params['filename']);

    // Linkbox
    $sql = rex_sql::factory();
    $sql->setQuery('SELECT lang.box_id, title FROM `' . rex::getTablePrefix() . 'd2u_linkbox_lang` AS lang '
        .'LEFT JOIN `' . rex::getTablePrefix() . 'd2u_linkbox` AS linkbox '
            . 'ON lang.box_id = linkbox.box_id '
        .'WHERE picture = "'. $filename .'" AND clang_id = '. rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()) .' '
        .'GROUP BY box_id, title');

    // Linkbox Warnings
    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $message = '<a href="javascript:openPage(\''. rex_url::backendPage('d2u_linkbox', ['entry_id' => $sql->getValue('box_id'), 'func' => 'edit']) .'\')">'. rex_i18n::msg('d2u_linkbox') .': '. $sql->getValue('title') .'</a>';
        $warning[] = $message;
        $sql->next();
    }

    return $warning;
}

/**
 * Addon translation list.
 * @param rex_extension_point<array<string>> $ep Redaxo extension point
 * @return array<array<string,array<int,array<string,string>>|string>|string> Addon translation list
 */
function rex_d2u_linkbox_translation_list(rex_extension_point $ep) {
    $params = $ep->getParams();
    $source_clang_id = (int) $params['source_clang_id'];
    $target_clang_id = (int) $params['target_clang_id'];
    $filter_type = (string) $params['filter_type'];

    $list = $ep->getSubject();
    $list_entry = [
        'addon_name' => rex_i18n::msg('d2u_linkbox'),
        'pages' => []
    ];

    $linkboxes = \TobiasKrais\D2ULinkbox\Linkbox::getTranslationHelperObjects($target_clang_id, $filter_type);
    if (count($linkboxes) > 0) {
        $html_linkboxes = '<ul>';
        foreach ($linkboxes as $linkbox) {
            if ('' === $linkbox->title) {
                $linkbox = new \TobiasKrais\D2ULinkbox\Linkbox($linkbox->box_id, $source_clang_id);
            }
            $html_linkboxes .= '<li><a href="'. rex_url::backendPage('d2u_linkbox/linkbox', ['entry_id' => $linkbox->box_id, 'func' => 'edit']) .'">'. $linkbox->title .'</a></li>';
        }
        $html_linkboxes .= '</ul>';
        
        $list_entry['pages'][] = [
            'title' => rex_i18n::msg('d2u_linkbox'),
            'icon' => 'fa-window-maximize',
            'html' => $html_linkboxes
        ];
    }

    $list[] = $list_entry;

    return $list;
}