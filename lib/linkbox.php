<?php
namespace D2U_Linkbox;

/**
 * Linkbox details
 */
class Linkbox implements \D2U_Helper\ITranslationHelper {
	/**
	 * @var int Database linkbox ID
	 */
	var $box_id = 0;
	
	/**
	 * @var int Redaxo language ID
	 */
	var $clang_id = 0;
	
	/**
	 * @var string Picture name
	 */
	var $picture = "";
	
	/**
	 * @var string Language specific preview picture file name 
	 */
	var $picture_lang = "";
	
	/**
	 * @var string Background color (hex)
	 */
	var $background_color = "";
	
	/**
	 * @var string Link type
	 */
	var $link_type = "";
	
	/**
	 * @var string Document file name
	 */
	var $document = "";
	
	/**
	 * @var string Language specific document file name
	 */
	var $document_lang = "";
	
	/**
	 * @var int Redaxo article ID for link
	 */
	var $article_id = 0;
	
	/**
	 * @var int ID for link to data (e.g. machine_id) from D2U Addons
	 */
	var $link_addon_id = 0;
	
	/**
	 * @var string external URL
	 */
	var $external_url = "";
	
	/**
	 * @var string Language specific external URL
	 */
	var $external_url_lang = "";
	
	/**
	 * @var string Online status "online" or "offline".
	 */
	var $online_status = "offline";
	
	/**
	 * @var int Sort Priority
	 */
	var $priority = 0;
	
	/**
	 * @var Category[] Array with categories, linkbox belongs to
	 */
	var $categories = [];
	
	/**
	 * @var string Box title
	 */
	var $title = "";
	
	/**
	 * @var string Box teaser
	 */
	var $teaser = "";
	
	/**
	 * @var string "yes" if translation needs update
	 */
	var $translation_needs_update = "delete";
	
	/**
	 * @var string link
	 */
	private $link = "";
	
	/**
	 * Constructor
	 * @param int $box_id Linkbox ID.
	 * @param int $clang_id Redaxo language ID
	 */
	public function __construct($box_id, $clang_id) {
		$this->clang_id = $clang_id;
		if($box_id > 0) { 
			$query = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_linkbox AS linkbox "
					."LEFT JOIN ". \rex::getTablePrefix() ."d2u_linkbox_lang AS lang "
						."ON linkbox.box_id = lang.box_id "
					."WHERE linkbox.box_id = ". $box_id ." "
						."AND clang_id = ". $clang_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			if ($result->getRows() > 0) {
				$this->box_id = $result->getValue("box_id");
				$this->link_type = $result->getValue("link_type");
				$this->article_id = $result->getValue("article_id");
				$this->document = $result->getValue("document");
				$this->document_lang = $result->getValue("document_lang");
				$this->link_addon_id = $result->getValue("link_addon_id");
				$this->external_url = $result->getValue("external_url");
				$this->external_url_lang = $result->getValue("external_url_lang");
				$this->title = stripslashes($result->getValue("title"));
				$this->teaser = stripslashes($result->getValue("teaser"));
				$this->picture = $result->getValue("picture");
				$this->picture_lang = $result->getValue("picture_lang");
				$this->background_color = $result->getValue("background_color");
				$this->priority = $result->getValue("priority");
				$category_ids = preg_grep('/^\s*$/s', explode("|", $result->getValue("category_ids")), PREG_GREP_INVERT);
				foreach ($category_ids as $category_id) {
					$this->categories[$category_id] = new Category($category_id, $clang_id);
				}
				$this->online_status = $result->getValue("online_status");
				$this->translation_needs_update = $result->getValue("translation_needs_update");
			}
		}
		else {
			return $this;
		}
	}
	
	/**
	 * Changes the online status of this object
	 */
	public function changeStatus() {
		if($this->online_status == "online") {
			if($this->box_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_linkbox "
					."SET online_status = 'offline' "
					."WHERE box_id = ". $this->box_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "offline";
		}
		else {
			if($this->box_id > 0) {
				$query = "UPDATE ". \rex::getTablePrefix() ."d2u_linkbox "
					."SET online_status = 'online' "
					."WHERE box_id = ". $this->box_id;
				$result = \rex_sql::factory();
				$result->setQuery($query);
			}
			$this->online_status = "online";			
		}
	}
	
	/**
	 * Deletes the object in all languages.
	 * @param int $delete_all If TRUE, all translations and main object are deleted. If 
	 * FALSE, only this translation will be deleted.
	 */
	public function delete($delete_all = TRUE) {
		$query_lang = "DELETE FROM ". \rex::getTablePrefix() ."d2u_linkbox_lang "
			."WHERE box_id = ". $this->box_id
			. ($delete_all ? '' : ' AND clang_id = '. $this->clang_id) ;
		$result_lang = \rex_sql::factory();
		$result_lang->setQuery($query_lang);
		
		// If no more lang objects are available, delete
		$query_main = "SELECT * FROM ". \rex::getTablePrefix() ."d2u_linkbox_lang "
			."WHERE box_id = ". $this->box_id;
		$result_main = \rex_sql::factory();
		$result_main->setQuery($query_main);
		if(intval($result_main->getRows()) === 0) {
			$query = "DELETE FROM ". \rex::getTablePrefix() ."d2u_linkbox "
				."WHERE box_id = ". $this->box_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);

			// reset priorities
			$this->setPriority(TRUE);			
		}
	}
	

