<?php
include("connect.inc.php");



function dimension_list($type) {
    global $mysqli;

    $sql = " SELECT d.id, d.short_name, d.full_name
             FROM tbl_dimensions d ";

    if ($type == 1) {
        $sql .= " LEFT JOIN tbl_users u ON u.emp_company_id = d.id AND u.status='Active'
                  WHERE d.dimension_type='1' ";  
    }
    if ($type == 2) {
        $sql .= " LEFT JOIN tbl_users u ON u.emp_division_id = d.id AND u.status='Active'
                  WHERE d.dimension_type='2' ";  
    }
    if ($type == 3) {
        $sql .= " LEFT JOIN tbl_users u ON u.emp_subdivision_id = d.id AND u.status='Active'
                  WHERE d.dimension_type='3' ";  
    }

    $sql .= " GROUP BY d.id  HAVING COUNT(u.user_id) > 0 ORDER BY d.short_name ASC";

    $result = $mysqli->query($sql);

    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row; 
        }
    }

    return $rows; 
}


function view_training_hours($employee_id, $select_year){

global $mysqli;

$sql1 = "SELECT tot_general,tot_ext,tot_div,tot_training_hrs,remarks FROM tbl_training_master WHERE user_id=$employee_id AND training_year=$select_year";

$training_result = $mysqli->query($sql1);

$result = $training_result->fetch_assoc();

$training_remarks= $result['remarks'];

$total_general1 = $result['tot_general'];
$total_division1 = $result['tot_div'];
$total_external1 = $result['tot_ext'];

$total_duration1 = $result['tot_training_hrs'];


$hours = floor($total_duration1); 
$minutes = ($total_duration1 - $hours) * 60; 
$total_duration = sprintf('%d:%02d', $hours, $minutes);

$hours1 = floor($total_general1); 
$minutes1 = ($total_general1 - $hours1) * 60; 
$total_general = sprintf('%d:%02d', $hours1, $minutes1);

$hours2 = floor($total_division1); 
$minutes2 = ($total_division1 - $hours2) * 60; 
$total_division = sprintf('%d:%02d', $hours2, $minutes2);

$hours3 = floor($total_external1); 
$minutes3 = ($total_external1 - $hours3) * 60; 
$total_external = sprintf('%d:%02d', $hours3, $minutes3);


return [
        'total_duration' => $total_duration,
        'training_remarks' => $training_remarks,
        'general' => $total_general,
        'division' => $total_division,
        'external' => $total_external
];

}

// function fetchUserIds($moduleId, $userId, $individualAccess, $tree=null)
// {
//     global $mysqli;
//     $finalArray = [];
//     $SQL = "SELECT group_id, location_id AS work_location, division_id AS emp_division_id, sub_division_id AS emp_subdivision_id FROM tbl_user_module_access WHERE module_access_id='$moduleId' AND user_id='$userId'";
//     $result = $mysqli->query($SQL);
//     $ids = array();
//     while ($row = $result->fetch_assoc()) {
//         $ids[] = $row;
//     }

//     $nonZeroValues = [];

//     // Filter out non-zero values
//     foreach ($ids as $item) {
//         $filteredItem = [];
//         foreach ($item as $key => $value) {
//             if ($value != 0) {
//                 $filteredItem[$key] = $value;
//             }
//         }
//         if (!empty($filteredItem)) {
//             $nonZeroValues[] = $filteredItem;
//         }
//     }


//     $orConditions = [];

//     // Build conditions based on non-zero values with AND condition if multiple values exist
//     foreach ($nonZeroValues as $values) {
//         $conditions = [];
//         foreach ($values as $key => $value) {
//             $conditions[] = "$key='$value'";
//         }
//         $orConditions[] = '(' . implode(' AND ', $conditions) . ')';
//     }

//         // Combine all OR conditions into a single query
//         $userIds = [];
//         if (!empty($orConditions)) {
//             $whereClause = implode(' OR ', $orConditions);
//             $query = "SELECT user_id FROM tbl_users WHERE status='Active' AND ($whereClause)";
//             //echo $query."<br>";
//             $result = $mysqli->query($query);

//             if ($result->num_rows > 0) {
//                 while ($row = $result->fetch_assoc()) {
//                     $userIds[] = $row['user_id'];
//                 }
//             }
//         }

//         /**
//          *  CASE : 1
//          * 
//          * tbl_user_access_setting + tbl_user_module_access
//          * for merging these two table values
//          * 
//          **/
//         $SQL = "SELECT s.access_id FROM tbl_user_access_setting s 
//         LEFT JOIN tbl_users u ON u.user_id = s.access_id
//         WHERE s.user_id='$userId' AND s.access_type='$moduleId' AND u.status='Active'";
//         $result = $mysqli->query($SQL);
//         $accessIds = [];
//         while ($row = $result->fetch_assoc()) {
//             $accessIds[] = $row['access_id'];
//         }
        
//         $mergedArray = array_merge($accessIds, $userIds);
        
//          /**
//          *  CASE : 2
//          * 
//          * This is for excluding ids selecting from tbl_user_access_setting
//          * with parent id tbl_moduleaccess
//          * 
//          **/
//         $moduleAccessIds = [];
        
//         $SQL = "SELECT id FROM tbl_moduleaccess WHERE parent_id='$moduleId'";
//         $result = $mysqli->query($SQL);
//         $row = $result->fetch_assoc(); 
        
//         if($row) 
//         {
//             $id = isset($row['id']) ? $row['id'] : 0;
//             $SQL = "SELECT access_id FROM tbl_user_access_setting WHERE user_id='$userId' AND access_type='$id'";
//             $result = $mysqli->query($SQL);
//             while ($row = $result->fetch_assoc()) {
//                 $moduleAccessIds[] = $row['access_id'];
//             }
//         }  
        
//     $finalArray = array_diff($mergedArray, $moduleAccessIds);

//     if($individualAccess==1)
//     {
//         $finalArray[] = $userId; 
//     }


//     //Include tree employees 
//     if ($tree == 1) {
//         $SQL = "SELECT lft, rght FROM tbl_users WHERE user_id = $userId";
//         $result = $mysqli->query($SQL);
//         $row = $result->fetch_assoc();
//         $left = $row['lft'];
//         $right = $row['rght'];
        
//         $SQL = "SELECT GROUP_CONCAT(user_id SEPARATOR ',') AS tree_ids 
//                 FROM tbl_users WHERE lft >= $left AND rght <= $right";
    
//         $result = $mysqli->query($SQL);
//         if ($result) {
//             $row = $result->fetch_assoc();
//             if (!empty($row['tree_ids'])) {
//                 $treeIds = explode(",", $row['tree_ids']);
//             } else {
//                 $treeIds = [];
//             }
//             $finalArray = array_merge($finalArray, $treeIds);
//         }
//     }
    
//     $uniqueUserIds = array_unique($finalArray);
//     $userIdsString = implode(',', $uniqueUserIds);

//     return $userIdsString;
// }


function fetchUserIds($moduleId, $userId, $individualAccess, $tree=null, $is_full=null)
{
    global $mysqli;
    $finalArray = [];

    // Determine status condition based on $is_full
    $statusCondition = ($is_full == true) ? "u.status IN ('Active', 'Inactive')" : "u.status='Active'";
    $statusConditionPlain = ($is_full == true) ? "status IN ('Active', 'Inactive')" : "status='Active'";

    $SQL = "SELECT group_id, location_id AS work_location, division_id AS emp_division_id, sub_division_id AS emp_subdivision_id FROM tbl_user_module_access WHERE module_access_id='$moduleId' AND user_id='$userId'";
    $result = $mysqli->query($SQL);
    $ids = array();
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row;
    }

    $nonZeroValues = [];

    // Filter out non-zero values
    foreach ($ids as $item) {
        $filteredItem = [];
        foreach ($item as $key => $value) {
            if ($value != 0) {
                $filteredItem[$key] = $value;
            }
        }
        if (!empty($filteredItem)) {
            $nonZeroValues[] = $filteredItem;
        }
    }

    $orConditions = [];

    // Build conditions based on non-zero values with AND condition if multiple values exist
    foreach ($nonZeroValues as $values) {
        $conditions = [];
        foreach ($values as $key => $value) {
            $conditions[] = "$key='$value'";
        }
        $orConditions[] = '(' . implode(' AND ', $conditions) . ')';
    }

    // Combine all OR conditions into a single query
    $userIds = [];
    if (!empty($orConditions)) {
        $whereClause = implode(' OR ', $orConditions);
        $query = "SELECT user_id FROM tbl_users WHERE $statusConditionPlain AND ($whereClause)";
        $result = $mysqli->query($query);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $userIds[] = $row['user_id'];
            }
        }
    }

    /**
     *  CASE : 1
     * 
     * tbl_user_access_setting + tbl_user_module_access
     * for merging these two table values
     * 
     **/
    $SQL = "SELECT s.access_id FROM tbl_user_access_setting s 
    LEFT JOIN tbl_users u ON u.user_id = s.access_id
    WHERE s.user_id='$userId' AND s.access_type='$moduleId' AND $statusCondition";
    $result = $mysqli->query($SQL);
    $accessIds = [];
    while ($row = $result->fetch_assoc()) {
        $accessIds[] = $row['access_id'];
    }
    
    $mergedArray = array_merge($accessIds, $userIds);
    
     /**
     *  CASE : 2
     * 
     * This is for excluding ids selecting from tbl_user_access_setting
     * with parent id tbl_moduleaccess
     * 
     **/
    $moduleAccessIds = [];
    
    $SQL = "SELECT id FROM tbl_moduleaccess WHERE parent_id='$moduleId'";
    $result = $mysqli->query($SQL);
    $row = $result->fetch_assoc(); 
    
    if($row) 
    {
        $id = isset($row['id']) ? $row['id'] : 0;
        $SQL = "SELECT access_id FROM tbl_user_access_setting WHERE user_id='$userId' AND access_type='$id'";
        $result = $mysqli->query($SQL);
        while ($row = $result->fetch_assoc()) {
            $moduleAccessIds[] = $row['access_id'];
        }
    }  
    
    $finalArray = array_diff($mergedArray, $moduleAccessIds);

    if($individualAccess==1)
    {
        $finalArray[] = $userId; 
    }

    // Include tree employees 
    if ($tree == 1) {
        $SQL = "SELECT lft, rght FROM tbl_users WHERE user_id = $userId";
        $result = $mysqli->query($SQL);
        $row = $result->fetch_assoc();
        $left = $row['lft'];
        $right = $row['rght'];
        
        $SQL = "SELECT GROUP_CONCAT(user_id SEPARATOR ',') AS tree_ids 
                FROM tbl_users WHERE lft >= $left AND rght <= $right";
    
        $result = $mysqli->query($SQL);
        if ($result) {
            $row = $result->fetch_assoc();
            if (!empty($row['tree_ids'])) {
                $treeIds = explode(",", $row['tree_ids']);
            } else {
                $treeIds = [];
            }
            $finalArray = array_merge($finalArray, $treeIds);
        }
    }
    
    $uniqueUserIds = array_unique($finalArray);
    $userIdsString = implode(',', $uniqueUserIds);

    return $userIdsString;
}



