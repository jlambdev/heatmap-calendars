<main>
  <header>
  	<h1>Application Settings</h1>
  </header>
  <section>

    <h2>Account Details</h2>
    <?php
      $user = new User();
    ?>
    <p>Username: <?= $user->data()->username . '<br/>'; ?>
    Actual Name: <?= $user->data()->name . '<br/>'; ?>
    Date Joined: <?= $user->data()->joined . '<br/>'; ?>
    Account permission level:
      <?php
        if ($user->data()->group_id == '1') echo 'Standard User';
        else if ($user->data()->group_id == '2') echo 'Power User';
        else echo 'Administrator';
      ?>
    </p>

    <h2>Password</h2>
    <ul id="indexList">
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>settings/password">Change Password</a></li>
    </ul>

    <?php
      if ($user->hasPermission('power')) {
    ?>
    <h2>Registration</h2>
    <ul id="indexList">
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>settings/register">Create New User</a></li>
    </ul>
    <?php
      }
    ?>
  </section>
</main>
