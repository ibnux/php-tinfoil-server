<?php
/**
 * Import files inside google Team drive
 */
echo "Import files inside google Team drive\n";
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

    ulang:
    $jsoncredential = json_decode(file_get_contents("token/creds.txt"),true);
    $sisa = time()-filemtime("token/creds.txt");
    if($sisa>$jsoncredential['expires_in']-300){
        updateToken();
    }
    $pathPageToken = "./temp/$idfolder.token";
    $pageToken = (file_exists($pathPageToken))?file_get_contents($pathPageToken):null;

    echo $pageToken;

    $list =  listFolder($idfolder,$pageToken);

    foreach($list['items'] as $itm){
        $item = getFile($itm['id']);
        if($item['fileExtension']=='nsz' || $item['fileExtension']=='xci'){
            if($item['parents'][0]['id'] == $idfolder && !empty($item['name'])){
                $gameid = getGameID($item['name']);
                $gameName = $db->get("t_games","name",['titleid'=>$gameid]);
                if(empty($gameName)) $gameName = str_replace([".xci",".nsp",".nsz"],"",$item['name']);
                $db->insert('t_games_url',[
                    'url'=>$item['id'],
                    'filename'=>$item['name'],
                    'title'=>$gameName,
                    'titleid'=>$gameid,
                    'fileSize'=>$item['size'],
                    'md5Checksum'=>$item['md5Checksum'],
                    'root'=>$idfolder,
                    'owner'=>trim($item['driveId']),
                    'folder'=>$folder,
                    'shared'=>($item['viewersCanCopyContent'])?"1":"0",
                    ]);
                $id = $db->id();
                if($id){
                    echo $db->id()." - ".$item['name']."\n";
                }else{
                    echo json_encode($db->error())."\n".$item['name']."\n";
                }
            }else{
                echo "Parents different ".$item['parents'][0]['id']."\n";
            }
        }else{
            echo "NOT XCI/NSZ - ".$item['name']."\n";
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