function auditing_count_update($current_user_id, $employee_id,$date_report) {
    global $mysqli;
    // $date_report = date('Y-m-d');
        $todayz = date('Y-m-d');

    if ($current_user_id != $employee_id) {
        $audit_check_row =array();
        $audit_check_sql = "Select id from tbl_employee_log where date(jobdiary_date)='$date_report' and date(log_date)='$date_report' and user_id='$current_user_id' and access_id='$employee_id' and action='Employee Audit'";

        $audit_check_result = $mysqli->query($audit_check_sql);
        $audit_check_row = $audit_check_result->fetch_assoc();
        if ($audit_check_result->num_rows>0 && $audit_check_row['id'] > 0) {

        } else {

            $time_check_sql = "select id from tbl_time where date_log='$date_report' and  user_id='$current_user_id'";
            $time_check_result = $mysqli->query($time_check_sql);
            $time_check_row = $time_check_result->fetch_assoc();
            if ($time_check_row['id'] > 0) {
                if($date_report<=$todayz)
                $mysqli->query("update  tbl_time set auditing=auditing+1 where date_log='$date_report' and user_id=$current_user_id");
            } else {
                 $audited=0;
                if($date_report<=$todayz)
                $auditing=1;
                $mysqli->query("insert into tbl_time(user_id,date_log,auditing) values('$current_user_id','$date_report','$auditing')");
            }


            $time_check_sql = "select id from tbl_time where date_log = '$date_report' and user_id = '$employee_id'";
            $time_check_result = $mysqli->query($time_check_sql);
            $time_check_row = $time_check_result->fetch_assoc();
            // if($date_report)
            if ($time_check_row['id'] > 0){ 
                if($date_report<=$todayz)
                $mysqli->query("update tbl_time set audited = audited+1 where date_log = '$date_report' and user_id = '$employee_id'");}
            else{ 
                $audited=0;
                if($date_report<=$todayz)
                $audited=1;
                $mysqli->query("insert into tbl_time(user_id,date_log,audited) values('$employee_id','$date_report','$audited')");
                
            }
        }
    }
}

function today_audited($user_id, $date_report) {
    global $mysqli;

    $sql1 = "SELECT remarks,action,page_type,jobdiary_date,DATE_FORMAT(log_date,'%h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name from tbl_employee_log l left join tbl_users u on u.user_id = l.user_id where access_id = $user_id and date(jobdiary_date) = '$date_report' and l.user_id>0 and access_id!=l.user_id and   (page_type!= 'Monthly Plan' and page_type!= 'Monthly Plan D&R')  order by date(jobdiary_date) ";



    $mysql1 = $mysqli->query($sql1);
    ?>
<div id="remarks_by">
    <table id="customers_time" class="reviewed_by" style="border-collapse: collapse;
               " width="90%" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="8" align="center">
                <div style="display: block;
                                                    ">
                    <font color="#ff0000;" size="+1"><strong>Reviewed By</strong>&nbsp;</font>
                </div>
            </td>
        </tr>
        <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
            <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                    <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
                </b></td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                    <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Time</font>
                </b></td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                    <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Superior Name</font>
                </b></td>
        </tr>
        <?php
            $count = 0;$old_time="";
            while ($row6 = $mysql1->fetch_assoc()) {
                if ($count % 2 == 0)
                    $bgcolor = '#e9e9e9';
                else
                    $bgcolor = '#f2f2f2';
                    if($old_time!=$row6['log_time']){
                ?>
        <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                    <b><?php echo $row6['page_type']; ?></b>
                </font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                            <b><?php  echo $row6['log_time']; ?></b>
                        </font>
                    </b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                    <b><?php echo $row6['full_name']; ?></b>
                </font>
            </td>
        </tr>
        <?php $old_time =$row6['log_time'];
                $count++;
            }}
            ?>
    </table>
</div>
<br>
<?php
}

function today_auditing($user_id, $date_report) {
    global $mysqli;

    $sql1 = "SELECT  l.page_type,jobdiary_date,DATE_FORMAT(log_date,'%h:%i %p') as log_time,
    if(u.display_name!='',u.display_name,u.full_name) as full_name from tbl_employee_log l   left join  tbl_users u on u.user_id=l.access_id  where l.user_id=$user_id and date(log_date) = '$date_report'  and access_id>0 and access_id!=l.user_id and (page_type!= 'Monthly Plan' and page_type!= 'Monthly Plan D&R')   order by date(log_date) ";

    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_to" style="border-collapse: collapse;" width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="8" align="center">
            <div style="display: block;">
                <font color="#ff0000" size="+1"><strong>Reviewed To</strong></font>
            </div>
        </td>
    </tr>

    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">

        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Time</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Subordinate Name</font>
            </b></td>
    </tr>


    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['log_time']; ?></b>
            </font>
        </td>

        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>

    <?php
            $count++;
        }
        ?>
</table>
<br>

<?php
}

function thisMonthaudited($user_id, $month, $year) {
    global $mysqli;

    $sql1 = "SELECT  remarks,action,page_type, DATE_FORMAT( jobdiary_date, '%M' ) month,DATE_FORMAT(log_date,'%d/%m/%Y %h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name from tbl_employee_log l left join  tbl_users u on u.user_id=l.user_id  where access_id=$user_id and action = 'Monthly Plan Review' and ((MONTH(log_date) = '$month' AND YEAR(log_date) = '$year') || (MONTH(jobdiary_date) = '$month' AND YEAR(jobdiary_date) = '$year')) and l.user_id>0 and access_id!=l.user_id order by date(log_date) DESC limit 10";



    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_by" style="border-collapse: collapse;" width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="8" align="center">
            <div style="display: block;">
                <font color="#ff0000;" size="+1"><strong>Last Five Reviews By</strong>&nbsp;</font>
            </div>
        </td>
    </tr>
    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed Month</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed On</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Superior Name</font>
            </b></td>
    </tr>
    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['month']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                        <b><?php echo $row6['log_time']; ?></b>
                    </font>
                </b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>
    <?php
            $count++;
        }
        ?>
</table>
<br>
<?php
}

function thisMonthaudited_to($user_id, $month, $year) {
    global $mysqli;

    $sql1 = "SELECT  remarks,action,page_type, DATE_FORMAT( jobdiary_date, '%M' ) month,DATE_FORMAT(log_date,'%d/%m/%Y %h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name
  			from tbl_employee_log l left join  tbl_users u on u.user_id=l.access_id
  			where l.user_id=$user_id and action = 'Monthly Plan Review' and ((MONTH(log_date) = '$month' AND YEAR(log_date) = '$year') || (MONTH(jobdiary_date) = '$month' AND YEAR(jobdiary_date) = '$year'))   and l.access_id>0 and l.access_id!=l.user_id order by date(log_date) DESC limit 10";



    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_to" style="border-collapse: collapse; " width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="8" align="center">
            <div style="display: block;">
                <font color="#ff0000;" size="+1"><strong>Last Five Reviews To</strong>&nbsp;</font>
            </div>
        </td>
    </tr>
    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed Month</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed On</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Subordinate Name</font>
            </b></td>
    </tr>
    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['month']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                        <b><?php echo $row6['log_time']; ?></b>
                    </font>
                </b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>
    <?php
            $count++;
        }
        ?>
</table>
<br>
<?php
}

function observation_to($type, $user_id, $plan_month, $plan_year, $date_report) {
    global $mysqli;

    $sql_fetch = "select o.remarks,
DATE_FORMAT(o.audited_date,'%d/%m/%Y') as audit_date,
DATE_FORMAT(o.report_date,'%d/%m/%Y') as report_date,
if(u.display_name!='',u.display_name,u.full_name) as full_name,u.user_id,
if(u1.display_name!='',u1.display_name,u1.full_name) as subordinate_name ,
if(o.subordinate_id>0,concat(t.type,' to ',if(u1.display_name!='',u1.display_name,u1.full_name)),t.type)  as  type ,
o.observation,

t.image,
o.user_remarks,
o.notified,
o.audited_by
from tbl_observations o
left join tbl_observation_types t
on t.id=o.observation
left join tbl_users u
on u.user_id=o.user_id
left join tbl_users u1
on u1.user_id=o.subordinate_id

where o.audited_by=" . $user_id;

    if ($type == 'monthly') {
         $sql_fetch .= "	and o.type='monthly'
  and o.report_month=" . $plan_month . "
and o.report_year=" . $plan_year; 
    }
    
     if ($type == 'kpi') {
         $sql_fetch .= "	and o.type='kpi'
  and o.report_month=" . $plan_month . "
and o.report_year=" . $plan_year; 
    }

    if ($type == 'marketing') {
        $sql_fetch .= "	and o.type='marketing'
  and o.report_month=" . $plan_month . "
and o.report_year=" . $plan_year;
    }

    if ($type == 'daily') {
        $sql_fetch .= " and o.type='daily'
and (o.audited_date='".$date_report."' or o.report_date='" . $date_report . "')";
    }

    if ($type == 'UT-Reporting') {
        $sql_fetch .= " and o.type='UT-Reporting'
 and o.report_month=" . $plan_month . "
and o.report_year=" . $plan_year;
    }

    $sql_fetch .= " order by o.audited_date desc";
    
    // echo $sql_fetch;

  // echo $sql_fetch;exit;
    $mysql1 = $mysqli->query($sql_fetch);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="observation_to" style="border-collapse: collapse;" width="98%" align="center"
    border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="12" align="center">
            <div style="display: block;">
                <font color="#ff0000">&nbsp;<b></b></font>
            </div>
        </td>
    </tr>

    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">

        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Reviewed To</b></font>
        </td>
        <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Rating</b></font>
        </td>
        <td colspan="2" align="center" style="border:#000000 solid 1px;">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> Reviewed On</b></font>
        </td>
        <?php if($type!="monthly"){?>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Job Diary Date </b></font>
        </td>
        <?php }?>
        <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Remarks</b></font>
        </td>
        <td colspan="3" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>User Response</b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notified</b></font>
        </td>
    </tr>


    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="30" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

        <?php  if ($type == 'monthly') {?>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <a
                        href="javascript:openWindow('monthly_plan_history_new.php?select_employee_id=<?php echo $row6['user_id'] ?>&plan_month=<?php echo $plan_month ?>&plan_year=<?php echo $plan_year ?>','view_status');">
                        <?php echo $row6['full_name']; ?></a></b></font>
        </td>
        <?php } else{ ?>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo $row6['full_name']; ?> </b></font>
        </td>
        <?php }?>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">

            <?php if ($row6['observation'] == 8 || $row6['observation'] == 9) {
                        ?>

            <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php
                            echo $row6['type'];
                            ?></b></font>
            <?php } else { ?>


            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php
                            echo $row6['type'];
                            ?></b></font>

            <?php } ?>
            &nbsp;
        </td>
        <td colspan="2" style="border-left-style:hidden">
            <?php if (isset($row6['type'])) {
                        ?>
            <img src="images/ratings/<?php echo $row6['image'] ?>">
            <?php } ?>
        </td>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo $row6['audit_date']; ?></b></font>
        </td>
        <?php if($type!="monthly"){?>
        <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo $row6['report_date']; ?></b></font>
        </td>
        <?php }?>
        <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo nl2br($row6['remarks']); ?></b></font>
        </td>
        <td colspan="3" align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo nl2br($row6['user_remarks']); ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <?php
                    if ($row6['notified'] == 1) {
                        ?>
            <img src="images/tick.png" />
            <?php } ?>
        </td>
    </tr>

    <?php
            $count++;
        }
        ?>
</table>
<?php
}

