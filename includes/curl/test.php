<?php

ini_set('display_errors', '1');
$con = new mysqli("p:" . "localhost", "effism_eff", "{-Ea},fuEV{_", "effism_efficiency");
//$con = new mysqli("p:"."localhost","root","","effism_efficiency");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
$sql = "SELECT * FROM tbl_users WHERE username REGEXP '^[0-9]+$' AND CHAR_LENGTH(username)<=4";
$sql="SELECT * FROM `tbl_users` WHERE `username` LIKE '%AG0000%'";
 $user_result = mysqli_query($con, $sql);
 while($row = mysqli_fetch_assoc($user_result)) {
	 $username = $row['username'];
	 $username = str_replace('AG0000','',$username);
	 $username = str_replace('AES ','',$username);
	 $username = trim($username);
	 $sql = " UPDATE tbl_users SET username='$username' WHERE user_id=".$row['user_id'];
	 mysqli_query($con,$sql);
 }
?>