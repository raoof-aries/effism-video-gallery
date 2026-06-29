<?php 
header("Content-Type: application/rss+xml; charset=ISO-8859-1");

include("connect.inc.php");
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];
$division_id = $_REQUEST['division_id'];
$location_id = $_REQUEST['location_id'];
$employee_ids = $_REQUEST['employee_ids'];
$empArray = explode(',',$employee_ids);
/*
$year = 2014;
$month = 06;
$division_id = 2;
$location_id = 6;
*/
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
/*
$sql = "SELECT u.hr_id, u.emp_division_id division_id, u.reporting_location location_id, t.date_log, t.time_in, t.time_out, t.nwt, t.home, t.night
				FROM tbl_time t
				LEFT JOIN tbl_users u ON t.user_id = u.user_id
				WHERE month( t.date_log ) = '$month'
				AND year( t.date_log ) = '$year'
				AND u.emp_division_id IN ($division_id)
				AND u.reporting_location IN ($location_id)
				AND u.hr_id != 0
				ORDER BY u.hr_id ASC,t.date_log ASC
				";
*/
$sql = "SELECT u.hr_id, u.emp_division_id division_id, u.reporting_location location_id, t.date_log, t.time_in, t.time_out, t.nwt, t.home, t.night,t.extra_break,
 t.leave_hours,t.site_travel
FROM tbl_time t
LEFT JOIN tbl_users u ON t.user_id = u.user_id
WHERE month( t.date_log ) = '$month'
AND year( t.date_log ) = '$year'
AND u.hr_id in ($employee_ids)
ORDER BY u.hr_id ASC,t.date_log ASC
";



$result = mysqli_query($mysqli,$sql);
$num = mysqli_num_rows($result);
$str = array();
$str = '<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
		<channel><ROOT>';
if($num>0)
{
while($row = mysqli_fetch_assoc($result))
{
			$str .= '<ROW_DATA>
	<EmployeeID>'.$row['hr_id'].'</EmployeeID>
	<DivisionId>'.$row['division_id'].'</DivisionId>
	<LocationId>'.$row['location_id'].'</LocationId>
	<Date>'.$row['date_log'].'</Date>
    <StartTime>'.$row['time_in'].'</StartTime>
    <EndTime>'.$row['time_out'].'</EndTime>
    <BreakTime>'.$row['nwt'].'</BreakTime>
    <HomeTime>'.$row['home'].'</HomeTime>
    <MidNightTime>'.$row['night'].'</MidNightTime>
     <ExtraBreak>'.$row['extra_break'].'</ExtraBreak>
      <SiteTravel>'.$row['site_travel'].'</SiteTravel>
     <LeaveHours>'.$row['leave_hours'].'</LeaveHours>
    		</ROW_DATA>';
}
}
else
{

				$str .= '<ROW_DATA>
	<EmployeeID>'.$empArray[0].'</EmployeeID>
	<DivisionId>0</DivisionId>
	<LocationId>0</LocationId>
	<Date>'.$year.'-'.$month.'-01</Date>
    <StartTime>00:00:00</StartTime>
    <EndTime>00:00:00</EndTime>
    <BreakTime>00:00:00</BreakTime>
    <HomeTime>00:00:00</HomeTime>
    <MidNightTime>00:00:00</MidNightTime>
     <ExtraBreak>00:00:00</ExtraBreak>
     <LeaveHours>00:00:00</LeaveHours>
    		</ROW_DATA>';
	
}
	$str .= '</ROOT></channel></rss>';	

print_r($str);