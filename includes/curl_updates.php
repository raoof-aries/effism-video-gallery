<?php 
//mysql_connect('localhost',root,'');
$con =new mysqli("p:"."localhost", "effism_eff", "{-Ea},fuEV{_", "effism_efficiency");

//mysql_select_db('ames');
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}



//insert into efficiency 
if(!empty($_REQUEST['type']) && $_REQUEST['type'] == 'DIVISION_CHANGE') {
	$employee_id = $_REQUEST['employee_id'];
	$edit_type = $_REQUEST['edit_type'];
	$reason = $_REQUEST['reason'];
	$old_value = $_REQUEST['old_value'];
	$new_value = $_REQUEST['new_value'];
	
	$sql = "INSERT INTO tbl_emp_edit_log(employee_id,change_type,remarks,from_value,to_value)
				VALUES('$employee_id','$edit_type','$reason','$old_value','$new_value')";
	mysqli_query($con,$sql);
}


?>