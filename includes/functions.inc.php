<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * ******** Common Functions ****************
 * For Cochin Office
 * By Santhosh Kumar M On 20 Feb 2014
 * Start
 */
include_once("calevents.inc.php");
include("includes/connect.inc.php");

Class FUNCTIONS {

    public $cal=NULL;

    function FUNCTIONS() {
    }
    /******** Calculate Net Time ********
     * For Cochin Office
     * By Santhosh Kumar M On 20 Feb 2014
     * Start
     */
    function calculateEffectiveWorkingHours($location_id, $date_log, $effective_hours,$userid,$from='') {
        $cal = new CALEVENTS();
        //$full = '09:30';
           $full = '09:00';
        $empty = '00:00';
        if($from == 'ind_time_sheet'){
           //$full = '09:30:00';
            $full = '09:00:00';
           $empty = '00:00:00';
        }else if($from == 'job_diary'){
          // $full = '09:30';
           $full = '09:00';
           $empty = '00:00';
        }else if($from == 'daily_job_status'){
         //  $full = '09:30:00';
            $full = '09:00:00';
           $empty = '00:00:00';
        }else if($from == 'time_sheet'){
//           $full = '9.5';
            $full = '9.0';
           $empty = '0.0';
        }
       /* if($location_id == 3){ now applcbl for all loctn staffs*/
//        echo $effective_hours;
            $res = $cal->getDayStatus($date_log,2);// 2 : cochin
            $arr_day_status = $res->fetch_assoc();
            $day_name =date("D", strtotime($date_log));
            if($effective_hours != '' && $effective_hours != $empty){
                if($day_name == 'Sat' || $day_name == 'Sun' || (isset($arr_day_status['status']) && $arr_day_status['status'] == 'Holiday')){
                    //No change for E.F.H needed for saturday/sunday/holiday if worked
                }else{ 
                   if(isset($arr_day_status['status']) && $arr_day_status['status'] == 'Working Day'){
                       //Check for if any half day leave taken for the day, then add extra 4:45Hrs to existing E.F.H
                       //function for checking if leave taken
                       $effective_hours = $effective_hours;
                   } 
                }
            }else{
                //if net time is null
                //check if the date is saturday/sunday/LOP, then assign its E.F.H = 0.00Hrs, else E.F.H = 09:30 Hrs
                if(isset($arr_day_status['status']) && $arr_day_status['status'] != 'Working Day'){
                   if($day_name == 'Sat' || $day_name == 'Sun'){
                       $effective_hours = $empty;
                   }
                   else{
                       if(isset($arr_day_status['status']) && $arr_day_status['status'] == 'Unplanned Holiday'){
                            $effective_hours = $empty;
                       }else{
                            $effective_hours = $full;
                       }
                   }
                }
                else{
                    //check if the day is LOP Leave, then set E.F.H : 0.00Hrs, else for other leaves E.F.H = 09:30Hrs
                    //function for checking if leave taken
                   
                    /*****added by saranya for leave day***********/
                    
                     global $mysqli;
                    $user_id=$_REQUEST['user_id'];
                    $que_leave_count  = $mysqli->query("Select COUNT(employee_id) from tbl_leave_details as cnt where employee_id='$userid' 
                                          and leave_date='$date_log' AND leave_type_id='3' AND leave_status='4'" );
//echo $userid;exit;
            $row         = $que_leave_count->fetch_row();
                      //  print_r($row);
            $leave_count =  $row[0];
            $day_name =date("D", strtotime($date_log));
            $monthNum =  date("m",strtotime($date_log));
            $full_year= date("Y", strtotime($date_log));
            
             $nextMonthStart = mktime(0,0,0,$monthNum+1,1,$full_year);
             $last_saturday = date("Y-m-d",strtotime("previous saturday", $nextMonthStart));

            if($leave_count>0)
            {
                if($day_name == 'Sat' || $day_name == 'Sun'){
                    if(($location_id==3)&&$date_log==$last_saturday)
                         $effective_hours  = $full;
                    else $effective_hours  = $empty;
                }else{
//                 $start;
                $effective_hours =$full;
                
                }
            }
                    
                    /*****added by saranya end***********/
                 else{   
                    $effective_hours = $empty;}
                }
            }
        /*}* now applcbl for all loctn staffs*/
        return $effective_hours;
    }
    //End
	function getDateRangeArray($strDateFrom,$strDateTo)
	{
	// takes two dates formatted as YYYY-MM-DD and creates an
	// inclusive array of the dates between the from and to dates.

	// could test validity of dates here but I'm already doing
	// that in the main script

	$aryRange=array();

	$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
	$iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

	if ($iDateTo>=$iDateFrom)
	{
		array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
		while ($iDateFrom<$iDateTo)
		{
			$iDateFrom+=86400; // add 24 hours
			array_push($aryRange,date('Y-m-d',$iDateFrom));
		}
	}
	return $aryRange;
	}
  }
    ?>