<!-- 	
	obj_eval.php

	GURPREET SINGH
 -->

<!-- include the db_connect.php file for the connection to DB -->

<?php
require 'db_connect.php'; 

// from db_connect
global $mysqli;

$obj_id = $_GET["o_id"];
$ser_id = $_GET["ser_id"];

$errors = array();

if (isset($_POST["submit_changes"])) {
	// var_dump($_POST);
	// var_dump($_FILES);

	if($_FILES['uploaded_file']['error'] == 0) {
        
        $file_name = mysqli_real_escape_string($mysqli, $_FILES['uploaded_file']['name']);
        $file_mime = mysqli_real_escape_string($mysqli, $_FILES['uploaded_file']['type']);
        $data = mysqli_real_escape_string($mysqli, file_get_contents($_FILES['uploaded_file']['tmp_name']));
        $size = intval($_FILES['uploaded_file']['size']);

		$below_exp = $_POST["below_exp"];
		$exc_exp = $_POST["exc_exp"];
		$meet_exp;

		if (isset($_POST["file_name"]) AND isset($_POST["type"])) {
			$name = $_POST["file_name"];
			$type = $_POST["type"];

			if ($below_exp < $exc_exp) {
		        $query = "INSERT INTO `QMS_obj_eval`(`name`, `type`, `size`, `data`, `obj_id`, `ser_id`, `below_exp`, `meet_exp`, `exc_exp`, `file_name`, `file_type`) VALUES ('$name','$type','$size','$data','$obj_id','$ser_id','$below_exp','$exc_exp','$exc_exp', '$file_name', '$file_mime')";
				$result = mysqli_query($mysqli, $query);
				if ($result)
					echo "Success";
				else 
					echo "No Success";

			} else {
				$errors[] = "Below Expectations must be smaller than Exceed Expectations.";
			}
		} else {
			$errors[] = "You must enter a Name and Type for the entry.";
		}
    }
}

if (isset($_POST["submit_updates"])) {
	// foreach ($_POST as $key => $value) {
	// 	echo $key, " ", $value;
	// 	echo "<br>";
	// }
	$new_name;
	$new_type;
	$new_below_exp;
	$new_exc_exp;
	$ID_to_edit;

	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 12) == "obj_eval_ID_") {
			$new_name = $value;
		} else if (substr($key, 0, 14) == "obj_eval_type_") {
			$new_type = $value;
		} else if (substr($key, 0, 10) == "below_exp_") {
			$new_below_exp = $value;
		} else if (substr($key, 0, 7) == "exc_exp") {
			$new_exc_exp = $value;
		} else if (substr($key, 0, 4) == "edit") {
			$ID_to_edit = substr($key, 4);
			if ($value == "update") {
				if (isset($new_name) AND isset($new_type)) {
					if ($new_below_exp < $new_exc_exp) {
						$new_uploaded_file = 'new_uploaded_file_' . $ID_to_edit;
						if($_FILES[$new_uploaded_file]['error'] == 0) {
							
							$new_data = mysqli_real_escape_string($mysqli, file_get_contents($_FILES[$new_uploaded_file]['tmp_name']));
							$new_size = intval($_FILES[$new_uploaded_file]['size']);
							$new_file_name = mysqli_real_escape_string($mysqli, $_FILES[$new_uploaded_file]['name']);
        					$new_file_mime = mysqli_real_escape_string($mysqli, $_FILES[$new_uploaded_file]['type']);
							$query = "UPDATE `QMS_obj_eval` SET `name`='$new_name',`type`='$new_type',size='$new_size',`below_exp`='$new_below_exp',`exc_exp`='$new_exc_exp', file_name='$new_file_name', file_type='$new_file_mime' WHERE ID = $ID_to_edit";
							$result = mysqli_query($mysqli, $query);
						} else {
							$query = "UPDATE `QMS_obj_eval` SET `name`='$new_name',`type`='$new_type',`below_exp`='$new_below_exp',`exc_exp`='$new_exc_exp' WHERE ID = $ID_to_edit";
							$result = mysqli_query($mysqli, $query);
						}
					} else {
						$errors[] = "Below Expectations must be smaller than Exceed Expectations.";
					}
				} else {
					$errors[] = "You must enter a Name and Type for the entry.";
				}
			} else if ($value == "delete") {
				$query = "DELETE FROM `QMS_obj_eval` WHERE ID = $ID_to_edit";
				$result = mysqli_query($mysqli, $query);
				$ID_to_edit = 0;
			}
		}
	}

}

