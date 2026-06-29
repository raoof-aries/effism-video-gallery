<?php
session_start();
if(!isset($_SESSION['user_id']))
header("location:index.php");
include("includes/connect.inc.php");
$current_user_id= $_SESSION['user_id'];

$job_type_array=array();	
$job_type_sql = "select * from tbl_job_type order by job_type_name";
$job_type_result = $mysqli->query($job_type_sql);
$job_type_row = $job_type_result->fetch_assoc();
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];
while($job_type_row = $job_type_result->fetch_assoc()) {
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];

}


$user_query = $mysqli->query("select a.group_access,a.location_access,a.division_access,a.user_access,a.user_not_access,a.location_not_access, u.username from tbl_users u left join tbl_user_access a on  u.user_id=a.user_id 
 where a.module_access='main_module' and u.user_id=$current_user_id");
$user_row = $user_query->fetch_assoc();
$group_access= $user_row['group_access'];
$location_access= $user_row['location_access'];
$division_access= $user_row['division_access'];
$user_access= $user_row['user_access'];
$user_not_access= $user_row['user_not_access'];
$location_not_access= $user_row['location_not_access'];


$query ="SELECT full_name,short_name,user_id,d.emp_div_name as division,d.emp_div_id FROM tbl_users u left join tbl_emp_division d on d.emp_div_id=u.division_id  
 where  u.status='Active' and u.is_regular=1 and  (u.user_id=$current_user_id ";
if($group_access!="")
$query.=" or u.group_id in  ($group_access)  ";
if($location_access!="")
$query.=" or u.location_id in  ($location_access)  ";
if($division_access!="")
$query.=" or u.division_id in  ($division_access)  ";
if($user_access!="")
$query.=" or u.user_id in  ($user_access)  ";

$query.=") ";

if($user_not_access!="")
$query.=" and( u.user_id not in  ($user_not_access) ) ";
if($location_not_access!="")
$query.=" and ( u.location_id not in  ($location_not_access) ) ";

$query.=" order by d.emp_div_name,u.short_name";



$result= $mysqli->query($query);

$log_date=date('Y-m-d H:i:s',time()+14400);






$user_id = ($_REQUEST['user_id']!="")?$_REQUEST['user_id']:$_SESSION['user_id'];
$user_name = get_user_details($user_id);

if($user_id!=$current_user_id)
{


   $log_sql= "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,jobdiary_date,access_id) values('$current_user_id','$log_date','Daily Job Status','View Reports','View Reports of $user_name ','','$user_id')";
   
$mysqli->query($log_sql);

}

$current_month=date("m");
$current_day= date("d");
$current_year= date("Y");

$date_from ="01-$current_month-$current_year";
$date_to   =date('d-m-Y',time()+14400);


$datefrom = ($_REQUEST['date_from']!="")? $_REQUEST['date_from']:$date_from;
$dateto =   ($_REQUEST['date_to']!="")? $_REQUEST['date_to']:$date_to;

$date_from =date("Y-m-d",strtotime($datefrom));
$date_to   =date("Y-m-d",strtotime($dateto));


$time_work_query = $mysqli->query("select   time_to_sec(ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i'))
,TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(outside,'%k:%i')),TIME_FORMAT(night, '%k:%i'))) as net_time_sec,efficiency,system_ok,condition_remarks,is_complete, 	(current_efficiency*100) as current_efficiency,(not_punctual*5.00) as  not_punctual,late_remarks,(no_health*2) as no_health,(unplan*5) as unplan,	remarks,DATE_FORMAT(health,'%H:%i') as health,DATE_FORMAT(night,'%H:%i') as night,DATE_FORMAT(outside,'%H:%i') as outside,DATE_FORMAT(nwt,'%H:%i') as nwt,IF(time_in='00:00:00','00:00',TIME_FORMAT(time_in, '%r')) as time_in,IF(not_punctual=1,'No','Yes') as punctual_status,IF(time_out='00:00:00'||time_out='00:00:12' ,'00:00',TIME_FORMAT(time_out, '%r')) as time_out,date_log,ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i')),TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(outside,'%k:%i')),TIME_FORMAT(night,'%k:%i')) as effective,time_to_sec(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i')),TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(outside,'%k:%i'))) as effective_sec  from tbl_time where user_id=$user_id and date_log<='$date_to' and date_log>='$date_from' order by  date_log desc ");




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
<script type="text/javascript" src="javascript/date-functions.js"></script>
<script type="text/javascript" src="javascript/js.js"></script>

