<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require '../config.php';
require '../function.php';
$files = ['nsz', 'xci', 'updates', 'dlc','demos'];
foreach ($files as $file) {
    echo "$file\n";
    if(file_exists($file . '/index.html')) unlink($file . '/index.html');
    $games = $db->select('t_games',['id', 'title', 'fileSize'],['folder'=>$file,'ORDER'=>['title'=>'ASC']]);
    $html = '<!DOCTYPE html><html>
<head><title>Index of ' . $file . '</title><meta charset="utf-8"></head>
<body bgcolor="white">
<h1>Index ' . $file . '</h1><hr><pre><a href="../">../</a>'."\n";
    file_put_contents($file . '/index.html', $html,FILE_APPEND);
    foreach ($games as $game) {
        echo $game['title']."\n";
        if(!empty($game['title'])){
            $html = '<a href="https://docs.google.com/uc?export=download&id=' . $game['id'] . '#' . urlencode(str_replace('#','',$game['title'])) . '">' . str_replace('#','',$game['title'])  . "</a>".addSpace(str_replace('#','',$game['title'])).date("Y-m-d H:i:s")."\t\t\t\t".$game['fileSize']. "\n";
            file_put_contents($file . '/index.html', $html,FILE_APPEND);
        }
    }
    $html = '</pre><hr></body></html>';
    file_put_contents($file . '/index.html', $html,FILE_APPEND);
}

function addSpace($txt){
    $hsl = 128 - strlen($txt);
    $spasi = '';
    for($n=0;$n<$hsl;$n++){
        $spasi .= ' ';
    }
    return $spasi;
}