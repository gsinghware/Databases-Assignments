<!-- 	
	index.html
	- Main page displayed on the web with the Goals and services tables.

	GURPREET SINGH
 -->

<!-- include the db_connect.php file for the connection to DB -->
<?php 
require 'db_connect.php'; 

// from db_connect
global $mysqli;

$errors = array();

if (isset($_POST["add_new"])) {
	$num_words = str_word_count($_POST["new_goal"]);
	if ($num_words < 10) {
		$errors[] = "You Goals statement must be greater than 10 Words";
	} 
	else if ($num_words > 30) {
		$errors[] = "You Goals statement must be less than 30 Words";
	} 
	else {
		$goal_stmt = (string)$_POST["new_goal"];
		$query = "INSERT INTO QMS_goals (g_statement) VALUES ('$goal_stmt')";
		$result = mysqli_query($mysqli, $query);
	}
}

if (isset($_POST["submit_changes"])) {
	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 4) == "edit") {
			$goal_to_edit = substr($key, 4);
			if ($_POST[$key] == "delete") { 
				$query = "DELETE FROM `QMS_goals` WHERE g_id = $goal_to_edit";
				$result = mysqli_query($mysqli, $query);
				$query = "DELETE FROM `QMS_obj_goal` WHERE g_id = $goal_to_edit";
				$result = mysqli_query($mysqli, $query);
				
			} else if ($_POST[$key] == "update") {
				$x = "goal_statement_" . $goal_to_edit;
				$new_goal_stmt = (string)$_POST[$x];

				$num_words = str_word_count($new_goal_stmt);
				if ($num_words < 10) {
					$errors[] = "Your New Goals statement must be greater than 10 Words";
				}
				else if ($num_words > 30) {
					$errors[] = "You New Goals statement must be less than 30 Words";
				} 
				else {
					$query = "UPDATE `QMS_goals` SET `g_statement`= '$new_goal_stmt' WHERE `g_id` = $goal_to_edit";
					$result = mysqli_query($mysqli, $query);
				}
			} else
				continue;
		}
	}
}

