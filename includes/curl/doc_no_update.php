<?php 
ini_set('display_errors', '1');
//mysql_connect('localhost',root,'');
//$con =new mysqli("p:"."localhost", "effism_eff", "{-Ea},fuEV{_", "effism_efficiency");
//$con =new mysqli("p:"."localhost", "effismuser_efficiency", "{ebx+MnNkPG0", "effismuser_live");
$con = new mysqli("p:"."localhost", "effism_live", "4G&L6b^GFQNB!U-WT)", "effism_live");
//$con =new mysqli("p:"."localhost", "root", "", "effism_efficiency");
//mysql_select_db('ames');
// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
extract($_REQUEST);
if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='ADD') {

	$insert_sql = "insert ignore into 0_documents(document_code,document_no,document_date,document_type,title,job_no,prepared_by,project_incharge,revision,class_revision,description,is_current,revision_type,next_followup_date)
    	 values('$document_code','$document_no','$document_date','$document_type','$title','$job_no','$prepared_by',
    '$project_incharge','$revision','$class_revision','" . addslashes($description) . "',1,'$revision_type','$next_followup_date')";
	mysqli_query($con, $insert_sql);
}
else if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='REVISION') {
	$update_sql = "update ignore 0_documents set is_current=0 where document_id='$document_id'";
	mysqli_query($con, $update_sql);
	$insert_sql = "insert ignore into 0_documents(document_code,document_no,document_date,document_type,title,job_no,prepared_by,project_incharge,revision,class_revision,description,is_current,revision_type,next_followup_date)
    	 values('$document_code','$document_no','$document_date','$document_type','$title','$job_no','$prepared_by',
    '$project_incharge','$revision','$class_revision','" . addslashes($description) . "',1,'$revision_type','$next_followup_date')";
	mysqli_query($con, $insert_sql);
}
else if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='DELETE') {
	extract($_REQUEST);
		$update_sql = "delete from 0_documents where document_no = '$document_no'
		AND job_no='$job_no'";
		mysqli_query($con, $update_sql);
		if($is_current==1) {
		$sql = 'select document_id from 0_documents 
		where document_no="'.$document_no.'"
		and job_no="'.$job.'" 
		order by document_id desc limit 1';
		$res = mysqli_query($sql);
		$rs = $res->fetch_assoc($res);
		$docno = $rs['document_id'];
		$sql_up = 'update 0_documents
		set is_current=1 
		where document_id='.$docno;
		mysqli_query($sql_up);
		}

}
else if(isset($_REQUEST['doAction']) && $_REQUEST['doAction']=='ADD_JOBNO') {
	extract($_REQUEST);
	$sql = "SELECT id FROM tbl_job WHERE job_no='".$job_no."'";
	$res = mysqli_query($con,$sql);
	$rs = $res->fetch_assoc();

	 $sql_com = "SELECT id FROM tbl_dimensions WHERE hr_dimension_id='".$company_id."'";
	$res_com = mysqli_query($con,$sql_com);
	$rs_com = $res_com->fetch_assoc();
	$company_id = $rs_com['id'];

	$sql_com = "SELECT id FROM tbl_dimensions WHERE hr_dimension_id='".$division_id."'";
	$res_com = mysqli_query($con,$sql_com);
	$rs_com = $res_com->fetch_assoc();
	$division_id = $rs_com['id'];

	$sql_com = "SELECT id FROM tbl_dimensions WHERE hr_dimension_id='".$sub_division_id."'";
	$res_com = mysqli_query($con,$sql_com);
	$rs_com = $res_com->fetch_assoc();
	$sub_division_id = $rs_com['id'];



	if(empty($rs['id'])) {
		 print $sql_insert = "INSERT INTO tbl_job (date, job_no, description,vessel_name, client, company_id, division_id, sub_division_id,added_date)
		VALUES('".$date."','".$job_no."','".addslashes($description)."','".$vessel_name."','".$client."','".$company_id."','".$division_id."',
		'".$sub_division_id."','".date('Y-m-d H:i:s')."')";
		mysqli_query($con,$sql_insert);
		$job_id =  mysqli_insert_id($con);

		$sql_ins_so = "INSERT INTO tbl_so_number(job_id,sales_order_no,added_date)
		VALUES('".$job_id."','0000','".gmdate('Y-m-d H:i:s')."')";
		mysqli_query($con,$sql_ins_so);
	}
	else {
		print $sql_upd = "UPDATE tbl_job
		SET date='".$date."',
		description='".addslashes($description)."',
		vessel_name='".addslashes($vessel_name)."',
		client='".addslashes($client)."',
		company_id='".addslashes($company_id)."',
		division_id='".addslashes($division_id)."',
		sub_division_id='".addslashes($sub_division_id)."'
		WHERE job_no='".$job_no."'";
		mysqli_query($con,$sql_upd);
	}
}
?>			