	/**
	 * Create an empty object instance.
	 * @return Linkbox empty new object
	 */
	 public static function factory() {
		 return new Linkbox(0, 0);
	}

	/**
	 * Get all linkboxes
	 * @param int $clang_id Redaxo language ID
	 * @param int $category_id Category ID if only linkbox of that category should be returned.
	 * @param boolean $online_only If only online linkbox should be returned TRUE, otherwise FALSE
	 * @return Linkbox[] Array with linkbox objects
	 */
	public static function getAll($clang_id, $category_id = 0, $online_only = TRUE) {
		$query = "SELECT lang.box_id FROM ". \rex::getTablePrefix() ."d2u_linkbox_lang AS lang "
				."LEFT JOIN ". \rex::getTablePrefix() ."d2u_linkbox AS linkbox "
					."ON lang.box_id = linkbox.box_id "
				."WHERE clang_id = ". $clang_id;
		if($online_only) {
			$query .= " AND online_status = 'online'";
		}
		if($category_id > 0) {
			$query .= " AND category_ids LIKE '%|". $category_id ."|%'";
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
			$linkbox[$result->getValue('box_id')] = new Linkbox($result->getValue('box_id'), $clang_id);
			$result->next();
		}
		
		return $linkbox;
	}

	/**
	 * Get objects concerning translation updates
	 * @param int $clang_id Redaxo language ID
	 * @param string $type 'update' or 'missing'
	 * @return Linkbox[] Array with Linkbox objects.
	 */
	public static function getTranslationHelperObjects($clang_id, $type) {
		$query = 'SELECT lang.box_id FROM '. \rex::getTablePrefix() .'d2u_linkbox_lang AS lang '
				.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_linkbox AS main '
					.'ON lang.box_id = main.box_id '
				."WHERE clang_id = ". $clang_id ." AND translation_needs_update = 'yes' "
				.'ORDER BY title';
		if($type == 'missing') {
			$query = 'SELECT main.box_id FROM '. \rex::getTablePrefix() .'d2u_linkbox AS main '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_linkbox_lang AS target_lang '
						.'ON main.box_id = target_lang.box_id AND target_lang.clang_id = '. $clang_id .' '
					.'LEFT JOIN '. \rex::getTablePrefix() .'d2u_linkbox_lang AS default_lang '
						.'ON main.box_id = default_lang.box_id AND default_lang.clang_id = '. \rex_config::get('d2u_helper', 'default_lang') .' '
					."WHERE target_lang.box_id IS NULL "
					.'ORDER BY default_lang.title';
			$clang_id = \rex_config::get('d2u_helper', 'default_lang');
		}
		$result = \rex_sql::factory();
		$result->setQuery($query);

		$objects = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$objects[] = new Linkbox($result->getValue("box_id"), $clang_id);
			$result->next();
		}
		
