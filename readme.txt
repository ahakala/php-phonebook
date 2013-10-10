PHP - Active Directory Phone Book
Created By Darren McCabe - darren@techmonkeys.co.uk
===================================================
===================================================


===================================================================================================
How to use this phone book:

1 - Pre-Requisites:

	 - 1.1 Active Directory.
	 - 1.2 PHP (with ldap enabled).
	 - 1.3 Web Server.

2 - Changing the settings within the phonebook.

===================================================================================================

1.1 - Active Directory

	If you dont already have this then I am affraid it is a bit beyond the scope of this project to tell you how to install it.


1.2 - PHP (with ldap enabled)

	Instructions for installing PHP can be found here:

	Make sure you download the latest version of PHP regardless of what the install instructions say.
	- IIS : http://uk3.php.net/install.windows
	- Apache : http://www.php-mysql-tutorial.com/install-apache-php-mysql.php#php

	To enable ldap, open up your php.ini file and uncomment the line that says:

		extension=php_ldap.dll

	Restart your webserver after uncommenting the line and thats it, your off!

1.3 - Web Server
	
	If you dont already have a machine set up as a web server, the quickest and simplest way is to download something 
	like WAMP Server (http://www.wampserver.com/en/)  which will install Apache, MySQL and PHPMyAdmin all very simply 
	little/no configuration.

	Alternatively set up IIS on any windows based machine and that can become a web server.
		-Installing IIS on XP Pro: 	http://www.webwizguide.com/kb/asp_tutorials/installing_iis_winXP_pro.asp
		-Installing IIS on Server 2003: http://technet.microsoft.com/en-us/library/aa998483(EXCHG.65).aspx


2 - Changing the phonebook Settings

	Before you get in to this section, you should know I am not a programmer and make no warranty for the quality of code within this app.

	However these are the bits you may need to change:

	ldap.config.php - 	This contains the connection settings for your active directory.
				You will need to change the domain name, OU and username and password.

	index.php -		This contains the whole phonebook code.
				There are various items in here that you may want to change to suit your needs.
				I have commented the lines I think you may want info such as what fields it collects from the AD server.

	default.css -		This contatins the layout for the site, colours, image links etc. Customise to suit.

	/images/idcard-rev2.png - This is the background image for the ID card

	/images/pbooklogo.png - This is the logo at the top of the site.

	/images/photos/idcard-nopic.png - 	This is the blank photo that shows on the id card.
						If you want your users photo to appear simply stick the photos in this directory
						and call the filename the same as the users login name.
						e.g. if your username is "john.doe" then the image name should be "john.doe.png"


		