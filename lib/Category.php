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
    public function getLinkboxes($only_online = false): array
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

        $linkboxes = [];
        for ($i = 0; $i < $result->getRows(); ++$i) {
            $linkboxes[] = new Linkbox((int) $result->getValue('box_id'), $this->clang_id);
            $result->next();
        }

        return $linkboxes;
    }

    /**
     * Deletes this category together with its linkboxes. Linkboxes assigned only
     * to this category are removed completely; linkboxes shared with other
     * categories keep existing and only lose the reference to this category.
     */
    public function deleteWithLinkboxes(): void
    {
        $sql = rex_sql::factory();
        $sql->setQuery('SELECT box_id, clang_id, category_ids FROM '. rex::getTablePrefix() ."d2u_linkbox_lang WHERE category_ids LIKE '%|". $this->category_id ."|%'");
        $rows = [];
        for ($i = 0; $i < $sql->getRows(); ++$i) {
            $rows[] = [
                'box_id' => (int) $sql->getValue('box_id'),
                'clang_id' => (int) $sql->getValue('clang_id'),
                'category_ids' => (string) $sql->getValue('category_ids'),
            ];
            $sql->next();
        }

        foreach ($rows as $row) {
            $ids = array_values(array_filter(array_map('intval', explode('|', $row['category_ids']))));
            $remaining = array_values(array_diff($ids, [$this->category_id]));
            if (0 === count($remaining)) {
                // Linkbox belongs only to this category: remove this language row (and the
                // box itself once no language rows remain).
                (new Linkbox($row['box_id'], $row['clang_id']))->delete(false);
            } else {
                // Keep the shared linkbox, only unassign this category.
                $update = rex_sql::factory();
                $update->setQuery('UPDATE '. rex::getTablePrefix() .'d2u_linkbox_lang SET category_ids = :ids WHERE box_id = :box AND clang_id = :clang', [
                    ':ids' => '|'. implode('|', $remaining) .'|',
                    ':box' => $row['box_id'],
                    ':clang' => $row['clang_id'],
                ]);
            }
        }

        $this->delete();
    }

    /**
     * Sanitizes a table or column identifier from the usage-detection JSON.
     * @return string the identifier if it is a safe alnum/underscore name, otherwise ''
     */
    private static function safeIdentifier(string $identifier): string
    {
        return 1 === preg_match('/^[a-zA-Z0-9_]+$/', $identifier) ? $identifier : '';
    }

    /**
     * Checks whether a category id is referenced by any of the module usage tables
     * configured in the 'category_usage_tables' setting (JSON). Without a valid
     * configuration nothing is considered used.
     * @param int $category_id category id to look for
     */
    public static function isUsedInModules(int $category_id): bool
    {
        $config = trim((string) rex_config::get('d2u_linkbox', 'category_usage_tables', ''));
        if ('' === $config) {
            return false;
        }
        $tables = json_decode($config, true);
        if (!is_array($tables)) {
            return false;
        }

        foreach ($tables as $table_config) {
            if (!is_array($table_config)) {
                continue;
            }
            $table = self::safeIdentifier((string) ($table_config['table'] ?? ''));
            $field = self::safeIdentifier((string) ($table_config['field'] ?? ''));
            if ('' === $table || '' === $field) {
                continue;
            }
            $db_table = str_starts_with($table, rex::getTablePrefix()) ? $table : rex::getTable($table);

            $where = '';
            $raw_where = trim((string) ($table_config['where'] ?? ''));
            if ('' !== $raw_where && 1 === preg_match('/^[a-zA-Z0-9_]+\s*=\s*[a-zA-Z0-9_]+$/', $raw_where)) {
                $where = ' AND '. $raw_where;
            }

            $sql = rex_sql::factory();
            $sql->setQuery('SELECT 1 FROM `'. $db_table .'` '
                .'WHERE (`'. $field .'` = :id OR FIND_IN_SET(:id, `'. $field .'`) OR `'. $field .'` LIKE :pipe)'. $where .' LIMIT 1', [
                    ':id' => (string) $category_id,
                    ':pipe' => '%|'. $category_id .'|%',
                ]);
            if ($sql->getRows() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns all categories that are not referenced by any configured module usage.
     * @param int $clang_id redaxo clang id
     * @return Category[] unused categories keyed by category id
     */
    public static function getUnusedCategories($clang_id): array
    {
        $unused = [];
        foreach (self::getAll($clang_id, false) as $category) {
            if (!self::isUsedInModules($category->category_id)) {
                $unused[$category->category_id] = $category;
            }
        }
        return $unused;
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
                    .'name = :name ';

            if (0 === $this->category_id) {
                $query = 'INSERT INTO '. $query;
            } else {
                $query = 'UPDATE '. $query .' WHERE category_id = '. (int) $this->category_id;
            }
            $result = rex_sql::factory();
            $result->setQuery($query, [':name' => $this->name]);
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