if (isset($_POST["submit_obj_changes"])) {

	$sid = $_POST["ser_id"];

	$obj_goal_ary = array();

	$x = True;

	var_dump($_POST);
	$save_obj;

	foreach ($_POST as $key => $value) {

		if (substr($key, 0, 14) == "obj_statement_") {
			$obj_to_edit = substr($key, 14);
			$goal_stmt = (string)$_POST[$key];
		}
		else if (substr($key, 0, 4) == "edit") {
			
			if ($_POST[$key] == "delete") {
				$query = "DELETE FROM `QMS_srv_obj` WHERE o_id = $obj_to_edit and s_id = $sid";
				$result = mysqli_query($mysqli, $query);
			} else if ($_POST[$key] == "update") {
				$x = "obj_statement_" . $obj_to_edit;
				$new_obj_stmt = (string)$_POST[$x];
				$query = "UPDATE `QMS_objectives` SET `o_statement`= '$new_obj_stmt'  WHERE `o_id` = $obj_to_edit";
				$result = mysqli_query($mysqli, $query);

				$query = "DELETE FROM `QMS_obj_goal` WHERE o_id = $obj_to_edit";
				$result = mysqli_query($mysqli, $query);

				foreach ($obj_goal_ary as $key => $value) {
					$query = "INSERT INTO QMS_obj_goal (o_id, g_id) VALUES ($obj_to_edit, $value)";
					$result = mysqli_query($mysqli, $query);
				}
			} else {
				$obj_goal_ary = array();
			}
		}
		else if (substr($key, 0, 11) == "new_obj_id_") {
			if (isset($_POST["new_obj_statement"]) && $x) {
				$new_stmt = (string)$_POST["new_obj_statement"];
				echo $new_stmt;
				$query = "INSERT INTO `QMS_objectives` (`o_statement`) VALUES ('$new_stmt')";
				$x = False;
				$result = mysqli_query($mysqli, $query);

				$save_obj = mysqli_insert_id($mysqli);
				echo $save_obj;

				$g_id = substr($key, 11);
				echo $g_id;

				$query2 = "INSERT INTO `QMS_obj_goal`(`o_id`, `g_id`) VALUES ($save_obj, $g_id)";
				$result = mysqli_query($mysqli, $query2);

				$query = "INSERT INTO `QMS_srv_obj`(`s_id`, `o_id`) VALUES ($sid, $save_obj)";
				$result = mysqli_query($mysqli, $query);

			}
			else if (isset($_POST["new_obj_statement"])) {
				$g_id = substr($key, 11);
				echo $g_id;

				$query2 = "INSERT INTO `QMS_obj_goal`(`o_id`, `g_id`) VALUES ($save_obj, $g_id)";
				$result = mysqli_query($mysqli, $query2);

			}
		}
		else if ($_POST[$key] == "Submit") {

		}
		else {
			$obj_goal_ary[] = substr($key, strlen($obj_to_edit));
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

		<title>QMS</title>

		<header>
			<h1><b>Quality Management System</b></h1>
		</header>

	</head>

	<body>

		<?php 

			if ($errors) {

				echo "<h3 style='color:red'>Errors</H3> ";

				$output = array();
				
				foreach ($errors as $error) {
					$output[] = '<li>'. $error . '</li>';
				}

				echo '<ul>'. implode('', $output) .'</ul>';
			}

			 if (isset($_POST['Edit_obj'])) {}
			 else {

		?>

		<!-- PRINT THE QMS_Goals TABLE -->

		<table cellpadding="5" align='center' width='100%' border='2' >
	 		<tr>
	 			<!-- header of the table -->
	 			<td colspan='4' align='center'>
	 				<h1>Goals</h1>
	 			</td>
			</tr>

			<tr align='center'>
				<th>Goal Id</th>
				<th>Goal Statement</th>
				<th>Edit Goals</th>
			</tr>

			<tr>
			<?php
			
				// returns boolean
				$result = mysqli_query($mysqli, "SELECT * FROM QMS_goals");

				if ($result)
				{
					$tot_row = mysqli_num_rows($result);
					// print each row

					$rowspan = 0;

					if(isset($_POST['Edit']))
					{
						?>
						<form action="" method="post">
						<?php
						while ($row = mysqli_fetch_array($result))
						{
							?>
								<td align='center'><?php echo $row["g_id"]; ?></td> 
								<td>
									<input size="150" name="goal_statement_<?php echo $row["g_id"]; ?>" value="<?php echo $row["g_statement"]; ?>">
									</input> 
								</td> 
								<td align='center'>
									<input type="radio" name="edit<?php echo $row["g_id"]; ?>" value="as-is" checked>As-is 
									<input type="radio" name="edit<?php echo $row["g_id"]; ?>" value="update">Update 
									<input type="radio" name="edit<?php echo $row["g_id"]; ?>" value="delete">Delete
								</td>
								</tr>
							<?php
						}
						?>
							<tr>
							<td colspan='2'></td> 
							<td align='center'>
								<input type="Submit" name="submit_changes" size="4" value="Submit" style="height:150px; width:100px; font-size: 16px;"></input>
							</td> 
							</tr>
						</form>
						<?php
					}
					else {
						$goals_array = array();

						while ($row = mysqli_fetch_array($result))
						{
							?> 
							<form action="" method="post">
								<td align='center'><?php echo $row["g_id"]; ?></td>
								<td><?php echo $row["g_statement"]; ?></td>
								<?php 
									$goals_array[] = $row["g_id"];
									if ($rowspan == 0)
									{
										echo "<td align='center' rowspan=$tot_row><input name=Edit type=Submit value=Edit style='height:150px; width:100px; font-size: 16px;'></td>";
										$rowspan += 1;
									}
								?>
								
								</tr>
							</form>
							<?php
						}
					}
				}
			?>
			
			<form action="" method="post">
				<td></td> 
				<td> <input size="150" placeholder="Insert New Goal Here" name="new_goal"></input></td> 
				<td colspan='2' align='center'><input type="Submit" name="add_new" size="4" value="Add New Goal" style="height:150px; width:100px; font-size: 16px;"></input></td> 
			</form>

		</table>

		<!-- PRINT THE QMS SERVICES TABLE -->

	</br></br>
	<?php 
	
	}

	if(isset($_POST['Edit'])) {}
	else if (isset($_POST['Edit_obj'])) {

		$goals_array = $_POST['result'];

		?>

		<table cellpadding="5" align='center' width='100%' border='2' >
			<tr align='center'>

				<th>OID</th>
				<th>Objective Statements</th>
				<?php
					for ($i = 0; $i <= count($goals_array) - 1; $i++) {
						echo "<th>G" . $goals_array[$i] . "</th>";
					}
				?>

				<th>Edit</th>
			</tr>
		
			<tr>
			<?php
			
			$s_id = $_POST["ser_id"];

			$result1 = mysqli_query($mysqli, "SELECT * FROM `QMS_srv_obj` WHERE `s_id` = $s_id");
			if ($result1)
			{
				?>

				<form action="" method="post">
				
				<?php

				while ($row1 = mysqli_fetch_array($result1))
				{
					$tot_row = mysqli_num_rows($result1);
					?>
					<td align='center'>
					<?php 
						$obj_id = $row1["o_id"];
						echo $obj_id; 
					?>
					</td>

					<td width='40%'>
						<?php
							$result2 = mysqli_query($mysqli, "	SELECT o_statement 
																FROM `QMS_objectives` 
																WHERE `o_id` = $obj_id");
							$row2 = mysqli_fetch_array($result2);
							$obj_st = $row2['o_statement'];
						?>
						<input size="130" name="obj_statement_<?php echo $obj_id; ?>" value="<?php echo $row2['o_statement']; ?>">
						</input>
					</td>

					<?php
						$result3 = mysqli_query($mysqli, "	SELECT g_id
															FROM `QMS_obj_goal`
															WHERE o_id = $obj_id");
						
						$output = array();
						while ($row3 = mysqli_fetch_array($result3))
							$output[] = $row3["g_id"];

						for ($i = 0; $i <= count($goals_array) - 1; $i++) {
							if (in_array($goals_array[$i], $output)) {
								echo "<td align='center'><input type=checkbox name=$obj_id". $goals_array[$i] . " value=Bike checked></td>";
							}
							else {
								echo "<td align='center'><input type=checkbox name=$obj_id". $goals_array[$i] . "  value=Bike></td>";
							}
						}
					?>

					<td align='center'>
						<input type="radio" name="edit<?php echo $obj_id; ?>" value="as-is" checked>As-is 
						<input type="radio" name="edit<?php echo $obj_id; ?>" value="update">Update 
						<input type="radio" name="edit<?php echo $obj_id; ?>" value="delete">Delete
					</td>

				</tr>
				<?php 
				}
				?>
				<tr>
					<!-- <td colspan='<?php echo count($goals_array) + 2; ?>'></td>  -->
					<td></td>
					<td><input name="new_obj_statement" placeholder="Insert New Objective Here" size="130"></input></td>
					<?php 
						for ($i = 0; $i <= count($goals_array) - 1; $i++) {
							echo "<td align='center'><input type=checkbox name=new_obj_id_". $goals_array[$i] . " value=Bike></td>";
						}
					?>
					<td align='center'>
						<input type="Submit" name="submit_obj_changes" size="4" value="Submit" style="height:150px; width:100px; font-size: 16px;"></input>
					</td> 
				</tr>

				<input type="hidden" name="ser_id" value="<?php echo $s_id ?>">

				</form>

				<?php
			}
			?>

		</table>
		
		<?php
	}
	else {
	?>
	<table cellpadding="5" align='center' width='100%' border='2' >
 		<tr>
 			<!-- header of the table -->
 			<td colspan='3' align='center'>
 				<h1>Services</h1>
 			</td>
		</tr>

		<tr align='center'>
			<th width='5%'>Service Id</th>
			<th width='15%'>Service Name</th>
			<th>Objectives</th>
		</tr>

		<tr>
		<?php
			// from db_connect
			global $mysqli;

			// returns boolean
			$result = mysqli_query($mysqli, "SELECT * FROM QMS_services");

			if ($result)
			{
				while ($row = mysqli_fetch_array($result))
				{
					?>

					<form action="" method="post">

					<td align='center'><a href="obj_of.php?s_id=<?php echo $row["s_id"]; ?>"><?php echo $row["s_id"]; ?></a></td> 
					<td><?php echo $row["s_name"]; ?> </td> 
					<td>
						<table cellpadding="5" align='center' width='100%' border='2' >
							<tr align='center'>

								<th>OID</th>
								<th>Objective Statements</th>
								<?php
									for ($i = 0; $i <= count($goals_array) - 1; $i++) {
										echo "<th>G" . $goals_array[$i] . "</th>";
									}
	
									foreach($goals_array as $value)
									{
									  echo '<input type="hidden" name="result[]" value="'. $value. '">';
									}
								?>

								<input type="hidden" name="ser_id" value="<?php echo $row["s_id"]; ?>">

								<th>Edit</th>
							</tr>

							<tr>

							<?php
								$result1 = mysqli_query($mysqli, "SELECT * FROM `QMS_srv_obj` WHERE `s_id` = $row[s_id]");
								if ($result1)
								{
									$bool = 1;

									while ($row1 = mysqli_fetch_array($result1))
									{
										$tot_row = mysqli_num_rows($result);
										?>

										<td align='center'>
											<a href="obj_eval.php?o_id=<?php echo $row1["o_id"]; ?>&ser_id=<?php echo $row1["s_id"]; ?>">
												<?php 
													$obj_id = $row1["o_id"];
													echo $row1["o_id"]; 
												?>
											</a>
										</td>

										<td width='70%'>
											<?php
												$result2 = mysqli_query($mysqli, "	SELECT o_statement 
																					FROM `QMS_objectives` 
																					WHERE `o_id` = $obj_id");
												$row2 = mysqli_fetch_array($result2);
												$obj_st = $row2['o_statement'];
												echo $row2['o_statement'];
											?>
										</td>

										<?php
											$result3 = mysqli_query($mysqli, "	SELECT g_id
																				FROM `QMS_obj_goal`
																				WHERE o_id = $obj_id");
											
											$output = array();
											while ($row3 = mysqli_fetch_array($result3))
												$output[] = $row3["g_id"];

											for ($i = 0; $i <= count($goals_array) - 1; $i++) {
												if (in_array($goals_array[$i], $output)) {
													echo "<td align='center'><svg width=20 height=20><circle cx=10 cy=10 r=5 fill=purple /></svg></td>";
												}
												else {
													echo "<td align='center'><svg width=20 height=20></svg></td>";
												}
											}

											if ($bool) {
												echo "<td align='center' rowspan=$tot_row><input name=Edit_obj type=Submit value=Edit font-size: 16px;'></td>";
												$bool = 0;
											}

										?>	

										</tr>

									

										<?php		
									}
								}
							?>

						</table>
						</form>
					</td>
					<tr>
					<?php
				}
			}
		?>

	</table>

	<?php } ?>



	</body>
</html>