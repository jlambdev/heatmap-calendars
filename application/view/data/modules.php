<main>
  <header>
    <h1>Data Management</h1>
  </header>
  <section>

    <div id="wrap">
		<h2 class="dataHeader">Modules</h2>

      <p class="dataHeader"><em>
        Click on the cell for the 'Title' or 'Credits' to change values<br />
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
      <input id="HTMLidentifier" type="hidden" value="modules">

		</div>

    <!-- simple form, used to add a new row -->
    <div id="addform">

      <div class="row">
        <input type="text" id="module_code" class="rowInput" name="module_code" placeholder="Module Code: 3 letters, 4 numbers" pattern="[a-zA-Z]{3}[0-9]{4}" required />
      </div>

      <div class="row">
        <input type="text" id="title" class="rowInput" name="title" placeholder="Module Title" required />
      </div>

      <div class="row">
        <input type="number" id="credits" class="rowInput" name="credits" placeholder="Credits" min="10" max="60" step="10" required />
      </div>

      <div class="row tright">
        <a id="addbutton" class="button green" ><i class="fa fa-save"></i> Apply</a>
        <a id="cancelbutton" class="button delete">Cancel</a>
      </div>
    </div>
  </section>
</main>
