/*global $:false,define:false*/
define([
	"manager",
	"constants",
	"lang",
	"generator"
], function(manager, C, L, generator) {

	"use strict";

	var MODULE_ID = "data-type-NumberRange";
	var LANG = L.dataTypePlugins.NumberRange;

	var _loadRow = function(rowNum, data) {
		return {
			execute: function() {
				$("#dtNumRangeMin_" + rowNum).val(data.rangeMin);
				$("#dtNumRangeMax_" + rowNum).val(data.rangeMax);
			},
			isComplete: function() { return true; }
		};
	};

	var _saveRow = function(rowNum) {
		return {
			rangeMin: $("#dtNumRangeMin_" + rowNum).val(),
			rangeMax: $("#dtNumRangeMax_" + rowNum).val()
		};
	};

	var _validate = function(rows) {
		var visibleProblemRows = [];
		var problemFields      = [];

		var intOnly = 	/^[\-\d]+$/;				// regular expression for the integers
		var int_float =	/^-?\d+\.?\d*$/				// regular expression for floats (1.0 or 1 both work)
		
		for (var i = 0; i < rows.length; i++) {

			var visibleRowNum = generator.getVisibleRowOrderByRowNum(rows[i]);
			var hasError = false;

			// gets the min and max value input by the user
			var numWordsMin = $.trim($("#dtNumRangeMin_" + rows[i]).val());
			var numWordsMax = $.trim($("#dtNumRangeMax_" + rows[i]).val());
				 
			// gets the value selected by the user in the examples column 
			var select_field = $.trim($("#dtExample_" + rows[i]).val());

			// if the user doesn't select anything add the column to the
			// problems field. set the test bool value to true, flaging an error.
			// also add te row to the 'visibleRowNum' array
			var test = false;
			if (select_field == 'select') {
				test = true;
				problemFields.push($("#dtExample_" + rows[i]));
				visibleProblemRows.push(visibleRowNum);
			}
			else if (select_field == 'integral_num') {
				// if the user selected the int value, the min max value is
				// tested against the int regex, if it passes then no error
				// other wise error is signaled. 
				if (numWordsMin === "" || !intOnly.test(numWordsMin)) {
					hasError = true;
					problemFields.push($("#dtNumRangeMin_" + rows[i]));
				}
				if (numWordsMax === "" || !intOnly.test(numWordsMax)) {
					hasError = true;
					problemFields.push($("#dtNumRangeMax_" + rows[i]));
				}

				// verify that the min is less than the max value
				// if not signal another error by setting the hasError flag.
				if (!hasError) {
					if (Number(numWordsMin) > Number(numWordsMax)) {
						console.log(numWordsMin, numWordsMax)
						hasError = true;
						problemFields.push($("#dtNumRangeMin_" + rows[i]));
						problemFields.push($("#dtNumRangeMax_" + rows[i]));
					}
				}

				// add the row to the visibleProblemRows array
				if (hasError) {
					visibleProblemRows.push(visibleRowNum);
				}
			}
			else if (select_field == 'real_num') {
				// if the user selected real_num, the min, max value is tested against
				// the float regex. if the test doesn't pass, flag an error.
				if (numWordsMin === "" || !int_float.test(numWordsMin)) {
					hasError = true;
					problemFields.push($("#dtNumRangeMin_" + rows[i]));
				}
				if (numWordsMax === "" || !int_float.test(numWordsMax)) {
					hasError = true;
					problemFields.push($("#dtNumRangeMax_" + rows[i]));
				}
				// also compare the min and max fields
				if (!hasError) {
					if (numWordsMin > numWordsMax) {
						hasError = true;
						problemFields.push($("#dtNumRangeMin_" + rows[i]));
						problemFields.push($("#dtNumRangeMax_" + rows[i]));
					}
				}
				// if any errors push the row to the visibleProblemRows array.
				if (hasError) {
					visibleProblemRows.push(visibleRowNum);
				}
			}
		}
 
		var errors = [];

		if (visibleProblemRows.length) {
			// if any rows have been added to the visibleProblemRows array
			// then if test was set, it means you didn't select a field.
			// if haserror was set, it means, either the max min values didn't pass
			// the regex test or min max were not entered correctly.
			if (test)
				errors.push({ els: problemFields, error: LANG.int_or_float + " <b>" + visibleProblemRows.join(", ") + "</b>"});
			if (hasError)
				errors.push({ els: problemFields, error: LANG.incomplete_fields + " <b>" + visibleProblemRows.join(", ") + "</b>"});
		}
		return errors;
	};

	manager.registerDataType(MODULE_ID, {
		validate: _validate,
		loadRow: _loadRow,
		saveRow: _saveRow
	});

});