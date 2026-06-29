<?php
session_start();
if(!isset($_SESSION['user_id']))
header("location:index.php");
include("includes/connect.inc.php");

/*$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '1'";

$companies = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '2'";

$divisions = $mysqli->query($sql);

$sql = "SELECT id,short_name FROM tbl_dimensions WHERE is_active = '1' AND dimension_type = '3'";

$sub_divisions = $mysqli->query($sql);*/
//$arrdate = explode('-',$_REQUEST['date']);
$date = $_REQUEST['date'];
######################################################### ADD JOB NO###########################################
if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='ADD_JOB') {

	//print_r($_REQUEST);
	$_REQUEST['division_id']=3;
	//document code
	if(!empty($_REQUEST['lstDoc'])) {
	$sql_code = "SELECT document_no document_code FROM 0_documents WHERE document_id=".$_REQUEST['lstDoc'];
	$rslt_doc = $mysqli->query($sql_code);
	$row_doc = $rslt_doc->fetch_assoc();
	}
	else {
		$row_doc['document_code']="";
	}
	$sql_sel = "SELECT MAX(row_id) row_id FROM tbl_workreports WHERE user_id=".$_SESSION['user_id']." AND date_report='".$_REQUEST['date']."'";
	$rslt_row = $mysqli->query($sql_sel);
	$row_data = $rslt_row->fetch_assoc(); 
	//print_r($row_data);
	if(empty($row_data['row_id']) || $row_data['row_id']=='NULL') $row_data['row_id']=0;
	 $next_row = $row_data['row_id']+1;
	 $task = "";
	 if(!empty($_REQUEST['log_vessel_name'])) {
		 $task .= $_REQUEST['log_vessel_name']."_";
	 }
	 if(!empty($_REQUEST['hddocnodescription'])) {
		 $task .= $_REQUEST['hddocnodescription']."_";
	 }
	 if(!empty($_REQUEST['txtTask'])) {
		 $task .= $_REQUEST['txtTask'];
	 }
	 
	  $sql_ins = "INSERT INTO tbl_workreports (row_id, taskname, description, client, date_report, user_id, main_type, job_no,  is_current,document_id,so_order_id)
	VALUES('".addslashes($next_row)."',
	'".addslashes($task)."',
	'".addslashes("Document Code:-".$row_doc['document_code']." ".$_REQUEST['hdsonodescription']."-".$_REQUEST['log_description'])."',
	'".addslashes($_REQUEST['log_client'])."',
	'".addslashes($_REQUEST['date'])."',
	'".addslashes($_SESSION['user_id'])."',
	'".addslashes(1)."',
	'".addslashes($_REQUEST['lstJobno'])."',
	'".addslashes(1)."',
	'".addslashes($_REQUEST['lstDoc'])."',
	'".addslashes($_REQUEST['so_order_id'])."'
	)";
	$mysqli->query($sql_ins);
	$job_id = $mysqli->insert_id;
	//$arrdate = explode('-',$_REQUEST['date']);
$date = $_REQUEST['date'];
?><script>window.opener.location.reload();
window.close();
	</script><?php
	//header('location:add_job_to_daily_log.php?date='.$date);
}
###################################################################################################################

$sql = "SELECT CONCAT_WS('-',j.job_no,n.sales_order_no) job_no FROM tbl_job j
LEFT JOIN tbl_so_number n
ON j.id = n.job_id 
WHERE j.division_id=3 AND j.date>='2019-01-01' ORDER BY j.date DESC";
$jobs = $mysqli->query($sql);
$mysqli->close();
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta name="jobdiary" content="Marine BizTV jobdiary">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<title>Online Job Diary - User Profile</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="css/colorbox.css" />

<script type="text/javascript" src="javascript/js.js"></script>
<script src="js/jquery.js" type="text/javascript"></script>

		<script src="js/jquery.alerts.js" type="text/javascript"></script>

		<link href="css/jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" />
         <link rel='stylesheet' type='text/css' href='js/chosen/1.1.0/chosen.css'/>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="background-image:url(images/themes/<?php	echo rand(5,17); ?>.jpg); background-repeat:repeat-x;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
   


