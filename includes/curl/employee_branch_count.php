<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

$sql_division = "select d.id as div_id,count(u.user_id) as emp_count from tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id where u.status='Active' and u.group_id= 1";
//and u.emp_division_id!=10 ;
    $result_division = $mysqli->query($sql_division);

    while ($row_division = $result_division->fetch_assoc()) {
            $eff_cnt =$row_division['emp_count'];
           
         
    }
 echo $eff_cnt."->";
$sql_division1 = "select d.id as div_id,count(u.user_id) as emp_count from tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id where u.status='Active' and u.group_id= 2";
//and u.emp_division_id!=10 
    $result_division1 = $mysqli->query($sql_division1);

    while ($row_division1 = $result_division1->fetch_assoc()) {
            $eff_cnt1 =$row_division1['emp_count'];
           
         
    }
 echo $eff_cnt1;

?>