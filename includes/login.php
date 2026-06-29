<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 
if (isset($_POST ['txtUserName']) && isset($_POST ['txtPassword'])) {//echo "hi";exit;
    if($_POST ['txtUserName']==""&&$_POST ['txtPassword']==""){
     $message = "Please enter your username and password to continue!";echo "<script type='text/javascript'>alert('$message'); window.location.href='$site_url';</script>";
   exit;
     }
    
     if($_POST ['txtUserName']!=""&&$_POST ['txtPassword']==""){
     $message = "Please enter your password!";echo "<script type='text/javascript'>alert('$message'); window.location.href='$site_url';</script>";
    
     exit;
     }
    
     if($_POST ['txtUserName']==""&&$_POST ['txtPassword']!=""){
     $message = "Please enter your username!";echo "<script type='text/javascript'>alert('$message'); window.location.href='$site_url';</script>";
  exit;
     }
    else{
session_start(); 
session_regenerate_id(true); 
setcookie('user_id', '', 1);
    include_once("user_access.class.php");
    $user_name = trim($_POST ['txtUserName']);
    $password = trim($_POST ['txtPassword']);

/*******************************/
        
		
/*******************************/


$stmt = $mysqli->prepare(" SELECT login_block,notification_status, emp_type,is_regular, gender,is_mail, exit_only, division_head, w.calendar, SUBSTRING(employee_code, 7) AS ecode, employee_code AS ecode2, temp_username, username, if(display_name!='',display_name,full_name) as full_name, is_emp_code, work_location, holiday1, holiday2, group_id, role_id, personal_email, photo, employee_code, time_zone, is_lock, reporting_time, division_rate, hourly_rate, currency, IF(original_dob != '0000-00-00', original_dob, dob) AS dob, user_id, UPPER(username) AS username, credential, emp_division_id,emp_subdivision_id, designation_id, date_created, blood_group, email, is_health, latest_pic, gdoj, service_break_days FROM tbl_users u LEFT JOIN tbl_emp_workplace w ON w.id = u.work_location WHERE status = 'Active' AND is_regular in (1,2) AND username = ? "); 


$stmt->bind_param('s', $user_name); 
// Execute the statement
$stmt->execute(); 
// Get the result 
$result_user = $stmt->get_result(); 
// Fetch the associative array 
$row = $result_user->fetch_assoc(); 

// Close the statement 
$stmt->close();




if($row['login_block']>'0'){
  echo "<script>alert('Your Effism Account has been restricted by the system. Please contact software@ariesgroup.ae');</script>";
  echo "<script>window.location='index.php'</script>";
exit();
}



    if (($result_user->num_rows > 0) && (password_verify($password, $row['credential']))) {
       
       $ecode =$row['ecode2'];
       
       
        // if($ecode=="")$ecode=$empCode=$row['ecode2'];
       
        $empCode=$row['ecode2'];

        //$prefix = substr("$empCode",0,2);
        // if($prefix=="FC"||$prefix=="FL"||$prefix=="PC"||$prefix=="ST"||$prefix=="TR"||$prefix=="PT"){
        //  $ecode =substr("$empCode",0,7);    
        // }
        // else $ecode="AG".$ecode;
       // $ecode=$ecode;

        $user_id = $row ['user_id'];
        
    
      

        
        
        
    
    

$SESSION['no_menu']=0;
       
      
         
         
         
           
             
           
       
        
    

    
    
        
        //*********FOR PROFILE COMPLETION CHECK*****************
      /*  $sql_sel = "SELECT * FROM tbl_employee_contact_details WHERE user_id =$user_id";
        $result = $mysqli->query($sql_sel);
        $row_data = $result->fetch_assoc();
        $completestatus = $row_data['is_complete'];
         if($result->num_rows==0){
          $_SESSION['profile_completion']=0;
        }
        if($completestatus==1){
        $_SESSION['profile_completion']=1;
         }
         else if($completestatus==0||$completestatus==2){
        $_SESSION['profile_completion']=0;
         }
        */ 
       
        //************PROFILE COMPLETION CHECK ENDS****************
        
        
        ///***********FOR IT COMPLAINT ADMIN PRIVILAGE CHECK*********************
         $rslt = $mysqli->query("SELECT * FROM `complaint_category` where FIND_IN_SET($user_id, assign_users) or 
                        FIND_IN_SET($user_id,req_process_permisison) or 
                        FIND_IN_SET($user_id,assign_permission) or 
                        FIND_IN_SET($user_id,view_permission ) or 
                        FIND_IN_SET($user_id,status_change_permission )or 
                        FIND_IN_SET($user_id,approval_head  ) ");
                        $complAdminCnt=$rslt->num_rows; 
                    if($complAdminCnt>0)
                    $_SESSION['complAdminCnt']=1;
                    else $_SESSION['complAdminCnt']=0;
        //*********IT COMPLAINT ADMIN PRIVILAGE CHECK ENDS*********************
        


        
        
         $is_mail=$row ['is_mail'];
        
        $hourly_rate = $row ['hourly_rate'];
        $_SESSION['gender']=$row ['gender'];
        $div_id = $_SESSION ['emp_division_id'] = $row ['emp_division_id'];
        $_SESSION ['emp_subdivision_id'] = $row ['emp_subdivision_id'];
        $_SESSION ['user_id'] = $user_id;
        //setcookie('user_id', $user_id, time() + (86400 * 30), "/");
        
        $_SESSION ['employee_code'] = $row['employee_code'];
        $_SESSION['ecode'] = $ecode;
        $_SESSION ['time_zone'] = ($row['time_zone'] == "") ? 'Asia/Dubai' : $row['time_zone'];
        $_SESSION ['photo'] = $row['photo'];
        $_SESSION ['full_name'] = $row['full_name'];
        $_SESSION ['division_head'] = $row['division_head'];

        $_SESSION ['user_name'] = $user_name;
        $_SESSION ['hourly_rate'] = $hourly_rate;
        $_SESSION ['division_rate'] = $row ['division_rate'];
        $_SESSION ['work_location'] = $row ['work_location'];
        $_SESSION ['holiday1'] = $row ['holiday1'];
        $_SESSION ['holiday2'] = $row ['holiday2'];
        $_SESSION ['calendar'] = $row ['calendar'];
        $_SESSION ['exit_only'] = $row ['exit_only'];
        
        $_SESSION ['latest_pic'] = $row['latest_pic'];
        $work_location_id = $row['work_location'];
        $_SESSION ['is_not_gcc'] = 1;

        if (($work_location_id == 1) || ($work_location_id == 6) || ($work_location_id == 7) ||  ($work_location_id == 11) || ($work_location_id == 15) )
            $_SESSION ['is_not_gcc'] = 0;
            
            

           

        


        $_SESSION ['date_created'] = $row ['date_created'];
        // $_SESSION ['no_of_jobs'] = $row ['no_of_jobs'];
        $_SESSION ['logged_in'] = 1;
        $_SESSION ['email'] = $row ['email'];
        $_SESSION ['personal_email'] = $row ['personal_email'];
        
        $_SESSION ['password'] = $_POST ['txtPassword']; $_SESSION['wkpassword']=0;
       
        if (empty($_SESSION['email'])) {
            $_SESSION['no_email'] = true;
        }

        $_SESSION ['group_id'] = $row ['group_id'];
          $_SESSION ['role'] = $row ['role_id'];
                    $_SESSION ['is_regular'] = $row ['is_regular'];


        $_SESSION ['blood_group'] = $row ['blood_group'];
        $_SESSION ['currency'] = $row ['currency'];
        $_SESSION ['reporting_time'] = $row ['reporting_time'];
        $designation_id = $row['designation_id'];

        $_SESSION['designation_id'] = $designation_id;

        $_SESSION['is_lock'] = $row['is_lock'];
        $_SESSION['username'] = $row['username'];
        
        $_SESSION['default_filter'] = 0;
		
        $filter_result = $mysqli->query("SELECT filter_id FROM `tbl_filter` where is_default=1 and created_by=$user_id ");
        $filter_row = $filter_result->fetch_assoc();
        if (isset($filter_row)&&$filter_row['filter_id'] > 0)
            $_SESSION['default_filter'] =$filter_row['filter_id'];
 
 $_SESSION['notification_status']=0;
 

if($row['notification_status']==0)
$_SESSION['notification_status']=1;
else
$_SESSION['notification_status']=0;



 
 
if($row['is_regular']==2){
 
        if($row['emp_division_id']==3){
            
            echo "<script>window.location='time-tracker/'</script>";
                exit();
        }
        else
        {
        
                echo "<script>window.location='index.php'</script>";
                exit();
        }
}

 	
            
 
 
 

	$_SESSION['is_monthly_plan_rework'] = 0;
	
	/*
		
        $monthly_result = $mysqli->query("SELECT id FROM tbl_observations  WHERE audited_date>='2024-01-01' and  user_id = '$user_id' and observation=11 and notified=0");
        $monthly_row = $monthly_result->fetch_assoc();
        if ($monthly_row['id'] > 0)
            $_SESSION['is_monthly_plan_rework'] = 1;
            
    */
            
            
    /*        
	
            $_SESSION['danger_zone']="";
            

        $dz_result = $mysqli->query("SELECT id FROM  tbl_danger_zone  WHERE user_id = '$user_id' and success_per!=100");
        $dz_row = $dz_result->fetch_assoc();
        if ($dz_row!=false&&$dz_row['id'] > 0)
            $_SESSION['danger_zone'] = 1;
        
        $today_d = date('Y-m-d');
        $rw_result = $mysqli->query("SELECT * FROM `tbl_danger_zone` dz left join tbl_users u on u.user_id=dz.user_id   WHERE `manager_id` = $user_id and next_review_date<'$today_d' and success_per!=100 and success_per!=-1 and u.status='Active'");

        if ($rw_result->num_rows > 0 ){
            
        $_SESSION['next_review'] = 1;}

*/


        
        //notification
        $_SESSION['yrpass']=0;
        $_SESSION['bday']=0;
        $join_date = $row['gdoj'];
        $dob = $row['dob'];
        if(!empty($join_date)){
         $curr_month = date("m");
        $curr_year = date("Y");
        $c_today = date("d");
         $join_month = date("m",strtotime($join_date));
        $join_day = date("d",strtotime($join_date));
        $join_year = date("Y",strtotime($join_date));
        if($curr_month==$join_month&&$c_today==$join_day&&$curr_year!=$join_year&&$row['service_break_days']==0){ 
              $years = $curr_year-$join_year;
            if($years==1){
            $yr="year";}else{ $yr="years";} 
            $_SESSION['yrpass']=1;
        $_SESSION['compltedyears']  ="You are completing $years $yr with Aries today!!!";}
        }
        //For notificaton ends


        //*****FOR BIRTHDAY*******//
        if(!empty($dob)){
        $curr_month = date("m");
        $curr_year = date("Y");
        $c_today = date("d");
        $birth_month = date("m",strtotime($dob));
        $birth_day = date("d",strtotime($dob));
       
        if($curr_month==$birth_month&&$c_today==$birth_day){ 
           //   $years = $curr_year-$join_year;
            
        $_SESSION['bday']=1;
        $_SESSION['bdaywish']  ="Wishing you the best on your birthday and everything good in the year ahead";
        
        
       
        
      }
        
        }  
        //*****FOR BIRTHDAY ends*******//
            

        $is_health = $row ['is_health'];

        $result_user->free();

        $time_zone = $_SESSION['time_zone'];
        ini_set('date.timezone', $time_zone);
        $time = date('H:i:s', time());

        $timestamp=$_POST['timestamp'];
              
        $log_date = date('Y-m-d H:i:s', time());
        $today = date('Y-m-d');
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        
      
         $log_sql = "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,timestamp,user_ip,user_device) values('$user_id','$log_date','Login','Login Success','','$timestamp','$ip','$agent')";
         
         $mysqli->query($log_sql);
        
		
		
           $_SESSION['contact_details_complete'] = 1;
        
	
	
	    //Best employee nomination
	     $_SESSION['monthly_best_emp']=0;
         $forday = date('Y-m-d');
         //$forday = '2025-01-04';
         $is_fifth_day = (date('j', strtotime($forday)) <= 5);
         if ($is_fifth_day) {
	     $result_user_div_head = $mysqli->query("SELECT * FROM `best_employee_monthly_access` WHERE status=1 and user_id=$user_id and division_id!=''" );
	     if($result_user_div_head->num_rows>0){
	         $formonth=date('m', strtotime('-1 month'));//date('m');
            
           $foryear = date('Y', strtotime(' -1 month'));
	         //$foryear=date('Y');
	         $result_user_div_head = $mysqli->query("SELECT * FROM `tbl_best_employee_monthly` WHERE added_by=$user_id and year=$foryear and month=$formonth" ); 
	         if($result_user_div_head->num_rows==0)
	          $_SESSION['monthly_best_emp']=1;
	     }
     }
	    
	    //Best employee nomination ends
	    
	    

        

        

        $one_day_before = date('Y-m-d', strtotime('-1 days'));


        $observation_query = "SELECT count(o.id) as count_id  FROM  tbl_observations o left join tbl_users u on u.user_id=o.audited_by
		  WHERE o.audited_date>='2024-01-01' and  o.audited_date<='$one_day_before' and   o.user_id=$user_id and  o.notified=0 and u.status='Active' ";// o.observation in(0,1,2,3,4) and
        $observation_result = $mysqli->query($observation_query);
        $observation_row = $observation_result->fetch_assoc();


        if ($observation_row['count_id'] >= 1)
            $_SESSION['reply_comments'] = 1;

        $spouse_query = "SELECT id,country_code,whatsapp FROM `0_spouse_data` where user_id='$user_id' and status=1 and whatsapp_status=0";// o.observation in(0,1,2,3,4) and
        $spouse_result = $mysqli->query($spouse_query);
        $spouse_row = $spouse_result->fetch_assoc();
        if($spouse_row['id']>0)
        {
            $_SESSION['spouse_notification_status']=1;
            $_SESSION['spouse_country_code']=$spouse_row['country_code'];
            $_SESSION['spouse_whatsapp']=$spouse_row['whatsapp'];;

        }




if($_SESSION['is_regular']==1)
{

       $user_access = new USER_ACCESS($mysqli);

if(isset($user_access)&&!empty($user_access)){//******ADDED ISSET AND !EMPTY CONDITIONS BY SARANYA*****
        $str_access = $user_access->get_module_access();
        //print_r($str_access);
        //die();
        $_SESSION[] = $str_access;

        $children = $user_access->obj_employee->get_children($_SESSION['user_id'], true);
        $_SESSION['children'] = $children;
        $complete_children = $user_access->obj_employee->get_children($_SESSION['user_id']);
        
      
        $_SESSION['complete_children'] = $complete_children;
        
}
}
else
{
    
    $menu_result = $mysqli->query("SELECT id FROM tbl_user_access_setting WHERE user_id = '$user_id' and access_type=31");
    $menu_row = $menu_result->fetch_assoc();
    if($menu_row['id']>0)
      $SESSION['no_menu']=1;
    
    
}

// echo "SELECT id,first_login FROM  tbl_time  WHERE user_id = '$user_id' and date_log='$today'";exit;

 

        $first_result = $mysqli->query("SELECT id,TIME_FORMAT(first_login, '%H:%i') as first_login FROM  tbl_time  WHERE user_id = '$user_id' and date_log='$today'");
        $first_row = $first_result->fetch_assoc();
        if ($first_row!=false&&(($first_row['first_login'] == null) || ($first_row['first_login'] == "") || ($first_row['first_login'] == "00:00"))) {
            $rslt_routine = $mysqli->query("SELECT SEC_TO_TIME(SUM(time_to_sec(est_time))) routine_time
			FROM    tbl_daily_jobs 
			WHERE user_id='$user_id'
			AND status=1");

			$row_routine = $rslt_routine->fetch_assoc();
            
            if ($first_row!=false&&$first_row['id'] > 0)
                $mysqli->query("update  tbl_time set first_login='$time',routine='".$row_routine['routine_time']."' where user_id = '$user_id' and date_log='$today'");
            else
                $mysqli->query("insert into tbl_time(user_id,date_log,first_login,routine) values('$user_id','$today','$time','".$row_routine['routine_time']."')");
            
           

        }
        else{
            if(empty($first_row)){
                
            $rslt_routine = $mysqli->query("SELECT SEC_TO_TIME(SUM(time_to_sec(est_time))) routine_time
			FROM    tbl_daily_jobs 
			WHERE user_id='$user_id'
			AND status=1");

			$row_routine = $rslt_routine->fetch_assoc();    
                
             $mysqli->query("insert into tbl_time(user_id,date_log,first_login,routine) values('$user_id','$today','$time','".$row_routine['routine_time']."')");
            
        

            }
        }
        //
        if(isset($_POST['page'])&&$_POST['page']=='dashboard')
        header("location:beta/dashboard.php");    
    } else {

        $user_result = $mysqli->query("select user_id from tbl_users where username = '$user_name'");

$timestamp=$_POST['timestamp'];

        $user_row = $user_result->fetch_assoc();
        $fail_user_id = $user_row ['user_id'];
        $user_result->free();
        if ($fail_user_id > 0) {
            $user_query1 = "SELECT exit_only,division_head,if(u.display_name!='',u.display_name,u.full_name) as full_name,d.short_name as divs,work_location,group_id,personal_email,photo,employee_code,time_zone,is_lock,reporting_time,division_rate,hourly_rate,u.currency,user_id, upper(username) as username, credential, emp_division_id , designation_id,date_created,blood_group,email,is_health,latest_pic  FROM tbl_users u  "
                    . " left join  tbl_emp_workplace w on w.id=u.work_location "
                    . "LEFT JOIN tbl_dimensions d ON u.emp_division_id=d.id "
                    
                    . "   WHERE  status='Active' and username = '$user_name'";
            $result_user1 = $mysqli->query($user_query1);
            $user_row1 = $result_user1->fetch_assoc();
            
            $time_zone1 = $user_row1['time_zone'];
            ini_set('date.timezone',$time_zone1);
            $log_date = date('Y-m-d H:i:s');
            
            $ip = $_SERVER['REMOTE_ADDR'];
            $agent = $_SERVER['HTTP_USER_AGENT'];
            
               
            
            $log_sql = "insert into tbl_employee_log(user_id,log_date,page_type,action,remarks,user_ip,user_device) values('$fail_user_id','$log_date','Login','Login Failure','Password Error <br>Password:$password ','$ip','$agent')";
            $mysqli->query($log_sql);
            
            
            //*******Password error mail to user*********************************

         
            
            $err_date = date('d-m-Y h:i:s a'); 
            $to = $user_row1['email']!=''?$user_row1['email']:$user_row1['personal_email'];
            ///*********password error count taking********
             $today1 = date('Y-m-d');
            $attempt_query = "SELECT count(id) as count_id  FROM  tbl_employee_log   WHERE user_id=$fail_user_id  and date(log_date)='$today1' and remarks like '%Password Error%'";
            $at_result = $mysqli->query($attempt_query);
            $at_row = $at_result->fetch_assoc();
            $no_of_wrong_attempts = $at_row['count_id'];
            if ($no_of_wrong_attempts > 3 && $password != "") {
                    //**********End of password error count************


                    $message = "<b>Dear " . $user_row1['full_name'] . "</b>,<br><br>";
                    $message .= " This is to inform you that there was an attempt to login your Effism with Wrong Password<br><br>
			  <table style='background-color:#ffffff;border:1px solid #c3c3c3;border-collapse:collapse;width:70%; padding:3'>
				<tbody><tr>
					<th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Last Attempt Time</th>
                                        <th style='background-color:#e5eecc;border:1px solid #c3c3c3;padding:3px;text-align:left;'>Attempt Password</th>
					
				</tr>
				<tr>
			<td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $err_date . "</td>
                            <td style='border:1px solid #c3c3c3; padding:3px;vertical-align:top;'>" . $password . "</td>
			
			";
//                    mail($to, "Effism Password Error Notification", $message, $headers);
                  ////***********PHP mailer STARTS************  
                //   @include_once 'PHPMailer/class.phpmailer.php';
               /*    require 'vendor/autoload.php';
       $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "ns1016702.ip-92-204-146.us";
        $mail->Port = 25;
        $mail->SMTPAuth = true;
        $mail->Username = "mail@effism.com";
        $mail->Password = "aries2016";
        $mail->ClearAllRecipients();
        $mail->SetFrom("mail@effism.com", 'Effism Mail');
        $mail->AddAddress($to);
        $mail->IsHTML(true);
        $mail->Subject = "Effism Password Error Notification";
        $mail->Body = $message;*/
    //   if (!$mail->Send()) {
    //         echo "Mailer Error: " . $mail->ErrorInfo;
    //     } else {
            
    //     }
   
        ////***********PHP mailer ends     
    }
                ///***********Password error mail ends
        
    
        }
         if(isset($_POST['page'])&&$_POST['page']=='dashboard')
              header("Location:index.php?page=dashboard");
     else {
         $message = "User name and password do not match";echo "<script type='text/javascript'>alert('$message'); window.location.href='$site_url';</script>";
//  header("Location:index.php?time=$timestamp");
     }
        exit();
    }
}
}else {
     if(isset($_POST['page'])&&$_POST['page']=='dashboard')
              header("Location:index.php?page=dashboard");
     else   header("Location:index.php");
    exit();
}

//******The scroll Query*************//
$_SESION['scroll'] = "";
$scroll_sql = $mysqli->query("SELECT * FROM  tbl_autoscroll where active_till>='$today' and status=1 LIMIT 1");
$scroll_row = $scroll_sql->fetch_assoc();
if($scroll_row!=false)
$_SESION['scroll'] = base64_decode($scroll_row['content']); 

////***Scroll ends ************//



?>