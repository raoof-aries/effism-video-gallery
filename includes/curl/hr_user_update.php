<?php
ini_set('display_errors', '1');

//$con = new mysqli("p:"."localhost", "effismuser_efficiency", "{ebx+MnNkPG0", "effismuser_live");
$con = new mysqli("p:"."localhost", "effism_live", "4G&L6b^GFQNB!U-WT)", "effism_live");
//$con = new mysqli("p:"."localhost", "effism_eff", "EEV4_ADV*{fF", "effism_efficiency");

//$con = new mysqli("p:"."localhost","root","","effism_efficiency");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
include("../../employee_tree.class.php");
//include("../../encryption.class.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 
require '../../vendor/autoload.php';

    


$hr_id = (isset($_REQUEST['employee_id'])?$_REQUEST['employee_id']:"");
$full_name = (isset($_REQUEST['full_name'])?$_REQUEST['full_name']:"");
$employee_code = (isset($_REQUEST['employee_code'])?$_REQUEST['employee_code']:"");
$designation = (isset($_REQUEST['designation'])?$_REQUEST['designation']:"");
$gender = (isset($_REQUEST['gender'])?$_REQUEST['gender']:"");
$dob = (isset($_REQUEST['dob'])?$_REQUEST['dob']:"");
$doj = (isset($_REQUEST['doj'])?$_REQUEST['doj']:"");
$gdoj = (isset($_REQUEST['gdoj'])?$_REQUEST['gdoj']:"");
$personal_email = (isset($_REQUEST['personal_email'])?$_REQUEST['personal_email']:"");
$mobile = (isset($_REQUEST['mobile'])?$_REQUEST['mobile']:"");
$personal_mobile = (isset($_REQUEST['personal_mobile'])?$_REQUEST['personal_mobile']:"");
$emp_company_id = (isset($_REQUEST['emp_company_id'])?$_REQUEST['emp_company_id']:"");
$emp_division_id = (isset($_REQUEST['emp_division_id'])?$_REQUEST['emp_division_id']:"");
$emp_subdivision_id = (isset($_REQUEST['emp_subdivision_id'])?$_REQUEST['emp_subdivision_id']:"");
$group_id = (isset($_REQUEST['group_id'])? $_REQUEST['group_id'] : "");
$emp_type = (isset($_REQUEST['emp_type'])?$_REQUEST['emp_type']:"");

$marine_category = (isset($_REQUEST['marine_category'])?$_REQUEST['marine_category']:"");
$work_category = (isset($_REQUEST['work_category'])?$_REQUEST['work_category']:"");
$job_category = (isset($_REQUEST['job_category'])?$_REQUEST['job_category']:"");
$admin_category = (isset($_REQUEST['admin_category'])?$_REQUEST['admin_category']:"");


$reporting_location = (isset($_REQUEST['reporting_location'])?$_REQUEST['reporting_location']:"");
$work_location = (isset($_REQUEST['work_location'])?$_REQUEST['work_location']:"");
$photo = (isset($_REQUEST['photo'])?$_REQUEST['photo']:"");
$user_height = (isset($_REQUEST['user_height'])?$_REQUEST['user_height']:"");
$blood_group = (isset($_REQUEST['blood_group'])?$_REQUEST['blood_group']:"");
$marital_status = (isset($_REQUEST['marital_status'])?$_REQUEST['marital_status']:"");
$home_town = (isset($_REQUEST['hometown'])? $_REQUEST['hometown']:"");
$address = (isset($_REQUEST['address'])?$_REQUEST['address']:"");



$reporting_time = (isset($_REQUEST['reporting_time'])?$_REQUEST['reporting_time']:"");

