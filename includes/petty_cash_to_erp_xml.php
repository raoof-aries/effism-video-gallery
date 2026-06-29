<?php
header("Content-Type: application/rss+xml; charset=ISO-8859-1");

//mysql_connect('localhost',root,'');
$con = new mysqli("p:"."localhost","effism_live","4G&L6b^GFQNB!U-WT)","effism_live");
//$con =new mysqli("p:"."localhost", "root", "", "eff_final");
//mysql_select_db('eff_final');

$claim = $_REQUEST['claim'];

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error();
}

/* $sql = "SELECT c.id,u.hr_id,c.entrydate,c.claimstatus
  FROM tbl_claim c LEFT JOIN tbl_users u ON u.user_id=c.userid
  WHERE c.updated_to_erp = 0"; */
$sql = "SELECT c.id,u.hr_id requested,u1.hr_id approved,c.entrydate, c.total_amount
			FROM tbl_claim c LEFT JOIN tbl_claim_approve ap ON c.id=ap.claim_id
                        LEFT JOIN tbl_users u ON u.user_id=c.userid
                        LEFT JOIN tbl_users u1 ON u1.user_id=ap.approved_user
				WHERE c.updated_to_erp = 0 AND c.claimstatus = 4";
$result = mysqli_query($con,$sql);
$str = array();
$str = '<?xml version="1.0" encoding="ISO-8859-1"?>
<rss version="2.0">
		<channel><ROOT>';

$detail = array();
$detail = '<?xml version="1.0" encoding="ISO-8859-1"?>
	<rss version="2.0">
		<channel><ROOT>';

while ($row = mysqli_fetch_assoc($result)) {
    $str .= '<ROW_DATA>
	<ClaimId>'.$row['id'].'</ClaimId>
	<EmployeeId>'.$row['requested'].'</EmployeeId>
	<ApprovedId>'.$row['approved'].'</ApprovedId>
	<EntryDate>'.$row['entrydate'].'</EntryDate>
	<TotalAmount>'.$row['total_amount'].'</TotalAmount>
    		</ROW_DATA>';

    /* 	$sql = "SELECT id,claimId,title,date1,category,subcategory,division_id,subdivision_id,jobno,amount,currency
      FROM tbl_claimdetails
      WHERE claimId = '".$row['id']."' AND updated_to_erp = 0"; */
    $sql = "SELECT id, claimId, title, purposedate, category, subcategory, division_id, subdivision_id, jobno, amount, currency
				FROM tbl_claimdetails
					WHERE claimId = '".$row['id']."' AND status = 1";
    $result_detail = mysqli_query($con,$sql);

    while ($row1 = mysqli_fetch_assoc($result_detail)) {

        $detail .= '<ROW_DATA>
			<ClaimId>'.$row1['claimId'].'</ClaimId>
			<Title>'.htmlentities($row1['title']).'</Title>
			<Date>'.$row1['purposedate'].'</Date>
			<Category>'.$row1['category'].'</Category>
			<SubCategory>'.$row1['subcategory'].'</SubCategory>
		    <DivisionId>'.$row1['division_id'].'</DivisionId>
		    <SubDivisionId>'.$row1['subdivision_id'].'</SubDivisionId>
			<JobNo>'.$row1['jobno'].'</JobNo>
			<Amount>'.$row1['amount'].'</Amount>
		    <Currency>'.$row1['currency'].'</Currency>
		    		</ROW_DATA>';
    }
    $sql = "UPDATE tbl_claim SET updated_to_erp = 1
					WHERE id = '".$row['id']."'";
    //mysqli_query($con,$sql);
}

$str .= '</ROOT></channel></rss>';

$detail .= '</ROOT></channel></rss>';

if ($claim == 'HEADER')
    print_r($str);
elseif ($claim == 'DETAIL')
    print_r($detail);

if ($claim == 'LOCK') {
    $sql = "SELECT c.id,u.hr_id requested,u1.hr_id approved,c.entrydate, c.total_amount
			FROM tbl_claim c LEFT JOIN tbl_claim_approve ap ON c.id=ap.claim_id
                        LEFT JOIN tbl_users u ON u.user_id=c.userid
                        LEFT JOIN tbl_users u1 ON u1.user_id=ap.approved_user
				WHERE c.updated_to_erp = 0 AND c.claimstatus = 4";
    $result = mysqli_query($con,$sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $sql = "UPDATE tbl_claim SET updated_to_erp = 1
					WHERE id = '".$row['id']."'";
        mysqli_query($con,$sql);
    }
}







/*$a[0] = $arr;
$a[0] = $arr;
$a[1] = $brr;
print_r($a);

/*	$arr['Title'][] = $row1['title'];
		$arr['Date'][] = $row1['date1'];
		$arr['Category'][] = $row1['category'];
		$arr['SubCategory'][] = $row1['subcategory'];
		$arr['DivisionId'][] = $row1['division_id'];
		$arr['SubDivisionId'][] = $row1['subdivision_id'];
		$arr['JobNo'][] = $row1['jobno'];
		$arr['Amount'][] = $row1['amount'];
		$arr['Currency'][] = $row1['currency'];
		$arr[] = array("Title"=>$row1['title'],"Date"=>$row1['date1'],"Category"=>$row1['category'],"SubCategory"=>$row1['subcategory'],
				"DivisionId"=>$row1['division_id'],
				"SubDivisionId"=>$row1['subdivision_id'],
				"JobNo"=>$row1['jobno'],
				"Amount"=>$row1['amount'],
				"Currency"=>$row1['currency']);
		$brr[] = array("Title"=>$row1['title'],"Date"=>$row1['date1'],"Category"=>$row1['category'],"SubCategory"=>$row1['subcategory'],
				"DivisionId"=>$row1['division_id'],
				"SubDivisionId"=>$row1['subdivision_id'],
				"JobNo"=>$row1['jobno'],
				"Amount"=>$row1['amount'],
				"Currency"=>$row1['currency']); */
?>