</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form action="view_reports.php" method="post">	
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<?php	                                       			
	include("includes/header.php");

$num = 1;
?>
<tr>
	<td>
    
     <br>

	<table style="border-collapse: collapse;" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="8" align="center"><font size="+1" color="#a6107b"><b>Daily Status of <?php	   echo $user_name ?></b></font></td>
        </tr>
        <tr>
          <td colspan="8"><br></td>
        </tr>
      
	  <tr>
        <td colspan="2" align="center"> From:
          <input name="date_from" id="date_from" readonly="readonly" onClick="return showCal('date_from', 'dd-mm-y');" value="<?php	                                       			 echo $datefrom?>" type="text">
            <img title="Calendar" style="vertical-align: top;" src="images/calendar0.gif" onClick="return showCal('date_from', 'dd-mm-y');" border="0">
            <div class="dateChooser" id="chooserSpan1" style="display: none; visibility: hidden; width: 145px;"></div></td>
	    <td align="left"> To:
	      <input name="date_to" id="date_to" readonly="readonly" onClick="return showCal('date_to', 'dd-mm-y');" value="<?php	                                       			 echo $dateto?>" type="text">
            <img title="Calendar" style="vertical-align: top;" src="images/calendar0.gif" onClick="return showCal('date_to', 'dd-mm-y');" border="0">
            <div class="dateChooser" id="chooserSpan1" style="display: none; visibility: hidden; width: 145px;"></div></td>
	    <td colspan="3" align="left"><b>Name:&nbsp;</b>
            <select name="user_id" id="user_id" style="width: 150px;">
            
              <?php	                                       			
			  $previous_div_id=0;
              
			  while($user_row=$result->fetch_assoc())
				{
					
					if($previous_div_id!=$user_row['emp_div_id'])
					{
					echo "<optgroup label='".$user_row['division']."'>";
					$i=1;
					}
					
					$previous_div_id=$user_row['emp_div_id'];
					
					?>
				<option <?php	                                       			 if($user_row['user_id']==$user_id) {?> selected="selected" <?php	                                       			 }?> value="<?php	                                       			 echo $user_row['user_id'];?>"><?php	                                       			 echo ($user_row['short_name']!="")?$user_row['short_name']:$user_row['full_name'];?> -<?php echo $i." ";?></option><?php	                                       		
				$i++;
					 }?>
				
			  
            </select>
	      &nbsp;&nbsp;
	      <input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image">
	      &nbsp;&nbsp;
	      
	      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:document.location.href='daily_work_sheet.php?date_from=<?php	                                       			 echo $datefrom?>&amp;date_to=<?php	                                       			 echo $dateto?>&amp;user_id=<?php	                                       			 echo $user_id?>'" style="font-weight: bold;">Export To Excel <img src="images/page_excel.png" border="0"></a> </td>
	    </tr>
      <tr>
        <td colspan="8" align="center"><div style="display: block;"><font color="#ff0000"><b></b></font></div></td>
      </tr>

    </table>

	<?php
	$total_rate= 0;	                                       			
	while($q=$time_work_query->fetch_assoc())
	{
	 $get_task =get_task($q['date_log'],$user_id);	
	?>
	  <table style="border-collapse: collapse;" width="95%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tbody>
          <tr>
            <td colspan="3" width="30%"><b><font size="2" color="#0000ff" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></b></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td colspan="4"><b><font size="2" color="#0000ff" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;</font></b></td>
          </tr>
          <!--<tr>
					<td colspan="8">&nbsp;</td>
				</tr>-->
          <tr height="25">
            <td colspan="8" style="border-style: solid; border-color: rgb(0, 0, 0)  rgb(0, 0, 0)  rgb(0, 0, 0) rgb(0, 0, 0); border-width: 1px 1px 1px 1px;" bgcolor="#e2f3fd"><font size="2"><b style="color:#0000FF">&nbsp;&nbsp;<?php	                                       			 echo date("l, M d, Y",strtotime($q['date_log']));?>&nbsp;&nbsp;</b><font size="2"><b>Time in: <?php	                                       			 echo $q['time_in']?> &nbsp;&nbsp;&nbsp;Time out: <?php	                                       			 echo $q['time_out']?>&nbsp;&nbsp;&nbsp; Break: <?php	                                       			 echo $q['nwt']?> Hrs&nbsp;&nbsp;&nbsp; Outside: <?php	                                       			 echo $q['outside']?> Hrs&nbsp;&nbsp;&nbsp; Night: <?php	                                       			 echo $q['night']?> Hrs&nbsp;&nbsp;&nbsp; Health: 
              <?php	                                       			 echo $q['health']?> Hrs</b></font>&nbsp;</td>
            <td colspan="3" style="border-style: solid; border-color: rgb(0, 0, 0); border-width: 1px 1px 1px 1px;" bgcolor="#e2f3fd">&nbsp;<b>Total: <font size="2" color="#0000ff"><?php	                                       			 echo $q['effective']?> Hrs</font></b></b>&nbsp;&nbsp;</td>
          </tr>
      <?php if($q['remarks']!=""){?>    
       <tr height="25">
            <td colspan="11" style="border-style: solid; border-color: rgb(0, 0, 0) rgb(0, 0, 0)  rgb(0, 0, 0) rgb(0, 0, 0); border-width: 1px 1px 1px 1px;" bgcolor="#e2f3fd"><font size="2"><b style="color:#FF0000">&nbsp;
              <?php	                                       			 echo $q['remarks']?>
            </b></font></td>
          
            
          </tr>  
          
          <?php
          }?>
         <tr height="25">
            <td colspan="11" style="border-style: solid; border-color: rgb(0, 0, 0) rgb(0, 0, 0)  rgb(0, 0, 0) rgb(0, 0, 0); border-width: 1px 1px 1px 1px;" bgcolor="#e2f3fd">&nbsp;&nbsp;<font size="2"><b style="color:#0000FF">Punctual:<span style="color:#FF0000"><?php echo $q['punctual_status']?></span>&nbsp;&nbsp;<?php if($q['late_remarks']!=""){ echo "<span style='color:#0000FF'>Remarks:</span><span style='color:#FF0000'>".$q['late_remarks']."</span>"; }?>
            </b></font></td>
          
            
          </tr>            
          <tr style="border: 1px solid rgb(0, 0, 0);" bgcolor="#e2f3fd" height="25">
            <td width="5%" style="border-left: 1px solid rgb(0, 0, 0);" align="center"><font size="2">No</font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="3%" align="center" bgcolor="#e2f3fd"><strong><font size="2">CF</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="30%" align="center" bgcolor="#e2f3fd"><strong><font size="2">Work</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="center" bgcolor="#e2f3fd"><strong><font size="2">Type</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="center" bgcolor="#e2f3fd"><strong>Delegate By </strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="20%" align="center" bgcolor="#e2f3fd"><strong><font size="2">Description</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle" bgcolor="#e2f3fd"><font size="2"><strong>Est Time</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle" bgcolor="#e2f3fd"><font size="2"><strong>Act Time</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle" bgcolor="#e2f3fd"><font size="2"><strong>Target</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle" bgcolor="#e2f3fd"><strong><font size="2">Result</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle" bgcolor="#e2f3fd"><strong><font size="2">Ratio</font></strong></td>
            </tr>
	<?php	                                       			

	$i =1;
	$j=1;
	$flag=0;
	$total_act = 0;
	$total_act_eff = 0;
	$total_est = 0;
	
	?>	  
        <?php while($job =$get_task->fetch_assoc())
		{
			
			?>
              
            
            
     <?php if(($job['assigned_by']>0)&&($flag==0))
	 {
		 $flag=1;
		 ?> 
         
         
          <tr bgcolor="#FF0000" style="border: 1px solid rgb(166, 16, 123); " height="35">
<td colspan="6"  style="border: 1px solid rgb(0, 0, 0); padding-right:5px" align="right"  bgcolor="#f2f2f2">&nbsp;<strong>Unplan:-<?php echo $q['unplan']?>%, Punctual:-<?php echo $q['not_punctual']?>%, Health:-<?php echo $q['no_health']?>%</strong></td>

<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2"><strong><?php echo gmdate('H:i:s',$total_est);?></strong></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2"><strong><?php echo gmdate('H:i:s',$total_act);?></strong></td>
<td style="border: 1px solid rgb(0, 0, 0);" bgcolor="#f2f2f2">&nbsp; 
Actual: <font style="font-size:16px;color:#FF0000; font-weight:bold" align="center"><?php
	if($q['current_efficiency']>0)
	{
	if($q['net_time_sec']>=21600)
	 echo $q['current_efficiency']."%";
	}
	 else if($q['net_time_sec']>=21600)
	 echo $q['efficiency']."%";
	 
	 
 ?></font></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2" colspan="2">Net:<font style="font-size:16px;color:#FF0000; font-weight:bold" align="center"><?php
	 
	 $net = $q['current_efficiency']-$q['unplan']-$q['not_punctual']-$q['no_health'];
	 
	 if(($net>0)&&($q['net_time_sec']>=21600))
	 echo $net."%";
	 
	 
 ?></font>&nbsp;</td>
          </tr>      
             <tr height="25">
            <td align="center" colspan="11" style="border-style: solid; border-color: rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0) rgb(0, 0, 0); border-width: 1px 1px 1px 1px;" bgcolor="#94BFE9"><font size="2"><b>Delegated Jobs</b></font></td>
     
          
          </tr>
          <tr style="border: 1px solid rgb(0, 0, 0);" bgcolor="#94BFE9" height="25">
            <td width="5%" style="border-left: 1px solid rgb(0, 0, 0);" align="center"><font size="2">No</font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="5%" align="center" >&nbsp;</td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="30%" align="center" ><strong><font size="2">Work</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="center" ><strong><font size="2">Type</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="center" ><strong>Delegate to </strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="20%" align="center"><strong><font size="2">Description</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle"><font size="2"><strong>Est Time</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle"><font size="2"><strong>Act Time</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle"><font size="2"><strong>Target</strong></font></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle"><strong><font size="2">Result</font></strong></td>
            <td style="border: 1px solid rgb(0, 0, 0);" width="10%" align="middle"><strong><font size="2">Ratio</font></strong></td>
            </tr>
            
            
            
            
            
            
            
          <?php 
	 }?>
            
           <?php
		   $total_act=$total_act+$job['act_sec'];
		   $total_est=$total_est+$job['est_sec'];
		   
$total_act_eff=$total_act_eff+($job['act_sec']*$job['eff_ratio']);
		   
		   ?>
			   
            
            
        
          <tr <?php if($job['un_sch']==1){?> bgcolor="#FFCCFF" <?php } else{  echo "bgcolor='#f2f2f2'"; }?> style="border: 1px solid rgb(166, 16, 123);" height="35">
<td style="border: 1px solid rgb(0, 0, 0);" align="center" ><?php echo $j?></td>
<td style="border: 1px solid rgb(0, 0, 0); padding-left:5px" align="center"  ><strong><?php echo $job['cf']?></strong></td>
<td style="border: 1px solid rgb(0, 0, 0); padding-left:5px" align="left"  ><?php echo $job['taskname']?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job_type_array[$job['job_type']]?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['username']?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['description']?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['est_time']?><br><?php echo $job['est_entry']?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['act_time']?><br><?php echo (($job['act_time']!='00:00')&&($job['act_time']!=''))?($job['act_entry']):''?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['target_date']?></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><?php echo $job['status']?>%</td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"><strong><?php echo (($job['act_time']!='00:00')&&($job['act_time']!=''))?($job['eff_ratio']):''?></strong></td>
          </tr>
		  <?php	                                       			
		 $j++;
		  }
		  ?>
          
          <?php
		  if($flag==0)
		  {
			  
		//system_ok,condition_remarks,is_complete, 	current_efficiency,,late_remarks,,,,less_jobs, 	,rema	
			  ?>     
          <tr style="border: 1px solid rgb(166, 16, 123); " height="35">
<td colspan="6"  style="border: 1px solid rgb(0, 0, 0); padding-right:5px" align="left"  bgcolor="#f2f2f2">&nbsp;<strong>Unplan:-<?php echo $q['unplan']?>%, Punctual:-<?php echo $q['not_punctual']?>%, Health:-<?php echo $q['no_health']?>%</strong></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2"><strong><?php echo gmdate('H:i:s',$total_est);?></strong></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2"><strong><?php echo gmdate('H:i:s',$total_act);?></strong></td>
<td style="border: 1px solid rgb(0, 0, 0);" bgcolor="#f2f2f2">&nbsp; 
Actual: <font style="font-size:16px;color:#FF0000; font-weight:bold" align="center"><?php
	
	if($q['current_efficiency']>0)
	{
	if($q['net_time_sec']>=21600)
	 echo $q['current_efficiency']."%";
	}
	 else if($q['net_time_sec']>=21600)
	 echo $q['efficiency']."%";
	 
	 
 ?></font></td>
<td style="border: 1px solid rgb(0, 0, 0);" align="center"  bgcolor="#f2f2f2" colspan="2">Net:<font style="font-size:16px;color:#FF0000; font-weight:bold" align="center"><?php
	 
	 $net = $q['current_efficiency']-$q['unplan']-$q['not_punctual']-$q['no_health'];
	 
	 if(($net>0)&&($q['net_time_sec']>=21600))
	 echo $net."%";
	 
	 
 ?></font>&nbsp;</td>
          </tr>
<?php    
		  }
		  
		$total_act=0;
 $total_est=0;

 
	}?>
          
                 
          <!--------------page initit------------------------>
          <tr>
            <td colspan="13" valign="bottom" align="center"></td>
          </tr>
          <!--------------page initit------------------------>
        </tbody>
    </table>
	
	<?php	                                       			
