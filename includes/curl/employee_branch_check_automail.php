<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

  $sql = "select d.id as div_id,u.group_id,
count(u.user_id) as emp_count
from tbl_users u
left join tbl_dimensions d
on d.id=u.emp_division_id
where u.is_erp=1 and u.status='Active'  ";


    $sql.=" group by d.id,u.group_id  order by d.full_name";

    $result = $mysqli->query($sql);

    while ($row = $result->fetch_assoc()) {
        echo $row['div_id'].'-'.$row['group_id'].'->'.$row['emp_count'].",";
    }

?>