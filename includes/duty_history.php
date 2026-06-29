<?php
/* * Created by SARANYA : DR details page
 * ON:16-AUG-17
 */
session_start();
if (!isset($_SESSION['user_id']))
    header("location:index.php");
include("includes/connect.inc.php");
include("includes/functions.php");
include("commonfunc.class.php");

$current_user_id = $_SESSION['user_id'];
$emp_division_id = $_REQUEST['emp_division_id'];
$emp_div_result = $mysqli->query("SELECT * FROM tbl_dimensions where dimension_type=2 order by short_name");

$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone', $time_zone);

$tree_id = $_REQUEST['tree_id'];
if ($tree_id > 0)
    $_REQUEST['select_employee_id'] = $tree_id;

$user_query = $mysqli->query("select  u.username,u.division_head from tbl_users u where   u.user_id=$current_user_id");

$user_row = $user_query->fetch_assoc();
$my_division_head = $user_row['division_head'];

$user_in = $_SESSION['USER_ACCESS']['MAINMODULE']; //implode(",",($user_array));

$user_id = (isset($_REQUEST['select_employee_id'])) && ($_REQUEST['select_employee_id'] > 0) ? $_REQUEST['select_employee_id'] : $current_user_id;

$select_user_row = get_user_details($user_id);
 $audit_employee_head = $select_user_row['division_head'];

//if (isset($_POST['lock'])) {
//    $sql = "SELECT COUNT(*) count FROM tbl_emp_duties_lock WHERE user_id = '$user_id'";
//    $result = $mysqli->query($sql);
//    $row = $result->fetch_assoc();
//    $time = date("Y-m-d H:i:s");
//    if ($_POST['locked'] == 'TRUE') {
//        $sql = "UPDATE tbl_emp_duties_lock SET locked_by = '0',
//									locked_time = '$time' WHERE user_id = '$user_id'";
//        Notify($user_id, '11', 'Duties & Responsibilities Unlocked.');
//    } else {
//        if ($row['count'] != '0') {
//            $sql = "UPDATE tbl_emp_duties_lock SET locked_by = '$current_user_id',
//									locked_time = '$time' WHERE user_id = '$user_id'";
//        } else {
//            $sql = "INSERT INTO tbl_emp_duties_lock(user_id,locked_by,locked_time)
//					VALUES('$user_id','$current_user_id','$time')";
//        }
//        Notify($user_id, '11', 'Duties & Responsibilities Locked.');
//    }
//    $mysqli->query($sql);
//}

$user_info_query = $mysqli->query("select u.photo,u.full_name,a.group_access,a.emp_location_access,a.emp_division_access,a.user_access,a.user_not_access,a.emp_location_not_access, u.username,u.email,u.mobile,u.hourly_rate,d1.full_name as emp_comp_name ,d2.full_name as emp_div_name,d3.full_name as subdivision,u.emp_division_id,user_photo,user_height  from tbl_users u left join tbl_user_access a on  u.user_id=a.user_id
left join tbl_dimensions d2 on d2.id=u.emp_division_id
left join tbl_dimensions d1 on d1.id=u.emp_company_id
left join tbl_dimensions d3 on d3.id=u.emp_subdivision_id
where u.user_id=$user_id");

$user_info_row = $user_info_query->fetch_assoc();

$query = "SELECT u.full_name,user_id,emp_division_id,d1.short_name as division_name FROM tbl_users u left join tbl_dimensions d1 on u.emp_division_id=d1.id where u.is_erp=1 and  status='Active' and is_regular=1 and  user_id in ($user_in) ";
$query .= " order by d1.full_name,u.full_name ";

$result_users = $mysqli->query($query);

 $sql1 = "select duty_id, entry_time,duty,row_id,remarks,hand_over,frequency,IF(time_FORMAT(approx_time,'%H:%i')!='00:00',time_FORMAT(approx_time,'%H:%i:%s'),'') as approx_time from tbl_emp_duties where duty!=''  and  user_id=" . $user_id . " order by row_id";

 

$output = $mysqli->query($sql1);

$user_name = $user_info_row['username'];

$full_name = get_full_name($user_id);

function get_full_name($user_id) {
    global $mysqli;
    $assigned_query = $mysqli->query("select full_name from tbl_users  where user_id=$user_id");
    $user_array = $assigned_query->fetch_assoc();
    return $user_array['full_name'];
}

$query = "SELECT u.short_name,user_id,d.short_name as division ,d.id as emp_div_id FROM tbl_users u left join tbl_dimensions d on d.id=u.emp_division_id  where  status='Active' and is_regular=1 and is_erp=1 ";

$query .= " order by d.short_name,u.short_name";
//$result_users = $mysqli->query($query);

$sql = "SELECT COUNT(*) count FROM tbl_users WHERE user_id = '$user_id' AND division_head = '$current_user_id'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] != '0') {
    $head = true;
} else {
    $head = false;
}