$mysqli->close();
	?>
    </td>
</tr>
 <?php	                                       			 include("includes/footer.php");?>
</table>

</form></body></html>

<?php	                                       			
function get_task($date,$user_id)
{
global $user_id,$mysqli;



$assigned_query =$mysqli->query("(SELECT if(w1.is_carry=1,'CF','') as cf,w1.job_type,w1.un_sch,w1.taskname,u1.username,w1.eff_ratio,w1.description,w1.status,if(w1.est_time='00:00:00','',w1.est_time) as  est_time,if(w1.act_time='00:00:00','',w1.act_time) as  act_time,w1.assigned_by,TIME_TO_SEC(w1.act_time) as act_sec,TIME_TO_SEC(w1.est_time) as est_sec,w1.target_date,if(w1.est_entry='0000-00-00 00:00:00','',w1.est_entry) as  est_entry,if(w1.act_entry='0000-00-00 00:00:00','',w1.act_entry) as act_entry   FROM  tbl_workreports w1 left join tbl_users u1 on u1.user_id=w1.user_id where  w1.assigned_by='$user_id' and w1.date_report='$date' and w1.taskname!='') UNION (SELECT if(w2.is_carry=1,'CF','') as cf ,w2.job_type,w2.un_sch,w2.taskname,u2.username,w2.eff_ratio,w2.description,w2.status,if(w2.est_time='00:00:00','',w2.est_time) as  est_time,if(w2.act_time='00:00:00','',w2.act_time) as  act_time,0 as  assigned_by,TIME_TO_SEC(w2.act_time) as act_sec,TIME_TO_SEC(w2.est_time) as est_sec,w2.target_date,if(w2.est_entry='0000-00-00 00:00:00','',w2.est_entry) as  est_entry,if(w2.act_entry='0000-00-00 00:00:00','',w2.act_entry) as act_entry FROM  tbl_workreports w2 left join tbl_users u2 on u2.user_id=w2.assigned_by where w2.user_id='$user_id' and w2.date_report='$date' and w2.taskname!='' ) order by assigned_by"); 

 
return $assigned_query;
}

function get_user_details($user_id)
{
global $mysqli;
$assigned_query =$mysqli->query("select u.short_name, div1.emp_div_name  from tbl_users u left join  tbl_emp_division div1 on div1.emp_div_id=u.division_id where user_id=$user_id");
$user_array = $assigned_query->fetch_assoc();
return $user_array['short_name']."(".$user_array['emp_div_name']." Division)";

}?>