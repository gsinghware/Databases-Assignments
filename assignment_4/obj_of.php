<!-- 	
	obj_of.html
	- display the objectives and the associated goals of the service

	GURPREET SINGH
 -->

<!-- include the db_connect.php file for the connection to DB -->
<?php require 'db_connect.php';?>

<html>

	<!-- HEADER -->
	<head>

		<nav>
			<ul>
				<li><a href="index.php">Home</a></li>
			</ul>
		</nav>

		<?php 
			// from db_connect
			global $mysqli;

			$ser_id = $_GET["s_id"];

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
			<h1><b>Objectives of <?php echo $service; ?></b></h1>
		</header>

	</head>

	<body>

		<!-- PRINT THE QMS_Goals TABLE -->

		<table align='center' width='60%' border='2' style='margin: 10px 10px 10px 10px'>
	 		<tr>
	 			<!-- header of the table -->
	 			<td colspan='3' align='center'>
	 				<h1>Objectives</h1>
	 			</td>
			</tr>

			<tr align='center'>
				<th>Obj Id</th>
				<th>Objective Statement</th>
				<th>Associated Goals</th>
			</tr>

			<tr>
				<?php 
					$result = mysqli_query($mysqli, "SELECT * FROM `QMS_srv_obj` WHERE `s_id` = $ser_id");
					
					if ($result)
					{
						while ($row = mysqli_fetch_array($result))
						{
							?>
							<td align='center'>
								<?php 
									$obj_id = $row["o_id"]; 
									echo $row["o_id"]; 
								?>
							</td>
							
							<td align='center'>
								<?php
									$result2 = mysqli_query($mysqli, "	SELECT o_statement 
																		FROM `QMS_objectives` 
																		WHERE `o_id` = $obj_id");
									$row2 = mysqli_fetch_array($result2);
									$obj_st = $row2['o_statement'];
									echo $row2['o_statement'];
								?>
							</td>

							<td align='center'>
							
								<?php
									$result3 = mysqli_query($mysqli, "	SELECT g_id
																		FROM `QMS_obj_goal`
																		WHERE o_id = $obj_id");
									
									$output = array();
									while ($row3 = mysqli_fetch_array($result3))
										$output[] = $row3["g_id"];

									echo implode(', ', $output);

								?>
							</td>
							

							</tr>
							
							<?php
							
						}
					}
				?>
			</tr>

		</table>


	</body>
</html>