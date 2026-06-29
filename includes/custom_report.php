<?php	                                       			
session_start();
if(!isset($_SESSION['user_id']))
header("location:index.php");
include("includes/connect.inc.php");


$time_zone =$_SESSION['time_zone'];
ini_set('date.timezone', $time_zone);

$current_month=date("n");
$current_year=date("Y");

   
$userid = $_SESSION['user_id'];

$user_result =$mysqli->query("SELECT u.work_location, a.group_access,a.emp_location_access,a.emp_division_access,a.user_access,a.user_not_access,a.emp_location_not_access FROM tbl_users u left join tbl_user_access a on  u.user_id=a.user_id  where u.status='Active' and is_regular=1 and module_access='time_sheet' and u.user_id=$userid");
  

	
$user_row= $user_result->fetch_assoc();
$group_access= $user_row['group_access'];
$emp_location_access= $user_row['emp_location_access'];
$emp_division_access= $user_row['emp_division_access'];
$user_access= $user_row['user_access'];
$user_not_access= $user_row['user_not_access'];
$emp_location_not_access= $user_row['emp_location_not_access'];
$sql_access = "SELECT GROUP_CONCAT(user_id) division_head_access FROM  tbl_users WHERE division_head='".$userid."'";


$access_query = $mysqli->query($sql_access);
$access_row = $access_query->fetch_assoc();
$user_under_division_head = $access_row['division_head_access'];
$user_location_id= $user_row['work_location'];



$select_month = isset($_REQUEST['select_month'])?$_REQUEST['select_month']:$current_month;
$select_year = isset($_REQUEST['select_year'])?$_REQUEST['select_year']:$current_year;
$emp_division_id = isset($_REQUEST['emp_division_id'])?$_REQUEST['emp_division_id']:$_SESSION['emp_division_id'];
$location_id = isset($_REQUEST['location_id'])?$_REQUEST['location_id']:$user_location_id;



$select_date = $select_year."-".$select_month."-15";
$previous_month=date('m',strtotime($select_date."-1 month"));
$previous_year=date('Y',strtotime($select_date."-1 month"));

$next_month=date('m',strtotime($select_date."+1 month"));
$next_year=date('Y',strtotime($select_date."+1 month"));



$sort_by=(isset($_REQUEST['sort_by']))?$_REQUEST['sort_by']:"name";
$sort_order=(isset($_REQUEST['sort_order']))?$_REQUEST['sort_order']:"asc";


$emp_company_id= isset($_REQUEST['emp_company_id'])?$_REQUEST['emp_company_id']:0;


$emp_subdivision_id= isset($_REQUEST['emp_subdivision_id'])?$_REQUEST['emp_subdivision_id']:0;

$group_id= isset($_REQUEST['group_id'])?$_REQUEST['group_id']:0;




$emp_div_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=2 order by short_name");
$emp_subdiv_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=3 order by short_name");
$emp_comp_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=1 order by short_name");
$group_result = $mysqli->query("SELECT * FROM  tbl_emp_group");
$location_result = $mysqli->query("SELECT * FROM  tbl_emp_workplace");

$num_days =cal_days_in_month(CAL_GREGORIAN,$select_month,$select_year);





$username = $_SESSION['user_name'];





	$log_date=date('Y-m-d H:i:s');


  $log_sql= "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,jobdiary_date) values('$userid','$log_date','Time Sheet','View Time Sheet','','')";
 
    $mysqli->query($log_sql);






$current_day= date("d");



$date_from ="2009-$current_month-01";

$date_to   ="2009-$current_month-$current_day";






$username = $_SESSION['user_name'];
$userid = $_SESSION['user_id'];







$query ="SELECT username,user_id FROM tbl_users where status='Active' and is_regular=1 and  (user_id=$userid ";
if($group_access!="")
$query.=" or group_id in  ($group_access)  ";
if($emp_location_access!="")
$query.=" or work_location in  ($emp_location_access)  ";
if($emp_division_access!="")
$query.=" or emp_division_id in  ($emp_division_access)  ";
if($user_access!="")
$query.=" or user_id in  ($user_access)  ";
if($user_under_division_head!="")
$query.=" or user_id in  ($user_under_division_head)  ";
$query.=") ";
if($user_not_access!="")
$query.=" and( user_id not in  ($user_not_access) ) ";
if($emp_location_not_access!="")
$query.=" and( work_location not in  ($emp_location_not_access) ) ";
$query.=" order by username";
$result =$mysqli->query($query);
echo $query;
$user_array=array();
while($user_row=$result->fetch_assoc())
{
	$user_array[$user_row['user_id']]=$user_row['user_id'];
}

