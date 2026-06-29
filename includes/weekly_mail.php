<?php
include("includes/connect.inc.php");


$day= date('j');
$month= date('n');
$year= date('Y');


$week=3;

$sql_emp="select short_name,
user_id,
if(email !='',email,personal_email) as email_id
from tbl_users 
where u.status='Active'
and is_regular=1
and is_erp=1
and emp_category in(1,2,3,18,20)
and user_id not in(1000,166,162,164,181,126,120,182)";

echo $sql_emp;

$result_emp=$mysqli->query($sql_emp);


while($row_emp=$result_emp->fetch_assoc())
{


$sql="select m.select_week,
m.taskname,m.description from 
tbl_monthly_plan_details m
where select_week=$week 
and user_id=".$row_emp['user_id']." and plan_month='$month' and plan_year='$year'";

$result=$mysqli->query($sql);

 
$body="<p><b>Dear Mr. ".$row_emp['short_name'].", Pls find your coming
week's plan</b>..</p>

<table  width=100%>
  <tr style='font-size: 1.1em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;'>
	<th  style=' font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;'>Week</th>
    
    <th  style=' font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;'>Taskname</th>
	
    <th  style=' font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;'>Description</th>
      
            	  </tr>";
				  
                  
while($row=$result->fetch_assoc())
		{
         
$body.="<tr >
  <td style='color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;'><strong>".$row['select_week']."</strong></td>
	 <td style='color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;' ><strong>".$row['taskname']."</strong></td>
    <td style='color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;'><strong>". $row['description']."</strong></td>
    

  </tr>";
  
 
		}
	
$body.="</table>";
	
	$subject = 'Weekly Plan Reminder';
	$headers = "From:  mail@effism.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Cc:software@ariesgroup.ae\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//mail($row_emp['email_id'],$subject, $body, $headers);

}

//echo $body;
?>