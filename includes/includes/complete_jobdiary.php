<?php
$main_type_array = array();
$main_type_array[0] = '';
$main_type_sql = "select * from   tbl_main_type order by main_type_id";
$main_type_result = $mysqli->query($main_type_sql);
while ($main_type_row = $main_type_result->fetch_assoc()) {
    $main_type_array[$main_type_row['main_type_id']] = $main_type_row['main_type_name'];
}

/* Edit job diary code starts here */
if (!empty($_GET['edit']) && $_GET['edit'] == 'TRUE') {
    /* delet routine jobs from workreports table */
    $sql = "DELETE FROM tbl_workreports WHERE user_id = '$user_id' AND date_report = '$sql_date' AND is_carry = '3'";
    $result = $mysqli->query($sql);

    /* get carry forward jobs id */
    $sql = "SELECT workreport_id FROM tbl_workreports
				WHERE user_id = '$user_id' AND date_report = '$sql_date'
					AND cf_date != '0000-00-00' AND status < '100'";
    $result = $mysqli->query($sql);

    /* delete carry forward jobs */
    while ($row = $result->fetch_assoc()) {
        $sql = "DELETE FROM tbl_workreports WHERE cf_work_id = '" . $row['workreport_id'] . "'";
        $mysqli->query($sql);
    }

    /* delete efficiency calculation figures from tbl_efficiency_calculation */
    $sql = "DELETE FROM tbl_efficiency_calculation WHERE user_id = '$user_id' AND work_date = '$sql_date'";
    $mysqli->query($sql);

    /* update number of edit time sheet result */
    $sql = "UPDATE tbl_time SET no_of_edit = no_of_edit + 1, is_complete = '0'
				WHERE user_id = '$user_id' AND date_log = '$sql_date'";
    $mysqli->query($sql);

    header('Location:jobdiary.php?date=' . $date);
}
/* Edit job diary code starts ends here */

  $time_query = "SELECT not_punctual as not_punctual_value,travel,friend,family,late_remarks,
 time_format(ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i'))
,TIME_FORMAT(nwt, '%k:%i')),extra_break),TIME_FORMAT(home,'%k:%i')),TIME_FORMAT(night, '%k:%i')),'%H:%i') as net_time,

time_to_sec(ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i'))
,TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(home,'%k:%i')),TIME_FORMAT(night, '%k:%i'))) as net_time_sec,

  work_status,IF(DATE_FORMAT(time_out,'%H:%i')!='00:00',time_out,'') as time_out,IF(DATE_FORMAT(time_in,'%H:%i')!='00:00',time_in,'') as time_in,IF(DATE_FORMAT(nwt,'%H:%i')!='00:00',nwt,'') as nwt,IF(DATE_FORMAT(home,'%H:%i')!='00:00',home,'') as home,IF(DATE_FORMAT(night,'%H:%i')!='00:00',night,'') as night,IF(DATE_FORMAT(outoffice,'%H:%i')!='00:00',outoffice,'') as outoffice,IF(DATE_FORMAT(health,'%H:%i')!='00:00',health,'') as  health,IF(DATE_FORMAT(sleep,'%H:%i')!='00:00',sleep,'') as  sleep,system_ok,condition_remarks,remarks,location,is_complete,(not_punctual*5) as not_punctual,(unplan*5) as unplan,(no_of_edit*5) as no_of_edit,
IF(DATE_FORMAT(extra_break,'%H:%i')!='00:00',extra_break,'') as extra_break,  
(no_health*2) as no_health,IF(system_ok<=70,5,0) as system_error,(current_efficiency*100)as current_efficiency FROM tbl_time where user_id=$user_id and date_log='" . $sql_date . "'";
$time_result = $mysqli->query($time_query);
$time_row = $time_result->fetch_assoc();

$location = $time_row['location']; //print_r($time_row);exit;

$nwt = $time_row['nwt'];
$nwt_array = explode(":", $nwt);
$nwt_hr = $nwt_array[0];
$nwt_min = $nwt_array[1];

$work_status = $time_row['work_status'];
$home = $time_row['home'];
$home_array = explode(":", $home);
$home_hr = $home_array[0];
$home_min = $home_array[1];
if ($home_hr == '') {
    $home_hr = '00';
    $home_min = '00';
}

$extra_break = $time_row['extra_break'];
$extra_break_array = explode(":", $extra_break);
$extra_break_hr = $extra_break_array[0];
$extra_break_min = $extra_break_array[1];
if ($extra_break_hr == '') {
    $extra_break_hr = '00';
    $extra_break_min = '00';
}

$not_punctual_value = $time_row['not_punctual_value'];
$late_remarks = $time_row['late_remarks'];
$net_time = $time_row['net_time'];

$health = $time_row['health'];
$health_array = explode(":", $health);
$health_hr = $health_array[0];
$health_min = $health_array[1];
if ($health_hr == '') {
    $health_hr = '00';
    $health_min = '00';
}

$friend = $time_row['friend'];
$friend_array = explode(":", $friend);
$friend_hr = $friend_array[0];
$friend_min = $friend_array[1];
if ($friend_hr == '') {
    $friend_hr = '00';
    $friend_min = '00';
}
$travel = $time_row['travel'];
$travel_array = explode(":", $travel);
$travel_hr = $travel_array[0];
$travel_min = $travel_array[1];
if ($travel_hr == '') {
    $travel_hr = '00';
    $travel_min = '00';
}
$family = $time_row['family'];
$family_array = explode(":", $family);
$family_hr = $family_array[0];
$family_min = $family_array[1];
if ($family_hr == '') {
    $family_hr = '00';
    $family_min = '00';
}

