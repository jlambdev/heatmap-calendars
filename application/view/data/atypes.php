<main>
  <header>
    <h1>Data Management</h1>
  </header>
  <section>

    <div id="wrap">
		<h2 class="dataHeader">Assessment Types</h2>

      <p class="dataHeader"><em>
        Click on the cell for the assessment type to change its name<br />
        Click on table headers to display entries in ascending or descending order
      </em></p>

			<!-- Feedback message zone -->
			<div id="message"></div>

      <!-- add entry button -->
      <div id="toolbar" class="dataHeader">
        <a id="showaddformbutton" class="button green"><i class="fa fa-plus"></i> Add new row</a>
      </div>

			<!-- Grid contents -->
			<div id="tablecontent"></div>

      <!-- Grid table identifier -->
      <input id="HTMLidentifier" type="hidden" value="atypes">

		</div>

    <!-- simple form, used to add a new row -->
    <div id="addform">

      <div class="row">
        <input type="text" id="assess_type_name" class="rowInput" name="assess_type_name" placeholder="Assessment Type" required />
      </div>

      <div class="row tright">
        <a id="addbutton" class="button green" ><i class="fa fa-save"></i> Apply</a>
        <a id="cancelbutton" class="button delete">Cancel</a>
      </div>
    </div>
  </section>
</main>
