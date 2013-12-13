<html>

<head>
<script>

//function setFocus() {document.searchform.criteria.focus();}
</script>

<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">

<link rel="stylesheet" type="text/css" href="default.css">
<title>Active Directory Phonebook</title></head>

<body onLoad="setFocus()" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0"> 

<table border="0" width="100%" id="table1" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" valign="top">
		<table border="0" width="600" id="table2" cellspacing="0" cellpadding="0">

			<tr>
				<td align="left">


<div class="imgblock"><p style="float: right; clear: right"><a href="./" Title="Hopkins Schools Staff Listings" Alt="Hopkins Schools Staff Listings"><img src="./images/pbooklogo.png" Title"Hopkins Schools Staff Listings" Alt="Hopkins Schools Staff Listings"></a></div><br /><br />

<p><form method="GET" action="<?php echo $PHP_SELF; ?>" name="searchform">
	<input type="text" name="criteria" size="30"><input type="submit" value="Search" name="submit"><br></p>
	<p><input type="radio" value="l_name" name="params">Last Name*&nbsp;&nbsp;
	<input type="radio" value="f_name" name="params"> First Name*&nbsp;&nbsp;
	<input type="radio" value="district" name="params"> Location*&nbsp;&nbsp;
	<!--<input type="radio" value="dept" name="params"> Department*&nbsp;&nbsp;-->
	<input type="radio" value="all" name="params" checked> All*<br>
	<br></p>

<p style="float: left; clear left">When calling within our District phone network, dial only the last four digits. Building codes are listed below. Right Click on a name to copy a person's email addresss or click the name to send an email using your email program on your computer. 
</p>
<br>
<br>
<p style="float: left; clear left">* Your search does not need to be a complete word or number, the first few letters are acceptable. </p>
<br>
<p style="float: left; clear left">To Email a staff member please click on the first or last name.</p>
</form>
				
        </td>
                        </tr>
			

<?php

require ('./ldap.config.php');
$params = $_GET['params'];
$criteria = $_GET['criteria'];
$name = $_GET['name'];


//Check if name has been clicked on
if ($name) {
	// if the users name has been clicked on then it deisplays the ID card
 	$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(samaccountname=$name))"; //This tells it not to pick up users who are disabled
	
	//this selects the fields you want to display on the ID card - you may wish to change these for your requirements
	$get_this=array("co", "streetaddress", "st", "postalcode", "l", "cn", "samaccountname", "physicaldeliveryofficename", "facsimiletelephonenumber", "title", "mobile", "mail", "givenname", "sn", "telephonenumber", "ipphone", "department", "mobile", "homephone" );
	
	$connect = ldap_connect( $ldap_host, $ldap_port)
		or exit(">>Could not connect to LDAP server<<");
		
	//for win2003
	ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	//for win2003
	ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
	
	//this is where the username and password are used to make the ldap connection
	$bind = ldap_bind($connect, $ldap_user, $ldap_pass)
	     or exit(">>Could not bind to $ldap_host<<");
	
	//search ad ldap
	$read = ldap_search($connect, $base_dn, $filter, $get_this)
	     or exit(">>Unable to search ldap server<<");
	
	//sort the results by the user specifications
	ldap_sort($connect, $read, $sort_by);
	
	//get the entries and put into a multi dimensional array
	$info = ldap_get_entries($connect, $read);
	
	// Get address details from OU.
	$ou = $info[0]["physicaldeliveryofficename"][0];
	
	$filter2="(ou=$ou)";
	
	//this picks up the address from the OU which the person is contained within (it gets the OU name "office" field in the users profile.  E.g. the users office is set to "head office" and the OU they are in is "head office"
	$get_this2=array("street", "l", "st", "postalcode", "co",);
	
	//search ad ldap for ou details
	$read2 = ldap_search($connect, $base_dn, $filter2, $get_this2)
	     or exit(">>Unable to search ldap server<<");
		 
	//get the  OU entries and put into a multi dimensional array
	$info2 = ldap_get_entries($connect, $read2);

	
?>

	
<tr><td align="center" colspan="4">		
<?php 
	//this is the ID card code	
