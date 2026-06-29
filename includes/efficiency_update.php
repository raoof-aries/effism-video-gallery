<?php 
include("connect.inc.php");

//mysql_select_db('ames');
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$employee_id = $_REQUEST['employee_id'];
$date = $_REQUEST['date'];
$start_time = $_REQUEST['start_time'];
$end_time = $_REQUEST['end_time'];
$break_time = $_REQUEST['break_time'];
$work_home_time = $_REQUEST['work_home_time'];
$midnight_work_time = $_REQUEST['midnight_work_time'];
$leave_hours = $_REQUEST['leave_hours'];

$sql = "UPDATE tbl_time t, tbl_users u SET 
			t.time_in = '$start_time',
			t.time_out = '$end_time', 
			t.nwt = '$break_time',
			t.home = '$work_home_time',
			t.night = '$midnight_work_time',
			t.leave_hours = '$leave_hours',
			t.is_complete = '1',
			t.current_efficiency = ROUND(time_to_sec(t.total_job)/((time_to_sec(ADDTIME('$work_home_time','$midnight_work_time')))+time_to_sec(TIMEDIFF(TIMEDIFF('$end_time','$start_time'),
'$break_time'))),2)
		WHERE t.date_log = '$date'
			AND t.user_id=u.user_id
			AND u.hr_id = '$employee_id'";
$result = mysqli_query($mysqli,$sql);

$net_sql="UPDATE tbl_time SET net_time=TIMEDIFF(addtime(addtime(addtime(addtime(timediff(timediff(timediff(time_out,time_in),nwt),extra_break),outside),night),home),leave_hours),site_travel) WHERE date_log='$date' and is_complete=1 ";
mysqli_query($mysqli,$net_sql);

