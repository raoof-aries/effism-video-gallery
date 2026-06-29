<?php

$delegate_array= array("1"=>"<font style='color:#FF0000;font-size:10px;font-weight:bold'>Delegation</font>","2"=>"<font style='color:#000000;font-size:10px;font-weight:bold'>Sharing</font>","3"=>"<font style='color:#0000FF;font-size:10px;font-weight:bold'>Requisition</font>","4"=>"<font style='color:#9900CC;font-size:10px;font-weight:bold'>Proposal</font>");	
	
$job_type_array=array();	
$job_type_array[0]='';
$source='"",';

$selected_type_result=$mysqli->query("select * from tbl_user_customisation c left join   tbl_job_type t on c.custom_id=t.id where c.type='job_type' and   t.type_status='Active' and c.user_id='$user_id' order by t.job_type_name");

if($selected_type_result->num_rows > 0) 
{
$job_type_row = $selected_type_result->fetch_assoc();
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];

$source.='"'.$job_type_row['job_type_name'].'"';

while($job_type_row = $selected_type_result->fetch_assoc()) {
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];

$source.=',"'.$job_type_row['job_type_name'].'"';	
}

	
}
else
{
$job_type_sql = "select * from  tbl_job_type order by job_type_name";
 $job_type_result = $mysqli->query($job_type_sql);
 

$job_type_row = $job_type_result->fetch_assoc();
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];

$source.='"'.$job_type_row['job_type_name'].'"';

while($job_type_row = $job_type_result->fetch_assoc()) {
$job_type_array[$job_type_row['id']]=$job_type_row['job_type_name'];

$source.=',"'.$job_type_row['job_type_name'].'"';	
}
}


$main_type_array=array();	
$main_type_array[0]='';
$main_type_source='"",';
$main_type_sql = "select * from   tbl_main_type order by main_type_id";
$main_type_result = $mysqli->query($main_type_sql);
 
$main_type_row = $main_type_result->fetch_assoc();
$main_type_array[$main_type_row['main_type_id']]=$main_type_row['main_type_name'];

$main_type_source.='"'.$main_type_row['main_type_name'].'"';

while($main_type_row = $main_type_result->fetch_assoc()) {
$main_type_array[$main_type_row['main_type_id']]=$main_type_row['main_type_name'];

$main_type_source.=',"'.$main_type_row['main_type_name'].'"';	
}



$job_no_array=array();	
$job_no_array[0]='';
 $job_no_sql = "select * from tbl_job_no_delegation d left join tbl_job_numbers j on j.id=d.job_id where  d.assigned_to='$user_id'";
 $job_no_result = $mysqli->query($job_no_sql);
 if($job_no_result->num_rows > 0) {

$job_no_row = $job_no_result->fetch_assoc();
$job_no_array[$job_no_row['id']]=$job_no_row['id'];

$job_no_source='"'.$job_no_row['job_no'].'"';
}
while($job_no_row = $job_no_result->fetch_assoc()) {
$job_no_array[$job_no_row['id']]=$job_no_row['job_no'];

$job_no_source.=',"'.$job_no_row['job_no'].'"';	
}

	


	


$sql= "SELECT DATE_FORMAT(w.job_est,'%H:%i') as  job_est, w.is_carry,d.delegate_type,w.workreport_id, d.taskname ,w.main_type,w.job_type,w.description ,w.job_no,w.client,IF(w.target_date='0000-00-00', '',DATE_FORMAT(w.target_date,'%d-%m-%Y'))  as  target_date,w.status,DATE_FORMAT(w.est_time,'%H') as  est_time_hr,DATE_FORMAT(w.est_time,'%i') as est_time_min,DATE_FORMAT(w.act_time,'%H') as  act_time_hr,DATE_FORMAT(w.act_time,'%i')act_time_min,d.assigned_by,d.is_reopen,d.reopen_remarks,IF(w.cf_date='0000-00-00', '',DATE_FORMAT(w.cf_date,'%d-%m-%Y'))  as  cf_date,u.username,w.eff_ratio from tbl_workreports w left join tbl_delegation d on d.delegation_id=w.delegation_id left join tbl_users u on u.user_id=d.assigned_by where w.user_id='$user_id' and d.assigned_by>0 and  w.date_report='$sql_date';";
$sql.= "SELECT  workreport_id,taskname ,w.main_type,w.job_type,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date,status,DATE_FORMAT(est_time,'%H') as  est_time_hr,DATE_FORMAT(est_time,'%i') as est_time_min,DATE_FORMAT(act_time,'%H') as  act_time_hr,DATE_FORMAT(act_time,'%i')act_time_min,IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,DATE_FORMAT(job_est,'%H:%i') as  job_est,DATE_FORMAT(prev_act,'%H:%i') as  prev_act,eff_ratio,is_carry from tbl_workreports w  where user_id='$user_id' and  delegation_id=0 and  (is_carry=1 or is_carry=2) and  date_report='$sql_date';";	




$other = array();
$carry = array();
$active = 'other';



/* execute multi query */
if ($mysqli->multi_query($sql)) {
    do {
        /* store result set */
        if ($result = $mysqli->store_result()) {
                 while($row=$result->fetch_assoc())
						${$active}[] =$row;
           
            $result->close();
        }
        /* next set of results */
        if ($mysqli->more_results()) {
            $active = 'carry';
        }
    } while ($mysqli->next_result());
} 




	



	

	
	
	$excel_query = "SELECT  if(un_sch=1,'true','false') as  un_sch,
	 if(is_rework!=0,'true','false') as  is_rework,
	workreport_id, main_type,row_id,taskname,  job_type_name,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date, IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,w.status, IF(DATE_FORMAT(est_time,'%H:%i')!='00:00',DATE_FORMAT(est_time,'%H:%i'),'') as est_time,IF(DATE_FORMAT(act_time,'%H:%i')!='00:00',DATE_FORMAT(act_time,'%H:%i'),'') as act_time,IF(DATE_FORMAT(job_est,'%H:%i')!='00:00',DATE_FORMAT(job_est,'%H:%i'),'') as job_est,eff_ratio  from tbl_workreports w left join tbl_job_type t  on w.job_type=t.id  where is_carry=0 and  date_report='$sql_date'  and user_id=$user_id and delegation_id=0 order by row_id desc";



	
	 $excel_result = $mysqli->query($excel_query);
	 



$daily_rows = array();
$i=1;


