<main>
  <header>
    <h1>Data Management</h1>
  </header>
  <section>

    <?php
      // display user management for admin users
      $user = new User();
      if ($user->hasPermission('admin')) {
    ?>
    <h2>User Account Management</h2>
    <ul id="indexList">
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/manageusers">Manage User Accounts</a></li>
    </ul>
    <?php
      }
    ?>

    <?php
      // display advanced data management options to power users
      if ($user->hasPermission('power')) {
    ?>
    <h2>Data Management</h2>
    <ul id="indexList">
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/years">Manage Years (semester dates)</a></li>
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/modules">Manage Modules</a></li>
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/atypes">Manage Assessment Types</a></li>
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/ftypes">Manage Feedback Types</a></li>
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/ymaf">Manage Y.M.A.F. Data</a></li>
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/viewcombos">Manage Module Combinations</a></li>
    </ul>
    <?php
      }
    ?>

    <h2>Create Module combinations</h2>
    <ul id="indexList">
      <li><a href="<?= URL_WITH_INDEX_FILE; ?>data/createcombos">Create Module Combinations</a></li>
    </ul>

  </section>
</main>
