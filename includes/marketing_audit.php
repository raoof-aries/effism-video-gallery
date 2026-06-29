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

$user_result =$mysqli->query("SELECT u.work_location, a.group_access,a.emp_location_access,a.emp_division_access,a.user_access,a.user_not_access,a.emp_location_not_access FROM tbl_users u left join tbl_user_access a on  u.user_id=a.user_id  where status='Active' and is_regular=1 and module_access='main_module' and u.user_id=$userid");
  

	
$user_row= $user_result->fetch_assoc();
$group_access= $user_row['group_access'];
$emp_location_access= $user_row['emp_location_access'];
$emp_division_access= $user_row['emp_division_access'];
$user_access= $user_row['user_access'];
$user_not_access= $user_row['user_not_access'];
$emp_location_not_access= $user_row['emp_location_not_access'];
$user_location_id= $user_row['work_location'];
$sql_access = "SELECT GROUP_CONCAT(user_id) division_head_access FROM  tbl_users WHERE division_head='".$userid."'";


$access_query = $mysqli->query($sql_access);
$access_row = $access_query->fetch_assoc();
$user_under_division_head = $access_row['division_head_access'];




$select_month = isset($_REQUEST['select_month'])?$_REQUEST['select_month']:$current_month;
$select_year = isset($_REQUEST['select_year'])?$_REQUEST['select_year']:$current_year;
$emp_division_id = $_REQUEST['emp_division_id'];
$location_id = $_REQUEST['location_id'];



$select_date = $select_year."-".$select_month."-15";
$previous_month=date('m',strtotime($select_date."-1 month"));
$previous_year=date('Y',strtotime($select_date."-1 month"));

$next_month=date('m',strtotime($select_date."+1 month"));
$next_year=date('Y',strtotime($select_date."+1 month"));






$emp_company_id=$_REQUEST['emp_company_id'];
$emp_subdivision_id=$_REQUEST['emp_subdivision_id'];

$group_id=$_REQUEST['group_id'];


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

$user_array=array();
while($user_row=$result->fetch_assoc())
{
	$user_array[$user_row['user_id']]=$user_row['user_id'];
}

$user_in= implode(",",($user_array));


	

$sql1 = "SELECT  u.user_id,sum(m.no_of_enquiry) as no_of_enquiry,sum(enquiry_amount) as enquiry_amount, 	sum(no_of_jobs) as no_of_jobs,sum(job_amount) as job_amount,sum(no_of_clients) as no_of_clients, 	sum(big_companies) as big_companies,sum(no_of_persons) as no_of_persons, sum(new_client) as new_client,sum(no_of_lost_client) as no_of_lost_client,sum(existing_client) as existing_client, 	sum(reactivate_client) as reactivate_client,sum(client_lost) as client_lost,u.full_name,d.full_name as division_name from tbl_marketing_coefficient m
      LEFT JOIN tbl_users u ON u.user_id=m.user_id left join tbl_dimensions  d on d.id=u.emp_division_id left join tbl_dimensions  d1 on d1.id=u.emp_subdivision_id  where  	  u.status='Active' AND (u.user_id in($user_in)) and (month(date_log)='$select_month') and (year(date_log)='$select_year') ";

if($emp_company_id>0)
$sql1.=" and u.emp_company_id=$emp_company_id ";
if($emp_division_id>0)
$sql1.=" and u.emp_division_id=$emp_division_id ";
if($emp_subdivision_id>0)
$sql1.=" and u.emp_subdivision_id=$emp_subdivision_id ";
if($group_id>0)
$sql1.=" and u.group_id=$group_id ";
if($location_id>0)
$sql1.=" and u.work_location=$location_id ";



$sql1.=" group by u.user_id  ";

$sql1.=" order by u.short_name $sort_order";

echo $sql1;

$mysql1=$mysqli->query($sql1);




	







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

			<td align="center" colspan="17">&nbsp;</td>

		</tr>

		<tr>
		  
		  <td colspan="17">
          <table width="90%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td colspan="3" align="center" width="8%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="5">&nbsp;Marketing Audit</font></b></td>
			    <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
			    </tr>
			  <?php	                                       			

$count_row=1;
		


			?>
			  <tr height="20" style="border:#a6107b solid 1px;"><td align="center" width="10%">
