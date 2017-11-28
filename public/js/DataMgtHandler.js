/**
 *  highlightRow and highlight are used to show a visual feedback. If the row has been successfully modified, it will be highlighted in green. Otherwise, in red
 */
function highlightRow(rowId, bgColor, after) {

	var rowSelector = $("#" + rowId);
	rowSelector.css("background-color", bgColor);
	rowSelector.fadeTo("normal", 0.5, function() {
		rowSelector.fadeTo("fast", 1, function() {
			rowSelector.css("background-color", '');
		});
	});
}

function highlight(div_id, style) {

	highlightRow(div_id, style == "error" ? "#e5afaf" : style == "warning" ? "#ffcc00" : "#8dc70a");
}

function updateCellValue(editableGrid, rowIndex, columnIndex, oldValue, newValue, row, onResponse) {

	$.ajax({
		url: baseURL + 'data/update',
		type: 'POST',
		dataType: "html",
	  data: {
			tableIdentifier : editableGrid.name,
			id: editableGrid.getValueAt(rowIndex, 0),
			newValue: editableGrid.getColumnType(columnIndex) == "boolean" ? (newValue ? 1 : 0) : newValue,
			colName: editableGrid.getColumnName(columnIndex),
			colType: editableGrid.getColumnType(columnIndex)
		},
		success: function (response)
		{
			// reset old value if failed then highlight row
			var success = onResponse ? onResponse(response) : (response == "ok" || !isNaN(parseInt(response))); // by default, a sucessfull reponse can be "ok" or a database id
			if (!success) editableGrid.setValueAt(rowIndex, columnIndex, oldValue);
		    highlight(row.id, success ? "ok" : "error");
		},
		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
		async: true
	});

}

function DatabaseGrid() {

	this.editableGrid = new EditableGrid("" + $('#HTMLidentifier').val(), {
		enableSort: true,
	  // define the number of row visible by page
    pageSize: 50,
    // Once the table is displayed, we update the paginator state
    tableRendered:  function() {  updatePaginator(this); },
    tableLoaded: function() { datagrid.initializeGrid(this); },
		modelChanged: function(rowIndex, columnIndex, oldValue, newValue, row) {
      updateCellValue(this, rowIndex, columnIndex, oldValue, newValue, row);
    }
  });
	this.fetchGrid();
}

DatabaseGrid.prototype.fetchGrid = function()  {
	// call a PHP script to get the data
	this.editableGrid.loadJSON(window.location.href + "JSON");
};

DatabaseGrid.prototype.initializeGrid = function(grid) {

  var self = this;
	// render for the action column
	grid.setCellRenderer("action", new CellRenderer({
		render: function(cell, id) {
			cell.innerHTML+= "<i onclick=\"datagrid.deleteRow('"
			 	+ grid.name + "', '" + id +
			 "');\" class='fa fa-trash-o red' ></i>";
		}
	}));

	grid.renderGrid("tablecontent", "testgrid");
};

DatabaseGrid.prototype.deleteRow = function(tableName, id) {

  var self = this;

  if (confirm('Are you sure you want to delete ID: ' + id + '?')) {

		$.ajax({
			url: baseURL + 'data/delete',
			type: 'POST',
			dataType: "html",
			data: {
				tableIdentifier : tableName,
				id: id
			},
			success: function (response)
			{
				if (response == "ok" )
			    self.editableGrid.removeRow(id);
			},
			error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
			async: true
		});
  }
};