//insert into efficiency 
if(!empty($_REQUEST['type']) && $_REQUEST['type'] == 'insert')
{
	$work_status = $_REQUEST['work_status'];
	$from = $_REQUEST['from'];
	$to = $_REQUEST['to'];
	$month_days = $_REQUEST['month_days'];
	$year = date("Y",strtotime($from));
	$month = date("m",strtotime($from));
	$from_day = date("d",strtotime($from));
	$to_day = date("d",strtotime($to));
	$time_sheet_start_date = $year.'-'.$month.'-01';
	
	//FRIDAY SATURDAY ARRAY CREATE
	$holidays = $_POST['holidays'];
	if($holidays == 1)
	{
		$holiday1 = 'friday';
		$holiday2 = 'saturday';
	}
	elseif ($holidays == 2)
	{
		$holiday1 = 'sunday';
		$holiday2 = 'saturday';
	} elseif ($holidays == 3) {
		$holiday1 = 'friday';
	} elseif ($holidays == 4) {
		$holiday2 = 'saturday';
	} elseif ($holidays == 5) {
		$holiday1 = 'sunday';
	}
	$start_date = $year."-".$month."-01";
	$end_date = $year."-".$month."-".$month_days;
	
    if(!empty($holidays)) {
	$arrDays = array();
	$arrDays1 = array();
	$saturdayFriday = array();
	$start = strtotime($start_date); // your start/end dates here
	$end = strtotime($end_date);
	
	$friday = strtotime($holiday1, $start);
	$saturday = strtotime($holiday2, $start);
	if($holidays == 1 || $holidays == 2 || $holidays == 3 || $holidays == 5) {
	while($friday <= $end) {
		$arrDays[date("d", $friday)] = date("d", $friday);
		//$arrDays1[date("d", strtotime("+1 days", $friday))] = date("d", strtotime("+1 days", $friday));
	
		$friday = strtotime("+1 weeks", $friday);
	}
	}
	if($holidays == 1 || $holidays == 2 || $holidays == 4) {
	while($saturday <= $end) {
		$arrDays1[date("d", $saturday)] = date("d", $saturday);
	
		$saturday = strtotime("+1 weeks", $saturday);
	}
	
	$i = 0;
	foreach ($arrDays1 as $key => $value)
	{
		if($i <= 2)
		{
			$saturdayFriday[$value] = $value;
		}
		$i++;
	}
	}
	}
	//END OF FRIDAY SATURDAY ARRAY CREATE
	
	$sql = "SELECT DAYOFMONTH(t.date_log) day,u.doj,u.user_id FROM tbl_users u LEFT JOIN tbl_time t ON t.user_id=u.user_id
							AND year(t.date_log) = '$year' AND month(t.date_log) = '$month'
					 WHERE u.hr_id = '$employee_id'
						 ORDER BY t.date_log DESC LIMIT 1";
		$result = mysqli_query($mysqli, $sql);
			$row = mysqli_fetch_assoc($result);
			$user_id = $row['user_id'];
			if(isset($row['day']))
			{
				$last_day = $row['day']+1;
			}
			elseif ($row['doj'] >= $time_sheet_start_date && !isset($row['day']))
			{
				$last_day = date("d",strtotime($row['doj']));
			}
		
		if($from_day > $last_day)
		{
			$till_date = $from_day - 1;
			while($last_day <= $till_date)
			{
				$date = $year.'-'.$month.'-'.$last_day;
				$sql = "INSERT INTO tbl_time (date_log,user_id,is_complete)
				SELECT '$date', user_id,'1' FROM tbl_users
				WHERE hr_id = '$employee_id'";
				$result = mysqli_query($mysqli, $sql);
				$last_day++;
			}
		}
		$leave_hours1 = $leave_hours;
		$start_time1 = $start_time;
			$end_time1 = $end_time;
			$break_time1 = $break_time;
		while($from_day <= $to_day)
		{
			$leave_hours = $leave_hours1;
		if($work_status == 'Annual Leave' || $work_status == 'Splt Annual Leave' || $work_status == 'Medical Leave' || $work_status == 'Work')
			{
				foreach ($arrDays as $key => $value)
				{
					if($value == $from_day) { $leave_hours = '00:00:00';
                                        $start_time = '00:00:00';
					$end_time = '00:00:00';
					$break_time = '00:00:00';
                                        }
				}
				foreach ($saturdayFriday as $key => $value)
				{
					if($value == $from_day) { $leave_hours = '00:00:00';
                                        $start_time = '00:00:00';
					$end_time = '00:00:00';
					$break_time = '00:00:00';
                                        }
				}
			}
			
			$date = $year.'-'.$month.'-'.$from_day;
		print 	$sql = "SELECT COUNT(*) count FROM tbl_time WHERE user_id = '$user_id' AND date_log = '$date'";
			$result = mysqli_query($mysqli, $sql);
			$row = mysqli_fetch_assoc($result);
			if($row['count'] != '0')
			{
				$sql = "UPDATE tbl_time SET
						work_status = '$work_status',
						time_in = '$start_time',
						time_out = '$end_time',
						nwt = '$break_time',
						leave_hours = '$leave_hours',
						is_complete = '1'
						WHERE user_id = '$user_id' AND date_log = '$date'";
			}
			else
			{
				$sql = "INSERT INTO tbl_time (work_status,time_in,time_out,nwt,leave_hours,date_log,user_id,is_complete)
						VALUES('$work_status',
						'$start_time',
						'$end_time',
						'$break_time',
						'$leave_hours',
						'$date',
						'$user_id',
						'1')";
			}
				$result = mysqli_query($mysqli, $sql);
				$from_day++;
				$start_time = $start_time1;
				$end_time = $end_time1;
				$break_time = $break_time1;
		}
	/*	
		while ($new_day >= $last_day)
		{
			print $date = $year.'-'.$month.'-'.$last_day;
			print $sql = "INSERT INTO tbl_time (date_log,leave_hours,user_id)
			SELECT '$date','$leave_hours', user_id FROM tbl_users
			WHERE hr_id='$employee_id'";
			$result = mysqli_query($mysqli, $sql);
		//display_error($sql);
			$last_day++;
		}*/
}

