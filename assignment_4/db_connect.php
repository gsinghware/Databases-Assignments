<!-- 	
	GURPREET SINGH  
-->

<?php
        /* connecting to the database with username and password selecting
        the dababase, it will throw an error if unable to connect to DB */
        
        // mysqli_connect($host, $username, $pass, $database name)
        $mysqli = mysqli_connect("localhost","root","","S1533618");

        // check connection
        if ($mysqli->connect_errno) {
    		echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    	}
?>