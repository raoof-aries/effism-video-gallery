<?php
session_start();
//$_SESSION['user_id']=589;
//ini_set('display_errors',1);
if (!isset($_SESSION['user_id']))
    header("location:index.php");
include("includes/connect.inc.php");
include('pagination_class.php');


$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone',$time_zone);

$current_month = date("n");
$current_year = date("Y");
$userid = $_SESSION['user_id'];




$select_year = isset($_REQUEST['select_year']) ? $_REQUEST['select_year'] : $current_year;
$division_id = isset($_REQUEST['division_id']) ? $_REQUEST['division_id'] : 0;
$sub_division_id = isset($_REQUEST['sub_division_id']) ? implode(',',$_REQUEST['sub_division_id']) : 0;

$location_id = isset($_REQUEST['location_id']) ? $_REQUEST['location_id'] : 0;
$employee_id = isset($_REQUEST['employee_id']) ? $_REQUEST['employee_id'] : 0;

$sub = $_REQUEST['sub'];
$company_id = isset($_REQUEST['company_id']) ? implode(',',$_REQUEST['company_id']) : 0;
$job_no = isset($_REQUEST['job_no']) ? implode(',',$_REQUEST['job_no']) : 0;
$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;
$client = isset($_REQUEST['client']) ? $_REQUEST['client'] : 0;


$div_result = $mysqli->query("SELECT * FROM  tbl_dimensions where dimension_type=2 and is_active=1");
$comp_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=1 and is_active=1");
$job_result = $mysqli->query("SELECT *  FROM tbl_job where 1");
$client_result = $mysqli->query("SELECT *  FROM tbl_job where 1");
$group_result = $mysqli->query("SELECT * FROM  tbl_emp_group");
$location_result = $mysqli->query("SELECT * FROM tbl_emp_workplace");




$previous_year = $select_year-1;
$next_year = $select_year+1;
$username = $_SESSION['user_name'];
//$mysqli->query("DROP TEMPORARY TABLE users;");


$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '1'";

$companies = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '2'";

$divisions = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '3' AND parent_id=3 ";

$sub_divisions = $mysqli->query($sql);

$sql = "SELECT emp_group_id id,emp_group_name short_name FROM tbl_emp_group";

$groups = $mysqli->query($sql);

$sql = "SELECT id,work_place,date_mode FROM tbl_emp_workplace ORDER BY sort_order";

$locations = $mysqli->query($sql);

$locations1 = $mysqli->query($sql);

while ($row_location = $locations1->fetch_assoc()) {

    $arr_mode[$row_location['id']] = $row_location['date_mode'];
}




/*$log_date = date('Y-m-d H:i:s');


$log_sql = "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,jobdiary_date) values('$userid','$log_date','Time Sheet','View Time Sheet','','')";

$mysqli->query($log_sql);*/


if(empty($_REQUEST['to_date']))  $_REQUEST['to_date'] = date('Y-m-d', strtotime('last day of this month')) ;
if(empty($_REQUEST['from_date'])) $_REQUEST['from_date'] = date('Y-m-d', strtotime('first day of this month')) ;

if(empty($division_id)) $division_id=3;

$current_day = date("d");

//$mysqli->query("DROP TEMPORARY TABLE users;");
$sql1 = " select j.id job_id, j.date, j.job_no, j.description, d1.short_name company, d2.short_name division, d3.short_name subdivision,  j.job_value, j.client
from tbl_job j
LEFT JOIN tbl_dimensions d1
ON j.company_id = d1.id
LEFT JOIN tbl_dimensions d2
ON j.division_id = d2.id
LEFT JOIN tbl_dimensions d3
ON j.sub_division_id = d3.id
WHERE 1 ";

//$sql1 = "SELECT * FROM users u WHERE u.status='Active'  AND (u.exclude IS NULL OR u.exclude='I')";

if (!empty($_REQUEST['from_date']))
    $sql1.=" and j.date >='".$_REQUEST['from_date']."' ";

if (!empty($_REQUEST['to_date']))
    $sql1.=" and j.date <='".$_REQUEST['to_date']."' ";

if ($company_id > 0)
    $sql1.=" and j.company_id in($company_id) ";


if ($division_id > 0)
    $sql1.=" and j.division_id=$division_id";
if ($sub_division_id > 0)
    $sql1.=" and j.sub_division_id IN($sub_division_id)";
if(!empty($job_no)) {
	 $sql1.=" and j.id IN($job_no)";
}

if(!empty($client)) {
	 $sql1.=" and j.client LIKE '%$client%'";
}

  $sql1.=" ORDER BY j.date ";
