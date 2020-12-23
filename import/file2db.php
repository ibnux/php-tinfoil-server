<?php
/**
 * Reserved for scanning folder and add it to database
 */
include "../vendor/autoload.php";
require '../config.php';
require 'function.php';

$dbpath = '../'.$dbpath;
require '../function.php';

$db = getDatabase();

$path = "../data/games/";
$folders = scandir($path);
foreach($folders as $folder){
    echo "$folder\n";
    if(is_dir($path.$folder) && !in_array($folder,['.','..'])){
        $files = scandir($path.$folder);
        foreach($files as $file){
            echo "$folder/$file\n";
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($ext,['nsp','xci','nsz'])){
                $gameid = getGameID($file);
                $gameName = $db->get("t_games","name",['titleid'=>$gameid]);
                if(empty($gameName)) $gameName = str_replace([".xci",".nsp",".nsz"],"",$file);
                $db->insert('t_games_url',[
                    'url'=>str_replace("../","/",$path."$folder/$file"),
                    'filename'=>$file,
                    'title'=>$gameName,
                    'titleid'=>$gameid,
                    'fileSize'=>filesize($path."$folder/$file"),
                    'md5Checksum'=>md5_file($path."$folder/$file"),
                    'root'=>$folder,
                    'owner'=>'files',
                    'folder'=>$folder,
                    'shared'=>1,
                    ]);
                $id = $db->id();
                if($id){
                    echo $db->id()." - ".$file."\n";
                }else{
                    echo json_encode($db->error())."\n".$file."\n";
                }
            }
        }
    }
}