if (!empty($_REQUEST['doAction']) && $_REQUEST['doAction'] == "CANCELEMPLOYEE") {
    
    $arrClidren=array();
    $Edate = $_REQUEST['Edate'];
    $user_sql = "select * from tbl_users where hr_id='$hr_id' and employee_code='$employee_code'";
    $user_result = mysqli_query($con, $user_sql);
    $user_row = mysqli_fetch_assoc($user_result);
    $user_id = $user_row['user_id'];
    $status = $user_row['status'];
    
    $obj_employee = new EMPLOYEE_TREE($con);

    $arrClidren = $obj_employee->get_children($user_id);
    
    if ($status == 'Inactive') {
        print "<b style='color:red;'>This person is already Inactive in EFFISM.</b>";
    } else if (isset($arrClidren)&&(gettype($arrClidren)=="array")&&count($arrClidren) > 0 && !empty($arrClidren)) {
        print $flag = "<b style='color:red;'>Sorry! You can't make this user Inactive in 'EFFISM' , due to active subordinates existing under him.</b>";
        $flag_edit = false;
        $sql_upd = "UPDATE tbl_users
		SET last_day='".$Edate."'
		WHERE user_id= $user_id";
        mysqli_query($con, $sql_upd);
    } else {
        $sql_upd = "UPDATE tbl_users
		SET status = 'Inactive',
		is_regular = 0,
		lft=0, rght=0,
		parent_id = 0,division_head = 0,last_day='".$Edate."'
		WHERE user_id= $user_id";
        mysqli_query($con, $sql_upd);
        print "<b style='color:red;'>This person has been inactivated from 'EFFISM' also.</b>";
    }
}

