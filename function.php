<?php
use Medoo\Medoo;

function getDatabase(){
    global $dbtype,$dbname,$dbhost,$dbuser,$dbpass,$dbpath;
    if($dbtype=='mysql'){
        return new Medoo([
            // required
            'database_type' => 'mysql',
            'database_name' => $dbname,
            'server' => $dbhost,
            'username' => $dbuser,
            'password' => $dbpass
        ]);

    }else if($dbtype=='sqlite'){
        $nodb = false;
        if(!file_exists($dbpath)){
            $nodb = true;
        }
        $db = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => $dbpath
        ]);
        if($nodb){
            $db->exec("CREATE TABLE `t_games_url` (
                `nomor` int(11) PRIMARY KEY,
                `url` varchar(128) UNIQUE,
                `filename` varchar(256) DEFAULT '',
                `title` varchar(256) DEFAULT '',
                `titleid` varchar(16) DEFAULT '',
                `fileSize` varchar(128) DEFAULT '0',
                `md5Checksum` varchar(64) DEFAULT '',
                `folder` varchar(32) DEFAULT '',
                `root` varchar(64) DEFAULT '',
                `owner` varchar(64) DEFAULT '',
                `shared` tinyint(1) DEFAULT '1')");
            $db->exec("CREATE TABLE `t_games` (
                `titleid` varchar(16) NOT NULL,
                `name` varchar(256) NOT NULL,
                `image` varchar(512) NOT NULL DEFAULT '',
                `description` text NOT NULL,
                `publisher` varchar(64) NOT NULL,
                `languages` varchar(256) NOT NULL,
                `rating` tinyint(1) NOT NULL DEFAULT '0',
                `size` varchar(16) NOT NULL)");
        }
        return $db;
    }
}

function require_auth() {
    global $msg_pass_send,$msg_login_info,$msg_sms_text;
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    $pesan = $msg_login_info;
    $is_not_authenticated = true;
    if(!empty($_SERVER['PHP_AUTH_USER'])){
        $phone = alphanumeric($_SERVER['PHP_AUTH_USER']);
        $pathUser = "./data/user/".md5($phone).".user";
        if(empty($_SERVER['PHP_AUTH_PW'])){
            if(file_exists($pathUser)){
                $pin = file_get_contents($pathUser);
            }else{
                $pin = rand(1000,9999);
                file_put_contents($pathUser,$pin);
            }
            $pesan = $msg_pass_send;
            sendSMS($phone,str_replace("{{pin}}",$pin,$msg_sms_text));
        }else{
            if(file_exists($pathUser)){
                $pin = file_get_contents($pathUser);
                if($pin!=$_SERVER['PHP_AUTH_PW']){
                    $pesan = $msg_pass_send;
                    sendSMS($phone,str_replace("{{pin}}",$pin,$msg_sms_text));
                }else{
                    $is_not_authenticated = false;
                }
            }else{
                $pin = rand(1000,9999);
                file_put_contents($pathUser,$pin);
                $pesan = $msg_pass_send;
                sendSMS($phone,str_replace("{{pin}}",$pin,$msg_sms_text));
            }
        }
    }
	if ($is_not_authenticated) {
		$json = [
            'files'=>['https://docs.google.com/uc?export=download&id=1Y1MAceMkS-EJXqrOYyX5RnSGNi4l6gt-#YouTube[01003A400C3DA800][US][v196608].nsz'],
            'success' => $pesan
        ];
        echo json_encode($json);
		exit;
    }
    return $phone.":".$pin;
}


function alphanumeric($str){
    return preg_replace("/[^a-zA-Z0-9 _-]+/", "", $str);
}

function sendSMS($phone,$txt){
    global $sms_server;
    $path = "./data/sms/".md5($phone).".sms";
    $sms_server = str_replace("{phone}",$phone,$sms_server);
    $sms_server = str_replace("{message}",urlencode($txt),$sms_server);
    if(file_exists($path)){
        if(time()-filemtime($path)>300)
            return file_get_contents($sms_server);
    }else{
        file_put_contents($path,$txt);
        return file_get_contents($sms_server);
    }
}