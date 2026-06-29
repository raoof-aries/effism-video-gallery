<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

 $from_date =$_REQUEST['from_date'];
$to_date = $_REQUEST['to_date'];
 $sql = " SELECT id, date, job_no, description, vessel_name, client, company_id, division_id, sub_division_id, job_value, added_date
FROM tbl_job WHERE division_id=3 AND date >= '".$from_date."' AND date <='".$to_date."'";
$result = $mysqli->query($sql);

$arr_data;

while($row = $result->fetch_assoc()) {
	$arr_data[] = array(
		"id"=>$row['id'],
		"date"=>$row['date'],
		"job_no"=>$row['job_no'],
		"description"=>$row['description'],
		"vessel_name"=>$row['vessel_name'],
		"client"=>$row['client'],
		"company_id"=>$row['company_id'],
		"division_id"=>$row['division_id'],
		"sub_division_id"=>$row['sub_division_id'],
		"job_value"=>$row['job_value'],
		"added_date"=>$row['added_date']
		);
}
//print_r($arr_data);
$arr = array(1=>array(1,2,3));
print $str1= json_encode($arr_data);

//var_dump(json_decode($str1, true));
//print $str1;
?>