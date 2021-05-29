<?php
require 'vendor/autoload.php';
require 'config.php';
require 'function.php';
use Medoo\Medoo;

if($must_login){
    $pin = require_auth()."@";
}else{
    $pin = "";
}

$db = getDatabase();

header("Content-Type: application/json");

if(!isset($url_server)){
    $_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$pin$_SERVER[HTTP_HOST]";
}else{
    $_host = $url_server;
}

if(empty($drive_url)){
    $drive_url = 'https://docs.google.com/uc?export=download&id=';
}

    //Parsing URL
$_path = array_values(array_filter(explode("/", parse_url($_SERVER['REQUEST_URI'])['path'])));

if(!empty($_path[0])){
    $_path[0] = str_replace('.json','',$_path[0]);
    $folder = alphanumeric($_path[0]);
    if($db->has("t_games_url",['folder'=>$folder])){
        header('Content-Disposition: filename="'.$folder.'.json"');
        $cacheFile = "./cache/".md5($folder.$dbpass).".json";

        if(file_exists($cacheFile )){
            readfile($cacheFile );
        }else{
            $json = array();
            $games = $db->select("t_games_url",['url', 'filename','titleid', 'fileSize'],['AND'=>['folder'=>$folder,'shared'=>1],'ORDER'=>['title'=>'ASC']]);
            foreach($games as $game){
                if(!empty($game['filename']) && !empty($game['titleid'])){
                    // if start with http
                    if(substr($game['url'],0,4)=='http'){
                        if($game['fileSize']>0){
                            $json[] = [
                                'url'=>$game['url'].'#'.urlencode(str_replace('#','',$game['filename'])),
                                'size'=>intval($game['fileSize'])
                            ];
                        }else{
                            $json[] = $game['url'].'#'.urlencode(str_replace('#','',$game['filename']));
                        }
                    // if start with / add url
                    }else if(substr($game['url'],0,1)=='/'){
                        if($game['fileSize']>0){
                            $json[] = [
                                'url'=>$_host.$game['url'].'#'.urlencode(str_replace('#','',$game['filename'])),
                                'size'=>intval($game['fileSize'])
                            ];
                        }else{
                            $json[] = $_host.$game['url'].'#'.urlencode(str_replace('#','',$game['filename']));
                        }
                    }else{
                        if($game['fileSize']>0){
                            $json[] = [
                                'url'=>$drive_url.$game['url'].'#'.urlencode(str_replace('#','',$game['filename'])),
                                'size'=>intval($game['fileSize'])
                            ];
                        }else{
                            $json[] = $drive_url.$game['url'].'#'.urlencode(str_replace('#','',$game['filename']));
                        }
                    }
                }
            }
            file_put_contents($cacheFile ,json_encode(['total'=>count($json),'files'=>$json]));
            echo json_encode(['total'=>count($json),'files'=>$json]);
        }
        die();
    }
}

$folders = $db->select("t_games_url", ['folder'=>Medoo::raw('DISTINCT folder')]);
$json = [
    'success' => $motd
];

foreach($folders as $folder){
    $json['locations'][] = $_host."/".$folder['folder'].'/index.json';
}

echo json_encode($json);
