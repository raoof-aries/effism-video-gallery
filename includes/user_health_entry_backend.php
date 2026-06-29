<?php

session_start();
include("connect.inc.php");

//$user_id = $_SESSION['user_id'];

$user_id = $_GET['user_id'];
$current_month = date('m');//"06"; //date('m');
$current_year = date('Y');
$month = date('M');//"June"; //date('M');

// $entrymonth_yr = addslashes($_GET['curmonth_year']);
//    $arr_entrymonth = explode("-", $entrymonth_yr); 
//    $current_month = date("m", strtotime($entrymonth_yr)); 
//    $current_year = $arr_entrymonth[1];

if(isset($_GET['do'])) {
    $do = $_GET['do'];

    if($do == "deleteImg") {
        $fileName = $_POST['fileName'];
    }
    $entrymonth_yr = addslashes($_GET['curmonth_year']);
    $arr_entrymonth = explode("-", $entrymonth_yr); 
    $current_month = date("m", strtotime($entrymonth_yr)); 
    $current_year = $arr_entrymonth[1];

    $fileName = explode('/', $fileName);
     $fileName = end($fileName);
//   echo "SELECT `lab_result` FROM `tbl_health` WHERE user_id=$user_id AND `health_month`='$current_month' and `health_year`='$current_year'";exit;
     $rslt2 = $mysqli->query("SELECT `lab_result` FROM `tbl_health` WHERE user_id=$user_id AND `health_month`='$current_month' and `health_year`='$current_year'");
    $row = $rslt2->fetch_assoc();

    if (!empty($row['lab_result'])) {
        $existingFiles = $row['lab_result'];

        $updatedFiles = str_replace($fileName, "", $existingFiles);
        $updatedFiles = str_replace(",,", ",", $updatedFiles);
        $updatedFiles = trim($updatedFiles, ',');

        $rslt3 = $mysqli->query("UPDATE `tbl_health` SET lab_result='" . addslashes($updatedFiles) . "' WHERE user_id=" . addslashes($user_id) . " and health_month='" . addslashes($current_month) . "' and health_year='" . addslashes($current_year) . "'");
        
        unlink("../upload/lab_results/$fileName");

        if($rslt3) {
            updateSuccess("File deleted successfully",$current_month,$current_year);
            header("location:user_health_entry.php");
//            die("<tr>
//        <td align='center'>
//            <font size='+1' color='green'><b>File deleted successfully</b></font>
//        </td>
//    </tr>");
        }
        else {
            updateFail("File could not be deleted",$current_month,$current_year);
            
//            header('HTTP/1.1 400 Bad Request');
//            exit("<tr>
//        <td align='center'>
//            <font size='+1' color='red'><b>File could not be deleted</b></font>
//        </td>
//    </tr>");
        }
    }
}
else {
    $entrymonth_yr = addslashes($_POST['curmonth_year']);
    $height = addslashes($_POST['h_height']);
    $weight = addslashes($_POST['weight']);
    $bp = addslashes($_POST['bp']);
    $pulse = addslashes($_POST['pulse']);
    $random_blood_sugar = addslashes($_POST['random_blood_sugar']);
    $lipid_profile = addslashes($_POST['lipid_profile']);
    $uric_acid = addslashes($_POST['uric_acid']);
    $hba1c = addslashes($_POST['hba1c']);
    //$lab_result = addslashes($_POST['lab_result']);
    $remarks = $_POST['remarks'];

    $fileUploadStatus;
    $arr_entrymonth = explode("-", $entrymonth_yr);

    $current_month = date("m", strtotime($entrymonth_yr));
        
    $current_year = $arr_entrymonth[1];
//echo $current_month.$current_year;exit;

    /*==============================================================================================================================*/

    if (isset ($_FILES['lab_result']))
    {

        //$valid_extensions = array('jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf' , 'doc' , 'ppt', 'sql'); // valid extensions

        if(!empty($_FILES['lab_result']))
        {
            //$files = array_filter($_FILES['lab_result']['name']); //something like that to be used before processing files.

            // Count # of uploaded files in array
            $total = count($_FILES['lab_result']['name']);

            // Loop through each file
            for( $i=0 ; $i <= $total ; $i++ ) {
                $attachment = $_FILES['lab_result']['name'][$i];
                //Get the temp file path
                $tmp = $_FILES['lab_result']['tmp_name'][$i];
                $errorimg = $_FILES["attachments"]['error'][$i];

                print_r($_FILES['lab_result'][$i]);

                //Make sure we have a file path
                if ($tmp != ""){
                    //Setup our new file path
                    // get uploaded file's extension

                    $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));

                    $target_dir = "../upload/lab_results/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    // can upload same image using rand function
                    $final_attachment = $user_id . "_" . $current_month . "_" . $current_year . "_" . $i . "_" . rand(99,1000) . "." . $ext;
                    //$final_attachment = $user_id . "_" . $current_month . "_" . $current_year . "_" . $i . "_" . rand(99,1000) . "_" . $attachment;

                    // check's valid format
                    //if(in_array($ext, $valid_extensions)) 
                    //{ 
                    $target_dir = $target_dir.strtolower($final_attachment); 

                    if(move_uploaded_file($tmp, $target_dir)) 
                    {
                        if($height!=="") {
                            $sql = "UPDATE  tbl_users set `h_height`='$height' WHERE user_id=$user_id";
                            $mysqli->query($sql);
                        }
//echo "SELECT * FROM `tbl_health` WHERE user_id=$user_id AND `health_month`='$current_month' and `health_year`='$current_year'";exit;
                        $rslt2 = $mysqli->query("SELECT * FROM `tbl_health` WHERE user_id=$user_id AND `health_month`='$current_month' and `health_year`='$current_year'");

                        if($rslt2->num_rows > 0) {
                            $row = $rslt2->fetch_assoc();

                            if (!empty($row['lab_result'])) { /* File already exists */
                                $existing_result = $row['lab_result'];
                                $lab_result = "$existing_result,$final_attachment";

                                $rslt3 = $mysqli->query("UPDATE `tbl_health` SET weight='" . addslashes($weight) . "', bp='" . addslashes($bp) . "', pulse='" . addslashes($pulse) . "', random_blood_sugar='" . addslashes($random_blood_sugar) . "', lipid_profile='" . addslashes($lipid_profile) . "', uric_acid='" . addslashes($uric_acid) . "', hba1c='" . addslashes($hba1c) . "', lab_result='" . addslashes($lab_result) . "', remarks='" . addslashes($remarks) . "', add_user='" . addslashes($user_id) . "' WHERE user_id=" . addslashes($user_id) . " and health_month='" . addslashes($current_month) . "' and health_year='" . addslashes($current_year) . "'");

                            }
                            else { /* No existing file found */
                                $lab_result = "$final_attachment";

                                $rslt3 = $mysqli->query("UPDATE `tbl_health` SET weight='" . addslashes($weight) . "', bp='" . addslashes($bp) . "', pulse='" . addslashes($pulse) . "', random_blood_sugar='" . addslashes($random_blood_sugar) . "', lipid_profile='" . addslashes($lipid_profile) . "', uric_acid='" . addslashes($uric_acid) . "', hba1c='" . addslashes($hba1c) . "', lab_result='" . addslashes($lab_result) . "', remarks='" . addslashes($remarks) . "', add_user='" . addslashes($user_id) . "' WHERE user_id=" . addslashes($user_id) . " and health_month='" . addslashes($current_month) . "' and health_year='" . addslashes($current_year) . "'");
                            }
                        }
                        else {
                            //echo "No record found";
                            $lab_result = "$final_attachment";
                            $rslt3 = $mysqli->query("INSERT INTO `tbl_health`( `user_id`, `weight`, `bp`, `pulse`, `random_blood_sugar`, `lipid_profile`, `uric_acid`, `hba1c`, `lab_result`, `health_year`, `health_month`, `remarks`, `add_user`) VALUES (" . addslashes($user_id) . ", '" . addslashes($weight) . "', '" . addslashes($bp) . "', '" . addslashes($pulse) . "', '" . addslashes($random_blood_sugar) . "', '" . addslashes($lipid_profile) . "', '" . addslashes($uric_acid) . "', '" . addslashes($hba1c) . "', '" . addslashes($lab_result) . "', '" . addslashes($current_year) . "', '" . addslashes($current_month) . "', '" . addslashes($remarks) . "', '" . addslashes($user_id) . "')");
                        }
                        
                        if(isset($_GET['type'])) {
                            $type = $_GET['type'];

                            if($type == "img") {
                                updateSuccess("",$current_month,$current_year);
                            }
                            else {
                                updateSuccess("Successfully updated the health details",$current_month,$current_year);
                            }
                        }
                        else {
                            updateSuccess("Successfully updated the health details",$current_month,$current_year);
                        }
                    }
                    else {
                        updateFail("File was not uploaded",$current_month,$current_year);
                    }
                    //} 
                }

                else { /* No file submited */
                    if($height!=="") {
                        $sql = "UPDATE  tbl_users set `h_height`='$height' WHERE user_id=$user_id";
                        $mysqli->query($sql);
                    }

                    $rslt3 = $mysqli->query("UPDATE `tbl_health` SET weight='" . addslashes($weight) . "', bp='" . addslashes($bp) . "', pulse='" . addslashes($pulse) . "', random_blood_sugar='" . addslashes($random_blood_sugar) . "', lipid_profile='" . addslashes($lipid_profile) . "', uric_acid='" . addslashes($uric_acid) . "', hba1c='" . addslashes($hba1c) . "', remarks='" . addslashes($remarks) . "', add_user='" . addslashes($user_id) . "' WHERE user_id=" . addslashes($user_id) . " and health_month='" . addslashes($current_month) . "' and health_year='" . addslashes($current_year) . "'");

                    updateSuccess("Successfully updated the health details",$current_month,$current_year);
                }
            }


        }

        if($errorimg > 0){
            updateFail("An error occurred while uploading the file",$current_month,$current_year);
//            die('<div class="alert alert-danger" role="alert"> An error occurred while uploading the file </div>');
        }

        //if($myFile['size'] > 500000){
        //    die('<div class="alert alert-danger" role="alert"> File is too big </div>');
        //}

    }

