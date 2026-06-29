<?php
if (isset($_POST['save_complete_x']) || ($_POST['save_value'] == "save_date_complete")) {



    $work_status = 'work';
    if ((isset($_POST['save_holiday'])) || ($_POST['current_job_status'] == 'holiday'))
        $work_status = 'holiday';
    else if ((isset($_POST['save_leave'])) || ($_POST['current_job_status'] == 'leave'))
        $work_status = 'leave';
    $timein_hr = $_POST['timein_hr'];
    $timein_min = $_POST['timein_min'];
    $ampm_in = $_POST['ampm_in'];
    if ($ampm_in == "pm" && $timein_hr == 12)
        $timein_hr = $timein_hr;
    else if ($ampm_in == "pm")
        $timein_hr = $timein_hr + 12;
    $timeout_hr = $_POST['timeout_hr'];
    $timeout_min = $_POST['timeout_min'];
    $ampm_out = $_POST['ampm_out'];
    if ($ampm_out == "pm" && $timeout_hr == 12)
        $timeout_hr = $timeout_hr;
    else if ($ampm_out == "pm")
        $timeout_hr = $timeout_hr + 12;
    $timein = $timein_hr.":".$timein_min.":"."00";
    $timeout = $timeout_hr.":".$timeout_min.":"."00";
    if (($_POST['timein_hr'] == "") && ($_POST['timein_min'] == "") && ($_POST['timeout_hr'] == "") && ($_POST['timeout_min'] == "")) {

        $timein = "00:00:00";
        $timeout = "00:00:00";
    }

    $nwt_hr = ($_POST['nwt_hr'] == "") ? "00" : $_POST['nwt_hr'];
    $nwt_min = ($_POST['nwt_min'] == "") ? "00" : $_POST['nwt_min'];
    $nwt = $nwt_hr.":".$nwt_min.":"."00";

    $home_hr = ($_POST['home_hr'] == "") ? "00" : $_POST['home_hr'];
    $home_min = ($_POST['home_min'] == "") ? "00" : $_POST['home_min'];
    $home = $home_hr.":".$home_min.":"."00";


    $night_hr = ($_POST['night_hr'] == "") ? "00" : $_POST['night_hr'];
    $night_min = ($_POST['night_min'] == "") ? "00" : $_POST['night_min'];
    $night = $night_hr.":".$night_min.":"."00";


    $outoffice_hr = ($_POST['outoffice_hr'] == "") ? "00" : $_POST['outoffice_hr'];
    $outoffice_min = ($_POST['outoffice_min'] == "") ? "00" : $_POST['outoffice_min'];
    $outoffice = $outoffice_hr.":".$outoffice_min.":"."00";





    $health_hr = ($_POST['health_hr'] == "") ? "00" : $_POST['health_hr'];
    $health_min = ($_POST['health_min'] == "") ? "00" : $_POST['health_min'];
    $health = $health_hr.":".$health_min.":"."00";

    $sleep_hr = ($_POST['sleep_hr'] == "") ? "00" : $_POST['sleep_hr'];
    $sleep_min = ($_POST['sleep_min'] == "") ? "00" : $_POST['sleep_min'];
    $sleep = $sleep_hr.":".$sleep_min.":"."00";



    $system_ok = $_POST['system_ok'];
    $condition_remarks = addslashes($_POST['condition_remarks']);

    $not_punctual = ($_POST['not_punctual'] == "Yes") ? "1" : 0;
    $late_remarks = addslashes($_POST['late_remarks']);




    $remarks = addslashes($_POST['remarks']);
    if ($remarks == "") {
        if (isset($_POST['save_holiday']))
            $remarks = 'Holiday';
        else if (isset($_POST['save_leave']))
            $remarks = 'Leave';
    }





    $location = addslashes($_POST['location']);

    $time_query = "SELECT  id FROM tbl_time where user_id=$user_id and date_log='".$sql_date."'";
    $time_result = $mysqli->query($time_query);

    if ($time_result->num_rows > 0)
        $query = "update tbl_time set  time_in='$timein',time_out='$timeout',nwt='$nwt',home='$home',outoffice='$outoffice',night='$night',health='$health',sleep='$sleep',system_ok='$system_ok',condition_remarks='$condition_remarks',remarks='$remarks',location='$location',work_status='$work_status',not_punctual='$not_punctual',late_remarks='$late_remarks' where user_id=$user_id and date_log='".$sql_date."'";
    else
        $query = "INSERT INTO tbl_time (time_in,time_out,nwt,home,date_log,user_id,remarks,location,work_status,outoffice,night,health,sleep,system_ok,condition_remarks,not_punctual,late_remarks) VALUES ('$timein','$timeout','$nwt','$home','$sql_date','$user_id','$remarks','$location','$work_status','$outoffice','$night','$health','$sleep','$system_ok','$condition_remarks','$not_punctual','$late_remarks')";


    $mysqli->query($query);


    /*
      $result = $mysqli->query("SELECT  count(workreport_id) as count_work  from tbl_workreports where is_carry=0 and  date_report='$sql_date' and TRIM(taskname)!='' and main_type=0  and user_id=$user_id and row_id>0");
      $row = $result->fetch_assoc();
      if($row['count_work']> 0){
      $result->free();
      $error='maintype_failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }

      $result = $mysqli->query("SELECT  count(workreport_id) as count_work  from tbl_workreports where is_carry=0 and  date_report='$sql_date' and TRIM(taskname)!='' and main_type=1 and (trim(job_no)='' || trim(job_no) is null)   and user_id=$user_id and row_id>0");
      $row = $result->fetch_assoc();
      if($row['count_work']> 0){
      $result->free();
      $error='Job_No_Failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }

     */

    $result = $mysqli->query("select count(workreport_id) as count_work from tbl_workreports where user_id='$user_id' and date_report='$sql_date' and status!=100 and taskname!='' and is_carry!=2 and  (cf_date='0000-00-00' || cf_date<='$sql_date')");
    $row = $result->fetch_assoc();


    if ($row['count_work'] > 0) {
        $result->free();
        $error = 'complete_failure';
        unset($_POST['save_complete_x']);
        unset($_POST['save_value']);



        header("Location:jobdiary.php?date=".$date."&error=".$error);
        exit();
    }

    /*
      $result = $mysqli->query("select count(workreport_id) as count_work from tbl_workreports
      where user_id='$user_id' and date_report='$sql_date' and status!=100 and taskname!='' and row_id>0 and  is_carry=0 and  (target_date='0000-00-00' || target_date='' || target_date is null )");
      $row = $result->fetch_assoc();
      if($row['count_work']> 0){

      $result->free();
      $error='CF_Target_Failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }

      $result = $mysqli->query("select count(workreport_id) as count_work from tbl_workreports
      where user_id='$user_id' and date_report='$sql_date' and status!=100 and taskname!=''  and  description=''");
      $row = $result->fetch_assoc();
      if($row['count_work']> 0){
      $result->free();
      $error='Description_Failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }



      /*$main_result = $mysqli->query("SELECT  count(workreport_id) count_main from tbl_workreports where  is_carry=0 and  main_type=0 and date_report='$sql_date'  and user_id=$user_id and delegation_id=0");
      $main_row = $main_result->fetch_assoc();
      if($main_row['count_main']> 0){
      $main_result->free();
      $error='main_type_failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }

     */

    /*
      if($_SESSION['emp_division_id']==2)
      {

      $equipment_access_result =$mysqli->query("select count(id) as count_id from  tbl_equipments  where 	 equipment_in_charge='$user_id'");
      $equipment_access_row=$equipment_access_result->fetch_assoc();
      $count_id= $equipment_access_row['count_id'];

      if(($count_id>0))
      {
      $equipment_query = "SELECT count(*) as count_equipment from tbl_equipments e left join tbl_equipment_utilisation u on e.id=u.equipment_id  and  utilisation_date='$sql_date'  where e.equipment_in_charge='$user_id'  and (u.status='' or u.status  is null or (u.status='Used' and u.usage_cost=0))   ";
      $equipment_result =$mysqli->query($equipment_query);
      $equipment_row = $equipment_result->fetch_assoc();
      if($equipment_row['count_equipment']> 0){
      $equipment_result->free();
      $error='equipment_failure';
      unset($_POST['save_complete_x']);
      unset($_POST['save_value']);
      header("Location:jobdiary.php?date=".$date."&error=".$error);
      exit();
      }
      }

      }

     */

    $result = $mysqli->query("select count(w.workreport_id) as count_work from tbl_workreports w left join tbl_delegation d on d.delegation_id=w.delegation_id where w.user_id='$user_id' and w.date_report='$sql_date' and w.status!=100 and w.taskname!='' and d.delegate_type=4 and w.is_carry=0 and  d.assigned_by>0 and ((w.target_date='0000-00-00' || w.target_date<='$sql_date')||((w.target_date!='0000-00-00'  ||   w.target_date!='') && w.target_date < w.cf_date) )");





    $row = $result->fetch_assoc();
    if ($row['count_work'] > 0) {

        $result->free();
        $error = 'proposal_failure';
        unset($_POST['save_complete_x']);
        unset($_POST['save_value']);

        header("Location:jobdiary.php?date=".$date."&error=".$error);
        exit();
    }



    $result = $mysqli->query("select count(w.workreport_id) as count_work from tbl_workreports w  left join tbl_delegation d on d.delegation_id=w.delegation_id where w.user_id='$user_id' and w.date_report='$sql_date' and w.status!=100 and w.taskname!='' and d.delegate_type!=3 and d.delegate_type!=2 and w.delegation_id>0 and w.cf_date>w.target_date ");



    $row = $result->fetch_assoc();
    if ($row['count_work'] > 0) {
        $error = 'delegation_failure';
        unset($_POST['save_complete_x']);
        unset($_POST['save_value']);
        $result->free();

        header("Location:jobdiary.php?date=".$date."&error=".$error);
        exit();
    }



    $day = date('j',strtotime($sql_date));
    $month = date('n',strtotime($sql_date));
    $year = date('Y',strtotime($sql_date));


    if ($day >= 1 && $day <= 7)
        $week = 1;
    else if ($day >= 8 && $day <= 14)
        $week = 2;
    else if ($day >= 15 && $day <= 21)
        $week = 3;
    else if ($day >= 22 && $day <= 28)
        $week = 4;
    else if ($day >= 29 && $day <= 31)
        $week = 5;

    if ((date('d',strtotime($sql_date)) == 7) || (date('d',strtotime($sql_date)) == 14) || (date('d',strtotime($sql_date)) == 21)) {

        $monthly_result = $mysqli->query("select count(plan_id) as count_work from  tbl_monthly_plan_details where taskname!='' and  user_id='$user_id' and is_current=1 and status!=100 and select_week='$week' and plan_year='$year' and plan_month='$month' and

(cf_week=0
or (cf_week='$week'   and  carry_forward=0)
or (cf_week='$week' and  carry_forward=$month)
)


  ");



        $monthly_row = $monthly_result->fetch_assoc();
        if ($monthly_row['count_work'] > 0) {
            $error = 'weekly_failure';
            unset($_POST['save_complete_x']);
            unset($_POST['save_value']);
            $result->free();

            header("Location:jobdiary.php?date=".$date."&error=".$error);
            exit();
        }
    }
}

if ((isset($_POST['cancel_holiday'])) || (isset($_POST['cancel_leave']))) {

    $mysqli->query("delete from tbl_time where user_id=$user_id and date_log='".$sql_date."'");
    if ((isset($_POST['cancel_holiday'])))
        $cancel_status = "Cancel Holiday";
    else if ((isset($_POST['cancel_leave'])))
        $cancel_status = "Cancel Leave";


    header("Location:jobdiary.php?date=".$date."&error=".$error);
    exit();
}



if ((isset($_POST['save_date_time']) || ($_POST['save_value'] == "save_date")) || (isset($_POST['save_holiday'])) || (isset($_POST['save_leave']))) {

    $late_remarks = addslashes($_POST['late_remarks']);
    $not_punctual = ($_POST['not_punctual'] == "Yes") ? "1" : 0;



    $work_status = 'work';
    if (isset($_POST['save_holiday']))
        $work_status = 'holiday';
    else if (isset($_POST['save_leave']))
        $work_status = 'leave';
    $timein_hr = $_POST['timein_hr'];
    $timein_min = $_POST['timein_min'];
    $ampm_in = $_POST['ampm_in'];
    if ($ampm_in == "pm" && $timein_hr == 12)
        $timein_hr = $timein_hr;
    else if ($ampm_in == "pm")
        $timein_hr = $timein_hr + 12;
    $timeout_hr = $_POST['timeout_hr'];
    $timeout_min = $_POST['timeout_min'];
    $ampm_out = $_POST['ampm_out'];
    if ($ampm_out == "pm" && $timeout_hr == 12)
        $timeout_hr = $timeout_hr;
    else if ($ampm_out == "pm")
        $timeout_hr = $timeout_hr + 12;
    $timein = $timein_hr.":".$timein_min.":"."00";
    $timeout = $timeout_hr.":".$timeout_min.":"."00";
    if (($_POST['timein_hr'] == "") && ($_POST['timein_min'] == "") && ($_POST['timeout_hr'] == "") && ($_POST['timeout_min'] == "")) {

        $timein = "00:00:00";
        $timeout = "00:00:00";
    }
    $nwt_hr = ($_POST['nwt_hr'] == "") ? "00" : $_POST['nwt_hr'];
    $nwt_min = ($_POST['nwt_min'] == "") ? "00" : $_POST['nwt_min'];
    $nwt = $nwt_hr.":".$nwt_min.":"."00";

    $home_hr = ($_POST['home_hr'] == "") ? "00" : $_POST['home_hr'];
    $home_min = ($_POST['home_min'] == "") ? "00" : $_POST['home_min'];
    $home = $home_hr.":".$home_min.":"."00";


    $night_hr = ($_POST['night_hr'] == "") ? "00" : $_POST['night_hr'];
    $night_min = ($_POST['night_min'] == "") ? "00" : $_POST['night_min'];

    $night = $night_hr.":".$night_min.":"."00";


    $outoffice_hr = ($_POST['outoffice_hr'] == "") ? "00" : $_POST['outoffice_hr'];
    $outoffice_min = ($_POST['outoffice_min'] == "") ? "00" : $_POST['outoffice_min'];
    $outoffice = $outoffice_hr.":".$outoffice_min.":"."00";

    $health_hr = ($_POST['health_hr'] == "") ? "00" : $_POST['health_hr'];
    $health_min = ($_POST['health_min'] == "") ? "00" : $_POST['health_min'];
    $health = $health_hr.":".$health_min.":"."00";

    $sleep_hr = ($_POST['sleep_hr'] == "") ? "00" : $_POST['sleep_hr'];
    $sleep_min = ($_POST['sleep_min'] == "") ? "00" : $_POST['sleep_min'];
    $sleep = $sleep_hr.":".$sleep_min.":"."00";

    $system_ok = $_POST['system_ok'];

    $condition_remarks = addslashes($_POST['condition_remarks']);






    $remarks = addslashes($_POST['remarks']);
    if ($remarks == "") {
        if (isset($_POST['save_holiday']))
            $remarks = 'Holiday';
        else if (isset($_POST['save_leave']))
            $remarks = 'Leave';
    }

    $location = $_POST['location'];

    $time_query = "SELECT  id FROM tbl_time where user_id=$user_id and date_log='".$sql_date."'";
    $time_result = $mysqli->query($time_query);

    if ($time_result->num_rows > 0)
        $query = "update tbl_time set  time_in='$timein',time_out='$timeout',nwt='$nwt',home='$home',outoffice='$outoffice',night='$night',health='$health',sleep='$sleep',system_ok='$system_ok',condition_remarks='$condition_remarks',remarks='$remarks',location='$location',work_status='$work_status',late_remarks ='$late_remarks',not_punctual ='$not_punctual' where user_id=$user_id and date_log='".$sql_date."'";
    else
        $query = "INSERT INTO tbl_time (time_in,time_out,nwt,home,date_log,user_id,remarks,location,work_status,outoffice,night,health,sleep,system_ok,condition_remarks,late_remarks,not_punctual) VALUES ('$timein','$timeout','$nwt','$home','$sql_date','$user_id','$remarks','$location','$work_status','$outoffice','$night','$health','$sleep','$system_ok','$condition_remarks','$late_remarks','$not_punctual')";





    $mysqli->query($query);




    header("Location:jobdiary.php?date=".$date."&error=".$error);
    exit();
}



if (isset($_POST['save_complete_x']) || ($_POST['save_value'] == "save_date_complete")) {

    $sum_act = 0;
    $daily_result = $mysqli->query("SELECT sum(time_to_sec(act_time)) as sum_act FROM tbl_daily_job_status where user_id=$user_id and job_date='$sql_date'");
    $daily_row = $daily_result->fetch_assoc();
    $sum_act = $daily_row['sum_act'];



    $result = $mysqli->query("SELECT round(100*($sum_act+COALESCE(sum(time_to_sec(act_time)),0))/  ((time_to_sec(ADDTIME(home,night)))+time_to_sec(TIMEDIFF(TIMEDIFF(time_out,time_in),nwt))),2) as efficiency  FROM tbl_time t left join tbl_workreports w on w.date_report=t.date_log and (w.job_type!=41 || w.job_type is null)  and w.user_id=t.user_id WHERE t.date_log = '$sql_date' AND t.user_id = $user_id group by t.date_log");




    $row = $result->fetch_assoc();
    $efficiency_value = $row['efficiency'];


    $result->free();

    if (($efficiency_value > 100) || ($efficiency_value < 0)) {
        $error = 'efficiency_error';

        unset($_POST);



        header("Location:jobdiary.php?date=".$date."&error=".$error);
        exit();
    } else {

        $work_sql = "SELECT job_type,is_carry ,un_sch, TIME_TO_SEC( tIMEDIFF( tIMEDIFF( act_entry, est_entry )  , act_time ) ) as diff_entry,datediff(act_entry,date_report) as diff_date,workreport_id FROM tbl_workreports where user_id=$user_id and date_report='".$sql_date."'";

        $work_result = $mysqli->query($work_sql);

        while ($work_row = $work_result->fetch_assoc()) {
            $diff_entry = $work_row['diff_entry'];
            $diff_date = $work_row['diff_date'];
            $workreport_id = $work_row['workreport_id'];
            $is_carry = $work_row['is_carry'];
            $un_sch = $work_row['un_sch'];

            if ($un_sch == 1)
                $eff_ratio = 1;
            else if ($is_carry == 1)
                $eff_ratio = 1;
            else if (($diff_date == 0) && ($diff_entry > 0))
                $eff_ratio = 1;
            else
                $eff_ratio = 0.9;
            $mysqli->query("update tbl_workreports set start_id=if(start_id=0,workreport_id,start_id), eff_ratio=$eff_ratio  where workreport_id='$workreport_id'");
        }


        $mysqli->query("INSERT INTO tbl_workreports(taskname,is_carry,description,status,date_report,user_id,est_time,act_time,eff_ratio,main_type,job_type)
SELECT job.job_name,3,remarks,100,'$sql_date',stat.user_id,job.est_time,stat.act_time,1,job.main_type,job.sub_type
from tbl_daily_job_status stat left join  tbl_daily_jobs job on stat.job_id=job.id  where stat.user_id='$user_id' and job_date='$sql_date' and time_to_sec(act_time)>0 ");



        $mysqli->query("update tbl_workreports set is_current=0  where user_id='$user_id' and date_report='$sql_date' and status!=100");




        $mysqli->query("INSERT INTO tbl_workreports(prev_act,start_id,taskname,description,status,is_carry,date_report,user_id,main_type,job_type,job_no,client,job_est,target_date,is_current,cf_work_id,delegation_id)
SELECT addtime(prev_act,act_time),if(start_id=0,workreport_id,start_id) as start_id, taskname,'',status,1,cf_date,user_id,main_type,job_type,job_no,client,job_est,target_date,1,workreport_id,delegation_id
from tbl_workreports where user_id='$user_id' and is_carry!=2 and is_carry!=3 and date_report='$sql_date' and status!=100");


        $mysqli->query("INSERT INTO tbl_store(workreport_id,prev_act,act_time,start_id,taskname,description,status,date_report,user_id,main_type,job_type,job_no,client,job_est,target_date)
SELECT  workreport_id,prev_act,act_time,start_id,taskname,description,status,date_report,user_id,main_type,job_type,job_no,client,job_est,target_date
from tbl_workreports where user_id='$user_id' and is_carry=2 and date_report='$sql_date' and status!=100");


        if ((date('d',strtotime($sql_date)) == 7) || (date('d',strtotime($sql_date)) == 14) || (date('d',strtotime($sql_date)) == 21)) {

            $mysqli->query("update tbl_monthly_plan_details set start_id=if(start_id=0,plan_id,start_id)  where user_id=$user_id and plan_year=2016 and select_week='$week' and plan_month='$month' and is_current=1 and status!=100");

            $mysqli->query("INSERT INTO  tbl_monthly_plan_details(user_id,plan_month,plan_year,select_week, 	taskname,work_entry,description,status,is_plan,is_carry,cf_id,start_id,is_current)
SELECT  user_id, if(carry_forward=0,'$month',carry_forward),if(carry_forward_year=0,'2016',carry_forward_year),cf_week,taskname,work_entry,description,status,1,1,plan_id,start_id,1 from tbl_monthly_plan_details where user_id=$user_id  and plan_year=2016 and select_week='$week' and plan_month='$month' and is_current=1 and status!=100");


            $mysqli->query("update tbl_monthly_plan_details set is_current=0  where user_id='$user_id' and plan_year=2016 and select_week='$week' and plan_month='$month' and status!=100");
        }


        $completion_time = date('Y-m-d H:i:s',time());


        //if(($nwt_hr*3600+$nwt_min*60)>3600)
        //$nwt="01:00:00";




        $result = $mysqli->query("SELECT round(sum(time_to_sec(act_time))/((time_to_sec(ADDTIME(home,night)))+time_to_sec(TIMEDIFF(TIMEDIFF(time_out,time_in),'$nwt'))),2) as efficiency,sum(if((eff_ratio=1.00)&&(time_to_sec(act_time)>0),1,0)) as count_1,sum(if((row_id>0 )&&(is_carry=0)&&(time_to_sec(act_time)>0),1,0)) as new_jobs, sum(if(((time_to_sec(act_time)>0)),1,0)) as count_all,not_punctual FROM tbl_time t left join tbl_workreports w on w.date_report=t.date_log  and w.user_id=t.user_id WHERE (w.job_type!=41 or w.job_type is null) and  t.date_log = '$sql_date' AND t.user_id = $user_id group by t.date_log");




        $row = $result->fetch_assoc();
        $current_efficiency = $row['efficiency'];

        $count_1 = $row['count_1'];
        $count_all = $row['count_all'];

        $not_punctual = $row['not_punctual'];

        if ($count_all > 0)
            $unplan = 1 - ($count_1 / $count_all);
        else
            $unplan = 0;







        if ($system_ok <= 70)
            $system_ok = 1;

        if (($_POST['health_hr'] >= 1) || ($_POST['health_min'] >= 30))
            $no_health = 0;
        else {

            $health_hr = $_POST['health_hr'];
            $health_min = $_POST['health_min'];

            $health_sec = $health_min * 60;



            $no_health = 1 - round(($health_sec / 1800),2);
        }





        $late_remarks = addslashes($_POST['late_remarks']);


        $sum_result = $mysqli->query("select sec_to_time(sum(time_to_sec(act_time))) as sum_act from tbl_workreports  where user_id='$user_id' and date_report='$sql_date'");
        $sum_row = $sum_result->fetch_assoc();
        $sum_act = $sum_row['sum_act'];




        $mysqli->query("update tbl_time set is_complete=1,completion_time='$completion_time',current_efficiency='$current_efficiency',
no_health='$no_health',not_punctual='$not_punctual',unplan='$unplan',late_remarks='$late_remarks',total_job='$sum_act'  where user_id='$user_id' and date_log='$sql_date'");



        $mysqli->query("update tbl_workreports w,tbl_delegation d set d.target_date=w.target_date where w.delegation_id=d.delegation_id and w.user_id='$user_id' and w.date_report='$sql_date' and d.delegate_type=4 and (d.target_date='0000-00-00' or d.target_date is null)");





        header("Location:jobdiary.php?date=".$date."&error=".$error);
        exit();
    }
}





$time_query = "SELECT not_punctual as not_punctual_value,late_remarks,time_format(ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i'))
,TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(home,'%k:%i')),TIME_FORMAT(night, '%k:%i')),'%H:%i') as net_time,
time_to_sec(ADDTIME(ADDTIME(TIMEDIFF(TIMEDIFF(TIME_FORMAT(time_out, '%k:%i'),TIME_FORMAT(time_in,'%k:%i'))
,TIME_FORMAT(nwt, '%k:%i')),TIME_FORMAT(home,'%k:%i')),TIME_FORMAT(night, '%k:%i'))) as net_time_sec,
  work_status,IF(DATE_FORMAT(time_out,'%H:%i')!='00:00',time_out,'') as time_out,IF(DATE_FORMAT(time_in,'%H:%i')!='00:00',time_in,'') as time_in,IF(DATE_FORMAT(nwt,'%H:%i')!='00:00',nwt,'') as nwt,IF(DATE_FORMAT(home,'%H:%i')!='00:00',home,'') as home,IF(DATE_FORMAT(night,'%H:%i')!='00:00',night,'') as night,IF(DATE_FORMAT(outoffice,'%H:%i')!='00:00',outoffice,'') as outoffice,IF(DATE_FORMAT(health,'%H:%i')!='00:00',health,'') as  health,IF(DATE_FORMAT(sleep,'%H:%i')!='00:00',sleep,'') as  sleep,system_ok,condition_remarks,remarks,location,is_complete,(not_punctual*5) as not_punctual,(unplan*5) as unplan,(no_of_edit*5) as no_of_edit,(no_health*2) as no_health,IF(system_ok<=70,5,0) as system_error,(current_efficiency*100)as current_efficiency FROM tbl_time where user_id=$user_id and date_log='".$sql_date."'";

$time_result = $mysqli->query($time_query);
$time_row = $time_result->fetch_assoc();
$nwt = $time_row['nwt'];
$nwt_array = explode(":",$nwt);
$nwt_hr = $nwt_array[0];
$nwt_min = $nwt_array[1];


$home = $time_row['home'];
$home_array = explode(":",$home);
$home_hr = $home_array[0];
$home_min = $home_array[1];

$not_punctual_value = $time_row['not_punctual_value'];
$late_remarks = $time_row['late_remarks'];




$net_time = $time_row['net_time'];
$health = $time_row['health'];
$health_array = explode(":",$health);
$health_hr = $health_array[0];
$health_min = $health_array[1];

$sleep = $time_row['sleep'];
$sleep_array = explode(":",$sleep);
$sleep_hr = $sleep_array[0];
$sleep_min = $sleep_array[1];


$outoffice = $time_row['outoffice'];
$outoffice_array = explode(":",$outoffice);
$outoffice_hr = $outoffice_array[0];
$outoffice_min = $outoffice_array[1];

$night = $time_row['night'];
$night_array = explode(":",$night);
$night_hr = $night_array[0];
$night_min = $night_array[1];

$system_ok = $time_row['system_ok'];
$condition_remarks = $time_row['condition_remarks'];

$net_time_sec = $time_row['net_time_sec'];




$remarks = $time_row['remarks'];
$location = ($time_row['location'] == "") ? get_location($user_id) : $time_row['location'];

$work_status = $time_row['work_status'];



$timein = $time_row['time_in'];
$timeout = $time_row['time_out'];
$current_efficiency = $time_row['current_efficiency'];



$timein_array = explode(":",$timein);
$timeout_array = explode(":",$timeout);

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
        $timein_hr = sprintf("%02s",$timein_hr);
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
        $timeout_hr = sprintf("%02s",$timeout_hr);
    } else {
        if ($timeout_hr == 12)
            $ampm_out = "PM";
        else
            $ampm_out = "AM";
    }
} else
    $ampm_out = "PM";

$delegate_array = array("1" => "<font style='color:#FF0000;font-size:10px;font-weight:bold'>Delegation</font>","2" => "<font style='color:#000000;font-size:10px;font-weight:bold'>Sharing</font>","3" => "<font style='color:#0000FF;font-size:10px;font-weight:bold'>Requisition</font>","4" => "<font style='color:#9900CC;font-size:10px;font-weight:bold'>Proposal</font>");

$job_type_array = array();
$job_type_array[0] = '';
$source = '"",';

$selected_type_result = $mysqli->query("select * from tbl_user_customisation c left join   tbl_job_type t on c.custom_id=t.id where c.type='job_type' and   t.type_status='Active' and c.user_id='$user_id' order by t.job_type_name");

if ($selected_type_result->num_rows > 0) {
    $job_type_row = $selected_type_result->fetch_assoc();
    $job_type_array[$job_type_row['id']] = $job_type_row['job_type_name'];

    $source.='"'.$job_type_row['job_type_name'].'"';

    while ($job_type_row = $selected_type_result->fetch_assoc()) {
        $job_type_array[$job_type_row['id']] = $job_type_row['job_type_name'];

        $source.=',"'.$job_type_row['job_type_name'].'"';
    }
} else {
    $job_type_sql = "select * from  tbl_job_type order by job_type_name";
    $job_type_result = $mysqli->query($job_type_sql);


    $job_type_row = $job_type_result->fetch_assoc();
    $job_type_array[$job_type_row['id']] = $job_type_row['job_type_name'];

    $source.='"'.$job_type_row['job_type_name'].'"';

    while ($job_type_row = $job_type_result->fetch_assoc()) {
        $job_type_array[$job_type_row['id']] = $job_type_row['job_type_name'];

        $source.=',"'.$job_type_row['job_type_name'].'"';
    }
}


$main_type_array = array();
$main_type_array[0] = '';
$main_type_source = '"",';
$main_type_sql = "select * from   tbl_main_type order by main_type_id";
$main_type_result = $mysqli->query($main_type_sql);

$main_type_row = $main_type_result->fetch_assoc();
$main_type_array[$main_type_row['main_type_id']] = $main_type_row['main_type_name'];

$main_type_source.='"'.$main_type_row['main_type_name'].'"';

while ($main_type_row = $main_type_result->fetch_assoc()) {
    $main_type_array[$main_type_row['main_type_id']] = $main_type_row['main_type_name'];

    $main_type_source.=',"'.$main_type_row['main_type_name'].'"';
}



$job_no_array = array();
$job_no_array[0] = '';
$job_no_sql = "select * from tbl_job_no_delegation d left join tbl_job_numbers j on j.id=d.job_id where  d.assigned_to='$user_id'";
$job_no_result = $mysqli->query($job_no_sql);

$job_no_row = $job_no_result->fetch_assoc();
$job_no_array[$job_no_row['id']] = $job_no_row['job_type_name'];

$job_no_source = '"'.$job_no_row['job_no'].'"';

while ($job_no_row = $job_no_result->fetch_assoc()) {
    $job_no_array[$job_no_row['id']] = $job_no_row['job_no'];

    $job_no_source.=',"'.$job_no_row['job_no'].'"';
}





$sql = "SELECT  d.delegate_type,d.taskname,w.description,w.status,w.job_no,w.client,w.est_time,w.act_time,DATE_FORMAT(w.target_date,'%d-%m-%Y')  as  target_date,DATE_FORMAT(w.cf_date,'%d-%m-%Y')  as  cf_date,DATE_FORMAT(w.date_report,'%d-%m-%Y')  as  date_report, w.main_type,w.job_type,u.username  from tbl_workreports w  left join tbl_delegation d on d.delegation_id=w.delegation_id left join tbl_users u on u.user_id=w.user_id where w.taskname!='' and  d.assigned_by='$user_id' and  w.target_date<='$sql_date' and ((w.status!=100) or (w.status=100 and w.target_date='$sql_date') ) and w.is_current=1;";
$sql.="SELECT d.delegate_type, d.taskname,w.description,w.status,w.job_no,w.client,w.est_time,w.act_time,DATE_FORMAT(w.target_date,'%d-%m-%Y')  as  target_date,DATE_FORMAT(w.cf_date,'%d-%m-%Y')  as  cf_date,d.is_read,   w.job_type,w.main_type,u.username  from tbl_workreports w  left join tbl_delegation d on d.delegation_id=w.delegation_id left join tbl_users u on u.user_id=w.user_id where d.assigned_by='$user_id' and  w.date_report='$sql_date' and cf_work_id=0;";

$target = array();
$today = array();
$active = 'target';

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
            $active = 'today';
        }
    } while ($mysqli->next_result());
}

