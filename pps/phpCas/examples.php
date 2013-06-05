<?php
/*
 * Created on 2012-6-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
include_once("CAS.php");
phpCAS::setDebug();
phpCAS::client(CAS_VERSION_2_0, 'localhost', 8443, 'cas');
phpCAS::setNoCasServerValidation();
phpCAS::forceAuthentication();
if(isset($_REQUEST['logout'])){
	phpCAS::logout();
}
?>
<html>
<head>
	<title>phpCAS simple client</title>
</head>
<body>
	<h1>Successfull Authentication</h1>
	<p>the user's login is <b><?=phpCAS::getUser()?></b></p>
	<p>phpCAS Version is <b><?=phpCAS::getVersion()?></b></p>
	<p><a href="?logout=">Logout</a></p>
</body>
</html>