//print $sql_temp.="";

 /*$mysqli->query($sql1);
print $sql1;*/
//exit;

 $mysql1 = $mysqli->query($sql1);
$count_user = $mysql1->num_rows;
//print "sss".$count_user;
//$mysqli->query("DROP TEMPORARY TABLE users;");
//echo $sql1; 

if (isset($_GET['starting']) && !isset($_REQUEST['search'])) {
    $starting = $_GET['starting'];
} else {
    $starting = 0;
}
$recpage = 30; //number of records per page


$obj = new pagination_class($sql1,$starting,$recpage,$count_user);
$output = $obj->result;

$query = " SELECT u.level,u.full_name short_name,u.user_id,d.short_name as division ,d.id as emp_div_id 
FROM tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id 
$sql_left_join
where  $sql_where AND u.status='Active' ";


$query.=" order by d.short_name,u.full_name";

$result_users = $mysqli->query($query);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

    <head>

        <link rel="icon" href="images/favicon.ico" type="image/x-icon">

        <title>Online Job Diary - Aries Marine</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link href="css/style.css" rel="stylesheet" type="text/css">

        <link href="css/calendarstyle.css" rel="stylesheet" type="text/css">


 <link rel='stylesheet' type='text/css' href='js/chosen/1.1.0/chosen.css'/>

        <script type="text/javascript" src="javascript/calendar.js"></script>

        <script type="text/javascript" src="javascript/js.js"></script>

        <script type="text/javascript" src="javascript/date-functions.js"></script>
		<script src="js/jquery.js" type="text/javascript"></script>

<script src="js/jquery.alerts.js" type="text/javascript"></script>
<script src="js/jquery.ui.draggable.js" type="text/javascript"></script>

<link href="css/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" /><?php

if(isset($_REQUEST['add_so_number']) && !empty($_REQUEST['so_number'])) {
		 $sql = "SELECT id FROM tbl_so_number
		WHERE sales_order_no='".$_REQUEST['so_number']."'
		AND job_id = ".$_REQUEST['job_id'];
		$result = $mysqli->query($sql);
		$row = $result->fetch_assoc();
		if(!empty($row['id'])) {
			//exit;
			print '-';
			?><script>jAlert("<?php print $_REQUEST['so_number']?> is already exist.", 'Alert Dialog');</script><?php
		}
		else {
	/* sales order */
		$sql_ins = "INSERT INTO tbl_so_number (job_id, sales_order_no, added_date)
		VALUES('".$_REQUEST['job_id']."','".$_REQUEST['so_number']."','".date('Y-m-d')."')";
		$mysqli->query($sql_ins);
		header('location:job_list.php');
		}
}
if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='DELETE_JOB_NO') {
	$sql = "SELECT COUNT(id) no FROM tbl_so_number WHERE job_id=".$_REQUEST['job_id'];
	$so_no = $mysqli->query($sql);
	$row_no = $so_no->fetch_assoc();
	print $row_no['no'];
	if($row_no['no']>0) {
		print '-';
			?><script>jAlert("Sorry!SO Number(s) are exist under this job no.", 'Alert Dialog');</script><?php
	
	}
	else {
		$sql_del = "DELETE FROM tbl_job WHERE id=".$_REQUEST['job_id'];
		$mysqli->query($sql_del);
		header('location:job_list.php');
	}
}

    ?></head>

    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

        <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">

            <?php
            include("includes/header.php");
            ?>

            <?php
            $num = $starting;
            ?>

            <tr>

                <td>&nbsp;



                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">

                        <tr><td colspan="8">



                                <table width="95%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">



                                    <form action="job_list.php" name="frm" method="post">

                                        <tr>

                                            <td align="center" colspan="10">&nbsp;</td>

                                        </tr>

                                        <tr>

                                            <td colspan="20">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">







                                                    <tr height="30" style="border:#a6107b solid 1px;">

                                                        <td colspan="3" align="center" width="25%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;JOB  LIST</font></b>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jobdiary_buttons" style="padding:4px;float:left" href="rp_menu.php">BACK</a></td>



                                                    </tr>

                                                    <?php
                                                    $count_row = 1;
                                                    ?>

                                                    <tr height="20" style="border:#a6107b solid 1px;">

                                                        <td  width="100%" align="center" style="border:#000000 solid 1px; padding-left:5px"  <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>>

                                                            <strong> </strong><?php
                                                            $month_names = array("01" => "January","02" => "February","03" => "March","04" => "April","05" => "May","06" => "June","07" => "July","08" => "August","09" => "September","10" => "October","11" => "November","12" => "December");

                                                            $year = date('Y');

                                                            $month = date('m');
                                                            $arrYear = range(2014,2030);
                                                            ?>
                                                
 <!-- <label style="color:#F00" for="sub"><strong><font style="font-size:14px">View My Tree</font></strong></label>
                                                           
                                                            <input onClick='this.form.submit()' type="checkbox" name="sub" value="1"