$sql = "SELECT COUNT(*) count FROM tbl_emp_duties_lock WHERE user_id = '$user_id' AND locked_by != '0'";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();

if ($row['count'] != '0') {
    $lock = true;
} else {
    $lock = false;
}
if ($_POST['updatedr']) {
//    echo "hooo";exit;
    $up_duty = $_POST['newtext'];
    $id = $_POST['req_id'];
     $query = "UPDATE tbl_emp_duties set updated_duty='$up_duty',updtn_request_to='$my_division_head'   where  duty_id=$id";

    $mysqli->query($query);
          Notify($my_division_head,'12','Duties & Responsibilities Change Request Submitted.');
    echo "success";
    exit;
}

if ($_POST['deletedr']) {
//    echo "hooo";exit;
    $del_remarks = $_POST['del_remarks'];
    $id = $_POST['req_id'];
     $query = "UPDATE tbl_emp_duties set del_remarks='$del_remarks',updtn_request_to='$my_division_head'   where  duty_id=$id";

    $mysqli->query($query);
          Notify($my_division_head,'12','Duties & Responsibilities Deletion Request Submitted.');
    echo "success";
    exit;
}
if (isset($_POST['new']) && !empty($_POST['new'])) {
    $details = $_POST['details'];
    $handover = $_POST['handover'];
    $freq = $_POST['freq'];
    $apptime = $_POST['apptime'] . ":00";
    $remarks = $_POST['remarks'];
    $entry_time = date('Y-m-d H:i:s', time());
    $sql = "INSERT INTO `tbl_emp_duties`(`user_id`,  `duty`, `hand_over`, `frequency`, `approx_time`, "
            . "`remarks`, `entry_time`,  `updtn_request_to`, `status`) VALUES ($user_id,'$details','$handover','$freq','$apptime',"
            . "'$remarks','$entry_time','$my_division_head',2)";
    $mysqli->query($sql);
      Notify($my_division_head,'12','New DR Request Submitted');
}
//*******Log Saving ************//
$log_date = date('Y-m-d H:i:s');
 $log_date2 = date('Y-m-d');
 if($user_id!=$current_user_id){
 $log_sql = "INSERT INTO `tbl_emp_duties_log`(`user_id`, `audited_by`, `audited_date`) VALUES ($user_id,$current_user_id,'$log_date')";
 $mysqli->query($log_sql);
 }
///***Master table updation
$audited_sql = " select *,DATE_FORMAT(head_last_audit_date,'%Y-%m-%d') as head_date, DATE_FORMAT(last_audit_date,'%Y-%m-%d')last_audit_date from tbl_emp_duties_master where   user_id=$user_id   ";
$output2 = $mysqli->query($audited_sql);

if ($output2->num_rows != 0) {
    $audit_result = $output2->fetch_assoc();
    $auditvalue = $audit_result['audited'] + 1;
    $headauditvalue = $audit_result['head_audited'] + 1; //added on 16dec-17:***head review count
    
   
    
    if ($audit_employee_head == $current_user_id){//****if the reviewing peron is the reporting head then head cunt will get updated
        $audit_sql = " update tbl_emp_duties_master set ";
        if($audit_result['head_date']!=$log_date2){ ///****Need to increment head count only if the day is different.
        $audit_sql.="  head_audited=$headauditvalue ,";}
        $audit_sql.=" head_last_audit_date='$log_date' where user_id=$user_id";
    }
    else{
    $log_search_sql = "select * from tbl_emp_duties_log where audited_by=$current_user_id and DATE_FORMAT(audited_date,'%Y-%m-%d')==$log_date2";
    $output3 = $mysqli->query($log_search_sql);//***To find whether the audit user already audtd the same employee before on the same day

if ($output3->num_rows == 0){
    $audit_sql = "update tbl_emp_duties_master set audited=$auditvalue , last_audit_date='$log_date' where user_id=$user_id";
}
}
    
    $mysqli->query($audit_sql);//*****Query running goes here***********
}