$sleep = $time_row['sleep'];
$sleep_array = explode(":", $sleep);
$sleep_hr = $sleep_array[0];
$sleep_min = $sleep_array[1];
if ($sleep_hr == '') {
    $sleep_hr = '00';
    $sleep_min = '00';
}

$outoffice = $time_row['outoffice'];
$outoffice_array = explode(":", $outoffice);
$outoffice_hr = $outoffice_array[0];
$outoffice_min = $outoffice_array[1];

$night = $time_row['night'];
$night_array = explode(":", $night);
$night_hr = $night_array[0];
$night_min = $night_array[1];
if ($night_hr == '') {
    $night_hr = '00';
    $night_min = '00';
}

$system_ok = $time_row['system_ok'];
$condition_remarks = $time_row['condition_remarks'];
$net_time_sec = $time_row['net_time_sec'];
$remarks = $time_row['remarks'];
//$work_status=$time_row['work_status'];
$timein = $time_row['time_in'];
$timeout = $time_row['time_out'];
$current_efficiency = $time_row['current_efficiency'];
$timein_array = explode(":", $timein);
$timeout_array = explode(":", $timeout);
$timein_hr = $timein_array[0];
$timein_min = $timein_array[1];
$timeout_hr = $timeout_array[0];
$timeout_min = $timeout_array[1];
$not_punctual = $time_row['not_punctual'];
$unplan = $time_row['unplan'];
$no_health = $time_row['no_health'];
$no_of_edit = $time_row['no_of_edit'];
$system_error = $time_row['system_error'];
$net_efficiency = $current_efficiency - $not_punctual - $unplan - $no_health;
if ($timein != '') {
    if ($timein_hr > 12) {
        $ampm_in = "PM";
        $timein_hr = $timein_hr - 12;
        $timein_hr = sprintf("%02s", $timein_hr);
    } else {
        if ($timein_hr == 12)
            $ampm_in = "PM";
        else
            $ampm_in = "AM";
    }
} else
    $ampm_in = "AM";


if ($timeout != '') {
    if ($timeout_hr > 12) {

        $ampm_out = "PM";
        $timeout_hr = $timeout_hr - 12;
        $timeout_hr = sprintf("%02s", $timeout_hr);
    } else {
        if ($timeout_hr == 12)
            $ampm_out = "PM";
        else
            $ampm_out = "AM";
    }
} else
    $ampm_out = "PM";
$delegate_array = array("1" => "<font style='color:#FF0000;font-size:10px;font-weight:bold'>Delegation</font>", "2" => "<font style='color:#000000;font-size:10px;font-weight:bold'>Sharing</font>", "3" => "<font style='color:#0000FF;font-size:10px;font-weight:bold'>Requisition</font>", "4" => "<font style='color:#9900CC;font-size:10px;font-weight:bold'>Proposal</font>");
$sql = "SELECT DATE_FORMAT(w.job_est,'%H:%i') as  job_est, w.is_carry,d.delegate_type,w.workreport_id, w.taskname ,w.main_type,w.job_type,w.description ,w.job_no,w.client,IF(w.target_date='0000-00-00', '',DATE_FORMAT(w.target_date,'%d-%m-%Y'))  as  target_date,w.status,DATE_FORMAT(w.est_time,'%H') as  est_time_hr,DATE_FORMAT(w.est_time,'%i') as est_time_min,DATE_FORMAT(w.act_time,'%H') as  act_time_hr,DATE_FORMAT(w.act_time,'%i')act_time_min,d.assigned_by,d.is_reopen,d.reopen_remarks,IF(w.cf_date='0000-00-00', '',DATE_FORMAT(w.cf_date,'%d-%m-%Y'))  as  cf_date,u.username,w.eff_ratio from tbl_workreports w left join tbl_delegation d on d.delegation_id=w.delegation_id left join tbl_users u on u.user_id=d.assigned_by where w.user_id='$user_id' and d.assigned_by>0 and  w.date_report='$sql_date';";
$sql .= "SELECT  workreport_id,taskname ,w.main_type,w.job_type,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date,status,DATE_FORMAT(est_time,'%H') as  est_time_hr,DATE_FORMAT(est_time,'%i') as est_time_min,DATE_FORMAT(act_time,'%H') as  act_time_hr,DATE_FORMAT(act_time,'%i')act_time_min,IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,DATE_FORMAT(job_est,'%H:%i') as  job_est,DATE_FORMAT(prev_act,'%H:%i') as  prev_act,eff_ratio,is_carry from tbl_workreports w  where user_id='$user_id' and  delegation_id=0 and  (is_carry=1 or is_carry=2) and  date_report='$sql_date';";
$other = array();
$carry = array();
$active = 'other';
/* execute multi query */
if ($mysqli->multi_query($sql)) {
    do {
        /* store result set */
        if ($result = $mysqli->store_result()) {
            while ($row = $result->fetch_assoc())
                ${$active}[] = $row;
            $result->close();
        }
        /* next set of results */
        if ($mysqli->more_results()) {
            $active = 'carry';
        }
    } while ($mysqli->next_result());
}
$share_sql = "SELECT  workreport_id,taskname , job_type_name,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date,status,DATE_FORMAT(est_time,'%H') as  est_time_hr,DATE_FORMAT(est_time,'%i') as est_time_min,DATE_FORMAT(act_time,'%H') as  act_time_hr,DATE_FORMAT(act_time,'%i')act_time_min,assigned_by,IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,eff_ratio from tbl_workreports w left join tbl_job_type t on w.job_type=t.id where user_id='$user_id' and  assigned_by=0 and  delegate_type=2 and date_report='$sql_date';";
$share_result = $mysqli->query($share_sql);
$excel_query = "SELECT  if(un_sch=1,'true','false') as  un_sch,workreport_id, main_type,row_id,taskname,  job_type_name,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date, IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,w.status, IF(DATE_FORMAT(est_time,'%H:%i')!='00:00',DATE_FORMAT(est_time,'%H:%i'),'') as est_time,IF(DATE_FORMAT(act_time,'%H:%i')!='00:00',DATE_FORMAT(act_time,'%H:%i'),'') as act_time,IF(DATE_FORMAT(job_est,'%H:%i')!='00:00',DATE_FORMAT(job_est,'%H:%i'),'') as job_est,eff_ratio  from tbl_workreports w left join tbl_job_type t  on w.job_type=t.id  where is_carry=0 and  date_report='$sql_date'  and user_id=$user_id and delegation_id=0 order by row_id desc";
$excel_result = $mysqli->query($excel_query);
$daily_rows = array();
$i = 1;
$extra = 0;
while ($r = $excel_result->fetch_assoc()) {
    $r['id'] = $i++;
    $r['main_type_name'] = $main_type_array[$r['main_type']];
    $daily_rows[] = $r;
}
$excel_result->free();
$test = json_encode($daily_rows);
$start = strtotime('12am');
$tod = $start;
$est_act_source = '"' . date('H:i', $tod) . '"';
for ($i = 1; $i < (100 * 5); $i++) {
    $tod = $start + ($i * 5 * 60);
    $est_act_source .= ',"' . date('H:i', $tod) . '"';
}
$job_est = "";
$job_est = '"00:00"';
for ($hours = 0; $hours <= 100; $hours++)
    for ($minutes = 0; $minutes < 60; $minutes += 30)
        $job_est .= ',"' . sprintf("%02d:%02d", $hours, $minutes) . '"';
