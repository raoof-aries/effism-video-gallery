<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);
if(isset($_REQUEST['id'])){
    $id=$_REQUEST['id'];
    $short_name=$_REQUEST['short_name'];
    $full_name=$_REQUEST['full_name'];
    $dimension_type=$_REQUEST['dimension_type'];
    $hr_id=$_REQUEST['hr_id'];
    $sql_division="insert into tbl_dimensions(id,short_name,full_name,dimension_type,hr_dimension_id,is_active) values($id,'".$short_name."','".$full_name."',$dimension_type,$hr_id,1)";
    $mysqli->query($sql_division);
}
?>