$duty_log_sql = "SELECT  duty_id,count(duty_id) as count_duty from  tbl_duties_change_log  where user_id=$user_id  and duty!='' group by duty_id";
$duty_log_output = $mysqli->query($duty_log_sql);
$duty_log_array = array();
while ($duty_log_row = $duty_log_output->fetch_assoc()) {
    $duty_id = $duty_log_row['duty_id'];
    $duty_log_array[$duty_id] = $duty_log_row['count_duty'];
}
//print_r($duty_log_array);
if(isset($_POST['change_dr'])){ $select_employee_id = $_REQUEST['select_employee_id'];

    $completion_result = $mysqli->query("SELECT id FROM tbl_emp_duties_master WHERE user_id='$user_id' ");
$completion_row = $completion_result->fetch_assoc();
    if ($completion_row['id'] > 0)
       	$result = $mysqli->query("update tbl_emp_duties_master set is_locked=4,change_req_date='$current_time' WHERE user_id='$user_id' ");
     else	
	$result = $mysqli->query("INSERT INTO  tbl_emp_duties_master(`user_id`, `added_date`,`is_locked`,`locked_date`)  values ('$user_id','$current_time','1','$current_time')");
  
     //CHANGE DR mail notifctn sending  
       $user_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$user_id");
    $user_row = $user_info_query->fetch_assoc();
    $head_id = $user_row['division_head']; //echo "select * from tbl_users user where user.user_id=$head_id";exit;
    $head_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$head_id");
    $head_row = $head_info_query->fetch_assoc();
       $to =$user_row['email'];// "saranyasasi.biztv@gmail.com";//$head_row['email'];
     $cc = $head_row['email'];

   $message .= "<b>Dear " . $user_row['full_name'] . "</b>,<br><br>";
    $message .= " This is to inform you that your reporting head has been instructed you to change your Duties and Responsibilities. <br><br>";
    $message.="<table style='background-color:#ffffff;border:1px solid #c3c3c3;border-collapse:collapse;width:70%; padding:3'>
				<tbody><tr>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Head Name</th>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'> Date</th>
                                       

				</tr>
				<tr>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $head_row['full_name'] . "</td>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . date('d-m-Y')."  </tr>";
                   
//echo $message;exit;
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
     
     header("location:duty_history.php?select_employee_id=$select_employee_id") ;
}

 $duties_result = $mysqli->query("SELECT * FROM tbl_emp_duties_master WHERE user_id='$user_id' ");
