<?php

function require_auth() {
    header('Cache-Control: no-cache, must-revalidate, max-age=0');
    $is_not_authenticated = true;
    if(!empty($_SERVER['PHP_AUTH_USER'])){
        $nohp = alphanumeric($_SERVER['PHP_AUTH_USER']);
        $pathUser = "./data/user/".md5($nohp).".user";
        if(empty($_SERVER['PHP_AUTH_PW'])){
            if(file_exists($pathUser)){
                $pin = file_get_contents($pathUser);
            }else{
                $pin = rand(1000,9999);
                file_put_contents($pathUser,$pin);
            }
            sendWA($nohp,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
        }else{
            if(file_exists($pathUser)){
                $pin = file_get_contents($pathUser);
                if($pin!=$_SERVER['PHP_AUTH_PW']){
                    sendWA($nohp,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
                }else{
                    $is_not_authenticated = false;
                }
            }else{
                $pin = rand(1000,9999);
                file_put_contents($pathUser,$pin);
                sendWA($nohp,"Password Tinfoil anda: $pin\n\nHiraukan jika anda tidak merasa meminta Password ini");
            }
        }
    }
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
        header('WWW-Authenticate: Basic realm="Masukkan Nomor HP anda, lalu check Whatsapp"');
        $json = [
            'success' => $_SERVER['HTTP_HOST'].' Masukkan Nomor HP (cth: 0812345678) anda sebagai Username, kosongkan password, password akan dikirim ke nomor anda via Whatsapp. Hanya nomor Indonesia yang bisa. nomor HP tidak disimpan di server'
        ];
        echo json_encode($json);
		exit;
    }
    return $nohp.":".$pin;
}


function alphanumeric($str){
    return preg_replace("/[^a-zA-Z0-9 ]+/", "", $str);
}

function sendWA($nohp,$txt){
    $path = "./data/sms/".md5($nohp).".sms";
    if(file_exists($path)){
        if(time()-filemtime($path)>60)
            return file_get_contents("https://wa.ibnux.com/wa.php?to=$nohp&msg=" . urlencode($txt));
    }else{
        file_put_contents($path,$txt);
        return file_get_contents("https://wa.ibnux.com/wa.php?to=$nohp&msg=" . urlencode($txt));
    }
}