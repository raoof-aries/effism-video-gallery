<?php
/**
 * @Added By : Saranya
 * @For: DR new page
 * @Date: 18-Oct-19
 **/
ob_start();
session_start();
if (!isset($_SESSION['user_id']))
    header("location:index.php");
include("includes/connect.inc.php");
// include("includes/functions.php");
$time_zone = $_SESSION['time_zone'];
ini_set('date.timezone', $time_zone);

$current_time = date("Y-m-d H:i:s");

$user_id = (isset($_REQUEST['user_id']) && ($_REQUEST['user_id'] != "")) ? $_REQUEST['user_id'] : $_SESSION['user_id'];

$page_error = $_GET['error'];


$plan_flag = 1;

$duty_query = "SELECT duty_id,duty,row_id,remarks,hand_over,frequency,IF(time_FORMAT(approx_time,'%H:%i')!='00:00',time_FORMAT(approx_time,'%H:%i'),'') as approx_time from   tbl_emp_duties where user_id= '$user_id'";

$duty_result = $mysqli->query($duty_query);





$duty_rows = array();
$i = 1;
$extra = 0;

while ($r = $duty_result->fetch_assoc()) {
    $r['duty_id'] = $i++;
    $row_id = $r['row_id'];


    $extra = $row_id;
    $duty_rows[] = $r;
}


$duty_json = json_encode($duty_rows);


$time_dropdown = "";
$time_dropdown = '"00:00"';
for ($hours = 0; $hours <= 100; $hours++)
    for ($minutes = 0; $minutes < 60; $minutes+=10)
        $time_dropdown.=',"'.sprintf("%02d:%02d",$hours,$minutes).'"';





$current_user_id = $_SESSION['user_id'];

//$user_det = get_user_details($current_user_id);
//$full_name = $user_row['full_name'];


$user_result = $mysqli->query("SELECT u.work_location, a.group_access,a.emp_location_access,a.emp_division_access,a.user_access,a.user_not_access,a.emp_location_not_access FROM tbl_users u left join tbl_user_access a on  u.user_id=a.user_id  where u.status='Active' and is_regular=1 and module_access='main_module' and u.user_id=$current_user_id");

$user_row = $user_result->fetch_assoc();
$group_access = $user_row['group_access'];
$emp_location_access = $user_row['emp_location_access'];
$emp_division_access = $user_row['emp_division_access'];
$user_access = $user_row['user_access'];
$user_not_access = $user_row['user_not_access'];
$emp_location_not_access = $user_row['emp_location_not_access'];
$sql_access = "SELECT GROUP_CONCAT(user_id) division_head_access FROM  tbl_users WHERE division_head='".$current_user_id."'";

$access_query = $mysqli->query($sql_access);
$access_row = $access_query->fetch_assoc();
$user_under_division_head = $access_row['division_head_access'];
$user_location_id = $user_row['work_location'];









$query = "SELECT full_name,user_id FROM tbl_users where status='Active' and is_regular=1 and  (user_id=$current_user_id ";
if ($group_access != "")
    $query.=" or group_id in  ($group_access)  ";
if ($emp_location_access != "")
    $query.=" or work_location in  ($emp_location_access)  ";
if ($emp_division_access != "")
    $query.=" or emp_division_id in  ($emp_division_access)  ";
if ($user_access != "")
    $query.=" or user_id in  ($user_access)  ";
if ($user_under_division_head != "")
    $query.=" or user_id in  ($user_under_division_head)  ";
$query.=") ";
if ($user_not_access != "")
    $query.=" and( user_id not in  ($user_not_access) ) ";
if ($emp_location_not_access != "")
    $query.=" and( work_location not in  ($emp_location_not_access) ) ";
$query.=" order by username";
$audit_result = $mysqli->query($query);

$sql = "SELECT *   FROM tbl_emp_duties_master WHERE user_id = '$user_id'   ";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
$lock = $row['is_locked'];
if ($lock != 0 &&$lock != 3) {
    $editable = 0;
} else {
    $editable = 1;
}//echo $editable;exit;

