<?php
session_start();
include("connect.inc.php");
if ((!isset($_SESSION['user_id']))){
   echo "SESSION TIME OUT"; exit;
}
$user_id = $_SESSION['user_id'];
$emp_division_id = $_SESSION['emp_division_id'];
$sql_date = '';
if(isset($_GET['sql_date'])){
    $sql_date = $_GET['sql_date'];   
}
$jdc = '';
if($sql_date!=''){
    $query = "SELECT * FROM tbl_efficiency_calculation WHERE work_date = '$sql_date' and user_id = $user_id;";
    $ec_re = $mysqli->query($query);
    $ec_result = $ec_re->fetch_assoc();
    unset($ec_result['calId']);unset($ec_result['user_id']);unset($ec_result['work_date']);unset($ec_result['emp_div_id']);
    unset($ec_result['completed_date']);unset($ec_result['status']);unset($ec_result['actual_efficiency']);
    foreach($ec_result as $k=>$v){
        if($k!='' && $k!='total_efficiency'){
            $key = strtoupper(str_replace('_'," ", str_replace('ncr','review',str_replace('ncr_review','ncr',$k)))); 
                                                  if($key == 'REVIEW'){ $key = 'REVIEW NCR'; }
                                                  if($key == 'MAJOR REVIEW'){ $key = 'MAJOR NCR'; }
            $new_ar['Quarter'] = $key;
            $new_ar['Change'] = $v;
            $jdc = json_encode($new_ar).','.$jdc;
            $new_ar = array();
        }
    }
}
//echo $jdc;                       
?>

<!doctype html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<script data-jsfiddle="common" src="../lib/jquery.min.js"></script>
<script data-jsfiddle="common" src="../lib/jquery-ui/js/jquery-ui.custom.min.js"></script>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0"  style="background-image:url(images/themes/<?php echo rand(5,14); ?>.jpg); background-repeat:repeat-x;">
<table width="100%">
    <tr><td style="text-align:center"><b>Daily Graph Based on Parameters(<?=$sql_date?>)</b></td></tr>
    <tr><td>
            <?php if($jdc==''){ echo "Not Found"; exit;} 
            else{ ?>
                <div id="chart1" style="width:100%; height:600px"></div>
            <?php } ?>
    </td></tr>
</table>
 <!-- JQX WIDGET-->
 <link rel="stylesheet" href="../jqwidgets/styles/jqx.base.css" type="text/css" /> 
<!--<script type="text/javascript" src="javascript/jquery-1.10.2.min.js"></script>-->
<script type="text/javascript" src="../jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxchart.core.js"></script>
<script type="text/javascript" src="../jqwidgets/jqxdata.js"></script>

   <script type="text/javascript">
        $(document).ready(function () {
            var sampleData = [<?php echo $jdc?>];
            settings = {
                title: "",
                borderLineWidth: 1,
                showBorderLine: true,
                enableAnimations: true,
                description: '',
                showLegend: false,
                //padding: { left: 5, top: 5, right: 10, bottom: 5 },
                //titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: sampleData, //dataAdapter,
                xAxis:
                {
                    //description: 'Year and quarter',
                    dataField: 'Quarter',
                    unitInterval: 1,
                    textRotationAngle: -75,
                    formatFunction: function (value, itemIndex, serie, group) {
                        return value;
                    },
                    valuesOnTicks: false
                },
                colorScheme: 'scheme05',
                seriesGroups:
                [
                    {
                        type: 'column',
                        valueAxis:
                        {
                            description: 'Percentage Change',
                            formatFunction: function (value) {
                                return value + '%';
                            },
                            maxValue: 31,
                            minValue: -10,
                            unitInterval: 2
                        },
                        series:
						    [
                                {
                                    dataField: 'Change',
                                    displayText: 'Change',
                                    toolTipFormatFunction: function(value, itemIndex, serie, group, categoryValue, categoryAxis) {
                                        return '<DIV style="text-align:left";><b>Parameter Name: </b>' + categoryValue
                                                 + '<br /><b>Value:</b> ' + value + ' %</DIV>'
                                    },
                                    // Modify this function to return the desired colors.
                                    // jqxChart will call the function for each data point.
                                    // Sequential points that have the same color will be
                                    // grouped automatically in a line segment
                                    colorFunction: function (value, itemIndex, serie, group) {
                                        return (value < 0) ? '#CC1133' : '#55CC55';
                                    }
                                }
                            ]
                    }
                ]
            };
            $("#chart1").jqxChart(settings);
        });
    </script>
   
</body>
</html>