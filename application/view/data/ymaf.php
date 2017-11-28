<main>
  <header>
    <h1>Data Management</h1>
  </header>
  <section>

    <div id="wrap">
		<h2 class="dataHeader">'Y.M.A.F' Data: Years, Modules, Assessment and Feedback Types</h2>

      <p class="dataHeader"><em>
        Click on the cells from 'Year' to 'Feedback Due' to change values<br />
        Click on table headers to display entries in ascending or descending order<br />
        Begin typing values into the 'Filter' box to filter records<br />
        If expected data is not appearing, click on the 'Filter' box and press enter (search for all records)
      </em></p>

			<!-- Feedback message zone -->
			<div id="message"></div>

      <!-- filter and add entry button -->
      <div id="toolbar" class="dataHeader">
        <input type="text" id="filter" name="filter" placeholder="Filter :type any text here"  />
        <a id="showaddformbutton" class="button green"><i class="fa fa-plus"></i> Add new row</a>
      </div>

      <!-- Paginator control -->
			<div id="paginator"></div>

			<!-- Grid contents -->
			<div id="tablecontent"></div>

      <!-- Grid table identifier -->
      <input id="HTMLidentifier" type="hidden" value="ymaf">

		</div>

    <!-- simple form, used to add a new row -->
    <div id="addform">

        <div class="row">
          <select id="year_id" name="year_id">
            <?php
              foreach ($years as $y) {
            ?>
            <option value="<?= $y->year_id; ?>"><?= $y->aca_year; ?></option>
            <?php
              }
            ?>
          </select>
        </div>

        <div class="row">
          <select id="module_id" name="module_id">
            <?php
              foreach ($modules as $m) {
            ?>
            <option value="<?= $m->module_id; ?>"><?= $m->module_code; ?>: <?= $m->title; ?></option>
            <?php
              }
            ?>
          </select>
        </div>

        <div class="row">
          <select id="assess_id" name="assess_id">
            <?php
              foreach ($atypes as $a) {
            ?>
            <option value="<?= $a->assess_id; ?>"><?= $a->assess_type_name; ?></option>
            <?php
              }
            ?>
          </select>
        </div>

        <div class="row">
          <select id="feed_id" name="feed_id">
            <?php
              foreach ($ftypes as $f) {
            ?>
            <option value="<?= $f->feed_id; ?>"><?= $f->feed_type_name; ?></option>
            <?php
              }
            ?>
          </select>
        </div>

        <div class="row">
          <input type="text" id="title" class="rowInput" name="title" placeholder="Assessment Title" required />
        </div>

        <div class="row">
          <input type="text" id="set_date" class="rowInput" name="set_date" placeholder="Set date" required />
        </div>

        <div class="row">
          <input type="text" id="due_date" class="rowInput" name="due_date" placeholder="Due date" required />
        </div>

        <div class="row">
          <input type="text" id="feed_date" class="rowInput" name="feed_date" placeholder="Feedback due date" required />
        </div>

        <div class="row tright">
          <a id="addbutton" class="button green" ><i class="fa fa-save"></i> Apply</a>
          <a id="cancelbutton" class="button delete">Cancel</a>
        </div>
    </div>
  </section>
</main>
