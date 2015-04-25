<?php

/**
 * @package DataTypes
 */

class DataType_NumberRange extends DataTypePlugin {
	protected $isEnabled = true;
	protected $dataTypeName = "Number Range";
	protected $dataTypeFieldGroup = "numeric";
	protected $dataTypeFieldGroupOrder = 30;
	protected $jsModules = array("NumberRange.js");

	public function generate($generator, $generationContextData) {
		$options = $generationContextData["generationOptions"];
		
		// the options array contains all the selected options
		// first verify what the user selected in the examples column
		// if the user selected int, then compute the rand in (min, max) range
		// otherwise if the user selected real_num then verify the radio button
		// selected by the user, and compute the rands accordingly.
		if ($options["ex"] == "real_num")
		{	
			if ($options["opt"] == "as_is")
				return array(
					"display" => $options["min"] + mt_rand() / mt_getrandmax() * ($options["max"] - $options["min"])
				);
			else if ($options["opt"] == "ceil")
				return array(
					"display" => ceil($options["min"] + mt_rand() / mt_getrandmax() * ($options["max"] - $options["min"]))
				);
			else if ($options["opt"] == "floor")
				return array(
					"display" => floor($options["min"] + mt_rand() / mt_getrandmax() * ($options["max"] - $options["min"]))
				);
			else 
				return array(
					"display" => round($options["min"] + mt_rand() / mt_getrandmax() * ($options["max"] - $options["min"]))
				);
		}
		else
		{	
			return array(
				"display" => mt_rand($options["min"], $options["max"])
			);
		}
	}

	public function getRowGenerationOptions($generator, $postdata, $column, $numCols) {
		if ((empty($postdata["dtNumRangeMin_$column"]) && $postdata["dtNumRangeMin_$column"] !== "0") ||
			(empty($postdata["dtNumRangeMax_$column"]) && $postdata["dtNumRangeMax_$column"] !== "0")) {
			return false;
		}
		if (!is_numeric($postdata["dtNumRangeMin_$column"]) || !is_numeric($postdata["dtNumRangeMax_$column"])) {
			return false;
		}
		
		// Added "opt" to the options array to pass in the selected
		// options from the radio buttons for the real num.
		// Added "ex" to the options array to pass in the selected
		// options from the drop down in the examples column (real or int).
		$options = array(
			"min" => $postdata["dtNumRangeMin_$column"],
			"max" => $postdata["dtNumRangeMax_$column"],
			"opt" => $postdata["opt_$column"],
			"ex" => $postdata["dtExample_$column"]
		);

		return $options;
	}

	public function getOptionsColumnHTML() {
		$html =<<<END
&nbsp;{$this->L["between"]} <input type="text" name="dtNumRangeMin_%ROW%" id="dtNumRangeMin_%ROW%" style="width: 30px" value="1" />
{$this->L["and"]} <input type="text" name="dtNumRangeMax_%ROW%" id="dtNumRangeMax_%ROW%" style="width: 30px" value="10" />
</br>

<!-- the radio buttons that get shown when the user selects
	 real_num, and are hidden when real num is not selected. -->

<div id="real_num">
	<input type="radio" name="opt_%ROW%" id="opt_%ROW%" value="as_is" checked>As is
	<input type="radio" name="opt_%ROW%" id="opt_%ROW%" value="round">Round
	<input type="radio" name="opt_%ROW%" id="opt_%ROW%" value="ceil">Ceil
	<input type="radio" name="opt_%ROW%" id="opt_%ROW%" value="floor">Floor
</div>

END;
	
	return $html;
	}

	// getExampleColumnHTML(): gets displayed in the Examples column
	// when the NumberRange Datatype gets selected.
	public function getExampleColumnHTML() {
		$html =<<< END

	// <script>
	// 	$("#dtExample_" + rowNum).change(function() { 			// on options change	
	// 		if ($(this).val() === 'real_num') { 				// if the selected option is 'real_num'
	// 			$( "#real_num" ).show();					// show the radio buttons
	// 		} else {
	// 			$( "#real_num" ).hide();					//	otherwise hide the radio button
	// 		}
	// 	});
	// </script>

	
	<!-- 	These are the options to display when user selects 
			the NumberRange DataTypes -->

	<select style="width: 100%" name="dtExample_%ROW%" id="dtExample_%ROW%">
		<option value="select">Please Select</option>
		<option value="integral_num">Integral Number</option>
		<option value="real_num">Real Number</option>
	</select>

END;
		return $html;
	}

	public function getDataTypeMetadata() {
		return array(
			"type" => "numeric",
			"SQLField" => "mediumint default NULL",
			"SQLField_Oracle" => "varchar2(50) default NULL",
			"SQLField_MSSQL" => "INTEGER NULL",
			"SQLField_Postgres" => "integer NULL"
		);
	}

	public function getHelpHTML() {
		return "<p>{$this->L["help"]}</p>";
	}
}
