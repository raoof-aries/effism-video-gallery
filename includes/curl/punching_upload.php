<?php

include("../connect.inc.php");
$punch_value=$_POST['punch_value'];

//  $punch_value="('3090','AG00003567','2024-07-29','16:25:07')";

 $punch_query="insert into tbl_punch(hr_id,employee_code,punch_date,  punch_time) values".$punch_value.";";

    $result = $mysqli->query($punch_query);


$punch_query=$mysqli->query("update tbl_punch p,tbl_users u set p.user_id=u.user_id WHERE p.hr_id=u.hr_id and p.user_id=0;");


?>