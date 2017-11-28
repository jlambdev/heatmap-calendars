<?php

class ModelTest extends PHPUnit_Framework_TestCase {

  private $_model;
  private $_users;

  public function __construct() {

    // initialise reference to model
    $this->_model = model::getInstance();

    // create temporary user details
    $this->_users = array(
      '1' => array(
        'username' => 'drjones',
        'password' => 'password',
        'name' => 'Dr. Jones'
      ),
      '2' => array(
        'username' => 'mrsmith',
        'password' => 'simplepassword',
        'name' => 'Mr. Smith'
      ),
      '3' => array(
        'username' => 'jamface',
        'password' => 'jamjamjam',
        'name' => 'Jam Face'
      ),
      '4' => array(
        'username' => 'madgoat',
        'password' => 'fneiaogfneiaogheaio',
        'name' => 'Mad Goat'
      ),
      '5' => array(
        'username' => 'agentsmith',
        'password' => '390gnso390t3_DDDgeiogsn',
        'name' => 'Secret Agent Smith'
      )
    );
  }

  public function _countOrSetNumUsers() {

    $this->_db = DB::getInstance();

    // query db
    $value = $this->_db->query(
      "SELECT COUNT(`user_id`) as `total` FROM `sys_users`;"
    )->first();

    return $value->total;
  }

  /**
   *  @test
   */
  public function getInstance_callMethodTwice_sameObject() {

    $model_reference_2 = model::getInstance();
    $this->assertEquals($this->_model, $model_reference_2);
  }

  /**
   *  @test
   */
  public function registerNewUser_registerFiveTempUsers_FiveUsersInDB() {

    // get no. users before registration attempts
    $numUsers = $this->_countOrSetNumUsers();

    // update POST data and register each user
    foreach($this->_users as $user) {
      $_POST['username'] = $user['username'];
      $_POST['password'] = $user['password'];
      $_POST['password_again'] = $user['password'];
      $_POST['name'] = $user['name'];

      $this->_model->registerNewUser();
    }

    // identify number of users now in the database
    $this->assertEquals($numUsers + 5, $this->_countOrSetNumUsers());
  }

  /**
   *  @test
   */
  public function logUserIn_logTempUsersInAndOut_allActionsSuccessful() {

    // prepare boolean flag to fail test if any issues arise
    $noIssues = true;

    // update POST data, log in each user (check okay), log each user out
    foreach($this->_users as $user) {
      $_POST['username'] = $user['username'];
      $_POST['password'] = $user['password'];

      if($this->_model->logUserIn()) {
        $current = new User();
        $current->logout();
      } else {
        $noIssues = false;
      }
    }

    $this->assertTrue($noIssues);
  }

  /**
   *  @test
   */
  public function _deleteTempUsers_usersInDBReducedByFive() {

    // get no. users before registration attempts, connect to DB for direct query
    $numUsers = $this->_countOrSetNumUsers();
    $this->_db = DB::getInstance();

    // update POST data and register each user
    foreach($this->_users as $user) {
      $this->_db->query(
        "DELETE FROM `sys_users`
        WHERE `username` = '{$user['username']}';"
      );
    }

    // identify number of users now in the database
    $this->assertEquals($numUsers - 5, $this->_countOrSetNumUsers());
  }
}
