<?php 
$con =new mysqli("p:"."localhost", "effism_eff", "{ebx+MnNkPG0", "effismuser_live");
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

 $hr_id =$_REQUEST['hr_id'];
 $full_name=$_REQUEST['full_name'];
 $employee_code=$_REQUEST['employee_code'];
 $designation= $_REQUEST['designation'];
 $gender= $_REQUEST['gender'];
 $dob= $_REQUEST['dob'];
 $doj= $_REQUEST['doj'];
 $gdoj= $_REQUEST['gdoj'];
 $personal_email= $_REQUEST['personal_email'];
 $mobile= $_REQUEST['mobile'];
 $emp_company_id= $_REQUEST['emp_company_id'];
 $emp_division_id= $_REQUEST['emp_division_id']; 	
 $emp_subdivision_id= $_REQUEST['emp_subdivision_id'];
 $group_id = $_REQUEST['group_id'];	
 $emp_type 	= $_REQUEST['emp_type'];
 $emp_category= $_REQUEST['emp_category'];
 $reporting_location= $_REQUEST['reporting_location'];
 $work_location= $_REQUEST['work_location'];
 $user_photo= $_REQUEST['user_photo'];
 $user_height= $_REQUEST['user_height'];
 $blood_group= $_REQUEST['blood_group'];
 
if($hr_id>0)
{

	 $user_sql = "select * from tbl_users where hr_id='$hr_id' and employee_code='$employee_code'";
	 $user_result = mysqli_query($con, $user_sql);
	 $user_row = mysqli_fetch_assoc($user_result);
	 $user_id=$user_row['user_id'];

if($user_id>0)
	{
	$sql = "update tbl_users set 
	full_name='$full_name',
	designation='$designation',
	gender='$gender',
	dob='$dob',
	doj='$doj',
	gdoj='$gdoj',
	personal_email='$personal_email',
	mobile='$mobile',
	emp_company_id='$emp_company_id',
	emp_division_id='$emp_division_id',
	emp_subdivision_id='$emp_subdivision_id',
	emp_type='$emp_type',
	emp_category='$emp_category',
	reporting_location='$reporting_location',
	work_location='$work_location',
	user_photo='$user_photo',
	user_height='$user_height',
	blood_group='$blood_group' where hr_id='$hr_id' and employee_code='$employee_code'"; 
	}
	else
	{

		$sql = "insert into tbl_users(hr_id,username,full_name,	employee_code,designation,gender,dob,doj,gdoj,personal_email,mobile,category,emp_company_id,emp_division_id, 	emp_subdivision_id,group_id,emp_type,emp_category,reporting_location,work_location,status,is_regular,user_photo,date_created,user_height,blood_group,is_health, 	is_erp) values ('$hr_id','$hr_id','$full_name','$employee_code','$designation','$gender','$dob','$doj','$gdoj','$personal_email','$mobile','user','$emp_company_id','$emp_division_id', '$emp_subdivision_id','$group_id','$emp_type','$emp_category','$reporting_location','$work_location','Active',0,'$user_photo','$date_created','$user_height','$blood_group',0,1 )";

	}





$result = mysqli_query($con,$sql);

}
?>