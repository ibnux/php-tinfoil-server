<?php

function require_auth() {
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    $pesan = $_SERVER['HTTP_HOST'].' Masukkan Nomor HP (cth: 0812345678) anda sebagai Username, kosongkan password, password akan dikirim ke nomor anda via Whatsapp. Hanya nomor Indonesia yang bisa. nomor HP tidak disimpan di server';
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
            $pesan = "Password telah dikirimkan ke nomor Whatsapp $phone";
            sendSMS($phone,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
        }else{
            if(file_exists($pathUser)){
                $pin = file_get_contents($pathUser);
                if($pin!=$_SERVER['PHP_AUTH_PW']){
                    $pesan = "Password telah dikirimkan ke nomor Whatsapp $phone";
                    sendSMS($phone,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
                }else{
                    $is_not_authenticated = false;
                }
            }else{
                $pin = rand(1000,9999);
                file_put_contents($pathUser,$pin);
                $pesan = "Password telah dikirimkan ke nomor Whatsapp $phone";
                sendSMS($phone,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
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