else if(!empty($_REQUEST['doAction']) && $_REQUEST['doAction'] == "REPORTING_HEAD") {
	###########################################################SETTING NODE ##################
	$mysqli = $con;
	

	#################################################################################
		$sql1 = "SELECT user_id,lft,rght FROM tbl_users WHERE hr_id=".$_REQUEST['employee_id'];
		$result = $mysqli->query($sql1);
		 $row = $result->fetch_assoc();

		$sql2 = "SELECT user_id FROM tbl_users WHERE hr_id=".$_REQUEST['new_reporting_head'];
		$result2 = $mysqli->query($sql2);
		$row_head = $result2->fetch_assoc();

	$user_id = $row['user_id'];
/*	if(empty($row['lft']) && empty($row['rght'])) {
		$query = " select @max_no := MAX(rght) from tbl_users;
				 UPDATE tbl_users SET lft=@max_no+1, rght=@max_no+2
				WHERE user_id=$user_id;";
			if ($mysqli->multi_query($query)) {
				do {
					// store first result set 
					if ($result = $mysqli->store_result()) {

						$result->free();
					}
				} while ($mysqli->more_results());
			}

	}*/
	if(empty($row_head['user_id'])) {
		$row_head['user_id']=0;
		$sql_upd = "UPDATE tbl_users SET is_regular=0 WHERE user_id=".$row['user_id'];
		$mysqli->query($sql_upd);
	}
	//$obj_employee = new EMPLOYEE_TREE($mysqli);
	//$obj_employee->move( $row['user_id'],$row_head['user_id'],true);
	$sql_upd = "UPDATE tbl_users SET parent_id=".$row_head['user_id'].",division_head=".$row_head['user_id']. " WHERE user_id=".$row['user_id'];
	$mysqli->query($sql_upd);

	  $sql1 = "SELECT t1.full_name , t1.email, if(t1.email!='',t1.email,if(t1.personal_email!='',t1.personal_email,'mail@effism.com')) personal_email,
	  t2.full_name head_name,
	  if(t2.email!='',t2.email,if(t2.personal_email!='',t2.personal_email,'mail@effism.com')) head_email, t2.personal_email head_personal_email
			FROM tbl_users t1
			LEFT JOIN tbl_users t2
			ON t1.parent_id = t2.user_id
			WHERE t1.hr_id=".$_REQUEST['employee_id'];
	$user_result = $mysqli->query($sql1);
    $user_row = $user_result->fetch_assoc();
	if(!empty($_REQUEST['old_reporting_head'])) {
		$sql_old = "SELECT t1.full_name , if(t1.email!='',t1.email,if(t1.personal_email!='',t1.personal_email,'mail@effism.com')) email, t1.personal_email
					FROM tbl_users t1
					WHERE t1.hr_id=".$_REQUEST['old_reporting_head'] ." AND t1.status='Active'";
		$head_result =  $mysqli->query($sql_old);
		$head_row = $head_result->fetch_assoc();
	}
	else {
		$head_row['email'] = "mail@effism.com";
	}
	
	/*$arrr_data[] = array("to_mail"=>$user_row['personal_email'],
		"body" =>" <b>Dear ".$user_row['full_name']."</b>,<br/><b/>
				  Your Reporting head has been changed from <b>".$head_row['full_name']. "</b> to <b>". $user_row["head_name"]."</b>.<br/><br/><b>Kind Regards,<br/><br/>Effism Team</b>",
			"cc"=>""

		);*/
		$arrr_data[] = array("to_mail"=>( !empty($user_row['head_email'])?$user_row['head_email']:$user_row['head_personal_email']),
		"body" =>" <b>Dear ".$user_row['head_name']."</b>,<br/><br/>
				  <b>".$user_row['full_name']."</b> has been moved under your tree<br/><br/>
				  <table border='1'>
				  <tr style='background-color:#f4b642;'>
				  <th>Previous head</th>
				  <th>Current head</th>
				  <th>Effective From</th>
				  </tr>
				  <tr>
				  <td>".$head_row['full_name']."</td>
				  <td>".$user_row['head_name']."</td>
				  <td>".gmdate('d/m/Y')."</td>
				  </tr>
				  </table><br/><br/>
				  <b>Kind Regards,<br/><br/>Effism Team</b>",
		"cc"=>$head_row['email'].",".$user_row['personal_email']
		);
		
	 //Create a new PHPMailer instance
	$mail = new PHPMailer(true);
//Tell PHPMailer to use SMTP
	$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
	$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';
//Set the hostname of the mail server
	$mail->Host = "mail.effism.com";
//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = 465;
//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
//Username to use for SMTP authentication
	$mail->Username = "mail@effism.com";
//Password to use for SMTP authentication
	$mail->Password = "s+z#o(t*B*O}";
	   $mail->SMTPSecure = 'ssl'; // Enable TLS encryption

	$mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
	
//Set who the message is to be sent from
	foreach($arrr_data as $key => $data) {
		$mail->ClearAllRecipients();
		$mail->SetFrom("mail@effism.com",'Effism Reporting Head Change');
		//$data['to_mail'] = "software@ariesgroup.ae";
		$mail->AddAddress($data['to_mail']);
		/*if(!empty($email)) {
			 $mail->AddCC($email);
		}*/
		if(!empty($data['cc'])) {
			$arrCC = explode(",",$data['cc']);
			foreach($arrCC as $key => $cc) {
				$mail->AddCC($cc);
			}
		//$mail->AddCC($data['cc']);
		}
		$mail->AddCC("arshad@ariesgroup.ae");

		$mail->IsHTML(true);


		$mail->Subject = "Effism Reporting Head Change ";
		$mail->Body = $data['body'];

		if (!$mail->Send()) {

			//display_error("Mailer Error: " . $mail->ErrorInfo);
			echo "Mailer Error: ".$mail->ErrorInfo;
			//print 'not done';
		} else {
			print "mail_sent";
			//display_notification('Thank you! Your Division Leave Entry has been Sent to Admin Department.');
		}
		$mail->ClearAddresses();
	}
############## MIAL SENDING OPTION ##################################

}
else if(!empty($_REQUEST['doAction']) && $_REQUEST['doAction'] == "REPORTING_TIME") {
	###########################################################SETTING NODE ##################
	$mysqli = $con;
	

	#################################################################################
		$sql1 = "SELECT user_id,lft,rght FROM tbl_users WHERE hr_id=".$_REQUEST['employee_id'];
		$result = $mysqli->query($sql1);
		 $row = $result->fetch_assoc();

		
	$sql_upd = "UPDATE tbl_users 
	SET reporting_time='".$_REQUEST['new_reporting_time']."'
	WHERE user_id=".$row['user_id'];
	$mysqli->query($sql_upd);


	  $sql1 = "SELECT t1.full_name , t1.email, if(t1.email!='',t1.email,if(t1.personal_email!='',t1.personal_email,'mail@effism.com')) personal_email,
	  t2.full_name head_name,
	  if(t2.email!='',t2.email,if(t2.personal_email!='',t2.personal_email,'mail@effism.com')) head_email, t2.personal_email head_personal_email
			FROM tbl_users t1
			LEFT JOIN tbl_users t2
			ON t1.parent_id = t2.user_id
			WHERE t1.hr_id=".$_REQUEST['employee_id'];
	$user_result = $mysqli->query($sql1);
    $user_row = $user_result->fetch_assoc();
	
	
	/*$arrr_data[] = array("to_mail"=>$user_row['personal_email'],
		"body" =>" <b>Dear ".$user_row['full_name']."</b>,<br/><b/>
				  Your Reporting head has been changed from <b>".$head_row['full_name']. "</b> to <b>". $user_row["head_name"]."</b>.<br/><br/><b>Kind Regards,<br/><br/>Effism Team</b>",
			"cc"=>""

		);*/
		$arrr_data[] = array("to_mail"=>( !empty($user_row['email'])?$user_row['email']:$user_row['personal_email']),
		"body" =>" <b>Dear ".$user_row['full_name']."</b>,<br/><br/>
				  <b>Your Reporting time has been changed <br/><br/>
				  <table border='1'>
				  <tr style='background-color:#f4b642;'>
				  <th>Previous Reporting Time</th>
				  <th>Current Reporting Time</th>
				  <th>Effective From</th>
				  </tr>
				  <tr>
				  <td>".$_REQUEST['old_reporting_time']."</td>
				  <td>".$_REQUEST['new_reporting_time']."</td>
				  <td>".gmdate('d/m/Y')."</td>
				  </tr>
				  </table><br/><br/>
				  <b>Kind Regards,<br/><br/>Effism Team</b>",
		"cc"=>'arshad@ariesgroup.ae'.",".(!empty($user_row['head_email'])?$user_row['head_email']:$user_row['head_personal_email'])
		);
		
	 //Create a new PHPMailer instance
	$mail = new PHPMailer(true);
//Tell PHPMailer to use SMTP
	$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
	$mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';
//Set the hostname of the mail server
	$mail->Host = "mail.effism.com";
//Set the SMTP port number - likely to be 25, 465 or 587
	$mail->Port = 465;
//Whether to use SMTP authentication
	$mail->SMTPAuth = true;
//Username to use for SMTP authentication
	$mail->Username = "mail@effism.com";
//Password to use for SMTP authentication
	$mail->Password = "s+z#o(t*B*O}";
	
	  $mail->SMTPSecure = 'ssl'; // Enable TLS encryption

	$mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
	
//Set who the message is to be sent from
	foreach($arrr_data as $key => $data) {
		$mail->ClearAllRecipients();
		$mail->SetFrom("mail@effism.com",'Effism Reporting Time Change');
		//$data['to_mail'] = "software@ariesgroup.ae";
		$mail->AddAddress($data['to_mail']);
		/*if(!empty($email)) {
			 $mail->AddCC($email);
		}*/
		if(!empty($data['cc'])) {
			$arrCC = explode(",",$data['cc']);
			foreach($arrCC as $key => $cc) {
				$mail->AddCC($cc);
			}
		//$mail->AddCC($data['cc']);
		}
		$mail->AddCC("arshad@ariesgroup.ae");

		$mail->IsHTML(true);


		$mail->Subject = "Effism Reporting Time Change ";
		$mail->Body = $data['body'];

		if (!$mail->Send()) {

			//display_error("Mailer Error: " . $mail->ErrorInfo);
			echo "Mailer Error: ".$mail->ErrorInfo;
			//print 'not done';
		} else {
			print "mail_sent";
			//display_notification('Thank you! Your Division Leave Entry has been Sent to Admin Department.');
		}
		$mail->ClearAddresses();
	}
############## MIAL SENDING OPTION ##################################

}
else if(!empty($_REQUEST['doAction']) && $_REQUEST['doAction'] == "CHANGETYPE") {
    
    if($hr_id>0)
    {
        $user_sql = "select * from tbl_users where hr_id='$hr_id' ";
        $user_result = mysqli_query($con, $user_sql);
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = $user_row['user_id'];

        if ($user_id > 0) {
        $sql_upd = "UPDATE tbl_users 
	SET emp_type='$emp_type',employee_code='$employee_code'
	WHERE hr_id='$hr_id'";
	mysqli_query($con,$sql_upd);
    }
    
}

}
else {
    
   

    if ($hr_id > 0) {

        $user_sql = "select * from tbl_users where hr_id='$hr_id' and employee_code='$employee_code'";
        $user_result = mysqli_query($con, $user_sql);
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = $user_row['user_id'];

        if ($user_id > 0) {
            $sql = "update tbl_users set
	full_name='$full_name',
	designation='$designation',
	gender='$gender',
	dob='$dob',
	doj='$doj',
	gdoj='$gdoj',
	personal_email='$personal_email',
	mobile='$mobile',
        personal_mobile='$personal_mobile',
	emp_company_id='$emp_company_id',
	emp_division_id='$emp_division_id',
	emp_subdivision_id='$emp_subdivision_id',
	emp_type='$emp_type',

	work_category='$work_category',
	job_category='$job_category',
	marine_category='$marine_category',
	admin_category='$admin_category',
	
	reporting_location='$reporting_location',
	marital_status='$marital_status',
	home_town='$home_town',
	address='$address',
	work_location='$work_location'";
            if ($photo != "")
                $sql .= ",photo='$photo'";
            if ($user_height != "")
                $sql .= ",user_height='$user_height'";
            if ($blood_group != "")
                $sql .= ",blood_group='$blood_group'";

            $sql .= " where hr_id='$hr_id' and employee_code='$employee_code'";
			

        $result = mysqli_query($con, $sql);
        }
        else {
			$user_name =$employee_code;
		   $user_name = str_replace('AG0000','',$user_name);
		    $user_name = str_replace('AES ','',$user_name);
		   $user_name = trim($user_name);
             $sql = " insert into tbl_users
 (hr_id,username,full_name,	employee_code,designation,gender,dob,doj,gdoj,personal_email,mobile,personal_mobile,category,emp_company_id,emp_division_id, 	emp_subdivision_id,group_id,emp_type,work_category,marine_category,admin_category,reporting_location,work_location,status,is_regular,photo,date_created,user_height,blood_group,is_health, is_erp,marital_status,home_town,address,reporting_time) 
 values  ('$hr_id','$user_name','$full_name','$employee_code','$designation','$gender','$dob','$doj','$gdoj','$personal_email','$mobile', '$personal_mobile','user','$emp_company_id','$emp_division_id', '$emp_subdivision_id','$group_id','$emp_type','$work_category', '$marine_category','$admin_category','$reporting_location','$work_location', 'Active',0,'$photo','$date_created','$user_height','$blood_group',0,1,'$marital_status','$home_town','$address','$reporting_time')";
            $email = "";
            

        $result = mysqli_query($con, $sql);

            if (!empty($_REQUEST['division_head'])) {
                $sql_sel = "SELECT if(email!='',email,if(personal_email!='',personal_email,'arshad@ariesgroup.ae')) as email,user_id FROM tbl_users WHERE hr_id=" . $_REQUEST['division_head'];
                $result1 = mysqli_query($con, $sql_sel);
                $row_data = mysqli_fetch_assoc($result1);
                $_REQUEST['division_head'] = $row_data['user_id'];
                $email = $row_data['email'];
                
                    $user_sql = "select * from tbl_users where hr_id='$hr_id' ";
                    $user_result = mysqli_query($con, $user_sql);
                    $user_row = mysqli_fetch_assoc($user_result);
                    $user_id = $user_row['user_id'];

             $personal_email = (($personal_email != "")&&($personal_email != "NA")) ? $personal_email : "arshad@ariesgroup.ae";

           $token = bin2hex(random_bytes(50));
                
                $stmt = mysqli_prepare($con, "INSERT INTO tbl_pass_reset (user_id,email, token) VALUES (?, ?,?)");
                echo $user_id;

               mysqli_stmt_bind_param($stmt, "sss", $user_id, $personal_email, $token);

                mysqli_stmt_execute($stmt);



            $body = "Hi $full_name, <br/><br/> <b style='color:red;'>Your Efficiency Account has been created successfully!</b><br/><br/>
			 Click this link to activate the account&nbsp;&nbsp;&nbsp;<a href='http://www.effism.com/user-creation.php?token=$token'>http://www.effism.com/user-creation.php" . $dta . "</a>";
            //Create a new PHPMailer instance
            $mail = new PHPMailer(true);
//Tell PHPMailer to use SMTP
            $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
            $mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
            $mail->Debugoutput = 'html';
//Set the hostname of the mail server
            $mail->Host = "mail.effism.com";
//Set the SMTP port number - likely to be 25, 465 or 587
            $mail->Port = 465;
//Whether to use SMTP authentication
            $mail->SMTPAuth = true;
//Username to use for SMTP authentication
            $mail->Username = "mail@effism.com";
//Password to use for SMTP authentication
            $mail->Password = "s+z#o(t*B*O}";
            
              $mail->SMTPSecure = 'ssl'; // Enable TLS encryption

	$mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
            
//Set who the message is to be sent from

            $mail->ClearAllRecipients();
            $mail->SetFrom("mail@effism.com", 'Effism Account Activation Mail');
            $mail->AddAddress($personal_email);
            if (!empty($email)) {
                $mail->AddCC($email);
            }
			if($work_location == 12) {
				$mail->AddCC("admin@marinebiz.tv");
			}
			else if($work_location == 25) {
				$mail->AddCC("admin@ariesepica.com");
			}
            else {
				$mail->AddCC("admin@ariesgroup.ae");
			}
			$mail->AddCC("arshad@ariesgroup.ae");
            $mail->IsHTML(true);


            $mail->Subject = "Effism Account Activation ";
            $mail->Body = $body;
            if(!empty($_REQUEST['division_head'])) {
                  
                if (!$mail->Send()) {
  
                    //display_error("Mailer Error: " . $mail->ErrorInfo);
                    echo "Mailer Error: " . $mail->ErrorInfo;
                    //print 'not done';
                } else {
    
                    //display_notification('Thank you! Your Division Leave Entry has been Sent to Admin Department.');
                }
                
            }
        }
}
    }
}



/*
  if(!empty($_REQUEST['employee_id']) && $_REQUEST['HourlyRateUpdate'] == 'True') {
  $employee_id = $_REQUEST['employee_id'];
  $HourlyRate = $_REQUEST['HourlyRate'];
  $currency = $_REQUEST['currency'];
  $sql = "UPDATE tbl_users SET hourly_rate = '$HourlyRate',
  currency = '$currency'
  WHERE hr_id = '$employee_id'";
  $result = mysqli_query($con,$sql);
  if($result) {
  print 'success';
  }

  }
 */
?>