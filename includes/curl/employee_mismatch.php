<?php

include("../connect.inc.php");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);


$sql_count = "select count(user_id) as emp_count from tbl_users where status='Active'";
$result_count = $mysqli->query($sql_count);
while ($row_count = $result_count->fetch_assoc()) {
		$eff_cnt =$row_count['emp_count'];
}
 echo $eff_cnt.'->';
$type = $_REQUEST['type']; 	
if($type=='1')
{
	$emp_array = array();
	$sql_name = "select hr_id,full_name,short_name from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		    if($row_name['full_name']=='')
			{
				$row_name['full_name']=$row_name['short_name'];
			}
			echo "=".$row_name['hr_id'].'&&'.$row_name['full_name'];
	}
}
elseif($type=='2')
{
	$emp_array = array();
	$sql_name = "select hr_id,employee_code from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {

			echo "=".$row_name['hr_id'].'&&'.$row_name['employee_code'];
	}
}
elseif($type=='3')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,d.short_name,d.hr_dimension_id from tbl_users u left join tbl_dimensions d on d.id=u.emp_company_id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['short_name'].'##'.$row_name['hr_dimension_id'];
	}
}
elseif($type=='4')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,d.short_name,d.hr_dimension_id from tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['short_name'].'##'.$row_name['hr_dimension_id'];
	}
}
elseif($type=='5')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,d.short_name,d.hr_dimension_id from tbl_users u left join tbl_dimensions d on d.id=u.emp_subdivision_id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['short_name'].'##'.$row_name['hr_dimension_id'];
	}
}
elseif($type=='6')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,w.work_place,w.hr_id as hrId from tbl_users u left join tbl_emp_workplace w on w.id=u.work_location where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['work_place'].'##'.$row_name['hrId'];
	}
}
elseif($type=='7')
{
	$emp_array = array();
	$sql_name = "select hr_id,reporting_time from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['reporting_time'];
	}
}
elseif($type=='8')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,t.name,t.hr_id as hrId from tbl_users u left join tbl_emp_type t on u.emp_type=t.id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['name'].'##'.$row_name['hrId'];
	}
}
elseif($type=='9')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,t.work_category_name,t.hr_id as hrId from tbl_users u left join tbl_work_category t on u.work_category=t.id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['work_category_name'].'##'.$row_name['hrId'];
	}
}
elseif($type=='10')
{
	$emp_array = array();
	$sql_name = "select hr_id,personal_email from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['personal_email'];
	}
}
elseif($type=='11')
{
	$emp_array = array();
	$sql_name = "select hr_id,if(is_regular=1,'Yes','No') as effism from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['effism'];
	}
}
elseif($type=='12')
{
	$emp_array = array();
	$sql_name = "select u.hr_id,if(u1.full_name='',u1.short_name,u1.full_name) as HeadName,u1.hr_id as hrId from tbl_users u left join tbl_users u1 on u.parent_id =u1.user_id where u.status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['HeadName'].'##'.$row_name['hrId'];
	}
}
elseif($type=='13')
{
	$emp_array = array();
	$sql_name = "select hr_id,designation from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['designation'];
	}
}
elseif($type=='13')
{
	$emp_array = array();
	$sql_name = "select hr_id,blood_group from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['blood_group'];
	}
}
elseif($type=='14')
{
	$emp_array = array();
	$sql_name = "select hr_id,blood_group from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['blood_group'];
	}
}
elseif($type=='15')
{
	$emp_array = array();
	$sql_name = "select hr_id,user_height from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['user_height'];
	}
}
elseif($type=='16')
{
	$emp_array = array();
	$sql_name = "select hr_id,admin_category from tbl_users where status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['admin_category'];
	}
}
elseif($type=='17')
{
	$emp_array = array();
	$sql_name = "SELECT hr_id, gender
FROM tbl_users WHERE  status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['gender'];
	}
}
elseif($type=='18')
{
	$emp_array = array();
	$sql_name = "SELECT hr_id, IF(marital_status=1,'Married','Single') married
FROM tbl_users WHERE is_erp=1 AND status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['married'];
	}
}
elseif($type=='19')
{
	$emp_array = array();
	$sql_name = "SELECT hr_id, marine_category 
FROM tbl_users WHERE is_erp=1 AND status='Active'";
	$result_name = $mysqli->query($sql_name);
	while ($row_name = $result_name->fetch_assoc()) {
		
			echo "=".$row_name['hr_id'].'&&'.$row_name['marine_category'];
	}
}

else 
{
	echo "No";
}

?>