$sql = "SELECT DATE_FORMAT(w.job_est,'%H:%i') as  job_est, w.is_carry,d.delegate_type,w.workreport_id, d.taskname ,w.main_type,w.job_type,w.description ,w.job_no,w.client,IF(w.target_date='0000-00-00', '',DATE_FORMAT(w.target_date,'%d-%m-%Y'))  as  target_date,w.status,DATE_FORMAT(w.est_time,'%H') as  est_time_hr,DATE_FORMAT(w.est_time,'%i') as est_time_min,DATE_FORMAT(w.act_time,'%H') as  act_time_hr,DATE_FORMAT(w.act_time,'%i')act_time_min,d.assigned_by,d.is_reopen,d.reopen_remarks,IF(w.cf_date='0000-00-00', '',DATE_FORMAT(w.cf_date,'%d-%m-%Y'))  as  cf_date,u.username,w.eff_ratio from tbl_workreports w left join tbl_delegation d on d.delegation_id=w.delegation_id left join tbl_users u on u.user_id=d.assigned_by where w.user_id='$user_id' and d.assigned_by>0 and  w.date_report='$sql_date';";
$sql.= "SELECT  workreport_id,taskname ,w.main_type,w.job_type,description ,job_no,client,IF(target_date='0000-00-00', '',DATE_FORMAT(target_date,'%d-%m-%Y'))  as  target_date,status,DATE_FORMAT(est_time,'%H') as  est_time_hr,DATE_FORMAT(est_time,'%i') as est_time_min,DATE_FORMAT(act_time,'%H') as  act_time_hr,DATE_FORMAT(act_time,'%i')act_time_min,IF(cf_date='0000-00-00', '',DATE_FORMAT(cf_date,'%d-%m-%Y'))  as  cf_date,DATE_FORMAT(job_est,'%H:%i') as  job_est,DATE_FORMAT(prev_act,'%H:%i') as  prev_act,eff_ratio,is_carry from tbl_workreports w  where user_id='$user_id' and  delegation_id=0 and  (is_carry=1 or is_carry=2) and  date_report='$sql_date';";




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



