<?php

include("../connect.inc.php");

$effism_branch_sql = "select group_id,count(user_id) as cnt from tbl_users where status='Active'  group by group_id order by group_id";
$effism_branch_result = $mysqli->query($effism_branch_sql);
$arr_data=array();

while($effism_branch_row = $effism_branch_result->fetch_assoc()) {
	$arr_data['group'][$effism_branch_row['group_id']] =$effism_branch_row['cnt'];
}


$effism_company_sql = "select emp_company_id,count(user_id) as cnt from tbl_users where status='Active'  group by emp_company_id order by emp_company_id";
$effism_company_result = $mysqli->query($effism_company_sql);
while($effism_company_row = $effism_company_result->fetch_assoc()) {
	$arr_data['company'][$effism_company_row['emp_company_id']] =$effism_company_row['cnt'];
}

$effism_division_sql = "select emp_division_id,count(user_id) as cnt from tbl_users where status='Active'  group by emp_division_id order by emp_division_id";
$effism_division_result = $mysqli->query($effism_division_sql);
while($effism_division_row = $effism_division_result->fetch_assoc()) {
	$arr_data['division'][$effism_division_row['emp_division_id']] =$effism_division_row['cnt'];
}

 $effism_subdivision_sql = "select emp_subdivision_id,count(user_id) as cnt from tbl_users where status='Active'  group by emp_subdivision_id order by emp_subdivision_id";
$effism_subdivision_result = $mysqli->query($effism_subdivision_sql);
while($effism_subdivision_row = $effism_subdivision_result->fetch_assoc()) {
$arr_data['sub_division'][$effism_subdivision_row['emp_subdivision_id']] =$effism_subdivision_row['cnt'];
}

 $effism_emp_type_sql = "select emp_type,count(user_id) as cnt from tbl_users where status='Active'  group by emp_type order by (emp_type+0)";
 $effism_emp_type_result = $mysqli->query($effism_emp_type_sql);
while($effism_emp_type_row = $effism_emp_type_result->fetch_assoc()) {
$arr_data['emp_type'][$effism_emp_type_row['emp_type']] =$effism_emp_type_row['cnt'];
}




echo   $str1= json_encode($arr_data);
 


?>