if (!file_exists("./images/photos/$name.png")) {
		$imagename = "idcard-nopic.png";
		$title = "If you would like your image here, send a digital photo to email@yourdomain.com";
	} else {
		$imagename = "$name.png";
		$title = "$name";
	}

	if ($info[0]["ipphone"][0]) {
		$ext = " - Ext (".$info[0]["ipphone"][0].")";
	}
	
	$email = $info[0]["mail"][0];
	$dept = $info[0]["department"][0];

	echo "<div id=\"idcard\">";
		echo "<div id=\"photo\">";
			echo "<a href=\"mailto:email@yourdomain.com?subject=Please add my picture to my intranet profile&body=Please attach a photo of yourself to this email before sending\"><img src=\"./images/photos/$imagename\" title=\"$title\"></a>";
		echo "</div>";
		echo "<div id=\"idtext\">";
			echo "<h1>".$info[0]["cn"][0]."</h1>";
			echo "<span class=\"title\">".$info[0]["description"][0]."</span>";
			echo "<div class=\"idtextlabels\">";
				if ($dept) { echo "Department:<br />"; }
				echo "E-mail: <br />";
				echo "Location: <br />";
				echo "Telephone: <br />";
				echo "Mobile: <br />";
				echo "Fax: <br />";
			echo "</div>";
			echo "<div class=\"idtextvalues\">";
				if ($dept) { echo "$dept <br />"; }
				echo "<a href=\"mailto:$email\" title=\"Send e-mail to: ".$info[0]["cn"][0]."\">$email</a><br />";
				echo $info[0]["physicaldeliveryofficename"][0]."<br />";
				echo $info[0]["telephonenumber"][0]." $ext <br />";
				echo $info[0]["mobile"][0]."<br />";
				echo $info[0]["facsimiletelephonenumber"][0]."<br />";
			
			echo "</div>";
			echo "</div>";
		echo "<div id=\"idtext2\">";
			$roaming = $info2[0]["street"][0];
			if ($roaming != "Roaming User - no fixed location") {
				echo "<a href=\"http://maps.google.co.uk/maps?f=q&hl=en&geocode=&q=".$info2[0]["postalcode"][0]."\" Title=\"Click to view map of location\" Target=\"_new\">".$info2[0]["street"][0].", ".$info2[0]["l"][0].", ".$info2[0]["st"][0].", ".$info2[0]["postalcode"][0].",".$info2[0]["co"][0]."</a>";
			}
			else {
				echo "Roaming Staff - No Fixed Location";
			}
			echo "<hr />";
		echo "</div>";
	echo "</div>";

	echo "<h3>Are these details correct? If not <a href=\"mailto:email@yourdomain.com?subject=Contact Information Update: ".$info[0]["cn"][0]."\">Click Here!</a> to e-mail us with correct details</h3>";
	
	//diconnect from the ldap dbase
	ldap_close($connect);
}
?>  <!-- Commented out by Andy Hakala. Can be readded for ID card functionality -->
</td></tr>  
<?php 

