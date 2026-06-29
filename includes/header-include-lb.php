<?php
include("connect.inc.php");
$current_user_id = $header_current_user_id= $_SESSION['user_id'];$todaytt=date('Y-m-d');
$division_current_value = isset($division_value[$current_user_id]) ? $division_value[$current_user_id] : 0;
$div = $_SESSION['emp_division_id'];
$_SESION['scroll'] = "";
$scroll_sql = $mysqli->query("SELECT * FROM  tbl_autoscroll where active_till>='$todaytt' and status=1 ORDER BY id DESC");
$scroll_row = $scroll_sql->fetch_assoc();
if(!empty($scroll_row)){
$_SESION['scroll'] = html_entity_decode($scroll_row['content']); }
//query to fetch observations from the table of current user
 $sql_obs = "select DATE_FORMAT(DATE(o.audited_date),'%d/%m/%Y') as audit_date,dm.dm_title,
o.report_month,
o.report_year,
DATE_FORMAT(o.report_date,'%d/%m/%Y') as reportdate,
o.observation,
j.taskname, 
o.id,
j.job_observation_id,
o.remarks,o.ob_type,
o.type,t.image,
o.satisfied,
o.satisf_remarks,
u.full_name,cat.oc_name,
u1.full_name as subordinate_name ,
if(o.subordinate_id>0,concat(t.type,' to ',u1.full_name),t.type)  as obs_type
from tbl_observations o
left join tbl_users u
on u.user_id=o.audited_by
left join tbl_users u1
on u1.user_id=o.subordinate_id
left join tbl_observation_types t
on t.id=o.observation
left join tbl_job_observation j
on j.observation_id=o.id
left join tbl_observation_category cat on cat.oc_id=o.ob_type
left join tbl_digital_marketing_main dm
on dm.dm_id=o.dm_id

where o.notified=0 and o.observation!=11
and (u.status='Active' or u.user_id=2543) and o.user_id=".$_SESSION['user_id']." and o.is_current=1 ";
$mysql_obs = $mysqli->query($sql_obs);
 $sql = "SELECT n.*,u.full_name FROM tbl_notification n INNER JOIN tbl_users u ON n.requested_by=u.user_id WHERE n.user_id = '".$_SESSION['user_id']."' AND n.notified = 0";
$notify_petty = $mysqli->query($sql);
$sql = "SELECT j.*,u2.full_name old_apprv_by,u.*,m.main_type_name,s.job_type_name FROM tbl_daily_jobs j left join tbl_users u on u.user_id=j.user_id left join tbl_main_type m on j.main_type=m.main_type_id
 left join tbl_job_type s on s.id=j.sub_type  left join tbl_users u2 on u2.user_id=j.approved_by"
        . " WHERE u.parent_id = '".$_SESSION['user_id']."' AND u.status='Active' and (is_read=0 or j.approved_by!='".$_SESSION['user_id']."') and (approved_by!=0 or approved_by!=2)";
$routine_notify = $mysqli->query($sql);//if($current_user_id==103)echo $sql;
?>
<!--===========================FreiChatX=======END=========================-->
<link rel='stylesheet' type='text/css' href='styles1.css' />
<link href = "css/bootstrap.css" rel = "stylesheet" type = "text/css">
<link href = "css/font-awesome.min-4.7.0.css" rel = "stylesheet" type = "text/css">