function observation($type, $user_id, $plan_month, $plan_year, $date_report) {
    global $mysqli;

    $sql_fetch = "select o.remarks,
DATE_FORMAT(o.audited_date,'%d/%m/%Y') as audit_date,
if(u.display_name!='',u.display_name,u.full_name) as full_name,cat.oc_name,
if(u1.display_name!='',u1.display_name,u1.full_name) as subordinate_name ,
if(o.subordinate_id>0,concat(t.type,' to ',if(u1.display_name!='',u1.display_name,u1.full_name)),t.type)  as  type ,
o.observation,
j.taskname,
t.image,
o.user_remarks,
o.notified,
o.audited_by,o.ob_type
from tbl_observations o
left join tbl_observation_types t
on t.id=o.observation
left join tbl_users u
on u.user_id=o.audited_by
left join tbl_users u1 on u1.user_id=o.subordinate_id
left join tbl_job_observation j on j.observation_id=o.id
left join tbl_observation_category cat on cat.oc_id=o.ob_type
where o.user_id=" . $user_id;



    if ($type == 'daily') {
        $sql_fetch .= " and o.type='daily'
and o.report_date='" . $date_report . "'";
    } 
//    elseif($type == 'Self Assessment Review'){
//      $sql_fetch .= " and o.type='$type'";  
//    }
    
    else {
        $sql_fetch .= " and (o.type='$type' or o.type='Monthly Task')
 and o.report_month=" . $plan_month . "
and o.report_year=" . $plan_year;
    }

    $sql_fetch .= " order by o.audited_date desc";

// echo $sql_fetch;
    $mysql1 = $mysqli->query($sql_fetch);
    ?>
<div id="remarks_by">
    <table id="customers_time" class="observation" style="border-collapse: collapse; " width="98%" align="center"
        border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="13" align="center">
                <div style="display: block;">
                    <font color="#ff0000">&nbsp;<b></b></font>
                </div>
            </td>
        </tr>

        <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">

            <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Reviewed By</b></font>
            </td>
            <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Rating</b></font>
            </td>
            <td colspan="2" align="center" style="border:#000000 solid 1px;">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> Date</b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                    <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Job</font>
                </b></td>
            <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Remarks</b></font>
            </td>
            <td colspan="3" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>User Response</b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notified</b></font>
            </td>
        </tr>


        <?php
            $count = 0;
            while ($row6 = $mysql1->fetch_assoc()) {
                if ($count % 2 == 0)
                    $bgcolor = '#e9e9e9';
                else
                    $bgcolor = '#f2f2f2'; $ob_type="";
                if($row6['ob_type']!="0"){
                    $ob_type = " - ".$row6['oc_name'];
                }
                ?>
        <tr height="30" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

            <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['full_name']; ?></b></font>
            </td>
            <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">

                <?php if ($row6['observation'] == 8 || $row6['observation'] == 9) {
                            ?>

                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php
                                echo $row6['type'];
                                ?></b></font>
                <?php } else { ?>


                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php
                                echo $row6['type'] .$ob_type;
                                ?></b></font>

                <?php } ?>
                &nbsp;
            </td>
            <td colspan="2" style="border-left-style:hidden">
                <?php if (isset($row6['type'])) {
                            ?>
                <img src="images/ratings/<?php echo $row6['image'] ?>">
                <?php } ?>
            </td>
            <td colspan="2" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['audit_date']; ?></b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                    <b><?php echo $row6['taskname']; ?></b>
                </font>
            </td>
            <td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo nl2br($row6['remarks']); ?></b></font>
            </td>
            <td colspan="3" align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo nl2br($row6['user_remarks']); ?></b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <?php
                        if ($row6['notified'] == 1) {
                            ?>
                <img src="images/tick.png" />
                <?php } ?>
            </td>
        </tr>

        <?php
                $count++;
            }
            ?>
    </table>
</div>
<?php
}

function rating_table($user_id, $current_user_id) {
global $mysqli;
$sql1 = "SELECT * FROM tbl_observation_category WHERE oc_status=1";
$mysql1 = $mysqli->query($sql1);
    
if ($user_id != $current_user_id) {
?>
<br>
<table style="border-collapse: collapse;" width="95%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr height="25" style="border:#a6107b solid 1px;background-color:#008080;color:#ffffff;">
        <td colspan="10" align="center"><b style="font-size:14px">Review Findings</b></td>
    </tr>
    <tr style="border:#a6107b solid 1px;background-color:#ffffff">

        <td style="border:#a6107b solid 1px;background-color:#ffffff">
            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;
                    <input id='excellent' type="radio" name="observation" value="7">&nbsp;Excellent&nbsp;
                    <input id='very_good' type="radio" name="observation" value="6">&nbsp;Very Good&nbsp;
                    <input id='good' type="radio" name="observation" value="5">&nbsp;Good&nbsp;
                </font>
                <font color="#337ab7" face="Verdana, Arial, Helvetica, sans-serif" size="3">
                    &nbsp;
                    <input id="sugg" type="radio" name="observation" value="40">&nbsp;Suggestion
                </font>
            </b>
        </td>

        <td id="obs_cat" rowspan="3"
            style="border:1px solid #a6107b; background-color:#f9f9f9; padding:15px; display:none; vertical-align:top;">

            <label style="font-family:sans-serif; font-weight:bold; font-size:13px; color:#333;">
                Review Type
            </label>

            <br><br>

            <select id="ob_type" name="ob_type" style="width:90%; padding:6px; border:1px solid #ccc; border-radius:4px;
               font-family:sans-serif; font-weight:bold; display:none;">

                <option value="0">Select</option>
                <?php while ($row = $mysql1->fetch_assoc()) { ?>
                <option value="<?=$row['oc_id']?>"><?=$row['oc_name']?></option>
                <?php } ?>

            </select>

        </td>

        <td align="center" rowspan="3"
            style="border:1px solid #a6107b; background-color:#f9f9f9; padding:15px; vertical-align:top;">
            <label
                style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; color:#333;">Remarks</label>
            <br><br>
            <textarea id="remarks" name="remarks" rows="6" style="width:90%; border:1px solid #ccc; border-radius:5px;  
font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px;
box-shadow: inset 0 1px 3px rgba(0,0,0,0.1); resize:vertical;" placeholder="Enter your remarks here..."></textarea>

        </td>
    </tr>
    <tr style="border:#a6107b solid 1px;background-color:#ffffff">

        <td>
            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;
                    <input id='need_imp' type="radio" name="observation" value="4">&nbsp;Need Improvement&nbsp;
                    <input id="poor" type="radio" name="observation" value="3">&nbsp;Poor&nbsp;
                    <input id="obs" type="radio" name="observation" value="1">&nbsp;Observation

                </font>
            </b>
        </td>
    </tr>

    <tr style="border:#a6107b solid 1px;background-color:#ffffff">

        <td>
            <b>

                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="4">
                    &nbsp;<input id="ncr" type="radio" name="observation" value="2">&nbsp;N.C.R</font>
            </b>
        </td>
    </tr>

    <tr style="border:1px solid #a6107b; background-color:#f5f5f5;">
        <td colspan="10" align="center" style="padding:15px;">

            <!-- Submit Button -->
            <button type="submit" onclick="return validate();" name="submit_audit" class="btn btn-success"
                style="min-width:120px;">
                <i class="glyphicon glyphicon-ok"></i> Submit
            </button>

            <!-- Clear Button -->
            <button type="reset" class="btn btn-primary" style="margin-left:10px; min-width:120px;">
                <i class="glyphicon glyphicon-refresh"></i> Clear Review
            </button>

        </td>
    </tr>

</table>
<?php
    }
}

function rating_table_monthly($user_id, $current_user_id, $complete_flag) {
    ?>

<?php
    if ($user_id != $current_user_id) {
        ?>
<br>
<table style="border-collapse: collapse;" width="85%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr height="25" style="border:#a6107b solid 1px;background-color:#008080;color:#ffffff;">
        <td colspan="10" align="center"><b>Review Findings</b></td>
    </tr>

    <tr style="border:#a6107b solid 1px;background-color:#ffffff">
        <td style="border:#a6107b solid 1px;background-color:#ffffff">

            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php if ($complete_flag != 1) { ?>
                    <input type="radio" name="observation" value="11">
                    <font style="color:#FF0000">Change The Plan</font>
                    <?php } ?>
                    <input type="radio" name="observation" value="7">Excellent&nbsp;
                    <input type="radio" name="observation" value="6">Very Good&nbsp;
                    <input type="radio" name="observation" value="5">Good&nbsp;
        </td>

        <td align="center" style="border:#a6107b solid 1px;background-color:#ffffff" rowspan="3"><b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">Remarks</font>
            </b><br />
            <!--<td colspan="2" rowspan="3" align="left" rowspan="3">-->
            <textarea style="border:2px solid skyblue;margin:10px;" id="remarks" name="remarks" rows="5"
                cols="30"></textarea>
        </td>
    </tr>
    <tr style="border:#a6107b solid 1px;background-color:#ffffff">

        <td style="border:#a6107b solid 1px;background-color:#ffffff">

            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <input type="radio" name="observation" value="4">Need Improvement
                    <input type="radio" name="observation" value="3">&nbsp;Poor
                    <input id="obs" type="radio" name="observation" value="1">&nbsp;Observation

        </td>
    <tr style="border:#a6107b solid 1px;background-color:#ffffff">

        <td style="border:#a6107b solid 1px;background-color:#ffffff">

            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="3" color="red">
                    <input id="ncr" type="radio" name="observation" value="2">N.C.R
                </font>
            </b>
        </td>

    </tr>
    <tr height="25" style="border:#a6107b solid 1px;background-color:#e9e9e9">
        <td colspan="10" align="center">
            <input class="jobdiary_buttons" type="submit" id="ms" onclick="return validate();" name="submit_audit2"
                value="Submit" />
        </td>

    </tr>
</table>
<?php
    }
}

function observation_save($user_id, $remarks, $rating, $type, $plan_month, $plan_year, $audited, $date_report=NULL, $ob_type=NULL,string $dm_id=NULL) {
    global $mysqli;
    if(!isset($ob_type)) $ob_type=0;
    $log_date  = date('Y-m-d H:i:s');
    $log_date1 =  date('Y-m-d');
    $log_time  = date('H:i:s');

    if ($type == 'monthly') {
         $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,ob_type) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','monthly','$log_date1','$log_time', " . $plan_month . "," . $plan_year . "," . $audited . ",". $ob_type.")";
   
        
        //***Added by Saranya for monthly plan table updation
          $mysqli->query("update tbl_monthly_plan set is_modified=0, is_rework_verified=0 where user_id=$user_id and plan_month=$plan_month and plan_year=$plan_year"); 
        
    }
    
    if ($type == 'kpi') {
         $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,ob_type) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','kpi','$log_date1', '$log_time', " . $plan_month . "," . $plan_year . "," . $audited . ",". $ob_type.")";
   
    }

    if ($type == 'daily') {
        $sql_ins = "insert into tbl_observations
(user_id,remarks,observation,type,audited_date,audited_time,report_date,audited_by,ob_type)
values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','daily','$log_date1', '$log_time',   '" . $date_report . "'," . $audited. ",". $ob_type.")";
        
    }

    if ($type == 'marketing') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','marketing','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . ")";
    }

    if ($type == 'late') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','late','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . ")";
    }

    if ($type == 'UT-Reporting') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','UT-Reporting','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . ")";
    }
     if ($type == 'market-input') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','market-input','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . ")";
    }
    
    if ($type == 'digital-market-seo-input') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,dm_id) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','digital-market-seo-input','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . "," . $dm_id . ")";
    }
    
    if ($type == 'digital-market-revenue-input') { 
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,dm_id) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','digital-market-revenue-input','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . "," . $dm_id . ")";
    }
    
    if ($type == 'division-performance') {
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,dm_id) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','division-performance','$log_date1', '$log_time'," . $plan_month . "," . $plan_year . "," . $audited . "," . $dm_id . ")";
    }
    
    if ($type == 'Health-CHO-Audit') { 
        $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,report_date,audited_time,report_month,report_year,audited_by,dm_id) values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','Health-CHO-Audit','$log_date1','$log_date1' ,'$log_time'," . $plan_month . "," . $plan_year . "," . $audited . "," . $dm_id . ")";
    }

    if ($type == 'Emergency') { 
 $sql_ins = "INSERT INTO tbl_observations
(user_id,remarks,observation,type,audited_date,audited_time,report_date,audited_by,ob_type)
values (" . $user_id . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','Emergency Tasks Auditing','$log_date1', '$log_time',   '" . $date_report . "'," . $audited. ",". $ob_type.")";
    }

    $mysqli->query($sql_ins);
    $last_id  = $mysqli->insert_id;
    $updation_audit_sql ="update  tbl_observations  set start_id=$last_id where id='$last_id'";
    $mysqli->query($updation_audit_sql);
}

