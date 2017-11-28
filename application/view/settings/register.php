<main>
  <header>
  	<h1>Application Settings</h1>
  </header>
  <section>

    <h2>Register New User</h2>

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
      <form action="<?= URL_WITH_INDEX_FILE; ?>settings/register" onsubmit="createUser();" method="post">
    		<div class="field">
    			<label for="username">Username:</label><br />
    			<input type="text" class="userTextInput" name="username" id="username" value="<?php echo Input::get('username'); ?>" autocomplete="off" required><br /><br />
    		</div>

    		<div class="field">
    			<label for="password">Choose a password:</label><br />
    			<input type="password" class="userTextInput" name="password" id="password" required><br /><br />
    		</div>

    		<div class="field">
    			<label for="password_again">Repeat password:</label><br />
    			<input type="password" class="userTextInput" name="password_again" id="password_again" required><br /><br />
    		</div>

    		<div class="field">
    			<label for="name">Provide name:</label><br />
    			<input type="text" class="userTextInput" name="name" value="<?php echo Input::get('name'); ?>" id="name" required><br /><br />
    		</div>

        <?php
          $user = new User();
          if ($user->hasPermission('admin')) {
        ?>

    		<div class="field">
    			<label for="group">Users permissions:</label><br />
    			<select name="group">
    				<option value="1">1: standard user</option>
    				<option value="2">2: power user</option>
    				<option value="3">3: administrator</option>
    			</select><br /><br />
    		</div>

        <?php
          }
        ?>

    		<input type="submit" value="Create">
    		<input type="hidden" name="token" value="<?= Token::generate(); ?>">

    	</form>
    </div>

    <p><em>Please note that registration can take time to process. On success, you will be redirected back to the settings page</em></p>

  </section>
</main>
