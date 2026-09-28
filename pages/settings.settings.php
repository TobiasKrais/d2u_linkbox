<?php

use TobiasKrais\D2UHelper\BackendHelper;

$csrfToken = BackendHelper::getPageCsrfToken();
$invalidCsrf = false;
if ((
    'save' === filter_input(INPUT_POST, 'btn_save')
    || 'Speichern' === rex_request::request('btn_save', 'string')
    || 1 === (int) filter_input(INPUT_POST, 'btn_save')
    || 1 === (int) filter_input(INPUT_POST, 'btn_apply')
    || 1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT)
) && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
    $invalidCsrf = true;
}
$categoryUsageTablesExample = <<<'JSON'
[
	{
		"table": "article_slice",
		"field": "value1",
		"where": "module_id = 24"
	}
]
JSON;

$clang_id = (int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId());

// save settings
if (!$invalidCsrf && 'save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);
    $category_usage_tables = trim((string) ($settings['category_usage_tables'] ?? ''));
    if ('' !== $category_usage_tables && !is_array(json_decode($category_usage_tables, true))) {
        echo rex_view::error(rex_i18n::msg('d2u_linkbox_settings_json_invalid'));
    } elseif (rex_config::set('d2u_linkbox', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));
    } else {
        echo rex_view::error(rex_i18n::msg('form_save_error'));
    }
}

// delete unused categories together with their linkboxes
if (!$invalidCsrf && 'delete_unused' === filter_input(INPUT_POST, 'func')) {
    $selected = rex_post('category_ids', 'array', []);
    $deleted = 0;
    foreach ($selected as $selected_id) {
        $selected_id = (int) $selected_id;
        // Re-check right before deleting so a category used in the meantime is spared.
        if ($selected_id > 0 && !\TobiasKrais\D2ULinkbox\Category::isUsedInModules($selected_id)) {
            (new \TobiasKrais\D2ULinkbox\Category($selected_id, $clang_id))->deleteWithLinkboxes();
            ++$deleted;
        }
    }
    echo rex_view::success(rex_i18n::msg('d2u_linkbox_settings_unused_deleted', $deleted));
}
?>
<form action="<?= BackendHelper::getCurrentBackendPage([], ['message', 'message_type']) ?>" method="post">
	<?= $csrfToken->getHiddenField() ?>
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_helper_settings') ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?= rex_i18n::msg('d2u_helper_settings') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        $options_sort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
						BackendHelper::form_select('d2u_helper_sort', 'settings[default_sort]', $options_sort, [(string) rex_config::get('d2u_linkbox', 'default_sort')]);
						BackendHelper::form_textarea('d2u_linkbox_settings_category_usage_tables', 'settings[category_usage_tables]', (string) rex_config::get('d2u_linkbox', 'category_usage_tables'), 8, false, false, false);
						echo '<p><small>'. nl2br(rex_escape(rex_i18n::msg('d2u_linkbox_settings_category_usage_tables_description'))) .'</small></p>';
						echo '<pre><code>'. rex_escape($categoryUsageTablesExample) .'</code></pre>';
                    ?>
				</div>
			</fieldset>
		</div>
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?= rex_i18n::msg('form_save') ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
// ===== Nicht genutzte Kategorien =====
$configPresent = '' !== trim((string) rex_config::get('d2u_linkbox', 'category_usage_tables', ''));
?>
<div class="panel panel-edit">
	<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_linkbox_settings_unused_categories') ?></div></header>
	<div class="panel-body">
	<?php
	if (!$configPresent) {
		echo rex_view::info(rex_i18n::msg('d2u_linkbox_settings_unused_categories_configure_first'));
	} else {
		$unusedCategories = \TobiasKrais\D2ULinkbox\Category::getUnusedCategories($clang_id);
		if (0 === count($unusedCategories)) {
			echo rex_view::info(rex_i18n::msg('d2u_linkbox_settings_unused_categories_none'));
		} else {
			?>
			<form action="<?= BackendHelper::getCurrentBackendPage([], ['message', 'message_type']) ?>" method="post" onsubmit="return confirm('<?= rex_escape(rex_i18n::msg('d2u_linkbox_settings_unused_delete_confirm'), 'js') ?>');">
				<?= $csrfToken->getHiddenField() ?>
				<input type="hidden" name="func" value="delete_unused">
				<table class="table table-striped">
					<thead><tr><th></th><th><?= rex_i18n::msg('d2u_helper_name') ?></th><th><?= rex_i18n::msg('d2u_linkbox_linkbox') ?></th></tr></thead>
					<tbody>
					<?php foreach ($unusedCategories as $unusedCategory) {
						echo '<tr><td><input type="checkbox" name="category_ids[]" value="'. $unusedCategory->category_id .'" checked></td><td>'. rex_escape($unusedCategory->name) .'</td><td>'. count($unusedCategory->getLinkboxes(false)) .'</td></tr>';
					} ?>
					</tbody>
				</table>
				<button class="btn btn-delete" type="submit"><?= rex_i18n::msg('d2u_linkbox_settings_delete_unused') ?></button>
			</form>
			<?php
		}
	}
	?>
	</div>
</div>
<?php
	echo BackendHelper::getCSS();
	echo BackendHelper::getJS();
	echo BackendHelper::getJSOpenAll();