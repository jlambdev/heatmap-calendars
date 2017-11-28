<?php

/*
 *  MODEL
 *
 *  Contains 'business logic' for the application and defines SQL quries for common operations
 *  Performs validation checks when operations are requested by the Controller scripts
 *
 *  System operations: log user in, change user password, register new user
 *
 *  Data management operations (get [...] JSON): add columns to EditableGrid and return JSON of data
 *  Data management operations (fetchPairs): convert an array of objects into an associative array
 *  Data management operations (get YMAF [...]): return PHP data for years, modules, a/ftypes
 *  Data management operations (create [...]): create database entries for modules, ymaf data etc.
 *  Data management operations (update/delete):  update cell values, delete database entries
 *
 *  Calendar operations: fetch academic dates, ymaf/table data for module/combinations, convert combo_id to module array
 */
class Model
{
  // establish Model as a singleton
  private static $_instance = null;

  // store private connection to the database
  private $_db;

  /*
   * Private Constructor (the static factory method 'getInstance' should be called instead)
   */
  private function __construct($db) {
    $this->_db = $db;
  }

  /*
   * GET INSTANCE
   * Allow only one Model object to be instantiated
   */
  public static function getInstance() {

    // check if an instance has not yet been instantiated
    if(!isset(self::$_instance)) {
      self::$_instance = new Model(DB::getInstance());
    }
    return self::$_instance;
  }

  //---------------------------------------------------------------------------
  //  System operations: logging in, registering users, changing passwords etc.
  //---------------------------------------------------------------------------

