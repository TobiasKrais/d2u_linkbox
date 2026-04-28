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
// save settings
if (!$invalidCsrf && 'save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);

    // Save settings
    if (rex_config::set('d2u_linkbox', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));
    } else {
        echo rex_view::error(rex_i18n::msg('form_save_error'));
    }
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
	echo BackendHelper::getCSS();
	echo BackendHelper::getJS();
	echo BackendHelper::getJSOpenAll();