<script type="text/javascript" src="js/popup.js"></script>
<script type="text/javascript" src="./new_design/js/header.js"></script>
<style>
    /* Menu Dropdown */
    .cf:before,.cf:after {content: " ";display: table;}.cf:after { clear: both;}.cf {*zoom: 1;}
    .menu,.submenu {margin: 0;padding: 0;list-style: none;}
    .menu {  margin: 1px auto;min-width: 100px;
             width: -moz-fit-content;width: -webkit-fit-content;width: fit-content; }
    .menu > li { background: #34495e;float: left;position: relative;
                 /*transform: skewX(25deg);*/
    }
    .menu a { display: block;color: #fff;text-transform: uppercase;
              text-decoration: none;font-family: Arial, Helvetica; font-size: 12px;font-weight:bold;}
    .menu li:hover { background: #e74c3c;}
    .menu > li > a {
        /*transform: skewX(-25deg);*/
        padding: 7px 27.5px; border-right:1px solid #ffffff;  }
    /* Dropdown */
    .submenu {position: absolute; width: 200px; left: 50%; margin-left: -100px;
              /*transform: skewX(-25deg);*/
              transform-origin: left top;}
    .submenu li {  background-color: #34495e;position: relative;overflow: hidden;  }
    .submenu > li > a {padding: 7px 10px;*padding: 10px 6px; }
    .submenu > li::after { content: '';  position: absolute;top: -125%;height: 100%;width: 100%;  box-shadow: 0 0 50px rgba(0, 0, 0, .9); }
    /*.submenu > li:nth-child(odd){ transform: skewX(-25deg) translateX(0); }*/
    /*.submenu > li:nth-child(odd) > a {transform: skewX(25deg);}*/
    .submenu > li:nth-child(odd)::after { right: -50%;
                                          /*transform: skewX(-25deg) rotate(3deg);*/
    }
    /*.submenu > li:nth-child(even){ transform: skewX(25deg) translateX(0); }*/
    /*.submenu > li:nth-child(even) > a {transform: skewX(-25deg); }*/
    .submenu > li:nth-child(even)::after {left: -50%;
                                          /*       //transform: skewX(25deg) rotate(3deg); */
    }
    /* Show dropdown */
    .submenu,.submenu li { opacity: 0;z-index:10000; visibility: hidden; border-bottom:1px solid #ffffff;  }
    .submenu{ margin-top:-5px;}
    .submenu li { transition: .2s ease transform;}
    .menu > li:hover .submenu,.menu > li:hover .submenu li {opacity: 1;visibility: visible;}
    .menu > li:hover .submenu li:nth-child(even){
        /*    //transform: skewX(25deg) translateX(15px); */
    }
    .menu > li:hover .submenu li:nth-child(odd){
        /*    //transform: skewX(-25deg) translateX(-15px); */
    }
    #topmenu td{
        background:none !important;
    }
    #topmenu td:hover{
        background:#e74c3c !important;
    }
</style>

<style type="text/css">
    #topmenu td {
        background: #008080;
        padding: 3px 3px;
        position: relative;
        z-index: 1;
        border:1px solid #fff;
        color:#fff;
        font-weight:bold;
    }
    #topmenu{
        border-collapse: separate;
        border-spacing: 3px;
        position: absolute;
        right: 10px;
        width: 40px;
        top:-3px;
    }
    #topmenu td a{
        color:#fff;
    }
    #tbl td{
        text-align: left;
        font-family:sans-serif;
    }
    #head_menu a{
        padding:6px;
    }