  /*
   * LOG USER IN
   * Validate user input and keep active session if credentials are correct
   */
  public function logUserIn() {

    // validate POST data if it exists
    $validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array('required' => true),
			'password' => array('required' => true)
		));

    // check the validation result
    if ($validation->passed()) {

      // create new user object
			$user = new User();

      // check if the user want's to be remembered
			$remember = (Input::get('remember') === 'on') ? true : false;

      // attempt to log the user in
			$login = $user->login(Input::get('username'), Input::get('password'), $remember);

      // change action variable to advise controller that login was successful if it was
			if ($login) {

				return true;

			} else {

				// if login was unsuccessful, either an incorrect username or password was provided
				return 'Invalid USERNAME or PASSWORD.';
			}

    } else {

			// load up messages in case there were any problems
			$action = '';
			foreach($validation->errors() as $error) {
				$action = $action . $error . '<br />';
			}
		}
  }

  /*
   * CHANGE USER PASSWORD
   * Validate inputs provided by user and changes password if validation passes
   */
  public function changeUserPassword() {
  // validate POST data if it exists
  $validate = new Validate();
    $validation = $validate->check($_POST, array(
        'password_current' => array(
        'required' => true,
        'min' => 6
      ),
        'password_new' => array(
        'required' => true,
        'min' => 6
      ),
        'password_new_again' => array(
        'required' => true,
        'min' => 6,
        'matches' => 'password_new'
      )
    ));

    // check if validation passed
    if($validation->passed()) {

    // get the current user
    $user = new User();

    // check if password matches current password (add notice if this fails)
    if(Hash::make(Input::get('password_current'), $user->data()->salt) !== $user->data()->password) {
      $action = 'Your CURRENT PASSWORD is incorrect.';
    } else {

      // create a salt
      $salt = Hash::salt(32);

      try {

        // update password
        $user->update('password', Hash::make(Input::get('password_new'), $salt));

        // store salt
        $user->update('salt', $salt);

        // signal that the operation was a success
        $action = true;

      } catch (Exception $e) {

        // add any notices
        $action = $e->getMessage();
      }
    }

    } else {

      // load up messages in case there were any problems
      $action = '';
      foreach($validation->errors() as $error) {
        $action = $action . $error . '<br />';
    }
  }
  return $action;
}

  /*
   * REGISTER NEW USER
   * Validate inputs provided by user and create a new user if validation passes
   */
  public function registerNewUser() {

    // validate POST data if it exists
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'username' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'sys_users'
			),
			'password' => array(
				'required' => true,
				'min' => 6
			),
			'password_again' => array(
				'required' => true,
				'matches' => 'password'
			),
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			)
		));

		// check the validation result
		if ($validation->passed()) {

      // instantiate user
			$newUser = new User();

			// create salt
			$salt = Hash::salt(32);

			try {

        $group = 1;
        if (!(trim(Input::get('group')) == '')) {
          $group = Input::get('group');
        }

				// create a new user
				$newUser->create(
					Input::get('username'),
					Hash::make(Input::get('password'), $salt),
					$salt,
					Input::get('name'),
					$group
				);

				// set action to true to indicate success
				$action = true;

			} catch (Exception $e) {

				// load up any exception messages if there was a problem
				$action = $e->getMessage();

			}

		} else {

			// load up messages in case there were any problems
			$action = '';
			foreach($validation->errors() as $error) {
				$action = $action . $error . '<br />';
			}

		}

		return $action;
  }

  //---------------------------------------------------------------------------
  //  Data management operations: create and manage application data
  //---------------------------------------------------------------------------

  /*
   * GET ACADEMIC YEAR JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getYearsJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('aca_year', 'Year', 'string', NULL, false);
    $grid->addColumn('sem_1_start', 'Sem. 1 Start', 'date');
    $grid->addColumn('sem_1_end', 'Sem. 1 End', 'date');
    $grid->addColumn('sem_2_start', 'Sem. 2 Start', 'date');
    $grid->addColumn('sem_2_end', 'Sem. 2 End', 'date');
    $grid->addColumn('sem_3_start', 'Sem. 3 Start', 'date');
    $grid->addColumn('sem_3_end', 'Sem. 3 End', 'date');

    // run SQL query
    return $this->_db->query(
      "SELECT `year_id` as `id`, `aca_year`,
        date_format(`sem_1_start`, '%d %b %Y') as `sem_1_start`,
        date_format(`sem_1_end`, '%d %b %Y') as `sem_1_end`,
        date_format(`sem_2_start`, '%d %b %Y') as `sem_2_start`,
        date_format(`sem_2_end`, '%d %b %Y') as `sem_2_end`,
        date_format(`sem_3_start`, '%d %b %Y') as `sem_3_start`,
        date_format(`sem_3_end`, '%d %b %Y') as `sem_3_end`
        FROM `app_aca_year`;"
    )->results();
  }

  /*
   * GET MODULE JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getModulesJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('module_code', 'Module Code', 'string', NULL, false);
    $grid->addColumn('title', 'Title', 'string');
    $grid->addColumn('credits', 'Credits', 'integer');
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `module_id` as `id`,
      `module_code`, `title`, `credits`
      FROM `app_modules`;"
    )->results();
  }

  /*
   * GET ASSESSMENT TYPE JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getAssessmentTypesJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('assess_type_name', 'Assessment Type', 'string');
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `assess_id` as `id`, `assess_type_name`
      FROM `app_assess_types`;"
    )->results();
  }

  /*
   * GET ASSESSMENT TYPE JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getFeedbackTypesJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('feed_type_name', 'Assessment Type', 'string');
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `feed_id` as `id`, `feed_type_name` FROM `app_feed_types`;"
    )->results();
  }

  /*
   * GET YMAF JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getYMAFJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('year_id', 'Year', 'string',
      $this->fetchPairs($this->_db, "SELECT `year_id` as `key`, `aca_year` as `value` FROM `app_aca_year`"),
      true);
    $grid->addColumn('module_id', 'Module Code', 'string',
      $this->fetchPairs($this->_db, "SELECT `module_id` as `key`, `module_code` as `value` FROM `app_modules`"),
      true);
    $grid->addColumn('assess_id', 'Assessment Type', 'string',
      $this->fetchPairs($this->_db, "SELECT `assess_id` as `key`, `assess_type_name` as `value` FROM `app_assess_types`"),
      true);
    $grid->addColumn('feed_id', 'Feedback Type', 'string',
      $this->fetchPairs($this->_db, "SELECT `feed_id` as `key`, `feed_type_name` as `value` FROM `app_feed_types`"),
      true);
    $grid->addColumn('title', 'Title', 'string');
    $grid->addColumn('set_date', 'Set Date', 'date');
    $grid->addColumn('due_date', 'Due Date', 'date');
    $grid->addColumn('feed_date', 'Feedback Due', 'date');
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `ymaf_id` as `id`,
      `year_id`,
      `module_id`,
      `assess_id`,
      `feed_id`,
      `title`,
      date_format(`set_date`, '%d %b %Y') as `set_date`,
      date_format(`due_date`, '%d %b %Y') as `due_date`,
      date_format(`feed_date`, '%d %b %Y') as `feed_date`
      FROM `app_ymaf`;"
    )->results();
  }

  /*
   * GET MODULE COMBINATIONS JSON DATA
   * Add columns to Editable Grid and return data
   */
  public function getViewCombosJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('title', 'Title', 'string');
    $grid->addColumn('author', 'Author', 'string', NULL, false);
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `app_combos`.`combo_id` as `id`,
        `app_combos`.`title`,
        `sys_users`.`name` as `author`
      FROM `app_combos`
      LEFT JOIN `sys_users`
      ON `app_combos`.`user_id` = `sys_users`.`user_id`;"
    )->results();
  }

  /*
   * GET USER DATA JSON
   * Add columns to Editable Grid and return data
   */
  public function getManageUsersJSON($grid) {

    // add columns
    $grid->addColumn('id', 'ID', 'integer', NULL, false);
    $grid->addColumn('username', 'Username', 'string', NULL, false);
    $grid->addColumn('name', 'Actual Name', 'string');
    $grid->addColumn('joined', 'Date Created', 'string', NULL, false);
    $grid->addColumn('group_id', 'Permissions', 'string',
      $this->fetchPairs($this->_db, "SELECT `group_id` as `key`, `name` as `value` FROM `sys_groups`"),
    true);
    $grid->addColumn('action', 'Action', 'html', NULL, false, 'id');

    // run SQL query
    return $this->_db->query(
      "SELECT `user_id` as `id`,
      `username`, `name`, `joined`, `group_id`
      FROM `sys_users`;"
    )->results();
  }

  /*
   * FETCH PAIRS (for EditableGrid)
   * Converts array of objects into an associative array
   */
  public function fetchPairs($db, $pairQuery){

    $res = $db->query($pairQuery)->results();
    $rows = array();
    foreach($res as $row) {
      $rows[$row->key] = $row->value;
    }
    return $rows;
  }

  /*
   * GET YEARS FOR YMAF SELECTION
   * Reduced PHP to constrain choices for YMAF years
   */
  public function getYMAFYears() {
    return $this->_db->query(
      "SELECT `year_id`, `aca_year` FROM `app_aca_year`;"
    )->results();
  }

  /*
   * GET MODULES FOR YMAF SELECTION
   * Reduced PHP to constrain choices for YMAF modules
   */
  public function getYMAFModules() {
    return $this->_db->query(
      "SELECT `module_id`, `module_code`, `title` FROM `app_modules`;"
    )->results();
  }

  /*
   * GET ASSESSMENT TYPES FOR YMAF SELECTION
   * Reduced PHP to constrain choices for YMAF assessment types
   */
  public function getYMAFAssessmentTypes() {
    return $this->_db->query(
      "SELECT `assess_id`, `assess_type_name` FROM `app_assess_types`;"
    )->results();
  }

  /*
   * GET FEEDBACK TYPES FOR YMAF SELECTION
   * Reduced PHP to constrain choices for YMAF feedback types
   */
  public function getYMAFFeedbackTypes() {
    return $this->_db->query(
      "SELECT `feed_id`, `feed_type_name` FROM `app_feed_types`;"
    )->results();
  }

  /*
   * CREATE A MODULE
   */
  public function createModule($moduleCode, $title, $credits) {

    // attempt insert query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "INSERT INTO `app_modules` (`module_code`, `title`, `credits`) VALUES
      ('{$moduleCode}', '{$title}', '{$credits}');"
    )->error());
  }

  /*
   * CREATE AN ASSESSMENT TYPE
   */
  public function createAssessmentType($assessmentType) {

    // attempt insert query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "INSERT INTO `app_assess_types` (`assess_type_name`) VALUES
      ('{$assessmentType}');"
    )->error());
  }

  /*
   * CREATE A FEEDBACK TYPE
   */
  public function createFeedbackType($feedbackType) {

    // attempt insert query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "INSERT INTO `app_feed_types` (`feed_type_name`) VALUES
      ('{$feedbackType}');"
    )->error());
  }

  /*
   * CREATE A 'YMAF' ENTRY
   */
  public function createYMAF($yearId, $moduleId, $assessId, $feedId,
    $title, $setDate, $dueDate, $feedDate) {

    // format dates for MySQL storage
    $set_info = date_parse_from_format('m/d/Y', $setDate);
    $setDate = "{$set_info['year']}-{$set_info['month']}-{$set_info['day']}";
    $due_info = date_parse_from_format('m/d/Y', $dueDate);
    $dueDate = "{$due_info['year']}-{$due_info['month']}-{$due_info['day']}";
    $feed_info = date_parse_from_format('m/d/Y', $feedDate);
    $feedDate = "{$feed_info['year']}-{$feed_info['month']}-{$feed_info['day']}";

    // attempt insert query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "INSERT INTO `app_ymaf`
      (`year_id`, `module_id`, `assess_id`, `feed_id`,
        `title`, `set_date`, `due_date`, `feed_date`) VALUES
      ('{$yearId}', '{$moduleId}', '{$assessId}', '{$feedId}',
        '{$title}', '{$setDate}', '{$dueDate}', '{$feedDate}');"
    )->error());
  }

  /*
   * CREATE A MODULE COMBINATION
   */
  public function createModuleCombination($comboTitle, $modules = array()) {

    // get current user's ID
    $user = new User();
    $userId = $user->data()->user_id;

    // create a new combination ID (return false on failure)
    if($this->_db->query(
      "INSERT INTO `app_combos` (`title`, `user_id`) VALUES
      ('{$comboTitle}', '{$userId}');"
    )->error()) {
      return false;
    }

    // retrieve the newly created primary key of the module combination
    $comboId = $this->_db->query(
      "SELECT `combo_id` FROM `app_combos`
      WHERE `title` = '{$comboTitle}';"
    )->first()->combo_id;

    // loop through each module
    foreach ($modules as $moduleId) {

      // attempt insert queries, returning false if any failure occurs
      if($this->_db->query(
        "INSERT INTO `app_modcom` VALUES
        ('{$comboId}', '{$moduleId}');"
      )->error()) {
        return false;
      }
    }
    return true;
  }

  /*
   * UPDATE CELL VALUE
   * Determines which table to update and modifies the corresponding cell value
   */
  public function updateCellValue($table, $id, $newValue, $colName, $colType) {

    // determine which table and primary key to use for the ID
    $tableId;
    switch($table) {

      case 'years':
        $table = 'app_aca_year';
        $tableId = 'year_id';
      break;

      case 'modules':
        $table = 'app_modules';
        $tableId = 'module_id';
      break;

      case 'atypes':
        $table = 'app_assess_types';
        $tableId = 'assess_id';
      break;

      case 'ftypes':
        $table = 'app_feed_types';
        $tableId = 'feed_id';
      break;

      case 'ymaf':
        $table = 'app_ymaf';
        $tableId = 'ymaf_id';
      break;

      case 'viewcombos':
        $table = 'app_combos';
        $tableId = 'combo_id';
      break;

      case 'users':
        $table = 'sys_users';
        $tableId = 'user_id';
      break;
    }

    // format the date for MySQL storage
    if ($colType == 'date') {
      if ($newValue === "")
      	$newValue = NULL;
      else {
        $date_info = date_parse_from_format('d/m/Y', $newValue);
        $newValue = "{$date_info['year']}-{$date_info['month']}-{$date_info['day']}";
      }
    }

    // attempt update query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "UPDATE `{$table}` SET `{$colName}` = '{$newValue}'
      WHERE `{$tableId}` = '{$id}';"
    )->error());
  }

  /*
   * DELETE A RECORD
   * Determines which table and id to use to delete an entry
   * If there was a request to delete a module combination, associations with combo_id are deleted as well
   */
  public function deleteDatabaseRecord($table, $id) {

    // determine which table and primary key to use for the ID
    $tableId;
    switch($table) {

      case 'years':
        $table = 'app_aca_year';
        $tableId = 'ac_year_id';
      break;

      case 'modules':
        $table = 'app_modules';
        $tableId = 'module_id';
      break;

      case 'atypes':
        $table = 'app_assess_types';
        $tableId = 'assess_id';
      break;

      case 'ftypes':
        $table = 'app_feed_types';
        $tableId = 'feed_id';
      break;

      case 'ymaf':
        $table = 'app_ymaf';
        $tableId = 'ymaf_id';
      break;

      case 'viewcombos':
        $table = 'app_combos';
        $tableId = 'combo_id';
      break;

      case 'users':
        $table = 'sys_users';
        $tableId = 'user_id';
      break;
    }

    // if deleting a module combination, delete all from app_modcom where combo_id matches id
    if ($this->_db->query(
      "DELETE FROM `app_modcom`
      WHERE `combo_id` = '{$id}';"
    )->error()) { return false; }

    // attempt delete query: returns 'true' if no errors were present, else false
    return !($this->_db->query(
      "DELETE FROM `{$table}`
      WHERE `{$tableId}` = '{$id}';"
    )->error());
  }

  //---------------------------------------------------------------------------
  //  Calendar operations: return 'YMAF' data used for displaying workload levels
  //---------------------------------------------------------------------------

  /*
   *  GET ACADEMIC CALENDAR DATES WITH YEAR ID
   *  Used to provide calendar object dimensions to WorkloadCalendarView.js
   */
  public function getAcademicDatesForSingleYear($yearId) {

    return $this->_db->query(
			"SELECT * FROM `app_aca_year` WHERE `year_id` = '{$yearId}';"
		)->first();
  }

  /*
   *  GET YMAF DATA FOR A SELECTION OF MODULES, FOR A SPECIFIC YEAR
   *  Controller should JSON encode this data and return it to WorkloadCalendarView.js
   *  Used by WorkloadCalendarView.js to produce a 'heatmap' of assignments
   */
  public function getYMAFSingleYearModuleSelection($yearId, $modules = array()) {

    return $this->_db->query(
      "SELECT `app_modules`.`module_code`,
        `app_ymaf`.`assess_id`, `app_ymaf`.`feed_id`,
        `app_ymaf`.`title`, `app_ymaf`.`set_date`,
        `app_ymaf`.`due_date`, `app_ymaf`.`feed_date`
      FROM `app_ymaf`
      LEFT JOIN `app_modules`
      ON `app_ymaf`.`module_id` = `app_modules`.`module_id`
      WHERE `app_ymaf`.`year_id` = '{$yearId}'
      AND `app_ymaf`.`module_id` IN {$modules};"
    )->results();
  }

  /*
   *  CONVERT COMBINATION ID TO AN ARRAY OF MODULES
   *  Allows later methods to obtain YMAF/Table data with an array of modules
   */
  public function getModuleArrayFromCombinationId($comboId) {

    // fetch module id's that are associated with the selected combination
    $modules = $this->_db->query(
      "SELECT `module_id` FROM `app_modcom` WHERE `combo_id` = '{$comboId}';"
    )->results();

    // convert to ("id", "id", "id") etc. for SQL query
    $modString = array();
    foreach ($modules as $m) {
      $result[] = $m->module_id;
    }
    $modString = json_encode($result);
    $modString = str_replace("[", "(", $modString);
    return str_replace("]", ")", $modString);
  }

  /*
   *  GET MODULE COMBINATION SELECTION WITH AUTHOR NAME
   *  Allows user to select combination and see which user created it
   */
  public function getCombinationsPlusAuthorName() {

    return $this->_db->query(
      "SELECT `app_combos`.`combo_id`,
        `app_combos`.`title`,
        `sys_users`.`name` as `author`
      FROM `app_combos`
      LEFT JOIN `sys_users`
      ON `app_combos`.`user_id` = `sys_users`.`user_id`;"
    )->results();
  }

  /*
   *  GET YMAF DATA FOR USER DEFINED MODULE COMBINATION, FOR A SPECIFIC YEAR
   *  Controller should JSON encode this data and return it to WorkloadCalendarView.js
   *  Used by WorkloadCalendarView.js to produce a 'heatmap' of assignments
   */
  public function getYMAFSingleYearCombinationSelection($yearId, $comboId) {

    // fetch module id's that are associated with the selected combination
    $modules = $this->getModuleArrayFromCombinationId($comboId);
    return $this->getYMAFSingleYearModuleSelection($yearId, $modules);
  }

  /*
   *  GET A TABLE OF INFORMATION USING MODULE SELECTION, FOR A SPECIFIC YEAR
   *  Used by WorkloadCalendarView.js to render a table of modules used to produce calendar
   */
  public function getModuleTableWithModuleArray($yearId, $modules = array()) {

    return $this->_db->query(
      "SELECT `app_modules`.`module_code`, `app_modules`.`title`, `app_modules`.`credits`,
        (SELECT COUNT(`ymaf_id`) FROM `app_ymaf`
        WHERE `app_ymaf`.`module_id` = `app_modules`.`module_id`
        AND `app_ymaf`.`year_id` = '{$yearId}') as `total`
      FROM `app_modules`
      WHERE `app_modules`.`module_id` IN {$modules};"
    )->results();
  }

  /*
   *  GET A TABLE OF INFORMATION USING COMBINATION ID, FOR A SPECIFIC YEAR
   *  Used by WorkloadCalendarView.js to render a table of modules used to produce calendar
   */
  public function getModuleTableWithCombinationId($yearId, $comboId) {

    // fetch module id's that are associated with the selected combination
    $modules = $this->getModuleArrayFromCombinationId($comboId);
    return $this->getModuleTableWithModuleArray($yearId, $modules);
  }
}
