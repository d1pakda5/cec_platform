<?php 
//echo "entered";
include("../config.php");

$sql1="select * from employee_master where is_sale=1 and is_login_active=1";
	   
        $conn = new mysqli("localhost", "clickech_usr", "eclick@123", "clickech_account");
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		  } 
        $result = $conn->query($sql1);
        
       
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               echo "<option value='".$row["id"]."'>".$row["employee_name"]."</option>";
            }
        } else {
            echo "<option value=''></option>";
        }
    
?>