</style>
<tr><td>&nbsp;</td></tr>
        <tr><td><?php
	$must_field = "<strong style='color:red;'>*</strong>";
	?><form name="frm" action="" enctype="multipart/form-data" method="post" >
	    <table width="94%" align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse;border:2px solid #008a8a"> 
                    <tr height="30"><td bgcolor="#008a8a" style="border:#ffffff solid 1px;color:#ffffff">
                            <strong style="font-size:15px;margin-left:5px;">ADD JOB</strong>&nbsp;</td></tr>
 
                    <?php  ?>
                    <tr>
                        <td align="center" colspan="2">
                    <br>
		<table style="background: #ffffff;border: 5px solid #ccc;;padding: 10px;" width="50%" border="0" align="center">	
			<tr>
				<td colspan="2" height="5"></td>
			</tr>
			<tr>
				<td align="left" width="48%"><b>Task name</b></td><td align="left"><input required type="text" name="txtTask" value="<?php echo $_REQUEST['txtTask']; ?>" class="txt_box"/></td>
				
			</tr>
			<tr>
				<td align="left" width="48%"><b>Job No:</b></td><td align="left">
					<select required name='lstJobno' id='lstJobno' class="chosen-select-deselect" >
						<option value="">Select Job No</option>
						<?php
						while ($row = $jobs->fetch_assoc()) {
							?><option value='<?php print($row['job_no'])?>'><?php print($row['job_no'])?></option><?php
						}
						?>
					</select>
				</td>
				
			</tr>
			<tr id='description' style='display:none'>
				<td align="left" width="48%"><b>Job Description:</b></td><td align="left" id="jobdescription">
					
				</td>
				
			</tr>
			<tr id='vessel_name'>
				<td align="left" width="48%"><b>Vessel Name:</b></td><td align="left" id="vesselname">
					
				</td>
				
			</tr>
			
			<tr id='sodescription'>
				<td align="left" width="48%"><b>Description:</b></td><td align="left" id="sonodescription"
				style='font-weight:bold;color:red;font-size:13px;'>
					
				</td>
				
			</tr>
			
			<tr id='docList' style=''>
				<td align="left" width="48%"><b>Document No:</b></td><td align="left" id="docColum">
				<select required name='lstDoc' id='lstDoc' class='chosen-select-deselect' ></select>
				</td>
				
			</tr>
			<tr id='docdescription'>
				<td align="left" width="48%"><b>Description:</b></td><td align="left" id="docnodescription" style=''>
					
				</td>
				
			</tr>

			
			
			<tr>
            <td colspan="2" height="40" align="center">&nbsp;&nbsp;
			<input type="button" class="jobdiary_buttons" value="ADD JOB" name="ADD_JOB" onclick="frmValidation(this.form,this.name)">
			<input type="button" class="jobdiary_buttons" value="CLOSE" name="CLOSE" onclick="ref_and_close()">
			</td>
				<input type="hidden" name="log_description" id='log_description' value=""/>
				<input type="hidden" name="log_client" id='log_client' value=""/>
				<input type="hidden" name="log_vessel_name" id='log_vessel_name' value=""/>
				<input type="hidden" name="so_order_id" id='so_order_id' value=""/>
				<input type="hidden" name="hddocnodescription" id='hddocnodescription' value=""/>
				<input type="hidden" name="hdsonodescription" id='hdsonodescription' value=""/>
				<input type="hidden" name="doAction" value=""/>
				<input type="hidden" name="date" value="<?php print($date);?>"/>
			</tr>
		</table><br/>
		</td>
                    </tr>
                    <?php ?>
        </table>
	</form><?php

?></td>
</tr>


</table>


<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="js/chosen/1.7.0/chosen.jquery.js"></script>
<script type="text/javascript">

