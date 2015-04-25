<!-- 	
	index.html
	- Main page displayed on the web with the Goals and services tables.

	GURPREET SINGH
 -->

<!-- include the db_connect.php file for the connection to DB -->
<?php require 'db_connect.php'; ?>

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

		<!-- PRINT THE QMS_Goals TABLE -->

		<table align='center' width='50%' border='2' style='margin: 10px 10px 10px 10px'>
	 		<tr>
	 			<!-- header of the table -->
	 			<td colspan='2' align='center'>
	 				<h1>Goals</h1>
	 			</td>
			</tr>

			<tr align='center'>
				<th>Goal Id</th>
				<th>Goal Statement</th>
			</tr>

			<tr>
			<?php
				// from db_connect
				global $mysqli;

				// returns boolean
				$result = mysqli_query($mysqli, "SELECT * FROM QMS_goals");

				if ($result)
				{
					// print each row
					while ($row = mysqli_fetch_array($result))
					{
						?> 
						<td align='center'><?php echo $row["g_id"]; ?> </td> 
						<td><?php echo $row["g_statement"]; ?> </td> 
						<tr>
						<?php
					}
				}
			?>

		</table>

		<!-- PRINT THE QMS SERVICES TABLE -->

		<table align='center' width='20%' border='2' style='margin: 10px 10px 10px 10px'>
	 		<tr>
	 			<!-- header of the table -->
	 			<td colspan='2' align='center'>
	 				<h1>Services</h1>
	 			</td>
			</tr>

			<tr align='center'>
				<th>Service Id</th>
				<th>Service Name</th>
			</tr>

			<tr>
			<?php
				// from db_connect
				global $mysqli;1

				// returns boolean
				$result = mysqli_query($mysqli, "SELECT * FROM QMS_services");

				if ($result)
				{
					// print each row
					while ($row = mysqli_fetch_array($result))
					{
						?> 
						<td align='center'><a href="obj_of.php?s_id=<?php echo $row["s_id"]; ?>"><?php echo $row["s_id"]; ?></a></td> 
						<td><?php echo $row["s_name"]; ?> </td> 
						<tr>
						<?php
					}
				}
			?>

		</table>



	</body>
</html>