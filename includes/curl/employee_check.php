<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);


$group_id = $_REQUEST['group'];
$type = $_REQUEST['type']; 	
$admin_category = $_REQUEST['admin_category']; 	

$division_name = $_REQUEST['division'];

if (isset($_REQUEST['division'])) {
    $sql_division = "select u.full_name,d3.short_name as subdivision
from tbl_users u
left join tbl_dimensions d3 on d3.id=u.emp_subdivision_id
where u.emp_division_id='".$division_name."'
and u.is_erp=1 and u.status='Active'";

    if ($group_id > 0)
        $sql_division.=" and u.group_id=".$group_id;
	
	if ($type > 0)
        $sql_division.=" and u.emp_type=".$type;
	
	if(!empty($admin_category)) {
		$sql_division.=" and u.admin_category='".$admin_category."'";
	}

    $sql_division.=" order by u.full_name";


    $result_division = $mysqli->query($sql_division);

    while ($row_division = $result_division->fetch_assoc()) {
        echo $row_division['full_name']."->";
    }
} else {
    $sql = "select d.id as div_id,
count(u.user_id) as emp_count
from tbl_users u
left join tbl_dimensions d
on d.id=u.emp_division_id
where u.is_erp=1 and u.status='Active'  ";
//and u.emp_division_id!=10
    if ($group_id > 0)
        $sql.=" and u.group_id=".$group_id;
	
	if ($type > 0)
        $sql.=" and u.emp_type=".$type;

	if(!empty($admin_category)) {
		$sql_division.=" and u.admin_category='".$admin_category."'";
	}

    $sql.=" group by d.id  order by d.full_name";

    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo $row['div_id'].'->'.$row['emp_count'].",";
    }
}
?>