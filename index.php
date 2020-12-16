<?php
require 'vendor/autoload.php';
require 'config.php';
require 'function.php';
use Medoo\Medoo;

$pin = require_auth();

$db = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => $dbname,
	'server' => $dbhost,
	'username' => $dbuser,
    'password' => $dbpass
]);

header("Content-Type: application/json");

$_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$pin@$_SERVER[HTTP_HOST]/";
//Parsing URL
$_path = array_values(array_filter(explode("/", parse_url($_SERVER['REQUEST_URI'])['path'])));

if(!empty($_path[0])){
    $folder = alphanumeric($_path[0]);
    if($db->has("t_games",['folder'=>$folder])){
        header('Content-Disposition: filename="'.$folder.'.json"');
        if(file_exists("./cache/$folder.json")){
            readfile("./cache/$folder.json");
        }else{
            $json = array();
            $games = $db->select('t_games',['id', 'title', 'fileSize'],['folder'=>$folder,'ORDER'=>['title'=>'ASC']]);
            foreach($games as $game){
                if(!empty($game['title'])){
                    if($game['fileSize']>0){
                        $json[] = [
                            'url'=>'https://docs.google.com/uc?export=download&id='.$game['id'].'#'.urlencode(str_replace('#','',$game['title'])),
                            'size'=>$game['fileSize']
                        ];
                    }else{
                        $json[] = 'https://docs.google.com/uc?export=download&id='.$game['id'].'#'.urlencode(str_replace('#','',$game['title']));
                    }
                }
            }
            file_put_contents("./cache/$folder.json",json_encode(['files'=>$json]));
            echo json_encode(['files'=>$json]);
        }
        die();
    }
}

$folders = $db->select('t_games', ['folder'=>Medoo::raw('DISTINCT folder')], $where);
$json = [
    'success' => 'Pakai Seperlunya, jangan di share kecuali ke member grup aja, biar ngga ditegur tendo. punya google drive mau dishare juga? mention @ibnux | Donasi Biaya Server trakteer.id/ibnux karyakarsa.com/ibnux'
];

foreach($folders as $folder){
    $json['locations'][] = $_host.$folder['folder'];
}

echo json_encode($json);
