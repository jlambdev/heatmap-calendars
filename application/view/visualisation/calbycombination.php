<main>
  <header>
    <h1>Data Visualisation</h1>
  </header>
  <section>

    <h2 class="dataHeader">Workload Calendar: Combination selection</h2>

    <p class="dataHeader"><em>
      Drag and drop items from the left list to the right list to include them in the calendar visualisation<br />
      Modify the academic year, choose a perspective, alter the assessment/feedback selection and click 'Update'<br />
      Hold Ctrl &#43; scroll the mouse wheel to change the zoom level of the page
    </em></p>

    <div id="selectionLists">
      <form onsubmit="calendarByCombinationRequest(); return false;">
        <div class="row">
          <label for="combo_id">Select module combination:</label>
          <br />
          <select id="combo_id" name="combo_id">
            <?php
              foreach ($combos as $c) {
            ?>
            <option value="<?= $c->combo_id; ?>"><?= $c->title; ?> (by <?= $c->author; ?>)</option>
            <?php
              }
            ?>
          </select>
        </div>
        <br />
        <div class="row">
          <label for="year_id">Select academic year:</label>
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
          <label for="perspective">Select student or staff perspective:</label>
          <select id="perspective" name="perspective">
            <option value="student">Student</option>
            <option value="staff">Staff</option>
          </select>
        </div>
        <div class="row">
          <label for="assess_id">Select assessment type (student perspective only)</label>
          <select id="assess_id" name="assess_id">
            <option value="all">All Assessment Types</option>
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
          <label for="feed_id">Select feedback type (staff perspective only)</label>
          <select id="feed_id" name="feed_id">
            <option value="all">All Feedback Types</option>
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
    		  <input type="submit" value="Update" />
        </div>
    	</form>
    </div>

    <div id="calendar"></div>

    <div id="clear_buffer"></div>

    <table>
      <thead>
        <th>Module Code</th>
        <th>Module Title</th>
        <th>Credits</th>
        <th>No. assignments</th>
      </thead>
      <tbody id="tableOfModulesBody">
      </tbody>
    </table>

  </section>
</main>
