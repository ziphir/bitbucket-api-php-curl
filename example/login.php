<?php
set_time_limit(0);
session_start();

include 'bitbucket.class.php';

bitbucket::$user = "myusername"; //here username
bitbucket::$pass = "mypassword"; //here password
bitbucket::$useragent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3";
bitbucket::$base_url = "https://bitbucket.org";
bitbucket::$login_url = bitbucket::$base_url . "/account/signin/";

$bitbucket = new bitbucket;

if(!isset($_SESSION["csrftoken"]))
	$_SESSION["csrftoken"] = $bitbucket->csrftoken(); //Generate a CSRFToken

if(!isset($_SESSION["cookie"]))
	$_SESSION["cookie"] = "csrftoken=".$_SESSION["csrftoken"]; //Put the CSRFToken in cookie

bitbucket::$csrftoken = $_SESSION["csrftoken"]; //Pass the CSRFToken to the $csrftoken variable of class

$bitbucket->login(bitbucket::$user, bitbucket::$pass);

session_destroy();

?>