$extra=0;

while($r = $excel_result->fetch_assoc()) {
	$r['id']=$i++;
	$r['main_type_name']=$main_type_array[$r['main_type']];
	
     $daily_rows[] = $r;
}


	$excel_result->free();

 
$test =json_encode($daily_rows);


	
	
	
$start = strtotime('12am');
$tod = $start;
$est_act_source='"'.date('H:i', $tod).'"';
for ($i = 1; $i < (100 * 5); $i++) {
    $tod = $start + ($i * 5 * 60);
  	$est_act_source.=',"'.date('H:i', $tod).'"';	
}  



$job_est="";
$job_est='"00:00"';

for($hours=0; $hours<=100; $hours++)
    for($minutes=0; $minutes<60; $minutes+=30)
	  	$job_est.=',"'.sprintf("%02d:%02d",$hours,$minutes).'"';	

        














?>
<!doctype html>

<head>


<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<script data-jsfiddle="common" src="lib/jquery.min.js"></script>
<script data-jsfiddle="common" src="dist/jquery.handsontable.full.js"></script>
<link data-jsfiddle="common" rel="stylesheet" media="screen" href="dist/jquery.handsontable.full.css">
<script data-jsfiddle="common" src="lib/jquery-ui/js/jquery-ui.custom.min.js"></script>
<link data-jsfiddle="common" rel="stylesheet" media="screen" href="lib/jquery-ui/css/ui-bootstrap/jquery-ui.custom.css">
<link href="css/global.css" rel="stylesheet" type="text/css" />
<link href="css/calendarstyle.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<script src="javascript/calendar_new.js" type="text/javascript"></script>
<script src="javascript/login-check.js" type="text/javascript"></script>
<script type="text/javascript" src="js/popup.js"></script>
<link href="main.css" rel="stylesheet" type="text/css" />
<style type="text/css">
    .handsontable .htDimmed {
    background-color: #cccccc;
    color: #f00000;
    font-style: italic;
    overflow: hidden;
}
.handsontable .currentRow {
  background: #FCE4EC
}//******ADDED BY SARANY ON 12-7-21 FOR ROW HIGHLIGHTING******/
.ui-datepicker {

    margin-left: -150px !important;
}
</style>
<script src="javascript/js.js" type="text/javascript"></script>
<script language="javascript"> 
from_page = 'jobdiary';

function openWindow(url, title)
{
 var left = (screen.width - 900) / 2;
 var top = (screen.height - 500) / 2;
 return window.open(url, title, 'width=900,height=500,left='+left+',top='+top+',screenX='+left+',screenY='+top+',status=no,scrollbars=yes');
}




function auto_save(id,type)
{
	
	
est_time_hr=document.getElementById('est_time_hr'+id).value;
est_time_min=document.getElementById('est_time_min'+id).value;
act_time_hr=document.getElementById('act_time_hr'+id).value;
act_time_min=document.getElementById('act_time_min'+id).value;
description=document.getElementById('description'+id).value;
cf_date= document.getElementById('cf_date'+id).value;
is_carry="";
target_date="";


if(document.getElementById('target_date'+id)&&(document.getElementById('target_date'+id).value!=""))
{
	target_date =document.getElementById('target_date'+id).value;
}


if(est_time_hr=="")
est_time_hr="00";
if(act_time_hr=="")
act_time_hr	="00";

if(est_time_min=="")
est_time_min="00";
if(act_time_min=="")
act_time_min="00";



if(document.getElementById('is_carry'+id))
{
	
	if(document.getElementById('is_carry'+id).type=="hidden")
	{
	is_carry=document.getElementById('is_carry'+id).value;
	}
	else
	{
		if(document.getElementById('is_carry'+id).checked==true)
		is_carry=2
		else	
		is_carry=1
	}


}


est_time=est_time_hr+":"+est_time_min+":00";
act_time=act_time_hr+":"+act_time_min+":00";	
	

var s = document.getElementById('status'+id);
var status = s.options[s.selectedIndex].value;

if(status==100)
document.getElementById('td_status_'+id).style.background='#e9e9e9';
else
document.getElementById('td_status_'+id).style.background='#FF0000';


	
	var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	
		request_save.open("POST", "auto_save.php", true);
		
	request_save.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request_save.onreadystatechange = function(){
		if (request_save.readyState == 4)
		{
			if(request_save.responseText=="Logout")
			{
				alert("Your Session has been Expired Please Login Again"); 
			window.location.href='index.php';

				
			}
			else
			{
				time_array= (request_save.responseText).split("&");

			document.getElementById("est_div").innerHTML=time_array[0];   
			document.getElementById("act_div").innerHTML=time_array[1];
			document.getElementById("routine_est_div").innerHTML=time_array[2];
			document.getElementById("routine_act_div").innerHTML=time_array[3]; 
			document.getElementById("total_est_div").innerHTML=time_array[4];
			document.getElementById("total_act_div").innerHTML=time_array[5]; 
			       
			}
			
			
		}
	}
	
	if(target_date!="")
	request_save.send('date=<?php echo $date?>&workreport_id='+id+'&est_time='+est_time+'&act_time='+act_time+'&description='+description+"&status="+status+"&cf_date="+cf_date+"&type="+type+"&target_date="+target_date);
	else
	request_save.send('date=<?php echo $date?>&workreport_id='+id+'&est_time='+est_time+'&act_time='+act_time+'&description='+description+"&status="+status+"&cf_date="+cf_date+"&type="+type+"&is_carry="+is_carry);
		
}




function routine_save(id)
{

daily_est_hr=document.getElementById('daily_est_hr'+id).value;
daily_est_min=document.getElementById('daily_est_min'+id).value;


daily_act_hr=document.getElementById('daily_act_hr'+id).value;
daily_act_min=document.getElementById('daily_act_min'+id).value;

routine_remarks=document.getElementById('routine_remarks'+id).value;


if(daily_act_hr=="")
daily_act_hr="00";

if(daily_act_min=="")
daily_act_min="00";


daily_act=daily_act_hr+":"+daily_act_min+":00";


if(daily_est_hr=="")
daily_est_hr="00";
if(daily_est_min=="")
daily_est_min="00";
daily_est=daily_est_hr+":"+daily_est_min+":00";	

	


var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
	
		request_save.open("POST", "auto_save.php", true);
		
	request_save.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request_save.onreadystatechange = function(){
		if (request_save.readyState == 4)
		{
			if(request_save.responseText=="Logout")
			{
				alert("Your Session has been Expired Please Login Again"); 
				window.location.href='index.php';

				
			}
			else
			{
				time_array= (request_save.responseText).split("&");

			document.getElementById("est_div").innerHTML=time_array[0];   
			document.getElementById("act_div").innerHTML=time_array[1];
			document.getElementById("routine_est_div").innerHTML=time_array[2];
			document.getElementById("routine_act_div").innerHTML=time_array[3]; 
			document.getElementById("total_est_div").innerHTML=time_array[4];
			document.getElementById("total_act_div").innerHTML=time_array[5]; 
			       
			}
			
			
		}
	}
	request_save.send('date=<?php echo $date?>&job_id='+id+'&routine=1&daily_est='+daily_est+"&daily_act="+daily_act+"&routine_remarks="+routine_remarks);

}





