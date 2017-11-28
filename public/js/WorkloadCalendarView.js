/*
 *	Calendar: wrapper class for D3 operations
 */
function WorkloadCalendarView(acYearData) {

	/* PRIVATE METHODS */

	// Internal method for modifying day number (y axis of calendar)
	var modDayNum = function(origDayNum) {

		// e.g. Sun: 0 becomes 6; Weds: 3 becomes 2, and so on
		// 0 - 6 should instead represent Monday to Sunday
		return (origDayNum + 6) % 7;
	}

	// Internal method for modifying week to academic week number (x axis of calendar)
	var modWeekNum = function(origDate) {

		// calculate the number of the days from the 1st of September
		var days = Math.floor((origDate - calcDrawStart) / (1000*60*60*24));

		// add the day number of September 1st to difference and modulo by 7 to get week number
		return Math.floor((days + dayNumCalcStart) / 7);
	}

	// Internal method for modifying academic month (to center text)
	var modMonthNum = function(origDate) {

		var month = origDate.getMonth();
		if (month >= 8) { return month - 8; }
		else { return month + 4; }

	}

	/* PRIVATE INSTANCE VARIABLES */

	// academic year ID and semester start/end dates
	var acYear = acYearData.aca_year;
	var sem1start = acYearData.sem_1_start;
	var sem1end = acYearData.sem_1_end;
	var sem2start = acYearData.sem_2_start;
	var sem2end = acYearData.sem_2_end;
	var sem3start = acYearData.sem_3_start;
	var sem3end = acYearData.sem_3_end;

	// use the ID to create start and end years, create Date and store day number for 1st sept
	var startYear = parseInt(acYear.substring(0,4));
	var endYear = startYear + 1;
	var calcDrawStart = new Date(startYear, 8, 1);
	var dayNumCalcStart = modDayNum(calcDrawStart.getDay());

	// define strings for month names for x axis of calendar
	var monthNames = [
		"Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sept", "Oct", "Nov", "Dec"
	];

	// define variables for YMAF data and dates with values
	var ymaf;
	var dateValues;

	/* PUBLIC METHODS */

	// Accessor method for crude String representation of dates
	this.getCrudeDetails = function() {
		return "Academic Year: " + acYear +
				" // Semester 1: " + sem1start + " - " + sem1end +
				" // Semester 2: " + sem2start + " - " + sem2end +
				" // Semester 3: " + sem3start + " - " + sem3end;
	}

	// Publish crude details to HTML element
	this.publishCrudeDetails = function(element) {
		document.getElementById(element).innerHTML = this.getCrudeDetails();
	}

	// Update the calendar year (mutator)
	this.updateCalendarYear = function(newAcYearData) {

		// update all instance variables
		acYear = newAcYearData.aca_year;
		sem1start = newAcYearData.sem_1_start;
		sem1end = newAcYearData.sem_1_end;
		sem2start = newAcYearData.sem_2_start;
		sem2end = newAcYearData.sem_2_end;
		sem3start = newAcYearData.sem_3_start;
		sem3end = newAcYearData.sem_3_end;
		startYear = parseInt(acYear.substring(0,4));
		endYear = startYear + 1;
		calcDrawStart = new Date(startYear, 8, 1);
		dayNumCalcStart = modDayNum(calcDrawStart.getDay());
	}

	// Update YMAF data
	this.updateYMAFData = function(newYMAF) {

		// update instance variables; create array for JS Objects
		ymaf = newYMAF;
		dateValues = [];

		// loop through semester dates and create an object for each
		var semDays = Math.floor((new Date(sem3end) - new Date(sem1start)) / (1000*60*60*24));
		var objDate = new Date(sem1start);
		for (var i = 0; i < semDays; i++) {

			// push new object onto array containing date and value
			objDate = new Date(objDate.setDate(objDate.getDate() + 1));
			strRep = function(objDate) {

				var strRep = objDate.getFullYear() + "-";
				var month = objDate.getMonth() + 1;
				if (month < 10) { month = "0" + month; }
				var day = objDate.getDate();
				if (day < 10) { day = "0" + day; }
				return strRep + month + "-" + day;

			}
			dateValues.push({
				DATE: strRep(objDate),
				VALUE: 0,
				STRING: ""
			});
		}
	}

	// update date values based on perspective (staff/student) and selection (none or assess/feed id)
	this.updateDateValues = function(perspective, selection) {

		// if a student or staff perspective was provided, loop through each assessment (ymaf entry)
		if (perspective === 'student') {

			ymaf.forEach(function(assessment) {
				dateCompare(assessment, 'student', selection);
			});

		} else if (perspective === 'staff') {

			ymaf.forEach(function(assessment) {
				dateCompare(assessment, 'staff', selection);
			});

		} else {

			// exit method here if an invalid perspective type was provided
			console.log("unknown perspective type passed to 'updateDateValues'");
			return;
		}

		// internal function: compare assessment date to calendar date (should value be increased?)
		function dateCompare(item, perspective, selection) {

			// Student: work with set_ and due_dates; Staff: work with due_ and feed_dates
			if (perspective === 'student') {

				// if no selection was made, or if the assessment type id matches the selection, proceed
				if (selection === 'all' || item.assess_id == selection) {

					// if the start and due date match, just increase the value for the day once
					if (item.set_date == item.due_date) {
						increaseDateValue(item.set_date, (item.module_code + ": " + item.title), 1);
					} else if (item.set_date > item.due_date) {
						// make sure the console displays any problematic dates
						console.log("!! ERROR !!: " + item.set_date + " to " + item.due_date);
					} else {
						// set the start date as a new variable, pass day range to increase function
						var dayRange = Math.floor((new Date(item.due_date) - new Date(item.set_date)) / (1000*60*60*24));
						increaseDateValue(item.set_date, (item.module_code + ": " + item.title), dayRange + 1);
					}
				}

			} else {

				// if no selection was made, or if the feedback type id matches the selection, proceed
				if (selection === 'all' || item.feed_id == selection) {

					// if the due and feed date match, just increase the value for the day once
					if (item.due_date == item.feed_date) {
						increaseDateValue(item.due_date, (item.module_code + ": " + item.title), 1);
					} else if (item.due_date > item.feed_date) {
						// make sure the console displays any problematic dates
						console.log("!! ERROR !!: " + item.due_date + " to " + item.feed_date);
					} else {
						// set the start date as a new variable, pass day range to increase function
						var dayRange = Math.floor((new Date(item.feed_date) - new Date(item.due_date)) / (1000*60*60*24));
						increaseDateValue(item.due_date, (item.module_code + ": " + item.title), dayRange + 1);
					}
				}
			}
		}

		// internal function: increase date value
		function increaseDateValue(dateValue, assessTitle, days) {
			var dayCount = false;
			dateValues.forEach(function (arrayItem) {
				if (dateValue == arrayItem.DATE || (dayCount && days > 0)) {
					days--;
					arrayItem.STRING += "\n" + assessTitle;
					if (arrayItem.VALUE == 0) { arrayItem.VALUE = .01; }
					else { arrayItem.VALUE = (parseFloat(arrayItem.VALUE) + 0.01); }
					if (days > 0) { dayCount = true; }
				}
			});
		}
	}

	// Draw the calendar using D3
	this.drawCalendar = function() {

		// temporary fix: allows calendar to be redrawn
		document.getElementById('calendar').innerHTML = '';

		// set the width height and cell size + academic year boundaries
		var width = 1200,
			height = 190,
			cellSize = 21;
			acYearStart = new Date(sem1start);
			calDrawStart = new Date(acYearStart.getFullYear(), acYearStart.getMonth(), 1);
			acYearEnd = new Date(sem3end);
			academicYear = "" + acYearStart.getFullYear() + " - " + acYearEnd.getFullYear();

		// create time formatters
		var day = d3.time.format("%w"),				// weekday as a decimal number [0(Sunday),6]
			week = d3.time.format("%U"),			// week number as a decimal number [00,53]
			percent = d3.format(".1%"),				// string formatting for percentage
			format = d3.time.format("%Y-%m-%d");	// full format: year, month, date (yyyy-mm-dd)

		// colour scaling - set the domain (?) and use a function to choose a CSS selector
		var color = d3.scale.quantize()
			.domain([0, .15])
			.range(d3.range(15).map(function(d) {
				// if value is greater than the highest gradient, limit to highest gradient
				if (d > 14) d = 14;
				return "q" + d;
			}));

		// append SVG elements to the body
		var svg = d3.select("#calendar").selectAll("svg")
				.data(d3.range(2013, 2014))
				.enter().append("svg")
					.attr("width", width)
					.attr("height", height)
					.attr("class", "RdYlGn")
					.append("g")
						.attr("transform", "translate(" + ((width - cellSize * 53) / 2)
						+ "," + (height - cellSize * 7 - 1) + ")");

		// add vertical text to each SVG element
		svg.append("text")
			.attr("transform", "translate(-9," + cellSize * 3.5 + ")rotate(-90)")
			.style("text-anchor", "middle")
			.style("font-size", "20")
			.text(academicYear);

		// draw a rectangle for each day of the year
		var rect = svg.selectAll(".day")
			.data(function(d) {
				return d3.time.day.range(calDrawStart, acYearEnd);
			})
			.enter().append("rect")
				.attr("class", "day")
				.attr("width", cellSize)
				.attr("height", cellSize)
				.attr("x", function(d) { return modWeekNum(d) * cellSize; })
				.attr("y", function(d) { return modDayNum(d.getDay()) * cellSize; })
				.datum(format);

		// provide a text box for each rectangle, providing further info when the mouse hovers over it
		rect.append("title")
			.text(function(d) { return d; });

		// draws a border for each month: pass the year range, append a path to each month,
		// give CSS properties and call monthPath function to create path
		var month = svg.selectAll(".month")
			.data(function(d) {
				return d3.time.months(
					new Date(acYearStart.getFullYear(), acYearEnd.getMonth() + 1, 1),
					new Date(acYearEnd.getFullYear(), acYearEnd.getMonth() + 1, 1)
				);
			})
			.enter().append("g");

		month.append("path")
			.attr("class", "month")
			.attr("d", monthPath);
		month.append("text")
			.attr("x", function(d) { return (modMonthNum(d) * (width / 13.2)) + 60; })
			.attr("y", -10)
			.style("text-anchor", "middle")
			.style("fill", "black")
			.style("font-size", "20")
			.text(function(d) { return monthNames[d.getMonth()]; });

		// check if YMAF data exists
		if (typeof ymaf != 'undefined') {

			// create a nest object
			var data = d3.nest()
				.key(function(d) { return d.DATE; })
				.rollup(function(d) { return [d[0].VALUE, d[0].STRING]; })
				.map(dateValues);

			// colour in the cells and append the text
			rect.filter(function(d) { return d in data; })
				.attr("class", function(d) { return "day " + color(data[d][0]); })
				.select("title")
					.text(function(d) {
						return d + ": " + Math.floor(data[d][0] * 100) + " assignments due:" +
							data[d][1];
					});
		}

		// define function for creating month path
		function monthPath(t0) {
			var t1 = new Date(t0.getFullYear(), t0.getMonth() + 1, 0),
				d0 = +modDayNum(t0.getDay()), w0 = +modWeekNum(t0),
				d1 = +modDayNum(t1.getDay()), w1 = +modWeekNum(t1);
			return "M" + (w0 + 1) * cellSize + "," + d0 * cellSize
				+ "H" + w0 * cellSize + "V" + 7 * cellSize
				+ "H" + w1 * cellSize + "V" + (d1 + 1) * cellSize
				+ "H" + (w1 + 1) * cellSize + "V" + 0
				+ "H" + (w0 + 1) * cellSize + "Z";
		}

		d3.select(self.frameElement).style("height", "2910px");
	}

	// draw module table
	this.drawTable = function(tableData) {
		var tbody = $('#tableOfModulesBody');
		tbody.html("");
		var tr;
		for (var i = 0; i < tableData.length; i++) {
			tr = $('<tr/>');
			tr.append("<td>" + tableData[i].module_code + "</td>");
			tr.append("<td>" + tableData[i].title + "</td>");
			tr.append("<td>" + tableData[i].credits + "</td>");
			tr.append("<td>" + tableData[i].total + "</td>");
			tbody.append(tr);
		}
	}
}
