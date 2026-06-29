<?php	                                       			
session_start();
if(!isset($_SESSION['user_id']))
header("location:index.php");
include("includes/connect.inc.php");
include("includes/functions.php");
include("commonfunc.class.php");
include("includes/access_config.php");


$time_zone =$_SESSION['time_zone'];
ini_set('date.timezone', $time_zone);

$emp_division_id=$_REQUEST['emp_division_id'];
$emp_div_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=2 order by short_name");

$tree_id=$_REQUEST['tree_id'];
if($tree_id>0)
$_REQUEST['select_employee_id']=$tree_id;
//print_r($_POST);
//print_r($_SESSION);

$current_month=date('m');
$current_year=date("Y");

$plan_month = isset($_REQUEST['plan_month'])?$_REQUEST['plan_month']:$current_month;

$report_type = isset($_REQUEST['report_type'])?$_REQUEST['report_type']:"weekly";

$plan_year = isset($_REQUEST['plan_year'])?$_REQUEST['plan_year']:$current_year;


$day = "$plan_year-$plan_month-01";
$previous_month = date("m", strtotime ( '-1 month' , strtotime ($day) )) ;
$previous_year = date("Y", strtotime ( '-1 month' , strtotime ($day) )) ;

$next_month = date("m", strtotime ( '+1 month' , strtotime ($day) )) ;
$next_year = date("Y", strtotime ( '+1 month' , strtotime ($day) )) ;





$user_id = ((isset($_REQUEST['select_employee_id']))&&($_REQUEST['select_employee_id']>0))?$_REQUEST['select_employee_id']:$_SESSION['user_id'];
$full_name_array = get_full_name($user_id);
$full_name = $full_name_array['full_name'];
$is_lock = $full_name_array['is_lock'];


  $select_user_row =get_user_details($user_id);
$audit_employee_head=$select_user_row['division_head'];
$declaration_result= $mysqli->query(" select * from  tbl_monthly_declaration where user_id='$user_id' and decl_month='$plan_month' and 	decl_year='$plan_year'");
$declaration_row= $declaration_result->fetch_assoc();


$work_category_array=array("1"=>"Productive","2"=>"Semi  Productive","3"=>"Supporting");



$current_user_id= $_SESSION['user_id'];

$user_query = $mysqli->query("select  u.username,u.division_head from tbl_users u where u.user_id=$current_user_id");
$user_row = $user_query->fetch_assoc();
$user_name=$user_row['username'];
$my_division_head=$user_row['division_head'];


$user_in= $_SESSION['USER_ACCESS']['MAINMODULE'];//implode(",",($user_array));
echo $access_config[$current_user_id];

if($access_config[$userid]!="")
	$user_in=$user_in.",".$access_config[$userid];

$query ="SELECT u.full_name,user_id,d.short_name as division ,d.id as emp_div_id FROM tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id  where  status='Active' and is_regular=1 and is_erp=1 and  user_id in ($user_in) ";

$query.=" order by d.short_name,u.full_name";




$result= $mysqli->query($query);








$invalid_jobs= "trim(taskname)='Routine Jobs' or trim(taskname)='Routine Job'";

if($report_type=="weekly")
{
$sql = "SELECT  m.*,if(m.work_entry='0000-00-00 00:00:00','',date_format(m.work_entry,'%d-%m-%y  %h:%i:%s %p')) as monthly_entry from  tbl_monthly_plan_details m where user_id=$user_id and plan_month='$plan_month' and plan_year='$plan_year' and taskname!='' order by select_week asc, row_id desc, taskname asc";
}
else if($report_type=="status")
{
$sql = "SELECT  m.*,if(m.work_entry='0000-00-00 00:00:00','',date_format(m.work_entry,'%d-%m-%y  %h:%i:%s %p')) as monthly_entry from  tbl_monthly_plan_details m where user_id=$user_id and plan_month='$plan_month' and plan_year='$plan_year' and taskname!='' and is_current=1 order by status desc,  taskname asc";
}
else if($report_type=="new_cf")
{
$sql = "SELECT  m.*,if(m.work_entry='0000-00-00 00:00:00','',date_format(m.work_entry,'%d-%m-%y  %h:%i:%s %p')) as monthly_entry from  tbl_monthly_plan_details m where user_id=$user_id and plan_month='$plan_month' and plan_year='$plan_year' and taskname!='' order by row_id asc, select_week asc ,taskname asc";
}