$user_in= implode(",",($user_array));


	

$sql1 = "SELECT sum(if(time_to_sec(addtime(addtime((addtime(addtime(TIMEDIFF(TIMEDIFF(time_out ,time_in),nwt),outside),night)),home),leave_hours))>=21600,1,0)) as effective_days,  SEC_TO_TIME(SUM( TIME_TO_SEC(home))) AS home,SEC_TO_TIME(SUM( TIME_TO_SEC(night))) AS night,SEC_TO_TIME(SUM( TIME_TO_SEC(leave_hours))) AS leave_hours,SUM( TIME_TO_SEC( TIMEDIFF(TIMEDIFF( time_out, time_in ),nwt) ) ) AS workhours1, tbl_users.full_name,d.short_name as emp_div_name,d1.short_name as emp_subdiv_name,tbl_users.user_id, SEC_TO_TIME(SUM(TIME_TO_SEC(TIMEDIFF(TIMEDIFF(time_out ,time_in),nwt)))) as workhours,
SEC_TO_TIME(SUM(TIME_TO_SEC(total_job))) as total_job,

sec_to_time(
sum(
time_to_sec(
addtime(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home),leave_hours)

)

)
)



 as net_total,
 
 

sum(
time_to_sec(
addtime(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home),leave_hours)

)

)



 as net_sec,
 
 SUM(TIME_TO_SEC(total_job)) as eff_sec,

 
sec_to_time(
sum(
time_to_sec(
timediff(addtime(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home),leave_hours),total_job)

)

)
)



 as difference 
 

 
 
 
  FROM     tbl_time t  left join tbl_users ON tbl_users.user_id=t.user_id  left join tbl_dimensions  d on d.id=tbl_users.emp_division_id left join tbl_dimensions  d1 on d1.id=tbl_users.emp_subdivision_id  where  (month(date_log)='$select_month') and (year(date_log)='$select_year') and  t.is_complete=1 and  tbl_users.status='Active' AND (tbl_users.user_id in($user_in)) ";

if($emp_company_id>0)
$sql1.=" and tbl_users.emp_company_id=$emp_company_id ";
if($emp_division_id>0)
$sql1.=" and tbl_users.emp_division_id=$emp_division_id ";
if($emp_subdivision_id>0)
$sql1.=" and tbl_users.emp_subdivision_id=$emp_subdivision_id ";
if($group_id>0)
$sql1.=" and tbl_users.group_id=$group_id ";
if($location_id>0)
$sql1.=" and tbl_users.work_location=$location_id ";



$sql1.=" group by tbl_users.user_id  ";

if($sort_by=="name")
$sql1.=" order by tbl_users.short_name $sort_order";
elseif($sort_by=="working")
$sql1.=" order by net_total $sort_order";
else if($sort_by=="eff_time")
$sql1.=" order by total_job $sort_order ";
else if($sort_by="difference")
$sql1.="order by difference $sort_order";





$time_sheet_result=$mysqli->query($sql1);



 function secondsToTime($seconds)
    {
        // extract hours
        $hours = floor($seconds / (60 * 60));
     
        // extract minutes
        $divisor_for_minutes = $seconds % (60 * 60);
        $minutes = floor($divisor_for_minutes / 60);
     
        // extract the remaining seconds
        $divisor_for_seconds = $divisor_for_minutes % 60;
        $seconds = ceil($divisor_for_seconds);
     
        // return the final array
        $obj = array(
            "h" => (int) $hours,
            "m" => (int) $minutes,
            "s" => (int) $seconds,
        );
        return $obj;
    }












?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<link rel="icon" href="images/favicon.ico" type="image/x-icon">

<title>Online Job Diary - Aries Marine</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/style.css" rel="stylesheet" type="text/css">

<link href="css/calendarstyle.css" rel="stylesheet" type="text/css">

 

<script type="text/javascript" src="javascript/calendar.js"></script>

