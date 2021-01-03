<?php

function updateToken(){
    global $client_id ,$client_sc,$jsoncredential;
    echo "update Token\n";
    $refresh_token = file_get_contents("token/refresh.token");
    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        json_encode([
                'client_id' => $client_id,
                'client_secret' => $client_sc,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ]));
    $authToken = curl_exec($ch);
    curl_close($ch);
    $jsoncredential = json_decode($authToken,true);
    if(isset($jsoncredential['access_token'])){
        file_put_contents("token/creds.txt", $authToken);
    }
}


function listFiles($folderID,$nextToken=null){
    global $jsoncredential;
    if(!empty($nextToken)){
        $url = $nextToken;
    }else{
        $url = 'https://www.googleapis.com/drive/v3/files?q=\''.$folderID.'\'+in+parents&includeItemsFromAllDrives=true&supportsAllDrives=true&fields=*' ;
    }
    echo "$url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$jsoncredential['access_token']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}

function listFolder($folderID,$nextToken=null){
    global $jsoncredential;
    if(!empty($nextToken)){
        $url = $nextToken;
    }else{
        $url = 'https://www.googleapis.com/drive/v2/files/'.$folderID.'/children' ;
    }
    echo "$url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$jsoncredential['access_token']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}

function getFile($fileID){
    global $jsoncredential;
    $url = 'https://www.googleapis.com/drive/v3/files/'.$fileID.'?supportsAllDrives=true&fields=*' ;

    echo "$url\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$jsoncredential['access_token']));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result,true);
}

function getGameID($str){
    $regex = '/\[\w{16,16}\]/';
    preg_match_all($regex, $str, $matches);
    $hasil = str_replace("[","",str_replace("]","",$matches[0][0]));
    echo "GameID $hasil\n";
    return $hasil;
}