//    if($fileUploadStatus == 1){
//        updateSuccess("Successfully updated the health details");
//    }
//    else if($fileUploadStatus == 0){
//        updateFail("File was not uploaded");
//    }
    

}









function updateSuccess($message,$current_month,$current_year) {
    global $user_id;
//    global $current_month;
//    global $current_year;
      $month=date("M",strtotime("01-$current_month-$current_year"));
      $curmonth_year = $month."-".$current_year;
    global $mysqli;
//    $current_month = date("M",strtotime("01-$current_month-$current_year"));
    
//    if(empty($message)) {
//        $message = "Successfully updated the health details";
//    }
    
    $rslt = $mysqli->query("select * from tbl_health WHERE user_id=$user_id and health_month='$current_month' and health_year='$current_year'");
    $row_data = $rslt->fetch_assoc();


    $rslt2 = $mysqli->query("select * from tbl_users WHERE user_id=$user_id");
    $row_user = $rslt2->fetch_assoc();

    $bp = $height=$weight=$remarks="";

    if (!empty($row_data['bp'])) {
        $bp = $row_data['bp'];
    } 

    if (!empty($row_data['pulse'])) {
        $pulse = $row_data['pulse'];
    } 

    if (!empty($row_user['user_height'])) {
        $height = $row_user['user_height'];
    }

    if (!empty($row_user['h_height'])) {
        $height = $row_user['h_height'];
    }

    if (!empty($row_data['weight'])) {
        $weight = $row_data['weight'];
    }

    if (!empty($row_data['random_blood_sugar'])) {
        $random_blood_sugar = $row_data['random_blood_sugar'];
    }

    if (!empty($row_data['lipid_profile'])) {
        $lipid_profile = $row_data['lipid_profile'];
    }

    if (!empty($row_data['uric_acid'])) {
        $uric_acid = $row_data['uric_acid'];
    }

    if (!empty($row_data['hba1c'])) {
        $hba1c = $row_data['hba1c'];
    }

    $images = "";

    if (!empty($row_data['lab_result'])) {
        $lab_result_string = $row_data['lab_result'];
        $lab_result = explode(",", $lab_result_string);

        $total = count($lab_result) - 1;

        $images = "<tr class='tableRow'>
    <td class='tableHead'>
    Uploaded Lab Results
    </td>
    <td class='tableBody'>";

        for( $i=0 ; $i <= $total ; $i++ ) {
            $ext = strtolower(pathinfo($lab_result[$i], PATHINFO_EXTENSION));
            if($ext == 'pdf' || $ext == 'PDF') {
                $filePath = "upload/lab_results/" . $lab_result[$i];
                $fileIcon = "images/pdf_icon.png";
            }
            else {
                $filePath = $fileIcon = "upload/lab_results/" . $lab_result[$i];
            }

            $fullFileName = explode('_', $lab_result[$i]);
            $fileName = end($fullFileName);


            $images .= "<a class='fileLink' target='_blank' href='$filePath'>
        <div class='imgWrap'>
    <img class='labResult' src='$fileIcon' alt='$fileName'>
    <label class='deleteImg' for='labResult'></label>
    </div>
    </a>
    ";
        }

        $images .= "</td>
    </tr>";

        $fileRequired = "";
        $fileValidate = "";
    }
    else {
        $fileRequired = " required";
        $fileValidate = "lab_result: {
                            required: true
                        }";
    }

    if (!empty($row_data['remarks'])) {
        $remarks = $row_data['remarks'];
    }
    
    header('HTTP/1.1 200 OK');
    
    exit($formBody = "

<table class='headingWrap' style='border-collapse: collapse;' width='90%' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tr>
        <td align='center'>
            <font size='+1' color='#a6107b'><b>Please enter latest health details</b></font>
        </td>
    </tr>
</table>
<table class='responseMsg' style='border-collapse: collapse;' width='90%' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tr>
        <td align='center'>
            <font size='+1' color='green'><b>$message</b></font>
        </td>
    </tr>
</table>

<form class='healthEntryForm' id='healthEntryForm' method='post' enctype='multipart/form-data'>
<input type='hidden' name='h_user_id' id='h_user_id' value='$user_id'>
   <input type='hidden' name='curmonth_year' id='curmonth_year' value='$curmonth_year'> 
    <table width='70%' align='center' cellpadding='0' cellspacing='0'>
        <tbody>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Months &amp; Year
                </td>
                <td class='tableBody'>
                    $month  - $current_year
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Height(in cms) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='h_height' id='h_height' class='txt_box' value='$height' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Weight(in Kg) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='weight' id='weight' class='txt_box' value='$weight' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Blood Pressure (mmHg) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='bp' id='bp' class='txt_box' value='$bp' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Pulse (Beats / min) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='pulse' id='pulse' class='txt_box' value='$pulse' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Random Blood Sugar (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='random_blood_sugar' id='bp' class='txt_box' value='$random_blood_sugar' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Lipid Profile (Cholesterol) (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='lipid_profile' id='lipid_profile' class='txt_box' value='$lipid_profile' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Uric Acid (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='uric_acid' id='uric_acid' class='txt_box' value='$uric_acid' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    HBA1c (mg/dl) <span style='color: red'>(optional)</span>
                </td>
                <td class='tableBody'>
                    <input name='hba1c' id='hba1c' class='txt_box' value='$hba1c'>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Upload Lab Results <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input type='file' name='lab_result[]' id='lab_result' class='stayInline txt_box' multiple$fileRequired>
                    <span class='hint stayInline'>To upload multiple files at once, use <span class='hintHighlight'>'Ctrl + Left Click'</span> combination</span>
                </td>
            </tr>
            $images
            <tr class='tableRow'>
                <td class='tableHead'>
                    If any health issues please mention &nbsp;
                </td>
                <td class='tableBody'>
                    <textarea style='overflow:auto;'  name='remarks' id='remarks' cols='40' class='txt_box' rows='4'>$remarks</textarea>
                </td>
            </tr>

            <tr class='tableRow'>
                <td class='tableHead'>&nbsp;</td>
                <td class='tableBody'>
                    <input  type='submit' name='submit' id='submit' value='Submit'>
                    <input type='reset' value='Cancel'>
                </td>
            </tr>
        </tbody>
    </table>

</form>

    ");

}

function updateFail($message,$current_month,$current_year) {
    global $user_id;
//    global $current_month;
//    global $current_year;
//    global $month;
    $month=date("M",strtotime("01-$current_month-$current_year"));
    $curmonth_year = $month."-".$current_year;
    global $mysqli;
    
    if(empty($message)) {
        $message = "File couldn't be uploaded";
    }
    
    $rslt = $mysqli->query("select * from tbl_health WHERE user_id=$user_id and health_month='$current_month' and health_year='$current_year'");
    $row_data = $rslt->fetch_assoc();


    $rslt2 = $mysqli->query("select * from tbl_users WHERE user_id=$user_id");
    $row_user = $rslt2->fetch_assoc();

    $bp = $height=$weight=$remarks="";

    if (!empty($row_data['bp'])) {
        $bp = $row_data['bp'];
    } 

    if (!empty($row_data['pulse'])) {
        $pulse = $row_data['pulse'];
    } 

    if (!empty($row_user['user_height'])) {
        $height = $row_user['user_height'];
    }

    if (!empty($row_user['h_height'])) {
        $height = $row_user['h_height'];
    }

    if (!empty($row_data['weight'])) {
        $weight = $row_data['weight'];
    }

    if (!empty($row_data['random_blood_sugar'])) {
        $random_blood_sugar = $row_data['random_blood_sugar'];
    }

    if (!empty($row_data['lipid_profile'])) {
        $lipid_profile = $row_data['lipid_profile'];
    }

    if (!empty($row_data['uric_acid'])) {
        $uric_acid = $row_data['uric_acid'];
    }

    if (!empty($row_data['hba1c'])) {
        $hba1c = $row_data['hba1c'];
    }

    $images = "";

    if (!empty($row_data['lab_result'])) {
        $lab_result_string = $row_data['lab_result'];
        $lab_result = explode(",", $lab_result_string);

        $total = count($lab_result) - 1;

        $images = "<tr class='tableRow'>
    <td class='tableHead'>
    Uploaded Lab Results
    </td>
    <td class='tableBody'>";

        for( $i=0 ; $i <= $total ; $i++ ) {
            $ext = strtolower(pathinfo($lab_result[$i], PATHINFO_EXTENSION));
            if($ext == 'pdf' || $ext == 'PDF') {
                $filePath = "upload/lab_results/" . $lab_result[$i];
                $fileIcon = "images/pdf_icon.png";
            }
            else {
                $filePath = $fileIcon = "upload/lab_results/" . $lab_result[$i];
            }

            $fullFileName = explode('_', $lab_result[$i]);
            $fileName = end($fullFileName);


            $images .= "<a class='fileLink' target='_blank' href='$filePath'>
        <div class='imgWrap'>
    <img class='labResult' src='$fileIcon' alt='$fileName'>
    <label class='deleteImg' for='labResult'></label>
    </div>
    </a>
    ";
        }

        $images .= "</td>
    </tr>";

        $fileRequired = "";
        $fileValidate = "";
    }
    else {
        $fileRequired = " required";
        $fileValidate = "lab_result: {
                            required: true
                        }";
    }

    if (!empty($row_data['remarks'])) {
        $remarks = $row_data['remarks'];
    }

    header('HTTP/1.1 400 Bad Request');
    
    exit($formBody = "

<table class='headingWrap' style='border-collapse: collapse;' width='90%' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tr>
        <td align='center'>
            <font size='+1' color='#a6107b'><b>Please enter latest health details</b></font>
        </td>
    </tr>
</table>
<table class='responseMsg' style='border-collapse: collapse;' width='90%' align='center' border='0' cellpadding='0' cellspacing='0'>
    <tr>
        <td align='center'>
            <font size='+1' color='red'><b>$message</b></font>
        </td>
    </tr>
</table>

<form class='healthEntryForm' id='healthEntryForm' method='post' enctype='multipart/form-data'>
<input type='hidden' name='h_user_id' id='h_user_id' value='$user_id'>
     <input type='hidden' name='curmonth_year' id='curmonth_year' value='$curmonth_year'> 
    <table width='70%' align='center' cellpadding='0' cellspacing='0'>
        <tbody>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Month &amp; Year
                </td>
                <td class='tableBody'>
                    $month - $current_year
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Height(in cms) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='h_height' id='h_height' class='txt_box' value='$height' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Weight(in Kg) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='weight' id='weight' class='txt_box' value='$weight' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Blood Pressure (mmHg) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='bp' id='bp' class='txt_box' value='$bp' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Pulse (Beats / min) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='pulse' id='pulse' class='txt_box' value='$pulse' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Random Blood Sugar (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='random_blood_sugar' id='bp' class='txt_box' value='$random_blood_sugar' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Lipid Profile (Cholesterol) (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='lipid_profile' id='lipid_profile' class='txt_box' value='$lipid_profile' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Uric Acid (mg/dl) <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input name='uric_acid' id='uric_acid' class='txt_box' value='$uric_acid' required>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    HBA1c (mg/dl) <span style='color: red'>(optional)</span>
                </td>
                <td class='tableBody'>
                    <input name='hba1c' id='hba1c' class='txt_box' value='$hba1c'>
                </td>
            </tr>
            <tr class='tableRow'>
                <td class='tableHead'>
                    Upload Lab Results <span class='required'><sup>*</sup></span>
                </td>
                <td class='tableBody'>
                    <input type='file' name='lab_result[]' id='lab_result' class='stayInline txt_box' multiple$fileRequired>
                    <span class='hint stayInline'>To upload multiple files at once, use <span class='hintHighlight'>'Ctrl + Left Click'</span> combination</span>
                </td>
            </tr>
            $images
            <tr class='tableRow'>
                <td class='tableHead'>
                    If any health issues please mention &nbsp;
                </td>
                <td class='tableBody'>
                    <textarea style='overflow:auto;'  name='remarks' id='remarks' cols='40' class='txt_box' rows='4'>$remarks</textarea>
                </td>
            </tr>

            <tr class='tableRow'>
                <td class='tableHead'>&nbsp;</td>
                <td class='tableBody'>
                    <input  type='submit' name='submit' id='submit' value='Submit'>
                    <input type='reset' value='Cancel'>
                </td>
            </tr>
        </tbody>
    </table>

</form>

    ");

}

?>