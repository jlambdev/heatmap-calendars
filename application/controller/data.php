<?php

/*
 * 	Data Management Controller
 */
class Data extends Controller
{
  /*
   *  PAGE: index
   */
  public function index() {

    // load views
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: academic years and semester dates
   */
  public function years() {

    // redirect user if they are not a power user (or administrator)
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: modules
   */
  public function modules() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: assessment types
   */
  public function atypes() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: feedback types
   */
  public function ftypes() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: view module combinations
   */
  public function viewcombos() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   *  PAGE: view ymaf data
   *  YMAF = Years, Modules, Assessment Types and Feedback Types
   *  Main application data used to generate workload calendar views
   */
  public function ymaf() {

    // redirect the user if they do not have the correct permissions
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    // exceptional case: request resources from the model to constrain input types for creating entries
    $resources = array(
      'years' => $this->model->getYMAFYears(),
      'modules' => $this->model->getYMAFModules(),
      'atypes' => $this->model->getYMAFAssessmentTypes(),
      'ftypes' => $this->model->getYMAFFeedbackTypes()
    );

    Functions::render(get_class(), __FUNCTION__, $resources);
  }

  /*
   *  PAGE: create module combinations
   */
  public function createcombos() {

    // request modules from the model
    $resources = array(
      'modules' => $this->model->getYMAFModules()
    );

    Functions::render(get_class(), __FUNCTION__, $resources);
  }

  /*
   *  PAGE: manage system users
   *  Note: this feature is only enabled for administrator accounts
   */
  public function manageusers() {

    // redirect the user if they do not have administrator permissions
    $user = new User();
    if (!$user->hasPermission('admin')) { Redirect::to(404); }
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   * DATA MANAGEMENT ACTIONS:
   * Checks if the user has appropriate permissions and adds JSON data to 'EditableGrid'
   */
  public function yearsJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getYearsJSON($grid));
  }
  public function modulesJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getModulesJSON($grid));
  }
  public function atypesJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getAssessmentTypesJSON($grid));
  }
  public function ftypesJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getFeedbackTypesJSON($grid));
  }
  public function ymafJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getYMAFJSON($grid));
  }
  public function viewcombosJSON() {
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getViewCombosJSON($grid));
  }
  public function manageusersJSON() {
    $user = new User();
    if (!$user->hasPermission('admin')) { Redirect::to(404); }
    $grid = new EditableGrid();
    $grid->renderJSON($this->model->getManageUsersJSON($grid));
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Update cell values
   */
  public function update() {

    // ensure the user is redirected if they are not a power user
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    // if the user is attempting to update user fields and does not have admin rights: redirect request
    if (Input::get('tableIdentifier') == 'users') {
      if (!$user->hasPermission('admin')) { Redirect::to(404); }
    }

    // pass (and escape!) data to the model
    $result = $this->model->updateCellValue(
      Input::get('tableIdentifier'),
      Input::get('id'),
      Input::get('newValue'),
      Input::get('colName'),
      Input::get('colType')
    );

    // echo result of SQL query
    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Delete an entry
   */
  public function delete() {

    // check user permissions
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }
    if (Input::get('tableIdentifier') == 'users') {
      if (!$user->hasPermission('admin')) { Redirect::to(404); }
    }

    // pass & escape data to the model
    $result = $this->model->deleteDatabaseRecord(
      Input::get('tableIdentifier'),
      Input::get('id')
    );

    // echo result of SQL query
    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Create a new Module
   */
  public function createModule() {

    // check user permissions
    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    // pass & escape data to the model
    $result = $this->model->createModule(
      Input::get('module_code'),
      Input::get('title'),
      Input::get('credits')
    );

    // echo result of SQL query
    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Create a new Assessment Type
   */
  public function createAssessmentType() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    $result = $this->model->createAssessmentType(
      Input::get('assess_type_name')
    );

    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Create a new Feedback Type
   */
  public function createFeedbackType() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    $result = $this->model->createFeedbackType(
      Input::get('feed_type_name')
    );

    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Create YMAF entry
   */
  public function createYMAF() {

    $user = new User();
    if (!$user->hasPermission('power')) { Redirect::to(404); }

    $result = $this->model->createYMAF(
      Input::get('year_id'),
      Input::get('module_id'),
      Input::get('assess_id'),
      Input::get('feed_id'),
      Input::get('title'),
      Input::get('set_date'),
      Input::get('due_date'),
      Input::get('feed_date')
    );

    echo $result ? "ok": "error";
  }

  /*
   * DATA MANAGEMENT ACTION:
   * Create Module Combination
   */
  public function createModuleCombination() {

    $result = $this->model->createModuleCombination(
      Input::get('comboTitle'),
      json_decode(stripslashes($_POST['moduleSelection']))
    );

    echo $result ? "ok": "error";
  }
}