$day = date('j',strtotime($sql_date));
if ($day >= 1 && $day <= 9)
    $week = 1;
else if ($day >= 10 && $day <= 16)
    $week = 2;
else if ($day >= 17 && $day <= 21)
    $week = 3;
else if ($day >= 22 && $day <= 28)
    $week = 4;
else if ($day >= 29 && $day <= 31)
    $week = 5;



$monthly_plan_sql = "SELECT  * from tbl_monthly_plan_details where user_id='$user_id' and status!=100 and is_current=1 and select_week='$week' and month('$sql_date')=plan_month and year('$sql_date')=plan_year and taskname!=''";
$monthly_plan_result = $mysqli->query($monthly_plan_sql);




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
$est_act_source = '"'.date('H:i',$tod).'"';
for ($i = 1; $i < (100 * 5); $i++) {
    $tod = $start + ($i * 5 * 60);
    $est_act_source.=',"'.date('H:i',$tod).'"';
}



$job_est = "";
$job_est = '"00:00"';

for ($hours = 0; $hours <= 100; $hours++)
    for ($minutes = 0; $minutes < 60; $minutes+=30)
        $job_est.=',"'.sprintf("%02d:%02d",$hours,$minutes).'"';









$routine_result = $mysqli->query(" SELECT s.remarks,j.id,j.job_name,j.main_type,j.sub_type,j.approved_by,j.auth_status,

							if(s.id>0,DATE_FORMAT(s.est_time,'%H'),DATE_FORMAT(j.est_time,'%H')) as est_time_hr,
							if(s.id>0,DATE_FORMAT(s.est_time,'%i'),DATE_FORMAT(j.est_time,'%i')) as est_time_min,
							DATE_FORMAT(s.act_time,'%H') as  act_time_hr,DATE_FORMAT(s.act_time,'%i') as act_time_min,
							s.status FROM tbl_daily_jobs j  left join tbl_daily_job_status s
								on j.id=s.job_id and s.job_date='$sql_date' where j.status=1 and  j.user_id=$user_id")
        or die('Error, query failed');





while ($row = $routine_result->fetch_assoc())
    $routine[] = $row;



$time_work_result = $mysqli->query("select SUM(TIME_TO_SEC(est_time)) as total_est,SUM(TIME_TO_SEC(act_time)) as total_act  from  tbl_workreports where  main_type!=6 and job_type!=41  and date_report='$sql_date' and user_id='$user_id'");
$time_work_row = $time_work_result->fetch_assoc();
$est = $time_work_row['total_est'];
$act = $time_work_row['total_act'];


$routine_time_result = $mysqli->query("select SUM(TIME_TO_SEC(

if(status.id>0,status.est_time,daily.est_time)



)) as total_est,SUM(TIME_TO_SEC(status.act_time)) as total_act  from   tbl_daily_jobs daily left join tbl_daily_job_status status on daily.id=status.job_id and job_date='$sql_date' where   daily.status=1 and daily.user_id='$user_id' ");
$routine_time_row = $routine_time_result->fetch_assoc();
$routine_est = $routine_time_row['total_est'];
$routine_act = $routine_time_row['total_act'];






$mysqli->query("update  tbl_workreports w ,tbl_delegation d  set d.is_read=1 where w.delegation_id=d.delegation_id and w.date_report='$sql_date' and w.user_id='$user_id'  and d.assigned_by>0");


