<?php

//the fqdn for the ad domain, company.local, etc.
$ldap_host = "domain here"; // enter your domain in domain.extension format: e.g. abc.com
	
//the ou the you will be pulling results from, it reads backwards - you may have to us CN= or OU=, have seen both work
$base_dn = "OU=ou here, DC=abc, DC=com";  //enter the primary OU here, then the first part of the domain name, then the second:  e.g:  "OU=users,  DC=abc, DC=com"

//the username for the domain, most users have read rights to ad
$ldap_user  = "user@abc.com";  

//the password for that username	
$ldap_pass = "password";

?>
