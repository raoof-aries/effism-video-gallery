
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59882611-1', 'auto');
  ga('send', 'pageview');

</script>
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
</script>
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
        <table width="100%" border="0" cellspacing="0" cellpadding="5">
	    <tr> 
	    <td width="15%" height="50" bgcolor="#e9e9e9"><font size ="1" color="black" font face="verdana">Welcome <strong></strong>[&nbsp;<b><?php	                                       			 echo $_SESSION['user_name']; ?></b>&nbsp;]</font></td>
		<td bgcolor="#e9e9e9" width="12%"><a href="index.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','../images/log_rollover.jpg',1)"><img src="../images/log.jpg" name="Image1" width="86" height="36" border="0" id="Image1" /></a></td>
		<!--<td bgcolor="#e9e9e9" width="15%"><img src="../images/reports.jpg" style="cursor:pointer;" onclick="window.location='index.php';"/></td>-->
		<td bgcolor="#e9e9e9" width="18%"><a href="add.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','../images/adduser_rollover.jpg',1)"><img src="../images/Add_New_User.jpg" name="Image2" width="154" height="36" border="0" id="Image2" /></a></td>

<td bgcolor="#e9e9e9" width="14%"><a href="view_reports.php?uid=<?php	                                       			 echo $_SESSION['user_id'];?>" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','../images/reports_rollover.jpg',1)"><img src="../images/reports.jpg" name="Image3" width="104" height="36" border="0" id="Image3" /></a></td>

<!--		<td bgcolor="#e9e9e9" width="20%"><img src="../images/Add_New_User.jpg" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','adduser_rollover.jpg',1)" style="cursor:pointer;" onclick="window.location='add.php';"/></td>-->

		<!--<td bgcolor="#e9e9e9" width="20%"><img src="../images/View_Reports.jpg" style="cursor:pointer;" onclick="window.location='view_reports.php';"/></td>-->
<!--		<td bgcolor="#e9e9e9"><img src="../images/profile.jpg" style="cursor:pointer;" onclick="window.location='profile.php?id=<?php	                                       			 echo $_SESSION['user_id'];?>';"/></td>-->
<td bgcolor="#e9e9e9" width="13%"><a href="profile.php?id=<?php	                                       			 echo $_SESSION['user_id'];?>&from=jobdiary.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','../images/profile_rollover.jpg',1)"><img src="../images/profile.jpg" name="Image4" width="95" height="36" border="0" id="Image4" /></a></td>
		<td bgcolor="#e9e9e9" width="13%"><a href="users.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image6','','../images/users_rollover.jpg',1)"><img src="../images/users.jpg" name="Image6" width="95" height="36" border="0" id="Image6" /></a></td>
		<!--<td bgcolor="#e9e9e9" align="right"><a href="../logout.php" style="text-decoration:none;"><font size ="2" color="black" font face="verdana"><strong>Log Out</strong>&nbsp;&nbsp;</font></a></td>-->
		<td bgcolor="#e9e9e9" width="15%"><a href="../logout.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','../images/logout_rollover.jpg',1)"><img src="../images/logout.jpg" name="Image5" width="95" height="36" border="0" id="Image5" /></a></td>
          </tr>
	<?php	                                       			
		$file_path=$_SERVER["SCRIPT_NAME"];
		$break=explode('/',$file_path);
		$file_name = $break[count($break)-1];
	?>
		  <!--<tr>
		  	<td align="left"><?php	                                       			 if($file_name=='view_reports.php') { ?><a href="get_time_sheet.php">Time Sheet</a><?php	                                       			 } else { ?><a href="suggestions.php">View Suggestions</a><?php	                                       			 } ?></td>
			<td align="right" colspan="3"><?php	                                       			 if($file_name=='view_reports.php') { ?><a href="get_leave_sheet.php">Leave Sheet</a><?php	                                       			 } else { ?><a href="../suggestion.php">Leave a suggestion</a><?php	                                       			 } ?></td>
			<td align="right" colspan="3"><a href="custom_report.php">Work Reports</a></td>
		  </tr>-->
        </table>
      </div></td>
</tr>