<a href="marketing_audit.php?select_month=<?php echo $previous_month?>&select_year=<?php echo $previous_year?>&sort_by=<?php echo $sort_by?>&sort_order=<?php echo $sort_order?>&company_id=<?php echo $company_id?>&division_id=<?php echo $division_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>"><img src="images/previous.png" border="0" ></a></td>  
			    <td align="center" style="border:#000000 solid 1px; padding-left:0px;" width="100%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>
                
        <table width="100%">
        <tr>
       <td height="30px" align="center" style="border:#000000 solid 1px;" width="100%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>    
                <strong>Year:</strong>
<select name="select_year" id="select_year" style="width: 150px;">
<option value="2015" <?php if($select_year=="2015") { ?> selected  <?php }?>>2015</option>
<option value="2014" <?php if($select_year=="2014") { ?> selected  <?php }?>>2014</option>
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


                
                
                &nbsp;&nbsp;&nbsp;&nbsp;
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
				</select>
 &nbsp;
                
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
		<td align="center"width="10%"><a href="marketing_audit.php?select_month=<?php echo $next_month?>&select_year=<?php echo $next_year?>&sort_by=<?php echo $sort_by?>&sort_order=<?php echo $sort_order?>&company_id=<?php echo $company_id?>&division_id=<?php echo $division_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>"><img src="images/next.png" border="0"></a>
                <br>
                </td>
			    <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
			    </tr>       	

			

			

			  

		

			 
			  <tr>
	    <td align="right" colspan="3"></td>
			    </tr>
		    </table>
          
          
          
          
          </td>
		  
		  </tr>

		<tr>
		  
		  <td colspan="16">&nbsp;</td>
		  
		  </tr>

		</form>

		<tr>

			<td colspan="17" align="center"></td>

		</tr>

		<tr>

			<td colspan="17"></td>

		</tr>

				<tr height="30" style="border:#a6107b solid 1px;">

			<td width="5%" style="border:#000000 solid 1px;" align="center" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;No:</font></b></td>

			<td width="25%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Division</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">View</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of Enquires</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Enquiry Amount </font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of jobs </font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Job amount 
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of clients 
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of Big Companies 
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of Persons Met
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">
            No of New Client 
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">no of lost client 
</font></b></td>
			
			<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">No of &nbsp;existing client 
</font></b></td>
			<td align="center" width="10%" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;reactivate client 
</font></b></td>
			<td align="center" width="139" style="border:#000000 solid 1px;border-right: thin solid  #000000;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;client lost</font></b></td>
			<!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->

			</tr>

		<?php	                                       			

						$num=0;

		//	$result4 =mysql_fetch_assoc($mysql4);

		$grand_total=0;
		$grand_eff=0;
			$count_row=1;
			echo "test";
			while($result = $mysql1->fetch_assoc()) { 

			

			

			/*echo "<pre>";

			

				

			

			print_r($result);

			//print_r($result4);

		echo "</pre>";

*/		  		 

			 

			  			   

			   $workhours= (($result['workhours']!="")  && ($result['workhours']!="00:00:00"))?$result['workhours']:"<span style='color:#FF0000'>00:00:00</span>";

			  
   	  $home= $result['home'];
      $night= $result['night'];

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
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php	                                       			 echo $result['division_name']; ?></font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a target='_blank'   href='view_marketing_status.php?user_id=<?php  echo $result['user_id']?>&select_year=<?php echo $select_year?>&select_month=<?php echo $select_month?>' onClick="javascript:openWindow(this.href,this.target); return false;"><img src='images/view.gif' width='12' height='12' border='0' title='Preview this Invoice' />
</a></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['no_of_enquiry']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['enquiry_amount']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['no_of_jobs']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['job_amount']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['no_of_clients']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['big_companies']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['no_of_persons']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['new_client']; ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			 echo $result['no_of_lost_client']; ?>
				</font></td>
				
				<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php	                                       			  echo $result['existing_client'] ?></font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $result['reactivate_client'] ?>
				</font></td>
				<td align="center" style="border:#000000 solid 1px;border-right: thin solid  #000000;" width="139" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
				  <?php	                                       			  echo $result['client_lost'] ?>
				</font></td>
			</tr>

		<?php	                                       			 $count_row++;

			

			        			

				

				

				



			

			

			  

		

		

		 } ?>

<tr>
  
  <td colspan="17">&nbsp;</td>
  
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
<script language="javascript">
function openWindow(url, title)
{
 var left = (screen.width - 900) / 2;
 var top = (screen.height - 500) / 2;
 return window.open(url, title, 'width=1024,height=500,left='+left+',top='+top+',screenX='+left+',screenY='+top+',status=no,scrollbars=yes');
}

</script>



</body>

</html>