function ceo_observation_save($user_id, $remarks, $rating, $type, $plan_month, $plan_year, $audited, $date_report, $audit_employee_head) {
    global $mysqli;
    $log_date = date('Y-m-d H:i:s');

    while (($audit_employee_head > 0) && ($audit_employee_head != 2)) {

        if ($type == 'monthly') {
            $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,subordinate_id) values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','monthly','$log_date'," . $plan_month . "," . $plan_year . "," . $audited . "," . $user_id . ")";
        }
        
        if ($type == 'kpi') {
            $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,subordinate_id) values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','kpi','$log_date'," . $plan_month . "," . $plan_year . "," . $audited . "," . $user_id . ")";
        }

        if ($type == 'daily') {
            $sql_ins = "insert into tbl_observations
(user_id,remarks,observation,type,audited_date,audited_time,report_date,audited_by,subordinate_id)
values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','daily','$log_date','" . $date_report . "'," . $audited . "," . $user_id . ")";
            //echo $sql_ins;
        }

        if ($type == 'marketing') {
            $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,subordinate_id) values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','marketing','$log_date'," . $plan_month . "," . $plan_year . "," . $audited . "," . $user_id . ")";
        }

        if ($type == 'late') {
            $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,subordinate_id) values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','late','$log_date'," . $plan_month . "," . $plan_year . "," . $audited . "," . $user_id . ")";
        }

        if ($type == 'UT-Reporting') {
            $sql_ins = "insert into tbl_observations(user_id,remarks,observation,type,audited_date,audited_time,report_month,report_year,audited_by,subordinate_id) values (" . $audit_employee_head . ",'" . trim(addslashes($remarks)) . "','" . $rating . "','UT-Reporting','$log_date'," . $plan_month . "," . $plan_year . "," . $audited . "," . $user_id . ")";
        }

        $mysqli->query($sql_ins);









        if ($remarks != "") {

            $audit_user_query = $mysqli->query("select if(u.display_name!='',u.display_name,u.full_name) as full_name,email,personal_email from tbl_users u  where user_id=$audit_employee_head");
            $audit_user_array = $audit_user_query->fetch_assoc();
            $audit_user = $audit_user_array['full_name'];
            $email = $audit_user_array['email'];
            $personal_email = $audit_user_array['personal_email'];

            if ($email != '')
                $cc = $email;
            else
                $cc = $personal_email;


            $user_query = $mysqli->query("select if(display_name!='',display_name,full_name) as full_name,email,personal_email from tbl_users  where user_id='$audited'");
            $user_array = $user_query->fetch_assoc();
            $username = $user_array['full_name'];

            $email = $user_array['email'];
            $personal_email = $user_array['personal_email'];

            if ($email != '')
                $from = $email;
            else
                $from = $personal_email;


            $subordinate_query = $mysqli->query("select if(display_name!='',display_name,full_name) as full_name from tbl_users  where user_id=$user_id");
            $subordinate_array = $subordinate_query->fetch_assoc();
            $subordinate_user = $subordinate_array['full_name'];


            $body = '
<b>Dear ' . $audit_user . '<br>';
            $body .= 'Please find  the CEO  Comments on your Subordinate\'s Job</b>
<br><br>
<table  width=100%>
  <tr style="font-size: 1.1em;
    text-align: left;
    padding-top: 5px;
    padding-bottom: 4px;
    background-color: #A7C942;
    color: #ffffff;">
	<th  style=" font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;">Date</th>

    <th  style=" font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;">By</th>

      <th  style=" font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;">Comments</th>
            	  </tr>';

            $body .= '

  <tr >
  <td style="color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;"  ><strong>' . $date_report . '</strong></td>

    <td style="color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;"  ><strong>' . $username . '</strong></td>


    <td style="color: #000000;
    background-color: #EAF2D3; font-size: 1em;
    border: 1px solid #98bf21;
    padding: 3px 7px 2px 7px;"><strong>' . $remarks . '</strong></td>

  </tr>';


            $body .= '</table>';

            if ($rating == 8)
                $subject = "CEO Observation On $subordinate_user" . "'s Job";
            if ($rating == 9)
                $subject = "CEO NCR On $subordinate_user" . "'s Job";


//            $headers = "From:  mail@effism.com\r\n";
//            $headers .= 'Reply-To: ' . $from . ',mail@effism.com ' . "\r\n";
//            $headers .= "MIME-Version: 1.0\r\n";
//            $headers .= 'Cc: ' . $cc . "\r\n";
//            $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
//            mail('mail@effism.com', $subject, $body, $headers);
            
        ////***********PHP mailer STARTS************
/*@include_once 'PHPMailer/class.phpmailer.php';
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        $mail->Host = "mail.effism.com";
        $mail->Port = 465;
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth = true;
        $mail->Username = "mail@effism.com";
        $mail->Password = "s+z#o(t*B*O}";
        $mail->ClearAllRecipients();
          $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
        $mail->SetFrom("mail@effism.com", 'Effism Mail');
        $mail->AddAddress("mail@effism.com");
        $mail->AddCC($cc);
        
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        if (!$mail->Send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            
        }*/

        ////***********PHP mailer ends  **************    
            
        }

        $sub_sql = "select username,if(display_name!='',display_name,full_name) as full_name,division_head from tbl_users where  user_id=$audit_employee_head";
        $sub_result = $mysqli->query($sub_sql);
        $sub_row = $sub_result->fetch_assoc();


        $audit_employee_head = $sub_row['division_head'];
    }
}

