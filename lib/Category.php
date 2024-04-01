<?php

namespace TobiasKrais\D2ULinkbox;

use rex;
use rex_config;
use rex_sql;

/**
 * Category class.
 */
class Category
{
    /** @var int Database ID */
    public int $category_id = 0;

    /** @var int Redaxo language ID */
    private int $clang_id = 0;

    /** @var string Name */
    public string $name = '';

    /**
     * Constructor.
     * @param int $category_id category ID
     * @param int $clang_id redaxo language ID
     */
    public function __construct($category_id, $clang_id)
    {
        $this->clang_id = $clang_id;
        $query = 'SELECT * FROM '. rex::getTablePrefix() .'d2u_linkbox_categories '
                .'WHERE category_id = '. $category_id;
        $result = rex_sql::factory();
        $result->setQuery($query);

        if ($result->getRows() > 0) {
            $this->category_id = (int) $result->getValue('category_id');
            $this->name = stripslashes((string) $result->getValue('name'));
        }
    }

    /**
     * Deletes the object in all languages.
     */
    public function delete(): void
    {
        $query_lang = 'DELETE FROM '. rex::getTablePrefix() .'d2u_linkbox_categories '
            .'WHERE category_id = '. $this->category_id;
        $result_lang = rex_sql::factory();
        $result_lang->setQuery($query_lang);
    }

    /**
     * Get all categories.
     * @param int $clang_id redaxo clang id
     * @param bool $ignoreOfflines Ignore offline categories
     * @return Category[] array with Category objects
     */
    public static function getAll($clang_id, $ignoreOfflines = true)
    {
        $query = 'SELECT category_id FROM '. rex::getTablePrefix() .'d2u_linkbox_categories '
            .'ORDER BY name';
        $result = rex_sql::factory();
        $result->setQuery($query);

        $categories = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            if ($ignoreOfflines) {
                $query_check_offline = 'SELECT lang.box_id FROM '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
                    .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox AS linkbox '
                        .'ON lang.box_id = linkbox.box_id AND lang.clang_id = '. $clang_id .' '
                    ."WHERE category_ids LIKE '%|". $result->getValue('category_id') ."|%'";

                $result_check_offline = rex_sql::factory();
                $result_check_offline->setQuery($query_check_offline);
                if ($result_check_offline->getRows() > 0) {
                    $categories[(int) $result->getValue('category_id')] = new self((int) $result->getValue('category_id'), $clang_id);
                }
            } else {
                $categories[(int) $result->getValue('category_id')] = new self((int) $result->getValue('category_id'), $clang_id);
            }
            $result->next();
        }
        return $categories;
    }

    /**
     * Get the linkboxes of the category.
     * @param bool $only_online Show only online linkbox
     * @return Linkbox[] Linkboxes in this category
     */
    public function getLinkboxes($only_online = false)
    {
        $query = 'SELECT lang.box_id FROM '. rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
            .'LEFT JOIN '. rex::getTablePrefix() .'d2u_linkbox AS linkbox '
                    .'ON lang.box_id = linkbox.box_id '
            ."WHERE category_ids LIKE '%|". $this->category_id ."|%' AND clang_id = ". $this->clang_id .' ';
        if ($only_online) {
            $query .= "AND online_status = 'online' ";
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
            $linkbox[] = new Linkbox((int) $result->getValue('box_id'), $this->clang_id);
            $result->next();
        }
        return $linkbox;
    }

    /**
     * Updates or inserts the object into database.
     * @return bool true if successful
     */
    public function save()
    {
        $error = true;

        // Save the not language specific part
        $pre_save_category = new self($this->category_id, $this->clang_id);

        if (0 === $this->category_id || $pre_save_category !== $this) {
            $query = rex::getTablePrefix() .'d2u_linkbox_categories SET '
                    ."name = '". addslashes($this->name) ."' ";

            if (0 === $this->category_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE category_id = '. $this->category_id;
            }
            $result = rex_sql::factory();
            $result->setQuery($query);
            if (0 === $this->category_id) {
                $this->category_id = (int) $result->getLastId();
                $error = !$result->hasError();
            }
        }

        return $error;
    }
}

namespace D2U_Linkbox;

/** @deprecated Since 1.5.0, to be removed in 2.0.0. Use \TobiasKrais\D2ULinkbox\Category instead. */
class Category extends \TobiasKrais\D2ULinkbox\Category {}
