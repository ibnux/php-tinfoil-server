<?php

/**
 * Import files inside google drive
 */
if (php_sapi_name() !== 'cli') {
    die("Run only from CLI");
}
echo "Import files inside google drive\n";
include "../vendor/autoload.php";
require '../config.php';
require 'function.php';
$dbpath = '../' . $dbpath;
require '../function.php';
$cleanTabel = false;
$cleanCache = false;

updateToken();
$jsoncredential = json_decode(file_get_contents("token/creds.txt"), true);
$filemtime = filemtime("token/creds.txt");

if (in_array("table", $argv)) {
    $cleanTabel = true;
} else {
    echo "Clean table before import? (y/n) : ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        $cleanTabel = true;
        echo "Clean table $line";
    }
}

if (in_array("cache", $argv)) {
    $cleanCache = true;
} else {
    echo "Clean cache after import? (y/n) : ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        $cleanCache = true;
        echo "Clean cache $line";
    }
}

$db = getDatabase();
if ($cleanTabel) {
    $db->exec("DELETE FROM t_games_url");
    echo "TABLE count " . $db->count("t_games_url") . " lines\n";
}

$drives = explode("\n", str_replace("\r", "", file_get_contents("./folder.txt")));
$n = 0;
foreach ($drives as $drive) {
    echo "$drive\n";
    $drive = explode(" ", $drive);
    if (count($drive) >= 2) {
        $idfolder = trim($drive[0]);
        $folder = trim($drive[1]);
        scanFolder($folder, $idfolder, $pageToken);
        echo "$idfolder FINISH\n\n";
    }
}
echo "$n games inserted\n";

function scanFolder($folder, $idfolder, $pageToken){
    global $db, $n, $filemtime, $jsoncredential ;
    echo "SCANNING $idfolder FINISH\n\n";
    while(true){
        echo "remaining ".time()." - $filemtime = ".(time()-$filemtime)."\n";
        if(time()-$filemtime>3400){
            updateToken();
            $jsoncredential = json_decode(file_get_contents("token/creds.txt"), true);
            $filemtime = filemtime("token/creds.txt");
        }
        echo $pageToken;
        $list =  listFiles($idfolder, $pageToken);
        $allowed_ext = ['xci','nsp','nsz'];
        foreach ($list['files'] as $item) {
            if (!$db->has('t_games_url', ['url' => $item['id']])) {
                if (in_array($item['fileExtension'],$allowed_ext)) {
                    if ($item['parents'][0] == $idfolder && !empty($item['name'])) {
                        $gameid = getGameID($item['name']);
                        $games = $db->get("t_games", ["name",'size'], ['titleid' => $gameid]);
                        $gameName = $games['name'];
                        if($item['size']==0){
                            $item['size'] = $games['size']*1;
                        }
                        if (empty($gameName)) $gameName = str_replace([".xci", ".nsp", ".nsz"], "", $item['name']);
                        if(empty($item['driveId'])){
                            $db->insert('t_games_url', [
                                'url' => $item['id'],
                                'filename' => $item['name'],
                                'title' => $gameName,
                                'titleid' => $gameid,
                                'fileSize' => $item['size'],
                                'md5Checksum' => $item['md5Checksum'],
                                'root' => $idfolder,
                                'owner' => trim($item['owners'][0]['emailAddress']),
                                'folder' => $folder,
                                'shared' => 1, // user must share it
                            ]);
                        }else{
                            $db->insert('t_games_url', [
                                'url' => $item['id'],
                                'filename' => $item['name'],
                                'title' => $gameName,
                                'titleid' => $gameid,
                                'fileSize' => $item['size'],
                                'md5Checksum' => $item['md5Checksum'],
                                'root' => $idfolder,
                                'owner' => $item['driveId'],
                                'folder' => $folder,
                                'shared' => 1, // user must share it
                            ]);
                        }
                        if ($db->has('t_games_url', ['url' => $item['id']])) {
                            $n++;
                            echo "$n INSERTED " . $item['id'] . " - " . $item['name'] . "\n";
                        } else {
                            echo json_encode($db->error()) . "\n" . $item['name'] . "\n";
                        }
                    } else {
                        echo "Parents different " . $item['parents'][0] . "\n";
                    }
                } else {
                    echo "NOT XCI/NSZ $item[mimeType]\n";
                    //print_r($item);
                    if($item['mimeType']=='application/vnd.google-apps.folder'){
                        scanFolder($folder, $item['id'], null);
                    }
                }
            } else {
                echo "EXISTS " . $item['id'] . " - " . $item['name'] . "\n";
            }
        }
        $pageToken = $list['nextPageToken'];
        if(empty($pageToken)) break;
    }
    // check subfolder
    // $pageToken = null;
    // while(!true){
    //     $folders = listFolder($idfolder,$pageToken);
    //     $pageToken = $folders['nextPageToken'];
    //     foreach($folders['items'] as $fdr){
    //         echo "subfolder scan $fdr[id] \n";
    //         scanFolder($folder, $fdr['id'], null);
    //     }
    //     if(empty($pageToken)) break;
    // }

}

if ($cleanCache) {
    $files = scandir("../cache/");
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'json') {
            unlink("../cache/$file");
            echo "DELETE CACHE ../cache/$file\n";
        }
    }
}else{
    echo "\n\nDONT FORGET TO RUN clean.php from browser\n\n";
}
if ($dbtype == "sqlite") $db->exec("VACUUM");