$d_row = $duties_result->fetch_assoc();
$is_lock =  $d_row['is_locked'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

    <head>

        <link rel="icon" href="images/favicon.ico" type="image/x-icon">

        <title>Online Job Diary - Aries Marine</title>

        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

        <link href="css/style.css" rel="stylesheet" type="text/css">

        <link href="css/calendarstyle.css" rel="stylesheet" type="text/css">

        <!--<script type="text/javascript" src="javascript/calendar.js"></script>-->

        <!--<script type="text/javascript" src="javascript/js.js"></script>-->

        <!--<script type="text/javascript" src="javascript/date-functions.js"></script>-->

        <link rel='stylesheet' type='text/css' href='js/chosen/1.1.0/chosen.css'/>
<!--<link href = "css/bootstrap.css" rel = "stylesheet" type = "text/css">-->
    </head>

    <body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <style>
        .modal .modal-header {
            border-bottom: none;
            position: relative;
        }
        .modal .modal-header .btn {
            position: absolute;
            top: 0;
            right: 0;
            margin-top: 0;
            border-top-left-radius: 0;
            border-bottom-right-radius: 0;
        }
        .modal .modal-footer {
            border-top: none;
            padding: 0;
        }
        .modal .modal-footer .btn-group > .btn:first-child {
            border-bottom-left-radius: 0;
        }
        .modal .modal-footer .btn-group > .btn:last-child {
            border-top-right-radius: 0;
        }
    </style>

    <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse">

        <?php
        $num = 1;
        ?>
        <tr>

            <td>&nbsp;

                <table width="100%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">

                    <tr><td colspan="8">

                            <table width="95%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
                                    <tr>
                                        <td align="center" colspan="4">&nbsp;</td>

                                    </tr>

                                    <tr>

                                        <td colspan="4"><br></td>

                                    </tr>
 

                                    <tr>

                                        <td colspan="2">&nbsp;</td>

                                    </tr>

                                </form>

                                <tr>

                                    <td colspan="4" align="center"></td>

                                </tr>

                                <tr>

                                    <td colspan="4"></td>

                                </tr>

                                <tr height="30" style="border:#a6107b solid 1px;">


                                    <td align="center"  style="border:#000000 solid 1px; padding-top:5px; padding-bottom:5px;" valign="top">
                                    <?php get_user_details_table($user_id, $my_division_head); ?>
                                    </td>
                                </tr>
                                
                                
                                <!--////*******AUDIT DETAILS*****-->
                             <?php
                                $sql1 = "select DATE_FORMAT(audited_date,'%d/%m/%Y %h:%i %p') as log_date, u.full_name as username, u2.full_name as audit_by from  tbl_emp_duties_log l left join tbl_users u on u.user_id=l.user_id "
                                        . "left join tbl_users u2 on u2.user_id=l.audited_by"
                                        . " where l.user_id= $user_id order by date(audited_date) DESC limit 5 ";
                             $mysql1 = $mysqli->query($sql1);
                             if ($mysql1->num_rows != 0)
                             {  
                             ?>
    <table  id="customers_time" class="reviewed_by" style="border-collapse: collapse;" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="8" align="center"><div style="display: block;"><font color="#ff0000;" size="+1"><strong>Last Five Reviews By</strong>&nbsp;</font></div></td>
        </tr>
        <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
            <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Audited By</font></b></td>
            <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></b></td>
        </tr>
        <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
            <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
                <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b><?php echo $row6['audit_by']; ?></b></font></td>
                <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b><?php echo $row6['log_date']; ?></b></font></td>
                  </tr>
            <?php
            $count++;
        }
        ?>
    </table>
                                <?php } ?>
    <br>
                                <!--//////****AUDIT DETAILS END*******-->
                                
                                
                                <!--////*******AUDIT TO DETAILS*****-->
                             <?php
                                $sql1 = "select DATE_FORMAT(audited_date,'%d/%m/%Y %h:%i %p') as log_date, u.full_name as username from  tbl_emp_duties_log l left join tbl_users u on u.user_id=l.user_id "
                                       
                                        . " where l.audited_by= $user_id order by date(audited_date) DESC limit 5 ";
                             $mysql1 = $mysqli->query($sql1);
                             if ($mysql1->num_rows != 0)
                             {  
                             ?>
    <table  id="customers_time" class="reviewed_by" style="border-collapse: collapse;" width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="8" align="center"><div style="display: block;"><font color="#ff0000;" size="+1"><strong>Last Five Reviews To</strong>&nbsp;</font></div></td>
        </tr>
        <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
            <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Audited To</font></b></td>
            <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Date</font></b></td>
        </tr>
        <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
            <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
                <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b><?php echo $row6['username']; ?></b></font></td>
                <td  align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b><?php echo $row6['log_date']; ?></b></font></td>
                  </tr>
            <?php
            $count++;
        }
        ?>
    </table>
                                <?php } ?>
    <br>
                                <!--//////****AUDIT TO DETAILS END*******-->

                                <tr height="30" style="border:#a6107b solid 1px;">

                                    <td  colspan="2" style="border:#000000 solid 1px; padding-top:5px" align="center"  valign="top"><table width="99%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-collapse:collapse">
                                      <tr>
                                          <form name="jobform" id="jobform" method="POST" action="duty_history.php?select_employee_id=<?=$user_id?>" enctype="multipart/form-data" >
                                          <td colspan=8  style="border:#000000 solid 1px; padding-top:5px" align="right" >
                                          <?php if($audit_employee_head==$current_user_id &&$is_lock!=3) {?>
                                           <input id="complete_button" onClick="return change_dr();" type="submit" class="btn btn-primary btn-xl" name="change_dr" value="CHANGE D&R"> 
                                           <?php }
                                           if($is_lock==3){?>
                                               <input id="complete_button"  class="btn btn-primary btn-xl" name="change_dr" value="CHANGE D&R SENT"> 
                                         <?php  }
                                           ?>
                                      </td>
                                      </form></tr>
                                            <tr height="30" style="border:#a6107b solid 1px;">
                                                <td align="center" width="5%"   style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;No</font></b></td>
                                                <td align="center"  width="25%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Job Details</font></b></td>
                                                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Handover during Leave</font></b></td>
                                                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Frequency</font></b></td>
                                                <td align="center"  width="10%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Approx.Time</font></b></td>
                                                <td width="20%" align="center" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Remarks</font></b></td>
                                                <td width="12%" align="center" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Updated Date</font></b></td> 
                                                <td width="12%" align="center" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">History</font></b></td>
                                            </tr>
                                            <?php
                                            $count_row = 1;
                                            $i = 1;
                                            while ($result = $output->fetch_assoc()) {
                                                ?>
                                                <tr height="20" style="border:#a6107b solid 1px;">
                                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $i++; ?>
                                                            </strong></font></td>
                                                    <td align="left" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $result['duty']; ?>
                                                            </strong></font></td>
                                                    <td align="left" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $result['hand_over']; ?>
                                                            </strong></font></td>
                                                    <td align="left" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $result['frequency']; ?>
                                                            </strong></font></td>
                                                    <td align="center" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $result['approx_time']; ?>
                                                            </strong></font></td>
                                                    <td align="left" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo $result['remarks']; ?>
                                                            </strong></font></td>
                                                    <td align="left" style="border:#000000 solid 1px; padding-left:5px;"  <?php if ($count_row % 2 == 0) { ?> bgcolor="#e9e9e9"<?php } else { ?>bgcolor="#f2f2f2"<?php } ?>><font face="Verdana, Arial, Helvetica, sans-serif"><strong>
    <?php echo date('d-m-y h:i a',strtotime($result['entry_time'])); ?>
                                                            </strong></font></td> 
                                                             <?php $log_duty_id = $result['duty_id'];?>
                                                    <td align="center"  <?php if ($duty_log_array[$log_duty_id] > 0) {?>bgcolor="#FF0000" <?php }?>>
                                                        <?php
                                                        if ($duty_log_array[$log_duty_id] > 0) {
                                                            ?>
                                                        <a href="javascript: openWindow('duty_log_view.php?duty_id=<?php echo $result['duty_id']?>','view_status');" ><img border="0" src="images/view.gif"></a></td>
                                                    <?php }?>
                                                </tr>
                                                 

                                                <?php
                                                $count_row++;
                                            }
                                            ?>
                                                 
                                        </table></td>
                                </tr>


                            </table>

                            <?php

                            function generate_menu($parent) {

                                global $user_row;
                                $has_childs = false;

                                //this prevents printing 'ul' if we don't have subcategories for this category

                                global $menu_array;

                                //use global array variable instead of a local variable to lower stack memory requierment

                                foreach ($menu_array as $key => $value) {

                                    if ($value['parent'] == $parent) {

                                        //if this is the first child print '<ul>'

                                        if ($has_childs === false) {

                                            $has_childs = true;
                                        }

                                        $user_row[$key] = $value['name'];



                                        generate_menu($key);

                                        //call function again to generate nested list for subcategories belonging to this category
                                        ///echo '</li>';
                                    }
                                }

                                if ($has_childs === true)
                                    echo '';
                            }
                            ?>
 <script type="text/javascript" src="lib/jquery.min.js"></script>
                            <script language="javascript">

                                function click_tree(user_id)
                                {

                                    document.getElementById('tree_id').value = user_id;
                                    document.forms['frm'].submit();
                                }
                                function saveDr(id) {
                                    var up_text = ($("#updated_dr_" + id).val());
                                    if(up_text==""){
                                      alert("New Text Cannot be Empty")  
                                    }else{
                                    var reqid = $("#req_id_" + id).val();
                                    var data = "req_id=" + reqid + "&updatedr=1" + "&newtext=" + up_text;
                                    $.ajax({
                                        data: data,
                                        type: "post",
                                        url: "duty_view.php",
                                        success: function (data) {
//                                                      $("#reqModal_"+id).modal('hide');
                                            alert("Sent Successfully!");
                                            location.reload();
                                        }
                                    });}
                                }
                                 function change_dr(){
         var r = confirm("Are you sure?")
            if (r == true)
            {

                return true;
            }
            else
            {
                return false;
            }
    }
                                
                                 $('.time_input').on('keyup', keyUpHandler);
    function keyUpHandler(e) {
        var element = this;
        var key = e.keyCode || e.which;
        insertTimingColor(element, key)
    }
    function insertTimingColor(element, key) {
        var inputValue = element.value;
        if (element.value.trim().length == 2 && key !== 8) {
            element.value = element.value + ':';
        }
    }
                            </script>
                          
                            <script type="text/javascript" src="js/bootstrap.min.js"></script>
                            <script type="text/javascript" src="js/chosen/1.7.0/chosen.jquery.js"></script>
                            <script type="text/javascript">
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
                            </body>
                            </html>