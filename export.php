<?php
$file = $argv[1];
if(empty($file)){
    die("php export.php folder");
}

if(!file_exists("$file/db.txt")){
    die("$file Not found");
}

if(file_exists("$file/process.txt")){
    echo "$file/process.txt\n";
    $games = file_get_contents("$file/process.txt");
}else{
    echo "$file/db.txt\n";
    $games = file_get_contents("$file/db.txt");
}
$games = explode("\n", str_replace("\r", "", $games));
$jml = count($games);
for ($n=0;$n<$jml;$n++) {
    echo "$n ";
    $game = $games[$n];
    if (checkFile(explode("#",$game)[0])) {
        file_put_contents("$file/temp.txt",$game."\n",FILE_APPEND);
    }
    unset($games[$n]);
    file_put_contents("$file/process.txt",implode("\n",array_values($games)));
}
if(file_exists("$file/aktif.txt")) unlink("$file/aktif.txt");
rename("$file/temp.txt","$file/aktif.txt");
if(file_exists("$file/process.txt")) unlink("$file/process.txt");

function checkFile($url)
{
    echo $url . "\n";
    $hasil = get_headers($url);
    if (strpos($hasil[0], "200 OK") !== false) {
        echo "OK\n";
        return ['OK' => $hasil];
    } elseif ($hasil[0] == 'HTTP/1.0 302 Moved Temporarily') {
        foreach ($hasil as $h) {
            if (strpos($h, "Location:") !== false) {
                return checkFile(substr($h, 10, strlen($h) - 10));
            }
        }
    }
    echo $hasil[0] . "\n";
    return false;
}
