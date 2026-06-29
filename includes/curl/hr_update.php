<?php
ini_set('display_errors', '0');
include_once("../connect.inc.php");
$array_final = str_replace('\"','"',$_POST['update_salary']);
$data = json_decode($array_final);
//print_r($data);
if(!empty($array_final)) {
	//$data = json_decode($_POST['update_salary']);
	foreach ($data as $value) {
		 print $sql_update = "UPDATE tbl_users
						SET marital_status ='".$value->marital_status."',
						home_town ='".$value->home_town."',
						address ='".$value->address."'
						WHERE hr_id ='".$value->employee_id."'";
		$mysqli->query($sql_update);
	}
}
?>