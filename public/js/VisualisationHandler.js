$(function() {
    $( "#sortable1, #sortable2" ).sortable({
		connectWith: ".connectedSortable"
    }).disableSelection();
 });

/*
 *  Update workload calendar with custom module combination
 *  Send academic year ID along with request for specific modules
 *  On success, the server will return YMAF data for the calendar object to work with
 */
function calendarByModuleRequest() {

	// obtain the module codes from the user as an array
	var modules = [];
	$('#sortable2 li').each(function() { modules.push($(this).attr('id')) });
	modules = JSON.stringify(modules).replace("[", "(").replace("]", ")");

	// ajax request for YMAF data
	$.ajax({
		url: baseURL + "visualisation/fetchYMAFModuleSelection",
		data: {
			yearId: $('#year_id').val(),
			moduleSelection: modules
		},
		type: "POST",
		dataType: "json",
		success: function(response) {

      // create calendar object, use semester dates for frame
			var cal = new WorkloadCalendarView(response.dates);

      // pass ymaf data, using the perspective and selection chosen by the user
			cal.updateYMAFData(response.ymaf);
      if ($('#perspective').val() == 'student') {
        cal.updateDateValues(
          'student',
          $('#assess_id').val()
        );
      } else {
        cal.updateDateValues(
          'staff',
          $('#feed_id').val()
        );
      }

      // draw the workload 'heatmap' and create table of modules used
      cal.drawCalendar();
      cal.drawTable(response.table);
		},
		error: function(xhr, status, errorThrown) {
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		}
	});
}

/*
 *
 */
function calendarByCombinationRequest() {

  // ajax request for YMAF data
	$.ajax({
		url: baseURL + "visualisation/fetchYMAFCombinationSelection",
		data: {
			yearId: $('#year_id').val(),
			comboId: $('#combo_id').val()
		},
		type: "POST",
		dataType: "json",
		success: function(response) {

      // create calendar object, use semester dates for frame
			var cal = new WorkloadCalendarView(response.dates);

      // pass ymaf data, using the perspective and selection chosen by the user
			cal.updateYMAFData(response.ymaf);
      if ($('#perspective').val() == 'student') {
        cal.updateDateValues(
          'student',
          $('#assess_id').val()
        );
      } else {
        cal.updateDateValues(
          'staff',
          $('#feed_id').val()
        );
      }

      // draw the workload 'heatmap' and create table of modules used
			cal.drawCalendar();
      cal.drawTable(response.table);
		},
		error: function(xhr, status, errorThrown) {
			console.log( "Error: " + errorThrown );
			console.log( "Status: " + status );
			console.dir( xhr );
		}
	});
}
