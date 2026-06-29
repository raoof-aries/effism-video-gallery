<?php
session_start();

if (!isset($_SESSION['user_id']))
    header("location:index.php");

include("includes/connect.inc.php");

include('pagination_class.php');

include("commonfunc.class.php");

include("includes/functions.php");

$time_zone = $_SESSION['time_zone'];

ini_set('date.timezone', $time_zone);


$dimension_array = array("0" => "");

$dimension_result = $mysqli->query("SELECT * FROM tbl_dimensions");

while ($dimension_row = $dimension_result->fetch_assoc()) {

    $id = $dimension_row['id'];

    $dimension_name = $dimension_row['short_name'];

    $dimension_array[$id] = $dimension_name;
}


$select_employee_id = isset($_REQUEST['select_employee_id']) ? $_REQUEST['select_employee_id'] : 0; 
$search_by = isset($_REQUEST['search_by']) ? $_REQUEST['search_by'] : 0;

$emp_company_id = isset($_REQUEST['emp_company_id']) ? $_REQUEST['emp_company_id'] : 0;

$emp_division_id = isset($_REQUEST['emp_division_id']) ? $_REQUEST['emp_division_id'] : 0;

$emp_subdivision_id = isset($_REQUEST['emp_subdivision_id']) ? $_REQUEST['emp_subdivision_id'] : 0;

$group_id = isset($_REQUEST['group_id']) ? $_REQUEST['group_id'] : 0;

$location_id = isset($_REQUEST['location_id']) ? $_REQUEST['location_id'] : 0;
  $view_filter = isset($_REQUEST['view_filter']) ? $_REQUEST['view_filter'] : 0;

$work_category = isset($_REQUEST['work_category']) ? $_REQUEST['work_category'] : 0;

if ($view_filter == 0) {
    $current_user_id = $_SESSION['user_id'];
    $selected_groups = $mysqli->query("select f.*,count(filter_id) as defCount from tbl_filter f  "
            . " where f.created_by= $current_user_id and filter_status=1 and is_default=1");
    $grp_row = $selected_groups->fetch_assoc();
    $defCount = $grp_row['defCount'];
    if ($defCount == 1)
        $view_filter = $grp_row['filter_id'];
}

$emp_div_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=2 and is_active=1 order by short_name");



if ($emp_division_id > 0) {

    $sql_sel = "SELECT is_tree FROM tbl_dimensions WHERE id=" . $emp_division_id;

    $result = $mysqli->query($sql_sel);

    $row = $result->fetch_assoc();
}

$sql_tree = "";

if (!empty($row['is_tree'])) {

    $sql_tree = " AND parent_id = $emp_division_id";
}

$emp_subdiv_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=3 and is_active=1 $sql_tree order by short_name");

$emp_comp_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=1 and is_active=1 order by short_name");

$group_result = $mysqli->query("SELECT * FROM  tbl_emp_group");

$location_result = $mysqli->query("SELECT * FROM  tbl_emp_workplace");


$date = (isset($_REQUEST['date'])) ? $_REQUEST['date'] : date("d-m-Y");

$sql_date = date("Y-m-d", strtotime($date)); 
$username = $_SESSION['user_name'];

$userid = $_SESSION['user_id'];

$current_user_id = $_SESSION['user_id'];

$log_date = date('Y-m-d H:i:s');

$user_result = $mysqli->query("SELECT a.group_access,a.emp_location_access,a.emp_division_access,a.user_access,a.user_not_access,a.emp_location_not_access FROM tbl_users u left join tbl_user_access a on  u.user_id=a.user_id  where u.status='Active' and is_regular=1 and module_access='main_module' and u.user_id=$userid");



$user_row = $user_result->fetch_assoc();

$group_access = $user_row['group_access'];

$emp_location_access = $user_row['emp_location_access'];

$emp_division_access = $user_row['emp_division_access'];

$user_access = $user_row['user_access'];

$user_not_access = $user_row['user_not_access'];

$emp_location_not_access = $user_row['emp_location_not_access'];

$sql_access = "SELECT GROUP_CONCAT(user_id) division_head_access FROM  tbl_users WHERE division_head='" . $userid . "'";

$access_query = $mysqli->query($sql_access);

$access_row = $access_query->fetch_assoc();

$user_under_division_head = $access_row['division_head_access'];

if ($user_under_division_head == "")
    $user_under_division_head = 0;


$query = "SELECT u.user_id FROM tbl_users u  where u.status='Active' and is_regular=1 and  (u.user_id=$userid ";

if ($group_access != "")
    $query .= " or group_id in  ($group_access)  ";

if ($emp_location_access != "")
    $query .= " or work_location in  ($emp_location_access)  ";

if ($emp_division_access != "")
    $query .= " or emp_division_id in  ($emp_division_access)  ";

