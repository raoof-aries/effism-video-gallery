<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

 $from_date =$_REQUEST['from_date'];
$to_date = $_REQUEST['to_date'];
  $sql = " SELECT hr_id, gender
FROM tbl_users WHERE is_erp=1 AND status='Active'";
$result = $mysqli->query($sql);
$arr_data;

while($row = $result->fetch_assoc()) {
	$arr_data[] = array(
	    "hr_id"=>$row['hr_id'],
		"gender"=>$row['gender']
		);
}
//print_r($arr_data);
$arr = array(1=>array(1,2,3));
print $str1= json_encode($arr_data);

//var_dump(json_decode($str1, true));
//print $str1;
?>