from_page = 'jobdiary';
 function check_form(form,type,status)
{
	
	 var  timein_hr= document.getElementById('timein_hr').value;
	 var  timein_min= document.getElementById('timein_min').value;
	 var  timeout_hr= document.getElementById('timeout_hr').value;
	 var  timeout_min= document.getElementById('timeout_min').value;
	 var   ampm_in= document.getElementById('ampm_in').value;
	 var   ampm_out= document.getElementById('ampm_out').value;
	 var   date_from= document.getElementById('date_from').value;
	 
	 var outoffice_hr= document.getElementById('outoffice_hr').value;
	 var outoffice_min= document.getElementById('outoffice_min').value;
	 
	 
	 var  home_hr= document.getElementById('home_hr').value;
	 var  home_min= document.getElementById('home_min').value;

	 
	 var nwt_hr= document.getElementById('nwt_hr').value;
 	 var nwt_min= document.getElementById('nwt_min').value;
	 var remarks= document.getElementById('remarks').value;
	 
	 var system_ok=  document.getElementById('system_ok').value;
	 var condition_remarks=  document.getElementById('condition_remarks').value;

		remarks=remarks.trim();

	if(type=='save_data')
	{
		
	
	if((timein_hr!="")&&(timein_min==""))
	{
		alert("Please Enter Time In Mins");
		document.getElementById('timein_min').focus();
		return false;
	}
	if((timein_min!="")&&(timein_hr==""))
	{
		alert("Please Enter Time In Hrs");
		document.getElementById('timein_hr').focus();
		return false;
	}
	
	if(timein_hr>12)
	{
		alert("Please Enter Correct Time in");
		document.getElementById('timein_hr').focus();
		return false;
	}
	
		
	if(timein_min>=60)
	{
		alert("Please Enter Correct Time in Minutes");
		document.getElementById('timein_min').focus();
		return false;
	}
	
	
	if((timeout_hr!="")&&(timeout_min==""))
	{
		alert("Please Enter Time Out Mins");
		document.getElementById('timeout_min').focus();
		return false;
	}
	if((timeout_min!="")&&(timeout_hr==""))
	{
		alert("Please Enter Time Out Hrs");
		document.getElementById('timeout_hr').focus();
		return false;
	}
	
	if(timeout_hr>12)
	{
		alert("Please Enter Correct Time Out");
		document.getElementById('timeout_hr').focus();
		return false;
	}
	
	if(timeout_min>60)
	{
		alert("Please Enter Correct Time out Minutes");
		document.getElementById('timeout_min').focus();
		return false;
	}
	
	
	
	
	if(nwt_hr>=24)
	{
		alert("Please Enter Correct Break Time");
		document.getElementById('nwt_hr').focus();
		return false;
	}
	
	
	
	if((system_ok>=10)&&(system_ok<=70)&&(condition_remarks==""))
	{
		alert("Please Enter the Reason For Poor Condition");
		document.getElementById('condition_remarks').focus();
		return false;
	}
	if(system_ok>=80)
	{
		document.getElementById('condition_remarks').value="";
		
	}
	

var date1 = new Date('01/14/2014 '+'<?php echo $_SESSION['reporting_time']?>');
	
	var date2 = new Date('01/14/2014 '+timein_hr+':'+timein_min+':00 '+ampm_in);
		
			  	var sec = (date2.getTime()/1000.0) - (date1.getTime()/1000.0);	

		document.getElementById('late_remarks').value="<?php echo $late_remarks?>";
		if(sec>0)
		{
			<?php if($not_punctual_value=="1"){?>
			
		 document.getElementById("not_punctual").selectedIndex=1;

   		
		<?php
			}
			else if(($not_punctual_value=="0")&&($late_remarks!="")){?>
			
			document.getElementById("not_punctual").selectedIndex=2;

   		

			<?php 
			}
			else
			{?>
			
			 document.getElementById("not_punctual").selectedIndex=0;
				
		 <?php }
		
		
		?>


	if(document.getElementById('late_remarks').value=="")
	{	
		   	document.getElementById('save_value').value='save_date';




		Popup.showModal('modal');
		return false;
		
	}
	else
	return true;
		
		}
		
		document.getElementById('not_punctual').value=0;
		document.getElementById('late_remarks').value="";	
	
		   		document.getElementById('save_value').value='save_date';

	
	return true;
	}
	else
	{
	if(type=='save_complete')
	{
	  
        
	 if((status!="leave")&&(status!="holiday"))
	 {	
	
	
	if((timein_hr=="")&&(timein_min=="")&&(timeout_hr=="")&&(timeout_min=="")&&(home_min=="")&&(home_hr==""))
	{
		alert("Please Enter Your Time ");
		return false;
	}
	
	
	if((timein_hr!="")&&(timein_min==""))
	{
		alert("Please Enter Time In Mins");
		document.getElementById('timein_min').focus();
		return false;
	}
	if((timein_min!="")&&(timein_hr==""))
	{
		alert("Please Enter Time In Hrs");
		document.getElementById('timein_hr').focus();
		return false;
	}
	
	if(timein_hr>12)
	{
		alert("Please Enter Correct Time in");
		document.getElementById('timein_hr').focus();
		return false;
	}
	
		
	if(timein_min>=60)
	{
		alert("Please Enter Correct Time in Minutes");
		document.getElementById('timein_min').focus();
		return false;
	}
	
	
	if((timeout_hr!="")&&(timeout_min==""))
	{
		alert("Please Enter Time Out Mins");
		document.getElementById('timeout_min').focus();
		return false;
	}
	if((timeout_min!="")&&(timeout_hr==""))
	{
		alert("Please Enter Time Out Hrs");
		document.getElementById('timeout_hr').focus();
		return false;
	}
	
	if(timeout_hr>12)
	{
		alert("Please Enter Correct Time Out");
		document.getElementById('timeout_hr').focus();
		return false;
	}
	
	if(timeout_min>60)
	{
		alert("Please Enter Correct Time out Minutes");
		document.getElementById('timeout_min').focus();
		return false;
	}
	
	
	
	
	if(nwt_hr>=24)
	{
		alert("Please Enter Correct Break Time");
		document.getElementById('nwt_hr').focus();
		return false;
	}
	

	if((timein_hr!="")&&(timeout_hr==""))
	{
		alert("Please Enter Office Time Out");
		document.getElementById('timeout_hr').focus();
		return false;
	}
	
	if((timeout_hr!="")&&(timein_hr==""))
	{
		alert("Please Enter Office Time In");
		document.getElementById('timein_hr').focus();
		return false;
	}

	
	
	

		
		
		
		var date1 = new Date('01/14/2014 '+timein_hr+':'+timein_min+':00 '+ampm_in);
  	var date2 = new Date('01/14/2014 '+timeout_hr+':'+timeout_min+':00 '+ampm_out);
	
	
	  	var sec = (date2.getTime()/1000.0) - (date1.getTime()/1000.0);	

if(nwt_hr=="")nwt_hr=0;
if(nwt_min=="")nwt_min=0;
		
nwt_sec= (nwt_hr)*3600+(nwt_min*60);

eff=(sec-nwt_sec);

if(eff<=0)
{
	alert("Please enter correct Time in and Time out and Break Time");
	return false;

}


if((system_ok>=10)&&(system_ok<=70)&&(condition_remarks==""))
	{
		alert("Please Enter the Reason For Poor Condition");
		document.getElementById('condition_remarks').focus();
		return false;
	}
var date1 = new Date('01/14/2014 '+'<?php echo $_SESSION['reporting_time']?>');
	
	var date2 = new Date('01/14/2014 '+timein_hr+':'+timein_min+':00 '+ampm_in);
		
			  	var sec = (date2.getTime()/1000.0) - (date1.getTime()/1000.0);	

		
		if(sec>0)
		{
			<?php if($not_punctual_value=="1"){?>
			
		 document.getElementById("not_punctual").selectedIndex=1;

   		document.getElementById('late_remarks').value="<?php echo $late_remarks?>";
		<?php
			}
else if(($not_punctual_value=="0")&&($late_remarks!="")){?>
			
			document.getElementById("not_punctual").selectedIndex=2;

   		document.getElementById('late_remarks').value="<?php echo $late_remarks?>";

			<?php 
			}
			else
			{?>
			
			 document.getElementById("not_punctual").selectedIndex=0;
				
		 <?php }
		
		
		?>
				   		document.getElementById('save_value').value='save_date_complete';

		

		Popup.showModal('modal');
		return false;
		}	
	
	

		
}
				   		document.getElementById('save_value').value='save_date_complete';

		document.getElementById('complete_button').style.display="none";
		document.getElementById('complete_message').style.display="";
		
		

			
	var status =confirm("Are You Sure Want to Complete this JobDiary....?");
	
	if(status==true)
	return true;
	else
	{
	
	document.getElementById('complete_button').style.display="";
	document.getElementById('complete_message').style.display="none";
	return false;
	}
	
	
		

	
	}
	}
	
}
 </script>