<script type="text/javascript" src="javascript/js.js"></script>

<script type="text/javascript" src="javascript/date-functions.js"></script>



</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">

<?php	                                       			

include("includes/header.php");

?>

<?php	                                       			

	

$num = 0;

?>

<tr>

	<td>&nbsp;

	

		<table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">

		<tr><td colspan="8">

		

		<table width="95%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">

		

		<form name="frm" method="post">

		<tr>

			<td align="center" colspan="12">&nbsp;</td>

		</tr>

		<tr>
		  
		  <td colspan="12">
          <table width="90%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td colspan="3" align="center" width="8%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="5">&nbsp;Time Sheet Auditing</font></b></td>
			    <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
			    </tr>
			  <?php	                                       			

$count_row=1;
		


			?>
			  <tr height="20" style="border:#a6107b solid 1px;"><td align="center" width="10%">
<a href="custom_report.php?select_month=<?php echo $previous_month?>&select_year=<?php echo $previous_year?>&sort_by=<?php echo $sort_by?>&sort_order=<?php echo $sort_order?>&emp_company_id=<?php echo $emp_company_id?>&emp_division_id=<?php echo $emp_division_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>&emp_subdivision_id=<?php	echo $emp_subdivision_id?>"><img src="images/previous.png" border="0" ></a></td>  
			    <td align="center" style="border:#000000 solid 1px; padding-left:0px;" width="100%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>
                
        <table width="100%">
        <tr>
       <td height="30px" align="center" style="border:#000000 solid 1px;" width="100%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>    
                <strong>Year:</strong>
<select name="select_year" id="select_year" style="width: 150px;">
<option value="2013" <?php if($select_year=="2013") { ?> selected  <?php }?>>2013</option>
<option value="2014" <?php if($select_year=="2014") { ?> selected  <?php }?>>2014</option>
<option value="2015" <?php if($select_year=="2015") { ?> selected  <?php }?>>2015</option>

</select>

<strong>Month:</strong>
<select name="select_month" id="select_month" style="width: 150px;">

<?php	                                       			 

					for($i=1;$i<=12;$i++)

					{

					$month=date("F",strtotime("2009-$i-1"));
					$month_num=date("m",strtotime("2009-$i-1"));


					?>					

					<option <?php	                                       			 if($select_month==$i){?> selected="selected" <?php	                                       			 }?> value="<?php	                                       			 echo $month_num?>"><?php	                                       			 echo $month?></option>

					<?php	                                       			

					}

					?>

					


</select>


                
                
                &nbsp;&nbsp;&nbsp;&nbsp;<strong>Sort By:</strong>
                <select name="sort_by"><option value="name" <?php if($sort_by=="name"){?> selected="true"  <?php }?>>Name</option><option value="working" <?php if($sort_by=="working"){?> selected="true"  <?php }?>>Working Hours</option><option value="eff_time" <?php if($sort_by=="eff_time"){?> selected="true"  <?php }?>>Eff Hours</option><option value="difference" <?php if($sort_by=="difference"){?> selected="true"  <?php }?>>Difference</option></select>
	      &nbsp;	      &nbsp;&nbsp;<strong>Sort Order:</strong>
<select name="sort_order">
<option value="asc" <?php if($sort_order=="asc"){?> selected="true"  <?php }?>>Asc</option>
<option value="desc" <?php if($sort_order=="desc"){?> selected="true" <?php }?> >Desc</option></select>

	      <input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image">
          </td></tr>
          <tr>
          
          <td  height="30px"  align="center" style="border:#000000 solid 1px;" width="100%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>
      <strong>Company:&nbsp;</strong>
      <select name="emp_company_id" class="txt_box">
				<option value="0">Select Company</option>
			<?php	                                       			
			
				while($row =$emp_comp_result->fetch_assoc())
				{
				?>
				<option value="<?php	                                       			 echo $row['id']?>" <?php	                                       	if($emp_company_id==$row['id']){?> selected="selected" <?php	                                       			 }?> ><?php	                                       			 echo $row['short_name']?></option>
				<?php	                                       			
				}
				
				?>
				</select>&nbsp;