<link rel='stylesheet' type='text/css' href='./new_design/css/style.css' />
<script type="text/javascript" src="js/popup.js"></script>
<script type="text/javascript" src="./new_design/js/header_new.js"></script>
<style>
    /* Menu Dropdown */
    .cf:before,.cf:after {content: " ";display: table;}.cf:after { clear: both;}.cf {*zoom: 1;}
    .menu,.submenu {margin: 0;padding: 0;list-style: none;}
    .menu {  margin: 1px auto;min-width: 1000px;
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
    
    .sticky {
  position: fixed;
  top: 0;
  width: 100%;
}

.sticky + .content {
  padding-top: 110px;
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
        width: 430px;
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
<style type="text/css">
	  		.my-alert {
				 position:fixed;
   top:55px;
   right:0;
   width:800px;
				z-index:1;
			}
	  </style>
<tr>
    <td  align="center" valign="top" width="100%">
        <table background="" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top:0px;">
            <tr><td style="width:30%;" background="">
                 <!--<img src="images/log.png" height="70" alt="Aries25">&nbsp;-->
                   <img src="images/aries-25.gif" height="70" alt="Aries25">&nbsp;
                <!--<img style="margin-left:30px;" width="105px;" src="images/time.png" >&nbsp;-->
                <img width="105px;" src="images/Effism.png" >
                </td>
                <td align="left" style="width:45%;text-shadow: 0px 3px 1px skyblue;color: #003333;font: 80px 'LeagueGothicRegular';font-size:18px;font-weight:bold;">ONLINE EFFICIENCY IMPROVEMENT TOOL
                    <?php  $notify=1; 
                    $notiCount=0;
                    if($_SESSION['yrpass'] == 1) {$notiCount=$notiCount+1;}
                    if($_SESSION['bday'] == 1) {$notiCount=$notiCount+1;}
                    if(($_SESSION['yrpass']==1)||$_SESSION['bday'] == 1){ ?>


                    <a href="javascript:display_notification()"  title="You have <?=$notiCount?> notifications. Click here to view">         <div class="notification-box">
                        <span class="notification-count"><?=$notiCount?></span>
                        <div class="notification-bell">
                            <span class="bell-top"></span>
                            <span class="bell-middle"></span>
                            <span class="bell-bottom"></span>
                            <span class="bell-rad"></span>
                        </div>
                        </div></a>
                    <?php }?>
                </td>


                <!--///*********STYLE FOR NOTIFICATION**************///-->            
                <style>

                    .notification-box {
                        position: fixed;
                        z-index: 99;
                        top: 12px;
                        right: 32%;
                        width: 50px;
                        height: 50px;
                        text-align: center;
                    }
                    .notification-bell {
                        animation: bell 1s 1s both infinite;
                    }
                    .notification-bell * {
                        display: block;
                        margin: 0 auto;
                        background-color: #FFB910;
                        box-shadow: 0px 0px 15px #F8D84E;
                    }
                    .bell-top {
                        width: 6px;
                        height: 6px;
                        border-radius: 3px 3px 0 0;
                    }
                    .bell-middle {
                        width: 25px;
                        height: 25px;
                        margin-top: -1px;
                        border-radius: 12.5px 12.5px 0 0;
                    }
                    .bell-bottom {
                        position: relative;
                        z-index: 0;
                        width: 32px;
                        height: 2px;
                    }
                    .bell-bottom::before,
                    .bell-bottom::after {
                        content: '';
                        position: absolute;
                        top: -4px;
                    }
                    .bell-bottom::before {
                        left: 1px;
                        border-bottom: 4px solid #fff;
                        border-right: 0 solid transparent;
                        border-left: 4px solid transparent;
                    }
                    .bell-bottom::after {
                        right: 1px;
                        border-bottom: 4px solid #fff;
                        border-right: 4px solid transparent;
                        border-left: 0 solid transparent;
                    }
                    .bell-rad {
                        width: 8px;
                        height: 4px;
                        margin-top: 2px;
                        border-radius: 0 0 4px 4px;
                        animation: rad 1s 2s both infinite;
                    }
                    .notification-count {
                        position: absolute;
                        z-index: 1;
                        top: -6px;
                        right: -6px;
                        width: 30px;
                        height: 30px;
                        line-height: 30px;
                        font-size: 18px;
                        border-radius: 50%;
                        background-color: #ff4927;
                        color: #fff;
                        animation: zoom 3s 3s both infinite;
                    } 
                </style>
                <!--///*********STYLE FOR NOTIFICATION ENDS**************///-->  

                <td style="width:30%;vartical-align:top;" align="right" valign="top">

                    <ul id="head_menu" class="menu cf" style="min-width:390px;">
                        <?php if($_SESSION['user_id']==36 ||$_SESSION['user_id']==103|| $_SESSION['user_id']==1452){?>
                        <li style='background-color:#c43c35;'><a href="beta/dashboard.php">Dashboard</a></li>
                        <?php } ?>
                        <li style='background-color:#c43c35;'><a href="profile.php">Change Password</a></li>

                        <?php if ($_SESSION['is_lock'] == 1) {?>
                        <li><a href="lock.php">UnLock</a></li><?php } else {?>
                        <li><a href="lock.php">Lock</a></li><?php }?>
                        <li><a href="it_log_register.php">Register Complaint</a></li>
                        <li><a href="appreciation-multi.php"  >Appreciation</a></li>

                        <li><a href="profile.php" style="padding:3px 4px 2px 3px;">
                            <img style="width:34px;" src="https://effism.com/images/employee/<?=$_SESSION['photo']?>" alt=""></a>
                            <!--                            <ul class="submenu" style="width:100px;">
<li><a style="padding:3px;" href='profile.php'>Profile</a></li>
</ul>-->
                        </li>
                        <li>
                        </li>
                    </ul>

                    <!--                    <table id="topmenu" style="background:#34495e;margin-top:0px;padding:2px;width:20% !important;" border="0" cellspacing="0" cellpadding="0">
<tr>
<td style="width:30px;border:0px;border-right:1px solid #fff;"><?php //if (($_SESSION['user_id'] == 2) || ($_SESSION['user_id'] == 1144) || ($_SESSION['user_id'] == 10) || ($_SESSION['user_id'] == 19) || ($_SESSION['user_id'] == 36) || ($_SESSION['user_id'] == 38) || ($_SESSION['user_id'] == 48) || ($_SESSION['user_id'] == 49) || ($_SESSION['user_id'] == 50) || ($_SESSION['user_id'] == 83) || ($_SESSION['user_id'] == 194) || ($_SESSION['user_id'] == 1283)) {   ?>
<a target="_blank" href="time/dashboard.php">New</a>
<img style="cursor:pointer;width:50px;" src="images/new_button.png" />
<?php // } ?>
<?php //if ($_SESSION['is_lock'] == 1) { ?>
<a href="lock.php">UnLock</a>
<?php // } else { ?>
<a href="lock.php">Lock</a>
<?php //} ?>
</td>
<td bgcolor=""  align="" valign="middle" style="width:175px;background:none;border:0px;">
<a href="it_log_register.php">Register Complaint</a>
</td>
<td align="" bgcolor=""  style="padding-left:0px;width:auto;background:none;border:0px;border-right:0px solid #fff;">
<img style="border:1px solid #333;width:25px;" class="profile-pic animated" src="http://effism.com/images/employee/<?=$_SESSION['photo']?>" alt="">
<?php //print_r($_SESSION); ?>
<font size ="1" color="#fff" face="verdana">&nbsp;<strong>Welcome  <?php //echo $_SESSION['user_name']; ?>(
<?php //if ($_SESSION['hourly_rate'] > 0) { echo $_SESSION['hourly_rate']." ".$_SESSION['currency']; ?><?php //} ?> )</strong>
</font>
</td>
</tr>
</table>                        -->
                </td>
            </tr>
             <table style="margin-left:750px;margin: 0px 0px 10px 750px;">
                        <tr>
                            
                       
                    <td><a href="notifications.php" ><h1 class="ml5">
  <span class="text-wrapper">
    <span class="line line1"></span>
    <span class="letters letters-left">Today</span>
    <span class="letters ampersand">in</span>
    <span class="letters letters-right">Aries</span>
    <span class="line line2"></span>
  </span>
</h1></a>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script> -->
<script src="dist-new/anime.min.js"></script> 
<style>.ml5 {
  position: relative;
  font-weight: 300;
  font-size: 2em;
  color: #e31912;
}

.ml5 .text-wrapper {
  position: relative;
  display: inline-block;
  /*padding-top: 0.1em;*/
  padding-right: 0.05em;
  /*padding-bottom: 0.15em;*/
  line-height: .1em;
}

.ml5 .line {
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  margin: auto;
  height: 2px;
  width: 100%;
  background-color: #402d2d;
  transform-origin: 0.5 0;
}

.ml5 .ampersand {
  font-family: Baskerville, serif;
  font-style: italic;
  font-weight: 400;
  width: 1em;
  margin-right: -0.1em;
  margin-left: -0.1em;
}

.ml5 .letters {
  display: inline-block;
  opacity: 0;
}</style>
<script>anime.timeline({loop: true})
  .add({
    targets: '.ml5 .line',
    opacity: [0.5,1],
    scaleX: [0, 1],
    easing: "easeInOutExpo",
    duration: 700
  }).add({
    targets: '.ml5 .line',
    duration: 600,
    easing: "easeOutExpo",
    translateY: (el, i) => (-0.625 + 0.625*2*i) + "em"
  }).add({
    targets: '.ml5 .ampersand',
    opacity: [0,1],
    scaleY: [0.5, 1],
    easing: "easeOutExpo",
    duration: 600,
    offset: '-=600'
  }).add({
    targets: '.ml5 .letters-left',
    opacity: [0,1],
    translateX: ["0.5em", 0],
    easing: "easeOutExpo",
    duration: 600,
    offset: '-=300'
  }).add({
    targets: '.ml5 .letters-right',
    opacity: [0,1],
    translateX: ["-0.5em", 0],
    easing: "easeOutExpo",
    duration: 600,
    offset: '-=600'
  }).add({
    targets: '.ml5',
    opacity: 0,
    duration: 500,
    easing: "easeOutExpo",
    delay: 500
  });</script>
</td>

                 
                            
                            <td>
                    <span style="font-weight:bold;font-size: 20px;"><span class="glyphicon glyphicon-user" ></span> Employee Code :</span>&nbsp;<span style="background-color:Yellow;color:red;font-weight:bold;font-size: 20px;"> <?php echo  $_SESSION ['ecode']; ?></span>
                    </td>
                    
                    </tr>
            </table>
            <tr ><td colspan="3" width="100%" style="text-align: center"  id="myHeader">
                <ul class="menu cf">
                    <li><a href='jobdiary.php'>Daily Log</a></li>
                    <?php

                    if( $SESSION['no_menu']!=1)
                    {?>
                    
                    <li><a >Customise</a>
                      <ul class="submenu">
                     <li><a href='jobtype-customisation.php'>Job Type</a></li>
                       <li><a href='routine_job_list.php'>Routine Jobs</a></li>
                         <li><a href='customise_group.php'>Team</a></li>
                           <li><a href='daily-time-customization.php'>Daily Time</a></li>
                     </ul>
                    </li>
                    <li><a>Delegation</a>
                        <ul class="submenu">
                            <li><a href='delegation.php'>Delegate Job</a></li>
                            <li><a href='delegation_list.php'>Edit Delegation</a></li>
                            <!--<li><a href='team_entry.php'>Team Delegation</a></li>-->
                            <!--<li class='last'><a href='team_reports.php'>Team Reports</strong></span></a></li>-->
            </ul>
</li>
<li><a><span><strong>Reports</strong></span></a>
    <ul class="submenu">
        <li><a href='view_reports.php'><span><strong>Daily Jobs Status</strong></span></a></li>
        <li class='last'><a href='efficiency_reports.php'><span><strong>View All Jobs</strong></span></a></li>
         <?php if ($header_current_user_id==103||$header_current_user_id==2167||$header_current_user_id==36) {?>
        <li class='last'><a href='efficiency_reports_jobnum.php'><span><strong>JOB NUMBER REPORT</strong></span></a></li>
         <li class='last'><a href='invoice-teamsummary.php'><span><strong>INVOICE: SUMMARY</strong></span></a></li>
          <li class='last'><a href='invoice-all-jobs.php'><span><strong>INVOICE: DETAILS</strong></span></a></li>
        <?php }?>
        
        
        
        <?php if (($_SESSION['is_not_gcc'] == 1) || ($_SESSION['USER_ACCESS']['ACCESS_TIMESHEET_VIEW'] != "")) {?>
        <li class='last'><a href='custom_report.php'><span><strong>Time Sheet</strong></span></a></li>
        <?php }?>
        <li class='last'><a href='leave_report_new.php'><span><strong>Leave Summary</strong></span></a></li>
        <li class='last'><a href='empleaves.php'><span><strong>Leave Details</strong></span></a></li>
        <li class='last'><a href='cf_jobs.php'><span><strong>CF Jobs</strong></span></a></li>
        <li class='last'><a href='store.php'><span><strong>Jobs in Store</strong></span></a></li>
        <li class='last'><a href='one_one_one.php'><span><strong>1:1:1 Data</strong></span></a></li>
        <?php if($_SESSION['user_id']==103 || $_SESSION['user_id']==36||$header_current_user_id==512||$header_current_user_id==2146||$header_current_user_id==2327){ ?>
        <li class='last'><a href='digital-marketing-credential-full-list.php'><span><strong>Social Media Account List</strong></span></a></li>
        <?php }?>
    </ul>
</li>
<li><a><span><strong>Review</strong></span></a>
    <ul class="submenu">
        <li><a href="audit.php?date=<?php echo date('d-m-Y')?>"><span><strong>Daily Review</strong></span></a></li>

		<?php if($_SESSION['user_id']==19 || $_SESSION['user_id']==2 || $_SESSION['user_id']==10 || $_SESSION['user_id']==103 || $_SESSION['user_id']==38 || $_SESSION['user_id']==83 || $_SESSION['user_id']==48 || $_SESSION['user_id']==52 || $_SESSION['user_id']==49 || $_SESSION['user_id']==1452 || $_SESSION['user_id']==36 ||$_SESSION['user_id']==3346 ||$_SESSION['user_id']==4858 ||$_SESSION['user_id']==4313  ) {?>
		<li><a href="profit_analysis.php"><span><strong>Profit Analysis</strong></span></a></li>
		<?php 
		
		}?>
		


		<?php if($_SESSION['user_id']==19 || $_SESSION['user_id']==2 || $_SESSION['user_id']==103 || $_SESSION['user_id']==1452 || $_SESSION['user_id']==36 ||$_SESSION['user_id']==3346 ||$_SESSION['user_id']==4858 ||$_SESSION['user_id']==4313  ) {?>
		<li><a href="analysis_prodvs_nonprod.php"><span><strong>Analysis</strong></span></a></li>
		<?php 
		
		}
		
		?>
		
			<?php if($_SESSION['user_id']==3675 ||$_SESSION['user_id']==120 || $_SESSION['user_id']==2 || $_SESSION['user_id']==103 || $_SESSION['user_id']==1452 || $_SESSION['user_id']==36 || $_SESSION['user_id']==2146 ) {?>
		<li><a href="25params.php"><span><strong>25 Parameters</strong></span></a></li>
		<?php 
		
		}
		
		?>
		
        <!--<li><a href='marketing_audit_new.php'><span><strong>Marketing Review</strong></span></a></li>-->
        	<?php if($div==1||$div==2||$div==3||$div==4||$div==1||$div==225) {//******Limited to Marine only***//?>
		
         <li><a href='marketing-audit.php'><span><strong>Marketing Review</strong></span></a></li>
         <?php }?>
        <li class='last'><a href='employee_history.php'><span><strong>Monthly Summary</strong></span></a></li>
        <li class='last'><a href='complete_history.php'><span><strong>Complete History</strong></span></a></li>
         <!--<li class='last'><a href='marketing-input-audit.php'><span><strong>Non-Marine Marketing Review</strong></span></a></li>-->
        <li class='last'><a href='audit_menu.php'><span><strong>Review Log</strong></span></a></li>
         <?php if ($header_current_user_id==103||$header_current_user_id==135||$header_current_user_id==2146||$header_current_user_id==3675||$header_current_user_id==36||$header_current_user_id==3811||$header_current_user_id==3810||$header_current_user_id==2327||$header_current_user_id==2246||$header_current_user_id==123||$header_current_user_id==4184) {?>
           <!--<li class='last'><a href='digital-marketing-seo-audit.php'><span><strong> Branding Review</strong></span></a></li>-->
           <!--<li class='last'><a href='digital-marketing-subdiv-summary.php'><span><strong> Branding Review</strong></span></a></li>-->
        <!--<li class='last'><a href='digital-marketing-revenue-audit.php'><span><strong>Digital Marketing Revenue Review</strong></span></a></li>-->
        <?php }?>
        	
		
        <?php if ($header_current_user_id==103||$header_current_user_id==134||$header_current_user_id==2327||$header_current_user_id==4205) {?>
        <li class='last'><a href='digital-marketing-revenue-list.php'><span><strong> Revenue Entry</strong></span></a></li>
          <?php }?>
          
          	<?php if($div==128||$div==92||$div==151||$div==150||$div==332||$div==4||$div==24||$div==135||$div==193||$div==335||$div==350 ||$div==237||$div==216||$div==214||$div==153||$div==259||$div==108||$div==400) {?>
		<li><a href="performance-menu.php"><span><strong>Marketing Performance Evaluation</strong></span></a></li>
		<?php 
		
		}?>
    </ul>
</li>
<li><a><span><strong>Monthly Plan</strong></span></a>
    <ul class="submenu">
        <li class='last'><a href='monthly_planning.php'><span><strong>Monthly Plan Entry</strong></span></a></li>
        <li class='last'><a href='monthly_plan_audit.php'><span><strong>Monthly Plan Review</strong></span></a></li>
	<?php if ($header_current_user_id==103||$header_current_user_id==36||$header_current_user_id==512) {?>
 <li class='last'><a href='monthly-checklist-save.php'><span><strong>Monthly Checklist</strong></span></a></li>
 <?php }?>
    </ul>
</li>
<!--<li><a><span><strong>Planning</strong></span></a>
    <ul class="submenu">
        <li class='last'><a href='monthly_planning.php'><span><strong>Monthly Plan Entry</strong></span></a></li>
        <li class='last'><a href='monthly_plan_audit.php'><span><strong>Monthly Plan Review</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>Annual Plan Entry</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>Annual Plan Review</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>5 Year Plan Entry</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>5 Year Plan Review</strong></span></a></li>

    </ul>
</li>-->
<li><a href="hr_menu.php"><span><strong>HR</strong></span></a></li>
<!--                        <li><a href="leave_menu.php"><span><strong>Leave</strong></span></a></li>-->
	<?php if($_SESSION['user_id']==36 ||$_SESSION['user_id']==103||$_SESSION['user_id']==1452 ) {?>
 <li><a><span><strong>ADMIN</strong></span></a>
    <ul class="submenu">
        <li class='last'><a href='menu-hr.php'><span><strong>HR</strong></span></a></li>
        <li class='last'><a href='menu-miscellaneous.php'><span><strong>Miscellaneous</strong></span></a></li>
		<li class='last'><a href='menu-operations.php'><span><strong>Operations</strong></span></a></li>
		<li class='last'><a href='menu-health.php'><span><strong>Health</strong></span></a></li>
		   <li class='last'><a href='menu-effism-monitoring.php'><span><strong>Effism Monitoring</strong></span></a></li>
        <li class='last'><a href='menu-training.php'><span><strong>Training</strong></span></a></li>
		<li class='last'><a href='menu-happiness.php'><span><strong>Happiness</strong></span></a></li>
	

    </ul>
</li> 
<?php }?>

<li><a href="chart_menu.php"><span><strong>Graphs</strong></span></a></li>
<!--<li><a><span><strong>KPI</strong></span></a>
    <ul class="submenu">
        <li class='last'><a href='under_construction.php'><span><strong>Daily</strong></span></a></li>
        <li class='last'><a href='under_construction.php'><span><strong>Weekly</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>Monthly</strong></span></a></li>
		<li class='last'><a href='under_construction.php'><span><strong>Yearly</strong></span></a></li>

    </ul>
</li>-->
<li><a href="job_menu.php"><span><strong>Miscellaneous</strong></span></a></li>
<?php if($_SESSION['user_id']==36 ||  $_SESSION['user_id']==103 ||  $_SESSION['user_id']==1452||  $_SESSION['user_id']==2167 || $_SESSION['user_id']==512) {?>
<!--<li><a href=''><span><strong>KPI</strong></span></a></li>-->
<!--<li><a><span><strong>KPI </strong></span></a>-->
<!--    <ul class="submenu">-->
<!--        <li class='last'><a href='kpi_planning.php'><span><strong>KPI Plan Entry</strong></span></a></li>-->
<!--        <li class='last'><a href='kpi_plan_audit.php'><span><strong>KPI  Review</strong></span></a></li>-->
		

<!--    </ul>-->
<!--</li>-->

<?php }?>

<?php
}
?>
<li><a href='logout.php'><span><strong>Logout</strong></span></a></li>


</ul>



<style >
    .example1 {
        height: 35px;	
        overflow: hidden;
        position: relative;
    }
    .example1 h3 {
        font-size: 2em;
        color: #800000;
        position: absolute;
        width: 100%;
        height: 100%;
        margin: 0;
        line-height: 50px;
        text-align: center;
        /* Starting position */
        -moz-transform:translateX(100%);
        -webkit-transform:translateX(100%);	
        transform:translateX(100%);
        /* Apply animation to this element */	
        -moz-animation: example1 15s linear infinite;
        -webkit-animation: example1 15s linear infinite;
        animation: example1 15s linear infinite;
    }
    /* Move it (define the animation) */
    @-moz-keyframes example1 {
        0%   { -moz-transform: translateX(100%); }
        100% { -moz-transform: translateX(-100%); }
    }
    @-webkit-keyframes example1 {
        0%   { -webkit-transform: translateX(100%); }
        100% { -webkit-transform: translateX(-100%); }
    }
    @keyframes example1 {
        0%   { 
            -moz-transform: translateX(100%); /* Firefox bug fix */
            -webkit-transform: translateX(100%); /* Firefox bug fix */
            transform: translateX(100%); 		
        }
        100% { 
            -moz-transform: translateX(-100%); /* Firefox bug fix */
            -webkit-transform: translateX(-100%); /* Firefox bug fix */
            transform: translateX(-100%); 
        }
    }
</style>

<!-- HTML -->	

</td></tr>
</table>
</td>
</tr>


<tr> 
    <td  align="center" valign="middle">
        <div align="left">

            <table style="border: 0px solid rgb(0, 0, 0);" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td  bgcolor="#e9e9e9" colspan="12">

                    </td></tr>
                <tr><td> <?php if ($mysql_obs->num_rows != 0) {?>
                    <table id="tbl" style="border-collapse:collapse;width:95%;margin:0 auto;" border="1" cellspacing="0">
                        <tr id="header" bgcolor="#008080" style="color:#fff;border:#a6107b solid 1px;font-size:12px;">
                            <td align="center"   style="border:#000000 solid 1px;"><b>Issued By</b></font></td>
                    <td align="center"   style="border:#000000 solid 1px;"><b>Type</b></font></td>
            <td width='40%' align="center"   style="border:#000000 solid 1px;"><b> Remarks</b></font></td>
    <td align="center"   style="border:#000000 solid 1px;"><b>Response</b></font></td>
<td align="center"   style="border:#000000 solid 1px;"><b>Notify</b></font></td>
</tr> <?php
    $count = 0;
    while ($row = $mysql_obs->fetch_assoc()) {
        $reopen = $reopen_remarks = "";$ob_type=" "; $row['ob_type'];
         if(!empty($row['ob_type'])&&$row['ob_type']!="0"){
                    $ob_type = " - ".$row['oc_name'];
                }
        if($row['satisfied']==0){
            if ($count % 2 == 0)
                $bgcolor = '#7ecab2';
            else
                $bgcolor = '#CEE3F6';}
        else{
            if($row['satisfied']==3)
            { $bgcolor = '#FF9999';
             $reopen="<span style='color: #336591;'>REOPENED REVIEW </span> ";
             $reopen_remarks = "<br> <span style='color: #336591;'> Reopen Remarks : </span>";}

            else  if($row['satisfied']==1){
                $bgcolor = '#7ecab2';
                $reopen="SATISFIED REVIEW ";
                $reopen_remarks = "<br>Satisfication Remarks : ";  
            }

            $reopen_remarks.= $row['satisf_remarks'];
        }
?>
<tr bgcolor="<?php echo $bgcolor?>" id="<?php echo $row['id']?>">
    <td  width="10%"  style="border:#000000 solid 1px; padding-left:5px;"><font  color="black" font face="verdana">&nbsp;<strong> <?php echo $row['full_name'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="color:#00F"><?php echo $row['audit_date']?></span></strong></font></td>
    <td   width="10%" style=" border:#000000 solid 1px; padding-left:5px;">
        <?php if ($row['job_observation_id'] > 0) {?>
        <span style="font-size:15px"><?php echo $row['type']?><br>
            <b style="color:#F00;">Job:</b></span>
        <font  color="black" font face="verdana"> <strong><?php echo $row['taskname'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php } else {?>
            <?php if(!empty($row['obs_type'])){ ?> <span style="color:#F00;font-size:15px"><b>(<?php echo $row['obs_type'].$ob_type?>) <br></b><img src="images/ratings/<?php echo $row['image'] ?>"></span><br /><?php }?>
            <font color="black" font face="verdana"><strong><?php echo $row['type']."<br>" .$reopen ."<br>" ;?> 
            <?php if($row['dm_title']!="") {echo "(". $row['dm_title'].") <br>"; }?>
                <span style="color:#00F"><?php
                          if ($row['report_month'] == 0 && $row['report_year'] == 0)
                              echo $row['reportdate'];
                          else
                              echo date("M",strtotime("2009-".$row['report_month']."-1")).' '.$row['report_year'];
                    ?></span> </strong></font>
            <?php }?>
            </td>
            <td  width="10%" style=" border:#000000 solid 1px; padding-left:5px;"><font  color="black" font face="verdana"> <strong>  <?php echo $row['remarks']. $reopen_remarks;?></strong></font></td>
            <td align="center"  width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                <textarea id="text<?php echo $row['id']?>" name="rmks1" /></textarea></td>
    <td align="center"   width="10%" style="border:#000000 solid 1px; padding-left:5px;">
        <input type="button" onclick="hide(<?php echo $row['id']?>,<?php echo $row['observation']?>)" name="notification" value="Notified" /></td>
</tr><?php
            $count++;
    }$count = 0;
?>
</table><?php }
?>
</td></tr>
<tr> <td>

    <?php if ($notify_petty->num_rows != 0) {
    ?>
    <table id="tbl" width="60%" align="center" style="border-collapse:collapse" border="1" cellspacing="0">
        <tr id="header" style="border:#a6107b solid 1px;">
            <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Type</b></font></td>
            <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Action By</b></font></td>
            <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notification</b></font></td>
            <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Notify</b></font></td>
        </tr>

        <?php
    $count = 0;
    while ($row = $notify_petty->fetch_assoc()) {
        if ($count % 2 == 0)
            $bgcolor = '#ffcc99';
        else
            $bgcolor = '#ff9999';
        ?>
        <tr id="<?php echo $row['id']?>">
            <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                <span style="color:#F00;font-size:15px"><b>Notification</b></span>
                <font size ="2" color="black" font face="verdana">&nbsp;<strong><?php echo $row['type'];?></strong></font></td>
            <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font face="verdana">&nbsp;<strong>  <?php echo $row['full_name'];?></strong></font></td>
            <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font face="verdana">&nbsp;<strong>  <?php echo $row['notification'];?></strong></font></td>

            <td align="center" bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                <input type="button" onclick="hide_notification(<?php echo $row['id']?>)" name="notification" value="Notified" /></td>
        </tr>

        <?php
            $count++;
    }
    $count = 0;
        ?>
    </table>
    <?php
}
    ?>

    </td></tr>
<tr>


    <td>

        <?php if ($routine_notify->num_rows != 0) {
        ?>
        <table id="tbl" width="80%" align="center" style="border-collapse:collapse" border="1" cellspacing="0">
            <tr id="header" style="border:#a6107b solid 1px;">
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>User</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Routine Job</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Type</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Job Number</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>New Est Value</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Old Job Description</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Old Job Est| Apprv By</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Rejection Remarks</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Approve</b></font></td>
                <td align="center" bgcolor="#e2f3fd"  style="border:#000000 solid 1px;"><font color="#a6107b" face="Verdana, Arial, Helvetica, sans-serif" size="2"><b>Reject</b></font></td>
            </tr>

            <?php
    $count = 0;
    while ($row = $routine_notify->fetch_assoc()) {
        if ($count % 2 == 0)
            $bgcolor = '#ffcc99';
        else
            $bgcolor = '#ff9999';
            ?>
            <tr id="<?php echo $row['id']?>">
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font="font" face="verdana"><strong><?php echo $row['full_name'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                    <font size ="2" color="black" font face="verdana">&nbsp;<strong><?php echo $row['job_name'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                    <font size ="2" color="black" font face="verdana">&nbsp;<strong><?php echo $row['main_type_name'];?><br><?php echo $row['job_type_name'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font face="verdana">&nbsp;<strong>  <?php echo $row['job_num'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font face="verdana">&nbsp;<strong>  <?php echo $row['est_time'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font="font" face="verdana"><strong><?php echo $row['old_job_name'];?></strong></font></td>
                <td bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font face="verdana">&nbsp;<strong>  <?php if($row['old_est_time']!="00:00:00"){ echo $row['old_est_time'];?>| <?php } echo $row['old_apprv_by'];?></strong></font></td>
                <td align="center" bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><font size ="2" color="black" font="font" face="verdana"><strong><?php echo $row['rejection_remarks'];?></strong></font></td>
                <td align="center" bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;"><input type="button" onclick="approve_routine(<?php echo $row['id']?>, 1)" value="Approve" /></td>

                <td align="center" bgcolor="<?php echo $bgcolor?>" width="10%" style="border:#000000 solid 1px; padding-left:5px;">
                    <input type="button" onclick="approve_routine(<?php echo $row['id']?>, -1)" value="Reject" /></td>
            </tr>

            <?php
                $count++;
    }
    $count = 0;
            ?>
        </table>
        <?php
}
        ?>

    </td></tr>

<?php 
if($_SESION['scroll']!=""){
?>
<tr>
    <marquee style="font-size:20px;font-weight: bold">
        <?php echo $_SESION['scroll'] ?>
    </marquee>
</tr>
<?php }?>

<!------***For health entry only*******------->
<?php 
//$h_user_id1 =  $_SESSION['user_id'];
//$current_year = 2019;
//$current_month = 12;
//$rslt = $mysqli->query("select * from tbl_health WHERE user_id=$h_user_id1 and health_month='$current_month' and health_year='$current_year' ");
//$rslt = $mysqli->query("select * from tbl_health WHERE user_id=$h_user_id1 and  health_year='$current_year'  and lab_result!=''");//****Newly added by Saranya as the month saving is current month when upload a new health report
//$rslt_row = $rslt->fetch_assoc();
if(0){
?>
<div class="marquee-txt" >

    <div class="register-btn" style="width:12%; float: left; background: blue; color: #fff; padding: 10px; margin-left: 96px;">
        <a href="user_health_entry.php" target="_blank" style="color: #fff; font-weight: bold;">***Please click here</a>
    </div>
    <div class="marquee-text" style="width:80%; float: right; padding: 8px 0;">
        <marquee direction="left">

            <span style="font-size:16px; font-weight: bold;">    
                <font color="red">***Kind Attn!*** </font>Please upload       <font color="red">Health report </font>, and fill the details
            </span>

        </marquee>
    </div>
</div>
<?php }?>
<!------***For health entry ends*******------->
</table>
</div></td>
</tr>
<div id="demo"></div>
<div class="PopupDiv" id="routine_modal" style=" width:600px;border: 3px solid black; background-color: rgb(153, 153, 255); padding: 25px; font-size: 150%; text-align: center; display: none; position: absolute; visibility: visible; top: 1941.5px; left: 548.5px; z-index: -1;">
    <div style="float:left">Remarks</div>
    <div><textarea id="routine_rejection_remarks" name="routine_rejection_remarks"></textarea>
        <input type="hidden" id="routine_rejection_value" />
    </div>
    <div><input type="button" value="Submit" onclick="reject_routine_job()" /><input type="button" value="Cancel" onclick="Popup.hide('routine_modal')" /></div>
</div>

<div class="modal" id="notifyModal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"  >
    <div class="modal-dialog" role="document" style=" width: auto !important; height: auto !important;">
        <div class="modal-content">
            <div class="modal-header modal-header-success">
                <h3 class="modal-title" id="exampleModalLabel">Message for you</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='closeNotifymodal()'>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-aqua-gradient" id="scribBody">
                <?php if($_SESSION['yrpass']==1){ ?>
                <h3 style="font-family: Impact, Charcoal, sans-serif;color: #56003D"><i><?=$_SESSION['compltedyears']?></i></h3>
                <img src='images/congrats.gif'>
                <hr>
                <?php } ?>
                 <?php if($_SESSION['bday'] == 1){ ?>
                <h4 style="font-family: Impact, Charcoal, sans-serif;color: #56003D"><i>Wishing you the best on your birthday and everything good in the year ahead</i></h4>
                <img src='images/bday2.gif'>
                <hr>
                <?php } ?>
                
                

<!--                <p> <h5> 
                <span style="font-size:16px; font-weight: bold;">    
                    <font color="blue">****** </font>
                    Happy to announce that our Founder Chairman and CEO of Aries Group Mr. Sohan Roy been awarded for the consecutive 4th time as one of the top Indian leaders in the Middle East by the prestigious Forbes Magazine. 

                    <font color="blue">****** </font></span></h5>-->


            </div>
        </div>
    </div>
</div>

<script>
    function display_notification(){
        var modal = document.getElementById('notifyModal');  
        modal.style.display = "block";

    }
    function closeNotifymodal(){
        var modal = document.getElementById('notifyModal');
        modal.style.display = "none";  
    }
</script>
