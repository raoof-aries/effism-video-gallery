<?php 
ini_set('display_errors', '1');
//mysql_connect('localhost',root,'');
$con =new mysqli("p:"."localhost", "effism_eff", "EEV4_ADV*{fF", "effism_efficiency");
//mysql_select_db('ames');
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
extract($_REQUEST);

$emp_sql = "select user_id from tbl_users where hr_id='$employee_id'"; 
$emp_result = mysqli_query($con, $emp_sql);
$emp_row = mysqli_fetch_assoc($emp_result);
$user_id = $emp_row['user_id'];

print $index_sql = "select parent_id from tbl_emp_parent where user_id='$user_id'"; 
$index_result = mysqli_query($con, $index_sql);
$index_row = mysqli_fetch_assoc($index_result);
$index_id = $index_row['parent_id'];


if($index_id>0)
{
	$index_query = "update  tbl_emp_parent
	 SET  fname='$fname',mname='$mname',madd1='$madd1',
	 fadd1='$fadd1',fadd2='$fadd2',madd2='$madd2',
	 fadd3='$fadd3',madd3='$madd3',fadd4='$fadd4',
	 madd4='$madd4', ftel='$ftel',
	 mtel='$mtel',ffax='$ffax',mfax='$mfax',
	femail ='$femail',memail='$memail', facc= '$facc',
	macc='$macc',fat='$fat',mat='$mat',
	fbn='$fbn', mbn='$mbn',fbcode='$fbcode',mbcode = '$mbcode',fscode='$fscode',
	mscode = '$mscode',fbr='$fbr',mbr='$mbr',fiban='$fiban',miban='$miban',
	fcountry='$fcountry',mcountry='$mcountry',factive='$factive',mactive='$mactive',";
	if (empty($factive)) {
        $index_query .= " f_deactive_dt= IF(f_deactive_dt='0000-00-00','" . gmdate('Y-m-d') . "',f_deactive_dt),";
    }
    if (empty($mactive)) {
        $index_query .= " m_deactive_dt= IF(m_deactive_dt='0000-00-00','" . gmdate('Y-m-d') . "',m_deactive_dt) ,";
    }

	$index_query .= "sib1='$sib1',sib2='$sib2',fremark='$fremark',mremark='$mremark',loc='$loc',
	date='$date',focc='$focc',mocc='$mocc',fdob='$fdob',mdob='$mdob'
	 WHERE user_id ='$user_id'";
	echo $index_query;
	mysqli_query($con, $index_query);
}
else
{
	print $index_query = "insert into   tbl_emp_parent(user_id,
	parent_emp_id,
	fname,
	mname,
	madd1,
	fadd1,
	fadd2,
	madd2,
	fadd3,
	madd3,
	fadd4,
	madd4,
	ftel,
	mtel,
	ffax,
	mfax,
	femail,
	memail,
	facc,
	macc,
	fat,
	mat,
	fbn,
	mbn,
	fbcode,
	mbcode,
	fscode,
	mscode,
	fbr,
	mbr,
	fiban,
	miban,
	fcountry,
	mcountry,
	factive
	,mactive,
	sib1,
	sib2,
	fremark,
	mremark,
	loc,
	date,
	focc,
	mocc,
	fdob,
	mdob) values('$user_id',
	'$employee_id',
	'$fname',
	'$mname',
	'$madd1',
	'$fadd1',
	'$fadd2',
	'$madd2',
	'$fadd3',
	'$madd3',
	'$fadd4',
	'$madd4',
	'$ftel',
	'$mtel',
	'$ffax',
	'$mfax',
	'$femail',
	'$memail',
	'$facc',
	'$macc',
	'$fat',
	'$mat',
	'$fbn',
	'$mbn',
	'$fbcode',
	'$mbcode',
	'$fscode', 
	'$mscode',
	'$fbr',
	'$mbr',
	'$fiban',
	'$miban',
	'$fcountry',
	'$mcountry',
	'$factive',
	'$mactive',
	'$sib1',
	'$sib2',
	'$fremark'
	,'$mremark',
	'$loc',
	'$date',
	'$focc',
	'$mocc',
	'$fdob',
	'$mdob')";
	mysqli_query($con, $index_query);
	
}
?>			