<strong>Division:</strong>
 <select name="emp_division_id" class="txt_box">
				<option value="0">All Divisions</option>
			<?php	                                       			
			
				while($row =$emp_div_result->fetch_assoc())
				{
				?>
				<option value="<?php	                                       			 echo $row['id']?>" <?php	                                       			 if($emp_division_id==$row['id']){?> selected="selected" <?php	                                       			 }?> ><?php	                                       			 echo $row['short_name']?></option>
				<?php	                                       			
				}
				
				?>
				</select>&nbsp;
                
                
<strong>Sub Division:</strong>
 <select name="emp_subdivision_id" class="txt_box">
				<option value="0">All Sub Divisions</option>
			<?php	                                       			
			
				while($row =$emp_subdiv_result->fetch_assoc())
				{
				?>
				<option value="<?php	                                       			 echo $row['id']?>" <?php	                                       			 if($emp_subdivision_id==$row['id']){?> selected="selected" <?php	                                       			 }?> ><?php	                                       			 echo $row['short_name']?></option>
				<?php	                                       			
				}
				
				?>
				</select>&nbsp;
                
<strong>Group:&nbsp;</strong>
  <select name="group_id" class="txt_box">
				<option value="0">Select Group</option>
			<?php	                                       			
			
				while($row =$group_result->fetch_assoc())
				{
				?>
				<option value="<?php	                                       			 echo $row['emp_group_id']?>" <?php	                                       			 if($group_id==$row['emp_group_id']){?> selected="selected" <?php	                                       			 }?> ><?php	                                       			 echo $row['emp_group_name']?></option>
				<?php	                                       			
				}
				
				?>
				</select>&nbsp;
<strong>Location:&nbsp;</strong>
 <select name="location_id" class="txt_box">
				<option value="0">All Locations</option>
			<?php	                                       			
			
				while($row =$location_result->fetch_assoc())
				{
				?>
				<option value="<?php	                                       			 echo $row['id']?>" <?php	                                       			 if($location_id==$row['id']){?> selected="selected" <?php	                                       			 }?> ><?php	                                       			 echo $row['work_place']?></option>
				<?php	                                       			
				}
				
				?>
				</select>
				
          </td></tr></table>
          </td>
		<td align="center"width="10%"><a href="custom_report.php?select_month=<?php echo $next_month?>&select_year=<?php echo $next_year?>&sort_by=<?php echo $sort_by?>&sort_order=<?php echo $sort_order?>&emp_company_id=<?php echo $emp_company_id?>&emp_division_id=<?php echo $emp_division_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>&emp_subdivision_id=<?php	echo $emp_subdivision_id?>"><img src="images/next.png" border="0"></a>
                <br>
                </td>
			    <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
			    </tr>       	

			

			

			  

		

			 
			  <tr>
	    <td align="right" colspan="3"><a href="javascript:document.location.href='time_sheet.php?select_year=<?php echo $select_year?>&select_month=<?php	echo $select_month?>