$routine_result = $mysqli->query(" SELECT s.remarks,j.id,j.job_name,j.main_type,j.sub_type,j.approved_by,j.auth_status,

							if(s.id>0,DATE_FORMAT(s.est_time,'%H'),DATE_FORMAT(j.est_time,'%H')) as est_time_hr,
							if(s.id>0,DATE_FORMAT(s.est_time,'%i'),DATE_FORMAT(j.est_time,'%i')) as est_time_min,
							DATE_FORMAT(s.act_time,'%H') as  act_time_hr,DATE_FORMAT(s.act_time,'%i') as act_time_min,
							s.status FROM tbl_daily_jobs j  left join tbl_daily_job_status s
								on j.id=s.job_id and s.job_date='$sql_date' where j.status=1 and  j.user_id=$user_id")
or die('Error, query failed');
while ($row = $routine_result->fetch_assoc())
    $routine[] = $row;
if ($time_row['is_complete'] == 1) {
    $result = $mysqli->query("SELECT round(100*(sum(time_to_sec(act_time)*eff_ratio))/(time_to_sec(TIMEDIFF(TIMEDIFF(time_out,time_in),nwt))),2) as efficiency  FROM tbl_time t left join tbl_workreports w on w.date_report=t.date_log  and w.user_id=t.user_id WHERE t.date_log = '$sql_date' AND t.user_id = $user_id group by t.date_log");
    $row = $result->fetch_assoc();
    $eff_ratio = $row['efficiency'];
}

/* job diary is editable is next day job diary is not completed. */
$editable = true;
$next_day = date('Y-m-d', strtotime('+1 day', strtotime($sql_date)));
$sql = "SELECT COUNT(*) count FROM tbl_time WHERE user_id = '$user_id' AND is_complete = '1' AND date_log = '$next_day'";
$result1 = $mysqli->query($sql);
$row = $result1->fetch_assoc();
if ($row['count'] != '0') {
    $editable = false;
}
//ini_set('display_errors',1);
$year = date('Y', strtotime($_REQUEST['date']));
$month = date('n', strtotime($_REQUEST['date']));
/* TOTAL WORKING DAYS */
$TOTAL_WKING_DAYS = $mysqli->query("select SUM(TIME_TO_SEC(ADDTIME(SUBTIME(ADDTIME(ADDTIME(TIMEDIFF(time_out,time_in),
home),night),nwt),leave_hours))) as whours,work_status,count(work_status) as sts from
tbl_time t WHERE YEAR(t.date_log) = $year AND MONTH(t.date_log) = $month AND t.user_id = $user_id  and t.is_complete=1 group by work_status");
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
    <link rel="stylesheet" href="css/circle.css">
    <script src="javascript/js.js" type="text/javascript"></script>
    <script language="javascript">
        function openWindow(url, title)
        {
            var left = (screen.width - 900) / 2;
            var top = (screen.height - 500) / 2;
            return window.open(url, title, 'width=900,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes,location=no');
        }
        from_page = 'jobdiary';
        function check_form(form, type, status) {
        }
    </script>

    <style>
        #customers {font-family:"Trebuchet MS", Arial, Helvetica, sans-serif;border-collapse:collapse;}
        #customers td, #customers th {font-size:10px;border:1px solid #6688AD;padding:2px;}
        #customers th {	font-size:1.1em;text-align:left;padding-top:5px;padding-bottom:4px;background-color:#A7C942;color:#ffffff;}
        #customers tr.alt td {color:#000000;background-color:#E8EFF5;}
        #time_logCom td{ vertical-align: top;background:#e2f3fd;border:1px solid #008080;}
        #time_logCom{ background:#fff;}
        .calendar{ z-index:100;}
    </style>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  style="background-image:url(images/themes/<?php echo rand(5, 14); ?>.jpg); background-repeat:repeat-x;">
    <form name="jobform" method="POST" action="jobdiary.php" enctype="multipart/form-data" >
        <?php if ($time_row['is_complete'] == 1) { ?>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
                <?php include("includes/header.php"); ?>
                <tr><td><br/>
                        <table id="customers" style="border:0px;" border="0" align="center" cellpadding="0" cellspacing="0" width="95%" class="form_style">

                            <tr height="25" style="border:0px solid #ffffff;">
                                <td width="25%"  align="left" bgcolor="#ffffff" style="color:#ffffff">&nbsp;<b style="vertical-align: middle">Date:</b>
                                    <input type="text" name="date" style="vertical-align: middle" size="10" id="date_from" readonly value="<?php echo $date ?>" onClick="return showCal('date_from', 'dd-mm-y');">
                                    <img title="Calendar" style="vertical-align: middle;" src="images/calendar0.gif" onClick="return showCal('date_from', 'dd-mm-y');" border="0">
                                    <?php
                                    $previous_month = date("m", strtotime('-1 month'));
                                    $previous_year = date("Y", strtotime('-1 month'));
                                    if (((date("Y") == date("Y", strtotime($sql_date))) && (date("m") == date("m", strtotime($sql_date))) && $editable) || (($previous_year == date("Y", strtotime($sql_date))) && ($previous_month == date("m", strtotime($sql_date)))) && $editable) {
                                        ?>
                                        <a style="padding:5px;margin:12px;" class="jobdiary_buttons" href="jobdiary.php?date=<?php echo $date ?>&edit=TRUE">
                                            Edit Jobdiary
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td colspan="2" width="75%"><table id="time_logCom" style="vertical-align:top;" width="100%">
                                        <tr><td><b>Time In</b></td><td><b>Time Out</b></td>
                                            <td><b>Break Time</b></td>
                                              <td><b>Extra Break Time</b></td>
                                            <td><b>Home</b></td>
                                            <td><b>Mid Night</b></td>
                                            <td><b>Net</b></td><td><b>Friends</b></td><td><b>Family</b></td><td><b>Travel</b></td><td><b>Health</b></td><td><b>Sleep</b></td></tr>
                                        <tr><td><strong><font style="color:#0000FF" size="-1"><?php echo $timein_hr . ":" . $timein_min . " " . $ampm_in ?></font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $timeout_hr . ":" . $timeout_min . " " . $ampm_out; ?></font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $nwt_hr . ":" . $nwt_min; ?>  Hrs</font></strong> </b></td>
                                             <td><strong><font style="color:#0000FF" size="-1"><?php echo $extra_break_hr . ":" . $extra_break_min;?>  Hrs</font></strong> </b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $home_hr . ":" . $home_min; ?> Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $night_hr . ":" . $night_min; ?> Hrs</font></strong> </b></td>
                                            <td><strong><font style="color:#FF0000" size="-1"><?php echo $net_time; ?>  Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $friend_hr . ":" . $friend_min; ?> Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $family_hr . ":" . $family_min; ?> Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $travel_hr . ":" . $travel_min; ?> Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $health_hr . ":" . $health_min; ?> Hrs</font></strong></b></td>
                                            <td><strong><font style="color:#0000FF" size="-1"><?php echo $sleep_hr . ":" . $sleep_min; ?> Hrs</font></strong></b></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr height="25" style="background:#fff;">
                                <?php //if($net_time_sec>=21600){  ?>
                                <td width="25%  align="left" bgcolor="" style="padding-left: 8px;font-size:18px;vertical-align:top"><br/>
                                    <?php if ($net_time_sec >= 21600) { ?>
                                        <div style="height:110px;width:115px;float:left;">
                                            <strong style="font-size:12px;">Daily Efficiency</strong>
                                            <div class="c100 p<?php echo round($current_efficiency) ?> small green" style="float:left;" >
                                                <span><?php echo $current_efficiency ?>%</span></a>
                                                <div class="slice">
                                                    <div class="bar"></div>
                                                    <div class="fill"></div>
                                                </div>
                                            </div>
                                        </div><?php
                                    }
                                    if (!empty($_REQUEST['date'])) {
                                        /* for monthly plan submmission form */
                                        $year = date('Y', strtotime($_REQUEST['date']));
                                        $month = date('n', strtotime($_REQUEST['date']));
                                        $last_day = date('d', strtotime('last day of this month', strtotime($_REQUEST['date'])));
                                        $day = date('d', strtotime($_REQUEST['date']));
                                        if ($last_day == $day) {
                                            $sql_data = " SELECT ef.description,SUM(ef.weightage) act_weight,SUM(ec.weightage) obtained_weightage
							FROM tbl_efficiency_parameters ef
							LEFT JOIN  tbl_monthly_efficiency_calculation ec
							ON ef.pName = ec.param_name
							AND month = $month
							AND year = $year
							AND user_id = '" . $_SESSION['user_id'] . "'
							WHERE type='ML'
							ORDER BY ef.sort_order";
                                            $result = $mysqli->query($sql_data);
                                            $row_weight = $result->fetch_assoc();
                                            ?><div style="height:110px;width:112px;float:left;">
                                                <strong style="font-size:12px;">Monthly Efficiency</strong><br/><div class="c100 p<?php print(round(($row_weight['obtained_weightage'] / $row_weight['act_weight']) * 100)) ?> small" style="float:right;" id="progress-bar">
                                                    <span><?php print(round(($row_weight['obtained_weightage'] / $row_weight['act_weight']) * 100, 1)) ?>%</span></a>
                                                    <div class="slice">
                                                        <div class="bar"></div>
                                                        <div class="fill"></div>
                                                    </div>
                                                </div><?php
                                            }
                                        }
                                        ?>
                                    </div><br>


                                    <div><font style="color:#000;font-size:14px;" size=""><strong>Day:</strong><?php echo strtoupper($work_status); ?> </font><br/>
                                        <font style="color:#000;font-size:14px;" size=""><strong>Location:</strong><?php echo $location ?> </font><br/>
                                        <font style="color:#F00" size=""><strong>Remarks:</strong><?php echo $remarks ?> </font></div>

                                </td>
                                <?php if ($net_time_sec >= 21600) { ?>

                                    <td width="60%">
                                        <a style="float:right;margin-top:-1px;background: #008080;color: #fff;padding:3px;" title="View All"
                                           href="efficiency_graphNew.php?sql_date=<?= $sql_date ?>&user_id=<?= $user_id ?>">All Graph</a>
                                        <!--<iframe frameborder="0" scrolling="no" width="100%" src="new_design/canvas/index.php"></iframe>-->
                                        <div id="chartContainer" style="height: 150px; width: 90%;"></div>
                                    </td>
                                <?php } ?>
                                <td width="15%" align="left" style="text-align:center;vertical-align:top;">
                                    <table border="0" width="99%" style="height:150px;border:1px solid #008080">
                                        <?php
                                        while ($working_days = $TOTAL_WKING_DAYS->fetch_assoc()) {
                                            $whours = $whours + $working_days['whours'];
                                            $total_days_month = $total_days_month + $working_days['sts'];
                                            $summary[$working_days['work_status']] = $working_days['sts'];
                                        }
                                        ?>
                                        <tr><td style="border:0px">Worked Days:<strong style="font-size:13px;color:#008a8a;margin-left:1px;">
                                                    <?php echo $summary['work']; ?></strong> </td></tr>
                                        <tr><td style="border:0px">Leave Days:<strong style="font-size:13px;color:#008a8a;margin-left:1px;">
                                                    <?php echo $summary['leave']; ?></strong> </td></tr>
                                        <tr><td style="border:0px"> HoliDays:<strong style="font-size:13px;color:#008a8a;margin-left:1px;">
                                                    <?php echo $summary['holiday']; ?></strong> </td></tr>
                                        <tr><td style="border:0px"> Worked Hours:<strong style="font-size:13px;color:#008a8a;margin-left:1px;">
                                                    <?php echo round($whours / 3600, 2); ?></strong> </td></tr>
                                        <tr><td style="border:0px;"> Total Days:<strong style="font-size:13px;color:#008a8a;margin-left:1px;">
                                                    <?php echo $total_days_month; ?></strong></td></tr>
                                        <tr><td style="border:0px;"> <a href="dashboard" style="background: #008080;color: #fff;padding: 3px;" target="_blank">View Summary</a>
                                            </td></tr>
                                    </table>
                                </td>
                            </tr>
                            <?php
                            //echo $sql_date.$net_time_sec; exit; //
                            if ($net_time_sec >= 21600) {
                                ?>
                                <?php if ($sql_date > '2015-02-08') { // 2015-02-08 //2016-02-08?>
                                    <tr height="25">
                                        <?php
                                        $ec_resultALL['total_efficiency'] = '100';
                                        $query = "SELECT * FROM tbl_efficiency_calculation WHERE work_date = '$sql_date' and user_id = $user_id;";
                                        $ec_re = $mysqli->query($query);
                                        $ec_result = $ec_re->fetch_assoc();
                                        unset($ec_result['calId']);
                                        unset($ec_result['user_id']);
                                        unset($ec_result['work_date']);
                                        unset($ec_result['emp_div_id']);
                                        unset($ec_result['completed_date']);
                                        unset($ec_result['actual_efficiency']);
                                        unset($ec_result['status']); //print_r($ec_result); exit;
                                        $ic = 0; //print_r( $ec_result);
                                        $query1 = "select pName,weightage from tbl_efficiency_parameters where type='';";
                                        $ec_re1 = $mysqli->query($query1);
                                        while ($ec_result1 = $ec_re1->fetch_assoc()) {
                                            $ec_resultALL[$ec_result1['pName']] = $ec_result1['weightage'];
                                        }//print_r($ec_resultALL);
                                        ?>

                                        <td colspan="7" align="left"  style="vartical-align:top;padding:0px;border:0px;">&nbsp;
                                            <table bgcolor="#e9e9e9" border="1" style="width:100%;border-collapse: collapse;">
                                                <tr style="border:1px solid #a6107b" bgcolor="#e2f3fd">
                                                    <td colspan="5" style="vertical-align:top;border:1px;color:#a6107b;padding:5px;font-size:15px;text-align: center"><u><b>Parameters</b></u>
                                                        <a href="javascript:void(0);" onclick="openWindow('includes/complete_jobdiary_parameter_graph.php?sql_date=<?= $sql_date ?>');" style="background: #008080;color: #fff;padding: 3px;" title="View In  Graph" >View In Graph</a>
                                                        <a class="pull-right" style="background: #008080;color: #fff;padding: 3px;float:right" href="javascript:void(0);" onclick="openWindow('new_design/canvas/', 'Parameter Description');">Efficiency Evaluation ?</a>
                                                    </td></tr>
                                                <?php
                                                foreach ($ec_result as $k => $v) {
                                                    $ic++;
                                                    if ($ic % 5 == 1) {
                                                        ?> </tr><tr style="border:1px solid #a6107b"> <?php } ?>
                                            <td style=""><table width="99%"><tr>
                                                        <td style="width:60%;text-align: left;border:1px solid #fff;background:#fff;">
                                                            <?php
                                                            $key = strtoupper(str_replace('_', " ", str_replace('ncr', 'review', str_replace('ncr_review', 'ncr', $k))));
                                                            if ($key == 'REVIEW') {
                                                                $key = 'REVIEW NCR';
                                                            }
                                                            if ($key == 'MAJOR REVIEW') {
                                                                $key = 'MAJOR NCR';
                                                            }
                                                            if ($key == 'AVGEFFICIENCYDIVISION') {
                                                                $key = 'AVERAGE DIVISION EFFICIENCY';
                                                            }
                                                            if ($key == 'BESTEFFICIENCYWEEK') {
                                                                $key = 'BEST WEEKLY EFFICIENCY';
                                                            }
                                                            if ($key == 'SYSTEMASSESSMENT') {
                                                                $key = 'SYSTEM ASSESSMENT';
                                                            }
                                                            if ($key == 'EFFEFFISMUTILISE') {
                                                                $key = 'UTILIZE EFFISM';
                                                            }
                                                            echo $key;
                                                            ?>
                                                        </td>
                                                        <td style="border:0px;text-align: right" bgcolor="#fff" width="20%"><b><?= round($v, 2) ?></b></td>
                                                        <td style="border:0px;text-align: right" bgcolor="#fff" width="15%">(<?= $ec_resultALL[$k] ?>)</td>
                                                    </tr></table></td>
                                        <?php } ?>
                                    </tr>
                                </table>

                            </td></tr>
                    <?php } else { ?>



                        <tr><td colspan="5" align="left" bgcolor="#e9e9e9" style="padding-left: 8px;">&nbsp;</td> </tr>

                        <?php if ($unplan > 0) { ?>
                            <tr height="25">
                                <td align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Unplanned(-)%</strong></td>
                                <td align="right" bgcolor="#e9e9e9" style="padding-left: 8px;"><font style="color:#0000FF" size="+1"><strong>-<?php echo $unplan ?>%</strong></font></td>
                                <td colspan="3"   align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Efficiency Deduction: Jobs to be Planned before starting</strong></td>
                            </tr><?php } if ($not_punctual > 0) { ?>

                            <tr height="25">
                                <td  align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Not Punctual(-)%</strong></td>
                                <td  align="right" bgcolor="#e9e9e9" style="padding-left: 8px;"><font style="color:#0000FF" size="+1"><strong>-<?php echo $not_punctual ?>%</strong></font></td>
                                <td colspan="3"   align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Efficiency Deduction: Punctuality is very much important in Efficiency</strong></strong></td>
                            </tr><?php } if ($no_health > 0) { ?>

                            <tr height="25">
                                <td  align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Not Maintaing Health(-)%</strong></td>
                                <td  align="right" bgcolor="#e9e9e9" style="padding-left: 8px;"><font style="color:#0000FF" size="+1"><strong>-<?php
                                        if ($current_efficiency > 0) {
                                            echo $no_health;
                                        }
                                        ?>%</strong></font></td>
                                <td colspan="3"   align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Efficiency Deduction: Atleast 30 Minutes to be spent for your Health</strong></td>
                            </tr><?php } ?>

                        <tr height="25">
                            <td  align="left" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong>Net Efficiency:</strong></td>
                            <td  align="right" bgcolor="#e9e9e9" style="padding-left: 8px;"><strong><font style="color:#F00" size="+2"><?php echo $net_efficiency ?>%</font></strong></td>
                            <td colspan="3"   align="left" bgcolor="#e9e9e9" style="padding-left: 8px;">&nbsp;</td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
        </td></tr>
    <tr><td>



            <?php if (count($other) > 0) { ?>
                <br>
                <table id="customers" border="0"
                       style="border-color: #A6107B; border-style: solid; border-width: 1px;" align="center"
                       cellpadding="0" cellspacing="0" width="95%"
                       class="form_style">
                    <tr>
                        <td colspan="12" align="center" bgcolor="#e9e9e9"><font
                                color="#a6107b" size="2"><b>Other's Delegated To Me</b> </font></td>
                    </tr>
                    <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                        <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b> </font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b> </font></td>
                        <td width="5%"><font color="#a6107b" size="2"><b>Job No</b> </font></td>
                        <td width="8%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                        <td width="10%"><strong><font color="#a6107b" size="2">Shared
                                by</font></strong></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                            </b> </font></td>
                        <td width="7%"><font color="#a6107b" size="2"><b>Act Time<br>
                            </b> </font></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Target</b> </font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b> </font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b><font color="#a6107b" size="2">Status</font></b> </font></td>
                        <td width="18%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
                        <!--<td width="18%"><font color="#a6107b" size="2"><b>Ratio</b></font></td>-->
                    </tr>
                    <?php
                    foreach ($other as $other_row) {
                        ?>
                        <tr align="center" valign="middle" bgcolor="#fff"
                            style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                            <td><?php echo $other_row['taskname'] ?></td>
                            <td><?php echo $job_type_array[$other_row['job_type']] ?></td>
                            <td><?php echo $other_row['job_no'] ?></td>
                            <td><?php echo $other_row['client'] ?></td>
                            <td><?php echo $other_row['username'] ?></td>
                            <td><?php echo $other_row['est_time_hr'] . ":" . $other_row['est_time_min']; ?></td>
                            <td width="10%"><?php echo $other_row['act_time_hr'] . ":" . $other_row['act_time_min']; ?></td>
                            <td><?php echo $other_row['target_date'] ?></td>
                            <td><?php echo $other_row['description']; ?></td>
                            <td><?php echo $other_row['status'] ?>
                                %</td>
                            <td><?php echo $other_row['cf_date'] ?></td>
                            <!--<td><?php echo $other_row['eff_ratio'] ?></td>-->
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>

            <?php if (count($carry) > 0) { ?>
                <br>
                <table id="customers" border="0"
                       style="border-color: #A6107B; border-style: solid; border-width: 1px;" align="center"
                       cellpadding="0" cellspacing="0" width="95%"
                       class="form_style">
                    <tr>
                        <td colspan="12" align="center" bgcolor="#e9e9e9"><font
                                color="#a6107b" size="2"><b>Carry Forwarded Jobs</b></font></td>
                    </tr>
                    <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                        <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                        <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                        <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                            </b></font></td>
                        <td width="7%"><font color="#a6107b" size="2"><b>Act Time<br>
                            </b></font></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                        <td width="18%"><font color="#a6107b" size="2"><b>Status</b></font></td>
                        <td width="18%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
                        <!--<td width="18%"><font color="#a6107b" size="2"><b>Ratio</b></font></td>-->
                    </tr>
                    <?php
                    foreach ($carry as $carry_row) {
                        ?>
                        <tr align="left" valign="middle" bgcolor="#e9e9e9"
                            style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                            <td><?php echo $carry_row['taskname'] ?></td>
                            <td><?php echo $job_type_array[$carry_row['job_type']] ?></td>
                            <td><?php echo $carry_row['job_no'] ?></td>
                            <td><?php echo $carry_row['client'] ?></td>
                            <td><?php echo $carry_row['est_time_hr'] . ":" . $carry_row['est_time_min']; ?></td>
                            <td width="10%"><?php echo $carry_row['act_time_hr'] . ":" . $carry_row['act_time_min']; ?></td>
                            <td><?php echo $carry_row['target_date'] ?></td>
                            <td><?php echo $carry_row['description']; ?></td>
                            <td><?php echo $carry_row['status']; ?></td>
                            <td><?php echo $carry_row['cf_date'] ?></td>
                            <!--<td><?php echo ((($carry_row['act_time_hr'] . ":" . $carry_row['act_time_min']) != '00:00') && (($carry_row['act_time_hr'] . ":" . $carry_row['act_time_min']) != '')) ? ($carry_row['eff_ratio']) : '' ?></td>-->
                        </tr>
                    <?php } ?>
                </table>
            <?php } ?>
            <br>
            <?php if ($share_result->num_rows > 0) { ?>
                <table id="customers" border="0"
                       style="border-color: #A6107B; border-style: solid; border-width: 1px;" align="center"
                       cellpadding="0" cellspacing="0" width="95%"
                       class="form_style">
                    <tr>
                        <td colspan="12" align="center" bgcolor="#e9e9e9"><font
                                color="#a6107b" size="2"><b>Shared Jobs</b></font></td>
                    </tr>
                    <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                        <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                        <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                        <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                        <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                            </b></font></td>
                        <td width="7%"><font color="#a6107b" size="2"><b>Act Time<br>
                            </b></font></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                        <td width="20%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                        <td width="8%"><font color="#a6107b" size="2"><b>Status</b></font></td>
                        <td width="18%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
                        <!--<td width="18%"><font color="#a6107b" size="2"><b>Ratio</b></font></td>-->
                    </tr>
                    <?php while ($share_row = $share_result->fetch_assoc()) {
                        ?>
                        <tr align="left" valign="middle" bgcolor="#fff"
                            style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                            <td><?php echo $share_row['taskname'] ?></td>
                            <td><?php echo $share_row['job_type_name'] ?></td>
                            <td><?php echo $share_row['job_no'] ?></td>
                            <td><?php echo $share_row['client'] ?></td>
                            <td><?php echo $share_row['est_time'] ?></td>
                            <td width="10%"><?php echo $share_row['act_time_hr'] . ":" . $carry_row['act_time_min']; ?></td>
                            <td><?php echo $share_row['target_date'] ?></td>
                            <td><?php echo $share_row['description']; ?></td>
                            <td><?php echo $share_row['status']; ?></td>
                            <td><?php echo $share_row['cf_date'] ?></td>
                            <!--<td><?php echo $share_row['eff_ratio'] ?></td>-->
                        </tr>
                    <?php } ?>
                </table>
                <br>
                <br>

            <?php } ?>
            <table id="customers" border="0"
                   style="border-color: #A6107B; border-style: solid; border-width: 1px;" align="center"
                   cellpadding="0" cellspacing="0" width="95%"
                   class="form_style">
                <tr>
                    <td colspan="13" align="center" bgcolor="#e9e9e9"><font
                            color="#a6107b" size="2"><b>Daily Jobs1</b></font></td>
                </tr>
                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                    <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                    <td width="10%"><b><font color="#a6107b" size="2">Main Type</font></b></td>
                    <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                    <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                        </b></font></td>
                    <td width="7%"><font color="#a6107b" size="2"><b>Act Time<br>
                        </b></font></td>
                    <td width="8%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                    <td width="22%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                    <td width="6%"><font color="#a6107b" size="2"><b>Status</b></font></td>
                    <td width="18%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
                    <!--<td width="18%"><b><font color="#a6107b" size="2">Ratio</font></b></td>-->
                </tr>
                <?php
                if (count($daily_rows)) {
                    foreach ($daily_rows as $row) {
                        ?>
                        <tr align="center" valign="middle" bgcolor="#fff"
                            style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                            <td><?php echo $row['taskname'] ?></td>
                            <td><?php echo $row['main_type_name'] ?></td>
                            <td><?php echo $row['job_type_name'] ?></td>
                            <td><?php echo $row['job_no'] ?></td>
                            <td><?php echo $row['client'] ?></td>
                            <td><?php echo $row['est_time'] ?></td>
                            <td width="10%"><?php echo $row['act_time'] ?></td>
                            <td><?php echo $row['target_date'] ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['status'] ?></td>
                            <td><?php echo $row['cf_date'] ?></td>
                            <!--<td><?php echo (($row['act_time'] != '00:00') && ($row['act_time'] != '')) ? ($row['eff_ratio']) : '' ?></td>-->
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>

            <br>
            <table id="customers" border="0"
                   style="border-color: #A6107B; border-style: solid; border-width: 1px;" align="center"
                   cellpadding="0" cellspacing="0" width="95%"
                   class="form_style">
                <tr>
                    <td colspan="13" align="center" bgcolor="#e9e9e9"><font
                            color="#a6107b" size="2"><b>Routine Jobs</b></font></td>
                </tr>
                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                    <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                    <td width="10%"><b><font color="#a6107b" size="2">Main Type</font></b></td>
                    <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                    <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                        </b></font></td>
                    <td width="7%"><font color="#a6107b" size="2"><b>Act Time<br>
                        </b></font></td>
                    <td width="8%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                    <td width="22%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                    <td width="6%"><font color="#a6107b" size="2"><b>Status</b></font></td>
                    <td width="18%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
                    <!--<td width="18%"><b><font color="#a6107b" size="2">Ratio</font></b></td>-->
                </tr>
                <?php
                $routine_query = "SELECT main_type,job_type, taskname, description ,status, IF(DATE_FORMAT(est_time,'%H:%i')!='00:00',DATE_FORMAT(est_time,'%H:%i'),'') as est_time,IF(DATE_FORMAT(act_time,'%H:%i')!='00:00',DATE_FORMAT(act_time,'%H:%i'),'') as act_time,eff_ratio  from tbl_workreports  where is_carry=3 and  date_report='$sql_date'  and user_id=$user_id and delegation_id=0 ";
                $routine_result = $mysqli->query($routine_query);


                while ($routine_row = $routine_result->fetch_assoc()) {
                    ?>
                    <tr align="center" valign="middle" bgcolor="#fff"
                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                        <td><?php echo $routine_row['taskname'] ?></td>
                        <td><?php echo $main_type_array[$routine_row['main_type']] ?></td>
                        <td><?php echo $job_type_array[$routine_row['job_type']] ?></td>
                        <td></td>
                        <td></td>
                        <td><?php echo $routine_row['est_time'] ?></td>
                        <td width="10%"><?php echo $routine_row['act_time'] ?></td>
                        <td></td>
                        <td><?php echo $routine_row['description']; ?></td>
                        <td><?php echo $routine_row['status'] ?></td>
                        <td></td>
                        <!--<td><?php echo (($routine_row['act_time'] != '00:00') && ($routine_row['act_time'] != '')) ? ($routine_row['eff_ratio']) : '' ?></td>-->
                    </tr>
                    <?php
                }
                ?>
            </table>


        </td>
    </tr>
    <tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
    <?php include("includes/footer.php"); ?>
    </table>

<?php } ?>
</form>
<?php
$dataPoints = '';
$dates = DateTime::createFromFormat("Y-m-d", $sql_date);
$start_date = $dates->format("Y") . '-' . $dates->format("m") . '-01';
$start_dateInfo = $dates->format("Y") . " " . $dates->format("M");
$eff = $mysqli->query("select date_log,current_efficiency*100 as efficiency from tbl_time where date_log >= '$start_date' and date_log <= '$sql_date' and user_id = $user_id order by date_log ASC");
while ($eff_res = $eff->fetch_assoc()) {
    //print_r($eff_res);
    $dateI = DateTime::createFromFormat("Y-m-d", $eff_res['date_log']);
    $i = $dateI->format("M") . " " . $dateI->format("d");
    $y = $eff_res['efficiency'];
    $dataPoints = '{' . "label:'$i'" . ',' . "y:$y" . '}' . ',' . $dataPoints;
}
?>

<link type="text/css" rel="stylesheet" href="css/jquery-ui-1.8.9.custom/jquery-ui-1.8.9.custom.css" />
<!--<script type="text/javascript" src="jquery-1.4.3.min.js"></script> -->
<script type="text/javascript" src="jquery-ui-1.8.13.custom.min.js"></script>
<style type="text/css">body{ font: 62.5% "Trebuchet MS", sans-serif; }</style>
<script type="text/javascript">
                                                            window.onload = function () {
                                                                var chart = new CanvasJS.Chart("chartContainer", {
                                                                    theme: "theme3", animationEnabled: true,
                                                                    title: {text: "Efficiency graph (<?= $start_dateInfo ?>)", titleFontSize: 20, titleFontColor: '#000'},
                                                                    toolTip: {shared: true},
                                                                    axisY: {title: "Percantage Efficiency", maximum: 150, labelFontSize: 13},
                                                                    axisX: {labelFontSize: 13, labelFontColor: '#000'},
                                                                    data: [{type: "column", name: "Actual Efficiency ", fontSize: 13, legendText: "", showInLegend: false,
                                                                            dataPoints: [<?php echo $dataPoints; ?>]
                                                                        }, ],
                                                                    legend: {cursor: "pointer",
                                                                        itemclick: function (e) {
                                                                            if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                                                                                e.dataSeries.visible = false;
                                                                            } else {
                                                                                e.dataSeries.visible = true;
                                                                            }
                                                                            chart.render();
                                                                        }
                                                                    },
                                                                });
                                                                chart.render();
                                                            }
</script>
<script type="text/javascript" src="new_design/canvas/canvas.js"></script>


</body>
</html>