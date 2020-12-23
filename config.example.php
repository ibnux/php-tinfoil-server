<?php
// URL domain https://domain/
// if you use docker, you need set this url
# $url_server = "https://ibnux.com";

// mysql or sqlite
$dbtype = "sqlite";

// MYSQL
$dbname = 'nintendo';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '##mysql';

// SQLITE
$dbpath = "data/base.db";

$motd   = "Use as needed, download only what you want to play, so you don't get to the limit quickly. Have a Google Drive to share too? mention t.me/ibnux | donate paypal.me/ibnux";

$must_login = false;


// GOOGLE DRIVE AUTH CONFIGURATION
// https://www.iperiusbackup.net/en/how-to-enable-google-drive-api-and-get-client-credentials/
// but in OAuth consent screen, just select internal, for your own use only, if it can be selected
// i share my conf, bas364 only to make google not notify me
$client_id = base64_decode("MTAyMDU3MDA2MzIwMS11Ymt2ZGM4bzNmMXNxOWl0N2pjOG9vZjQxazBoaDF2Zy5hcHBzLmdvb2dsZXVzZXJjb250ZW50LmNvbQ==");
$client_sc = base64_decode("cm5hTTdMQ1E1TjRYdVVYakZ0aFJBN2F6");
$scopes = [ 'https://www.googleapis.com/auth/drive.apps.readonly'];

// ADD REDIRECT To Credential API
$redirect = "http://".$_SERVER['HTTP_HOST']."/import/login.php";

// END GOOGLE DRIVE AUTH CONFIGURATION

// EDIT ONLY IF LOGIN true
// only SMS Gateway with simple API
$sms_server = "https://some.sms.server/sms.php?to={phone}&msg={message}";

$msg_login_info = $_SERVER['HTTP_HOST'].' use your phone number (cth: 0812345678) as Username, password qwe, real password will be send to your phone number';
$msg_pass_send  = "Password has been sent to your number";
$msg_sms_text   = "Your Tinfoil Password: {{pin}}\n\nIgnore if you dont request password";