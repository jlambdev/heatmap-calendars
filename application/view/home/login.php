<div id="loginBanner"></div>

<section id="loginContainer">

  <form id="login" action="<?= URL_WITH_INDEX_FILE; ?>home/login" method="post">

    <div class="field">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" autocomplete="off" autofocus required>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" autocomplete="off" required>
    </div>

    <div class="field">
      <label for="remember">
        Remember Me <input type="checkbox" name="remember" id="remember">
      </label>
    </div>

    <!-- CSRF token -->
    <input type="hidden" name="token" value="<?= Token::generate(); ?>">

    <input type="submit" id="loginSubmit" value="Log In">

  </form>

  <div id="loginErrors">

  <?php
    if (@isset($errors)) {
  ?>

  <p><?= $errors; ?></p>

  <?php
    }
  ?>

  </div>

</section>
