<?php

namespace TobiasKrais\D2ULinkbox;

use IndustrySector;
use Machine;
use rex;
use rex_addon;
use rex_config;
use rex_plugin;
use rex_sql;
use rex_url;
use UsedMachine;

use function is_array;

/**
 * @api
 * Linkbox details
 */
class Linkbox implements \TobiasKrais\D2UHelper\ITranslationHelper
{
    /** @var int Database linkbox ID */
    public int $box_id = 0;

    /** @var int Redaxo language ID */
    public int $clang_id = 0;

    /** @var string Picture name */
    public string $picture = '';

    /** @var string Language specific preview picture file name */
    public string $picture_lang = '';

    /** @var string Background color (hex) */
    public string $background_color = '';

    /** @var string Link type */
    public string $link_type = '';

    /** @var string Document file name */
    public string $document = '';

    /** @var string Language specific document file name */
    public string $document_lang = '';

    /** @var int Redaxo article ID for link */
    public int $article_id = 0;

    /** @var int ID for link to data (e.g. machine_id) from D2U Addons */
    public int $link_addon_id = 0;

    /** @var string external URL */
    public string $external_url = '';

    /** @var string Language specific external URL */
    public string $external_url_lang = '';

    /** @var string online status "online" or "offline" */
    public string $online_status = 'offline';

    /** @var int Sort Priority */
    public int $priority = 0;

    /** @var array<Category> Array with categories, linkbox belongs to */
    public array $categories = [];

    /** @var string Box title */
    public string $title = '';

    /** @var string Box teaser */
    public string $teaser = '';

    /** @var string "yes" if translation needs update */
    public string $translation_needs_update = 'delete';

    /** @var string link */
    private string $link = '';

    /**
     * Constructor.
     * @param int $box_id linkbox ID
     * @param int $clang_id Redaxo language ID
     */
    public function __construct($box_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        if ($box_id > 0) {
            $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_linkbox AS linkbox '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
                        .'ON linkbox.box_id = lang.box_id '
                    .'WHERE linkbox.box_id = '. $box_id .' '
                        .'AND clang_id = '. $clang_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            if ($result->getRows() > 0) {
                $this->box_id = (int) $result->getValue('box_id');
                $this->link_type = (string) $result->getValue('link_type');
                $this->article_id = (int) $result->getValue('article_id');
                $this->document = (string) $result->getValue('document');
                $this->document_lang = (string) $result->getValue('document_lang');
                $this->link_addon_id = (int) $result->getValue('link_addon_id');
                $this->external_url = (string) $result->getValue('external_url');
                $this->external_url_lang = (string) $result->getValue('external_url_lang');
                $this->title = stripslashes((string) $result->getValue('title'));
                $this->teaser = stripslashes((string) $result->getValue('teaser'));
                $this->picture = (string) $result->getValue('picture');
                $this->picture_lang = (string) $result->getValue('picture_lang');
                $this->background_color = (string) $result->getValue('background_color');
                $this->priority = (int) $result->getValue('priority');
                $category_ids_unmapped = preg_grep('/^\s*$/s', explode('|', (string) $result->getValue('category_ids')), PREG_GREP_INVERT);
                $category_ids = is_array($category_ids_unmapped) ? array_map('intval', $category_ids_unmapped) : [];
                foreach ($category_ids as $category_id) {
                    $this->categories[$category_id] = new Category($category_id, $clang_id);
                }
                $this->online_status = (string) $result->getValue('online_status');
                $this->translation_needs_update = (string) $result->getValue('translation_needs_update');
            }
        }
    }

    /**
     * Changes the online status of this object.
     */
    public function changeStatus(): void
    {
        if ('online' === $this->online_status) {
            if ($this->box_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_linkbox '
                    ."SET online_status = 'offline' "
                    .'WHERE box_id = '. $this->box_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'offline';
        } else {
            if ($this->box_id > 0) {
                $query = 'UPDATE '. rex::getTablePrefix() .'d2u_linkbox '
                    ."SET online_status = 'online' "
                    .'WHERE box_id = '. $this->box_id;
                $result = rex_sql::factory();
                $result->setQuery($query);
            }
            $this->online_status = 'online';
        }
    }

    /**
     * Deletes the object in all languages.
     * @param bool $delete_all If true, all translations and main object are deleted. If
     * false, only this translation will be deleted.
     */
    public function delete($delete_all = true): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_linkbox_lang '
            .'WHERE box_id = '. $this->box_id
            . ($delete_all ? '' : ' AND clang_id = '. $this->clang_id);
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);

