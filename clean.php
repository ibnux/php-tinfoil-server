<?php

header("Content-Type: application/json");
$files = scandir("./cache/");
foreach($files as $file){
    if(pathinfo($file, PATHINFO_EXTENSION)=='json'){
        unlink("./cache/$file");
    }
}
echo json_encode(['deleted'=>$files]);
exit;