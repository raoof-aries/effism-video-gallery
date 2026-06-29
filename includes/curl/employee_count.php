<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

$sql_division = "select count(user_id) as eff_cnt,emp_type from tbl_users where (emp_type=2 or emp_type=3 or emp_type=24 or emp_type=25) and status='Active'  group by emp_type";

    $result_division = $mysqli->query($sql_division);

    while ($row_division = $result_division->fetch_assoc()) {
        if($row_division['emp_type']==2){
            $eff_cnt0 =$row_division['eff_cnt'];
        }
        elseif($row_division['emp_type']==3){
            $eff_cnt =$row_division['eff_cnt'];
        }
        elseif($row_division['emp_type']==24){
            $eff_cnt1 =$row_division['eff_cnt'];
        }
        elseif($row_division['emp_type']==25){
            $eff_cnt2 =$row_division['eff_cnt'];
        }
           
         
    }
 echo $eff_cnt0."->";
 echo $eff_cnt."->";
echo $eff_cnt1."->";
echo $eff_cnt2."->";
$sql_division1 = "select count(user_id) as eff_cnt,emp_type from tbl_users where (emp_type!=2 and emp_type!=3 and emp_type!=24 and emp_type!=25) and status='Active' and emp_division_id!=10 group by emp_type";

    $result_division1 = $mysqli->query($sql_division1);

    while ($row_division1 = $result_division1->fetch_assoc()) {
            $eff_cnt3+=$row_division1['eff_cnt'];
           
         
    }
 echo $eff_cnt3;

?>