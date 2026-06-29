<?php	                                       			
$id = $_SESSION['user_id'];
?>
<script type="text/javascript">
<!--
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
</script><body onLoad="MM_preloadImages('../images/users_rollover.jpg')">
<tr> 
    <td align="center" valign="top">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td background="../images/paladion_topbanner-tile1.jpg"><img src="../images/paladion_topbanner-left.jpg" width="369" height="109"></td>
          <td background="../images/paladion_topbanner-tile1.jpg">&nbsp;</td>
          <td align="right" valign="top" background="../images/paladion_topbanner-tile1.jpg"><img src="../images/paladion_topbanner-rig3.jpg" width="349" height="109"></td>
        </tr>
      </table></td>
</tr>
  <tr> 
    <td align="center" valign="middle"><table width="100%" border="0" cellspacing="0" cellpadding="0">
       	<tr> 
    	   	  <td height="1" bgcolor="1A4D81"> </td>
       	</tr>
       	
    	</table></td>
</tr>
<tr> 
       <td height="1" bgcolor="1A4D81"></td>
</tr>
<tr>
    <td height="25" align="center" valign="middle"><div align="left">
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
	    <tr> 
	    <!--<td width="15%" height="50" bgcolor="#e9e9e9"><font size ="1" color="black" font face="verdana">&nbsp;Welcome <strong></strong>[&nbsp;<b><?php	                                       			 echo strtoupper($_SESSION['user_name']); ?></b>&nbsp;]</font></td>-->
		<td width="15%" height="50" bgcolor="#e9e9e9" style="padding-left:50px; padding-top:15px;"><img src="../photos/<?php	                                       			 echo $photo; ?>" width="57" height="57" /></td>
		
		<td bgcolor="#e9e9e9" width="12%"><a href="index.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image1','','../images/log_rollover.jpg',1)"></a><a href="users.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','../images/users_rollover.jpg',1)"><img src="../images/users.jpg" name="Image6" width="95" height="36" border="0" id="Image6" /></a></td>

<td bgcolor="#e9e9e9" width="15%"><a href="job_type.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image12','','../images/job_type_rollover.png',1)"><img src="../images/add_job_type.png" name="Image12" width="95" height="36" border="0" id="Image5" "></a></td>
		
		<td bgcolor="#e9e9e9" width="14%"><a href="view_reports.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','../images/reports_rollover.jpg',1)"></a></td>

		<td bgcolor="#e9e9e9" width="15%"><a href="../custom_report.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image2','','../images/Workreports_r.jpg',1)"></a></td>
		
		<!--<td bgcolor="#e9e9e9" width="15%"><a href="../sheets.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image7','','../images/monthlyreports_r.jpg',1)"><img src="../images/monthlyreports.jpg" name="Image7" width="120" height="36" border="0" id="Image7" /></a></td>-->
		
		<td bgcolor="#e9e9e9" width="12%"><a href="../admin/assign_job.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image7','','../images/job-over.jpg',1)"></a></td>
	
		<td bgcolor="#e9e9e9" width="12%"><a href="profile.php?id=<?php	                                       			 echo $_SESSION['user_id'];?>&from=jobdiary.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','../images/profile_rollover.jpg',1)"></a></td>

		<td bgcolor="#e9e9e9" width="13%"><a href="users.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','../images/users_rollover.jpg',1)"></a></td>
		
		<!--<td bgcolor="#e9e9e9" align="right"><a href="../logout.php" style="text-decoration:none;"><font size ="2" color="black" font face="verdana"><strong>Log Out</strong>&nbsp;&nbsp;</font></a></td>-->
		<td bgcolor="#e9e9e9" width="15%"><a href="../logout.php" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','../images/logout_rollover.jpg',1)"><img src="../images/logout.jpg" name="Image5" width="95" height="36" border="0" id="Image5" /></a></td>
          </tr>
		  <!--<tr>
		  	<td align="left"></td>
			<td align="right" colspan="3" style="padding-right:100px;"></td>
			<td align="right" colspan="3"></td>
		  </tr>-->
		  <tr>
			<td colspan="8">
				<table width="100%" bgcolor="#e9e9e9" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td bgcolor="#e9e9e9" width="17%" style="padding-left:20px;"><font size ="1" color="black" font face="verdana">&nbsp;Welcome <strong></strong>[&nbsp;<b><?php	                                       			 echo $_SESSION['user_name']; ?></b>&nbsp;]</font></td>
						<td height="25" bgcolor="#e9e9e9" align="right"><span id="clockspot" style="width:250px; font-size:12px"></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
        </table>
      </div></td>
</tr>
<script type="text/javascript">
function startclock()
{
	var thetime=new Date();
	var nhours=thetime.getHours();
	var nmins=thetime.getMinutes();
	var nsecn=thetime.getSeconds();
	var nday=thetime.getDay();
	var nmonth=thetime.getMonth();
	//if(nmonth<10)
		//nmonth = ""+nmonth;
	var ntoday=thetime.getDate();
	var nyear=thetime.getYear();
	var AorP=" ";
	
	if (nhours>=12)
		AorP="P.M";
	else
		AorP="A.M";
	
	if (nhours>=13)
		nhours-=12;
	
	if (nhours==0)
	   nhours=12;
	
	if (nsecn<10)
	 nsecn="0"+nsecn;
	
	if (nmins<10)
	 nmins="0"+nmins;
	
	if (nday==0)
	  nday="Sunday";
	if (nday==1)
	  nday="Monday";
	if (nday==2)
	  nday="Tuesday";
	if (nday==3)
	  nday="Wednesday";
	if (nday==4)
	  nday="Thursday";
	if (nday==5)
	  nday="Friday";
	if (nday==6)
	  nday="Saturday";
	
	nmonth+=1;
	
	if (nyear<=99)
	  nyear= "19"+nyear;
	
	if ((nyear>99) && (nyear<2000))
	 nyear+=1900;
	 
	//document.getElementById("clockspot").innerHTML="<b>"+nhours+": "+nmins+": "+nsecn+" "+AorP+" "+nday+", "+nmonth+"/"+ntoday+"/"+nyear+"</b>";
	document.getElementById("clockspot").innerHTML="<b>"+nday+", "+ntoday+"-"+nmonth+"-"+nyear+", "+nhours+": "+nmins+": "+nsecn+" "+AorP+"</b>";
	setTimeout('startclock()',1000);

}
startclock();
</script>
