<?php
/**
 * Just run from Commandline php importGameData.php
 */
if(php_sapi_name() !== 'cli'){
    die("Run only from CLI");
}
include "../vendor/autoload.php";
include "../config.php";
$dbpath = '../'.$dbpath;
include "../function.php";

if($dbtype=='mysql'){
    echo "reCreate table? (y/n) : ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    if (trim($line) == 'y') {
        $db = getDatabase(true);
    }else{
        $db = getDatabase();
    }
}else{
    $db = getDatabase();
}
#nswdb

$xmls = (array)simplexml_load_string(file_get_contents('http://nswdb.com/xml.php'));

$releases = $xmls['release'];
echo count($releases) . " titles\n";
sleep(1);
foreach ($releases as $release) {
    $data = (array) $release;
    $titleid = preg_replace("/[^a-zA-Z0-9]+/", "", $data['titleid']) . "";
    $titleid = substr($titleid,0,16);
    $game = $db->get("t_games", ['titleid', 'size'], ['titleid' => $titleid]);
    if (empty($game)) {
        try {
            $db->insert("t_games", [
                'titleid' => preg_replace("/[^a-zA-Z0-9]+/", "", $data['titleid']) . "",
                'name' => $data['name'] . "",
                'publisher' => $data['publisher'] . "",
                'languages' => $data['languages'] . "",
                'size' => $data['trimmedsize'] . "",
                'description' => "",
                'image' => "",
                'rating' => "",
                'size' => 0
            ]);
            if($db->has("t_games", ['titleid' => $titleid])){
                echo "INSERTED ".$data['name'] . " " . $data['titleid'] . "\n";
            }else{
                print_r($db->error());
            }
        } catch (Exception $e) {
            print_r($data);
            echo "\n";
            echo $data['name'] . " ERROR\n";
        }
    } else {
        if (empty($game['size'])) {
            $db->update("t_games", ['size' => $data['trimmedsize'] * 1], ['titleid' => $titleid]);
            echo "update ".$titleid . " " . $data['trimmedsize'] . "\n";
        }
    }
}
#tinfoil
$releases = json_decode(file_get_contents("https://raw.githubusercontent.com/blawar/titledb/master/US.en.json"), true);
echo count($releases) . " titles\n";
sleep(1);
foreach ($releases as $version => $data) {
    try {
        if (!empty($data['name'])) {
            if (strlen($data['id']) >= 16) {
                $titleid = substr(preg_replace("/[^a-zA-Z0-9]+/", "", $data['id']), 0, 16);
                if ($db->has('t_games', ['titleid' => $data['id']])) {
                    if (!empty($data['iconUrl'] && empty($db->get("t_games", 'image', ['titleid' => $titleid])))) {
                        echo  "image update " . $data['name'] . "\n";
                        $db->update(
                            "t_games",
                            [
                                'image' => $data['iconUrl'] . ""
                            ],
                            ['titleid' => $titleid]
                        );
                    }
                    if (!empty($data['rating'] && empty($db->get("t_games", 'rating', ['titleid' => $titleid])))) {
                        echo  "rating update " . $data['name'] . "\n";
                        $db->update(
                            "t_games",
                            [
                                'rating' => $data['rating'] . ""
                            ],
                            ['titleid' => $titleid]
                        );
                    }
                    if (!empty($data['description'] && empty($db->get("t_games", 'description', ['titleid' => $titleid])))) {
                        echo  "description update " . $data['name'] . "\n";
                        $db->update(
                            "t_games",
                            [
                                'description' => $data['description'] . ""
                            ],
                            ['titleid' => $titleid]
                        );
                    }
                    if (empty($db->get("t_games", 'size', ['titleid' => $titleid]))) {
                        $hasil = $db->update(
                            "t_games",
                            [
                                'size' => $data['size'] * 1
                            ],
                            ['titleid' => $titleid]
                        );
                        if ($hasil->rowCount() > 0) {
                            echo  $hasil->rowCount() . " size update " . $data['size'] . "\n";
                        } else {
                            print_r($db->error());
                        }
                    }
                } else {
                    echo  "insert " . $data['name'] . "\n";
                    $db->insert("t_games", [
                        'titleid' => preg_replace("/[^a-zA-Z0-9]+/", "", $data['id']) . "",
                        'name' => $data['name'] . "",
                        'publisher' => $data['publisher'] . "",
                        'languages' => implode(",", (array)$data['languages']),
                        'image' => $data['iconUrl'] . "",
                        'rating' => $data['rating'] . "",
                        'description' => $data['description'] . "",
                        'size' => $data['size'] * 1
                    ]);
                }
            }
        }
    } catch (Exception $e) {
        print_r($data);
        echo "\n";
        echo $data['name'] . " ERROR\n";
    }
}