$('#lstJobno').change(
	function() {
		//alert($(this).val());
		var job_no = $(this).val();
		var formData = "job_no="+job_no;
//Name value Pair
//var formData = {job_no:$(this).val()}; //Array 
$.ajax({
url : "log_ajax.php",
type: "POST",
data : formData,
success: function(data, textStatus, jqXHR)
{
var jsonData = JSON.parse(data);
document.getElementById('docList').style.display="inline;";
document.getElementById('description').style.display="";
document.getElementById('jobdescription').innerHTML = jsonData.description;
document.getElementById('sonodescription').innerHTML = jsonData.remarks;
document.getElementById('hdsonodescription').value = jsonData.remarks;
document.getElementById('vesselname').innerHTML = jsonData.vessel_name;
document.getElementById('docnodescription').innerHTML ="";
//alert(jsonData.remarks);
//alert(jsonData.vessel_name);
document.getElementById('log_description').value = jsonData.description;
document.getElementById('log_client').value = jsonData.client;
document.getElementById('log_vessel_name').value = jsonData.vessel_name;
document.getElementById('so_order_id').value = jsonData.so_order_id;
document.getElementById('docColum').innerHTML = jsonData.doclist;
$('#lstDoc').chosen();
//alert(jsonData.doclist);
 //data - response from server
},
error: function (jqXHR, textStatus, errorThrown){
}
});
}
);

function changeDescription(document_id) {
	//alert(document_id);
	//var document_id = $(this).val();
		var formData = "document_id="+document_id;
		$.ajax({
			url : "log_ajax.php?doAction=DISPLAY_DOC",
			type: "POST",
			data : formData,
			success: function(data, textStatus, jqXHR)
			{
				var jsonData = JSON.parse(data);
				
document.getElementById('docnodescription').innerHTML = jsonData.description;
document.getElementById('hddocnodescription').value = jsonData.description;

			},
			error: function (jqXHR, textStatus, errorThrown){
			}
	});
}
$('#lstDoc').change(
	function() {
		alert($(this).val());
		var document_id = $(this).val();
		var formData = "document_id="+document_id;
		$.ajax({
			url : "log_ajax.php?doAction=DISPLAY_DOC",
			type: "POST",
			data : formData,
			success: function(data, textStatus, jqXHR)
			{
				var jsonData = JSON.parse(data);
				
document.getElementById('docnodescription').innerHTML = jsonData.description;
document.getElementById('hddocnodescription').value = jsonData.description;

			},
			error: function (jqXHR, textStatus, errorThrown){
			}
	});
}
);
var config = {
	'.chosen-select': {width: "100%"},
	'.chosen-select-deselect': {allow_single_deselect: true,width: "100%"},
	'.chosen-select-no-single': {disable_search_threshold: 10,width: "100%"},
	'.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
	'.chosen-select-width': {width: "100%"}
}
for (var selector in config) {
	$(selector).chosen(config[selector]);
}



</script>

<script language="javascript">
function ref_and_close()
  {         
    window.opener.location.reload();
    window.close();       
  }

function validate_profile()
{
	    
	if(frm.password1.value=="" && frm.password2.value!="")
	{
		alert("Please enter the Password");
		frm.password1.focus();
		return false;
	}
		
		
	if(frm.password1.value!="" && frm.password2.value=="")
	{
		alert("Please confirm the Password");
		frm.password2.focus();
		return false;
	}
	
	
	if(frm.password2.value!=frm.password1.value)
	{
		alert("Password does not match!");
		frm.password2.focus();
		return false;
	}
   

	return true;

}

/* Function for validate data*/ 
function frmValidation(frm1,fldVal) {
	var flagErr = false;
	if(frm1.txtTask.value =="" && frm1.hddocnodescription.value=="") {
		jAlert('Please enter the  task', 'Alert Dialog');
		flagErr = true;
		return false;
	}
	
	if(frm1.lstJobno.value =="") {
		jAlert('Please Select Job No', 'Alert Dialog');
		flagErr = true;
		return false;
	}
	
	
	if(!flagErr) {
		frm1.doAction.value = fldVal;
		frm1.submit();
	}
}

/*Function show and hide values*/
function showHide(value) {
	if(value=='M') {
		document.getElementById('spouse').style.display="";
		document.getElementById('children').style.display="";
	}
	else {
		document.getElementById('spouse').style.display="none";
		document.getElementById('children').style.display="none";
	}
}
</script>



                    
                   
                    <?php

                  
                    ?>

</body>
</html>
