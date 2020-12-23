<?php
/**
 * Import files inside google drive
 */
include "../vendor/autoload.php";
require '../config.php';
require 'function.php';

$dbpath = '../'.$dbpath;
require '../function.php';

$db = getDatabase();

$drives = explode("\n",str_replace("\r","",file_get_contents("./folder.txt")));
foreach($drives as $drive){
    echo "$drive\n";
    $drive = explode(" ",$drive);
    $idfolder = trim($drive[0]);
    $folder = trim($drive[1]);

    $jsoncredential = json_decode(file_get_contents("token/credentials.txt"),true);
    $sisa = time()-filemtime("token/credentials.txt");
    if($sisa>$jsoncredential['expires_in']-300){
        updateToken();
    }
    $pathPageToken = "./temp/$idfolder.token";
    $pageToken = (file_exists($pathPageToken))?file_get_contents($pathPageToken):null;

    ulang:
    echo $pageToken;

    $list =  listFiles($idfolder,$pageToken);

    foreach($list['items'] as $item){
        if($item['fileExtension']=='nsz' || $item['fileExtension']=='xci'){
            if($item['parents'][0]['id'] == $idfolder && !empty($item['title'])){
                $gameid = getGameID($item['title']);
                $gameName = $db->get("t_games","name",['titleid'=>$gameid]);
                if(empty($gameName)) $gameName = str_replace([".xci",".nsp",".nsz"],"",$item['title']);
                $db->insert('t_games_url',[
                    'url'=>$item['id'],
                    'filename'=>$item['title'],
                    'title'=>$gameName,
                    'titleid'=>$gameid,
                    'fileSize'=>$item['fileSize'],
                    'md5Checksum'=>$item['md5Checksum'],
                    'root'=>$idfolder,
                    'owner'=>trim($item['owners'][0]['emailAddress']),
                    'folder'=>$folder,
                    'shared'=>($item['shared'])?"1":"0",
                    ]);
                $id = $db->id();
                if($id){
                    echo $db->id()." - ".$item['title']."\n";
                }else{
                    echo json_encode($db->error())."\n".$item['title']."\n";
                }
            }else{
                echo "Parents different ".$item['parents'][0]['id']."\n";
            }
        }else{
            echo "NOT XCI/NSZ - ".$item['title']."\n";
        }
    }


    if(isset($list['nextLink']) && !empty($list['nextLink'])){
        $pageToken = $list['nextLink'];
        file_put_contents("$pathPageToken",$pageToken);
        if(!empty($pageToken))
            goto ulang;
        else die("EMPTY $idfolder");
    }

    file_put_contents("$pathPageToken",'');
    echo "$idfolder FINISH\n\n";
}
echo "\n\nDONT FORGET TO RUN clean.php from browser\n\n";
