<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if ('' !== $message) {
    echo rex_view::success(rex_i18n::msg($message));
}

// save settings
if (1 === (int) filter_input(INPUT_POST, 'btn_save') || 1 === (int) filter_input(INPUT_POST, 'btn_apply')) {
    $form = rex_post('form', 'array', []);

    // Media fields and links need special treatment
    $input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
    $link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);

    $success = true;
    $linkbox = false;
    $box_id = $form['box_id'];
    foreach (rex_clang::getAll() as $rex_clang) {
        if (!$linkbox instanceof TobiasKrais\D2ULinkbox\Linkbox) {
            $linkbox = new TobiasKrais\D2ULinkbox\Linkbox($box_id, $rex_clang->getId());
            $linkbox->box_id = $box_id; // Ensure correct ID in case first language has no object
            $linkbox->picture = $input_media[1];
            $linkbox->pictogram = $input_media[3];
            $linkbox->background_color = $form['background_color'];
            $linkbox->link_type = $form['link_type'];
            $linkbox->article_id = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][1] : 0;
            $linkbox->document = $input_media[2];
            $linkbox->external_url = $form['external_url'];
            if ('d2u_immo_property' === $linkbox->link_type) {
                $linkbox->link_addon_id = (int) $form['d2u_immo_property_id'];
            } elseif ('d2u_machinery_industry_sector' === $linkbox->link_type) {
                $linkbox->link_addon_id = (int) $form['d2u_machinery_industry_sector_id'];
            } elseif ('d2u_machinery_machine' === $linkbox->link_type) {
                $linkbox->link_addon_id = (int) $form['d2u_machinery_machine_id'];
            } elseif ('d2u_machinery_used_machine' === $linkbox->link_type) {
                $linkbox->link_addon_id = (int) $form['d2u_machinery_used_machine_id'];
            } elseif ('d2u_courses_category' === $linkbox->link_type) {
                $linkbox->link_addon_id = (int) $form['d2u_courses_category_id'];
            }
            $category_ids = isset($form['category_ids']) ? array_map('intval', $form['category_ids']) : [];
            $linkbox->categories = [];
            foreach ($category_ids as $category_id) {
                $linkbox->categories[$category_id] = new TobiasKrais\D2ULinkbox\Category($category_id, $rex_clang->getId());
            }
            $linkbox->priority = $form['priority'];
            $linkbox->online_status = array_key_exists('online_status', $form) ? 'online' : 'offline';
        } else {
            $linkbox->clang_id = $rex_clang->getId();
        }
        $linkbox->title = $form['lang'][$rex_clang->getId()]['title'];
        $linkbox->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
        $linkbox->picture_lang = $input_media['picture_lang'. $rex_clang->getId()];
        $linkbox->document_lang = $input_media['document_lang'. $rex_clang->getId()];
        $linkbox->external_url_lang = $form['lang'][$rex_clang->getId()]['external_url_lang'];
        $linkbox->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

        if ('delete' === $linkbox->translation_needs_update) {
            $linkbox->delete(false);
        } elseif ($linkbox->save() > 0) {
            $success = false;
        } else {
            // remember id, for each database lang object needs same id
            $box_id = $linkbox->box_id;
        }
    }

    // message output
    $message = 'form_save_error';
    if ($success) {
        $message = 'form_saved';
    }

    // Redirect to make reload and thus double save impossible
    if (1 === (int) filter_input(INPUT_POST, 'btn_apply', FILTER_VALIDATE_INT) && false !== $linkbox) {
        header('Location: '. rex_url::currentBackendPage(['entry_id' => $linkbox->box_id, 'func' => 'edit', 'message' => $message], false));
    } else {
        header('Location: '. rex_url::currentBackendPage(['message' => $message], false));
    }
    exit;
}
// Delete
if (1 === (int) filter_input(INPUT_POST, 'btn_delete', FILTER_VALIDATE_INT) || 'delete' === $func) {
    $box_id = $entry_id;
    if (0 === $box_id) {
        $form = rex_post('form', 'array', []);
        $box_id = $form['box_id'];
    }
    $linkbox = new TobiasKrais\D2ULinkbox\Linkbox($box_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $linkbox->box_id = $box_id; // Ensure correct ID in case language has no object
    $linkbox->delete();

    $func = '';
}
// Change online status
elseif ('changestatus' === $func) {
    $box_id = $entry_id;
    $linkbox = new TobiasKrais\D2ULinkbox\Linkbox($box_id, (int) rex_config::get('d2u_helper', 'default_lang'));
    $linkbox->box_id = $box_id; // Ensure correct ID in case language has no object
    $linkbox->changeStatus();

    header('Location: '. rex_url::currentBackendPage());
    exit;
}

// Form
if ('edit' === $func || 'clone' === $func || 'add' === $func) {
?>
	<form action="<?= rex_url::currentBackendPage() ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_linkbox') ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[box_id]" value="<?= 'edit' === $func ? $entry_id : 0 ?>">
				<fieldset>
					<legend><?= rex_i18n::msg('d2u_helper_data_all_lang') ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
                            // Do not use last object from translations, because you don't know if it exists in DB
                            $linkbox = new TobiasKrais\D2ULinkbox\Linkbox($entry_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            $readonly = true;
                            if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_linkbox[edit_data]'))) {
                                $readonly = false;
                            }

                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_helper_picture', '1', $linkbox->picture, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('2u_linkbox_pictogram', '3', $linkbox->pictogram, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_linkbox_background_color', 'form[background_color]', $linkbox->background_color, false, false, 'color');
                            $options_link = [
                                'article' => rex_i18n::msg('d2u_helper_article_id'),
                                'document' => rex_i18n::msg('d2u_linkbox_document'),
                                'url' => rex_i18n::msg('d2u_linkbox_external_url'),
                            ];
                            if (rex_addon::get('d2u_immo')->isAvailable()) {
                                $options_link['d2u_immo_property'] = rex_i18n::msg('d2u_immo') .': '. rex_i18n::msg('d2u_immo_property');
                            }
                            if (rex_addon::get('d2u_machinery')->isAvailable()) {
                                if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                                    $options_link['d2u_machinery_industry_sector'] = rex_i18n::msg('d2u_machinery_meta_title') .': '. rex_i18n::msg('d2u_machinery_industry_sectors');
                                }
                                $options_link['d2u_machinery_machine'] = rex_i18n::msg('d2u_machinery_meta_title') .': '. rex_i18n::msg('d2u_machinery_machine');
                                if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
                                    $options_link['d2u_machinery_used_machine'] = rex_i18n::msg('d2u_machinery_meta_title') .': '. rex_i18n::msg('d2u_machinery_used_machines_machine');
                                }
                            }
                            if (rex_addon::get('d2u_courses')->isAvailable()) {
                                $options_link['d2u_courses_category'] = rex_i18n::msg('d2u_courses') .': '. rex_i18n::msg('d2u_helper_category');
                            }
                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_linkbox_linktype', 'form[link_type]', $options_link, [$linkbox->link_type], 1, false, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_linkfield('d2u_helper_article_id', '1', $linkbox->article_id, (int) rex_config::get('d2u_helper', 'default_lang'));
                            \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_linkbox_document', '2', $linkbox->document, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_linkbox_external_url', 'form[external_url]', $linkbox->external_url, false, $readonly);
                            if (rex_addon::get('d2u_immo')->isAvailable()) {
                                $options_immo = [];
                                $properties = \D2U_Immo\Property::getAll(rex_clang::getCurrentId(), '', true);
                                foreach ($properties as $property) {
                                    $options_immo[$property->property_id] = $property->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_immo_property', 'form[d2u_immo_property_id]', $options_immo, ['d2u_immo_property' === $linkbox->link_type ? $linkbox->link_addon_id : ''], 1, false, $readonly);
                            }
                            if (rex_addon::get('d2u_machinery')->isAvailable()) {
                                if (rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                                    $options_industry_sectors = [];
                                    $industry_sectors = IndustrySector::getAll(rex_clang::getCurrentId(), true);
                                    foreach ($industry_sectors as $industry_sector) {
                                        $options_industry_sectors[$industry_sector->industry_sector_id] = $industry_sector->name;
                                    }
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_industry_sectors', 'form[d2u_machinery_industry_sector_id]', $options_industry_sectors, ['d2u_machinery_industry_sector' === $linkbox->link_type ? $linkbox->link_addon_id : ''], 1, false, $readonly);
                                }
                                $options_machines = [];
                                $machines = Machine::getAll(rex_clang::getCurrentId(), true);
                                foreach ($machines as $machine) {
                                    $options_machines[$machine->machine_id] = $machine->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_machine', 'form[d2u_machinery_machine_id]', $options_machines, ['d2u_machinery_machine' === $linkbox->link_type ? $linkbox->link_addon_id : ''], 1, false, $readonly);
                                if (rex_plugin::get('d2u_machinery', 'used_machines')->isAvailable()) {
                                    $options_used_machines = [];
                                    $used_machines = UsedMachine::getAll(rex_clang::getCurrentId(), true);
                                    foreach ($used_machines as $used_machine) {
                                        $options_used_machines[$used_machine->used_machine_id] = $used_machine->name;
                                    }
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_machinery_used_machines_machine', 'form[d2u_machinery_used_machine_id]', $options_used_machines, ['d2u_machinery_used_machine' === $linkbox->link_type ? $linkbox->link_addon_id : ''], 1, false, $readonly);
                                }
                            }
                            if (rex_addon::get('d2u_courses')->isAvailable()) {
                                $options_course_categories = [];
                                $course_categories = \TobiasKrais\D2UCourses\Category::getAll();
                                foreach ($course_categories as $category) {
                                    $options_course_categories[$category->category_id] = ($category->parent_category instanceof TobiasKrais\D2UCourses\Category ? ($category->parent_category->parent_category instanceof TobiasKrais\D2UCourses\Category ? ($category->parent_category->parent_category->parent_category instanceof TobiasKrais\D2UCourses\Category ? $category->parent_category->parent_category->parent_category->name .' → ' : ''). $category->parent_category->parent_category->name .' → ' : ''). $category->parent_category->name .' → ' : ''). $category->name;
                                }
                                \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_category', 'form[d2u_courses_category_id]', $options_course_categories, ['d2u_courses_category' === $linkbox->link_type ? $linkbox->link_addon_id : ''], 1, false, $readonly);
                            }

                            $options_categories = [];
                            foreach (TobiasKrais\D2ULinkbox\Category::getAll((int) rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()), false) as $category) {
                                $options_categories[$category->category_id] = $category->name;
                            }
                            \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_categories', 'form[category_ids][]', $options_categories, count($linkbox->categories) > 0 ? array_keys($linkbox->categories) : [], 5, true, $readonly);
                            \TobiasKrais\D2UHelper\BackendHelper::form_input('header_priority', 'form[priority]', $linkbox->priority, true, $readonly, 'number');
                            \TobiasKrais\D2UHelper\BackendHelper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', 'online' === $linkbox->online_status, $readonly);
                        ?>
					</div>
				</fieldset>
				<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        $linkbox = new TobiasKrais\D2ULinkbox\Linkbox($entry_id, $rex_clang->getId());
                        $required = $rex_clang->getId() === (int) (rex_config::get('d2u_helper', 'default_lang')) ? true : false;

                        $readonly_lang = true;
                        if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_linkbox[edit_lang]') && rex::getUser()->getComplexPerm('clang') instanceof rex_clang_perm && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId())))) {
                            $readonly_lang = false;
                        }
                ?>
					<fieldset>
						<legend><?= rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"' ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
                                if ($rex_clang->getId() !== (int) rex_config::get('d2u_helper', 'default_lang')) {
                                    $options_translations = [];
                                    $options_translations['yes'] = rex_i18n::msg('d2u_helper_translation_needs_update');
                                    $options_translations['no'] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
                                    $options_translations['delete'] = rex_i18n::msg('d2u_helper_translation_delete');
                                    \TobiasKrais\D2UHelper\BackendHelper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$linkbox->translation_needs_update], 1, false, $readonly_lang);
                                } else {
                                    echo '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
                                }
                            ?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?= $rex_clang->getId() ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?= $rex_clang->getId() ?>);
								});
							</script>
							<div id="details_clang_<?= $rex_clang->getId() ?>">
								<?php
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_linkbox_title', 'form[lang]['. $rex_clang->getId() .'][title]', $linkbox->title, $required, $readonly_lang);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_textarea('d2u_linkbox_teaser', 'form[lang]['. $rex_clang->getId() .'][teaser]', $linkbox->teaser, 3, false, $readonly_lang, true);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_linkbox_picture_lang', 'picture_lang'. $rex_clang->getId(), $linkbox->picture_lang, $readonly_lang);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_mediafield('d2u_linkbox_document_lang', 'document_lang'. $rex_clang->getId(), $linkbox->document_lang, $readonly_lang);
                                    \TobiasKrais\D2UHelper\BackendHelper::form_input('d2u_linkbox_external_url_lang', 'form[lang]['. $rex_clang->getId() .'][external_url_lang]', $linkbox->external_url_lang, false, $readonly_lang);
                                ?>
							</div>
						</div>
					</fieldset>
				<?php
                    }
                ?>
			</div>
			<script>
				function changeType() {
					$('#LINK_1').hide();
					$('#MEDIA_2').hide();
					<?php
                    foreach (rex_clang::getAll() as $rex_clang) {
                        echo "$('#MEDIA_document_lang". $rex_clang->getId() ."').hide();";
                        echo "$('#form\\\\[lang\\\\]\\\\[". $rex_clang->getId() ."\\\\]\\\\[external_url_lang\\\\]').hide();";
                    }
                    ?>
					$('#form\\[external_url\\]').hide();
					$('#form\\[d2u_immo_property_id\\]').hide();
					$('#form\\[d2u_machinery_industry_sector_id\\]').hide();
					$('#form\\[d2u_machinery_machine_id\\]').hide();
					$('#form\\[d2u_machinery_used_machine_id\\]').hide();
					$('#form\\[d2u_courses_category_id\\]').hide();

					if($('select[name="form\\[link_type\\]"]').val() === "article") {
						$('#LINK_1').show();
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "document") {
						$('#MEDIA_2').show();
						<?php
                        foreach (rex_clang::getAll() as $rex_clang) {
                            echo "$('#MEDIA_document_lang". $rex_clang->getId() ."').show();";
                        }
                        ?>
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "url") {
						$('#form\\[external_url\\]').show();
						<?php
                        foreach (rex_clang::getAll() as $rex_clang) {
                            echo "$('#form\\\\[lang\\\\]\\\\[". $rex_clang->getId() ."\\\\]\\\\[external_url_lang\\\\]').show();";
                        }
                        ?>
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "d2u_immo_property") {
						$('#form\\[d2u_immo_property_id\\]').show();
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "d2u_machinery_industry_sector") {
						$('#form\\[d2u_machinery_industry_sector_id\\]').show();
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "d2u_machinery_machine") {
						$('#form\\[d2u_machinery_machine_id\\]').show();
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "d2u_machinery_used_machine") {
						$('#form\\[d2u_machinery_used_machine_id\\]').show();
					}
					else if($('select[name="form\\[link_type\\]"]').val() === "d2u_courses_category") {
						$('#form\\[d2u_courses_category_id\\]').show();
					}
				}

				// On init
				changeType();
				// On change
				$('select[name="form\\[link_type\\]"]').on('change', function() {
					changeType();
				});
			</script>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?= rex_i18n::msg('form_save') ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?= rex_i18n::msg('form_apply') ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?= rex_i18n::msg('form_abort') ?></button>
						<?php
                            if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_linkbox[edit_data]'))) {
                                echo '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
                            }
                        ?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
        echo \TobiasKrais\D2UHelper\BackendHelper::getCSS();
        echo \TobiasKrais\D2UHelper\BackendHelper::getJS();
}

