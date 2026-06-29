<html> <link rel="stylesheet" href="jqwidgets/styles/jqx.base.css" type="text/css" />
    <script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="jqwidgets/jqxmenu.js"></script>
   
</head>
<body>
    <div id='content' style="padding:15px">
        <script type="text/javascript">
		var js = $.noConflict();
            js(document).ready(function () {
                var theme = "";
                js("#jqxMenu").jqxMenu({ width: '100%', height: '40px', theme: theme });
            
                var centerItems = function () {
                    var firstItem = js(js("#jqxMenu ul:first").children()[0]);
                    firstItem.css('margin-left', 0);
                    var width = 0;
                    var borderOffset = 2;
                    js.each(js("#jqxMenu ul:first").children(), function () {
                        width += js(this).outerWidth(true) + borderOffset;
                    });
                    var menuWidth = js("#jqxMenu").outerWidth();
                    firstItem.css('margin-left', (menuWidth / 2 ) - (width / 2));
                }
                centerItems();
                js(window).resize(function () {
                    centerItems();
                });
            });
        </script>
		<?php
		$div = $_REQUEST['division'];
		$year = $_REQUEST['select_year'];
		$month = $_REQUEST['select_month'];
		$user_id = $_REQUEST['employee_id'];
		?>
           <div id='jqxMenu'>
                <ul>
                    <li><a href="punctual_chart.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Punctual</font></a></li>
                    <li><a href="efficiency_chart.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Efficiency</font></a></li>
                    <li><a href="time_job_chart.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Time Vs Job</font></a></li>
                    <li><a href="punctual_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Punctuality Co. Efficient</font></a></li>
                    <li><a href="overtime_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Overtime Co. Efficient</font></a></li>
                    <li><a href="health_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Health Co. Efficient</font></a></li>
                     <li><a href="income_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Income Co. Efficient</font></a></li>
                    <li><a href="marketing_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Marketing Co. Efficient</font></a></li>
                    <li><a href="overall_coefficient.php?select_year=<?php print $year; ?>&select_month=<?php print $month; ?>&employee_id=<?php print($user_id); ?>"><font size='2px'>Overall Co. Efficient</font></a></li>
                           
                          <!--  <li type='separator'></li> 
                            <li>More
                                <ul style='width: 220px;'>
                                    <li><a href="view_working_getting_salary_emp.php?month=<?php print $month; ?>&year=<?php print $year; ?>&division=<?php print $div; ?>">Customize Emp - Salary</a></li>
                                    <li><a href="customize_emp_for_salary_slip_entry.php?month=<?php print $month; ?>&year=<?php print $year; ?>&division=<?php print $div; ?>">Customize Emp - Salary Slip</a></li>
                                    <li><a href="salary_slip_send_to_email.php?month=<?php print $month; ?>&year=<?php print $year; ?>&division=<?php print $div; ?>">Send Salary Slip</a></li>
                                    <li><a href="salary_slip_resend.php?month=<?php print $month; ?>&year=<?php print $year; ?>&division=<?php print $div; ?>">ReSend Salary Slip</a></li>
                                </ul>
                              
                    </li> -->
                </ul>
            </div>

    </div>
<html>