		return $objects;
    }
	
	/**
	 * Get link
	 * @return string Link URL
	 */
	public function getUrl() {
		if($this->link != "") {
			return $this->link;
		}

		if($this->link_type == "document" && ($this->document != "" || $this->document_lang != "")) {
			$this->link = \rex_url::media($this->document_lang != "" ? $this->document_lang : $this->document);
		}
		else if($this->link_type == "url" && ($this->external_url != "" || $this->external_url_lang != "")) {
			$this->link = $this->external_url_lang != "" ? $this->external_url_lang : $this->external_url;
		}
		else if($this->link_type == "d2u_immo_property" && $this->link_addon_id > 0 && \rex_addon::get('d2u_immo')->isAvailable()) {
			$property = new \D2U_Immo\Property($this->link_addon_id, $this->clang_id);
			$this->link = $property->getURL();
		}
		else if($this->link_addon_id > 0 && \rex_addon::get('d2u_machinery')->isAvailable()) {
			if($this->link_type == "d2u_immo_property" && \rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
				$industry_sector = new \IndustrySector($this->link_addon_id, $this->clang_id);
				$this->link = $industry_sector->getURL();
			}
			elseif ($this->link_type == "d2u_machinery_machine") {
				$machine = new \Machine($this->link_addon_id, $this->clang_id);
				$this->link = $machine->getURL();
			}
			if($this->link_type == "d2u_machinery_used_machine" && \rex_plugin::get('d2u_machinery', 'industry_sectors')->isAvailable()) {
				$used_machine = new \UsedMachine($this->link_addon_id, $this->clang_id);
				$this->link = $used_machine->getURL();
			}
		}
		else if($this->link_type == "d2u_courses_category" && $this->link_addon_id > 0 && \rex_addon::get('d2u_courses')->isAvailable()) {
			$category = new \D2U_Courses\Category($this->link_addon_id);
			$this->link = $category->getURL();
		}
		else if(($this->link_type == "" || $this->link_type == "article") && $this->article_id > 0) {
			$this->link = \rex_getUrl($this->article_id);
		}
		
		return $this->link;
	}
	
	/**
	 * Updates or inserts the object into database.
	 * @return boolean TRUE if successful
	 */
	public function save() {
		$error = FALSE;

		// Save the not language specific part
		$pre_save_linkbox = new Linkbox($this->box_id, $this->clang_id);

		// save priority, but only if new or changed
		if($this->priority != $pre_save_linkbox->priority || $this->box_id == 0) {
			$this->setPriority();
		}

		if($this->box_id == 0 || $pre_save_linkbox != $this) {
			$query = \rex::getTablePrefix() ."d2u_linkbox SET "
					."link_type = '". $this->link_type ."', "
					."article_id = ". ($this->article_id ?: 0) .", "
					."document = '". $this->document ."', "
					."link_addon_id = ". ($this->link_addon_id ?: 0) .", "
					."external_url = '". $this->external_url ."', "
					."category_ids = '|". implode("|", array_keys($this->categories)) ."|', "
					."picture = '". $this->picture ."', "
					."background_color = '". $this->background_color ."', "
					."priority = ". $this->priority .", "
					."online_status = '". $this->online_status ."' ";

			if($this->box_id == 0) {
				$query = "INSERT INTO ". $query;
			}
			else {
				$query = "UPDATE ". $query ." WHERE box_id = ". $this->box_id;
			}
			$result = \rex_sql::factory();
			$result->setQuery($query);
			if($this->box_id == 0) {
				$this->box_id = $result->getLastId();
				$error = $result->hasError();
			}
		}

		if(!$error) {
			// Save the language specific part
			$pre_save_linkbox = new Linkbox($this->box_id, $this->clang_id);
			if($pre_save_linkbox != $this) {
				$query = "REPLACE INTO ". \rex::getTablePrefix() ."d2u_linkbox_lang SET "
						."box_id = '". $this->box_id ."', "
						."clang_id = '". $this->clang_id ."', "
						."picture_lang = '". $this->picture_lang ."', "
						."document_lang = '". $this->document_lang ."', "
						."external_url_lang = '". $this->external_url_lang ."', "
						."title = '". addslashes($this->title) ."', "
						."teaser = '". addslashes($this->teaser) ."', "
						."translation_needs_update = '". $this->translation_needs_update ."' ";
				$result = \rex_sql::factory();
				$result->setQuery($query);
				$error = $result->hasError();
			}
		}
		
		return $error;
	}
		
	/**
	 * Reassigns priorities in database.
	 * @param boolean $delete Reorder priority after deletion
	 */
	private function setPriority($delete = FALSE) {
		// Pull prios from database
		$query = "SELECT box_id, priority FROM ". \rex::getTablePrefix() ."d2u_linkbox "
			."WHERE box_id <> ". $this->box_id ." ORDER BY priority";
		$result = \rex_sql::factory();
		$result->setQuery($query);
		
		// When priority is too small, set at beginning
		if($this->priority <= 0) {
			$this->priority = 1;
		}
		
		// When prio is too high or was deleted, simply add at end 
		if($this->priority > $result->getRows() || $delete) {
			$this->priority = $result->getRows() + 1;
		}

		$linkboxes = [];
		for($i = 0; $i < $result->getRows(); $i++) {
			$linkboxes[$result->getValue("priority")] = $result->getValue("box_id");
			$result->next();
		}
		array_splice($linkboxes, ($this->priority - 1), 0, array($this->box_id));

		// Save all prios
		foreach($linkboxes as $prio => $box_id) {
			$query = "UPDATE ". \rex::getTablePrefix() ."d2u_linkbox "
					."SET priority = ". ($prio + 1) ." " // +1 because array_splice recounts at zero
					."WHERE box_id = ". $box_id;
			$result = \rex_sql::factory();
			$result->setQuery($query);
		}
	}
}