&emp_company_id=<?php	echo $emp_company_id?>
&emp_division_id=<?php	echo $emp_division_id?>&emp_subdivision_id=<?php	echo $emp_subdivision_id?>&location_id=<?php	                                       			 echo $location_id?>&group_id=<?php	                                       			 echo $group_id?>'" style="font-weight:bold">Monthly Time sheet<img border="0" src="images/page_excel.png"></a></td>
			    </tr>
		    </table>
          
          
          
          
          </td>
		  
		  </tr>

		<tr>
		  
		  <td colspan="9">&nbsp;</td>
		  
		  </tr>

		</form>

		<tr>

			<td colspan="12" align="center"></td>

		</tr>

		<tr>

			<td colspan="12"></td>

		</tr>

				<tr height="30" style="border:#a6107b solid 1px;">

			<td width="5%" style="border:#000000 solid 1px;" align="center" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;No:</font></b></td>

			<td width="25%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Division</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">SubDivision</font></b></td>
			
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Working Hours</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work At Home</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Late Night</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Leave Hrs</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Total</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Days</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Download</font></b></td>

			<!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->

			</tr>

		<?php	                                       			

						$num=0;

		//	$result4 =mysql_fetch_assoc($mysql4);

		$grand_total=0;
		$grand_eff=0;
			$count_row=1;

			while($result = $time_sheet_result->fetch_assoc()) { 

			

			

			/*echo "<pre>";

			

				

			

			print_r($result);

			//print_r($result4);

		echo "</pre>";

*/		  		 

			 

			  			   

			   $workhours= (($result['workhours']!="")  && ($result['workhours']!="00:00:00"))?$result['workhours']:"<span style='color:#FF0000'>00:00:00</span>";

		$home= $result['home'];	  
   	  $effective_days= $result['effective_days'];
      $night= $result['night'];
	  $leave_hours= $result['leave_hours'];
	  

				 $net_total= $result['net_total'];
				 $total_job= $result['total_job'];
				 $difference= $result['difference'];
				 				 $net_sec= $result['net_sec'];

				 				 $eff_sec= $result['eff_sec'];

				


			

				

				//echo $result4['user_id'],$q['user_id']."<br>";

				


			  

					

			?>

			

			<tr height="20" style="border:#a6107b solid 1px;">

	

				<td width="5%" style="border:#000000 solid 1px;" align="center" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php	                                       			 echo $num=$num+1;?></b></font></td>

				<td style="border:#000000 solid 1px; padding-left:5px;" width="25%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php	                                       			 echo $result['full_name']; ?></font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php	                                       			 echo $result['emp_div_name']; ?></font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['emp_subdiv_name']; ?>
				</font></td>
				
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php	                                       			  echo $workhours ?></font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $home ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $night ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $leave_hours ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	 
			
$grand_total=$grand_total+$net_sec;
$grand_eff=$grand_eff+$eff_sec;

				  
				                                        			  echo $net_total ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $effective_days ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a title="Download" href="individual_time_sheet.php?user_id=<?php	                                       			 echo $result['user_id']?>&select_month=<?php	                                       			 echo $select_month?>&select_year=<?php	                                       			 echo $select_year?>"><img border="0" src="images/pdf-icon1.jpg"></a>&nbsp;</td>

				

			</tr>

		<?php	                                       			 $count_row++;

			

			        			

				

				

				



			

			

			  

		

		

		 } ?>

<tr height="20" style="border:#a6107b solid 1px;">

	

				<td colspan="7" align="right"  style="border:#000000 solid 1px;"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;Average Working Hours </b></font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px; font-weight:bold" width="139"   >&nbsp;</td>

				<td align="center" style="border:#000000 solid 1px; padding-left:5px; font-weight:bold" width="139"   ><font face="Verdana, Arial, Helvetica, sans-serif"><?php	
				$average_total =round($grand_total/$num);
			$average_total_array=(secondsToTime($average_total));
				
				$average_eff =round($grand_eff/$num);
			$average_eff_array=(secondsToTime($average_eff));
				                                       				echo $average_total_array['h'].":".$average_total_array['m'].":".$average_total_array['s'];?></font></td>
				<td style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" align="center" ></td>
				<td style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" align="center" ></td>
			

				

			  <!--<td style="border:#000000 solid 1px;" width="4%" ><a href="individual_download.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $date_from?>&date_to=<?php	                                       			 echo $date_to?>"></a></td>-->

			</tr>

	



		<tr>

			<td colspan="12">&nbsp;</td>

		</tr>

		</table>

	

	</td>

</tr>



<?php	                                       			 include("includes/footer.php");?>

</table>

<?php	                                       			







//recursive function that prints categories as a nested html unorderd list



function generate_menu($parent)



{





global $user_row;

        $has_childs = false;



        //this prevents printing 'ul' if we don't have subcategories for this category







        global $menu_array;



        //use global array variable instead of a local variable to lower stack memory requierment







        foreach($menu_array as $key => $value)



        {



                if ($value['parent'] == $parent) 



                {       



                        //if this is the first child print '<ul>'                       



                        if ($has_childs === false)



                        {



                                //don't print '<ul>' multiple times                             



                                $has_childs = true;



                              //  echo '<ul>';



                        }



                        $user_row[$key]=$key;

						

						



                        generate_menu($key);



                        //call function again to generate nested list for subcategories belonging to this category



                        ///echo '</li>';



                }



        }



        if ($has_childs === true) echo '';



}





    
     
    /**
     * Convert number of seconds into hours, minutes and seconds
     * and return an array containing those values
     *
     * @param integer $seconds Number of seconds to parse
     * @return array
     */
   








?>





</body>

</html>