<?php if ($sub == 1) {?> checked<?php }?>>
                                                            <input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image"> -->

                                                        </td>

                                                       
                                                    </tr>



                                                    <tr height="40" style="border:#a6107b solid 1px;">



                                                        <td align="center" colspan="3" bgcolor="#e9e9e9">
															
															 <strong>From:&nbsp;</strong>
															 <input type="date" name="from_date" id="from_date"  value = "<?php print($_REQUEST['from_date'])?>" onchange='this.form.submit()'/>
															 <strong>To:&nbsp;</strong>
															 <input type="date" name="to_date" id="to_date"  value = "<?php print($_REQUEST['to_date'])?>" onchange='this.form.submit()'/>
                                                            <strong>Company:&nbsp;</strong>

                                                            <select name='company_id[]' id='company_id' multiple class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Company</option>

                                                                <?php
                                                                while ($row = $companies->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if (in_array($row['id'],$_REQUEST['company_id'])) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;

                                                            




                                                            <strong>Sub Division:</strong>

                                                            <select name='sub_division_id[]' multiple id='sub_division_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Subdivision</option>

                                                                <?php
                                                                while ($row = $sub_divisions->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if (in_array($row['id'] ,$_REQUEST['sub_division_id'])) {?> selected<?php }?>><?php print($row['short_name'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;
															 <strong>Job No:</strong>

                                                            <select name='job_no[]' multiple id='job_no' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Job No</option>

                                                                <?php
                                                                while ($row = $job_result->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['id'])?>' <?php if (in_array($row['id'] ,$_REQUEST['job_no'])) {?> selected<?php }?>><?php print($row['job_no'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;
<strong>Client:</strong>

                                                            <select name='client' multiple id='client' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                                <option value="0">Select Client</option>

                                                                <?php
                                                                while ($row = $client_result->fetch_assoc()) {
                                                                    ?><option value='<?php print($row['client'])?>' <?php if ($row['client'] ==$_REQUEST['client']) {?> selected<?php }?>><?php print($row['client'])?></option><?php
                                                                }
                                                                ?>

                                                            </select>&nbsp;



<a class="jobdiary_buttons" style="padding:4px;" href="add_job.php">ADD JOB </a>
                                                           
                                                         
                                                           </td>



                                                                                                <!--<td align="center" style="border:#000000 solid 1px;" width="4%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><a href="mail.php?user_id=<?php echo $q['user_id']?>&date_from=<?php echo $datefrom?>&date_to=<?php echo $dateto?>"><img border="0" src="images/email.png"></a></td>-->

                                                    </tr>

















                                                    <tr>
                                                     <td  align="right" colspan="15"><font size="3" style="background-color:#0099FF"></font></td>

                                                      



                                                    </tr>

                                                </table>




                                            </td>

                                        </tr>

                                    </form>

                                    <tr>

                                        <td style="border:#a6107b solid 1px;" colspan="15" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors;?></b></font></td>

                                    </tr>




                                    <tr height="30" style="border:#a6107b solid 1px;">


                                        <td width="5%" style="border:#000000 solid 1px;" align="center" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;No:</font></b></td>

                                        <td width="5%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Date</font></b></td>

										
										 <td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Job No</font></b></td>

                                        <td align="center" width="20%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Description</font></b></td>


                                        <td align="center" width="15%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Client</font></b></td>
										
										<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Company</font></b></td>
										<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Division</font></b></td>
										<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Sub Div</font></b></td>
                                      
										<td align="center" width="2%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;SONo</font></b></td>
										<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Job Value(AED)</font></b></td>
										<td align="center" width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;</font></b></td>
										
                        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->

                                    </tr>

                                    <?php

//	$result4 =mysql_fetch_assoc($mysql4);

                                    $count_row = 1;


                                    while ($result = $output->fetch_assoc()) {



                                        /* echo "<pre>";







                                          print_r($result);

                                          //print_r($result4);

                                          echo "</pre>";

                                         */



                                        $month = date('n',strtotime($result['date_created']));
                                        $year = date('Y',strtotime($result['date_created']));




                                        //echo $result4['user_id'],$q['user_id']."<br>";
                                        ?>



                                        <tr height="20" style="border:#a6107b solid 1px;">



                                            <td width="5%" style="border:#000000 solid 1px;" align="center" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $num = $num + 1;?></b></font></td>


                                            <td style="border:#000000 solid 1px; padding-left:5px;" width="8%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><?php echo $result['date'];?></font></td>

                                           
                                            <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="8%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['job_no'];?></b></font></td>

											 <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="15%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['description'];?></b></font></td>
											 <td align="left" style="border:#000000 solid 1px; padding-left:5px;" width="15%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['client'];?></b></font></td>
											<td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['company'];?></b></font></td>
														 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="5%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['division'];?></b></font></td>
													 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="5%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['subdivision'];?></b></font></td>
													 <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="5%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <a onClick="javascript:openWindow('add_so_number.php?job_id=<?php echo $result['job_id'] ?>')"><img src="images/add-entry.png" height="10" width="10"/></a></b></font></td>
													 <td align="right" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><b>
                                                        <?php echo $result['job_value'];?></b></font></td>
												<td align="right" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><a href="add_job.php?job_id=<?php echo $result['job_id'];?>" title="Edit Job" >
                                                        <img src="images/b_edit.png" alt="Edit Job"/></a></font></td>
														<?php if(false) {?>
												<td align="right" style="border:#000000 solid 1px; padding-left:5px;" width="10%" <?php if ($count_row % 2 == 0) {?> bgcolor="#e9e9e9"<?php } else {?>bgcolor="#f2f2f2"<?php }?>><font face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:deleteJob(<?php echo $result['job_id'];?>)" title="Delete Job" >
                                                        <img src="images/b_drop.png" alt="Delete Job"/></a></font></td><?php 
											 } ?>
											
                                        </tr>

                                        <?php
                                        $count_row++;
                                    }
                                    ?>
                                    <tr>

                                        <td style="border:#a6107b solid 1px;" colspan="15" align="center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors;?></b></font></td>

                                    </tr>

                                    <tr>

                                        <td colspan="10">&nbsp;</td>

                                    </tr>

                                </table>



                            </td>

                        </tr>



                        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
                        <?php include("includes/footer.php");
                        ?>


                    </table>

                    <script>
					function deleteJob(job_id) {
						jConfirm('Are you sure want to delete this?', 'Confirmation Dialog', function(r) {
							if(r==true) {
								location.href="job_list.php?job_id="+job_id+"&doAction=DELETE_JOB_NO";
							}
							//jAlert('Confirmed: ' + r, 'Confirmation Results');
						});
					}
					
						function display_popup(job_id)

                            {

                                document.getElementById('job_id').value = job_id;
                                //document.getElementById('comp_status').value='0';//To make the select box selected with first value 
                                Popup.showModal('modal1');

                            }

                        function pagination(page)
                        {
                            window.location = "job_list.php?&starting=" + page + "&select_year=<?php echo $select_year?>" + "&company_id=<?php echo $company_id?>" + "&division_id=<?php echo $division_id?>" +"&sub_division_id=<?php echo $sub_division_id?>" + "&location_id=<?php echo $location_id?>" + "&group_id=<?php echo $group_id?>";
                        }

						function openWindow(url, title)

                {

                    left = (screen.width - 900) / 2;

                    var top = (screen.height - 500) / 2;



                    var href;



                    if (typeof (url) == 'string')
                        href = url;



                    else
                        href = url.href;

                    if (window.wind && !wind.closed)

                    {

                        wind.close();

                        wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

                    } else {

                        wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

                    }



                    wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

                }



                    </script>
                    
<script type="text/javascript" src="js/chosen/1.7.0/chosen.jquery.js"></script>

                                <script type="text/javascript">

                                    var config = {'.chosen-select': {},
                                        '.chosen-select-deselect': {allow_single_deselect: true},
                                        '.chosen-select-no-single': {disable_search_threshold: 10},
                                        '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
                                        '.chosen-select-width': {width: "95%"}

                                    }

                                    for (var selector in config) {

                                        $(selector).chosen(config[selector]);





                                    }





                                    ;









                                </script>



                    </body>

                    </html>
					<form method="post" action="" >
					<div class="PopupDiv" id="modal1" style="border: 3px solid black; background-color: rgb(204, 210, 219); padding: 25px; font-size: 150%; text-align: center; display: none; position: absolute; visibility: visible; top: 1941.5px; left: 548.5px; z-index: -1;">

                            Add SO Number<br><br>

                            <table>


                                <tr>

                                    <td><strong>So Number</strong></td><td><input type="text" id="so_number" name="so_number" value="" /></td>

                                </tr>

                                <tr>

                                    <td></td><td> <input value="Add SO Number" name="add_so_number" type="submit" > <input value="Cancel"  onclick="Popup.hide('modal1')" type="button"></td>

                                </tr>

                            </table>

                        </div>
						<input type="hidden" name="job_id" id="job_id" value=""/>
					</form>