if ($user_access != "")
    $query .= " or u.user_id in  ($user_access)  ";



$query .= ") ";



if ($user_not_access != "")
    $query .= " and( u.user_id not in  ($user_not_access) ) ";

if ($emp_location_not_access != "")
    $query .= " and( work_location not in  ($emp_location_not_access) ) ";

$query .= " order by u.full_name";



$result = $mysqli->query($query);

$user_array = array();

while ($user_row = $result->fetch_assoc()) {

    $user_array[$user_row['user_id']] = $user_row['user_id'];
}

$user_in = implode(",", ($user_array));
if ($user_under_division_head != "")
    $user_in = $user_under_division_head . "," . $user_in;

    $user_in =  $_SESSION['USER_ACCESS']['MAINMODULE'];


//*******Monthly plan queries ->added by Saranya:23 Oct-17
$user_id = ((isset($_REQUEST['select_employee_id'])) && ($_REQUEST['select_employee_id'] > 0)) ? $_REQUEST['select_employee_id'] :0;
/* GET USER LST */
$query = "SELECT u.level,u.full_name short_name,user_id,d.short_name as division ,d.id as emp_div_id FROM tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id  where  status='Active' and is_regular=1 and is_erp=1 ";
if (!empty($user_in))
    $query .= " AND user_id IN($user_in)";
$query .= " order by d.short_name,u.full_name";
$result_users = $mysqli->query($query);
/* GET USER DATAS */

//print_r($_REQUEST);
$current_month = date('m');
$current_year = date("Y");
$plan_month = isset($_REQUEST['plan_month']) ? $_REQUEST['plan_month'] : $current_month;
$report_type = isset($_REQUEST['report_type']) ? $_REQUEST['report_type'] : "weekly";
$plan_year = isset($_REQUEST['plan_year']) ? $_REQUEST['plan_year'] : $current_year;
$day = "$plan_year-$plan_month-01";
/* FIND OUT PREVIOUS MONTH */
$previous_month = date("m", strtotime('-1 month', strtotime($day)));
$previous_year = date("Y", strtotime('-1 month', strtotime($day)));
$next_month = date("m", strtotime('+1 month', strtotime($day)));
$next_year = date("Y", strtotime('+1 month', strtotime($day)));
//**Monthly plan queries ends here 
 

    $sql1 = "SELECT 
        DATE_FORMAT(m.locked_date,'%d/%m/%Y %H:%i:%s') locked_date , m.audited as audited,m.head_audited as head_audited,
        DATE_FORMAT(m.last_audit_date,'%d/%m/%Y %H:%i:%s') last_audit_date ,
        DATE_FORMAT(m.head_last_audit_date,'%d/%m/%Y %H:%i:%s') head_last_audit_date ,is_locked,
        m.auditing,m.audited,month(u.doj) as join_month,year(u.doj) as join_year,u.doj as join_date,
        SUM(IF(duty!='',1,0)) as total_jobs,
         
        u.user_id,u.division_head,  u.is_lock, full_name as username, u.emp_division_id, u.emp_subdivision_id  FROM   tbl_users u    
  
        LEFT JOIN  tbl_emp_duties_master m ON m.user_id = u.user_id 
      
        LEFT JOIN    tbl_emp_duties duty on duty.user_id=u.user_id 
     
        where     u.status='Active' and u.is_regular=1 AND (u.user_id in($user_in))  ";

if (!empty($_REQUEST['select_employee_id'])) {

    $sql1 .= " AND u.user_id = '" . $_REQUEST['select_employee_id'] . "'";
}
if ($view_filter > 0 && empty($_REQUEST['select_employee_id'])) {

    $result = $mysqli->query("select group_concat(user_id) as filter_user from tbl_filter_users where filter_id=$view_filter");
    $filter_user_row = $result->fetch_assoc();

    $filter_users = ($filter_user_row['filter_user'] != "") ? $filter_user_row['filter_user'] : 0;
    $sql1 .= " AND u.user_id in  ($filter_users)";
}

