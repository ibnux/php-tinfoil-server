<?php

// mysql or sqlite
$dbtype = "mysql";

// MYSQL
$dbname = 'nintendo';
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '##mysql';

// SQLITE
$dbpath = "data/base.db";

$motd   = "Use as needed, download only what you want to play, so you don't get to the limit quickly. Have a Google Drive to share too? mention @ibnux | donate paypal.me/ibnux";

$must_login = false;


// GOOGLE DRIVE AUTH CONFIGURATION
// https://www.iperiusbackup.net/en/how-to-enable-google-drive-api-and-get-client-credentials/
// but in OAuth consent screen, just select internal, for your own use only, if it can be selected
$client_id = "1522280-sas.apps.googleusercontent.com";
$client_sc = "sasa-bU72HeBGpLeZol";
$scopes = [ 'https://www.googleapis.com/auth/drive',
            'https://www.googleapis.com/auth/drive.file',
            'https://www.googleapis.com/auth/drive.appdata',
            'https://www.googleapis.com/auth/drive.apps.readonly',
            'https://www.googleapis.com/auth/drive.photos.readonly'
        ];

// ADD REDIRECT To Credential API
$redirect = "http://".$_SERVER['HTTP_HOST']."/import/login.php";

// END GOOGLE DRIVE AUTH CONFIGURATION

// EDIT ONLY IF LOGIN true
// only SMS Gateway with simple API
$sms_server = "https://some.sms.server/sms.php?to={phone}&msg={message}";

$msg_login_info = $_SERVER['HTTP_HOST'].' use your phone number (cth: 0812345678) as Username, password qwe, real password will be send to your phone number';
$msg_pass_send  = "Password has been sent to your number";
$msg_sms_text   = "Your Tinfoil Password: {{pin}}\n\nIgnore if you dont request password";