if ('' === $func) {
    $query = 'SELECT linkbox.box_id, title, category_ids, priority, online_status '
        . 'FROM '. rex::getTablePrefix() .'d2u_linkbox AS linkbox '
        . 'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
            . 'ON linkbox.box_id = lang.box_id AND lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()) .' ';
    if ('priority' === rex_config::get('d2u_linkbox', 'default_sort')) {
        $query .= 'ORDER BY priority ASC';
    } else {
        $query .= 'ORDER BY title ASC';
    }
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-window-maximize"></i>';
    $thIcon = '';
    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_linkbox[edit_data]'))) {
        $thIcon = '<a href="'. $list->getUrl(['func' => 'add']) .'" title="'. rex_i18n::msg('add') .'"><i class="rex-icon rex-icon-add-module"></i></a>';
    }
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###box_id###']);

    $list->setColumnLabel('box_id', rex_i18n::msg('id'));
    $list->setColumnLayout('box_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('title', rex_i18n::msg('d2u_linkbox_title'));
    $list->setColumnParams('title', ['func' => 'edit', 'entry_id' => '###box_id###']);

    $list->setColumnLabel('category_ids', rex_i18n::msg('d2u_helper_categories'));
    $list->setColumnFormat('category_ids', 'custom', static function ($params) {
        $list_params = $params['list'];
        $cat_names = [];
        $category_ids_unfilterd = preg_grep('/^\s*$/s', explode('|', (string) $list_params->getValue('category_ids')), PREG_GREP_INVERT);
        $category_ids = is_array($category_ids_unfilterd) ? array_map('intval', $category_ids_unfilterd) : [];
        foreach ($category_ids as $category_id) {
            $category = new TobiasKrais\D2ULinkbox\Category($category_id, (int) rex_config::get('d2u_helper', 'default_lang'));
            $cat_names[] = $category->name;
        }
        return implode(', ', $cat_names);
    });

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###box_id###']);

    $list->removeColumn('online_status');
    if (rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_linkbox[edit_data]'))) {
        $list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###box_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
        $list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

        $list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
        $list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###box_id###']);

        $list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
        $list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
        $list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###box_id###']);
        $list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
    }

    $list->setNoRowsMessage(rex_i18n::msg('d2u_linkbox_no_linkboxes_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_linkbox'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}