//if the search parameters are sent, then start the results output
if ($params)
{

	//details what to do given the different type of searches, firstname, last, etc.
	if ($params == "all")
	{
		$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(|(sn=$criteria*)(givenname=$criteria*)(cn=$criteria)(physicaldeliveryofficename=$criteria*)))";
		// (&(objectClass=User)(!(userAccountControl:1.2.840.113556.1.4.803:=2))
		if (!$sort_by){$sort_by = "sn";}
	
	}
	elseif ($params == "f_name")
	{
		$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(givenname=$criteria*))";
		if (!$sort_by){$sort_by = "cn";}
	}
	
	elseif ($params == "l_name")
	{
		$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(sn=$criteria*))";
		if (!$sort_by){$sort_by = "cn";}
	}
	elseif ($params == "dept")
	{
		$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(department=$criteria*))";
		if (!$sort_by){$sort_by = "cn";}
	}
	elseif ($params == "district")
	{
		$filter = "(&(!(userAccountControl:1.2.840.113556.1.4.803:=2))(physicaldeliveryofficename=*$criteria*))";
		if (!$sort_by){$sort_by = "physicaldeliveryofficename";}
	}
	//the fields that the search will pull from in ad
	$get_this=array("cn", "samaccountname", "physicaldeliveryofficename", "facsimiletelephonenumber", "mobile", "mail", "givenname", "sn", "telephonenumber", "homephone", "mobile", "Title" );
	//HOPKINS STARTS HERE	
	//make the ldap connection
	$connect = ldap_connect( $ldap_host, $ldap_port)
	         or exit(">>Could not connect to LDAP server<<");
	
	//for win2003
	ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	
	//for win2003
	ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
	
	//this is where the username and password are used to make the ldap connection
	$bind = ldap_bind($connect, $ldap_user, $ldap_pass)
	     or exit(">>Could not bind to $ldap_host<<");
	
	//search ad ldap
	$read = ldap_search($connect, $base_dn, $filter, $get_this)
	     or exit(">>Unable to search or no details entered!<<");
	//sort the results by the user specifications
	ldap_sort($connect, $read, $sort_by);
	//get the entries and put into a multi dimensional array
	$info = ldap_get_entries($connect, $read);
	echo "<tr>";
	echo "<td align=\"center\">";
	echo "<p align=\"left\">";

	//print the number, if any, of results
	
	if ($info["count"] == 1)
	{
		echo "<h3><b>".$info["count"]." result for \"$criteria\"</b></h3>";
	}elseif ($info["count"] != 0) {
		echo "<h3><b>".$info["count"]." results for \"$criteria\"</b></h3>";
	}

	if ($info["count"] != 0) {
?>


</td>
			</tr>
			<tr>
				<td align="center">
<table border="1" width="75%" id="table4" bordercolorlight="#FFFFFF">
	<tr>
		<td width="16.6%" bgcolor="#1874CD"><a style="color: #DBDBDB" href="<?php echo "$PHP_SELF?params=$params&criteria=$criteria&sort_by=sn"; ?>"><b>Last Name <? if ($sort_by == "sn" || $sort_by == "cn"){echo "»"; } ?></b></a></td>
		<td width="16.6%" bgcolor="#1874CD"><a style="color: #DBDBDB" href="<?php echo "$PHP_SELF?params=$params&criteria=$criteria&sort_by=givenname"; ?>"><b>First Name <? if ($sort_by == "givenname"){echo "»"; } ?></b></a></td>
		<td width="16.6%" bgcolor="#1874CD"><a style="color: #DBDBDB" href="<?php echo "$PHP_SELF?params=$params&criteria=$criteria&sort_by=physicaldeliveryofficename"; ?>"><b>Location <? if ($sort_by == "physicaldeliveryofficename"){echo "»"; } ?></b></a></td>
		<td width="16.6%" bgcolor="#1874CD"><a style="color: #DBDBDB" href="<?php echo "$PHP_SELF?params=$params&criteria=$criteria&sort_by=telephonenumber"; ?>"><b>Phone <? if ($sort_by == "telephonenumber"){echo "»"; } ?></b></a></td>
		<td width="10%" bgcolor="#1874CD" ><font color="#DBDBDB"><b>Alternate Phone</b></font></td>
                <td width="10%" bgcolor="#1874CD" ><font color="#DBDBDB"><b>Department</b></font></td>
	</tr>
	
<?php

//start the loop for printing the results
for ($a=0; $a<$info["count"]; $a++)
{
		//make every other row colored, feel free to change the colors
		if ($a % 2 == 0)
		{
			$color = "#d8d2e3";
		}else{
			$color = "#f1eff5";
		}
		 //if ($info[$a]["description"][0] != "Admin Account") { --- This line is commented out.  We used the desciption field again to define if the account was a "special" account and typed in Admin Account, this line of the code detects if this is in the desctiption field.
			$email = $info[$a]["mail"][0];
			$accountname = $info[$a]["samaccountname"][0];
			echo "<tr>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\"><a href=\"mailto:$email\" title=\"Email ".$info[$a]["givenname"][0]." ".$info[$a]["sn"][0]."\"><b>".$info[$a]["sn"][0]."</b></a>&nbsp;</td>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\"><a href=\"mailto:$email\" title=\"Email ".$info[$a]["givenname"][0]." ".$info[$a]["sn"][0]."\"><b>".$info[$a]["givenname"][0]."</b></a>&nbsp;</td>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\"><a href=\"?criteria=".$info[$a]["physicaldeliveryofficename"][0]."&params=district\" Title=\"Click here to find more people at ".$info[$a]["physicaldeliveryofficename"][0]."\">".$info[$a]["physicaldeliveryofficename"][0]."</a>&nbsp;</td>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\">".$info[$a]["telephonenumber"][0]."&nbsp;</td>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\">".$info[$a]["homephone"][0]."&nbsp;</td>";
			echo "<td bgcolor=\"$color\" width=\"10.5%\">".$info[$a]["title"][0]."&nbsp;</td>";
			echo "</tr>";
		//}
	}
} else {
		echo "<h3><font color=\"#FF0000\"><b>".$info["count"]." results for \"$criteria\". Try again!</b></font></h3>";
	}
//diconnect from the ldap dbase
ldap_close($connect);
}

echo "</table>";
 
?>				
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>

</body>

</html>
