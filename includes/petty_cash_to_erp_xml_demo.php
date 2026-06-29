<?php
$con = new mysqli("p:"."localhost","effism_eff","{-Ea},fuEV{_","effism_efficiency");
//$con =new mysqli("p:"."localhost", "root", "", "eff_final");
//mysql_select_db('eff_final');
// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: ".mysqli_connect_error();
}
$sql = "SELECT pc.pc_id AS id, pc.pc_userid requested, pc.pc_sendto approved, pc_addDate entrydate, pc_total total_amount
FROM tbl_pettycash pc
LEFT JOIN tbl_users u ON u.user_id = pc.pc_userid
LEFT JOIN tbl_users u1 ON u1.user_id = pc.pc_sendto
WHERE pc.updated_to_erp =0
AND pc.pc_status =4";
$result = mysqli_query($con,$sql);
$row = mysqli_fetch_assoc($result);
$i=0;


pr($row);exit;
//foreach($row as $row){
////    $sql="sql";
//    $sql1 = "SELECT pcd_id id, pcd_pc_id claimId, pcd_remark title, pcd_purposeDate purposedate, pcd_category category, pcd_subcategory subcategory, pcd_division division_id, pcd_subdivision subdivision_id, pcd_jobno jobno, pcd_amount amount, pc.pc_currency currency
//				FROM tbl_pettycashDetails LEFT JOIN tbl_pettycash pc ON pc.pc_id = pcd_pc_id
//					WHERE pcd_pc_id = '".$row['id']."'";
//    $result_detail = mysqli_query($con,$sql1);
////    echo $sql;exit;
//    pr(mysqli_fetch_assoc($result_detail));exit;
//    $row1[$i]=mysqli_fetch_assoc($result_detail);
//    $i++;
//    $sql = "UPDATE tbl_pettycash SET updated_to_erp = 1
//					WHERE pc_id = '".$row['id']."'";
//    mysqli_query($con,$sql);
//}
//if ($claim == 'HEADER')
//    print_r($str);
//elseif ($claim == 'DETAIL')
//    print_r($detail);
//print_r($row1);
   ?>