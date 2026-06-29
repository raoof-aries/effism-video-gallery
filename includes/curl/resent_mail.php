<?php
ini_set('display_errors', '1');
//print_r($_REQUEST);
//$con = new mysqli("p:"."localhost", "effismuser_efficiency", "{ebx+MnNkPG0", "effismuser_live");
$con = new mysqli("p:"."localhost", "effism_live", "4G&L6b^GFQNB!U-WT)", "effism_live");
//$con = new mysqli("p:"."localhost", "effism_eff", "EEV4_ADV*{fF", "effism_efficiency");

//$con = new mysqli("p:"."localhost","root","","effism_efficiency");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
//print_r($_REQUEST);
//include("../../encryption.class.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception; 
$hr_id =$_REQUEST['hr_id'];
$employee_code = $_REQUEST['employee_code'];
$personal_email = $_REQUEST['email'];
$full_name = $_REQUEST['full_name'];

 if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='RESENT_ACTIVATION_MAIL') {
     
                 if (!empty($_REQUEST['division_head'])) {
                     $user_sql = "select * from tbl_users where hr_id='$hr_id' and employee_code='$employee_code'";
        $user_result = mysqli_query($con, $user_sql);
        $user_row = mysqli_fetch_assoc($user_result);
        $user_id = $user_row['user_id'];
        //$full_name = $user_row['full_name'];
        $work_location=$user_row['work_location'];

                $sql_sel = "SELECT if(email!='',email,if(personal_email!='',personal_email,'software@ariesgroup.ae')) as email,user_id FROM tbl_users WHERE hr_id=" . $_REQUEST['division_head'];
                $result1 = mysqli_query($con, $sql_sel);
                $row_data = mysqli_fetch_assoc($result1);
                $_REQUEST['division_head'] = $row_data['user_id'];
                $email = $row_data['email'];
            

             $personal_email = (($personal_email != "")&&($personal_email != "NA")) ? $personal_email : "software@ariesgroup.ae";
            

                $token = bin2hex(random_bytes(50));
                
                $stmt = mysqli_prepare($con, "INSERT INTO tbl_pass_reset (user_id,email, token) VALUES (?, ?,?)");

               mysqli_stmt_bind_param($stmt, "sss", $user_id, $personal_email, $token);

                mysqli_stmt_execute($stmt);
                
                
           
  


           // $dta = $ecryption->encode("userid=$hr_id&division_head=" . $_REQUEST['division_head']);

            //@include_once '../../PHPMailer/class.phpmailer.php';
            require '../../vendor/autoload.php';
//$mail = new PHPMailer(true);

            $body = "Hi $full_name, <br/><br/> <b style='color:red;'>Your Efficiency Account has been created successfully!</b><br/><br/>
			 &nbsp;&nbsp;&nbsp;<a href='http://www.effism.com/user-creation.php?token=$token'>Click this link to activate the account</a>";
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
             $mail->SMTPSecure = 'ssl'; // Enable TLS encryption

       // $mail->Port = 25;
//Set the SMTP port number - likely to be 25, 465 or 587
            $mail->Port = 465;
//Whether to use SMTP authentication
            $mail->SMTPAuth = true;
//Username to use for SMTP authentication
            $mail->Username = "mail@effism.com";
//Password to use for SMTP authentication
            $mail->Password = "s+z#o(t*B*O}";
//Set who the message is to be sent from

$mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

            $mail->ClearAllRecipients();
            $mail->SetFrom("mail@effism.com", 'Effism Account Activation Mail');
            $mail->AddAddress($personal_email);
            if (!empty($email)) {
                 $mail->AddCC($email);
            }
		  if(($work_location == 12)||($work_location == 22)) {
				$mail->AddCC("admin@marinebiz.tv");
			}
			else if($work_location == 25) {
				$mail->AddCC("admin@ariesepica.com");
			}
            else {
				//$mail->AddCC("admin@ariesgroup.ae");
			}
			$mail->AddCC("arshad@ariesgroup.ae");
			$mail->AddCC("haifa.ma@arieslocal.com");
			
            $mail->IsHTML(true);


            $mail->Subject = "Effism Account Activation ";
            $mail->Body = $body;
            if(!empty($_REQUEST['division_head'])) {
                //print print $body;;
               // exit;
                if (!$mail->Send()) {
    
                    //display_error("Mailer Error: " . $mail->ErrorInfo);
                   // echo $body;
                    //"Mailer Error: " . $mail->ErrorInfo;
                    print 'not sent';
                } else {
                     print 'message sent successfully';
                    //print $body;
                    //display_notification('Thank you! Your Division Leave Entry has been Sent to Admin Department.');
                }
            }
        }

     
 }
 ?>