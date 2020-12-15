<?php
$files = ['nsz', 'xci', 'updates', 'dlc'];
$gmss = array();
foreach ($files as $file) {
    $games = file_get_contents("$file/$file.txt");
    $games = explode("\n", str_replace("\r", "", $games));
    $html = '<!DOCTYPE html><html>
<head><title>Index of ' . $file . '</title><meta charset="utf-8"></head>
<body bgcolor="white">
<h1>Index ' . $file . '</h1><hr><pre><a href="../">../</a>'."\n";
    foreach ($games as $game) {
        if(checkFile($game)){
            $gms = explode("#", $game);
            $html .= '<a href="' . $game . '">' . $gms[1] . '</a>' . "\n";
        }
    }
    $gmss = array_merge($gmss, $games);
    $html .= '</pre><hr></body></html>';
    file_put_contents($file . '/index.html', $html);
    file_put_contents("$file/index.tfl", json_encode(['url' => $games, 'success' => 'Jangan di Abuse, beberapa game mungkin overload di download']));
}
file_put_contents("index.tfl", json_encode(['directories' => ['nsp', 'dlc', 'updates', 'xci'], 'success' => 'Jangan di Abuse, beberapa game mungkin overload di download']));


function checkFile($url){
    echo $url."\n";
    $hasil = get_headers($url);
    if(strpos($hasil[0],"200 OK")!==false){
        echo "OK\n";
        return ['OK'=>$hasil];
    }elseif($hasil[0]=='HTTP/1.0 302 Moved Temporarily'){
        foreach($hasil as $h){
            if(strpos($h,"Location:")!==false){
                return checkFile(substr($h,10,strlen($h)-10));
            }
        }
    }
    echo "NOT OK\n";
    return false;
}