if(!empty($_REQUEST['type']) && $_REQUEST['type'] == 'CLEAR_DATA') {
	$employee_id = $_REQUEST['employee_id'];
	$date = $_REQUEST['date'];
	$sql = "SELECT user_id FROM tbl_users WHERE hr_id = '$employee_id' LIMIT 1";
		$result = mysqli_query($mysqli, $sql);
		$row = mysqli_fetch_assoc($result);
		$user_id = $row['user_id'];
                
       $sql1 ="INSERT INTO `tbl_time_log` ( `id`, `work_status`, `time_in`, `time_out`, `nwt`, `extra_break`, `home`, `outoffice`, `outside`, `night`, `site_travel`, `leave_hours`, `health`, `sleep`, `date_log`, `user_id`, `remarks`, `system_ok`, `condition_remarks`, `location`, `is_complete`, `total_job`, `efficiency`, `current_efficiency`, `not_punctual`, `late_remarks`, `unplan`, `no_health`, `no_of_edit`, `completion_time`, `auditing`, `audited`, `friend`, `family`, `travel`, `first_login`)
                    SELECT `id`, `work_status`, `time_in`, `time_out`, `nwt`, `extra_break`, `home`, `outoffice`, `outside`, `night`, `site_travel`, `leave_hours`, `health`, `sleep`, `date_log`, `user_id`, `remarks`, `system_ok`, `condition_remarks`, `location`, `is_complete`, `total_job`, `efficiency`, `current_efficiency`, `not_punctual`, `late_remarks`, `unplan`, `no_health`, `no_of_edit`, `completion_time`, `auditing`, `audited`, `friend`, `family`, `travel`, `first_login`
                    FROM tbl_time 
                    WHERE date_log = '$date'  AND user_id=$user_id "; 
       mysqli_query($mysqli, $sql1);
                 
                
                
	$sql2 = "UPDATE tbl_time SET time_in = '',time_out = '',nwt = '',extra_break = '',leave_hours = '',site_travel=''
					WHERE user_id = '$user_id' AND date_log = '$date'";
	mysqli_query($mysqli, $sql2);
	
		$sql3 = "UPDATE tbl_time SET net_time=TIMEDIFF(addtime(addtime(addtime(addtime(timediff(timediff(timediff(time_out,time_in),nwt),extra_break),outside),night),home),leave_hours),site_travel) 	WHERE user_id = '$user_id' AND date_log = '$date'";
	    mysqli_query($mysqli, $sql3);
}
if(!empty($_REQUEST['type']) && $_REQUEST['type'] == 'CLEAR_ONE_MONTH_DATA') {//***ADDED BY SARANYA, TO DELETE ONE MONTH DATA
	$employee_id = $_REQUEST['employee_id'];
	$selyear = $_REQUEST['year'];
        $selmonth = $_REQUEST['month'];
	$sql = "SELECT user_id FROM tbl_users WHERE hr_id = '$employee_id' LIMIT 1";
		$result = mysqli_query($mysqli, $sql);
		$row = mysqli_fetch_assoc($result);
		$user_id = $row['user_id'];
                
                
       $sql1 ="INSERT INTO `tbl_time_log` ( `id`, `work_status`, `time_in`, `time_out`, `nwt`, `extra_break`, `home`, `outoffice`, `outside`, `night`, `site_travel`, `leave_hours`, `health`, `sleep`, `date_log`, `user_id`, `remarks`, `system_ok`, `condition_remarks`, `location`, `is_complete`, `total_job`, `efficiency`, `current_efficiency`, `not_punctual`, `late_remarks`, `unplan`, `no_health`, `no_of_edit`, `completion_time`, `auditing`, `audited`, `friend`, `family`, `travel`, `first_login`)
                    SELECT `id`, `work_status`, `time_in`, `time_out`, `nwt`, `extra_break`, `home`, `outoffice`, `outside`, `night`, `site_travel`, `leave_hours`, `health`, `sleep`, `date_log`, `user_id`, `remarks`, `system_ok`, `condition_remarks`, `location`, `is_complete`, `total_job`, `efficiency`, `current_efficiency`, `not_punctual`, `late_remarks`, `unplan`, `no_health`, `no_of_edit`, `completion_time`, `auditing`, `audited`, `friend`, `family`, `travel`, `first_login`
                    FROM tbl_time 
                    WHERE year(date_log) = '$selyear' AND month(date_log) = '$selmonth'    AND user_id=$user_id "; 
        mysqli_query($mysqli, $sql1);
        
        
                
	$sql2 = "UPDATE tbl_time SET time_in = '',time_out = '',nwt = '',extra_break = '', leave_hours = ''
					WHERE user_id = '$user_id' AND   year(date_log) = '$selyear' AND month(date_log) = '$selmonth' ";
        mysqli_query($mysqli, $sql2);
}

?>