DatabaseGrid.prototype.addRow = function(id) {

  var self = this;

	// use ajax method depending on page user submitted it from
	switch(self.editableGrid.name) {

		case 'modules':
			$.ajax({
				url: baseURL + 'data/createModule',
				type: 'POST',
				dataType: "html",
				data: {
					module_code: $('#module_code').val(),
					title: $('#title').val(),
					credits: $('#credits').val()
				},
				success: function (response) {
					if (response == "ok" ) {

						// hide form & reset values
						showAddForm();
						clearForm();
						alert("Row added! Click OK to reload data");
						self.fetchGrid();
					}
					else
						alert("There was a problem with the data. Please try again.");
				},
				error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
				async: true
			});
		break;

		case 'atypes':
			$.ajax({
				url: baseURL + 'data/createAssessmentType',
				type: 'POST',
				dataType: "html",
				data: {
					assess_type_name: $('#assess_type_name').val()
				},
				success: function (response) {
					if (response == "ok" ) {

						// hide form & reset values
						showAddForm();
						clearForm();
						alert("Row added! Click OK to reload data");
						self.fetchGrid();
					}
					else
						alert("There was a problem with the data. Please try again.");
				},
				error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
				async: true
			});
		break;

		case 'ftypes':
			$.ajax({
				url: baseURL + 'data/createFeedbackType',
				type: 'POST',
				dataType: "html",
				data: {
					feed_type_name: $('#feed_type_name').val()
				},
				success: function (response) {
					if (response == "ok" ) {

						// hide form & reset values
						showAddForm();
						clearForm();
						alert("Row added! Click OK to reload data");
						self.fetchGrid();
					}
					else
						alert("There was a problem with the data. Please try again.");
				},
				error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
				async: true
			});
		break;

		case 'ymaf':
			$.ajax({
				url: baseURL + 'data/createYMAF',
				type: 'POST',
				dataType: "html",
				data: {
					year_id: $('#year_id').val(),
					module_id: $('#module_id').val(),
					assess_id: $('#assess_id').val(),
					feed_id: $('#feed_id').val(),
					title: $('#title').val(),
					set_date: $('#set_date').val(),
					due_date: $('#due_date').val(),
					feed_date: $('#feed_date').val()
				},
				success: function (response) {
					if (response == "ok" ) {

						// hide form & reset values
						showAddForm();
						clearForm();
						alert("Row added! Click OK to reload data");
						self.fetchGrid();
					}
					else
						alert("There was a problem with the data. Please try again.");
				},
				error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + errortext); },
				async: true
			});
		break;

	}
};

function updatePaginator(grid, divId)
{
    divId = divId || "paginator";
	var paginator = $("#" + divId).empty();
	var nbPages = grid.getPageCount();

	// get interval
	var interval = grid.getSlidingPageInterval(20);
	if (interval == null) return;

	// get pages in interval (with links except for the current page)
	var pages = grid.getPagesInInterval(interval, function(pageIndex, isCurrent) {
		if (isCurrent) return "<span id='currentpageindex'>" + (pageIndex + 1)  +"</span>";
		return $("<a>").css("cursor", "pointer").html(pageIndex + 1).click(function(event) { grid.setPageIndex(parseInt($(this).html()) - 1); });
	});

	// "first" link
	var link = $("<a class='nobg'>").html("<i class='fa fa-fast-backward'></i>");
	if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.firstPage(); });
	paginator.append(link);

	// "prev" link
	link = $("<a class='nobg'>").html("<i class='fa fa-backward'></i>");
	if (!grid.canGoBack()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.prevPage(); });
	paginator.append(link);

	// pages
	for (p = 0; p < pages.length; p++) paginator.append(pages[p]).append(" ");

	// "next" link
	link = $("<a class='nobg'>").html("<i class='fa fa-forward'>");
	if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.nextPage(); });
	paginator.append(link);

	// "last" link
	link = $("<a class='nobg'>").html("<i class='fa fa-fast-forward'>");
	if (!grid.canGoForward()) link.css({ opacity : 0.4, filter: "alpha(opacity=40)" });
	else link.css("cursor", "pointer").click(function(event) { grid.lastPage(); });
	paginator.append(link);
};

function showAddForm() {
  if ( $("#addform").is(':visible') )
      $("#addform").hide();
  else
      $("#addform").show();
}

function clearForm() {
	$(".rowInput").each(function() {
		$(this).val('');
	});
}

var datagrid = new DatabaseGrid();
window.onload = function() {

  // key typed in the filter field
  $("#filter").keyup(function() {
      datagrid.editableGrid.filter( $(this).val());

      // To filter on some columns, you can set an array of column index
      //datagrid.editableGrid.filter( $(this).val(), [0,3,5]);
    });

	$('#set_date').datepicker({
      changeMonth: true,
      changeYear: true
    });
	$('#due_date').datepicker({
      changeMonth: true,
      changeYear: true
    });
	$('#feed_date').datepicker({
      changeMonth: true,
      changeYear: true
    });

  $("#showaddformbutton").click( function()  {
    showAddForm();
  });
  $("#cancelbutton").click( function() {
    showAddForm();
  });

  $("#addbutton").click(function() {
    datagrid.addRow();
  });
};