if ($time_row['is_complete'] == 1) {

    $result = $mysqli->query("SELECT round(100*(sum(time_to_sec(act_time)*eff_ratio))/(time_to_sec(TIMEDIFF(TIMEDIFF(time_out,time_in),nwt))),2) as efficiency  FROM tbl_time t left join tbl_workreports w on w.date_report=t.date_log  and w.user_id=t.user_id WHERE t.date_log = '$sql_date' AND t.user_id = $user_id group by t.date_log");

    $row = $result->fetch_assoc();
    $eff_ratio = $row['efficiency'];
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
    <script type="text/javascript" src="js/popup.js"></script>
    <link href="main.css" rel="stylesheet" type="text/css" />

    <script src="javascript/js.js" type="text/javascript"></script>
    <script language="javascript">

        function openWindow(url, title)
        {
            var left = (screen.width - 900) / 2;
            var top = (screen.height - 500) / 2;
            return window.open(url, title, 'width=1024,height=500,left=' + left + ',top=' + top + ',screenX=' + left + ',screenY=' + top + ',status=no,scrollbars=yes');
        }

        function monthly_auto_save(plan_id, type)
        {
            description = document.getElementById('description' + plan_id).value;
            carry_forward = document.getElementById('carry_forward' + plan_id).value;
            cf_week = document.getElementById('cf_week' + plan_id).value;

            select_week =<?php echo $week;?>;


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
            request_save.send('plan_id=' + plan_id + '&description=' + description + "&status=" + status + "&carry_forward=" + carry_forward + "&cf_week=" + cf_week + "&select_week=" + select_week);
        }




        function auto_time_save()
        {

            timein_hr = document.getElementById('timein_hr').value;
            timein_min = document.getElementById('timein_min').value;
            timeout_hr = document.getElementById('timeout_hr').value;
            timeout_min = document.getElementById('timeout_min').value;
            nwt_hr = document.getElementById('nwt_hr').value;
            nwt_min = document.getElementById('nwt_min').value;
            outoffice_hr = document.getElementById('outoffice_hr').value;
            outoffice_min = document.getElementById('outoffice_min').value;
            home_hr = document.getElementById('home_hr').value;
            home_min = document.getElementById('home_min').value;
            night_hr = document.getElementById('night_hr').value;
            night_min = document.getElementById('night_min').value;
            health_hr = document.getElementById('health_hr').value;
            health_min = document.getElementById('health_min').value;
            sleep_hr = document.getElementById('sleep_hr').value;
            sleep_min = document.getElementById('sleep_min').value;

            //ampm_in=document.getElementById('est_time_hr').value;
            //ampm_out=document.getElementById('est_time_hr').value;


            if (timein_hr == "")
                timein_hr = "00";
            if (timein_min == "")
                timein_min = "00";
            if (timeout_hr == "")
                timeout_hr = "00";
            if (timeout_min == "")
                timeout_min = "00";
            if (nwt_hr == "")
                nwt_hr = "00";
            if (nwt_min == "")
                nwt_min = "00";
            if (outoffice_hr == "")
                outoffice_hr = "00";
            if (outoffice_min == "")
                outoffice_min = "00";
            if (home_hr == "")
                home_hr = "00";
            if (home_min == "")
                home_min = "00";
            if (night_hr == "")
                night_hr = "00";
            if (night_min == "")
                night_min = "00";
            if (health_hr == "")
                health_hr = "00";
            if (health_min == "")
                health_min = "00";
            if (sleep_hr == "")
                sleep_hr = "00";
            if (sleep_min == "")
                sleep_min = "00";





            var s = document.getElementById('ampm_in');
            var ampm_in = s.options[s.selectedIndex].value;
            var s = document.getElementById('ampm_out');
            var ampm_out = s.options[s.selectedIndex].value;




            var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

            request_save.open("POST", "auto_save.php", true);

            request_save.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            request_save.onreadystatechange = function () {
                if (request_save.readyState == 4)
                {
                    if (request_save.responseText == "Logout")
                    {
                        alert("Your Session has been Expired Please Login Again");
                        window.location.href = 'index.php';


                    }



                }
            }


            request_save.send("date=<?php echo $date?>&" +
                    "time_auto_save=1&timein_hr=" + timein_hr +
                    "&timein_min=" + timein_min +
                    "&timeout_hr=" + timeout_hr +
                    "&timeout_min=" + timeout_min +
                    "&nwt_hr=" + nwt_hr +
                    "&nwt_min=" + nwt_min +
                    "&outoffice_hr=" + outoffice_hr +
                    "&outoffice_min=" + outoffice_min +
                    "&home_hr=" + home_hr +
                    "&home_min=" + home_min +
                    "&night_hr=" + night_hr +
                    "&night_min=" + night_min +
                    "&health_hr=" + health_hr +
                    "&health_min=" + health_min +
                    "&sleep_hr=" + sleep_hr +
                    "&ampm_in=" + ampm_in +
                    "&ampm_out=" + ampm_out +
                    "&sleep_min=" + sleep_min);

        }


        function auto_save(id, type)
        {


            est_time_hr = document.getElementById('est_time_hr' + id).value;
            est_time_min = document.getElementById('est_time_min' + id).value;
            act_time_hr = document.getElementById('act_time_hr' + id).value;
            act_time_min = document.getElementById('act_time_min' + id).value;
            description = document.getElementById('description' + id).value;
            cf_date = document.getElementById('cf_date' + id).value;
            is_carry = "";
            target_date = "";


            if (document.getElementById('target_date' + id) && (document.getElementById('target_date' + id).value != ""))
            {
                target_date = document.getElementById('target_date' + id).value;
            }


            if (est_time_hr == "")
                est_time_hr = "00";
            if (act_time_hr == "")
                act_time_hr = "00";

            if (est_time_min == "")
                est_time_min = "00";
            if (act_time_min == "")
                act_time_min = "00";



            if (document.getElementById('is_carry' + id))
            {

                if (document.getElementById('is_carry' + id).type == "hidden")
                {
                    is_carry = document.getElementById('is_carry' + id).value;
                }
                else
                {
                    if (document.getElementById('is_carry' + id).checked == true)
                        is_carry = 2
                    else
                        is_carry = 1
                }


            }


            est_time = est_time_hr + ":" + est_time_min + ":00";
            act_time = act_time_hr + ":" + act_time_min + ":00";


            var s = document.getElementById('status' + id);
            var status = s.options[s.selectedIndex].value;

            if (status == 100)
                document.getElementById('td_status_' + id).style.background = '#e9e9e9';
            else
                document.getElementById('td_status_' + id).style.background = '#FF0000';



            var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

            request_save.open("POST", "auto_save.php", true);

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
                        time_array = (request_save.responseText).split("&");

                        document.getElementById("est_div").innerHTML = time_array[0];
                        document.getElementById("act_div").innerHTML = time_array[1];
                        document.getElementById("routine_est_div").innerHTML = time_array[2];
                        document.getElementById("routine_act_div").innerHTML = time_array[3];
                        document.getElementById("total_est_div").innerHTML = time_array[4];
                        document.getElementById("total_act_div").innerHTML = time_array[5];

                    }


                }
            }

            if (target_date != "")
                request_save.send('date=<?php echo $date?>&workreport_id=' + id + '&est_time=' + est_time + '&act_time=' + act_time + '&description=' + description + "&status=" + status + "&cf_date=" + cf_date + "&type=" + type + "&target_date=" + target_date);
            else
                request_save.send('date=<?php echo $date?>&workreport_id=' + id + '&est_time=' + est_time + '&act_time=' + act_time + '&description=' + description + "&status=" + status + "&cf_date=" + cf_date + "&type=" + type + "&is_carry=" + is_carry);

        }




        function routine_save(id)
        {

            daily_est_hr = document.getElementById('daily_est_hr' + id).value;
            daily_est_min = document.getElementById('daily_est_min' + id).value;


            daily_act_hr = document.getElementById('daily_act_hr' + id).value;
            daily_act_min = document.getElementById('daily_act_min' + id).value;

            routine_remarks = document.getElementById('routine_remarks' + id).value;


            if (daily_act_hr == "")
                daily_act_hr = "00";

            if (daily_act_min == "")
                daily_act_min = "00";


            daily_act = daily_act_hr + ":" + daily_act_min + ":00";


            if (daily_est_hr == "")
                daily_est_hr = "00";
            if (daily_est_min == "")
                daily_est_min = "00";
            daily_est = daily_est_hr + ":" + daily_est_min + ":00";




            var request_save = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");

            request_save.open("POST", "auto_save.php", true);

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
                        time_array = (request_save.responseText).split("&");

                        document.getElementById("est_div").innerHTML = time_array[0];
                        document.getElementById("act_div").innerHTML = time_array[1];
                        document.getElementById("routine_est_div").innerHTML = time_array[2];
                        document.getElementById("routine_act_div").innerHTML = time_array[3];
                        document.getElementById("total_est_div").innerHTML = time_array[4];
                        document.getElementById("total_act_div").innerHTML = time_array[5];

                    }


                }
            }
            request_save.send('date=<?php echo $date?>&job_id=' + id + '&routine=1&daily_est=' + daily_est + "&daily_act=" + daily_act + "&routine_remarks=" + routine_remarks);

        }





        from_page = 'jobdiary';
        function check_form(form, type, status)
        {

            var timein_hr = document.getElementById('timein_hr').value;
            var timein_min = document.getElementById('timein_min').value;
            var timeout_hr = document.getElementById('timeout_hr').value;
            var timeout_min = document.getElementById('timeout_min').value;
            var ampm_in = document.getElementById('ampm_in').value;
            var ampm_out = document.getElementById('ampm_out').value;
            var date_from = document.getElementById('date_from').value;

            var outoffice_hr = document.getElementById('outoffice_hr').value;
            var outoffice_min = document.getElementById('outoffice_min').value;


            var home_hr = document.getElementById('home_hr').value;
            var home_min = document.getElementById('home_min').value;


            var nwt_hr = document.getElementById('nwt_hr').value;
            var nwt_min = document.getElementById('nwt_min').value;
            var remarks = document.getElementById('remarks').value;

            var system_ok = document.getElementById('system_ok').value;
            var condition_remarks = document.getElementById('condition_remarks').value;

            remarks = remarks.trim();

            if (type == 'save_data')
            {


                if ((timein_hr != "") && (timein_min == ""))
                {
                    alert("Please Enter Time In Mins");
                    document.getElementById('timein_min').focus();
                    return false;
                }
                if ((timein_min != "") && (timein_hr == ""))
                {
                    alert("Please Enter Time In Hrs");
                    document.getElementById('timein_hr').focus();
                    return false;
                }

                if (timein_hr > 12)
                {
                    alert("Please Enter Correct Time in");
                    document.getElementById('timein_hr').focus();
                    return false;
                }


                if (timein_min >= 60)
                {
                    alert("Please Enter Correct Time in Minutes");
                    document.getElementById('timein_min').focus();
                    return false;
                }


                if ((timeout_hr != "") && (timeout_min == ""))
                {
                    alert("Please Enter Time Out Mins");
                    document.getElementById('timeout_min').focus();
                    return false;
                }
                if ((timeout_min != "") && (timeout_hr == ""))
                {
                    alert("Please Enter Time Out Hrs");
                    document.getElementById('timeout_hr').focus();
                    return false;
                }

                if (timeout_hr > 12)
                {
                    alert("Please Enter Correct Time Out");
                    document.getElementById('timeout_hr').focus();
                    return false;
                }

                if (timeout_min > 60)
                {
                    alert("Please Enter Correct Time out Minutes");
                    document.getElementById('timeout_min').focus();
                    return false;
                }




                if (nwt_hr >= 24)
                {
                    alert("Please Enter Correct Break Time");
                    document.getElementById('nwt_hr').focus();
                    return false;
                }



                if ((system_ok >= 10) && (system_ok <= 70) && (condition_remarks == ""))
                {
                    alert("Please Enter the Reason For Poor Condition");
                    document.getElementById('condition_remarks').focus();
                    return false;
                }
                if (system_ok >= 80)
                {
                    document.getElementById('condition_remarks').value = "";

                }


                var date1 = new Date('01/14/2014 ' + '<?php echo $_SESSION['reporting_time']?>');

                var date2 = new Date('01/14/2014 ' + timein_hr + ':' + timein_min + ':00 ' + ampm_in);

                var sec = (date2.getTime() / 1000.0) - (date1.getTime() / 1000.0);

                document.getElementById('late_remarks').value = "<?php echo $late_remarks?>";
                if (sec > 0)
                {
<?php if ($not_punctual_value == "1") {?>

                        document.getElementById("not_punctual").selectedIndex = 1;


    <?php
} else if (($not_punctual_value == "0") && ($late_remarks != "")) {
    ?>

                        document.getElementById("not_punctual").selectedIndex = 2;



    <?php
} else {
    ?>

                        document.getElementById("not_punctual").selectedIndex = 0;

<?php }
?>


                    if (document.getElementById('late_remarks').value == "")
                    {
                        document.getElementById('save_value').value = 'save_date';




                        Popup.showModal('modal');
                        return false;

                    }
                    else
                        return true;

                }

                document.getElementById('not_punctual').value = 0;
                document.getElementById('late_remarks').value = "";

                document.getElementById('save_value').value = 'save_date';


                return true;
            }
            else
            {
                if (type == 'save_complete')
                {


                    if ((status != "leave") && (status != "holiday"))
                    {


                        if ((timein_hr == "") && (timein_min == "") && (timeout_hr == "") && (timeout_min == "") && (home_min == "") && (home_hr == ""))
                        {
                            alert("Please Enter Your Time ");
                            return false;
                        }


                        if ((timein_hr != "") && (timein_min == ""))
                        {
                            alert("Please Enter Time In Mins");
                            document.getElementById('timein_min').focus();
                            return false;
                        }
                        if ((timein_min != "") && (timein_hr == ""))
                        {
                            alert("Please Enter Time In Hrs");
                            document.getElementById('timein_hr').focus();
                            return false;
                        }

                        if (timein_hr > 12)
                        {
                            alert("Please Enter Correct Time in");
                            document.getElementById('timein_hr').focus();
                            return false;
                        }


                        if (timein_min >= 60)
                        {
                            alert("Please Enter Correct Time in Minutes");
                            document.getElementById('timein_min').focus();
                            return false;
                        }


                        if ((timeout_hr != "") && (timeout_min == ""))
                        {
                            alert("Please Enter Time Out Mins");
                            document.getElementById('timeout_min').focus();
                            return false;
                        }
                        if ((timeout_min != "") && (timeout_hr == ""))
                        {
                            alert("Please Enter Time Out Hrs");
                            document.getElementById('timeout_hr').focus();
                            return false;
                        }

                        if (timeout_hr > 12)
                        {
                            alert("Please Enter Correct Time Out");
                            document.getElementById('timeout_hr').focus();
                            return false;
                        }

                        if (timeout_min > 60)
                        {
                            alert("Please Enter Correct Time out Minutes");
                            document.getElementById('timeout_min').focus();
                            return false;
                        }




                        if (nwt_hr >= 24)
                        {
                            alert("Please Enter Correct Break Time");
                            document.getElementById('nwt_hr').focus();
                            return false;
                        }


                        if ((timein_hr != "") && (timeout_hr == ""))
                        {
                            alert("Please Enter Office Time Out");
                            document.getElementById('timeout_hr').focus();
                            return false;
                        }

                        if ((timeout_hr != "") && (timein_hr == ""))
                        {
                            alert("Please Enter Office Time In");
                            document.getElementById('timein_hr').focus();
                            return false;
                        }








                        var date1 = new Date('01/14/2014 ' + timein_hr + ':' + timein_min + ':00 ' + ampm_in);
                        var date2 = new Date('01/14/2014 ' + timeout_hr + ':' + timeout_min + ':00 ' + ampm_out);


                        var sec = (date2.getTime() / 1000.0) - (date1.getTime() / 1000.0);

                        if (nwt_hr == "")
                            nwt_hr = 0;
                        if (nwt_min == "")
                            nwt_min = 0;

                        nwt_sec = (nwt_hr) * 3600 + (nwt_min * 60);

                        eff = (sec - nwt_sec);

                        if (eff <= 0)
                        {
                            alert("Please enter correct Time in and Time out and Break Time");
                            return false;

                        }


                        if ((system_ok >= 10) && (system_ok <= 70) && (condition_remarks == ""))
                        {
                            alert("Please Enter the Reason For Poor Condition");
                            document.getElementById('condition_remarks').focus();
                            return false;
                        }
                        var date1 = new Date('01/14/2014 ' + '<?php echo $_SESSION['reporting_time']?>');

                        var date2 = new Date('01/14/2014 ' + timein_hr + ':' + timein_min + ':00 ' + ampm_in);

                        var sec = (date2.getTime() / 1000.0) - (date1.getTime() / 1000.0);


                        if (sec > 0)
                        {
<?php if ($not_punctual_value == "1") {?>

                                document.getElementById("not_punctual").selectedIndex = 1;

                                document.getElementById('late_remarks').value = "<?php echo $late_remarks?>";
    <?php
} else if (($not_punctual_value == "0") && ($late_remarks != "")) {
    ?>

                                document.getElementById("not_punctual").selectedIndex = 2;

                                document.getElementById('late_remarks').value = "<?php echo $late_remarks?>";

    <?php
} else {
    ?>

                                document.getElementById("not_punctual").selectedIndex = 0;

<?php }
?>
                            document.getElementById('save_value').value = 'save_date_complete';



                            Popup.showModal('modal');
                            return false;
                        }




                    }
                    document.getElementById('save_value').value = 'save_date_complete';

                    document.getElementById('complete_button').style.display = "none";
                    document.getElementById('complete_message').style.display = "";




                    var status = confirm("Are You Sure Want to Complete this JobDiary....?");

                    if (status == true)
                        return true;
                    else
                    {

                        document.getElementById('complete_button').style.display = "";
                        document.getElementById('complete_message').style.display = "none";
                        return false;
                    }





                }
            }

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
    <form name="jobform" method="POST" action="jobdiary.php"
          enctype="multipart/form-data" >
        <table width="100%" border="0" cellspacing="0" cellpadding="0"
               align="center">
                   <?php
                   include("includes/header.php");
                   ?>
            <tr>
                <?php
                if ($time_row['is_complete'] == 1) {

                } else {

                    $option = '

                <option	 value="100"> 100 % </option>
                <option	 value="99"> 99 % </option>
                <option	 value="98"> 98 % </option>
                <option	 value="97"> 97 % </option>
                <option	 value="96"> 96 % </option>
                <option	 value="95"> 95 % </option>
                <option	 value="94"> 94 % </option>
                <option	 value="93"> 93 % </option>
                <option	 value="92"> 92 % </option>
                <option	 value="91"> 91 % </option>
                <option	 value="90"> 90 % </option>
                <option	 value="89"> 89 % </option>
                <option	 value="88"> 88 % </option>
                <option	 value="87"> 87 % </option>
                <option	 value="86"> 86 % </option>
                <option	 value="85"> 85 % </option>
                <option	 value="84"> 84 % </option>
                <option	 value="83"> 83 % </option>
                <option	 value="82"> 82 % </option>
                <option	 value="81"> 81 % </option>
                <option	 value="80"> 80 % </option>
                <option	 value="79"> 79 % </option>
                <option	 value="78"> 78 % </option>
                <option	 value="77"> 77 % </option>
                <option	 value="76"> 76 % </option>
                <option	 value="75"> 75 % </option>
                <option	 value="74"> 74 % </option>
                <option	 value="73"> 73 % </option>
                <option	 value="72"> 72 % </option>
                <option	 value="71"> 71 % </option>
                <option	 value="70"> 70 % </option>
                <option	 value="69"> 69 % </option>
                <option	 value="68"> 68 % </option>
                <option	 value="67"> 67 % </option>
                <option	 value="66"> 66 % </option>
                <option	 value="65"> 65 % </option>
                <option	 value="64"> 64 % </option>
                <option	 value="63"> 63 % </option>
                <option	 value="62"> 62 % </option>
                <option	 value="61"> 61 % </option>
                <option	 value="60"> 60 % </option>
                <option	 value="59"> 59 % </option>
                <option	 value="58"> 58 % </option>
                <option	 value="57"> 57 % </option>
                <option	 value="56"> 56 % </option>
                <option	 value="55"> 55 % </option>
                <option	 value="54"> 54 % </option>
                <option	 value="53"> 53 % </option>
                <option	 value="52"> 52 % </option>
                <option	 value="51"> 51 % </option>
                <option	 value="50"> 50 % </option>
                <option	 value="49"> 49 % </option>
                <option	 value="48"> 48 % </option>
                <option	 value="47"> 47 % </option>
                <option	 value="46"> 46 % </option>
                <option	 value="45"> 45 % </option>
                <option	 value="44"> 44 % </option>
                <option	 value="43"> 43 % </option>
                <option	 value="42"> 42 % </option>
                <option	 value="41"> 41 % </option>
                <option	 value="40"> 40 % </option>
                <option	 value="39"> 39 % </option>
                <option	 value="38"> 38 % </option>
                <option	 value="37"> 37 % </option>
                <option	 value="36"> 36 % </option>
                <option	 value="35"> 35 % </option>
                <option	 value="34"> 34 % </option>
                <option	 value="33"> 33 % </option>
                <option	 value="32"> 32 % </option>
                <option	 value="31"> 31 % </option>
                <option	 value="30"> 30 % </option>
                <option	 value="29"> 29 % </option>
                <option	 value="28"> 28 % </option>
                <option	 value="27"> 27 % </option>
                <option	 value="26"> 26 % </option>
                <option	 value="25"> 25 % </option>
                <option	 value="24"> 24 % </option>
                <option	 value="23"> 23 % </option>
                <option	 value="22"> 22 % </option>
                <option	 value="21"> 21 % </option>
                <option	 value="20"> 20 % </option>
                <option	 value="19"> 19 % </option>
                <option	 value="18"> 18 % </option>
                <option	 value="17"> 17 % </option>
                <option	 value="16"> 16 % </option>
                <option	 value="15"> 15 % </option>
                <option	 value="14"> 14 % </option>
                <option	 value="13"> 13 % </option>
                <option	 value="12"> 12 % </option>
                <option	 value="11"> 11 % </option>
                <option	 value="10"> 10 % </option>
                <option	 value="9"> 9 % </option>
                <option	 value="8"> 8 % </option>
                <option	 value="7"> 7 % </option>
                <option	 value="6"> 6 % </option>
                <option	 value="5"> 5 % </option>
                <option	 value="4"> 4 % </option>
                <option	 value="3"> 3 % </option>
                <option	 value="2"> 2 % </option>
                <option	 value="1"> 1 % </option>
                <option	 value="0"> 0 % </option>';
                    ?>
                    <td><?php
                        if (isset($_GET['error'])) {


                            if ($_GET['error'] == "maintype_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Please Select Proper Main Type</strong></div>
                                <?php
                            } else if ($_GET['error'] == "Job_No_Failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Job Number Is Mandatory For Invoiceable Main Type</strong></div>
                                <?php
                            } else if ($_GET['error'] == "CF_Target_Failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Target Date is mandatory for all Newly Carry Forwarded Jobs</strong></div>
                                <?php
                            } else if ($_GET['error'] == "Description_Failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Remarks is mandatory for all Carry Forwarded Jobs</strong></div>
                                <?php
                            } else if ($_GET['error'] == "complete_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Please Select Proper Carry Forward Date if the Status is less than 100</strong></div>
                                <?php
                            } else if ($_GET['error'] == "proposal_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Please Select Proper  Target Date For the  Proposal</strong></div>
                                <?php
                            } else if ($_GET['error'] == "delegation_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Carry Forward Date Exceeds Delegation Target Date. Contact Delegator</strong></div>
                                <?php
                            } else if ($_GET['error'] == "efficiency_error") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Efficiency Error. Please Check the Actual Time</strong></div>
                                <?php
                            } else if ($_GET['error'] == "weekly_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Please Select Proper Carry Forward Week for the Weekly Plan</strong></div>
                                <?php
                            } else if ($_GET['error'] == "equipment_failure") {
                                ?>
                                <div style="width:100%; text-align:center; color:#F00; font-size:20px"><strong>Please Select  Proper  Utilisation Status/Usage Cost for the Equipment</strong></div>
                                <?php
                            }
                        }
                        ?>
                        <br>
                        <table id="customers" border="0" align="center" cellpadding="0" cellspacing="0"
                               width="99%" class="form_style">
                            <tr height="25">
                                <td colspan="6" align="left" bgcolor="#e2f3fd"><b
                                        style="vertical-align: middle">Date:</b>
                                    <input type="hidden" name="save_value" id="save_value">
                                    <input type="text"
                                           name="date" style="vertical-align: middle" size="6"
                                           id="date_from" readonly value="<?php echo $date?>"
                                           onClick="return showCal('date_from', 'dd-mm-y');">
                                    <img
                                        title="Calendar" style="vertical-align: middle;"
                                        src="images/calendar0.gif"
                                        onClick="return showCal('date_from', 'dd-mm-y');" border="0"> <!--<img src="images/Holiday.gif" onClick="holiday();" style="vertical-align:middle; cursor:pointer;">-->
                                    <b>Duty Start: </b>
                                    <input onChange="auto_time_save()"
                                           type="text" name="timein_hr" maxlength="2" id="timein_hr"
                                           class="txt_box" size="2" value="<?php echo $timein_hr;?>"
                                           style="vertical-align: middle"><strong>:</strong><input onChange="auto_time_save()"
                                           type="text" name="timein_min" maxlength="2" id="timein_min"
                                           class="txt_box" size="2" value="<?php echo $timein_min;?>"
                                           style="vertical-align: middle">
                                    <select onChange="auto_time_save()" name="ampm_in"
                                            class="txt_box" id="ampm_in" style="vertical-align: middle">
                                        <option value="am"
                                        <?php if ($ampm_in == "AM") {?>
                                                    selected="selected"
                                                <?php }?>>AM</option>
                                        <option value="pm"
                                        <?php if ($ampm_in == "PM") {?>
                                                    selected="selected"
                                                <?php }?>>PM</option>
                                    </select>
                                    <b>Duty End: </b>
                                    <input onChange="auto_time_save()"  type="text"
                                           name="timeout_hr" maxlength="2" id="timeout_hr" class="txt_box"
                                           size="2"
                                           value="<?php echo $timeout_hr;?>"><strong>:</strong><input onChange="auto_time_save()"  type="text"
                                           name="timeout_min" maxlength="2" id="timeout_min" class="txt_box"
                                           size="2"
                                           value="<?php echo $timeout_min;?>">

                                    <select onChange="auto_time_save()"
                                            name="ampm_out" class="txt_box" id="ampm_out">
                                        <option value="am"
                                        <?php if ($ampm_out == "AM") {?>
                                                    selected="selected"
                                                <?php }?>>AM</option>
                                        <option value="pm"
                                        <?php if ($ampm_out == "PM") {?>
                                                    selected="selected"
                                                <?php }?>>PM</option>
                                    </select>
                                    <b>Duty Break:</b>
                                    <input onChange="auto_time_save()"  type="text" name="nwt_hr"
                                           maxlength="2" id="nwt_hr" max="5" min="0" class="txt_box" size="2"
                                           value="<?php echo $nwt_hr;?>">
                                    <strong>:</strong>
                                    <input onChange="auto_time_save()"  type="text" name="nwt_min"
                                           maxlength="2" id="nwt_min" class="txt_box" size="2"
                                           value="<?php echo $nwt_min;?>">
                                    <b>Outside Duty:</b></b>
                                    <input onChange="auto_time_save()"  type="text" name="outoffice_hr"
                                           maxlength="2" id="outoffice_hr" max="5" min="0" class="txt_box" size="1"
                                           value="<?php echo $outoffice_hr;?>"><strong>:</strong><input onChange="auto_time_save()"  type="text" name="outoffice_min"
                                           maxlength="2" id="outoffice_min" class="txt_box" size="1"
                                           value="<?php echo $outoffice_min;?>">
                                    <b>Work At Home:</b></b>
                                    <input onChange="auto_time_save()"  type="text" name="home_hr"
                                           maxlength="2" id="home_hr" max="5" min="0" class="txt_box" size="1"
                                           value="<?php echo $home_hr;?>"><strong>:</strong><input onChange="auto_time_save()"  type="text" name="home_min"
                                           maxlength="2" id="home_min" class="txt_box" size="1"
                                           value="<?php echo $home_min;?>">
                                    <b>Mid Night:</b><input onChange="auto_time_save()"  type="text" name="night_hr"
                                                            maxlength="2" id="night_hr" max="5" min="0" class="txt_box" size="1"
                                                            value="<?php echo $night_hr;?>"><strong>:</strong><input onChange="auto_time_save()"  type="text" name="night_min"
                                                            maxlength="2" id="night_min" class="txt_box" size="1"
                                                            value="<?php echo $night_min;?>">
                                    &nbsp;</td>
                            </tr>

                            <tr height="15">
                                <td colspan="6" align="left" bgcolor="#e2f3fd">

                                    <font style="color:#FF0000; font-weight:bold">Net time = (Duty End- Duty Start-Break)+Work At Home +Mid Night Hours</font><font style="color:#0000FF; font-size:9px; font-weight:bold"> (Outside office=To  know  about the  Job . it is not added in the  net time)</font>

                                    &nbsp;</td>
                            </tr>


                            <tr height="25">
                                <td colspan="6" align="left" bgcolor="#e2f3fd">&nbsp;
                                    <b>Time Spent For Health : </b>
                                    <input onChange="auto_time_save()"  type="text" name="health_hr"
                                           maxlength="2" id="health_hr" max="5" min="0" class="txt_box" size="2"
                                           value="<?php echo $health_hr;?>">
                                    <strong>:</strong>
                                    <input onChange="auto_time_save()"  type="text" name="health_min"
                                           maxlength="2" id="health_min" class="txt_box" size="2"
                                           value="<?php echo $health_min;?>">

                                    &nbsp;      &nbsp;&nbsp;
                                    <b style="font-weight:bold; color:#FF0000">Time Spent For Sleep : </b>
                                    <input onChange="auto_time_save()"  type="text" name="sleep_hr"
                                           maxlength="2" id="sleep_hr" max="5" min="0" class="txt_box" size="2"
                                           value="<?php echo $sleep_hr;?>">
                                    <strong>:</strong>
                                    <input onChange="auto_time_save()"  type="text" name="sleep_min"
                                           maxlength="2" id="sleep_min" class="txt_box" size="2"
                                           value="<?php echo $sleep_min;?>">

                                    &nbsp;      &nbsp; <b style="color:#FF0000; font-weight:bold">System/Internet/Working  Atmosphere:</b></b>
                                    <select onChange="display_condition_remarks(this.value)" id="system_ok" name="system_ok"><option value=""></option>
                                        <optgroup label="Good">
                                            <option value="100" <?php if ($system_ok == "100") {?> selected <?php }?>>100%</option>
                                            <option value="90" <?php if ($system_ok == "90") {?> selected <?php }?>>90%</option>
                                            <option value="80" <?php if ($system_ok == "80") {?> selected <?php }?>>80%</option>
                                        </optgroup>
                                        <optgroup label="Poor">
                                            <option value="70" <?php if ($system_ok == "70") {?> selected <?php }?>>70%</option>
                                            <option value="60" <?php if ($system_ok == "60") {?> selected <?php }?>>60%</option>
                                            <option value="50" <?php if ($system_ok == "50") {?> selected <?php }?>>50%</option>
                                        </optgroup>
                                        <optgroup label="Very Poor">
                                            <option value="40" <?php if ($system_ok == "40") {?> selected <?php }?>>40%</option>
                                            <option value="30" <?php if ($system_ok == "30") {?> selected <?php }?>>30%</option>
                                            <option value="20" <?php if ($system_ok == "20") {?> selected <?php }?>>20%</option>
                                            <option value="10" <?php if ($system_ok == "10") {?> selected <?php }?>>10%</option>
                                        </optgroup>
                                    </select> <strong>OK</strong>


                                    &nbsp;&nbsp;<span   <?php if (($system_ok == 0) || ($system_ok >= 80)) {?> style="display:none" <?php }?>
                                                                                                               id='span_condition'>Reason For Poor Condition?</span>
                                    <input  <?php if (($system_ok == 0) || ($system_ok >= 80)) {?> style="display:none" <?php }?>  name="condition_remarks" id="condition_remarks" type="text" 	maxlength="100"  class="txt_box" size="50" 	value="<?php echo $condition_remarks;?>">


                                    <b> Net Time:
                                        <strong><font style="color:#FF0000" size="-1"><?php if ($net_time_sec > 0) echo $net_time;?>  Hrs
                                            </font></strong></b>
                                    <b> Month Total Hrs:
                                        <strong><font style="color:#FF0000" size="+0"><?php echo get_total_time($user_id,$sql_date);?>  Hrs
                                            </font></strong></b>
                                    <b> Month Total Days:
                                        <strong><font style="color:#FF0000" size="+0"><?php echo get_total_days($user_id,$sql_date);?>
                                            </font></strong></b>


                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align:middle;padding-left: 8px;" valign="middle" colspan="5" align="center" bgcolor="#e9e9e9"
                                    ><?php if ($date_failure == "Yes") {
                                                    ?>
                                        <span style="color:#F00; font-size:18px"><strong>Please Complete the <?php echo date('d-m-Y',strtotime($next_complete))?> Job Status</strong></span>
                                        <?php
                                    } else {
                                        ?>
                                        <strong>Location:</strong>
                                        <input name="location"
                                               value="<?php echo $location?>"
                                               type="text" size="30">
                                        <strong>Remarks:</strong>
                                        <input id="remarks" name="remarks"
                                               value="<?php echo $remarks?>"
                                               type="text" size="50">
                                        <input type="hidden" name="current_job_status" value="<?php echo $work_status;?>">
                                        <?php if (($work_status == "work") || ($work_status == "")) {?>
                                            <input type="submit" name="save_date_time"
                                                   value="Save Date and Time" onClick="return check_form(this.form, 'save_data', '<?php echo $work_status?>');">
                                               <?php }?>
                                               <?php
                                               if ($work_status == "holiday") {
                                                   ?>
                                            <input type="submit" name="cancel_holiday"
                                                   value="Cancel Holiday">
                                               <?php } elseif ($work_status == "leave") {
                                                   ?>
                                            <input type="submit" name="cancel_leave"
                                                   value="Cancel Leave">
                                                   <?php
                                               } else {
                                                   ?>
                                            <input type="submit" name="save_holiday" value="Holiday">
                                            <input type="submit" name="save_leave" value="Leave">
                                        <?php }?></td>
                                    <td bgcolor="#e9e9e9">&nbsp;

                                        <?php
                                        $current_time = time() - strtotime(date("d-m-Y"));

                                        $today_date = date("d-m-Y");


                                        echo "<br>";




                                        if (strtotime($date) <= strtotime($today_date)) {







                                            if (($date != $today_date) || (($date = $today_date) && ($current_time > 46800))) {
                                                ?>



                                                <span id="complete_message"  style="color:#FF0000; display:none; font-weight:bold; font-size:14px">Please Wait......</span>
                                                <input id="complete_button" type="image" src="images/complete_button.png" name="save_complete"
                                                       value="Complete Button" onClick="return check_form(this.form, 'save_complete', '<?php echo $work_status?>');">
                                                       <?php
                                                   }
                                               }
                                               ?>
                                    <?php }?></td>
                            </tr>
                        </table>


                        <?php if (count($target) > 0) {?>
                            <br>
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="99%"


                                   class="form_style">

                                <tr align="center" valign="middle" height="25" bgcolor="#7EB543">
                                    <td  colspan="12" align="center"><font color="#FFFFFF" size="2"><b>Delegated Jobs -- Target Exceeds</b></font></td>

                                </tr>


                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="15%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                                    <td width="10%"><b><font color="#a6107b" size="2">Type</font></b></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b> </font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b> </font></td>
                                    <td width="10%"><b><font color="#a6107b" size="2">Client</font></b></td>
                                    <td width="10%"><strong><font color="#a6107b" size="2">Shared
                                            to </font> </strong></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time </b> </font></td>
                                    <td width="7%"><font color="#a6107b" size="2"><b>Act Time </b> </font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Target</b> </font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b> </font></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Status</b> </font></td>
                                    <td width="18%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
                                </tr>
                                <?php
                                foreach ($target as $target_row) {
                                    ?>
                                    <tr align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;" height="25">
                                        <td align="left"><?php echo $target_row['taskname']?></td>
                                        <td><?php echo $delegate_array[$target_row['delegate_type']]?></td>
                                        <td><?php echo "<strong style='color:#FF0000'>".$main_type_array[$target_row['main_type']]."</strong><br>".$job_type_array[$target_row['job_type']]?></td>
                                        <td><?php echo $target_row['job_no']?></td>
                                        <td><?php echo $target_row['client']?></td>
                                        <td><?php echo $target_row['username']?></td>
                                        <td><?php echo $target_row['est_time']?></td>
                                        <td><?php echo $target_row['act_time']?></td>
                                        <td><?php echo $target_row['target_date']?></td>
                                        <td><?php echo $target_row['description']?></td>
                                        <td><?php echo $target_row['status']?>%</td>
                                        <td><?php echo $target_row['date_report']?></td>
                                    </tr>
                                <?php }?>
                            </table>


                        <?php }?>
                        <?php if (count($today) > 0) {?>
                            <br>
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="98%"
                                   class="form_style">
                                <td  colspan="12" align="center" bgcolor="#7EB543"><font color="#FFFFFF" size="2"><b>Today's Delegation</b></font></td>

                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                                    <td width="10%"><b><font color="#a6107b" size="2">Type</font></b></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                                    <td width="10%"><b><font color="#a6107b" size="2">Client</font></b></td>
                                    <td width="10%"><strong><font color="#a6107b" size="2">Shared
                                            to </font></strong></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time </b></font></td>
                                    <td width="7%"><font color="#a6107b" size="2"><b>Act Time </b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Target Date</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                                    <td width="18%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
                                    <td width="18%"><font color="#a6107b" size="2"><b>Status</b></font></td>
                                </tr>
                                <?php
                                foreach ($today as $today_row) {
                                    ?>
                                    <tr height="25" align="center" valign="middle" <?php
                                    if ($today_row['is_read'] == 1) {
                                        echo "bgcolor='#e9e9e9'";
                                    } else {
                                        echo "bgcolor='#FF9900'";
                                    }
                                    ?>
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                        <td>&nbsp;<?php echo $today_row['taskname']?></td>
                                        <td><?php echo $delegate_array[$today_row['delegate_type']]?></td>
                                        <td><?php echo "<strong style='color:#FF0000'>".$main_type_array[$today_row['main_type']]."</strong><br>".$job_type_array[$today_row['job_type']]?></td>
                                        <td><?php echo $today_row['job_no']?></td>
                                        <td><?php echo $today_row['client']?></td>
                                        <td><?php echo $today_row['username']?></td>
                                        <td><?php echo $today_row['est_time']?></td>
                                        <td><?php echo $today_row['act_time']?></td>
                                        <td><?php echo $today_row['target_date']?></td>
                                        <td><?php echo $today_row['description']?></td>
                                        <td><?php echo $today_row['cf_date']?></td>
                                        <td><strong><?php echo $today_row['status']."%"?></strong></td>
                                    </tr>
                                <?php }?>
                            </table>


                        <?php }?>
                        <?php if (count($other) > 0) {?>
                            <br>
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="99%"
                                   class="form_style">
                                <tr>
                                    <td colspan="12" align="center" bgcolor="#7EB543"><font
                                            color="#FFFFFF" size="2"><b>11Other's Delegated To Me</b> </font></td></tr>
                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="20%" align="center"><font color="#a6107b" size="2"><b>Work</b> </font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Type</b></font><br></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Job Type</b> </font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b> </font></td>
                                    <td width="8%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                                    <td width="8%"><strong><font color="#a6107b" size="2">By</font></strong></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br></b> </font></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Act Time<br>
                                        </b> </font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Target</b> </font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b> </font></td>
                                    <td width="7%"><b><font color="#a6107b" size="2">CF Date</font></b></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Status</b> </font></td>
                                </tr>
                                <?php
                                foreach ($other as $other_row) {
                                    ?>
                                    <tr align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                        <td align="left"><?php echo $other_row['taskname']?></td>
                                        <td><?php echo $delegate_array[$other_row['delegate_type']]?></td>
                                        <?php
                                        if ($other_row['is_reopen'] == 1) {
                                            ?>
                                            <td colspan="3" style="color:#FF0000"><strong><?php echo $other_row['reopen_remarks']?></strong></td>

                                            <?php
                                        } else {
                                            ?>


                                            <td>
                                                <?php echo "<strong style='color:#FF0000'>".$main_type_array[$other_row['main_type']]."</strong><br>".$job_type_array[$other_row['job_type']]?>




                                            </td>
                                            <td><?php echo $other_row['job_no']?></td>
                                            <td><?php echo $other_row['client']?></td>
                                        <?php }
                                        ?>

                                        <td><?php echo $other_row['username']?></td>
                                        <td>Total Est:<?php
                                            if ($other_row['job_est'] == "00:00" || $other_row['job_est'] == "") {
                                                echo "Nil";
                                            } else {
                                                echo $other_row['job_est'];
                                            }
                                            ?><br><input onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Est time Updation')"  type="text" name="est_time_hr<?php echo $other_row['workreport_id']?>" maxlength="2" id="est_time_hr<?php echo $other_row['workreport_id']?>"
                                                       class="txt_box" size="2" value="<?php echo $other_row['est_time_hr'];?>"
                                                       style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Est time Updation')"  type="text" name="est_time_min<?php echo $other_row['workreport_id']?>" maxlength="2" id="est_time_min<?php echo $other_row['workreport_id']?>"
                                                   class="txt_box" size="2" value="<?php echo $other_row['est_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td>Prev Act:<?php
                                            if ($other_row['prev_act'] == "00:00" || $other_row['prev_act'] == "") {
                                                echo "Nil";
                                            } else {
                                                echo $other_row['prev_act'];
                                            }
                                            ?><br><input onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_hr<?php echo $other_row['workreport_id']?>" maxlength="2" id="act_time_hr<?php echo $other_row['workreport_id']?>"
                                                       class="txt_box" size="2" value="<?php echo $other_row['act_time_hr'];?>"
                                                       style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_min<?php echo $other_row['workreport_id']?>" maxlength="2" id="act_time_min<?php echo $other_row['workreport_id']?>"
                                                   class="txt_box" size="2" value="<?php echo $other_row['act_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td style="color:#FF0000; font-size:13px">
                                            <?php if (($other_row['is_carry'] == 0) && ($other_row['delegate_type'] == 4)) {?>
                                                <input onBlur="auto_save('<?php echo $other_row['workreport_id']?>', '')" type="text" name="target_date<?php echo $other_row['workreport_id']?>"
                                                       style="vertical-align: middle" size="10" id="target_date<?php echo $other_row['workreport_id']?>"
                                                       value="<?php echo $other_row['target_date']?>"
                                                       onClick="return showCal('target_date<?php echo $other_row['workreport_id']?>', 'dd-mm-y');">
                                                       <?php
                                                   } else {
                                                       echo $other_row['target_date'];
                                                   }
                                                   ?>


                                            </strong></td>
                                        <td><textarea onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Job Updation')" name="description<?php echo $other_row['workreport_id']?>" style="overflow: auto;" 										class="txt_box" id="description<?php echo $other_row['workreport_id']?>" cols="15" rows="3"><?php echo $other_row['description'];?></textarea></td>
                                        <td><input onBlur="auto_save('<?php echo $other_row['workreport_id']?>', '')" type="text" name="cf_date<?php echo $other_row['workreport_id']?>"
                                                   style="vertical-align: middle" size="10" id="cf_date<?php echo $other_row['workreport_id']?>"
                                                   value="<?php echo $other_row['cf_date']?>"
                                                   onClick="return showCal('cf_date<?php echo $other_row['workreport_id']?>', 'dd-mm-y');">
                                        </td>
                                        <td id="td_status_<?php echo $other_row['workreport_id']?>"  <?php if ($other_row['status'] != 100) {?> style="background:#FF0000" <?php }?>><select onChange="auto_save('<?php echo $other_row['workreport_id']?>', 'Status Updation')"  id="status<?php echo $other_row['workreport_id']?>"
                                                                                                                                                                                             name="status<?php echo $other_row['workreport_id']?>" style="font-weight: 900">

                                                <?php for ($i = 100; $i >= 0; $i = $i - 1) {?>
                                                    <option	<?php if ($other_row['status'] == $i) {?>selected="selected"<?php }?> value="<?php echo $i?>"> <?php echo $i?> % </option>
                                                <?php }?>
                                            </select>
                                            <input type='hidden' value="<?php echo $other_row['is_carry']?>" name="is_carry<?php echo $other_row['workreport_id']?>" id="is_carry<?php echo $other_row['workreport_id']?>">

                                        </td>
                                    </tr>
                                <?php }?>
                            </table>


                        <?php }?>
                        <?php if (count($carry) > 0) {?>
                            <br>
                            <table id="customers" border="0"
                                   style=" border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="98%"
                                   class="form_style">
                                <tr>
                                    <td colspan="12" align="center" bgcolor="#7EB543"><font
                                            color="#FFFFFF" size="2"><b>Carry Forwarded Jobs </b><font
                                            color="#FF0000" size="2"><b>(Instead of Carry Forward We can move forward Job to Store</b>) </font> </font></td></tr>
                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b> Type</b></font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                                    <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Est Time<br>
                                        </b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Act Time<br>
                                        </b></font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
                                    <td width="7%"><b><font color="#a6107b" size="2">Status</font></b></td>
                                    <td width="7%"><b><font color="#a6107b" size="2">To Store</font></b></td>
                                </tr>
                                <?php
                                foreach ($carry as $carry_row) {
                                    ?>
                                    <tr align="center" valign="middle" bgcolor="#e9e9e9"
                                        style=" border-style: solid; border-width: 1px;">
                                        <td align="left"><?php echo $carry_row['taskname']?></td>
                                        <td><?php echo $main_type_array[$carry_row['main_type']]?><br><?php echo $job_type_array[$carry_row['job_type']]?></td>
                                        <td><?php echo $carry_row['job_no']?></td>
                                        <td><?php echo $carry_row['client']?></td>
                                        <td><?php
                                            if ($carry_row['job_est'] != "00:00" && $carry_row['job_est'] != "") {
                                                echo "Total Est:".$carry_row['job_est'];
                                            }
                                            ?><br><input onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Est time  Updation')"  type="text" name="est_time_hr<?php echo $carry_row['workreport_id']?>" maxlength="2" id="est_time_hr<?php echo $carry_row['workreport_id']?>"
                                                       class="txt_box" size="1" value="<?php echo $carry_row['est_time_hr'];?>"
                                                       style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Est time  Updation')"  type="text" name="est_time_min<?php echo $carry_row['workreport_id']?>" maxlength="2" id="est_time_min<?php echo $carry_row['workreport_id']?>"
                                                   class="txt_box" size="1" value="<?php echo $carry_row['est_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td width="10%"><?php
                                            if ($carry_row['prev_act'] != "00:00" && $carry_row['prev_act'] != "") {
                                                echo "Prev Act:".$carry_row['prev_act'];
                                            }
                                            ?><br><input onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_hr<?php echo $carry_row['workreport_id']?>" maxlength="2" id="act_time_hr<?php echo $carry_row['workreport_id']?>"
                                                       class="txt_box" size="1" value="<?php echo $carry_row['act_time_hr'];?>"
                                                       style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_min<?php echo $carry_row['workreport_id']?>" maxlength="2" id="act_time_min<?php echo $carry_row['workreport_id']?>"
                                                   class="txt_box" size="1" value="<?php echo $carry_row['act_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td><?php echo $carry_row['target_date']?></td>
                                        <td><textarea onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Job Updation')" name="description<?php echo $carry_row['workreport_id']?>" style="overflow: auto;"
                                                      class="txt_box" id="description<?php echo $carry_row['workreport_id']?>" cols="30" rows="1"><?php echo $carry_row['description'];?></textarea><a target='_blank'   href='view_cf_status.php?workreport_id=<?php echo $carry_row['workreport_id']?>'  onClick="javascript:openWindow(this.href, this.target);
                                                              return false;"><img src='images/view.gif' width='12' height='12' border='0' title='Preview this Invoice' />
                                            </a></td>
                                        <td><input onBlur="auto_save('<?php echo $carry_row['workreport_id']?>', '')" type="text" name="cf_date<?php echo $carry_row['workreport_id']?>"
                                                   style="vertical-align: middle" size="10" id="cf_date<?php echo $carry_row['workreport_id']?>"
                                                   value="<?php echo $carry_row['cf_date']?>"
                                                   onClick="return showCal('cf_date<?php echo $carry_row['workreport_id']?>', 'dd-mm-y');"></td>
                                        <td id="td_status_<?php echo $carry_row['workreport_id']?>" <?php if ($carry_row['status'] != 100) {?> style="background:#FF0000" <?php }?>><select  onChange="auto_save('<?php echo $carry_row['workreport_id']?>', 'Status Updation')"  id="status<?php echo $carry_row['workreport_id']?>"
                                                                                                                                                                                             name="status<?php echo $carry_row['workreport_id']?>" style="font-weight: 900">
                                                                                                                                                                                                 <?php echo $option;?>
                                            </select></td>
                                        <td><input type='checkbox' onClick="auto_save('<?php echo $carry_row['workreport_id']?>', 'Status Updation')" value="2" <?php if ($carry_row['is_carry'] == 2) {?>  checked<?php }?> name="is_carry<?php echo $carry_row['workreport_id']?>" id="is_carry<?php echo $carry_row['workreport_id']?>"></td>
                                    </tr>
                                    <script language="javascript">
                                        $('#status<?php echo $carry_row['workreport_id']?>').val(<?php echo $carry_row['status']?>);
                                    </script>
                                <?php }?>
                            </table>
                            <br>
                            <strong></strong>

                        <?php }?>
                        <?php if ($share_result->num_rows > 0) {?>
                            <h3><a href="#"><font size="2"><b>Shared Jobs</b></font></a></h3>
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="95%"
                                   class="form_style">
                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="12%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Job Type</b></font></td>
                                    <td width="5%"><font color="#a6107b" size="2"><b>Job No</b></font></td>
                                    <td width="10%"><strong><font color="#a6107b" size="2">Client</font></strong></td>
                                    <td ><font color="#a6107b" size="2"><b>Est Time<br>
                                        </b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Act Time<br>
                                        </b></font></td>
                                    <td width="8%"><font color="#a6107b" size="2"><b>Target</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="2"><b>CF Date</b></font></td>
                                    <td width="10%"><b><font color="#a6107b" size="2">Status</font></b></td>
                                </tr>
                                <?php while ($share_row = $share_result->fetch_assoc()) {
                                    ?>
                                    <tr align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                        <td><?php echo $share_row['taskname']?></td>
                                        <td><?php echo $share_row['job_type_name']?></td>
                                        <td><?php echo $share_row['job_no']?></td>
                                        <td><?php echo $share_row['client']?></td>
                                        <td><input onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Est time  Updation')"  type="text" name="est_time_hr<?php echo $share_row['workreport_id']?>" maxlength="2" id="est_time_hr<?php echo $share_row['workreport_id']?>"
                                                   class="txt_box" size="2" value="<?php echo $share_row['est_time_hr'];?>"
                                                   style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Est time  Updation')"  type="text" name="est_time_min<?php echo $share_row['workreport_id']?>" maxlength="2" id="est_time_min<?php echo $share_row['workreport_id']?>"
                                                   class="txt_box" size="2" value="<?php echo $share_row['est_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td width="10%"><input onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_hr<?php echo $share_row['workreport_id']?>" maxlength="2" id="act_time_hr<?php echo $share_row['workreport_id']?>"
                                                               class="txt_box" size="2" value="<?php echo $share_row['act_time_hr'];?>"
                                                               style="vertical-align: middle">
                                            <input onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Act time Updation')"  type="text" name="act_time_min<?php echo $share_row['workreport_id']?>" maxlength="2" id="act_time_min<?php echo $share_row['workreport_id']?>"
                                                   class="txt_box" size="2" value="<?php echo $share_row['act_time_min'];?>"
                                                   style="vertical-align: middle"></td>
                                        <td><?php echo $share_row['target_date']?></td>
                                        <td><textarea onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Job Updation)" name="description<?php echo $share_row['workreport_id']?>" style="overflow: auto;"
                                                      class="txt_box" id="description<?php echo $share_row['workreport_id']?>" cols="15" rows="2"><?php echo $share_row['description'];?></textarea></td>
                                        <td><input onBlur="auto_save('<?php echo $share_row['workreport_id']?>', '')" type="text" name="cf_date<?php echo $share_row['workreport_id']?>"
                                                   style="vertical-align: middle" size="10" id="cf_date<?php echo $share_row['workreport_id']?>"
                                                   value="<?php echo $share_row['cf_date']?>"
                                                   onClick="return showCal('cf_date<?php echo $share_row['workreport_id']?>', 'dd-mm-y');"></td>
                                        <td id="td_status_<?php echo $share_row['workreport_id']?>" <?php if ($share_row['status'] != 100) {?> style="background:#FF0000" <?php }?>><select  onChange="auto_save('<?php echo $share_row['workreport_id']?>', 'Status Updation')"  id="status<?php echo $share_row['workreport_id']?>"
                                                                                                                                                                                             name="status<?php echo $share_row['workreport_id']?>" style="font-weight: 900">
                                                                                                                                                                                                 <?php for ($i = 100; $i >= 0; $i = $i - 1) {?>
                                                    <option
                                                    <?php if ($share_row['status'] == $i) {?>
                                                            selected="selected"
                                                        <?php }?>
                                                        value="<?php echo $i?>"> <?php echo $i?> % </option>
                                                    <?php }?>
                                            </select></td>
                                    </tr>
                                <?php }?>
                            </table>
                            <br>

                        <?php }
                        ?>



                        <?php if (count($routine) > 0) {?>
                            <br>
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="95%"
                                   class="form_style">
                                <tr>
                                    <td colspan="6" align="center" bgcolor="#7EB543"><font
                                            color="#FFFFFF" size="2"><b>Daily Routine Jobs</b> </font></td>
                                </tr>
                                <tr align="center" valign="middle" height="10" bgcolor="#e2f3fd">
                                    <td width="3%" align="center"><b><font color="#a6107b" size="1">No</font></b></td>
                                    <td width="30%"><font color="#a6107b" size="1"><b>Task</b></font></td>
                                    <td width="10%"><font color="#a6107b" size="1"><b>Type</b></font></td>
                                    <td><b><font color="#a6107b" size="1">Est Time</font></b></td>
                                    <td ><font color="#a6107b" size="1"><b>Act Time</b> </font></td>
                                    <td width="18%"><font color="#a6107b" size="1"><b>Remarks</b> </font></td>
                                </tr>
                                <?php
                                $i = 1;
                                foreach ($routine as $routine_row) {
                                    if ($routine_row['auth_status'] == 1) {
                                        $font = "Blue";
                                        $label = "(Approved)";
                                    } else if ($routine_row['auth_status'] == -1) {

                                        $font = "Red";
                                        $label = "(Rejected)";
                                    } else {
                                        $font = "";
                                        $label = "";
                                    }
                                    ?>
                                    <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                        <td><?php echo $i++;?></td>
                                        <td align="left"><font color='<?php echo $font;?>'><?php echo $routine_row['job_name']?> <b><?php echo $label?></b></font></td>
                                        <td><font color='<?php echo $font;?>'><?php echo $main_type_array[$routine_row['main_type']]?><br><?php echo $job_type_array[$routine_row['sub_type']]?></font></td>
                                        <td>
                                            <input onChange="routine_save(<?php echo $routine_row['id']?>)" type="text" class="txt_box" maxlength="2" size="3" name="daily_est_hr[<?php echo $routine_row['id']?>]" id="daily_est_hr<?php echo $routine_row['id']?>" value=<?php echo $routine_row['est_time_hr'];?> >
                                            <input onChange="routine_save(<?php echo $routine_row['id']?>)" type="text" class="txt_box" size="3" name="daily_est_min[<?php echo $routine_row['id']?>]" id="daily_est_min<?php echo $routine_row['id']?>"  value=<?php echo $routine_row['est_time_min'];?>  ></td>
                                        <td><input onChange="routine_save(<?php echo $routine_row['id']?>)" type="text" class="txt_box" maxlength="2" size="3" name="daily_act_hr[<?php echo $routine_row['id']?>]" id="daily_act_hr<?php echo $routine_row['id']?>" value=<?php echo $routine_row['act_time_hr'];?> >
                                            :<input onChange="routine_save(<?php echo $routine_row['id']?>)" type="text" class="txt_box" size="3" name="daily_act_min[<?php echo $routine_row['id']?>]" id="daily_act_min<?php echo $routine_row['id']?>"  value=<?php echo $routine_row['act_time_min'];?>  ></td>
                                        <td><textarea onChange="routine_save(<?php echo $routine_row['id']?>)"    name="routine_remarks<?php echo $routine_row['id'];?>" style="overflow: auto;height:20px"
                                                      class="txt_box" id="routine_remarks<?php echo $routine_row['id']?>" cols="50" rows="1"><?php echo $routine_row['remarks'];?></textarea></td>
                                    </tr>
                                <?php }?>
                            </table>
                            <br>
                        <?php }?>

                        <table id="customers" border="0"
                               style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                               align="center" cellpadding="0" cellspacing="0" width="25%"
                               class="form_style">
                            <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                <td align="center"><strong style="font-size:18px">Click Here to Log Daily Marketing</strong></td>

                            </tr>

                            <tr height="30" align="center" valign="middle" bgcolor=""
                                style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                <td align="centre"><strong style="font-size:18px"><a target='_blank'   href='marketing_entry.php?marketing_date=<?php echo $sql_date?>'   onClick="javascript:openWindow(this.href, this.target);
                                        return false;"><img src="images/marketing.png"></a></strong></td>

                            </tr>
                        </table>
                        <br>
                        <br>
                        <?php
                        if ($_SESSION['emp_division_id'] == 225) {
                            ?>

                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="25%"
                                   class="form_style">
                                <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                    <td align="center"><strong style="font-size:18px">UT Reporting</strong></td>

                                </tr>

                                <tr height="30" align="center" valign="middle" bgcolor=""
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                    <td align="centre"><strong style="font-size:18px"><a target='_blank'   href='ut_reporting.php?reporting_date=<?php echo $sql_date?>'   onClick="javascript:openWindow(this.href, this.target);
                                            return false;"> <button style="height:50px" name="unlock" value="unlock"><strong>Click here to Log Spot</strong></button></a></strong></td>

                                </tr>
                            </table>

                        <?php }
                        ?>

                        <?php
                        if ((date('d',strtotime($sql_date)) == 7) || (date('d',strtotime($sql_date)) == 14) || (date('d',strtotime($sql_date)) == 21)) {
                            ?>

                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="95%"
                                   class="form_style">
                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td colspan="6" align="center" bgcolor="#7EB543"><font
                                            color="#FFFFFF" size="2"><b>Last Week Plan</b> </font></td>


                                </tr>
                                <tr align="center" valign="middle" height="25" bgcolor="#e2f3fd">
                                    <td width="3%" align="center"><font color="#a6107b" size="2"><b>No</b></font></td>
                                    <td width="45%" align="center"><font color="#a6107b" size="2"><b>Work</b></font></td>               <td width="10%"><font color="#a6107b" size="2"><b>Remarks</b></font></td>
                                    <td width="5%"><b><font color="#a6107b" size="2">Status</font></b></td>
                                    <td width="5%"><b><font color="#a6107b" size="2">CF Month</font></b></td>
                                    <td width="5%"><b><font color="#a6107b" size="2">CF Week</font></b></td>
                                </tr>
                                <?php
                                $k = 1;
                                $flag = 0;
                                while ($monthly_plan_row = $monthly_plan_result->fetch_assoc()) {
                                    ?>

                                    <tr align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                        <td align="center"><?php echo $k++?></td>
                                        <td align="left"> <?php echo $monthly_plan_row['taskname']?></td>
                                        <td><textarea onChange="monthly_auto_save('<?php echo $monthly_plan_row['plan_id']?>', 'Job Updation')" name="description<?php echo $monthly_plan_row['plan_id']?>" style="overflow: auto; height:25px"
                                                      class="txt_box" id="description<?php echo $monthly_plan_row['plan_id']?>" cols="45" rows="1"><?php echo $monthly_plan_row['description'];?></textarea></td>
                                        <td><select  onChange="monthly_auto_save('<?php echo $monthly_plan_row['plan_id']?>', 'Status Updation')"  id="status<?php echo $monthly_plan_row['plan_id']?>"
                                                     name="status<?php echo $monthly_plan_row['plan_id']?>" style="font-weight: 900">
                                                <option value="0">0%</option>
                                                <?php for ($i = 5; $i <= 100; $i = $i + 5) {?>
                                                    <option
                                                    <?php if ($monthly_plan_row['status'] == $i) {?>
                                                            selected="selected"
                                                        <?php }?>
                                                        value="<?php echo $i?>"> <?php echo $i?> % </option>
                                                    <?php }?>
                                            </select></td>
                                        <td><select  onChange="monthly_auto_save('<?php echo $monthly_plan_row['plan_id']?>', 'Carry  Forward Updation')"  id="carry_forward<?php echo $monthly_plan_row['plan_id']?>"
                                                     name="carry_forward<?php echo $monthly_plan_row['plan_id']?>" style="font-weight: 900">
                                                <option value="0"></option>

                                                <?php
                                                for ($z = 1; $z <= 12; $z++) {
                                                    ?>
                                                    <option <?php if ($monthly_plan_row['carry_forward'] == $z) {?>  selected="selected" <?php }?> 										value="<?php echo $z?>"><?php echo date('F',strtotime("2015-$z-01"))?> </option>

                                                <?php }
                                                ?>


                                            </select></td>
                                        <td><select  onChange="monthly_auto_save('<?php echo $monthly_plan_row['plan_id']?>', 'Carry  Forward Week Updation')"  id="cf_week<?php echo $monthly_plan_row['plan_id']?>"
                                                     name="cf_week<?php echo $monthly_plan_row['plan_id']?>" style="font-weight: 900">
                                                <option value="0"></option>
                                                <option <?php if ($monthly_plan_row['cf_week'] == 1) {?> selected="selected"	<?php }?> value="1">1st Week</option>
                                                <option <?php if ($monthly_plan_row['cf_week'] == 2) {?> selected="selected"	<?php }?> value="2">2  Week</option>
                                                <option <?php if ($monthly_plan_row['cf_week'] == 3) {?> selected="selected"	<?php }?> value="3">3rd Week</option>
                                                <option <?php if ($monthly_plan_row['cf_week'] == 4) {?> selected="selected"	<?php }?> value="4">4th Week</option>
                                                <option <?php if ($monthly_plan_row['cf_week'] == 5) {?> selected="selected"	<?php }?> value="5">5th Week</option>

                                            </select></td>
                                    </tr>
                                <?php }?>
                            </table>


                            <?php
                        } else {
                            ?>


                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="40%"
                                   class="form_style">
                                <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                    <td align="right"><strong style="font-size:18px"><a target='_blank'   href='monthly_plan_view.php?no_menu=yes'  onClick="javascript:openWindow(this.href, this.target);
                                            return false;">Monthly Plan</a></strong></td>
                                    <td  align="left"><strong><a target='_blank'   href='view_weekly_plan.php?date=<?php echo $sql_date?>'  onClick="javascript:openWindow(this.href, this.target);
                                            return false;">Weekly Plan
                                            </a></strong></td>
                                </tr>
                            </table>
                        <?php }?>



                        <?php
                        if ($_SESSION['emp_division_id'] == 2) {


                            $equipment_access_result = $mysqli->query("select count(id) as count_id from  tbl_equipments  where 	 equipment_in_charge=$user_id");
                            $equipment_access_row = $equipment_access_result->fetch_assoc();
                            $count_id = $equipment_access_row['count_id'];

                            if (($count_id > 0)) {
                                ?>
                                <br>
                                <table id="customers" border="0"
                                       style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                       align="center" cellpadding="0" cellspacing="0" width="40%"
                                       class="form_style">
                                    <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                        style="border-color: #A6107B; border-style: solid; border-width: 1px;">


                                        <td align="center"><strong style="font-size:18px"><a target='_blank'   href='equipment_utilisation.php?utilisation_date=<?php echo $sql_date?>'  onClick="javascript:openWindow(this.href, this.target);
                                                return false;">Equipment Utilisation Entry</a></strong></td>

                                    </tr>
                                </table>
                                <?php
                            }
                        }
                        ?>

                        <br>
                        <div id="example1"><br>
                        </div>
                        <div style="width:100%;text-align:center;" >
                            <table id="customers" border="0"
                                   style="border-color: #A6107B; border-style: solid; border-width: 1px;"
                                   align="center" cellpadding="0" cellspacing="0" width="30%"
                                   class="form_style">
                                <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                    <td align="right"><strong>Est1(Excluding Routine Jobs)</strong></td>
                                    <td  align="right"><span style="font-size:14px" id="est_div"><strong><?php echo gmdate('H:i',$est);?></strong></span></td>
                                    <td align="right"><strong>Act(Excluding Routine Jobs)</strong></td>
                                    <td width="18%" align="right"><strong><span style=" font-size:14px" id="act_div"><?php echo gmdate('H:i',$act);?></span></strong></td>
                                </tr>
                                <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                    <td align="right"><strong>Routine Jobs Est:</strong></td>
                                    <td align="right"><strong><span style="font-size:14px" id="routine_est_div"><?php echo gmdate('H:i',$routine_est)?></span></strong></td>
                                    <td align="right"><strong>Routine Jobs Act:</strong></td>
                                    <td align="right"><strong><span style="font-size:14px" id="routine_act_div"><?php echo gmdate('H:i',$routine_act)?></span></strong></td>
                                </tr>
                                <tr height="30" align="center" valign="middle" bgcolor="#e9e9e9"
                                    style="border-color: #A6107B; border-style: solid; border-width: 1px;">
                                    <td align="right"><strong>Total Jobs Est:</strong></td>
                                    <td align="left"><strong><span style="color:#FF0000;font-size:14px" id="total_est_div"><?php echo gmdate('H:i',$est + $routine_est)?></span></strong></td>
                                    <td align="right"><strong>Total Jobs Act:</strong></td>
                                    <td align="right"><strong><span style="color:#FF0000;font-size:14px" id="total_act_div"><?php echo gmdate('H:i',$act + $routine_act)?></span></strong></td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </div>
                        <script>
                            var $container = $("#example1");
                            var $console = $("#exampleConsole");
                            Handsontable.cellLookup.renderer.negativeValueRenderer = negativeValueRenderer; //maps function to lookup string
                            Handsontable.cellLookup.renderer.rateValueRenderer = rateValueRenderer; //maps function to lookup string


                            var res = <?php echo $test;?>;

                            //   res=[{"id":"1","manufacturer":"abcl","year":"2001","price":"100"},{"id":"2","manufacturer":"efgh","year":"2002","price":"101"}];
                            var data = [], row;
                            for (var i = 0, ilen = res.length; i < ilen; i++) {
                                row = [];

                                row[0] = res[i].taskname;
                                row[1] = res[i].main_type_name;
                                row[2] = res[i].job_type_name;
                                row[3] = res[i].un_sch;
                                row[4] = res[i].job_no;
                                row[5] = res[i].client;
                                row[6] = res[i].est_time;
                                row[8] = res[i].act_time;
                                row[9] = res[i].target_date;
                                row[10] = res[i].description;
                                row[11] = res[i].cf_date;
                                row[12] = res[i].job_est;

                                row[13] = res[i].status;


                                data[res[i].row_id - 1] = row;
                            }
                            var $parent = $container.parent();
                            $container.handsontable({
                                startRows: 8,
                                data: data,
                                columns: [
                                    {data: "0", type: 'text'},
                                    {data: "1",
                                        type: 'autocomplete',
                                        options: {items: 27},
                                        strict: true,
                                        allowInvalid: false,
                                        source: [<?php echo $main_type_source?>]
                                    },
                                    {data: "2",
                                        type: 'autocomplete',
                                        options: {items: 27},
                                        strict: true,
                                        allowInvalid: false,
                                        source: [<?php echo $source?>]
                                    },
                                    {data: "3", type: 'checkbox'},
                                    {data: "4",
                                        type: 'autocomplete',
                                        options: {items: 27},
                                        exactMatch: false,
                                        source: [<?php echo $job_no_source?>]
                                    },
                                    {data: "5", type: 'text'},
                                    {data: "6",
                                        type: 'autocomplete',
                                        options: {items: 15},
                                        source: [<?php echo $est_act_source?>]},
                                    {data: "7", type: 'text', readOnly: true},
                                    {data: "8",
                                        type: 'autocomplete',
                                        options: {items: 12},
                                        source: [<?php echo $est_act_source?>]},
                                    {data: "9", type: 'date', dateFormat: 'dd-mm-yy'},
                                    {data: "10", type: 'text'},
                                    {data: "11", type: 'date', dateFormat: 'dd-mm-yy'},
                                    {data: "12",
                                        type: 'autocomplete',
                                        source: [<?php echo $job_est?>]
                                    },
                                    {data: "13",
                                        type: 'autocomplete',
                                        source: ["100", "95", "90", "85", "80", "10", "70", "65", "60", "55", "50", "45", "40", "35", "30", "25", "20", "15", "10", "5", "0"]

                                    }





                                ],
                                startCols: 3,
                                stretchH: 'all',
                                rowHeaders: true,
                                colWidths: [350, 80, 80, 50, 75, 75, 50, 50, 50, 65, 320, 80, 80, 90],
                                rowHeight: 10,
                                colHeaders: ['Task', 'Main Type', 'SubType', 'UnPlan', 'Job No.', 'Client', 'Est', 'Rate', 'Act', 'Target', 'Description', 'CF Date', 'Total Est', 'Status%'],
                                minSpareCols: 0,
                                minSpareRows: 1,
                                afterChange: function (change, source) {
                                    if (source === 'loadData') {
                                        return; //don't save this change
                                    }

                                    $.ajax({
                                        url: "php/save.php?date=<?php echo $date?>",
                                        dataType: "json",
                                        type: "POST",
                                        data: {changes: change}, //contains changed cells' data
                                        success: function (data) {

                                            if (data.logout == 'Yes')
                                            {
                                                alert("Your Session has been Expired Please Login Again");
                                                window.location.href = 'index.php';
                                            }
                                            else if (data.traffic == 'Yes')
                                            {
                                                alert("Network Issue Please Wait");

                                            }
                                            else
                                            {
                                                document.getElementById("act_div").innerHTML = data.act_time;
                                                document.getElementById("est_div").innerHTML = data.est_time;
                                                document.getElementById("routine_est_div").innerHTML = data.routine_est;
                                                document.getElementById("routine_act_div").innerHTML = data.routine_act;
                                                document.getElementById("total_est_div").innerHTML = data.total_est;
                                                document.getElementById("total_act_div").innerHTML = data.total_act;





                                            }
                                        },
                                        error: function (xhr, status, errorThrown) {
                                            if (status == 'error') {
                                                alert('Not Saved, Check Your Internet Connection');
                                            }


                                        }
                                    });
                                },
                                cells: function (row, col, prop) {
                                    var cellProperties = {};


                                    if (col === 13) {

                                        cellProperties.renderer = "negativeValueRenderer"; //uses lookup map
                                    }
                                    if (col === 7) {



                                        cellProperties.renderer = "rateValueRenderer"; //uses lookup map



                                    }

                                    return cellProperties;
                                }

                            });



                            function negativeValueRenderer(instance, td, row, col, prop, value, cellProperties) {
                                Handsontable.TextCell.renderer.apply(this, arguments);

                                if (value != 100) {


                                    //if row contains negative number
                                    td.style.background = '#FF0000';

                                    //add class "negative"
                                }



                            }

                            function rateValueRenderer(instance, td, row, col, prop, value, cellProperties) {
                                Handsontable.TextCell.renderer.apply(this, arguments);
                                rate = 0;

                                var est = act = "";

                                if ($container.handsontable('getData')[row])
                                    est = $container.handsontable('getData')[row][6];
                                if ($container.handsontable('getData')[row])
                                    act = $container.handsontable('getData')[row][8];







                                if ((est != "") && (est != 0)) {



                                    if ((act != "") && (act != null))
                                    {
                                        act_array = (act).split(":");

                                        var hours = parseInt(act_array[0]) + (parseInt(act_array[1]) / 60);
                                        rate = hours * (<?php echo $_SESSION['hourly_rate'];?>);

                                    }
                                    else if ((est != "") && (est != null))
                                    {
                                        est_array = (est).split(":");

                                        var hours = parseInt(est_array[0]) + (parseInt(est_array[1]) / 60);
                                        rate = Math.round(hours * (<?php echo $_SESSION['hourly_rate'];?>));

                                    }
                                    value = "";
                                    est = "";
                                    td.innerHTML = rate;
                                    //add class "negative"
                                }



                            }





                        </script></td>
                </tr>
                <?php include("includes/footer.php");?>
            </table>
        <?php }
        ?>

        <div class="PopupDiv" id="modal" style="border: 3px solid black; background-color: rgb(153, 153, 255); padding: 25px; font-size: 150%; text-align: center; display: none; position: absolute; visibility: visible; top: 1941.5px; left: 548.5px; z-index: 143;">
            You are Late as per the Office Reporting Time(<?php echo $_SESSION['reporting_time']?>)!!!<br><br>
            <table>
                <tr>
                    <td style="font-size:16px;color:#FF0000;font-weight:bold">Are You Late?</td><td><select name="not_punctual" id="not_punctual"><option value=""></option><option value="Yes">Yes</option><option value="No">No</option></select></td>
                </tr>
                <tr>
                    <td id="reason_td"><strong>Remarks</strong></td><td><input type="text" id="late_remarks" name="late_remarks"></td>
                </tr>
                <tr>
                    <td></td><td> <input   Value="Submit" onClick="return  check_late(this.form)" type="submit"> <input value="Cancel"  onclick="Popup.hide('modal')" type="button"></td>
                </tr>



            </table>

        </div>




    </form>
    <link type="text/css" rel="stylesheet" href="css/jquery-ui-1.8.9.custom/jquery-ui-1.8.9.custom.css" />
    <script type="text/javascript" src="jquery-1.4.3.min.js"></script>
    <script type="text/javascript" src="jquery-ui-1.8.13.custom.min.js"></script>
    <style type="text/css">
        /*demo page css*/
        body{ font: 62.5% "Trebuchet MS", sans-serif; }
    </style>
    <script type="text/javascript">


                        function check_late(form)
                        {
                            if (document.getElementById("not_punctual").selectedIndex == 0)
                            {
                                alert("Please Select the Late drop down");
                                document.getElementById("not_punctual").focus();

                                return false;
                            }
                            if (document.getElementById("late_remarks").value == "")
                            {
                                alert("Please Enter some remarks");
                                document.getElementById("late_remarks").focus();

                                return false;
                            }

                            return true;



                        }

                        function display_condition_remarks(value)
                        {
                            if ((value >= 80) || (value == ""))
                            {
                                document.getElementById('condition_remarks').style.display = 'none';
                                document.getElementById('span_condition').style.display = 'none';
                            }
                            else
                            {
                                document.getElementById('condition_remarks').style.display = '';
                                document.getElementById('span_condition').style.display = '';
                            }


                        }
    </script>










    <br>