$output=$mysqli->query($sql);

	$result_complete = $mysqli->query("SELECT DATE_FORMAT(completion_time,'%d/%m/%Y %h:%i:%s') completion_time FROM tbl_monthly_plan WHERE user_id = '$user_id' AND plan_month = '$plan_month' AND plan_year = '$plan_year'");
	$row = $result_complete->fetch_assoc();
	
	if(!empty($row['completion_time']))
		$status = "<font color='blue'><b>Completed On ".$row['completion_time']."</font></b>";
	else 
		$status = "<font color='blue'><b> Not Completed</font></b>";

$current_userid = $_SESSION['user_id'];
	
	if($_POST['submit_audit']=='Submit' && $_POST['key'] == $_SESSION['key'])
{
	$audited=$_SESSION['user_id'];
	$rating=$_POST['observation'];
	$remarks=$_POST['remarks'];
	
	observation_save($user_id,$remarks,$rating,'monthly',$plan_month,$plan_year,$audited);



if(($current_user_id==2)&&($rating==1 || $rating==2))
{	
if($rating==1)
$rating=8;
if($rating==2)
$rating=9;



	ceo_observation_save($user_id,$remarks,$rating,'monthly',$plan_month,$plan_year,$current_user_id,null,$audit_employee_head);
	
	
}
	
}
function get_full_name($user_id)
{
global $mysqli;
$assigned_query =$mysqli->query("select is_lock,full_name from tbl_users  where user_id=$user_id");
$user_array = $assigned_query->fetch_assoc();
return $user_array;

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
<script>
function validate()
{  
    var a=document.forms['frm']['observation'].value;
	var x=document.getElementById('obs').checked;
	var y=document.getElementById('ncr').checked;
	var z=document.getElementById('remarks').value.trim();
	
	
	if((x || y) && z.length==0)
	{
		alert('Remarks mandatory for this rating');
		return false;
	}
	else
	return true;
	
}




</script>


</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">

<?php	                                       			

include("includes/header.php");

?>

<?php	                                       			

	

$num = 1;

?>


		<form name="frm"  id="frm" method="post">


	

		<tr><td colspan="10" align="center">

	<?php
	$employee_id = $_SESSION['user_id'];
	if(empty($_REQUEST['tree_id']) && !empty($_REQUEST['user_id'])) {
		$employee_id = $_REQUEST['user_id'];
	}
	else if(!empty($_REQUEST['tree_id'])) {
		$employee_id = $_REQUEST['tree_id'];
	}
	if(COMMONFUNC::checkAccess($employee_id)<=0) {
	//	if($il) {

		
		?>
    
	<strong style="color:#F00;font-size:18px" ><br/><br/>Sorry You can't access the details</strong>	
    
    <a href=""><strong>Click here to go to Monthly Plan</strong></a>
    
	<?php
    }
	else
	{
		
		$user_row = get_user_details($user_id);
		$log_date=date('Y-m-d H:i:s');
$user_name = $user_row['username'];

if($user_id!=$current_userid)
{
	$sql_date = $plan_year."-".$plan_month."-01";
  $log_sql= "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,jobdiary_date,access_id) values('$current_userid','$log_date','Monthly Plan','Monthly Plan Audit','Monthly Plan History of  $user_name ','$sql_date','$user_id')";
 
    $mysqli->query($log_sql);
}
		?>	

		<table width="85%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">



		

		<tr>

			<td align="center" colspan="4">&nbsp;</td>

		</tr>

		<tr>

			<td colspan="4"><br></td>

		</tr>

		<tr>

			<td colspan="6" align="center" valign="top">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td colspan="3" align="center" width="8%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;<?php echo date("F",strtotime("2009-$plan_month-1"))." ".$plan_year?> Monthly Plan <?php print $status; ?></font></b></td>
			    <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
			    </tr>
			  <?php	                                       			

$count_row=1;
		


			?>
			  <tr height="20" style="border:#a6107b solid 1px;"><td width="5%" align="center"><a href="monthly_plan_history.php?plan_month=<?php echo $previous_month?>&plan_year=<?php echo $previous_year?>&emp_division_id=<?php echo $emp_division_id?>&select_employee_id=<?php echo $user_id?>&report_type=<?php echo $report_type?>"><img src="images/previous.png" border="0" ></a></td>  
			    <td width="90%" align="center" style="border:#000000 solid 1px; padding-left:1px;"  <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><strong>Division:</strong>
          <select onChange="this.form.submit()" name="emp_division_id" class="txt_box">
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
           
            &nbsp;&nbsp;&nbsp;	      &nbsp;&nbsp;<strong>Name:</strong>
<link href="jquery.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="jquery-latest.js">
</script>
<script language="javascript" type="text/javascript" src="jquery.js"></script> 
<script type="text/javascript">
    $(document).ready(function(){
 
	$("#select_employee_name").autocomplete(gDarr,{max:30,matchContains: true}).result(function(evt,item){
		if(item!=null && item.length==1){
			var tar=item[0].split("-");
                       
			evt.target.value=item[0];
                       
			document.forms[0].select_employee_id.value=$.trim(tar[tar.length-1]); 
                       x=item[0].replace($.trim(tar[tar.length]),"")
                       var newStr = x.substring(0, x.length-(document.forms[0].select_employee_id.value.length)-1);
                       document.forms[0].select_employee_name.value=newStr;
                       
                                           	}
	});
	
});

 var gDarr=[
    <?php
 $user_row=$result->fetch_assoc();
 
 echo '"' . addslashes($user_row['full_name'])  . "-" . $user_row['user_id'] . '"';
 while($user_row=$result->fetch_assoc())
 {
 echo ',"' . addslashes($user_row['full_name']) . "-" . $user_row['user_id'] . '"';
    }
    ?>
 ];

 
         function set_id()
        {
            if(document.forms[0].select_employee_id.value==''&document.forms[0].select_employee_name.value!='')
            {
                var x;
                var y;
                var pos;
                var cl_det;
                var cl_name;
                for (x in gDarr)
                {
                    cl_det=gDarr[x];
                    pos=cl_det.lastIndexOf("-");
                    y=cl_det.length;
                    cl_name=cl_det.substring(0,pos);
                    if(cl_name==document.forms[0].select_employee_name.value)
                    {
                        document.forms[0].select_employee_id.value=(cl_det.substring(pos,y+2)).split("-")[1];
                        break;
                    }
                }
                    
            }
        }
</script>

<input value="<?php echo $full_name?>" class="ac_input" name="select_employee_name" id="select_employee_name" onFocus="focustxt=this;" size="30" autocomplete="off" onChange="document.getElementById('select_employee_id').value='' " onBlur="set_id()"><input name="select_employee_id" id="select_employee_id" value="<?php echo $user_id?>" type="hidden"><input name="tree_id" id="tree_id" type="hidden">

<select  id="plan_month" name="plan_month">

					<?php	                                       			 

					for($i=1;$i<=12;$i++)

					{

					$month=date("F",strtotime("2009-$i-1"));
					$month_num=date("m",strtotime("2009-$i-1"));


					?>					

					<option <?php	                                       			 if($plan_month==$i){?> selected="selected" <?php	                                       			 }?> value="<?php	                                       			 echo $month_num?>"><?php	                                       			 echo $month?></option>

					<?php	                                       			

					}

					?>

					

					</select>

&nbsp;&nbsp;
<select name="plan_year" id="plan_year">
<option <?php if($plan_year==2014) {?> selected <?php }?>  value="2014">2014</option>
<option <?php if($plan_year==2015) {?> selected <?php }?>  value="2015">2015</option>


</select>

&nbsp;&nbsp;&nbsp;
<strong>Type:</strong>
<select name="report_type" id="report_type" style="width: 150px;">
<option <?php if($report_type=="weekly"){?> selected  <?php }?> value="weekly">Weekly</option>
<option <?php if($report_type=="new_cf"){?> selected  <?php }?> value="new_cf">New Jobs/CF Jobs</option>
<option <?php if($report_type=="status"){?> selected  <?php }?>  value="status">Status of Completion</option>


</select>

	      <input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image"></td>
		<td align="center"><a href="monthly_plan_history.php?plan_month=<?php echo $next_month?>&plan_year=<?php echo $next_year?>&emp_division_id=<?php echo $emp_division_id?>&select_employee_id=<?php echo $user_id?>&report_type=<?php echo $report_type?>"><img src="images/next.png" border="0"></a>
                <br>
                </td>
			    <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
			    </tr>       	

			

			

			  

		

			 
			  
		    </table></td>
								



						

					


		    </tr>

		<tr>

			<td colspan="2" align="right">
 <a href="javascript:document.location.href='monthly_plan_history_excel.php?plan_month=<?php echo $plan_month?>&plan_year=<?php echo $plan_year?>&report_type=<?php echo $report_type?>&user_id=<?php echo $user_id?>'" style="font-weight:bold">Monthly Planning Reports<img src="images/page_excel.png"></a>           
            
            </td>

		</tr>

		
		<tr>

			<td colspan="4" align="center"></td>

		</tr>

		<tr>

			<td colspan="4"></td>

		</tr>

				<tr height="30" style="border:#a6107b solid 1px;">


			<td align="center"  style="border:#000000 solid 1px; padding-top:5px; padding-bottom:5px; " valign="top">
          
<?php get_user_details_table($user_id,$my_division_head);?>
 
          
            </td>

</tr>
<?php
if($is_lock>0)
{
$lock_query = $mysqli->query("select l.*,t.type as type_name  from tbl_user_lock l left join tbl_lock_types t on t.id=l.type 
where locked_user=".$user_id."  order by id desc limit 0,1");	
$lock_row = $lock_query->fetch_assoc();
$lock_remarks= $lock_row['remarks'];
$lock_id= $lock_row['id'];
$type_name= $lock_row['type_name'];

	
	?>

	<tr height="30" style="border:#a6107b solid 1px;">
				<td align="center"  style="border:#000000 solid 1px; padding-left:5px; color:#FFFFFF" width="6%" bgcolor="#FF0000" ><strong>Account Locked Due to <?php echo $type_name?></strong>
				&nbsp;&nbsp;&nbsp;&nbsp;<strong>Remarks:<?php echo $lock_remarks; ?></strong></td>
</tr>
<?php }?>
	<tr height="30" style="border:#a6107b solid 1px;">
<td colspan="2" align="center">
<?php observation('monthly',$user_id,$plan_month,$plan_year);
?><br>
</td>
</tr><tr height="30" style="border:#a6107b solid 1px">
  <td colspan="12" valign="middle">
<?php
   thisMonthaudited($user_id,$plan_month,$plan_year);
?></td></tr>

	<tr height="30" style="border:#a6107b solid 1px;">
    
    

			<td  colspan="2" style="border:#000000 solid 1px; padding-top:5px" align="center"  valign="top">
            
            
            <table width="99%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td colspan="8" align="center"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Self Declaration</font></b></td>
			   
			   
			
			   
			    </tr>
                	
            
			 
            
            
              <tr  style="border:#a6107b solid 1px;">
                <td width="15%" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<strong>Minimum Working days</strong></td>
                <td width="5%" align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['working_days'];?></strong></td>
                <td width="10%" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong>Monthly Targets</strong></td>
                <td width="5%" align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['targets'];?></strong></td>
                <td width="20%" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong>Daily Review</strong></td>
                <td width="5%" align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['daily_audit'];?></strong></td>
                <td width="15%" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong>Training</strong></td>
                <td width="5%" align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['training'];?></strong></td>
              </tr>
              <tr style="border:#a6107b solid 1px;">
              
       <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<?php echo $declaration_row['working_days_rmks']?></td> 
       
       <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;<?php echo $declaration_row['targets_rmks']?></td> 
       <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<?php echo $declaration_row['daily_audit_rmks']?></td> 
       <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;<?php echo $declaration_row['training_rmks']?></td> 
       </tr>
                        <tr style="border:#a6107b solid 1px;">
   
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong>Minimum working hrs</strong></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['working_hrs'];?></strong></td>
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong>Zero NCR</strong><a href="javascript:click_tree(10)"></a></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['ncr'];?></strong></td>
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">                  <strong>Revenue Generation Contribution</strong></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['revenue'];?></strong></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>&nbsp;</td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>&nbsp;</td>
              </tr>
              <tr height="20" style="border:#a6107b solid 1px;">
              
               <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;<?php echo $declaration_row['working_hrs_rmks']?></td>
               
               <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<?php echo $declaration_row['ncr_rmks']?></td>
               
               <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;<?php echo $declaration_row['revenue_rmks']?></td>
               
               <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;</td>
               
               </tr>
                <tr height="20" style="border:#a6107b solid 1px;">
      
              <td align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong>Punctuality</strong></td>
              <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" bgcolor="#e9e9e9"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['punctuality'];?></strong></td>
              <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="25%" bgcolor="#f2f2f2"><strong>Efficiency software usage</strong></td>
              <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" bgcolor="#f2f2f2"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['efficiency'];?></strong></td>
              <td align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong>Additional Responsibility</strong></td>
              <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9"><strong  style="color:#FF0000; font-size:13px"><?php echo $declaration_row['additional'];?></strong></td>
              <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;</td>
              <td align="center" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;</td>
              </tr>
              
                <tr height="20" style="border:#a6107b solid 1px;">
   <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<?php echo $declaration_row['punctuality_rmks']?></td>
   
   <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;<?php echo $declaration_row['efficiency_rmks']?></td>
   
   <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#e9e9e9">&nbsp;<?php echo $declaration_row['additional_rmks']?></td>
   
   <td colspan="2" align="left" style="border:#000000 solid 1px; padding-left:5px;" bgcolor="#f2f2f2">&nbsp;</td>
   
   
      
              <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
            </tr>
			  <?php	                                       			 $count_row++;

			

			        			


		?>

			

			  

		

		

	
			 
			  <tr>
			    <td colspan="10">&nbsp;</td>
			    </tr>
		    </table>
            
            
            
            <?php if($report_type=="weekly")
			{?>
            
            <table width="99%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td align="center" width="5%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">No</font></b></td>
			    <td align="center" width="30%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Plan</font></b></td>
			    <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Entry Date</font></b></td>
			    <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></b></td>
			    <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Week</font></b></td>
			    <td align="center"  width="40%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Decription</font></b></td>
			    <td  align="center"  width="5%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">CF</font></b></td>
			    <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">CF Week</font></b></td>
			    <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Status</font></b></td>
			    <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
			    </tr>
                	
            
			  <?php	                                       			

$count_row=1;
$flag=0;
$i=1;

$current=0;

			while($result = $output->fetch_assoc()) { 
			if($result['select_week']!=$current)
			
			{?>
            
            
            <tr height="20" style="border:#a6107b solid 1px;">
			    <td colspan="9" align="center" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><strong  style="color:#FF0000; font-size:15px"> <?php        			 echo generate_week($result['select_week']); ?></strong></td>
			   
			    
			    </tr>
            <?php }?>
            
            
            
            
			  <tr height="20" style="border:#a6107b solid 1px;">
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $i++; ?>
			    </font></td>
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $result['taskname']; ?>
			      </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $result['monthly_entry']; ?>
			    </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>>
	<?php	                                    			if($result['row_id']>0) { echo "<font face='Verdana, Arial, Helvetica, sans-serif'  style='color:#FF0000; font-size:14px; font-weight:bold'>New</font>";} else {echo "<font face='Verdana, Arial, Helvetica, sans-serif' style='font-size:14px; font-weight:bold'>CF</font>"; }?>
			    </td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			     <strong> <?php        			 echo generate_week($result['select_week']); ?></strong>
			    </font></td>
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			  echo $result['description'] ?>
			      </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
			      <?php	
				  if($result['carry_forward']>0)      
				  echo date("F", mktime(0, 0, 0, $result['carry_forward'], 10));
				   ?>
			   </strong> </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			     <strong> <?php        			 echo generate_week($result['cf_week']); ?></strong>
			    </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			  echo $result['status'] ?>
			      </font></td>
			    <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
			    </tr>
			  <?php	                                       			 $count_row++;

			

			        			

		$current	=$result['select_week'];	

		

			

			  

		

		

		 } ?>
			 
			  <tr>
			    <td colspan="11">&nbsp;</td>
			    </tr>
		    </table>
            <?php } 
			else if($report_type=="status")
			{
			
			
			?>
            <table width="99%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
			  
			 
			
			  <tr height="30" style="border:#a6107b solid 1px;">
			    <td align="center" width="5%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">No</font></b></td>
			    <td align="center" width="30%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Plan</font></b></td>
			    <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Entry Date</font></b></td>
			    <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Week</font></b></td>
			    <td align="center"  width="40%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Decription</font></b></td>
			    <td  align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">CF</font></b></td>
			    <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">CF Week</font></b></td>
			    <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Status</font></b></td>
			    <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
			    </tr>
                	
            
			  <?php	                                       			

$count_row=1;
$flag=0;
$i=1;

$current=0;

			while($result = $output->fetch_assoc()) { 
			if($result['select_week']!=$current)
			
			{?>
            
            <?php }?>
            
            
            
            
			  <tr height="20" style="border:#a6107b solid 1px;">
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="5%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $i++; ?>
			    </font></td>
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="30%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $result['taskname']; ?>
			      </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			 echo $result['monthly_entry']; ?>
			    </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <strong> <?php        			 echo generate_week($result['select_week']); ?></strong>
			      </font></td>
			    <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="40%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			  echo $result['description'] ?>
			      </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
			      <?php	
				  if($result['carry_forward']>0)      
				  echo date("F", mktime(0, 0, 0, $result['carry_forward'], 10));
				   ?>
			   </strong> </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			     <strong> <?php        			 echo generate_week($result['cf_week']); ?></strong>
			    </font></td>
			    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
			      <?php	                                       			  echo $result['status'] ?>
			      </font></td>
			    <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
			    </tr>
			  <?php	                                       			 $count_row++;

			

			        			

		$current	=$result['select_week'];	

		

			

			  

		

		

		 } ?>
			 
			  <tr>
			    <td colspan="10">&nbsp;</td>
			    </tr>
		    </table>
            
            <?php
			}
			
			else if($report_type="new_cf")
			{
			
			
			?>
            <table width="99%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
              <tr height="30" style="border:#a6107b solid 1px;">
                <td align="center" width="5%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">No</font></b></td>
                <td align="center" width="30%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Plan</font></b></td>
                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Entry Date</font></b></td>
                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font></b></td>
                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Week</font></b></td>
                <td align="center"  width="40%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Decription</font></b></td>
                <td  align="center"  width="5%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">CF</font></b></td>
                <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">CF Week</font></b></td>
                <td  align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Status</font></b></td>
                <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
              </tr>
              <?php	                                       			

$count_row=1;
$flag=0;
$i=1;

$row_id=0;

			while($result = $output->fetch_assoc()) { 
			
			if(($result['row_id']==0)&&($row_id==-0))
			
			{
				$row_id=1;
				
				?>
              <tr height="20" style="border:#a6107b solid 1px;">
                <td colspan="9" align="center" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><strong  style="color:#FF0000; font-size:15px">
                  Carry Forwarded Jobs
                </strong></td>
              </tr>
              <?php }
			  else if(($result['row_id']>0)&&($row_id==0 || $row_id==1))
			
			{
				$row_id=2;
				
				?>
              <tr height="20" style="border:#a6107b solid 1px;">
                <td colspan="9" align="center" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><strong  style="color:#FF0000; font-size:15px">
                 New Jobs
                </strong></td>
              </tr>
              <?php }
			  
			  
			  ?>
              <tr height="20" style="border:#a6107b solid 1px;">
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
                  <?php	                                       			 echo $i++; ?>
                </font></td>
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="198" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
                  <?php	                                       			 echo $result['taskname']; ?>
                </font></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
                  <?php	                                       			 echo $result['monthly_entry']; ?>
                </font></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><?php	                                    			if($result['row_id']>0) { echo "<font face='Verdana, Arial, Helvetica, sans-serif'  style='color:#FF0000; font-size:14px; font-weight:bold'>New</font>";} else {echo "<font face='Verdana, Arial, Helvetica, sans-serif' style='font-size:14px; font-weight:bold'>CF</font>"; }?></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"> <strong>
                  <?php        			 echo generate_week($result['select_week']); ?>
                </strong> </font></td>
                <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
                  <?php	                                       			  echo $result['description'] ?>
                </font></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
                  <?php	
				  if($result['carry_forward']>0)      
				  echo date("F", mktime(0, 0, 0, $result['carry_forward'], 10));
				   ?>
                </strong></font></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif"> <strong>
                  <?php        			 echo generate_week($result['cf_week']); ?>
                </strong> </font></td>
                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><font face="Verdana, Arial, Helvetica, sans-serif">
                  <?php	                                       			  echo $result['status'] ?>
                </font></td>
                <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php	                                       			 if($count_row%2 == 0) { ?> bgcolor="#e9e9e9"<?php	                                       			 } else {?>bgcolor="#f2f2f2"<?php	                                       			 } ?>><a href="mail.php?user_id=<?php	                                       			 echo $q['user_id']?>&date_from=<?php	                                       			 echo $datefrom?>&date_to=<?php	                                       			 echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->
              </tr>
              <?php	                                       			 $count_row++;

			

			        			

		$current	=$result['select_week'];	

		

			

			  

		

		

		 } ?>
         
         
              <tr>
                <td colspan="11">&nbsp;</td>
              </tr>
            </table>            <?php
			}?>
            </td>

	

</tr></table>
<?php

	 rating_table($user_id,$current_user_id);
	  

  $_SESSION['key'] = mt_rand(1, 1000);


?>

 <input type="hidden" name="key" value="<?php echo $_SESSION['key'] ?>">
 </form>
 <table style="border-collapse: collapse;" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
<?php	                                       			          include("includes/footer.php");?>
</table>
<?php
}?>

</td></tr></table>

<?php	                                       			


function generate_week($week)
{

if($week>0)
{
switch ($week) {
        case 1:  return $week.'st Week';				
        case 2:  return $week.'nd Week';
        case 3:  return $week.'rd Week';
        default: return $week.'th Week';
 	   }
	   
	
	
}

}


?>
<script language="javascript">
function click_tree(user_id)
{

	document.getElementById('tree_id').value=user_id;
document.forms['frm'].submit();
}
</script>

</body>
</html>