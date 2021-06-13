<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

/**
 * Import files inside google drive
 * Remove Duplicate
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
if(file_exists("./temp/folder.txt")){
    $finished = file_get_contents("./temp/folder.txt");
}else{
    $finished = "";
}

$n = 0;
$c = 0;
foreach ($drives as $drive) {
    $drv = explode(" ", $drive);
    if(strpos($finished,$drv[0])===false){
        echo "$drv[0]\n";
        if (count($drv) >= 2) {
            $idfolder = trim($drv[0]);
            $folder = trim($drv[1]);
            scanFolder($folder, $idfolder, '');

        }
        file_put_contents("./temp/folder.txt",$drv[0]."\n",FILE_APPEND);
        $finished = $drv[0]."\n";
        echo "$idfolder FINISH\n\n";
    }else{
        echo "$drv[0] ALREADY DONE\n";
    }
}
echo "$c files scanned\n";
echo "$n games inserted\n";
if(file_exists("./temp/folder.txt")) unlink("./temp/folder.txt");


function scanFolder($folder, $idfolder, $pageToken){
    global $db, $n, $c, $filemtime, $jsoncredential,$delete_duplicate;
    echo "SCANNING $idfolder\n\n";
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
            $c++;
            if (!$db->has('t_games_url', ['url' => $item['id']])) {
                if (in_array($item['fileExtension'],$allowed_ext)) {
                    if ($item['parents'][0] == $idfolder && !empty($item['name'])) {
                        $md5Checksum = $db->get('t_games_url','md5Checksum',['AND'=>['filename'=>$item['name'],'owner'=>[trim($item['owners'][0]['emailAddress']),$item['driveId']]]]);
                        if(!empty($md5Checksum) && $md5Checksum==$item['md5Checksum']){
                            if($delete_duplicate){
                                $del = delFile($item['id']);
                                if(!empty($del['error'])){
                                    echo $del['error']['message']."\n";
                                }
                            }
                        }else{
                            $fileSize = $db->get('t_games_url','fileSize',['AND'=>['md5Checksum'=>$item['md5Checksum'],'owner'=>[trim($item['owners'][0]['emailAddress']),$item['driveId']]]]);
                            if(!empty($fileSize) && $fileSize==$item['size']){
                                if($delete_duplicate){
                                    $del = delFile($item['id']);
                                    if(!empty($del['error'])){
                                        echo $del['error']['message']."\n";
                                    }
                                }
                            }else{
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
                            }
                        }
                    } else {
                        echo "Parents different " . $item['parents'][0] . "\n";
                    }
                } else {
                    echo "NOT XCI/NSZ $item[mimeType]\n";
                    //Scan subfolder
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
    echo "DONE SCANNING $idfolder\n\n";
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