</body>
</html><?php

function get_location($user_id) {
    global $mysqli;

    $location_sql = "select custom_location from tbl_users where user_id=$user_id";
    $location_result = $mysqli->query($location_sql);
    $location_row = $location_result->fetch_assoc();

    return $location_row['custom_location'];
}

function get_total_time($user_id,$sql_date) {
    global $mysqli;

    $year = date('Y',strtotime($sql_date));
    $month = date('m',strtotime($sql_date));



    $time_sql = "select
sec_to_time(sum(time_to_sec(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home)))) as total_time  from tbl_time where time_to_sec(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home))>0 and user_id='$user_id' and year(date_log)='$year' and month(date_log)='$month'";

    $time_result = $mysqli->query($time_sql);
    $time_row = $time_result->fetch_assoc();

    return $time_row['total_time'];
}

function get_total_days($user_id,$sql_date) {
    global $mysqli;

    $year = date('Y',strtotime($sql_date));
    $month = date('m',strtotime($sql_date));



    $time_sql = "select
count(id) as count_id from  tbl_time where   time_to_sec(addtime(addtime(addtime(timediff(timediff(time_out,time_in),nwt),outside),night),home))>=21600 and user_id='$user_id' and year(date_log)='$year' and month(date_log)='$month'";

    $time_result = $mysqli->query($time_sql);
    $time_row = $time_result->fetch_assoc();

    return $time_row['count_id'];
}
?>