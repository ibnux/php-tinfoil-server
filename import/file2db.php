<?php
/**
 * Reserved for scanning folder and add it to database
 */
if(php_sapi_name() !== 'cli'){
    die("Run only from CLI");
}
include "../vendor/autoload.php";
require '../config.php';
require 'function.php';

$dbpath = '../'.$dbpath;
require '../function.php';
$cleanTabel = false;
$cleanCache = false;

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
updateToken();
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
                $link = str_replace("../","/",$path."$folder/$file");
                if (!empty($gameid) && !$db->has('t_games_url', ['url' => $link])) {
                    $gameName = $db->get("t_games","name",['titleid'=>$gameid]);
                    if(empty($gameName)) $gameName = str_replace([".xci",".nsp",".nsz"],"",$file);
                    $db->insert('t_games_url',[
                        'url'=>$link,
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
                    if ($db->has('t_games_url', ['url' => $link])) {
                        $n++;
                        echo $gameid." - ".$file."\n";
                    }else{
                        echo json_encode($db->error())."\n".$file."\n";
                    }
                }else{
                    echo $file." no [GAME ID]\n";
                }
            }
        }
    }
}
echo "$n games inserted\n";
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