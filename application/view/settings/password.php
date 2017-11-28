<main>
  <header>
  	<h1>Application Settings</h1>
  </header>
  <section>

    <h2>Change Password</h2>

    <?php
  		if (@isset($errors)) {
  	?>

  	<div>
  		<p>Unable to change password:</p>
  		<p><?= $errors; ?></p>
  	</div>

  	<?php
  		}
  	?>

    <div id="settingsContainer">
      <form action="<?= URL_WITH_INDEX_FILE; ?>settings/password" method="post">
    		<div class="field">
    			<label for="password_current">Current password:</label><br/>
    			<input type="password" class="userTextInput" name="password_current" id="password_current" required><br/><br/>
    		</div>

    		<div class="field">
    			<label for="password_new">New password:</label><br/>
    			<input type="password" class="userTextInput" name="password_new" id="password_new" required><br/><br/>
    		</div>

    		<div class="field">
    			<label for="password_new_again">New password again:</label><br/>
    			<input type="password" class="userTextInput" name="password_new_again" id="password_new_again" required><br/><br/>
    		</div>

    		<input type="submit" value="Change">
    		<input type="hidden" name="token" value="<?= Token::generate(); ?>">
    	</form>
    </div>

    <p><em>Please note that on success, you will be redirected back to the settings page</em></p>

  </section>
</main>
