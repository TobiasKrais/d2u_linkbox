<?php
namespace D2U_Linkbox;

/**
 * Category class
 */
class Category {
	/**
	 * @var int Database ID
	 */
	public int $category_id = 0;
	
	/**
	 * @var int Redaxo language ID
	 */
	public int $clang_id = 0;
	
	/**
	 * @var string Name
	 */
	public string $name = "";

	/**
	 * Constructor
	 * @param int $category_id Category ID.
	 * @param int $clang_id Redaxo language ID.
	 */
	public function __construct($category_id, $clang_id) {
		$this->clang_id = $clang_id;
		$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_linkbox_categories "
				."WHERE category_id = ". $category_id;
		$result = \rex_sql::factory();
		$result->setQuery($query);

		if ($result->getRows() > 0) {
			$this->category_id = (int) $result->getValue("category_id");
			$this->name = stripslashes((string) $result->getValue("name"));
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 */
	public function delete() {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_linkbox_categories "
			."WHERE category_id = ". $this->category_id;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
	}
	
	/**
	 * Get all categories.
	 * @param int $clang_id Redaxo clang id.
	 * @param boolean $ignoreOfflines Ignore offline categories
	 * @return Category[] Array with Category objects.
	 */
	public static function getAll($clang_id, $ignoreOfflines = true) {
		$query = "SELECT category_id FROM ". \rex::getTablePrefix() ."d2u_linkbox_categories "
			.'ORDER BY name';
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$categories = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			if($ignoreOfflines) {
				$query_check_offline = "SELECT lang.box_id FROM ". \rex::getTablePrefix() ."d2u_linkbox_lang AS lang "
					."LEFT JOIN ". \rex::getTablePrefix() ."d2u_linkbox AS linkbox "
						."ON lang.box_id = linkbox.box_id AND lang.clang_id = ". $clang_id ." "
					."WHERE category_ids LIKE '%|". $result->getValue("category_id") ."|%'";

				$result_check_offline = \rex_sql::factory();
				$result_check_offline->setQuery($query_check_offline);
				if($result_check_offline->getRows() > 0) {
					$categories[$result->getValue("category_id")] = new Category($result->getValue("category_id"), $clang_id);
				}
			}
			else {
				$categories[$result->getValue("category_id")] = new Category($result->getValue("category_id"), $clang_id);
			}
			$result->next();
		}
		return $categories;
	}

	/**
	 * Get the linkboxes of the category.
	 * @param boolean $only_online Show only online linkbox
	 * @return Linkbox[] Linkboxes in this category
	 */
	public function getLinkboxes($only_online = false) {
		$query = "SELECT lang.box_id FROM ". \rex::getTablePrefix() ."d2u_linkbox_lang AS lang "
			."LEFT JOIN ". \rex::getTablePrefix() ."d2u_linkbox AS linkbox "
					."ON lang.box_id = linkbox.box_id "
			."WHERE category_ids LIKE '%|". $this->category_id ."|%' AND clang_id = ". $this->clang_id ." ";
		if($only_online) {
			$query .= "AND online_status = 'online' ";
		}
		if(\rex_config::get('d2u_linkbox', 'default_sort', 'name') == 'name') {
			$query .= ' ORDER BY title';
		}
		else {
			$query .= ' ORDER BY priority';
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		$linkbox = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$linkbox[] = new Linkbox($result->getValue("box_id"), $this->clang_id);
			$result->next();
		}
		return $linkbox;
	}

	/**
	 * Updates or inserts the object into database.
	 * @return boolean true if successful
	 */
	public function save() {
		$error = true;

		// Save the not language specific part
		$pre_save_category = new Category($this->category_id, $this->clang_id);

		if($this->category_id === 0 || $pre_save_category != $this) {
			$query = \rex::getTablePrefix() ."d2u_linkbox_categories SET "
					."name = '". addslashes($this->name) ."' ";

			if($this->category_id === 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE category_id = ". $this->category_id;
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->category_id === 0) {
				$this->category_id = intval($result->getLastId());
				$error = !$result->hasError();
			}
		}

		return $error;
	}
}