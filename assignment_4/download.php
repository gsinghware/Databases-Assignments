<?php 
require 'db_connect.php'; 

// from db_connect
global $mysqli;

if(isset($_GET['id'])) {
    
    $id = intval($_GET['id']);
 
    if($id <= 0) {
        die('The ID is invalid!');
    } else {
        $query = "SELECT `file_name`, `file_type`, `size`, `data` FROM `QMS_obj_eval` WHERE `id` = $id";

        $result = mysqli_query($mysqli, $query);
 
        if($result) {

            if($result == 1) {
            
                $row = mysqli_fetch_assoc($result);
 
                header("Content-Type: ". $row['file_type']);
                header("Content-Length: ". $row['size']);
                header("Content-Disposition: attachment; filename=". $row['file_name']);
                
                echo $row['data'];
            }
            else {
                echo 'Error! No image exists with that ID.';
            }
            @mysqli_free_result($result);
        }
        else {
            echo "Error! Query failed";
        }
        @mysqli_close($dbLink);
    }
}
else {
    echo 'Error! No ID was passed.';
}
?>