//SAVE LOCK------------------------------------------------------------------*/
if (isset($_POST['save_lock'])) {
  $completion_result = $mysqli->query("SELECT id FROM tbl_emp_duties_master WHERE user_id='$user_id' ");
$completion_row = $completion_result->fetch_assoc();
    if ($completion_row['id'] > 0)
       	$result = $mysqli->query("update tbl_emp_duties_master set is_locked=1,locked_date='$current_time' WHERE user_id='$user_id' ");
     else	
	$result = $mysqli->query("INSERT INTO  tbl_emp_duties_master(`user_id`, `added_date`,`is_locked`,`locked_date`)  values ('$user_id','$current_time','1','$current_time')");
     //Locked mail notifctn sending  
       $user_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$user_id");
    $user_row = $user_info_query->fetch_assoc();
    $head_id = $user_row['division_head']; //echo "select * from tbl_users user where user.user_id=$head_id";exit;
    $head_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$head_id");
    $head_row = $head_info_query->fetch_assoc();
       $to =$head_row['email'];// "saranyasasi.biztv@gmail.com";//$head_row['email'];
    $cc = $user_row['email'];

   $message .= "<b>Dear " . $head_row['full_name'] . "</b>,<br><br>";
    $message .= " The below employee's Duties and Responsibilities are locked. <br><br>";
    $message.="<table style='background-color:#ffffff;border:1px solid #c3c3c3;border-collapse:collapse;width:70%; padding:3'>
				<tbody><tr>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Subordinate Name</th>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Locked Date</th>
                                       

				</tr>
				<tr>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $user_row['full_name'] . "</td>
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


                                                    $mail->Subject = "Duties and Responsibilities Locked";
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

/*****UN LOCK REQUEST SENDING*******/
if (isset($_POST['un_lock'])) {
  $completion_result = $mysqli->query("SELECT id FROM tbl_emp_duties_master WHERE user_id='$user_id' ");
$completion_row = $completion_result->fetch_assoc();
    if ($completion_row['id'] > 0)
       	$result = $mysqli->query("update tbl_emp_duties_master set is_locked=2,unlock_submit_date='$current_time' WHERE user_id='$user_id' ");
    
    //Locked mail notifctn sending  
       $user_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$user_id");
    $user_row = $user_info_query->fetch_assoc();
    $head_id = $user_row['division_head']; //echo "select * from tbl_users user where user.user_id=$head_id";exit;
    $head_info_query = $mysqli->query("select * from tbl_users user where user.user_id=$head_id");
    $head_row = $head_info_query->fetch_assoc();
       $to =$head_row['email'];// "saranyasasi.biztv@gmail.com";//$head_row['email'];
    $cc = $user_row['email'];

   $message .= "<b>Dear " . $head_row['full_name'] . "</b>,<br><br>";
    $message .= " The below employee had requested to unlock his Duties and Responsibilities <br><br>";
    $message.="<table style='background-color:#ffffff;border:1px solid #c3c3c3;border-collapse:collapse;width:70%; padding:3'>
				<tbody><tr>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Subordinate Name</th>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Locked Date</th>
                                       

				</tr>
				<tr>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $user_row['full_name'] . "</td>
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


                                                    $mail->Subject = "Duties and Responsibilities Unlock Request";
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
    <script src="javascript/js.js" type="text/javascript"></script>


    <script language="javascript">

        function check_form(form, type, status)
        {
            var r = confirm("Are you sure you want to lock your D&R?")
            if (r == true)
            {

                return true;
            }
            else
            {
                return false;
            }
        }

  function unlock_confirm(form, type, status){
     var r = confirm("Are you sure you want to ulcok your D&R?")
            if (r == true)
            {

                return true;
            }
            else
            {
                return false;
            }
}

        function auto_save(plan_id, type)
        {

            description = document.getElementById('description' + plan_id).value;


            var s = document.getElementById('status' + plan_id);
            var status = s.options[s.selectedIndex].value;



            var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

            request_save.open("POST", "php/monthly_auto_save.php", true);

            request_save.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            request_save.onreadystatechange = function () {
                if (request_save.readyState == 4)
                {
                    if (request_save.responseText == "Logout")
                    {
                        alert("Your Session has been Expired Please Login Again");
                        window.location.href = 'index.php';


                    }
                    else
                    {



                    }


                }
            }
            request_save.send('plan_id=' + plan_id + '&description=' + description + "&status=" + status);
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
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  style="background-image:url(images/themes/<?php echo rand(5,14);?>.jpg); background-repeat:repeat-x;">
    <form name="jobform" id="jobform" method="POST" action="duty_entry_latest.php" enctype="multipart/form-data" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0"
               align="center">
                   <?php
                   include("includes/header.php");
                   ?>
            <tr>

                <td>
                    <br>
                    <br>


                    <table id="customers" border="0" align="center" cellpadding="0" cellspacing="0"
                           width="90%" class="form_style">

                        <tr height="25">
                            <td width="150%"  colspan="3"  align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;">
                                <b>
                                    <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;DUTIES AND RESPONSIBILITIES OF  <?=$_SESSION['full_name']?></font></b> 

                            </td>  
                            <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;">
                                <?php if ($lock==0){ ?>
                                <input id="complete_button" onClick="return check_form();" type="submit" class="btn btn-primary btn-xl" name="save_lock" value="LOCK">   
                                <?php } 
                                else if ($lock==2){ ?>
                                <input   type="button" class="btn btn-success btn-xl"  value="Unlock Request Sent">   
                                <?php }
                                else if ($lock==3){ ?>
                                <input   type="submit" class="btn btn-info btn-xl"  onClick="return check_form();" name="save_lock" value="Lock Again"> 
                                <?php } else{ ?>
                                <input id="complete_button2" onClick="return unlock_confirm();" type="submit" class="btn btn-warning btn-xl" name="un_lock" value=" Unlock Request Sent">   

                                <?php }?>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <table border="0" align="center" cellpadding="0" cellspacing="0"
                           width="90%">
                        <tr height="25">
                            <?php if ($editable==1) {?>
                                <td>
                                    <div id="example1" style="width:100%"></div>
                                </td>
                            <?php } else { ?>
                                <td width="90%" align="center" style="border:#000000 solid 1px; padding-left:1px;" align="center">
                                      <?php include_once 'duty_view.php'; ?>  
                                </td>
                            <?php }?>

                        </tr>
                    </table>

                    <div style="width:100%;text-align:center;" ><br><br><br><br><br><br><br><br><br><br><br><br><br></div>
                    <script>
                        var $container = $("#example1");
                        var $console = $("#exampleConsole");

                        var res = <?php echo $duty_json;?>;

                        //   res=[{"id":"1","manufacturer":"abcl","year":"2001","price":"100"},{"id":"2","manufacturer":"efgh","year":"2002","price":"101"}];
                        var data = [], row;
                        for (var i = 0, ilen = res.length; i < ilen; i++) {
                            row = [];

                            row[0] = res[i].duty;
                            row[1] = res[i].hand_over;
                            row[2] = res[i].frequency;
                            row[3] = res[i].approx_time;
                            row[4] = res[i].remarks;




                            data[res[i].row_id - 1] = row;
                        }
                        var $parent = $container.parent();
                        $container.handsontable({
                            startRows: 8,
                            data: data,
                            stretchH: 'all',
                            columns: [
                                {data: "0", type: 'text'},
                                {data: "1", type: 'text'},
                                {data: "2",
                                    options: {items: 27},
                                    type: 'autocomplete',
                                    source: ["Daily", "Weekly", "Monthly", "Quarterly", "Half Yearly", "Yearly"]

                                },
                                {data: "3",
                                    type: 'autocomplete',
                                    options: {items: 50},
                                    source: [<?php echo $time_dropdown?>]
                                },
                                {data: "4", type: 'text'}
                            ],
                            startCols: 1,
                            rowHeaders: true,
                            colWidths: [200, 100, 100, 100, 200],
                            stretchH: 'all',
                                    rowHeight: 15,
                            colHeaders: ['Job Details', 'Handover during Leave', 'Frequency of Task', 'Approx. Time for Each Task', 'Remarks'],
                            minSpareCols: 0,
                            minSpareRows: 1,
                            afterChange: function (change, source) {
                                if (source === 'loadData') {
                                    return; //don't save this change
                                }

                                $.ajax({
                                    url: "ajax/duty_save.php?user_id=<?php echo $user_id?>",
                                    dataType: "json",
                                    type: "POST",
                                    data: {changes: change}, //contains changed cells' data
                                    success: function (data) {

                                        if (data.logout == 'Yes')
                                        {
                                            alert("Your Session has been Expired Please Login Again");
                                            window.location.href = 'index.php';
                                        }

                                    }
                                });
                            }
                        });
 function click_tree(user_id)
                        {

                            document.location.href = 'view_status.php?employee_id=' + user_id + "&date_report=<?php echo $date_report ?>";

                        }
                    </script>
 
                </td>
            </tr>
            <?php include("includes/footer.php");?>
        </table>
    </form>

    <link type="text/css" rel="stylesheet" href="css/jquery-ui-1.8.9.custom/jquery-ui-1.8.9.custom.css" />
    <script type="text/javascript" src="jquery-1.4.3.min.js"></script>
    <script type="text/javascript" src="jquery-ui-1.8.13.custom.min.js"></script>
    <script type="text/javascript" src="jquery.multi-accordion-1.5.3.js"></script>
    <style type="text/css">
        /*demo page css*/
        body{ font: 62.5% "Trebuchet MS", sans-serif; }
    </style>

</body>
</html>