<style>
#customers {
	font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;
	border-collapse:collapse;
}
#customers td, #customers th {
	font-size:1em;
	border:1px solid #6688AD;
	padding:3px 7px 2px 7px;
}
#customers th {
	font-size:1.1em;
	text-align:left;
	padding-top:5px;
	padding-bottom:4px;
	background-color:#A7C942;
	color:#ffffff;
}
#customers tr.alt td {
	color:#000000;
	background-color:#E8EFF5;
}
</style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  style="background-image:url(images/themes/<?php	                                       			 echo rand(5,14); ?>.jpg); background-repeat:repeat-x;">
<form name="jobform" method="POST" action="jobdiary.php"
					enctype="multipart/form-data" >
  <table width="100%" border="0" cellspacing="0" cellpadding="0"
		align="center">
    <?php	                                       			
		include("includes/header.php");
		?>
    <tr>
      <?php if(isset($time_row['is_complete'])&&$time_row['is_complete']==1){}
else
{
	
		$option='
	
                <option	 value="100"> 100 % </option>
                <option	 value="99"> 99 % </option>
                <option	 value="98"> 98 % </option>
                <option	 value="97"> 97 % </option>
                <option	 value="96"> 96 % </option>
                <option	 value="95"> 95 % </option>
                <option	 value="94"> 94 % </option>
                <option	 value="93"> 93 % </option>
                <option	 value="92"> 92 % </option>
                <option	 value="91"> 91 % </option>
                <option	 value="90"> 90 % </option>
                <option	 value="89"> 89 % </option>
                <option	 value="88"> 88 % </option>
                <option	 value="87"> 87 % </option>
                <option	 value="86"> 86 % </option>
                <option	 value="85"> 85 % </option>
                <option	 value="84"> 84 % </option>
                <option	 value="83"> 83 % </option>
                <option	 value="82"> 82 % </option>
                <option	 value="81"> 81 % </option>
                <option	 value="80"> 80 % </option>
                <option	 value="79"> 79 % </option>
                <option	 value="78"> 78 % </option>
                <option	 value="77"> 77 % </option>
                <option	 value="76"> 76 % </option>
                <option	 value="75"> 75 % </option>
                <option	 value="74"> 74 % </option>
                <option	 value="73"> 73 % </option>
                <option	 value="72"> 72 % </option>
                <option	 value="71"> 71 % </option>
                <option	 value="70"> 70 % </option>
                <option	 value="69"> 69 % </option>
                <option	 value="68"> 68 % </option>
                <option	 value="67"> 67 % </option>
                <option	 value="66"> 66 % </option>
                <option	 value="65"> 65 % </option>
                <option	 value="64"> 64 % </option>
                <option	 value="63"> 63 % </option>
                <option	 value="62"> 62 % </option>
                <option	 value="61"> 61 % </option>
                <option	 value="60"> 60 % </option>
                <option	 value="59"> 59 % </option>
                <option	 value="58"> 58 % </option>
                <option	 value="57"> 57 % </option>
                <option	 value="56"> 56 % </option>
                <option	 value="55"> 55 % </option>
                <option	 value="54"> 54 % </option>
                <option	 value="53"> 53 % </option>
                <option	 value="52"> 52 % </option>
                <option	 value="51"> 51 % </option>
                <option	 value="50"> 50 % </option>
                <option	 value="49"> 49 % </option>
                <option	 value="48"> 48 % </option>
                <option	 value="47"> 47 % </option>
                <option	 value="46"> 46 % </option>
                <option	 value="45"> 45 % </option>
                <option	 value="44"> 44 % </option>
                <option	 value="43"> 43 % </option>
                <option	 value="42"> 42 % </option>
                <option	 value="41"> 41 % </option>
                <option	 value="40"> 40 % </option>
                <option	 value="39"> 39 % </option>
                <option	 value="38"> 38 % </option>
                <option	 value="37"> 37 % </option>
                <option	 value="36"> 36 % </option>
                <option	 value="35"> 35 % </option>
                <option	 value="34"> 34 % </option>
                <option	 value="33"> 33 % </option>
                <option	 value="32"> 32 % </option>
                <option	 value="31"> 31 % </option>
                <option	 value="30"> 30 % </option>
                <option	 value="29"> 29 % </option>
                <option	 value="28"> 28 % </option>
                <option	 value="27"> 27 % </option>
                <option	 value="26"> 26 % </option>
                <option	 value="25"> 25 % </option>
                <option	 value="24"> 24 % </option>
                <option	 value="23"> 23 % </option>
                <option	 value="22"> 22 % </option>
                <option	 value="21"> 21 % </option>
                <option	 value="20"> 20 % </option>
                <option	 value="19"> 19 % </option>
                <option	 value="18"> 18 % </option>
                <option	 value="17"> 17 % </option>
                <option	 value="16"> 16 % </option>
                <option	 value="15"> 15 % </option>
                <option	 value="14"> 14 % </option>
                <option	 value="13"> 13 % </option>
                <option	 value="12"> 12 % </option>
                <option	 value="11"> 11 % </option>
                <option	 value="10"> 10 % </option>
                <option	 value="9"> 9 % </option>
                <option	 value="8"> 8 % </option>
                <option	 value="7"> 7 % </option>
                <option	 value="6"> 6 % </option>
                <option	 value="5"> 5 % </option>
                <option	 value="4"> 4 % </option>
                <option	 value="3"> 3 % </option>
                <option	 value="2"> 2 % </option>
                <option	 value="1"> 1 % </option>
                <option	 value="0"> 0 % </option>';
	
	 
	
	?>
  <td><?php 
  
  	
	
	
	
	 
	 ?>
     <br>
    <table id="customers" border="0" align="center" cellpadding="0" cellspacing="0"
							width="80%" class="form_style">
      <tr height="25">
        <td colspan="6" align="left" bgcolor="#e2f3fd"><b
									style="vertical-align: middle">Date:</b>
          <input type="hidden" name="save_value" id="save_value">          
           <?php
                         $tk_yesterday = date('d-m-Y', strtotime('-1 day', strtotime($date)));
                         $tk_nextday = date('d-m-Y', strtotime('+1 day', strtotime($date)));
                         ?>
                            <a href="javascript:void(0)" onclick="takeMetoDate('<?php echo $tk_yesterday?>')"> <img src="assets/icons/pre.png" width="20"></a>
                             
          <input type="text"
									name="date" style="vertical-align: middle" size="6"
									id="date_from" readonly value="<?php echo $date ?>"
									onClick="return showCal('date_from', 'dd-mm-y');">
          <img
									title="Calendar" style="vertical-align: middle;"
									src="images/calendar0.gif"
									onClick="return showCal('date_from', 'dd-mm-y');" border="0"> <!--<img src="images/Holiday.gif" onClick="holiday();" style="vertical-align:middle; cursor:pointer;">--> 
         
                               <a href="javascript:void(0)" onclick="takeMetoDate('<?php echo $tk_nextday?>')"> <img src="assets/icons/nex.png" width="20"></a>
                            
   
         
       </td>
      
           <td style="vertical-align:middle;padding-left: 8px;" valign="middle" colspan="5" align="center" bgcolor="#e9e9e9"
									><?php if($date_failure=="Yes")
	{?>
             <span style="color:#F00; font-size:18px"><strong>Please Complete the <?php echo date('d-m-Y',strtotime($next_complete))?> Job Status</strong></span>
            <?php	}
	else
	{} ?></td>
         </tr>
    </table>
   
   
     
      <?php if(count($other)>0) { ?>
        <br>
    <table id="customers" border="0"
							style="border-color: #A6107B; border-style: solid; border-width: 1px;"
							align="center" cellpadding="0" cellspacing="0" width="99%"
							class="form_style">
           <tr>
                             <td colspan="12" align="center" bgcolor="#7EB543"><font
									color="#FFFFFF" size="2"><b>Other's Delegated To Me</b> </font></td></tr>                 
  <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
            <td width="20%" align="center"><font color="#a6107b" size="2"><b>Work</b> </font></td>
            <td width="5%"><font color="#a6107b" size="2"><b>Type</b></font><br></td>
            <td width="8%"><font color="#a6107b" size="2"><b>Job Type</b> </font></td>
            <td width="5%"><font color="#a6107b" size="2"><b>Job No</b> </font></td>
            <td width="8%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
            <td width="8%"><strong><font color="#a6107b" size="2">By</font></strong></td>
            <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br></b> </font></td>
            <td width="8%"><font color="#a6107b" size="2"><b>Act Time<br>
              </b> </font></td>
            <td width="5%"><font color="#a6107b" size="2"><b>Target</b> </font></td>
            <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b> </font></td>
            <td width="7%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
            <td width="8%"><font color="#a6107b" size="2"><b>Status</b> </font></td>
          </tr>
          <?php foreach($other as $other_row)
			 {
				?>
          <tr align="center" valign="middle" bgcolor="#e9e9e9"
									style="border-color: #A6107B; border-style: solid; border-width: 1px;">
            <td align="left"><?php echo $other_row['taskname']?></td>
           <td><?php  echo $delegate_array[$other_row['delegate_type']]?></td>
           <?php if($other_row['is_reopen']==1)
		   {
			?>
           <td colspan="3" style="color:#FF0000"><strong><?php echo $other_row['reopen_remarks']?></strong></td>
         
           <?php
		   }
		   else
		   {
		   ?>
            
            
            <td>
			<?php echo "<strong style='color:#FF0000'>".$main_type_array[$other_row['main_type']]."</strong><br>".$job_type_array[$other_row['job_type']]?>
            
            
            
            
            </td>
            <td><?php echo $other_row['job_no']?></td>
            <td><?php echo $other_row['client']?></td>
           <?php
           }?> 
            
            <td><?php echo $other_row['username']?></td>
            <td>Total Est:<?php if($other_row['job_est']=="00:00" || $other_row['job_est']==""){ echo "Nil"; } else {echo $other_row['job_est'];}?><br><input onChange="auto_save('<?php echo $other_row['workreport_id']?>','Est time Updation')"  type="text" name="est_time_hr<?php echo $other_row['workreport_id']?>" maxlength="2" id="est_time_hr<?php echo $other_row['workreport_id']?>"
										class="txt_box" size="2" value="<?php echo $other_row['est_time_hr'];?>"
										style="vertical-align: middle">
              <input onChange="auto_save('<?php echo $other_row['workreport_id']?>','Est time Updation')"  type="text" name="est_time_min<?php echo $other_row['workreport_id']?>" maxlength="2" id="est_time_min<?php echo $other_row['workreport_id']?>"
										class="txt_box" size="2" value="<?php echo $other_row['est_time_min'];?>"
										style="vertical-align: middle"></td>
            <td>Prev Act:<?php if($other_row['prev_act']=="00:00" || $other_row['prev_act']==""){ echo "Nil"; } else {echo $other_row['prev_act'];}?><br><input onChange="auto_save('<?php echo $other_row['workreport_id']?>','Act time Updation')"  type="text" name="act_time_hr<?php echo $other_row['workreport_id']?>" maxlength="2" id="act_time_hr<?php echo $other_row['workreport_id']?>"
										class="txt_box" size="2" value="<?php echo $other_row['act_time_hr'];?>"
										style="vertical-align: middle">
              <input onChange="auto_save('<?php echo $other_row['workreport_id']?>','Act time Updation')"  type="text" name="act_time_min<?php echo $other_row['workreport_id']?>" maxlength="2" id="act_time_min<?php echo $other_row['workreport_id']?>"
										class="txt_box" size="2" value="<?php echo $other_row['act_time_min'];?>"
										style="vertical-align: middle"></td>
            <td style="color:#FF0000; font-size:13px">
            <?php if(($other_row['is_carry']==0)&&($other_row['delegate_type']==4)){?>
			<input onBlur="auto_save('<?php echo $other_row['workreport_id']?>','')" type="text" name="target_date<?php echo $other_row['workreport_id']?>"
										style="vertical-align: middle" size="10" id="target_date<?php echo $other_row['workreport_id']?>"
										value="<?php echo $other_row['target_date']?>"
										onClick="return showCal('target_date<?php echo $other_row['workreport_id']?>', 'dd-mm-y');">
			<?php }
			else
			{ 	echo $other_row['target_date'];}
			
			?>							
			
			
			</strong></td>
            <td><textarea onChange="auto_save('<?php echo $other_row['workreport_id']?>','Job Updation')" name="description<?php echo $other_row['workreport_id']?>" style="overflow: auto;" 										class="txt_box" id="description<?php echo $other_row['workreport_id']?>" cols="15" rows="3"><?php echo $other_row['description'];?></textarea></td>
            <td><input onBlur="auto_save('<?php echo $other_row['workreport_id']?>','')" type="text" name="cf_date<?php echo $other_row['workreport_id']?>"
										style="vertical-align: middle" size="10" id="cf_date<?php echo $other_row['workreport_id']?>"
										value="<?php echo $other_row['cf_date']?>"
										onClick="return showCal('cf_date<?php echo $other_row['workreport_id']?>', 'dd-mm-y');">
                                        </td>
            <td id="td_status_<?php echo $other_row['workreport_id']?>"  <?php if($other_row['status']!=100){?> style="background:#FF0000" <?php } ?>><select onChange="auto_save('<?php echo $other_row['workreport_id']?>','Status Updation')"  id="status<?php echo $other_row['workreport_id']?>" 
										name="status<?php echo $other_row['workreport_id']?>" style="font-weight: 900">
                
                <?php for($i=100;$i>=0;$i=$i-1) {?>
<option	<?php   if($other_row['status']==$i) {?>selected="selected"<?php	                                       			 }?> value="<?php echo $i?>"> <?php echo $i?> % </option>
                <?php }?>
              </select>
              <input type='hidden' value="<?php echo $other_row['is_carry']?>" name="is_carry<?php echo $other_row['workreport_id']?>" id="is_carry<?php echo $other_row['workreport_id']?>">
              
              </td>
          </tr>
          <?php }?>
        </table>
      
     
      <?php }?>
      <?php if(count($carry)>0) { ?>
    <br>
        <table id="customers" border="0"
							style=" border-style: solid; border-width: 1px;"
							align="center" cellpadding="0" cellspacing="0" width="98%"
							class="form_style">
                           <tr>
                             <td colspan="12" align="center" bgcolor="#7EB543"><font
									color="#FFFFFF" size="2"><b>Carry Forwarded Jobs </b><font
									color="#FF0000" size="2"><b>(Instead of Carry Forward We can move forward Job to Store</b>) </font> </font></td></tr>
          <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
            <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
            <td width="10%"><font color="#a6107b" size="2"><b> Type</b></font></td>
            <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
            <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
            <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
              </b></font></td>
            <td width="10%"><font color="#a6107b" size="2"><b>Act Time<br>
              </b></font></td>
            <td width="5%"><font color="#a6107b" size="2"><b>Target</b></font></td>
            <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
            <td width="10%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
            <td width="7%"><b><font color="#a6107b" size="2">Status</font></b></td>
            <td width="7%"><b><font color="#a6107b" size="2">To Store</font></b></td>
          </tr>
          <?php foreach($carry as $carry_row)
			 {
				?>
          <tr align="center" valign="middle" bgcolor="#e9e9e9"
									style=" border-style: solid; border-width: 1px;">
            <td align="left"><?php echo $carry_row['taskname']?></td>
            <td><?php  echo $main_type_array[$carry_row['main_type']]?><br><?php  echo $job_type_array[$carry_row['job_type']]?></td>
            <td><?php echo $carry_row['job_no']?></td>
            <td><?php echo $carry_row['client']?></td>
            <td><?php if($carry_row['job_est']!="00:00" && $carry_row['job_est']!=""){echo "Total Est:".$carry_row['job_est'];}?><br><input onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Est time  Updation')"  type="text" name="est_time_hr<?php echo $carry_row['workreport_id']?>" maxlength="2" id="est_time_hr<?php echo $carry_row['workreport_id']?>"
										class="txt_box" size="1" value="<?php echo $carry_row['est_time_hr'];?>"
										style="vertical-align: middle">
              <input onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Est time  Updation')"  type="text" name="est_time_min<?php echo $carry_row['workreport_id']?>" maxlength="2" id="est_time_min<?php echo $carry_row['workreport_id']?>"
										class="txt_box" size="1" value="<?php echo $carry_row['est_time_min'];?>"
										style="vertical-align: middle"></td>
            <td width="10%"><?php if($carry_row['prev_act']!="00:00" && $carry_row['prev_act']!=""){ echo "Prev Act:".$carry_row['prev_act'];}?><br><input onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Act time Updation')"  type="text" name="act_time_hr<?php echo $carry_row['workreport_id']?>" maxlength="2" id="act_time_hr<?php echo $carry_row['workreport_id']?>"
										class="txt_box" size="1" value="<?php echo $carry_row['act_time_hr'];?>"
										style="vertical-align: middle">
              <input onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Act time Updation')"  type="text" name="act_time_min<?php echo $carry_row['workreport_id']?>" maxlength="2" id="act_time_min<?php echo $carry_row['workreport_id']?>"
										class="txt_box" size="1" value="<?php echo $carry_row['act_time_min'];?>"
										style="vertical-align: middle"></td>
            <td><?php echo $carry_row['target_date']?></td>
            <td><textarea onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Job Updation')" name="description<?php echo $carry_row['workreport_id']?>" style="overflow: auto;"
											class="txt_box" id="description<?php echo $carry_row['workreport_id']?>" cols="30" rows="1"><?php echo $carry_row['description'];?></textarea><a target='_blank'   href='view_cf_status.php?workreport_id=<?php  echo $carry_row['workreport_id']?>'  onClick="javascript:openWindow(this.href,this.target); return false;"><img src='images/view.gif' width='12' height='12' border='0' title='Preview this Invoice' />
</a></td>
            <td><input onBlur="auto_save('<?php echo $carry_row['workreport_id']?>','')" type="text" name="cf_date<?php echo $carry_row['workreport_id']?>"
										style="vertical-align: middle" size="10" id="cf_date<?php echo $carry_row['workreport_id']?>"
										value="<?php echo $carry_row['cf_date']?>"
										onClick="return showCal('cf_date<?php echo $carry_row['workreport_id']?>', 'dd-mm-y');"></td>
            <td id="td_status_<?php echo $carry_row['workreport_id']?>" <?php if($carry_row['status']!=100){?> style="background:#FF0000" <?php } ?>><select  onChange="auto_save('<?php echo $carry_row['workreport_id']?>','Status Updation')"  id="status<?php echo $carry_row['workreport_id']?>" 
										name="status<?php echo $carry_row['workreport_id']?>" style="font-weight: 900">
                <?php echo $option;?>
              </select></td>
      <td><input type='checkbox' onClick="auto_save('<?php echo $carry_row['workreport_id']?>','Status Updation')" value="2" <?php if($carry_row['is_carry']==2) {?>  checked<?php } ?> name="is_carry<?php echo $carry_row['workreport_id']?>" id="is_carry<?php echo $carry_row['workreport_id']?>"></td>
          </tr>
          <script language="javascript">
		$('#status<?php echo $carry_row['workreport_id']?>').val(<?php echo $carry_row['status']?>);
		</script>
          <?php }?>
        </table>
      <br>
      <strong></strong>
   
      <?php }?>
      
    
      


    <br>
      <br>
   
	 
    
 
           
            
    
      
<br>
    <div id="example1"><br>
    </div>
    <div style="width:100%;text-align:center;" ><br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
    </div>
    <script>
var $container = $("#example1");
// var $console = $("#exampleConsole");
Handsontable.cellLookup.renderer.negativeValueRenderer = negativeValueRenderer; //maps function to lookup string
Handsontable.cellLookup.renderer.rateValueRenderer = rateValueRenderer; //maps function to lookup string


var res = <?php echo $test;?>;

  //   res=[{"id":"1","manufacturer":"abcl","year":"2001","price":"100"},{"id":"2","manufacturer":"efgh","year":"2002","price":"101"}];
      var data = [], row;
      for (var i = 0, ilen = res.length; i < ilen; i++) {
        row = [];
		
        row[0] = res[i].taskname;
        row[1] = res[i].is_rework;
        row[2] = res[i].main_type_name;
        row[3] = res[i].job_type_name;
        row[4] = res[i].un_sch;
        row[5] = res[i].job_no;
        row[6] = res[i].client;
        row[7] = res[i].est_time;
        row[9] = res[i].act_time;
        row[10] = res[i].target_date;
        row[11] = res[i].description;
        row[12] = res[i].cf_date;
        row[13] = res[i].job_est;
        row[14] = res[i].status;
        data[res[i].row_id - 1] = row;
      }
var $parent = $container.parent();
$container.handsontable({
  startRows: 8,
  data: data,
   columns: [
    {data: "0", type: 'text'},
	 {data: "1", type: 'checkbox'},
	{data: "2",
      type: 'autocomplete',
      options: {items: 27},
	strict: true,
      allowInvalid: false,
     
      source: [<?php echo $main_type_source?>]
    },
	
	{data: "3",
      type: 'autocomplete',
      options: {items: 27},
	strict: true,
      allowInvalid: false,
     
      source: [<?php echo $source?>]
    },
	
		{data: "4", type: 'checkbox'},
		
		{data: "5",
      type: 'autocomplete',
      options: {items: 27},
			        exactMatch: false,   

      source: [<?php echo $job_no_source?>]
		},

	
	
	{data: "6", type: 'text'},
	
	{data: "7", 
	  
	  type: 'autocomplete',
	    options: {items: 15},
      source: [<?php echo $est_act_source?>]	},
	  
	 {data: "8", type: 'text',readOnly: true}, 
	
	{data: "9", 
	  
	  type: 'autocomplete',
	    options: {items: 12},
      source: [<?php echo $est_act_source?>]	},
  	{data: "10", type: 'date',dateFormat: 'dd-mm-yy'},
	{data: "11", type: 'text'},
	{data: "12", type: 'date',dateFormat: 'dd-mm-yy'},
	{data: "13",
      type: 'autocomplete',
      source: [<?php echo $job_est?>]
    },
	{data: "14", 
	   type: 'autocomplete',
    source: ["100","95","90","85","80","10","70","65","60","55","50","45","40","35","30","25","20","15","10","5","0"]	
	
	}
	  
	
	
	

  ],
  startCols: 3,
   stretchH: 'all',
  rowHeaders: true,
  colHeaders: true,
        afterGetColHeader: function(col, TH) {//**ADDED BY SARANYA ON 3-3-26 FOR REWORK COLUMN HIGHLIGHTING**/
        if (col === 1) { // Target "Rework" column
            // TH.style.backgroundColor = '#FFD700';
            TH.style.color = '#FFD700';
            TH.style.fontWeight = 'bold';
        }
    },//** ENDS ADDED BY SARANYA ON 3-3-26 FOR REWORK COLUMN HIGHLIGHTING **/
  colWidths: [250, 50, 80, 80, 40, 75, 75, 50, 50, 50, 65, 180, 70, 80, 45],
	rowHeight:10,
  colHeaders: ['Task','Rework','Main Type','SubType','UnPlan','Job No.','Client','Est','Rate','Act','Target','Outcome of the Task','CF Date','Total Est','Status%'],
  minSpareCols: 0,
  minSpareRows: 1,
    afterChange: function (change, source) {
    if (source === 'loadData') {
      return; //don't save this change
    }
	
         $.ajax({
        url: "php/save.php?date=<?php echo $date?>",
        dataType: "json",
        type: "POST",
        data: {changes: change}, //contains changed cells' data
        success: function (data) {
		
		if(data.logout=='Yes')
		{
			alert("Your Session has been Expired Please Login Again"); 
			window.location.href='index.php';
		}
		else if(data.traffic=='Yes')
		{
			alert("Network Issue Please Wait"); 
			
		}
		else
		{
			document.getElementById("act_div").innerHTML=data.act_time;        
			document.getElementById("est_div").innerHTML=data.est_time; 
			document.getElementById("routine_est_div").innerHTML=data.routine_est;        
			document.getElementById("routine_act_div").innerHTML=data.routine_act;
			document.getElementById("total_est_div").innerHTML=data.total_est;        
			document.getElementById("total_act_div").innerHTML=data.total_act;  
		
			
			
			
		
		}
        },
		 error: function (xhr, status, errorThrown) {
			 if(status=='error'){
                                alert('Not Saved, Check Your Internet Connection');
                         }


                        }
      });
    },
	 cells: function (row, col, prop) {
    var cellProperties = {};
	
  
    if (col === 14) {
    
      cellProperties.renderer = "negativeValueRenderer"; //uses lookup map
    }
	if (col === 8) {
    


      cellProperties.renderer = "rateValueRenderer"; //uses lookup map



    }
	
    return cellProperties;
  }
 
});



function negativeValueRenderer(instance, td, row, col, prop, value, cellProperties) {
  Handsontable.TextCell.renderer.apply(this, arguments);
  
  if (value!=100) { 
  

  //if row contains negative number
   td.style.background = '#FF0000';
   
 //add class "negative"
  }

 

}

  function rateValueRenderer(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.TextCell.renderer.apply(this, arguments);
        rate = 0;
        var est = act = "";
        if ($container.handsontable('getData')[row])
            est = $container.handsontable('getData')[row][7];
        if ($container.handsontable('getData')[row])
            act = $container.handsontable('getData')[row][9];
        if ((est != "") && (est != 0)) {



            if ((act != "") && (act != null))
            {
                act_array = (act).split(":");
                var hours = parseInt(act_array[0]) + (parseInt(act_array[1]) / 60);
                rate = Math.round(hours * (<?php echo $_SESSION['hourly_rate']; ?>));
            } else if ((est != "") && (est != null))
            {
                est_array = (est).split(":");
                var hours = parseInt(est_array[0]) + (parseInt(est_array[1]) / 60);
                rate = Math.round(hours * (<?php echo $_SESSION['hourly_rate']; ?>));
            }
            value = "";
            est = "";//alert(rate);
            td.innerHTML = rate;
            //add class "negative"
        }



    }



  
  
  </script></td>
  </tr>
  <?php	                                       			 include("includes/footer.php");?>
  </table>
  <?php
}?>