function get_user_details_table($user_id, $my_division_head) {
    $work_category_array = array("1" => "Productive", "2" => "Semi  Productive", "3" => "Supporting");

    global $mysqli;
    $user_info_query = $mysqli->query("select  user.photo,user.hr_id,user.employee_code, user.work_category,user.username, if(user.display_name!='',user.display_name,user.full_name) as full_name, user.designation, user.gender, DATE_FORMAT(user.dob,'%d-%b-%Y') as dob, DATE_FORMAT(user.doj,'%d-%b-%Y') as doj,DATE_FORMAT(user.gdoj,'%d-%b-%Y') as  gdoj,user.personal_email,user.email, IF(ed.officialmobile != '', 
       CONCAT(ed.cc_officialmobile, '-', ed.officialmobile), 
       CONCAT(ed.cc_personalmobile, '-', ed.personalmobile)
    ) AS mobile, user.hourly_rate,user.currency, user.emp_company_id, user.emp_division_id, user.emp_subdivision_id,emp_group_name,t.name as job_type,work_place, user.status, user.is_regular,user.date_created,user.user_height, user.blood_group,user.reporting_time,if(head.display_name!='',head.display_name,head.full_name) as division_head_name,user.division_head,
    
round(((DATEDIFF(now(),user.gdoj)-user.service_break_days)/365.25) + user.prev_aries,2)


as year_in_aries    
from tbl_users user
left join  tbl_users  head on head.user_id=user.division_head
left join  tbl_emp_workplace w on w.id=user.work_location
left join  tbl_emp_type t on t.id=user.emp_type
left join  tbl_emp_group g on g.emp_group_id=user.group_id
left join  tbl_employee_contact_details ed on ed.user_id=user.user_id


where user.user_id=$user_id");





    $user_info_row = $user_info_query->fetch_assoc();
    $tree_sql = "SELECT if(display_name!='',display_name,full_name) as full_name,user_id  FROM  tbl_users WHERE  division_head='" . $user_id . "' and is_regular=1 and status='Active' order by full_name";
    $tree_result = $mysqli->query($tree_sql);


    $dimension_array = array();
    $dimension_result = $mysqli->query("SELECT * FROM tbl_dimensions");
    while ($dimension_row = $dimension_result->fetch_assoc()) {
        $id = $dimension_row['id'];
        $dimension_name = $dimension_row['short_name'];
        $dimension_array[$id] = $dimension_name;
    }
    
     $index_result = $mysqli->query("SELECT

 round(qi.plustwo+qi.diploma+ qi.ug+qi.pg+qi.computer+qi.othrs+ ((round((datediff(now(),u.GDOJ)/365.25),2) + qi.prev_aries )*2)+ qi.relnt+(( qi.total- qi.relnt)/4),2) as emp_index




	FROM
       tbl_user_index as qi left join tbl_users as  u ON u.user_id = qi.user_id   where qi.user_id=$user_id





");

    $index_row = $index_result->fetch_assoc();
    ?>
<table width="99%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
    <tr height="20" style="border:#a6107b solid 1px;">
        <td rowspan="6" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><img width="100px"
                src="images/employee/<?php echo $user_info_row['photo'] ?>">&nbsp;</td>
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name
                </font>
            </b>
        </td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['full_name'] ?>&nbsp;
                </font>
            </b>
        </td>

        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">&nbsp;Employee Code</font>
            </b></td>

        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['employee_code'] ?></font>
            </b></td>

        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Email</font>
            </b></td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;">&nbsp;<b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2"><a
                        href="mailto:<?php echo $user_info_row['email'] ?>"><?php echo $user_info_row['email'] ?></a>&nbsp;&nbsp;
                    <?php
                    if ($user_info_row['personal_email'] != $user_info_row['email']) {?>
                    <a href="mailto: <?php echo $user_info_row['personal_email'];?>">
                        &nbsp;<?php echo $user_info_row['personal_email'];?></a>
                    <?php   }
                    ?>
                </font>
            </b></td>
        <!--      <td width="198" align="left" bgcolor="#e2f3fd"   style="border:#000000 solid 1px;"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Date of Birth</font></b></td>
            <td width="198" align="left" bgcolor="#e2f3fd"   style="border:#000000 solid 1px;"><b><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;<?php echo $user_info_row['dob'] ?></font></b></td> -->


        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>



    <tr height="20" style="border:#a6107b solid 1px;">
        <!--              <td  bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Employee Code</font></b></td>
            <td align="left"   style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;<?php echo $user_info_row['employee_code'] ?></font></b></td>-->
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Designation</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['designation'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Mobile</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['mobile'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Date of Joining</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['doj'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Hourly Rate</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['hourly_rate'] . " " . $user_info_row['currency'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Gender</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['gender'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">
            <b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Group Joining Date
                </font>
            </b>
            <hr> <b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Years in Aries</font>:
            </b>
            <hr> <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Qualification Index
                </font>:
            </b>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">
            <b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['gdoj'] ?></font>
            </b>
            <hr>
            <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                        &nbsp;<?php echo $user_info_row['year_in_aries'] ?></font>
            </b>
            <hr> <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php if (isset($index_row['emp_index'])) echo $index_row['emp_index'] ?></font>
            </b>


        </td>


        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Comp/Div/SubDiv</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $dimension_array[$user_info_row['emp_company_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_division_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_subdivision_id']] ?>
                </font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Category</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $work_category_array[$user_info_row['work_category']] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Location</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['work_place'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Reporting To</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;<?php
                if ($my_division_head != $user_info_row['division_head']) {
                    ?>
            <b>
                <a href="javascript:click_tree(<?php echo $user_info_row['division_head'] ?>)"
                    style='text-decoration:underline;color:#0000FF' face='Verdana, Arial, Helvetica, sans-serif'
                    size='2'><?php echo $user_info_row['division_head_name'] ?></a></b>
            <?php
                } else {
                    ?>
            <b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['division_head_name'] ?></font>
            </b>
            <?php
                }
                ?>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Reporting Time</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['reporting_time'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Job Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['job_type'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Review Tree</font>
            </b></td>
        <td colspan="6" align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;

            <?php
                $count = 1;
                while ($tree_row = $tree_result->fetch_assoc()) {
                    echo "<b><a href='javascript:click_tree(" . $tree_row['user_id'] . ")'  style='text-decoration:underline;color:#0000FF;font-size:11px'  face='Verdana, Arial, Helvetica, sans-serif'>" . "" . $count++ . ")" . $tree_row['full_name'] . "</a></b>" . " ";
                }
                ?>

        </td>

    </tr>
</table>

<?php
}

function get_exit_user_details_table($user_id, $my_division_head="") {
    $work_category_array = array("1" => "Productive", "2" => "Semi  Productive", "3" => "Supporting");

    global $mysqli;
    $user_info_query = $mysqli->query("select  ex.date,user.photo,user.hr_id,user.employee_code, user.work_category,user.username, if(user.display_name!='',user.display_name,user.full_name) as full_name, user.designation, user.gender, DATE_FORMAT(user.dob,'%d-%b-%Y') as dob, DATE_FORMAT(user.doj,'%d-%b-%Y') as doj,DATE_FORMAT(user.gdoj,'%d-%b-%Y') as  gdoj,user.personal_email,user.email, IF(ed.officialmobile != '', 
       CONCAT(ed.cc_officialmobile, '-', ed.officialmobile), 
       CONCAT(ed.cc_personalmobile, '-', ed.personalmobile)
    ) AS mobile, user.hourly_rate,user.currency, user.emp_company_id, user.emp_division_id, user.emp_subdivision_id,emp_group_name,t.name as job_type,work_place, user.status, user.is_regular,user.date_created,user.user_height, user.blood_group,user.reporting_time,if(head.display_name!='',head.display_name,head.full_name) as division_head_name,user.division_head,
    
round(((DATEDIFF(ex.date,user.gdoj)-user.service_break_days)/365.25) + user.prev_aries,2)
as year_in_aries
    
from tbl_users user
left join  tbl_users  head on head.user_id=user.division_head
left join  tbl_emp_workplace w on w.id=user.work_location
left join  tbl_emp_type t on t.id=user.emp_type
left join  tbl_emp_group g on g.emp_group_id=user.group_id
left join  tbl_exit_interview_status ex on ex.user_id=user.user_id
left join  tbl_employee_contact_details ed on ed.user_id=user.user_id

where user.user_id=$user_id");





    $user_info_row = $user_info_query->fetch_assoc();
    $tree_sql = "SELECT if(display_name!='',display_name,full_name) as full_name,user_id  FROM  tbl_users WHERE  division_head='" . $user_id . "' and is_regular=1 and status='Active' order by full_name";
    $tree_result = $mysqli->query($tree_sql);


    $dimension_array = array();
    $dimension_result = $mysqli->query("SELECT * FROM tbl_dimensions");
    while ($dimension_row = $dimension_result->fetch_assoc()) {
        $id = $dimension_row['id'];
        $dimension_name = $dimension_row['short_name'];
        $dimension_array[$id] = $dimension_name;
    }
    
     $index_result = $mysqli->query("SELECT

 round(qi.plustwo+qi.diploma+ qi.ug+qi.pg+qi.computer+qi.othrs+ (round((datediff(ex.date,u.GDOJ)/365.25),2)*2)+ qi.relnt+(( qi.total- qi.relnt)/4),2) as emp_index




	FROM
       tbl_user_index as qi left join tbl_users as  u ON u.user_id = qi.user_id left join  tbl_exit_interview_status ex on ex.user_id=u.user_id  where qi.user_id=$user_id





");

    $index_row = $index_result->fetch_assoc();
    ?>
<table width="99%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
    <tr height="20" style="border:#a6107b solid 1px;">
        <td rowspan="6" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><img width="100px"
                src="images/employee/<?php echo $user_info_row['photo'] ?>">&nbsp;</td>
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name
                    <br>&nbsp;Employee Code
                </font>
            </b>
        </td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['full_name'] ?>&nbsp;
                    <br>&nbsp;<?php echo $user_info_row['employee_code'] ?>
                </font>
            </b>
        </td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Email</font>
            </b></td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;">&nbsp;<b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2"><a
                        href="mailto:<?php echo $user_info_row['email'] ?>"><?php echo $user_info_row['email'] ?></a>&nbsp;&nbsp;
                    <?php
                    if ($user_info_row['personal_email'] != $user_info_row['email']) {?>
                    <a href="mailto: <?php echo $user_info_row['personal_email'];?>">
                        &nbsp;<?php echo $user_info_row['personal_email'];?></a>
                    <?php   }
                    ?>
                </font>
            </b></td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Date of Birth</font>
            </b></td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['dob'] ?></font>
            </b></td>
        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>



    <tr height="20" style="border:#a6107b solid 1px;">
        <!--              <td  bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Employee Code</font></b></td>
            <td align="left"   style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;<?php echo $user_info_row['employee_code'] ?></font></b></td>-->
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Designation</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['designation'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Mobile</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['mobile'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Date of Joining</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['doj'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Hourly Rate</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['hourly_rate'] . " " . $user_info_row['currency'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">Gender</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['gender'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">
            <b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Group Joining Date
                </font>
            </b>
            <hr> <b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Years in Aries</font>:
            </b>
            <hr> <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Emp Index</font>:
            </b>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">
            <b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['gdoj'] ?></font>
            </b>
            <hr>
            <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                        &nbsp;<?php echo $user_info_row['year_in_aries'] ?></font>
            </b>
            <hr> <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $index_row['emp_index'] ?></font>
            </b>


        </td>


        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Comp/Div/SubDiv</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $dimension_array[$user_info_row['emp_company_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_division_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_subdivision_id']] ?>
                </font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Category</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $work_category_array[$user_info_row['work_category']] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Location</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['work_place'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Reporting To</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;<?php
                if ($my_division_head != $user_info_row['division_head']) {
                    ?>
            <b>
                <a href="javascript:click_tree(<?php echo $user_info_row['division_head'] ?>)"
                    style='text-decoration:underline;color:#0000FF' face='Verdana, Arial, Helvetica, sans-serif'
                    size='2'><?php echo $user_info_row['division_head_name'] ?></a></b>
            <?php
                } else {
                    ?>
            <b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['division_head_name'] ?></font>
            </b>
            <?php
                }
                ?>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;LAST DAY</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo date('d-m-Y',strtotime($user_info_row['date'])) ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Job Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['job_type'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Review Tree</font>
            </b></td>
        <td colspan="6" align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;

            <?php
                $count = 1;
                while ($tree_row = $tree_result->fetch_assoc()) {
                    echo "<b><a href='javascript:click_tree(" . $tree_row['user_id'] . ")'  style='text-decoration:underline;color:#0000FF;font-size:11px'  face='Verdana, Arial, Helvetica, sans-serif'>" . "" . $count++ . ")" . $tree_row['full_name'] . "</a></b>" . " ";
                }
                ?>

        </td>

    </tr>
</table>

<?php
}




function get_user_details_audit($user_id, $my_division_head,$date_report) {
    $work_category_array = array("1" => "Productive", "2" => "Semi  Productive", "3" => "Supporting");

    global $mysqli;
    
    $current_user_id=$_SESSION['user_id'];
    //************TO GET ACCESS USERS**********
    $sql_access = "SELECT GROUP_CONCAT(user_id) division_head_access FROM  tbl_users WHERE division_head='" . $current_user_id . "'";
    $access_query = $mysqli->query($sql_access);
    $access_row = $access_query->fetch_assoc();
    $user_under_division_head = $access_row['division_head_access'];
    if ($user_under_division_head == "")
        $user_under_division_head = 0;
        $user_acc = $user_under_division_head;
    if ($_SESSION['USER_ACCESS']['MAINMODULE'] != "")
        $user_acc = $user_acc . "," . $_SESSION['USER_ACCESS']['MAINMODULE'];
    //************TO GET ACCESS USERS ENDS**********
    
    
    
    //************For this month and last month efficiency average**********
 $select_year = date('Y',strtotime($date_report));
 $select_month = date('m',strtotime($date_report));
//  $last_month = date('m', strtotime(date($date_report)." -1 month"));
//  $last_year = date('Y', strtotime(date($date_report)." -1 month"));
 $last_month = date('m', strtotime(" first day of previous month"));
 $last_year = date('Y', strtotime(" first day of previous month"));
 $last_date = $last_year."-".$last_month."-01";
 
/* $sqleff = "SELECT month(date_log) as efmonth , ROUND(SUM(if(time_to_sec(addtime(addtime((addtime(addtime(TIMEDIFF(TIMEDIFF(time_out ,time_in),nwt),outside),night)),
home),leave_hours))>=21600 and current_efficiency>'0.10' and  work_status!='leave' and work_status!='holiday' and work_status!='Annual Leave' and work_status!='Splt Annual Leave',(current_efficiency*100), NULL)),2) as acteff,
 sum(if(time_to_sec(addtime(addtime((addtime(addtime(TIMEDIFF(TIMEDIFF(time_out ,time_in),nwt),outside),night)),home),leave_hours))>=21600 and  current_efficiency>='0.10' and   work_status!='leave' and work_status!='holiday' and work_status!='Annual Leave' and work_status!='Splt Annual Leave' and current_efficiency>='0.1',1, 0)) 
as count_days , no_health,unplan,not_punctual,tbl_users.full_name,d.short_name as emp_div_name,d1.short_name as emp_subdiv_name,tbl_users.user_id 
FROM tbl_time t left join tbl_users ON tbl_users.user_id=t.user_id left join tbl_dimensions d on d.id=tbl_users.emp_division_id
 left join tbl_dimensions d1 on d1.id=tbl_users.emp_subdivision_id where date_log>='$last_date' and date_log<='$date_report' and t.is_complete=1 
and (tbl_users.user_id ='$user_id') group by MONTH(date_log) order by year(date_log),month(date_log) asc  ";
$eff_result2 = $mysqli->query($sqleff);
$m=0;$arr_effc=array();
while($arr_eff = $eff_result2->fetch_assoc()){
    if($arr_eff['count_days']!=0){
 $arr_effc[$m]['eff'] = round($arr_eff['acteff']/$arr_eff['count_days'],2);
    $m++;}
}
if(!empty($arr_effc)){
     if(isset($arr_effc[1]['eff'])){
$this_month_efficiency = $arr_effc[1]['eff'] ;  $last_month_efficiency = $arr_effc[0]['eff'] ;}}

*/

//*****************End efficiency calculation************
    
    

$user_info_query = $mysqli->query("select  z.teams_id,user.photo,user.employee_code,user.hr_id, j.name as job_category,user.username,user.user_id,user.dob,
	user.work_category,if(user.display_name!='',user.display_name,user.full_name) as full_name, user.designation, user.gender,user.gdoj,user.original_dob , if(user.original_dob!='0000-00-00',DATE_FORMAT(user.original_dob,'%d-%b-%Y'),DATE_FORMAT(user.dob,'%d-%b-%Y')) as sdob,
ROUND(((DATEDIFF(now(),user.gdoj)-user.service_break_days)/365.25) + user.prev_aries,2) as year_in_aries,
duties.is_locked,sum(e.Basic_Salary+e.Food_Allowance+e.House_Allowance+e.Fixed_Overtime+e.Mobile_Allowance+e.Car_Allowance+e.Petrol_Allowance+e.Fixed_Incentive+e.Child_Education+e.Traveling_Allowance+e.Target_Allowance+e.Other_Allowance+e.Dearness_Allowance+e.conveyance+e.special_allowance4) as gross,

DATE_FORMAT(user.dob,'%d-%b-%Y') as dob, DATE_FORMAT(user.doj,'%d-%b-%Y') as doj,DATE_FORMAT(user.gdoj,'%d-%b-%Y') as  gdoj,user.personal_email,user.email, IF(ed.officialmobile != '', CONCAT(ed.cc_officialmobile, '-', ed.officialmobile), CONCAT(ed.cc_personalmobile, '-', ed.personalmobile)) AS mobile, user.hourly_rate,user.currency, user.emp_company_id, user.emp_division_id, user.emp_subdivision_id,emp_group_name,t.name as job_type,work_place, user.status, user.is_regular,user.date_created,user.user_height, user.blood_group,user.reporting_time,
if(head.display_name!='',head.display_name,head.full_name) as division_head_name,
user.division_head,user.service_break_days,ed.updated_date ,ed.is_complete   from tbl_users user
left join  tbl_users  head on head.user_id=user.division_head
LEFT JOIN 0_emp e ON user.hr_id = e.id
left join  tbl_emp_workplace w on w.id=user.work_location
left join  tbl_emp_category j on j.id=user.emp_category
left join  tbl_emp_type t on t.id=user.emp_type
left join  tbl_emp_group g on g.emp_group_id=user.group_id
left join  tbl_emp_duties_master duties on duties.user_id=user.user_id 
left join  tbl_teams_id z on z.user_id=user.user_id AND z.active=1
left join  tbl_employee_contact_details ed on ed.user_id=user.user_id 

where user.user_id=$user_id");


$user_info_row = $user_info_query->fetch_assoc();

    $tree_sql = "SELECT if(display_name!='',display_name,full_name) as full_name,user_id  FROM  tbl_users WHERE  division_head='" . $user_id . "' and is_regular=1 and status='Active' order by full_name";
    $tree_result = $mysqli->query($tree_sql);


    $dimension_array = array();
    $dimension_result = $mysqli->query("SELECT * FROM tbl_dimensions");
    while ($dimension_row = $dimension_result->fetch_assoc()) {
        $id = $dimension_row['id'];
        $dimension_name = $dimension_row['short_name'];
        $dimension_array[$id] = $dimension_name;
    }


    $index_result = $mysqli->query("SELECT round(qi.plustwo+qi.diploma+ qi.ug+qi.pg+qi.computer+qi.othrs+ ((round((datediff(now(),u.GDOJ)/365.25),2) + qi.prev_aries )*2)+ qi.relnt+(( qi.total- qi.relnt)/4),2) as emp_index




	FROM
       tbl_user_index as qi left join tbl_users as  u ON u.user_id = qi.user_id   where qi.user_id=$user_id





");

    $index_row = $index_result->fetch_assoc();
    
    $yrpass=0;$bday=0;
    $join_date = $user_info_row['gdoj'];
    $dob = $user_info_row['sdob'];
  //  $original_dob = $user_info_row['original_dob']; 
//   echo $dob = $original_dob!="0000-00-00"?$original_dob:$dob;
        if(!empty($join_date)){
         $curr_month = date("m");
        $curr_year = date("Y");
        $c_today = date("d");
         $join_month = date("m",strtotime($join_date));
        $join_day = date("d",strtotime($join_date));
        $join_year = date("Y",strtotime($join_date));
        $service_break_days = $user_info_row['service_break_days'];
        if($curr_month==$join_month&&$c_today==$join_day&&$curr_year!=$join_year&&$service_break_days==0){ 
            $years = $curr_year-$join_year;
            if($years==1){
            $yr="year";}else{ $yr="years";} 
            $chk_sql2 = $mysqli->query("SELECT * FROM tbl_observations WHERE audited_by=$current_user_id and user_id=$user_id and observation=22");//Added ON 26-4-22 TO HIDE THE BDAY MSG IF WISHED ALREADY
          if($chk_sql2->num_rows==0){
            $yrpass=1;}
            $empname = $user_info_row['full_name'];
        $compltedyears ="$empname is completing $years $yr with Aries today!!!";}
        
        }
       ////For year 
       //*****FOR BIRTHDAY*******//
       
       
           if(!empty($dob)){
         $curr_month = date("m");
        $curr_year = date("Y");
        $c_today = date("d");
         $birth_month = date("m",strtotime($dob));
        $birth_day = date("d",strtotime($dob));
       $currentdate = date('Y-m-d');
        if($curr_month==$birth_month&&$c_today==$birth_day){ 
           //   $years = $curr_year-$join_year;
           
          $chk_sql = $mysqli->query("SELECT * FROM tbl_observations WHERE audited_by=$current_user_id and user_id=$user_id and observation=21 AND audited_date= $currentdate");//Added ON 26-4-22 TO HIDE THE BDAY MSG IF WISHED ALREADY
          if($chk_sql->num_rows==0){
             $bday=1;}
            $empname = $user_info_row['full_name'];
        $birthday ="$empname is celebrating birthday today!!!";}
        
        }  
        //*****FOR BIRTHDAY ends*******//
        
        
    $sql_sel = "SELECT *,ROUND((SUM(ha.answer)/(COUNT(ha.answer)*10))*100) avg_level FROM tbl_happiness_index hi LEFT JOIN tbl_happiness_index_answer ha ON ha.user_id = hi.user_id WHERE hi.user_id=$user_id ";
	$result_cm = $mysqli->query($sql_sel);
	$row_total_happiness = $result_cm->fetch_assoc();
	
	 	if($row_total_happiness['avg_level']=="") {
												$happiness ="<b style='color:red;'>NOT ATTENDED</b>";
												$happiness2 ="<span style='font-size:100px;'>&#x1F44E;</span>";
											}
	 
						elseif($row_total_happiness['avg_level']<=20) {
												$happiness ="<b style='color:red;'>Disappointed</b>";
												$happiness2 ="<span style='font-size:100px;'>&#128546;</span>";
											}
											else if($row_total_happiness['avg_level']>=21 && $row_total_happiness['avg_level']<=40) {
												$happiness ="<b style='color:#f57e42;'>Not Happy</b>";
												$happiness2 ="<span style='font-size:100px;'>&#128543;</span>";
											}
											else if($row_total_happiness['avg_level']>=41 && $row_total_happiness['avg_level']<=60) {
												$happiness ="<b style='color:#f5da42;'>Neutral</b>";
												$happiness2 ="<span style='font-size:100px;'>&#128528;</span>";
											}
											else if($row_total_happiness['avg_level']>=61 && $row_total_happiness['avg_level']<=80) {
												$happiness ="<b style='color:#63f542;'>Happy</b>";
												$happiness2 ="<span style='font-size:100px;'>&#128578;</span>";
											}
											else if($row_total_happiness['avg_level']>=81 && $row_total_happiness['avg_level']<=100) {
												$happiness ="<b style='color:#266329;'>Very Happy";
												$happiness2 ="<span style='font-size:100px;'>&#128516;</span>";
											}
    ?>
<table width="99%" border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
    <tr height="20" style="border:#a6107b solid 1px;">
        <td rowspan="7" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><img width="100px"
                src="images/employee/<?php echo $user_info_row['photo'] ?>">&nbsp;
            <br><br><br>
            &nbsp;<a class='iframe1'
                href='../profile-completed.php?employee_id=<?php echo $user_info_row['user_id'] ?>'><input type="button"
                    class="btn btn-warning btn-xs btn-round" value="View Profile" style="font-weight:600;">
            </a>
            <?php //if($_SESSION['user_id']==103){?>
            <?php if($user_info_row['is_complete']==1){ ?>
            <br><br><button class="btn btn-success btn-xs ">Completed on
                <?php echo date('d-m-y',strtotime($user_info_row['updated_date'])) ?></button>
            <!--&nbsp;<label style="margin-top: 10px; padding: 4px;" class="alert alert-success"><span class="glyphicon glyphicon-ok"> </label>-->

            <?php } 
else if($user_info_row['is_complete']==2){ ?>
            <br><br>&nbsp;&nbsp;&nbsp;<button class="btn btn-info btn-xs ">Reopened </button>
            <!--&nbsp;<label style="margin-top: 10px; padding: 4px;" class="alert alert-success"><span class="glyphicon glyphicon-ok"> </label>-->

            <?php } 
else{ ?>
            <br><br>&nbsp;<button class="btn btn-danger btn-xs ">Not Completed</button>
            <?php }//}?>
        </td>

        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Name
                    <br>&nbsp;Employee Code
                </font>
            </b> </td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['full_name'] ?>&nbsp;
                    <br>&nbsp;<?php echo $user_info_row['employee_code'] ?>
                </font>
            </b><?php if($current_user_id==83||$current_user_id==19||$current_user_id==49||$current_user_id==48||$current_user_id==52||$current_user_id==1452||$current_user_id==3272||$current_user_id==10||$current_user_id==2||$current_user_id==38||$current_user_id==36 || $current_user_id==103 || $current_user_id==512 ){?>
            <a class='iframe1' href='employee_hr_data.php?user_id=<?php echo $user_id?>'><img
                    src='images/view_emp_details.png' title='View History' /></a><?php }?>
        </td>

        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Email</font>
            </b></td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;">&nbsp;<b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['email'] ?>&nbsp;&nbsp;
                    <?php
                    if ($user_info_row['personal_email'] != $user_info_row['email']) {
                        echo $user_info_row['personal_email'];
                    }
                    ?>
                </font>
            </b></td>
        <td rowspan="6" align="center" bgcolor="#e2f3fd" style="border:#000000 solid 1px;">

            <?php if($current_user_id==103||$current_user_id==1452){?>
            Happiness Level is </strong><?php print($happiness);?> (<?= round($row_total_happiness['avg_level'])?>%)
            <br /><?php print$happiness2;?><br />
            <?php if($row_total_happiness['avg_level']!=""){?>
            <a href="view-happiness-index.php?user_id=<?php echo $user_id;?>" class="iframe1"> Click here to view
                assessment</a></center>
            <?php }} else{  include_once("job_chart.php");} ?>

        </td>
        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Designation</font>
            </b>
            <br />
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['designation'] ?></font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Mobile</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['mobile'] ?></font>
            </b></td>
    </tr>

    <tr height="10" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Duties &
                    Responsibilities</font>
            </b>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">


            <a class='iframe1' href="dr_list.php?user_id=<?php echo $user_id?>">
                <input type="button" value="View D&R"></a>



            <!--<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>-->


            <!--<script src="js/jquery.colorbox.js"></script>-->
            <!--<script type="text/javascript">-->
            <!--    $(document).ready(function() {-->
            <!--        $(".iframe1").colorbox({-->
            <!--            iframe: true,-->
            <!--            width: "95%",-->
            <!--            height: "90%",-->
            <!--            transition: 'none'-->
            <!--        });-->
            <!--        $(".iframe").colorbox({-->
            <!--            iframe: true,-->
            <!--            width: "95%",-->
            <!--            height: "90%",-->
            <!--            transition: 'none'-->
            <!--        });-->
            <!--        $(".inline").colorbox({-->
            <!--            inline: true,-->
            <!--            width: "95%"-->
            <!--        });-->
            <!--    });-->
            <!--</script>-->







        </td>
        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Teams ID</font>
            </b></td>

        <td width="198" align="left" bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['teams_id'] ?>
                    <?php
                   /* if ($user_info_row['meeting_id'] !="") {
                        echo "<br>";
                        echo $user_info_row['meeting_id'];
                    }*/
                    ?>
                </font>
            </b></td>
    </tr>

    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Hourly Rate</font>
            </b></td>

        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['currency']." ". $user_info_row['hourly_rate']   ?></font>
            </b>
            <?php if($_SESSION['user_id']==103||$_SESSION['user_id']==36||$_SESSION['user_id']==1452||$_SESSION['user_id']==2)
           { ?>
            <br>
            <font color="#efac4d" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                <b> Gross Salary :
                    <?php echo $user_info_row['currency']." ".  number_format($user_info_row['gross']);    ?> </b>
            </font>
            <?php }?>
        </td>



        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Category</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $work_category_array[$user_info_row['work_category']] ?></font>
            </b></td>
        <!--<td width="4%" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Remainder</font></b></td>-->
    </tr>



    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Total Years in Aries
                </font>
            </b><br /><b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Qualification Index
                </font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['year_in_aries'] ?></font>
            </b><br />
            <b>
                <font color="#FF0000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php if(isset($index_row)) echo $index_row['emp_index'] ?></font>
            </b>
        </td>

        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Work Location</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['work_place'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Comp/Div/SubDiv</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $dimension_array[$user_info_row['emp_company_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_division_id']] ?>&nbsp;&nbsp;<?php echo $dimension_array[$user_info_row['emp_subdivision_id']] ?>
                </font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Employee Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>&nbsp;</b></font><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['job_type'] ?></font>
            </b></td>
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Reporting To</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;<?php 
                if ($my_division_head != $user_info_row['division_head']&&str_contains($user_acc,  $user_info_row['division_head'])) {
                   
                    ?>
            <b>
                <a href="javascript:click_tree(<?php echo $user_info_row['division_head'] ?>)"
                    style='text-decoration:underline;color:#0000FF' face='Verdana, Arial, Helvetica, sans-serif'
                    size='2'><?php echo $user_info_row['division_head_name'] ?></a></b>
            <?php
                } else {
                    ?>
            <b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php echo $user_info_row['division_head_name'] ?></font>
            </b>
            <?php
                }
                ?>
        </td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Reporting Time</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd"><b>
                <font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    &nbsp;<?php echo $user_info_row['reporting_time'] ?></font>
            </b></td>
        <!--<td align="center" height="40"  style="border:#000000 solid 1px;" bgcolor="#f5e0a6"><b><font color="#000000" face="Verdana, Arial, Helvetica, sans-serif" >
             <?php if(isset($last_month_efficiency)){?>
            <span style="margin-bottom:5px"></span> &nbsp;Last Month Efficiency : <font color="green"><?=$last_month_efficiency?>% </font>
            <?php }?>
            <?php if(isset($this_month_efficiency)){?>
            |&nbsp; Current Month Efficiency : <font color="red"><?=$this_month_efficiency?>%</font></span></font></b>
              <?php if($this_month_efficiency>$last_month_efficiency){?>
            <img src="images/efhappy5.png" width=18 title="Efficiency is improved"> 
            <?php } else{?>
            <img src="images/efsad5.png" width=18 title="Efficiency is decreased">
            <?php }?>
            <?php }?>
            </td>-->
    </tr>
    <tr height="20" style="border:#a6107b solid 1px;">
        <td bgcolor="#e2f3fd" style="border:#000000 solid 1px;"><b>
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2">&nbsp;Review Tree</font>
            </b></td>
        <td colspan="5" align="left" style="border:#000000 solid 1px;" bgcolor="#e2f3fd">&nbsp;

            <?php
                $count = 1;
                while ($tree_row = $tree_result->fetch_assoc()) {
                    echo "<b><a href='javascript:click_tree(" . $tree_row['user_id'] . ")'  style='text-decoration:underline;color:#0000FF;font-size:11px'  face='Verdana, Arial, Helvetica, sans-serif' style='font-size:11px' >" . "" . $count++ . ")" . $tree_row['full_name'] . "</a></b>" . " ";
                }
                ?>

        </td>

    </tr>
    <?php if($yrpass==1&&$current_user_id!=$user_id){ ?>
    <tr style="border:#a6107b solid 1px;">
        <td colspan="6" bgcolor="#e2f3fd">
            <img src="images/award.gif" width="50" height="50"><i
                style="font-size:18px;color: #9C4DEA"><?php echo $compltedyears?></i>&nbsp;&nbsp;<button type="button"
                class="btn btn-warning" data-toggle="modal" data-target="#congModal">
                Say Congrats
            </button>
        </td>
    </tr><?php }?>

    <?php if($bday==1&&$current_user_id!=$user_id){ ?>
    <tr style="border:#a6107b solid 1px;">
        <td colspan="6" bgcolor="#e2f3fd">
            <img src="images/bday.gif" width="50" height="50"><i
                style="font-size:18px;color: #E33DB2"><?php echo $birthday?></i>&nbsp;&nbsp;<button type="button"
                class="btn btn-warning" data-toggle="modal" data-target="#wishesModal">
                Send Wishes
            </button>
        </td>
    </tr><?php }?>



</table>

<?php
}

function get_user_details($user_id) {
    global $mysqli;
   //echo "select  user.username,is_lock,user.full_name,user.division_head  from tbl_users user where user.user_id=$user_id";
    $user_info_query = $mysqli->query("select  user.username,is_lock,if(user.display_name!='',user.display_name,user.full_name) as  full_name,user.division_head  from tbl_users user where user.user_id=$user_id");
    $user_info_row = $user_info_query->fetch_assoc();

    return $user_info_row;
}

function get_user_details_complete($user_id, $field_array) {

    $fields = implode(",", $field_array);

    $field = ($fields != "") ? $fields : "*";

    global $mysqli;
    $user_info_query = $mysqli->query("select  " . $field . " from tbl_users user where user.user_id=$user_id");
    $user_info_row = $user_info_query->fetch_assoc();

    return $user_info_row;
}

function get_database_table_details($table_name, $condition,  string $field_name = "*") {

    global $mysqli;
    $query = $mysqli->query("select  $field_name  from $table_name where  $condition");
    $row = $query->fetch_assoc();
    return $row;
}

function Notify($notify_by, $notify_id, $remarks) {
    global $mysqli;
    $sql = "INSERT INTO tbl_notification(notification_id,notification,user_id,requested_by)
						VALUES('$notify_id','$remarks','$notify_by','" . $_SESSION['user_id'] . "') ";
    $mysqli->query($sql);
}

function get_user_permissions($user_id, string $module=NULL) {
    global $mysqli;
     $perm_sql = "SELECT * FROM  tbl_user_access where user_id=$user_id";
    if ($module != "")
        $perm_sql .= " and  module_access='$module'";
    $perm_result = $mysqli->query($perm_sql);
    $access_array = array();
    while ($user_row = $perm_result->fetch_assoc()) {
        $module = $user_row['module_access'];
        if ($user_row['group_access'] != "")
            $access_array[$module]['group_access'] = $user_row['group_access'];
        if ($user_row['user_access'] != "")
            $access_array[$module]['user_access'] = $user_row['user_access'];

        if ($user_row['emp_location_access'] != "")
            $access_array[$module]['emp_location_access'] = $user_row['emp_location_access'];
        if ($user_row['emp_division_access'] != "")
            $access_array[$module]['emp_division_access'] = $user_row['emp_division_access'];
        if ($user_row['emp_location_not_access'] != "")
            $access_array[$module]['emp_location_not_access'] = $user_row['emp_location_not_access'];
    }
    return $access_array;
}

function get_lock_type($type_id) {
    global $mysqli;
    $lock_sql = "SELECT type,mail_needed FROM  tbl_lock_types  where id=$type_id";

    $lock_result = $mysqli->query($lock_sql);
    $lock_row = $lock_result->fetch_assoc();
    return $lock_row;
}
function self_reviewed_to($user_id) {
    global $mysqli;

    $sql1 = "SELECT  l.page_type,jobdiary_date,DATE_FORMAT(log_date,'%h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name from tbl_employee_log l  "
            . " left join  tbl_users u on u.user_id=l.access_id  where l.user_id=$user_id  and action='Self Assessment Review'"
            . " and access_id>0 and access_id!=l.user_id order by l.id ";

    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_to" style="border-collapse: collapse;" width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">


    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">For the Month</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Time</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Subordinate Name</font>
            </b></td>
    </tr>


    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo date("M-Y",strtotime( $row6['jobdiary_date'])); ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo date("d-M-Y",strtotime($row6['jobdiary_date']))."--". $row6['log_time']; ?></b>
            </font>
        </td>

        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>

    <?php
            $count++;
        }
        ?>
</table>
<br>

<?php
}
function self_observation_to($type, $user_id, $plan_month, $plan_year, $date_report) {
    global $mysqli;

    $sql_fetch = "select o.remarks,
DATE_FORMAT(o.audited_date,'%d/%m/%Y') as audit_date,
if(u.display_name!='',u.display_name,u.full_name) as full_name,
if(u1.display_name!='',u1.display_name,u1.full_name) as  subordinate_name ,
if(o.subordinate_id>0,concat(t.type,' to ',if(u1.display_name!='',u1.display_name,u1.full_name)),t.type)  as  type ,
o.observation,

t.image,
o.user_remarks,
o.notified,
o.audited_by
from tbl_observations o
left join tbl_observation_types t
on t.id=o.observation
left join tbl_users u
on u.user_id=o.user_id
left join tbl_users u1
on u1.user_id=o.subordinate_id

where o.audited_by=" . $user_id;

        $sql_fetch .= " and o.type='Self Assessment Review'";
    

    $sql_fetch .= " order by o.audited_date desc";

    //echo $sql_fetch;
    $mysql1 = $mysqli->query($sql_fetch);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="observation_to" style="border-collapse: collapse;" width="98%" align="center"
    border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="12" align="center">
            <div style="display: block;">
                <font color="#ff0000">&nbsp;<b></b></font>
            </div>
        </td>
    </tr>

    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">

        <td align="left" style="border:#000000 solid 1px;">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Reviewed To</b></font>
        </td>
        <!--<td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Rating</b></font></td>-->
        <td align="center" style="border:#000000 solid 1px;">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> Date</b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Remarks</b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>User Response</b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notified</b></font>
        </td>
    </tr>


    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="30" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

        <td align="left" style="border:#000000 solid 1px;">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo $row6['full_name']; ?></b></font>
        </td>

        <!--                <td style="border-left-style:hidden">
                    <?php if (isset($row6['type'])) {
                        ?>
                        <img src="images/ratings/<?php echo $row6['image'] ?>">
                    <?php } ?>
                </td>-->
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo $row6['audit_date']; ?></b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo nl2br($row6['remarks']); ?></b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <?php echo nl2br($row6['user_remarks']); ?></b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <?php
                    if ($row6['notified'] == 1) {
                        ?>
            <img src="images/tick.png" />
            <?php } ?>
        </td>
    </tr>

    <?php
            $count++;
        }
        ?>
</table>
<?php
}
function observation_self_assessment($type, $user_id, $plan_month, $plan_year, $date_report) {
    global $mysqli;

    $sql_fetch = "select o.remarks,
DATE_FORMAT(o.audited_date,'%d/%m/%Y') as audit_date,
if(u.display_name!='',u.display_name,u.full_name) as full_name,
if(u1.display_name!='',u1.display_name,u1.full_name) as  subordinate_name ,
if(o.subordinate_id>0,concat(t.type,' to ',if(u1.display_name!='',u1.display_name,if(u1.display_name!='',u1.display_name,u1.full_name))),t.type)  as  type ,
o.observation,
j.taskname,
t.image,
o.user_remarks,
o.notified,
o.audited_by
from tbl_observations o
left join tbl_observation_types t
on t.id=o.observation
left join tbl_users u
on u.user_id=o.audited_by
left join tbl_users u1 on u1.user_id=o.subordinate_id
left join tbl_job_observation j on j.observation_id=o.id

where o.user_id=" . $user_id;



 
   
      $sql_fetch .= " and o.type='$type'";  
   
    
   

    $sql_fetch .= " order by o.audited_date desc";

//echo $sql_fetch;
    $mysql1 = $mysqli->query($sql_fetch);
    ?>
<div id="remarks_by">
    <table id="customers_time" class="observation" style="border-collapse: collapse; " width="98%" align="center"
        border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td colspan="13" align="center">
                <div style="display: block;">
                    <font color="#ff0000">&nbsp;<b></b></font>
                </div>
            </td>
        </tr>

        <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">

            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Reviewed By</b></font>
            </td>
            <!--<td colspan="4" align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Rating</b></font></td>-->
            <td align="center" style="border:#000000 solid 1px;">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b> Date</b></font>
            </td>
            <!--<td align="left" style="border:#000000 solid 1px;text-indent:5px"><b><font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Job</font></b></td>-->
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Remarks</b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>User Response</b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notified</b></font>
            </td>
        </tr>


        <?php
            $count = 0;
            while ($row6 = $mysql1->fetch_assoc()) {
                if ($count % 2 == 0)
                    $bgcolor = '#e9e9e9';
                else
                    $bgcolor = '#f2f2f2';
                ?>
        <tr height="30" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">

            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['full_name']; ?></b></font>
            </td>


            <td align="left" style="border:#000000 solid 1px;">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['audit_date']; ?></b></font>
            </td>
            <!--<td align="left" style="border:#000000 solid 1px;text-indent:5px"><font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b><?php echo $row6['taskname']; ?></b></font></td>-->
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['remarks']; ?></b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                        <?php echo $row6['user_remarks']; ?></b></font>
            </td>
            <td align="left" style="border:#000000 solid 1px;text-indent:5px">
                <?php
                        if ($row6['notified'] == 1) {
                            ?>
                <img src="images/tick.png" />
                <?php } ?>
            </td>
        </tr>

        <?php
                $count++;
            }
            ?>
    </table>
</div>
<?php
}
function rating_table_kpi($user_id, $current_user_id, $complete_flag) {
//  echo $user_id. $current_user_id. $complete_flag; ?>

<?php
    if ($user_id != $current_user_id) {
        ?>
<br>
<table style="border-collapse: collapse;" width="85%" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr height="25" style="border:#a6107b solid 1px;background-color:#008080;color:#ffffff;">
        <td colspan="10" align="center"><b>Review Findings</b></td>
    </tr>

    <tr style="border:#a6107b solid 1px;background-color:#ffffff">
        <td colspan="6" align="center">

            <b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2">
                    <?php // if ($complete_flag != 1) { ?>
                    <!--<input type="radio" name="observation" value="11"><font style="color:#FF0000">Change The Plan</font>-->
                    <?php // } ?>
                    <input type="radio" name="observation" value="7">Excellent&nbsp;
                    <input type="radio" name="observation" value="6">Very Good&nbsp;
                    <input type="radio" name="observation" value="5">Good&nbsp;
                    <input type="radio" name="observation" value="4">Need Improvement
                    <input type="radio" name="observation" value="3">&nbsp;Poor
                    <input id="obs" type="radio" name="observation" value="1">&nbsp;Observation
                    <input id="ncr" type="radio" name="observation" value="2">N.C.R
                </font>
            </b>
        </td>
        <td colspan="2" align="center"><b>
                <font face="Verdana, Arial, Helvetica, sans-serif" size="2"> Remarks:</font>
            </b></td>
        <td colspan="2" align="left">
            <textarea style="border:2px solid skyblue;margin:10px;" id="remarks" name="remarks" rows="5"
                cols="30"></textarea>
        </td>
    </tr>
    <tr height="25" style="border:#a6107b solid 1px;background-color:#e9e9e9">
        <td colspan="10" align="center">
            <input class="jobdiary_buttons" type="submit" onclick="return validate();" name="submit_audit"
                value="Submit" />
        </td>

    </tr>
</table>
<?php
    }
}
function thisMonth_kpi_audited_to($user_id, $month, $year) {
    global $mysqli;

    $sql1 = "SELECT  remarks,action,page_type, DATE_FORMAT( jobdiary_date, '%M' ) month,DATE_FORMAT(log_date,'%d/%m/%Y %h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name
  			from tbl_employee_log l left join  tbl_users u on u.user_id=l.access_id
  			where l.user_id=$user_id and action = 'KPI Plan Review' and ((MONTH(log_date) = '$month' AND YEAR(log_date) = '$year') || (MONTH(jobdiary_date) = '$month' AND YEAR(jobdiary_date) = '$year'))   and l.access_id>0 and l.access_id!=l.user_id order by date(log_date) DESC limit 10";



    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_to" style="border-collapse: collapse; " width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="8" align="center">
            <div style="display: block;">
                <font color="#ff0000;" size="+1"><strong>Last Five Reviews To</strong>&nbsp;</font>
            </div>
        </td>
    </tr>
    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed Month</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed On</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Subordinate Name</font>
            </b></td>
    </tr>
    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['month']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                        <b><?php echo $row6['log_time']; ?></b>
                    </font>
                </b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>
    <?php
            $count++;
        }
        ?>
</table>
<br>
<?php
}
function thisMonth_kpi_audited($user_id, $month, $year) {
    global $mysqli;

    $sql1 = "SELECT  remarks,action,page_type, DATE_FORMAT( jobdiary_date, '%M' ) month,DATE_FORMAT(log_date,'%d/%m/%Y %h:%i %p') as log_time,if(u.display_name!='',u.display_name,u.full_name) as full_name from tbl_employee_log l left join  tbl_users u on u.user_id=l.user_id  where access_id=$user_id and action = 'KPI Plan Review' and ((MONTH(log_date) = '$month' AND YEAR(log_date) = '$year') || (MONTH(jobdiary_date) = '$month' AND YEAR(jobdiary_date) = '$year')) and l.user_id>0 and access_id!=l.user_id order by date(log_date) DESC limit 10";



    $mysql1 = $mysqli->query($sql1);
    if ($mysql1->num_rows == 0)
        return false;
    ?>
<table id="customers_time" class="reviewed_by" style="border-collapse: collapse;" width="90%" align="center" border="0"
    cellpadding="0" cellspacing="0">
    <tr>
        <td colspan="8" align="center">
            <div style="display: block;">
                <font color="#ff0000;" size="+1"><strong>Last Five Reviews By</strong>&nbsp;</font>
            </div>
        </td>
    </tr>
    <tr height="30" style="border:#a6107b solid 1px; background-color:#e2f3fd">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Type</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed Month</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Reviewed On</font>
            </b></td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px"><b>
                <font color="#a6107b" size="2" face="Verdana, Arial, Helvetica, sans-serif">Superior Name</font>
            </b></td>
    </tr>
    <?php
        $count = 0;
        while ($row6 = $mysql1->fetch_assoc()) {
            if ($count % 2 == 0)
                $bgcolor = '#e9e9e9';
            else
                $bgcolor = '#f2f2f2';
            ?>
    <tr height="20" style="border:#a6107b solid 1px; background-color:<?php echo $bgcolor ?>">
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['page_type']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['month']; ?></b>
            </font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>
                    <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                        <b><?php echo $row6['log_time']; ?></b>
                    </font>
                </b></font>
        </td>
        <td align="left" style="border:#000000 solid 1px;text-indent:5px">
            <font color="#0000FF" face="Verdana, Arial, Helvetica, sans-serif" size="1">
                <b><?php echo $row6['full_name']; ?></b>
            </font>
        </td>
    </tr>
    <?php
            $count++;
        }
        ?>
</table>
<br>
<?php
}
/* function for updating  manhour date based on category */

/* function for updating  manhour date based on category */

function updateManhourRate($user_id, $date,$mysqli) {
    /*
	$sqlsel = " SELECT s.user_id, s.workreport_id, s.row_id, s.job_no, s.main_type, s.job_type, s.job_no, s.taskname, s.description, s.client, s.so_order_id, s.act_time
				FROM tbl_workreports s
				LEFT JOIN tbl_users u
				ON s.user_id = u.user_id
				WHERE s.date_report='".$date."'
				AND s.user_id ='".$user_id."'
				AND s.act_time !='00:00:00'
				AND s.job_no !=''
				AND u.emp_division_id =3";
			
			$result_job = $mysqli->query($sqlsel);
	
	  $sql_rate = "SELECT r.manhr_rate , r.currency ,u.resource_category, u.work_location
	FROM tbl_users u
	LEFT JOIN tbl_manhr_rate r
	ON u.resource_category = r.category_id
	AND u.work_location = r.location_id
	WHERE r.is_current=1
	AND u.user_id ='".$user_id."'";

	$result_rate = $mysqli->query($sql_rate);

	$row_rate = $result_rate->fetch_assoc(); 
	
	$manhr_rate = $row_rate['manhr_rate'];
	$currency = $row_rate['currency'];
	$category_id = $row_rate['resource_category'];
	$work_location = $row_rate['work_location'];


	while($row_job = $result_job->fetch_assoc()) {

		 $sql = " SELECT id FROM tbl_work_manhr_rate WHERE workreport_id=".$row_job['workreport_id'].
			" AND row_id=".$row_job['row_id'];
		$result_data = $mysqli->query($sql);

		$row_data = $result_data->fetch_assoc();


		if(empty($row_data['id'])) {
			 $sql_ins = " INSERT INTO tbl_work_manhr_rate(user_id, date, workreport_id, row_id, hr_rate, currency, main_type, sub_type, job_no, task_name, description,  act_time, client, so_order_id, category_id, work_location)
						VALUES('".$user_id."', '".$date."','".$row_job['workreport_id']."','".$row_job['row_id']."','".$manhr_rate."','".$currency."','".$row_job['main_type']."','".$row_job['job_type']."', '".$row_job['job_no']."', '".$row_job['taskname']."','".$row_job['description']."','".$row_job['act_time']."','".$row_job['client']."', '".$row_job['so_order_id']."', '".$category_id."', '".$work_location."')";
			$mysqli->query($sql_ins);
		}
		else {
			 $sql_upd = " UPDATE tbl_work_manhr_rate 
			SET hr_rate='".$manhr_rate."',
			currency = '".$currency."',
			main_type = '".$row_job['main_type']."',
			sub_type = '".$row_job['job_type']."', 
			job_no = '".$row_job['job_no']."', 
			task_name = '".$row_job['task_name']."', 
			description = '".$row_job['description']."', 
			act_time  = '".$row_job['act_time']."', 
			
			client  = '".$row_job['client']."', 
			so_order_id  = '".$row_job['so_order_id']."', 
			
			categry_id  = '".$category_id."', 
			work_location  = '".$work_location."'
			WHERE user_id='".$user_id."'
			AND date='".$date."'
			AND workreport_id = '".$row_job['workreport_id']."'
			AND row_id = '".$row_job['row_id']."'";

			$mysqli->query($sql_upd);

		}
	}*/
	
	
	
	
}
function croneJobresourceupdate($user_id, $date,$mysqli) {
	    $sqlsel = " SELECT s.user_id, s.workreport_id, s.row_id, s.job_no, s.main_type, s.job_type, s.job_no, s.taskname, s.description, s.client, s.so_order_id, s.act_time
				FROM tbl_workreports s
				LEFT JOIN tbl_users u
				ON s.user_id = u.user_id
				WHERE s.date_report='".$date."'
				AND s.user_id ='".$user_id."'
				AND s.act_time !='00:00:00'
				AND s.job_no !=''
				AND u.emp_division_id =3";
			
			$result_job = $mysqli->query($sqlsel);
	
	  $sql_rate = "SELECT r.manhr_rate , r.currency ,u.resource_category, u.work_location
	FROM tbl_users u
	LEFT JOIN tbl_manhr_rate r
	ON u.resource_category = r.category_id
	AND u.work_location = r.location_id
	WHERE r.is_current=1
	AND u.user_id ='".$user_id."'";

	$result_rate = $mysqli->query($sql_rate);

	$row_rate = $result_rate->fetch_assoc(); 
	
	$manhr_rate = $row_rate['manhr_rate'];
	$currency = $row_rate['currency'];
	$category_id = $row_rate['resource_category'];
	$work_location = $row_rate['work_location'];


	while($row_job = $result_job->fetch_assoc()) {

		 $sql = " SELECT id FROM tbl_work_manhr_rate WHERE workreport_id=".$row_job['workreport_id'].
			" AND row_id=".$row_job['row_id'];
		$result_data = $mysqli->query($sql);

		$row_data = $result_data->fetch_assoc();


		if(empty($row_data['id'])) {
			 print $sql_ins = " INSERT INTO tbl_work_manhr_rate(user_id, date, workreport_id, row_id, hr_rate, currency, main_type, sub_type, job_no, task_name, description,  act_time, client, so_order_id, category_id, work_location)
						VALUES('".$user_id."', '".$date."','".$row_job['workreport_id']."','".$row_job['row_id']."','".$manhr_rate."','".$currency."','".$row_job['main_type']."','".$row_job['job_type']."', '".$row_job['job_no']."', '".$row_job['taskname']."','".$row_job['description']."','".$row_job['act_time']."','".$row_job['client']."', '".$row_job['so_order_id']."', '".$category_id."', '".$work_location."')";
			$mysqli->query($sql_ins);
		}
		else {
			 echo $sql_upd = " UPDATE tbl_work_manhr_rate 
			SET hr_rate='".$manhr_rate."',
			currency = '".$currency."',
			main_type = '".$row_job['main_type']."',
			sub_type = '".$row_job['job_type']."', 
			job_no = '".$row_job['job_no']."', 
			task_name = '".$row_job['task_name']."', 
			description = '".$row_job['description']."', 
			act_time  = '".$row_job['act_time']."', 
			
			client  = '".$row_job['client']."', 
			so_order_id  = '".$row_job['so_order_id']."', 
			
			categry_id  = '".$category_id."', 
			work_location  = '".$work_location."'
			WHERE user_id='".$user_id."'
			AND date='".$date."'
			AND workreport_id = '".$row_job['workreport_id']."'
			AND row_id = '".$row_job['row_id']."'";
			
			echo "<br>";

			$mysqli->query($sql_upd);

		}
	    
	}
}