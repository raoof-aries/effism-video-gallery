<?php
ini_set('display_errors', '1');
include_once("../connect.inc.php");
$array_final = str_replace('\"','"',$_POST['update_salary']);
$data = json_decode($array_final);
//print_r($data);
if(!empty($array_final)) {
	//$data = json_decode($_POST['update_salary']);
	foreach ($data as $value) {
		    $sql_update = "UPDATE tbl_users
						SET gross_salary ='".$value->gross_salary."'
						WHERE hr_id ='".$value->employee_id."'";
						
		$mysqli->query($sql_update);
	}
}
?>