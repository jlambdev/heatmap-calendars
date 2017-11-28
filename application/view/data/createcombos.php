<main>
  <header>
    <h1>Data Management</h1>
  </header>
  <section>

    <h2 class="dataHeader">Create module combinations</h2>

    <p class="dataHeader"><em>
      Drag and drop items from the left list to the right list to include them in the module combination<br />
      Provide a name for your module combination and click 'Create'
    </em></p>

    <div id="selectionLists">
      <ul id="sortable1" class="connectedSortable">
        <?php
          foreach ($modules as $m) {
        ?>
        <li id="<?= $m->module_id; ?>"><?= $m->module_code; ?>: <?= $m->title; ?></li>
        <?php
          }
        ?>
      </ul>

      <ul id="sortable2" class="connectedSortable">
  	  </ul>

      <br /><br />

      <form onsubmit="createCombination(); return false;">
        <label for="comboTitle">Provide a name for the combination:</label>
        <br />
        <input type="text" class="userTextInput" id="comboTitle" name="comboTitle" required />
        <br />
    		<input type="submit" value="Create" />
    	</form>
    </div>





  </section>
</main>
