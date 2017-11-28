<?php

/*
 * 	Visualisation Controller
 */
class Visualisation extends Controller
{
  /*
   * PAGE: index
   */
  public function index() {

    // load views
    Functions::render(get_class(), __FUNCTION__);
  }

  /*
   * PAGE: Workload Calendar: module view
   */
  public function calbymodule() {

    // load modules & years
    $resources = array(
      'years' => $this->model->getYMAFYears(),
      'modules' => $this->model->getYMAFModules(),
      'atypes' => $this->model->getYMAFAssessmentTypes(),
      'ftypes' => $this->model->getYMAFFeedbackTypes()
    );

    // load views and pass resources
    Functions::render(get_class(), __FUNCTION__, $resources);
  }

  /*
   * PAGE: Workload Calendar: combination view
   */
  public function calbycombination() {

    // load modules & years
    $resources = array(
      'combos' => $this->model->getCombinationsPlusAuthorName(),
      'years' => $this->model->getYMAFYears(),
      'atypes' => $this->model->getYMAFAssessmentTypes(),
      'ftypes' => $this->model->getYMAFFeedbackTypes()
    );

    // load views and pass resources
    Functions::render(get_class(), __FUNCTION__, $resources);
  }

  /*
   * ACTION: return JSON of workload data for custom module selection
   */
  public function fetchYMAFModuleSelection() {

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    // create response object containing dates (calendar frame), ymaf data (workload 'heatmap') and table of data
		$response = new stdClass();
    $response->dates = $this->model->getAcademicDatesForSingleYear(
      Input::get('yearId')
    );
		$response->ymaf = $this->model->getYMAFSingleYearModuleSelection(
      Input::get('yearId'),
      stripslashes($_POST['moduleSelection'])
    );
    $response->table = $this->model->getModuleTableWithModuleArray(
      Input::get('yearId'),
      stripslashes($_POST['moduleSelection'])
    );

    // encode response and return as JSON data
    echo json_encode($response);
  }

  /*
   * ACTION: return JSON of workload data for user-defined combination
   */
  public function fetchYMAFCombinationSelection() {

    // change the header to indicate that JSON data is being returned
		header('Content-Type: application/json');

    // create response object containing dates (calendar frame), ymaf data (workload 'heatmap') and table of data
		$response = new stdClass();
    $response->dates = $this->model->getAcademicDatesForSingleYear(
      Input::get('yearId')
    );
		$response->ymaf = $this->model->getYMAFSingleYearCombinationSelection(
      Input::get('yearId'),
      Input::get('comboId')
    );
    $response->table = $this->model->getModuleTableWithCombinationId(
      Input::get('yearId'),
      Input::get('comboId')
    );

    // encode response and return as JSON data
    echo json_encode($response);
  }
}
