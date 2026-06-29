<?php
ob_start ();
session_start ();
if (! isset ( $_SESSION ['user_id'] )) {
	$out = array (
			'logout' => 'Yes' 
	);
	echo json_encode ( $out );
} else {
	include ("../includes/connect.inc.php");
	
	
	
	if($mysqli->connect_error)
	{
		$out = array (
			'traffic' => 'Yes' 
	);
	echo json_encode ( $out );
	}
	else
	{
	
	$colMap = array (
			0 => 'taskname',
			1 => 'job_type',
			2=>  'un_sch',
			3 => 'job_no',
			4 => 'client',
			5 => 'div_id',
			6 => 'est_time',
			7 => 'act_time',
			8 => 'target_date',
			9 => 'description',
			10 => 'cf_date',
			11 => 'status' 
	);
	
	$est_entry = "";
	$act_entry = "";
	
	
	$userid = $_SESSION ['user_id'];
	$date = $_REQUEST ['date'];
	$sql_date = date ( 'Y-m-d', strtotime ( $date ) );
	$job_type_result = $mysqli->query ( "select * from tbl_job_type" );
	$jobtypeMap = array ();
	while ( $job_type_row = $job_type_result->fetch_assoc () ) {
		$jobtypeMap [$job_type_row ['job_type_name']] = $job_type_row ['id'];
	}
	
		$un_sch_Map = array('false'=>0,'true'=>1);
		
	if ($_POST ['changes']) {
		
		foreach ( $_POST ['changes'] as $change ) {
			
			$rowId = $change [0] + 1;
			$colId = $change [1];
			$oldVal = $change [2];
			$newVal = $change [3];
			
			$select = "SELECT  workreport_id,est_time,est_entry,act_time,act_entry  from tbl_workreports where date_report='$sql_date' and user_id='$userid' and row_id=$rowId";
			$result = $mysqli->query ( $select );
			$row = $result->fetch_assoc ();
			
			if (($colId == 0) || ($colId == 3)||($colId == 4) || ($colId == 9)) {
				$newVal = addslashes ( $newVal );
			} elseif ($colId == 1) {
				$newVal = $jobtypeMap [$newVal];
			} elseif ($colId == 2) {
				$newVal = $un_sch_Map[$newVal];
			} 	elseif ($colId == 5) {
				$div_result = $mysqli->query ( "select div_id from  tbl_divisions where div_name='$newVal'" );
				$div_row = $div_result->fetch_assoc ();
				$newVal = $div_row ['div_id'];
			} elseif ($colId == 8) {
				if ($newVal == "")
					$newVal = "0000-00-00";
				else
					$newVal = date ( 'Y-m-d', strtotime ( $newVal ) );
			} elseif ($colId == 10) {
				
				if ($newVal == "")
					$newVal = "0000-00-00";
				else
					$newVal = date ( 'Y-m-d', strtotime ( $newVal ) );
			}
			if ($colId == 6) {
				
				$newVal = $newVal . ":00";
				
				if (($row ['est_time'] == $newVal) && ($newVal != ""))
					$est_entry = $row ['est_entry'];
				else if (($row ['est_time'] != $newVal) && ($newVal != ""))
					$est_entry = date ( 'Y-m-d H:i:s', time () + 14400 );
				
				if ($row ['workreport_id'] > 0) {
					$query = "UPDATE tbl_workreports set est_time='" . $newVal . "',est_entry='" . $est_entry . "' WHERE date_report='$sql_date' and user_id='$userid' and row_id='$rowId'";
					$mysqli->query ( $query );
				} else {
					$query = "INSERT INTO tbl_workreports(user_id,date_report,row_id,est_time,est_entry" . ") VALUES('$userid','$sql_date','$rowId','$newVal','$est_entry')";
					$mysqli->query ( $query );
				}
			} else if ($colId == 7) {
				
				$newVal = $newVal . ":00";
				
				if (($row ['act_time'] == $newVal) && ($newVal != ""))
					$act_entry = $row ['act_entry'];
				else if (($row ['act_time'] != $newVal) && ($newVal != ""))
					$act_entry = date ( 'Y-m-d H:i:s', time () + 14400 );
				if ($row ['workreport_id'] > 0) {
					$query = "UPDATE tbl_workreports set act_time='" . $newVal . "',act_entry='" . $act_entry . "' WHERE date_report='$sql_date' and user_id='$userid' and row_id='$rowId'";
					$mysqli->query ( $query );
					
				} else {
					$query = "INSERT INTO tbl_workreports(user_id,date_report,row_id,act_time,act_entry" . ") VALUES('$userid','$sql_date','$rowId','$newVal','$act_entry')";
					$mysqli->query ( $query );
					
				}
			} 

			else {
				
				if ($row ['workreport_id'] > 0) {
					$query = "UPDATE tbl_workreports set " . $colMap [$colId] . "='" . $newVal . "' WHERE date_report='$sql_date' and user_id='$userid' and row_id='$rowId'";
					
					$mysqli->query ( $query );
					
				} else {
					$query = "INSERT INTO tbl_workreports(user_id,date_report,row_id," . $colMap [$colId] . ") VALUES('$userid','$sql_date','$rowId','$newVal')";
					$mysqli->query ( $query );
					
				}
			}
			
			$delegation_result = $mysqli->query ( "select count(workreport_id) as  count_unread  from  tbl_workreports where date_report='$sql_date' and user_id='$userid' and assigned_by>0 and is_read=0" );
			
			$delegation_row = $delegation_result->fetch_assoc ();
			$unread = $delegation_row ['count_unread'];
			
			$time_work_result = $mysqli->query ( "select SUM(TIME_TO_SEC(est_time)) as total_est,SUM(TIME_TO_SEC(act_time)) as total_act  from  tbl_workreports where date_report='$sql_date' and user_id='$userid'" );
			$time_work_row = $time_work_result->fetch_assoc ();
			$est = $time_work_row ['total_est'];
			$act = $time_work_row ['total_act'];
			
			$routine_time_result = $mysqli->query ( "select SUM(TIME_TO_SEC(

if(status.est_time='00:00:00' || status.est_time is null ,daily.est_time,status.est_time)



)) as total_est,SUM(TIME_TO_SEC(status.act_time)) as total_act  from   tbl_daily_jobs daily left join tbl_daily_job_status status on daily.id=status.job_id and job_date='$sql_date' where  daily.status=1 and  daily.user_id='$userid' " );
			$routine_time_row = $routine_time_result->fetch_assoc ();
			$routine_est = $routine_time_row ['total_est'];
			$routine_act = $routine_time_row ['total_act'];
			
			$total_est = $est + $routine_est;
			$total_act = $act + $routine_act;
			
			$out = array (
					'est_time' => gmdate ( 'H:i', $est ),
					'act_time' => gmdate ( 'H:i', $act ),
					'routine_est' => gmdate ( 'H:i', $routine_est ),
					'routine_act' => gmdate ( 'H:i', $routine_act ),
					'total_est' => gmdate ( 'H:i', $total_est ),
					'total_act' => gmdate ( 'H:i', $total_act ),
					'unread' => $unread 
			);
			echo json_encode ( $out );
		}
	}
}
}
?>