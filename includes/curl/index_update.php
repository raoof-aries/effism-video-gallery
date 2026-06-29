<?php 
ini_set('display_errors', '1');
//mysql_connect('localhost',root,'');
$con =new mysqli("p:"."localhost", "effism_eff", "EEV4_ADV*{fF", "effism_efficiency");
//mysql_select_db('ames');
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
echo "t";

$ssc=  $_REQUEST['ssc'];
$plustwo=$_REQUEST['plustwo'];
$diploma=$_REQUEST['diploma'];
$ug=$_REQUEST['ug'];
$pg=$_REQUEST['pg'];
$othrs=$_REQUEST['othrs'];
$total=$_REQUEST['total'];
$computer=$_REQUEST['computer'];
$relnt=$_REQUEST['relnt'];
$id=$_REQUEST['id'];

$emp_sql = "select user_id from tbl_users where hr_id='$id'"; 
$emp_result = mysqli_query($con, $emp_sql);
$emp_row = mysqli_fetch_assoc($emp_result);
$user_id = $emp_row['user_id'];

$index_sql = "select index_id from tbl_user_index where user_id='$user_id'"; 
$index_result = mysqli_query($con, $index_sql);
$index_row = mysqli_fetch_assoc($index_result);
$index_id = $index_row['index_id'];


if($index_id>0)
{
$index_query = "update  tbl_user_index
 SET  ssc='$ssc',plustwo='$plustwo',diploma='$diploma',ug='$ug',pg='$pg',computer='$computer',othrs='$othrs',total='$total',relnt='$relnt' WHERE user_id ='$user_id'";
echo $index_query;
mysqli_query($con, $index_query);
}
else
{
$index_query = "insert into   tbl_user_index(user_id,ssc,plustwo,diploma,ug,pg,computer,othrs,total,relnt) values('$user_id','$ssc','$plustwo','$diploma','$ug','$pg','$computer','$othrs','$total','$relnt')";
mysqli_query($con, $index_query);
	
}
?>			