        // If no more lang objects are available, delete
        $query_main = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_linkbox_lang '
            .'WHERE box_id = '. $this->box_id;
        $result_main = rex_sql::factory();
        $result_main->setQuery($query_main);
        if (0 === (int) $result_main->getRows()) {
            $query = 'DELETE FROM '. rex::getTablePrefix() .'d2u_linkbox '
                .'WHERE box_id = '. $this->box_id;
            $result = rex_sql::factory();
            $result->setQuery($query);

            // reset priorities
            $this->setPriority(true);
        }
    }

     /**
      * @api
      * Create an empty object instance.
      * @return Linkbox empty new object
      */
     public static function factory()
     {
         return new self(0, 0);
     }

    /**
     * Get all linkboxes.
     * @param int $clang_id Redaxo language ID
     * @param int $category_id category ID if only linkbox of that category should be returned
     * @param bool $online_only If only online linkbox should be returned true, otherwise false
     * @return Linkbox[] Array with linkbox objects
     */
    public static function getAll($clang_id, $category_id = 0, $online_only = true)
    {
        $query = 'SELECT lang.box_id FROM '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox AS linkbox '
                    .'ON lang.box_id = linkbox.box_id '
                .'WHERE clang_id = '. $clang_id;
        if ($online_only) {
            $query .= " AND online_status = 'online'";
        }
        if ($category_id > 0) {
            $query .= " AND category_ids LIKE '%|". $category_id ."|%'";
        }
        if ('name' === rex_config::get('d2u_linkbox', 'default_sort', 'name')) {
            $query .= ' ORDER BY title';
        } else {
            $query .= ' ORDER BY priority';
        }

        $result = rex_sql::factory();
        $result->setQuery($query);

        $linkbox = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $linkbox[(int) $result->getValue('box_id')] = new self((int) $result->getValue('box_id'), $clang_id);
            $result->next();
        }

        return $linkbox;
    }

    /**
     * Get objects concerning translation updates.
     * @param int $clang_id Redaxo language ID
     * @param string $type 'update' or 'missing'
     * @return Linkbox[] array with Linkbox objects
     */
    public static function getTranslationHelperObjects($clang_id, $type)
    {
        $query = 'SELECT lang.box_id FROM '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
                .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox AS main '
                    .'ON lang.box_id = main.box_id '
                .'WHERE clang_id = '. $clang_id ." AND translation_needs_update = 'yes' "
                .'ORDER BY title';
        if ('missing' === $type) {
            $query = 'SELECT main.box_id FROM '. rex::getTablePrefix() .'d2u_linkbox AS main '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox_lang AS target_lang '
                        .'ON main.box_id = target_lang.box_id AND target_lang.clang_id = '. $clang_id .' '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox_lang AS default_lang '
                        .'ON main.box_id = default_lang.box_id AND default_lang.clang_id = '. rex_config::get('d2u_helper', 'default_lang') .' '
                    .'WHERE target_lang.box_id IS NULL '
                    .'ORDER BY default_lang.title';
            $clang_id = (int) rex_config::get('d2u_helper', 'default_lang');
        }
        $result = rex_sql::factory();
        $result->setQuery($query);

        $objects = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $objects[] = new self((int) $result->getValue('box_id'), $clang_id);
            $result->next();
        }

        return $objects;
    }

    /**
     * Get link.
     * @return string Link URL
     */
    public function getUrl()
    {
        if ('' !== $this->link) {
            return $this->link;
        }

        if ('document' === $this->link_type && ('' !== $this->document || '' !== $this->document_lang)) {
            $this->link = rex_url::media('' !== $this->document_lang ? $this->document_lang : $this->document);
        } elseif ('url' === $this->link_type && ('' !== $this->external_url || '' !== $this->external_url_lang)) {
            $this->link = '' !== $this->external_url_lang ? $this->external_url_lang : $this->external_url;
        } elseif ('d2u_immo_property' === $this->link_type && $this->link_addon_id > 0 && rex_addon::get('d2u_immo')->isAvailable()) {
            $property = new \D2U_Immo\Property($this->link_addon_id, $this->clang_id);
            $this->link = $property->getUrl();
        } elseif ($this->link_addon_id > 0 && rex_addon::get('d2u_machinery')->isAvailable()) {
            if ('d2u_immo_property' === $this->link_type && rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $industry_sector = new IndustrySector($this->link_addon_id, $this->clang_id);
                $this->link = $industry_sector->getUrl();
            } elseif ('d2u_machinery_machine' === $this->link_type) {
                $machine = new Machine($this->link_addon_id, $this->clang_id);
                $this->link = $machine->getUrl();
            }
            if ('d2u_machinery_used_machine' === $this->link_type && rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
                $used_machine = new UsedMachine($this->link_addon_id, $this->clang_id);
                $this->link = $used_machine->getUrl();
            }
        } elseif ('d2u_courses_category' === $this->link_type && $this->link_addon_id > 0 && rex_addon::get('d2u_courses')->isAvailable()) {
            $category = new \D2U_Courses\Category($this->link_addon_id);
            $this->link = $category->getUrl();
        } elseif (('' === $this->link_type || 'article' === $this->link_type) && $this->article_id > 0) {
            $this->link = rex_getUrl($this->article_id);
        }

        return $this->link;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = false;

        // Save the not language specific part
        $pre_save_linkbox = new self($this->box_id, $this->clang_id);

        // save priority, but only if new or changed
        if ($this->priority !== $pre_save_linkbox->priority || 0 === $this->box_id) {
            $this->setPriority();
        }

        if (0 === $this->box_id || $pre_save_linkbox !== $this) {
            $query = rex::getTablePrefix() .'d2u_linkbox SET '
                    ."link_type = '". $this->link_type ."', "
                    .'article_id = '. ($this->article_id > 0 ? $this->article_id : 0) .', '
                    ."document = '". $this->document ."', "
                    .'link_addon_id = '. ($this->link_addon_id > 0 ? $this->link_addon_id : 0) .', '
                    ."external_url = '". $this->external_url ."', "
                    ."category_ids = '|". implode('|', array_keys($this->categories)) ."|', "
                    ."picture = '". $this->picture ."', "
                    ."background_color = '". $this->background_color ."', "
                    .'priority = '. $this->priority .', '
                    ."online_status = '". $this->online_status ."' ";

            if (0 === $this->box_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE box_id = '. $this->box_id;
            }

            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->box_id) {
                $this->box_id = (int) $result->getLastId();
                $error = $result->hasError();
            }
        }

        if (!$error) {
            // Save the language specific part
            $pre_save_linkbox = new self($this->box_id, $this->clang_id);
            if ($pre_save_linkbox !== $this) {
                $query = 'REPLACE INTO '. rex::getTablePrefix() .'d2u_linkbox_lang SET '
                        ."box_id = '". $this->box_id ."', "
                        ."clang_id = '". $this->clang_id ."', "
                        ."picture_lang = '". $this->picture_lang ."', "
                        ."document_lang = '". $this->document_lang ."', "
                        ."external_url_lang = '". $this->external_url_lang ."', "
                        ."title = '". addslashes($this->title) ."', "
                        ."teaser = '". addslashes($this->teaser) ."', "
                        ."translation_needs_update = '". $this->translation_needs_update ."' ";
                $result = rex_sql::factory();
                $result->setQuery($query);
                $error = $result->hasError();
            }
        }

        return $error;
    }

    /**
     * Reassigns priorities in database.
     * @param bool $delete Reorder priority after deletion
     */
    private function setPriority($delete = false): void
    {
        // Pull prios from database
        $query = 'SELECT box_id, priority FROM '. rex::getTablePrefix() .'d2u_linkbox '
            .'WHERE box_id <> '. $this->box_id .' ORDER BY priority';
        $result = rex_sql::factory();
        $result->setQuery($query);

        // When priority is too small, set at beginning
        if ($this->priority <= 0) {
            $this->priority = 1;
        }

        // When prio is too high or was deleted, simply add at end
        if ($this->priority > $result->getRows() || $delete) {
            $this->priority = (int) $result->getRows() + 1;
        }

        $linkboxes = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $linkboxes[$result->getValue('priority')] = $result->getValue('box_id');
            $result->next();
        }
        array_splice($linkboxes, $this->priority - 1, 0, [$this->box_id]);

        // Save all prios
        foreach ($linkboxes as $prio => $box_id) {
            $query = 'UPDATE '. rex::getTablePrefix() .'d2u_linkbox '
                    .'SET priority = '. ((int) $prio + 1) .' ' // +1 because array_splice recounts at zero
                    .'WHERE box_id = '. $box_id;
            $result = rex_sql::factory();
            $result->setQuery($query);
        }
    }
}

namespace D2U_Linkbox;

/** @deprecated Since 1.5.0, to be removed in 2.0.0. Use \TobiasKrais\D2ULinkbox\Linkbox instead. */
class Linkbox extends \TobiasKrais\D2ULinkbox\Linkbox {}