<div class="PopupDiv" id="modal" style="border: 3px solid black; background-color: rgb(153, 153, 255); padding: 25px; font-size: 150%; text-align: center; display: none; position: absolute; visibility: visible; top: 1941.5px; left: 548.5px; z-index: 143;">
       You are Late as per the Office Reporting Time(<?php echo $_SESSION['reporting_time']?>)!!!<br><br>
                <table>
        <tr>
        <td style="font-size:16px;color:#FF0000;font-weight:bold">Are You Late?</td><td><select name="not_punctual" id="not_punctual"><option value=""></option><option value="Yes">Yes</option><option value="No">No</option></select></td>
        </tr>
        <tr>
        <td id="reason_td"><strong>Remarks</strong></td><td><input type="text" id="late_remarks" name="late_remarks"></td>
        </tr>
        <tr>
        <td></td><td> <input   Value="Submit" onClick="return  check_late(this.form)" type="submit"> <input value="Cancel"  onclick="Popup.hide('modal')" type="button"></td>
        </tr>
        
       
       
        </table>
       
    </div>
    
    
    

</form>
<link type="text/css" rel="stylesheet" href="css/jquery-ui-1.8.9.custom/jquery-ui-1.8.9.custom.css" />
<script type="text/javascript" src="jquery-1.4.3.min.js"></script> 
<script type="text/javascript" src="jquery-ui-1.8.13.custom.min.js"></script> 
<style type="text/css">
		/*demo page css*/
		body{ font: 62.5% "Trebuchet MS", sans-serif; }
	</style>

    
    
    <script>
                                                                    
function takeMetoDate(date){
    window.location.href='jobdiary.php?date='+date;
}         
    </script>
    
    
    
  
      
      <br>  
</body>
</html><?php

?>