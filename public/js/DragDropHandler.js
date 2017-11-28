window.onload = function() {
	$( "#sortable1, #sortable2" ).sortable({
		connectWith: ".connectedSortable"
	}).disableSelection();
};

// create combination function
function createCombination() {

	// obtain the module codes from the user as an array
	var modules = [];
	$('#sortable2 li').each(function() { modules.push($(this).attr('id')) });

	// send modules via Ajax request
	$.ajax({
		url: baseURL + "data/createModuleCombination",
		data: {
			comboTitle: $('#comboTitle').val(),
			moduleSelection: JSON.stringify(modules)
		},
		type: "POST",
		dataType: "html",
		success: function(response) {
			if (response == "ok" ) {
				alert("Cobination created added! Click OK to be redirected");
				window.location.replace(baseURL + "data");
			}
			else
				alert("There was a problem with the data. Please try again.");
		},
		error: function(XMLHttpRequest, textStatus, exception) { alert("Ajax failure\n" + exception); },
		async: true
	});
}