if ($emp_company_id > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and emp_company_id=$emp_company_id ";

if ($emp_division_id > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and emp_division_id=$emp_division_id ";

if ($emp_subdivision_id > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and emp_subdivision_id=$emp_subdivision_id ";

if ($group_id > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and group_id=$group_id ";

if ($location_id > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and  work_location=$location_id ";
if ($work_category > 0 && empty($_REQUEST['select_employee_id']))
    $sql1 .= " and  work_category=$work_category ";

if($search_by>0 &&$search_by==1){
    $sql1 .=  " and (mplan.is_completed is null or mplan.is_completed=0)";
}

if($search_by>0 &&$search_by==2){
    $sql1 .=  " and (mplan.audited=0 or mplan.audited is null) ";
}
$sql1 .= " group by u.full_name having 1=1 ";

 $sql1.=" ORDER BY case when u.user_id in ($user_under_division_head) then 0 else 1 end, u.full_name "; 
// echo $sql1;

if (isset($_GET['starting']) && !isset($_REQUEST['search'])) {

    $starting = $_GET['starting'];
} else {

    $starting = 0;
}

$recpage = 30; //number of records per page

$count_sql = "SELECT 
        DATE_FORMAT(m.locked_date,'%d/%m/%Y %H:%i:%s') locked_date , m.audited as audited,m.head_audited as head_audited,
        DATE_FORMAT(m.last_audit_date,'%d/%m/%Y %H:%i:%s') last_audit_date ,
        DATE_FORMAT(m.head_last_audit_date,'%d/%m/%Y %H:%i:%s') head_last_audit_date ,is_locked,
        m.auditing,m.audited,month(u.doj) as join_month,year(u.doj) as join_year,u.doj as join_date,
        SUM(IF(duty!='',1,0)) as total_jobs,
         
        u.user_id,u.division_head,  u.is_lock, full_name as username, u.emp_division_id, u.emp_subdivision_id  FROM   tbl_users u    
  
        LEFT JOIN  tbl_emp_duties_master m ON m.user_id = u.user_id 
      
        LEFT JOIN    tbl_emp_duties duty on duty.user_id=u.user_id 
     
        where     u.status='Active' and u.is_regular=1 AND (u.user_id in($user_in)) ";


if ($view_filter > 0 && empty($_REQUEST['select_employee_id'])) {

    $count_sql .= " AND u.user_id in  ($filter_users)";
}

if ($emp_company_id > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and emp_company_id=$emp_company_id ";

if ($emp_division_id > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and emp_division_id=$emp_division_id ";

if ($emp_subdivision_id > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and emp_subdivision_id=$emp_subdivision_id ";

if($search_by>0 &&$search_by==1){
    $count_sql .=  " and (mplan.is_completed is null or mplan.is_completed=0) ";
}

if($search_by>0 &&$search_by==2){
    $count_sql .=  " and   (audited=0 or audited is null) ";
}

if ($group_id > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and group_id=$group_id ";

if ($location_id > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and work_location=$location_id ";
if ($work_category > 0 && empty($_REQUEST['select_employee_id']))
    $count_sql .= " and  work_category=$work_category ";


$count_sql .= " group by u.full_name having 1=1 "; 

$count_result = $mysqli->query($count_sql);

 $count_user = $count_result->num_rows;
$obj = new pagination_class($sql1, $starting, $recpage, $count_user);

$output = $obj->result;
$filter_results = $mysqli->query("SELECT * from tbl_filter where created_by=$current_user_id order by filter_name");


/*****UN LOCK Approve SENDING*******/
if (isset($_POST['approve_un_lock'])) {$select_employee_id = $_POST['select_employee_id'];
  $aprv_result = $mysqli->query("SELECT id FROM tbl_emp_duties_master WHERE user_id='$select_employee_id' ");
$aprv_row = $aprv_result->fetch_assoc();
    if ($aprv_row['id'] > 0)
       	$result = $mysqli->query("update tbl_emp_duties_master set is_locked=3,unlock_approve_date='$current_time' WHERE user_id='$select_employee_id' ");
        
    //Locked mail notifctn sending  
       $user_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$select_employee_id");
    $user_row = $user_info_query->fetch_assoc();
    $head_id = $user_row['division_head']; //echo "select * from tbl_users user where user.user_id=$head_id";exit;
    $head_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$head_id");
    $head_row = $head_info_query->fetch_assoc();
       $to =$head_row['email'];// "saranyasasi.biztv@gmail.com";//$head_row['email'];
    $cc = $user_row['email'];

   $message .= "<b>Dear " . $user_row['full_name'] . "</b>,<br><br>";
    $message .= " This is to inform you that your Duties and Responsibilities unlock request  has been approved. <br><br>";
    $message.="<table style='background-color:#ffffff;border:1px solid #c3c3c3;border-collapse:collapse;width:70%; padding:3'>
				<tbody><tr>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Approved By Name</th>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Locked Date</th>
                                       

				</tr>
				<tr>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $head_row['full_name'] . "</td>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . date('d-m-Y')."  </tr>";
                   

 @include_once 'PHPMailer/class.phpmailer.php';

 
                                                    $mail = new PHPMailer(true);
 
                                                    $mail->isSMTP();
 
                                                    $mail->SMTPDebug = 0;
 
                                                    $mail->Debugoutput = 'html';
 
                                                    $mail->Host = "mail.effism.com";
 
                                                    $mail->Port = 25;
 
                                                    $mail->SMTPAuth = true;
 
                                                    $mail->Username = "mail@effism.com";
 
                                                    $mail->Password = "aries2016";
 

                                                    $mail->ClearAllRecipients();
                                                    $mail->SetFrom("mail@effism.com", 'Effism Mail');

                                                   $mail->AddAddress($to);
                                                  
                                                   
                                                       $mail->AddCC($cc); 
                                                  

                                                    $mail->IsHTML(true);


                                                    $mail->Subject = "Duties and Responsibilities Unlocked ";
                                                    $mail->Body = $message;

                                                    if (!$mail->Send()) {

                                                        //display_error("Mailer Error: " . $mail->ErrorInfo);
                                                        echo "Mailer Error: " . $mail->ErrorInfo;
                                                        //print 'not done';
                                                    } else {

                                                        //display_notification('Thank you! Your Division Leave Entry has been Sent to Admin Department.');
                                                    }
     //Ends mail 
    
    header("location:duty_entry_latest.php") ;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <link rel="icon" href="images/favicon.ico" type="image/x-icon">
        <title>Online Job Diary - Aries Marine</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link rel="stylesheet" href="css/colorbox.css" />


        <link href="css/style.css" rel="stylesheet" type="text/css">

        <link href="css/calendarstyle.css" rel="stylesheet" type="text/css">

        <script type="text/javascript" src="javascript/calendar.js"></script>

        <script type="text/javascript" src="javascript/js.js"></script>

        <script type="text/javascript" src="javascript/date-functions.js"></script>

        <link rel='stylesheet' type='text/css' href='js/chosen/1.1.0/chosen.css'/>
    </head>



    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<?php
//if ($date == date("d-m-Y")) {
?>

        <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">
        <?php
        include("includes/header.php");
        ?>



            <?php
            $num = 1;
            ?>
            <tr>
                <td>&nbsp;

                    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
                        <tr>
                            <td colspan="8">
                                <table width="97%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">

                                    <td colspan="20"><br></td>

                        </tr>
                        <tr><form name="frm" method="post" action="duty_audit_latest.php">
                            <td colspan="20" align="center" valign="top">

                                <table width="90%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">

<?php
$count_row = 1;
?>

                                    <tr height="20" style="border:#a6107b solid 1px;">
                                       
                                        <td align="center" style="border:#000000 solid 1px; padding-left:3px;" width="80%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>

                                            <table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
                                                <tr height="30" style="border:#a6107b solid 1px;"><td colspan="3" align="center" width="8%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Duties & Responsibilities</font></b></td></tr>
<?php $count_row = 1; ?> 
                                                <form name="frm" method="post" action="duty_audit_latest.php">
                                                    <tr height="20" style="border:#a6107b solid 1px;"><td width="5%" align="center"><a href="duty_audit_latest.php?select_employee_id=<?php echo $select_employee_id ?>&plan_month=<?php echo $previous_month ?>&plan_year=<?php echo $previous_year ?>&search_by=<?php echo $search_by ?>&emp_company_id=<?php echo $emp_company_id?>&emp_division_id=<?php echo $emp_division_id ?>&emp_subdivision_id=<?php echo $emp_subdivision_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>&view_filter=<?php echo $view_filter ?>&work_category=<?php echo $work_category?>"><img src="images/previous.png" border="0" ></a></td>
                                                        <td width="90%" align="left" style="border:#000000 solid 1px; padding-left:1px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>&nbsp;<strong>Name:</strong>
                                                            <select name='select_employee_id' id='select_employee_id' class="chosen-select-deselect" onchange='this.form.submit()'>
                                                                <option value="0">Select Employee</option><?php
                                                while ($row = $result_users->fetch_assoc()) {
                                                    if ($previous_div_id != $row['emp_div_id'])
                                                        echo "<optgroup label='" . $row['division'] . "'>";
                                                    $previous_div_id = $row['emp_div_id'];
                                                    ?>
                                                                    <option value='<?php print($row['user_id']) ?>' <?php if ($row['user_id'] == $user_id) { ?> selected<?php } ?>><?php print($row['short_name']) ?></option><?php }
                                                ?>
                                                            </select>
                                                            &nbsp;&nbsp;&nbsp;
                                                            
<!--                                                            <strong>Search By:</strong>
                                                            <select  style="width: 100px;" name="search_by" id="search_by" class="chosen-select-deselect" onchange='this.form.submit()'>
                                                                  <option <?php if ($search_by == 0) { ?> selected <?php } ?>  value="0">Select</option>
                                                                <option <?php if ($search_by == 1) { ?> selected <?php } ?>  value="1">Not Completed</option>
                                                                <option <?php if ($search_by == 2) { ?> selected <?php } ?>  value="2">Not Audited</option>
                                                                
                                                            </select>-->
                                                            <!--<input name="go" style="cursor: pointer; vertical-align: top;" src="images/Go.gif" type="image">-->
                                                        </td>
                                                        <td align="center"><a href="duty_audit_latest.php?select_employee_id=<?php echo $select_employee_id ?>&plan_month=<?php echo $next_month ?>&plan_year=<?php echo $next_year ?>&search_by=<?php echo $search_by ?>&emp_company_id=<?php echo $emp_company_id?>&emp_division_id=<?php echo $emp_division_id ?>&emp_subdivision_id=<?php echo $emp_subdivision_id?>&group_id=<?php echo $group_id?>&location_id=<?php echo $location_id?>&view_filter=<?php echo $view_filter ?>&work_category=<?php echo $work_category?>"><img src="images/next.png" border="0"></a><br></td>
                                                    </tr>
                                            </table>  
                                    </tr>

                                    <tr height="40" style="border:#a6107b solid 1px;">  
                                    <!--<form name="frm" method="post" action="duty_audit_latest.php">-->
                                        <td align="center" colspan="3" bgcolor="#e9e9e9">

                                            <strong>Company:&nbsp;</strong>

                                            <select name="emp_company_id" class="chosen-select-deselect" onchange='this.form.submit()'>

                                                <option value="0">Select Company</option>

<?php
while ($row = $emp_comp_result->fetch_assoc()) {
    ?>

                                                    <option value="<?php echo $row['id'] ?>" <?php if ($emp_company_id == $row['id']) { ?> selected="selected" <?php } ?> ><?php echo $row['short_name'] ?></option>

                                                    <?php
                                                }
                                                ?>

                                            </select>&nbsp;

                                            <strong>Division:</strong>

                                            <select name="emp_division_id" class="chosen-select-deselect" onchange='this.form.submit()'>

                                                <option value="0">All Divisions</option>

<?php
while ($row = $emp_div_result->fetch_assoc()) {
    ?>

                                                    <option value="<?php echo $row['id'] ?>" <?php if ($emp_division_id == $row['id']) { ?> selected="selected" <?php } ?> ><?php echo $row['short_name'] ?></option>

                                                    <?php
                                                }
                                                ?>

                                            </select>&nbsp;
                                            <strong>Sub Division:</strong>
                                            <select name="emp_subdivision_id" class="chosen-select-deselect" onchange='this.form.submit()'>
                                                <option value="0">All Sub Divisions</option>
<?php
while ($row = $emp_subdiv_result->fetch_assoc()) {
    ?>

                                                    <option value="<?php echo $row['id'] ?>" <?php if ($emp_subdivision_id == $row['id']) { ?> selected="selected" <?php } ?> ><?php echo $row['short_name'] ?></option>

                                                    <?php
                                                }
                                                ?>

                                            </select>&nbsp;

                                            <strong>Group:&nbsp;</strong>

                                            <select name="group_id" class="chosen-select-deselect" onchange='this.form.submit()'>

                                                <option value="0">Select Group</option>

<?php
while ($row = $group_result->fetch_assoc()) {
    ?>

                                                    <option value="<?php echo $row['emp_group_id'] ?>" <?php if ($group_id == $row['emp_group_id']) { ?> selected="selected" <?php } ?> ><?php echo $row['emp_group_name'] ?></option>

                                                    <?php
                                                }
                                                ?>

                                            </select>&nbsp;

                                            <strong>Location:&nbsp;</strong>

                                            <select name="location_id" class="chosen-select-deselect" onchange='this.form.submit()'>

                                                <option value="0">All Locations</option>

<?php
while ($row = $location_result->fetch_assoc()) {
    ?>

                                                    <option value="<?php echo $row['id'] ?>" <?php if ($location_id == $row['id']) { ?> selected="selected" <?php } ?> ><?php echo $row['work_place'] ?></option>

                                                    <?php
                                                }
                                                ?>

                                            </select>

                                        </td>

                                        </tr>



                                        <tr height="40" style="border:#a6107b solid 1px;">

                                            <td align="left" colspan="3" bgcolor="#e9e9e9" style='padding-left:200px;'>

                                                <strong>Emp Category:&nbsp;</strong>

                                                <select name="work_category" class="chosen-select-deselect" onchange='this.form.submit()'>

                                                    <option value="0">All Categories</option>

<?php
$work_category_array = array("1" => "Productive", "2" => "Semi  Productive", "3" => "Supporting");

foreach ($work_category_array as $key => $value) {
    ?>

                                                        <option value="<?php echo $key ?>" <?php if ($work_category == $key) { ?> selected="selected" <?php } ?> ><?php echo $value ?></option>

                                                        <?php
                                                    }
                                                    ?>
                                                </select>

                                                &nbsp;
                                                &nbsp;<strong>&nbsp;</strong>
                                                <strong>Filter By:&nbsp;</strong>

                                                <select name='view_filter' id='employee_id' class="chosen-select-deselect" onchange='this.form.submit()'>

                                                    <option value="-1">View All</option><?php
                                                    while ($filter_row = $filter_results->fetch_assoc()) {
                                                        ?><option value='<?php print($filter_row['filter_id']) ?>' <?php if ($filter_row['filter_id'] == $view_filter) { ?> selected<?php } ?>><?php print($filter_row['filter_name']) ?></option><?php
                                                    }
                                                    ?></select>
                                            </td>

                                        </tr>
                                        <tr>

                                            <td colspan="3">&nbsp;

                                            </td>

                                        </tr>

                                </table></td>
               </form>          </tr>
                       
                        <tr>
                            <td height="5" colspan="20"></td>

                        </tr>

                        <tr height="30" style="border:#a6107b solid 1px;">


                            <td colspan="18" align="center"  style="border:#000000 solid 1px;"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors; ?> </b></font></td>


                        </tr>

                        <tr height="30" style="border:#a6107b solid 1px;">

                            <td width="2%" style="border:#000000 solid 1px;" align="center" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:11px">&nbsp;No</font></b></td>

                            <td width="15%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:11px">&nbsp;Name</font></b></td>

      <!--<td align="center"  width="5%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:11px">Summ.</font></b></td>-->

                            <td align="center"  width="5%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Div</font></b></td>

                            <td align="center"  width="3%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Sub</font></b></td>

                            <td align="center"  width="3%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">View</font></b></td>
                            
                             <td align="center"  width="3%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Status</font></b></td>
                             <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Reviewing</font></b></td>
                            

                            <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Head Reviewed</font></b></td>

                            <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Head Last Reviewed Date</font></b></td>

                            <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Reviewed</font></b></td>

                            <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" style="font-size:11px" face="Verdana, Arial, Helvetica, sans-serif">Last Reviewed Date</font></b></td>

                            <td align="center"  width="6%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:11px">Total </font></b></td>


                            <td align="center" width="5%"  style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" style="font-size:11px">Locked Date</font></b></td>


                        </tr>

                            <?php
                            $num = $starting + 1;

                            $count_row = 1;

                            $starting = 1;

                            $tree_flag = 0;

                            while ($result = $output->fetch_assoc()) {

                                $lock_id = 0;

                                if ($result['is_lock'] > 0) {

                                    $lock_query = $mysqli->query("select l.*,t.type as type_name, if(from_date<='$sql_date' and to_date>='$sql_date','1',0) as lock_current ,if(to_date<'$sql_date','1',0) as lock_expiry

												  from tbl_user_lock l left join tbl_lock_types t on t.id=l.type where locked_user=" . $result['user_id'] . "  and from_date<='$sql_date' and is_current=1  order by id desc limit 0,1");



                                    $lock_row = $lock_query->fetch_assoc();

                                    $lock_remarks = $lock_row['remarks'];
                                    $lock_current = $lock_row['lock_current'];
                                    $lock_expiry = $lock_row['lock_expiry'];
                                    $no_lock = $lock_row['no_lock'];
                                    $lock_job_no = $lock_row['job_no'];


                                    $lock_id = $lock_row['id'];

                                    $type_name = $lock_row['type_name'];
                                    if ($lock_job_no != "")
                                        $type_name .= $type_name . "(" . $lock_job_no . ")";
                                }

                                $unlock_text="";
                                if (($tree_flag == 0) && ($current_user_id == $result['division_head'])) {

                                    $font_color = "#FF0000";

                                    $tree_flag = 1;
                                    
                                   
                                    ?>

                                <tr height="30" style="border:#a6107b solid 1px;">
                                    <td colspan="18" align="center"  style="border:#000000 solid 1px;"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FF000"><b>&nbsp;My Audit Tree</b></font></td>

                                </tr><?php } ?>

    <?php
    if (($tree_flag == 1) && ($current_user_id != $result['division_head'])) {

        $font_color = "#000000";

        $tree_flag = 2;
        ?>

                                <tr height="20" style="border:#a6107b solid 1px;"> 


                                    <td colspan="18" align="center"  style="border:#000000 solid 1px;"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;</b></font></td>


                                </tr><?php } ?> 
    <?php 
    
   
    
    if ($lock_id > 0) {

        if ($lock_expiry == 1)
            $lock_bg_color = "#FF870F";
        else
            $lock_bg_color = "#800000";
        ?>

                                <tr height="20" style="border:#a6107b solid 1px;">

                                    <td width="2%" style="border:#000000 solid 1px;" align="center" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $num++; ?></b></font></td>



                                    <td style="border:#000000 solid 1px; padding-left:5px;" width="15%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="<?php echo $font_color ?>" ><strong>

        <?php echo $result['username']; ?>

                                            </strong></font></td>
                                    <!--<td align="center" style="border:#000000 solid 1px;" width="5%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><a class='iframe1'  href="dashboard/index.php?user_id=<?php echo $result['user_id'] ?>"><img src='images/summary.png' height="25px"  border='0' title='View this Employee' /></a></td>-->

                                    <td align="left" style="border:#000000 solid 1px;" width="5%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            &nbsp;<?php echo $dimension_array[$result['emp_division_id']]; ?>

                                        </font></td>

                                    <td align="left" style="border:#000000 solid 1px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;				  <?php echo $dimension_array[$result['emp_subdivision_id']]; ?>

                                        </font></td>

                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>
                                       <?php if($result['is_locked']!=""){ ?>
                                        <a href="javascript:openWindow('duty_history.php?select_employee_id=<?php echo $result['user_id'] ?>&plan_month=<?php echo $plan_month ?>&plan_year=<?php echo $plan_year ?>','view_status');"><img src='images/view.gif' width='12' height='12' border='0' title='View D&R' />

                                        </a>
                                       <?php }?>
                                    </td>
                                         <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>
                                        <?php echo $result['is_locked'];exit; if($result['is_locked']=="1") {?>
                                            <button class="btn btn-success btn-xs">Locked</button>
                                        <?php } else if($result['is_locked']=="0"){?>
                                             <button class="btn btn-danger btn-xs">Not Locked</button>
                                        <?php } else if($result['is_locked']=="2"){
                                             if($current_user_id == $result['division_head']){ 
                                            ?>
                                              <input id="complete_button2" onClick="return unlock_approve();" type="submit" class="btn btn-warning btn-xs" name="approve_un_lock" value="Unlock requested! Click to unlock">   
                                             <?php } else{?>
                                               <button class="btn btn-warning btn-xs">Unlock requested</button>
                                             <?php } } else if($result['is_locked']=="3"){?>
                                              <button class="btn btn-info btn-xs">Unlock Approved</button>
                                             <?php }   else if($result['is_locked']=="4"){?>
                                              <button class="btn btn-danger btn-xs">Change D&R</button>
                                             <?php } else{?>
                                              <span style="color:red;font-weight: bold"> Not Added!!</span>
                                     <?php   }
?>
                                        </td>

                                    <td align="left" colspan="12" style="border:#000000 solid 1px; padding-left:5px; color:#FFFFFF" width="6%" bgcolor="<?php echo $lock_bg_color ?>"><strong>Locked Until <?php echo date('d-m-Y', strtotime($lock_row['to_date'])) ?>. Type:<?php echo $type_name ?></strong>&nbsp;&nbsp;&nbsp;<strong>Remarks:<?php echo $lock_remarks; ?></strong></td>


                                </tr> 

        <?php
    } else {
        ?>
<form name="frm" method="post" action="duty_audit_latest.php">
    <input type="hidden" name="select_employee_id" value="<?=$result['user_id']?>">
                                <tr height="20" style="border:#a6107b solid 1px;">


                                    <td width="2%" style="border:#000000 solid 1px;" align="center" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $num++; ?></b></font></td>



                                    <td style="border:#000000 solid 1px; padding-left:5px;" width="15%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="<?php echo $font_color ?>" ><strong>

        <?php echo $result['username'] ?>

                                            </strong></font></td>
                                    
                                    <td align="left" style="border:#000000 solid 1px;" width="5%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            &nbsp;<?php echo $dimension_array[$result['emp_division_id']]; ?>

                                        </font></td>

                                    <td align="left" style="border:#000000 solid 1px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">&nbsp;				  <?php echo $dimension_array[$result['emp_subdivision_id']]; ?>

                                        </font></td>

                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>
                                        <?php if($result['is_locked']!=""){ ?>
                                        <a  href="javascript: openWindow('duty_history.php?select_employee_id=<?php echo $result['user_id'] ?>&plan_month=<?php echo $plan_month ?>&plan_year=<?php echo $plan_year ?>','view_status');"><img src='images/view.gif' width='12' height='12' border='0' title='View D&R' />

                                        </a>
                                        <?php }?>
                                    </td>
                                        
                                        <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="3%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>>
                                        <?php if($result['is_locked']=="1") {?>
                                            <button class="btn btn-success btn-xs">Locked</button>
                                        <?php } else if($result['is_locked']=="0"){?>
                                             <button class="btn btn-danger btn-xs">Not Locked</button>
                                        <?php } else if($result['is_locked']=="2"){
                                             if($current_user_id == $result['division_head']){ 
                                            ?>
                                              <input id="complete_button2" onClick="return unlock_approve();" type="submit" class="btn btn-warning btn-xs" name="approve_un_lock" value="Unlock requested! Click to unlock">   
                                             <?php } else{?>
                                               <button class="btn btn-warning btn-xs">Unlock requested</button>
                                             <?php } } else if($result['is_locked']=="3"){?>
                                              <button class="btn btn-info btn-xs">Unlock Approved</button>
                                             <?php }    else if($result['is_locked']=="4"){?>
                                              <button class="btn btn-info btn-xs">Change D&R</button>
                                             <?php }else{?>
                                              <span style="color:red;font-weight: bold"> Not Added!!</span>
                                     <?php   }
?>
                                        </td>

                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px; color:#FF0000" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            <strong> <?php echo $result['auditing']; //get_audit_count($plan_month,$plan_year,$result['user_id'])  ?></strong>

                                        </font></td>
                                        
                                        
                                        <!--head count sart-->
                                        <td align="center" style="border:#000000 solid 1px; padding-left:5px; color:#FF0000" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                                        <strong>
                                                            <?php echo $result['head_audited'];  //get_audited_count($plan_month,$plan_year,$result['user_id']);// $result['audited']  ?>

                                                        </strong></font></td>


                                                <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                                        <strong> 
                                                            <?php if ($result['head_last_audit_date'] != "00/00/0000 00:00:00") echo $result['head_last_audit_date'];
                                                            else echo "__"; ?> 
                                                        </strong></font></td>
                                        <!--head count end-->
                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px; color:#FF0000" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            <strong>

        <?php echo $result['audited'];  //get_audited_count($plan_month,$plan_year,$result['user_id']);// $result['audited']  ?>

                                            </strong>			    </font></td>


                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            <strong>

        <?php if($result['last_audit_date']!="00/00/0000 00:00:00") echo $result['last_audit_date']; else echo "__" ?>

                                            </strong>			    </font></td>
                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="6%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $result['total_jobs'] ?></b></font></td>



                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;" width="5%" <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif">

                                            <?php if($result['is_locked']=="1") echo $result['locked_date']; else echo "__" ; ?>

                                        </font></td>
                                        
                                        
                                </tr></form>

                                            <?php
                                        }

                                        $count_row++;
                                    }
                                    ?>
                        <tr height="30" style="border:#a6107b solid 1px;">

                            <td colspan="18" align="center"  style="border:#000000 solid 1px;"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000"><b>&nbsp;<?php echo $obj->anchors; ?> </b></font></td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>

    </td></tr></table>
<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/chosen/1.7.0/chosen.jquery.js"></script>
<script language="JavaScript">

    function pagination(page)

    {

         window.location = "duty_audit_latest.php?&starting=" + page + "&plan_month=<?php echo $plan_month ?>" + "&plan_year=<?php echo $plan_year ?>"+"&search_by=<?php echo $search_by ?>"+"&emp_company_id=<?php echo $emp_company_id ?>"+"&emp_division_id=<?php echo $emp_division_id ?>"+"&emp_subdivision_id=<?php echo $emp_subdivision_id ?>"+"&group_id=<?php echo $group_id ?>"+"&location_id=<?php echo $location_id ?>"+"&view_filter=<?php echo $view_filter ?>"+"&work_category=<?php echo $work_category; ?>";

    }



    function openWindow(url, title)

    {



        var left = (screen.width - 900) / 2;

        var top = (screen.height - 500) / 2;



        var href;



        if (typeof (url) == 'string')
            href = url;



        else
            href = url.href;

        if (window.wind && !wind.closed)

        {

            //alert("Test");

            wind.close();

            wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

        } else {

            wind = window.open(href, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');

        }

    }

 

    function pagination(page)

    { 

         window.location = "duty_audit_latest.php?&starting=" + page + "&plan_month=<?php echo $plan_month ?>" + "&plan_year=<?php echo $plan_year ?>"+"&search_by=<?php echo $search_by ?>"+"&emp_company_id=<?php echo $emp_company_id ?>"+"&emp_division_id=<?php echo $emp_division_id ?>"+"&emp_subdivision_id=<?php echo $emp_subdivision_id ?>"+"&group_id=<?php echo $group_id ?>"+"&location_id=<?php echo $location_id ?>"+"&view_filter=<?php echo $view_filter ?>"+"&work_category=<?php echo $work_category; ?>";

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

    function unlock_approve(form, type, status){
     var r = confirm("Are you sure to approve this unlock request?");
            if (r == true)
            {

                return true;
            }
            else
            {
                return false;
            }
}


 

        var config = {
            '.chosen-select': {},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 10},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}

        }

        for (var selector in config) {

            $(selector).chosen(config[selector]);

        }
</script>
<?php
// include("includes/footer.php");

$mysqli->close();
include("includes/footer.php");
?>
</body> 
</html>