?>

<html>

	<!-- HEADER -->
	<head>

		<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
			</ul>
		</nav>

		<?php 

			if ($errors) {
				echo "<h3 style='color:red'>Errors</H3> ";

				$output = array();
				
				foreach ($errors as $error) {
					$output[] = '<li>'. $error . '</li>';
				}

				echo '<ul>'. implode('', $output) .'</ul>';
			}

			$result = mysqli_query($mysqli, "SELECT `s_name` FROM QMS_services WHERE `s_id` = $ser_id");
			
			if ($result)
			{
				// print each row
				$row = mysqli_fetch_array($result);
				$service = $row["s_name"];
			}

		?>

		<title>Objectives of <?php echo $service; ?></title>

		<header>
			<h1><b>Objectives <?php echo $obj_id; echo " of ["; echo $ser_id; echo ": "; echo $service; echo "]";?></b></h1>
		</header>

		<style type="text/css"> 
			input[type="radio"]{margin: 0px 10px};} 
			input[type="file"]{margin: 0px 5};}

		</style>

	</head>

	<body>

		<!-- PRINT THE QMS_Goals TABLE -->

		<table align='center' width='100%' border='2' >
	 		<tr>
	 			<!-- header of the table -->
	 			<td colspan='4' align='center'>
	 				<h1>Objective Evaluation</h1>
	 			</td>
			</tr>

			<form action="" method="post" enctype="multipart/form-data">
			
				<tr align='center'>
					<th>Measuring Instrument</th>
					<th>PDF Documents</th>
					<th>Evaluation Codition</th>
					<th>Edit Entries</th>
				</tr>

				<?php
				$y = True;
				$query = "SELECT * FROM QMS_obj_eval WHERE ser_id = $ser_id AND obj_id = $obj_id";
				$result = mysqli_query($mysqli, $query);
				if(isset($_POST['Edit'])) {
					if ($result) {
						$tot_row = mysqli_num_rows($result);
						?>
						<form action="" method="post">
						<?php
						while ($row = mysqli_fetch_array($result)) {
							?>
							<td align='center' width='20%'>
								<?php echo "<b>Name</b>: ";?><input size="20" name="obj_eval_ID_<?php echo $row["ID"]; ?>" value="<?php echo $row["name"]; ?>">
								</input> 
								<?php echo "<b>Type</b>: ";
									$chk = $row["type"];
									if ($chk == "assignment")
									{
										?>
										<input type="radio" name="obj_eval_type_<?php echo $row["ID"]; ?>" value="assignment" checked="checked">Assignment
										<input type="radio" name="obj_eval_type_<?php echo $row["ID"]; ?>" value="exam">Exam
										<?php
									} else {
										?>
										<input type="radio" name="obj_eval_type_<?php echo $row["ID"]; ?>" value="assignment">Assignment
										<input type="radio" name="obj_eval_type_<?php echo $row["ID"]; ?>" value="exam" checked="checked">Exam
										<?
									}
								?>
							</td>

							<td align='center' width='30%'>
								<input type="file" name="new_uploaded_file_<?php echo $row["ID"]; ?>"></input>
								<?php echo $row["file_name"]; ?>
							</td>

							<td align='center' width='20%'>
								<?php 
									$checked = $row["below_exp"];
								?>
								Below Expectations &lt;: <select name="below_exp_<?php echo $row["ID"]; ?>">
												<?php
												    for ($i=1; $i<=100; $i++)
												    {
												    	if ($i % 10 == 0 AND $i == $checked) {
												        ?>
												            <option name="below_exp_<?php echo $i; ?>" value="<?php echo $i;?>" selected><?php echo $i;?></option>
												        <?php
												        } else if ($i % 10 == 0) {
												        ?>
												            <option name="below_exp_<?php echo $i; ?>" value="<?php echo $i;?>"><?php echo $i;?></option>
												        <?php	
												        }
												    }
												?>
											</select> <br>
								<?php 
									$checked = $row["exc_exp"];
								?>
								Exceed Expectations &ge;: <select name="exc_exp<?php echo $row["ID"]; ?>">
												<?php
												    for ($i=1; $i<=100; $i++)
												    {
												    	if ($i % 10 == 0 AND $i == $checked) {
												        ?>
												            <option name="exc_exp_<?php echo $row["ID"]; echo $i; ?>" value="<?php echo $i;?>" selected><?php echo $i;?></option>
												        <?php
												        } else if ($i % 10 == 0) {
												        ?>
												            <option name="exc_exp_<?php echo $row["ID"]; echo $i; ?>" value="<?php echo $i;?>"><?php echo $i;?></option>
												        <?php	
												        }
												    }
												?>
											</select> <br>
							</td>

							<td align='center' width='35%'>
								<input type="radio" name="edit<?php echo $row["ID"]; ?>" value="as-is" checked>As-is 
								<input type="radio" name="edit<?php echo $row["ID"]; ?>" value="update">Update 
								<input type="radio" name="edit<?php echo $row["ID"]; ?>" value="delete">Delete
							</td>

							</tr><?php 
						} ?>
						<tr>
							<td colspan='3'></td> 
							<td align='center'>
								<input type="Submit" name="submit_updates" size="4" value="Submit" style="height:150px; width:100px; font-size: 16px;"></input>
							</td> 
							</tr>
						</form>

						<?php
					}
				} else {
					if ($result) {
						$tot_row = mysqli_num_rows($result);
						while ($row = mysqli_fetch_array($result)) {
							?>
							<td align='center' width='40%'>
								<?php echo "<b>Name</b>: "; echo $row["name"]; ?>
								<?php echo " <b>Type</b>: "; echo $row["type"]; ?>
							</td>

							<td align='center' width='40%'><a href="download.php?id=<?php echo $row["ID"]; ?>"><?php echo $row["file_name"]; ?></td>

							<td align='center' width='50%'>
								Below Expectations: ~ <?php echo $row["below_exp"]; ?></br>
								Meet Expectations: <?php echo $row["below_exp"] + 1; ?> ~ <?php echo $row["exc_exp"] - 1; ?></br>
								Exceed Expectations: ~ <?php echo $row["exc_exp"]; ?>
							</td>

							<?php
								if ($y) {
									echo "<td align='center' rowspan=$tot_row><input name=Edit type=Submit value=Edit style='height:150px; width:100px; font-size: 16px;'></td>";
									$y = False;
								}
							?>
							</tr>

							<?php
						}
					}
				}
				?>

				<tr>
					<td width="30%" align='center'><input name="file_name" placeholder="Enter File Name Here"></input>
						<input type="radio" name="type" value="assignment">Assignment
						<input type="radio" name="type" value="exam">Exam
					</td>
					<td width="30%" align='center'>
						<input type="file" name="uploaded_file">
					</td>
					<td align='center'>
						Below Expectations &lt; : <select name="below_exp">
												<?php
												    for ($i=1; $i<=100; $i++)
												    {
												    	if ($i % 10 == 0) {
												        ?>
												            <option value="<?php echo $i;?>"><?php echo $i;?></option>
												        <?php
												        }
												    }
												?>
											</select> <br>
						Exceed Expectations &ge;: <select name="exc_exp">
												<?php
												    for ($i=1; $i<=100; $i++)
												    {
												    	if ($i % 10 == 0) {
												        ?>
												            <option value="<?php echo $i;?>"><?php echo $i;?></option>
												        <?php
												        }
												    }
												?>
											</select>
					</td>
					<td align='center'><input name="submit_changes" style='height:150px; width:100px; font-size: 16px;' type="Submit" value="Add New" font-size: 16px;></td>
				</tr>
			</form>

		</table>


	</body>
</html>