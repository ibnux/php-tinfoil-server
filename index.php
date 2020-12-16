<?php
require_auth();

require 'vendor/autoload.php';
require 'config.php';

use Medoo\Medoo;

$db = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => $dbname,
	'server' => $dbhost,
	'username' => $dbuser,
    'password' => $dbpass
]);

header("Content-Type: application/json");

$_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/";
//Parsing URL
$_path = array_values(array_filter(explode("/", parse_url($_SERVER['REQUEST_URI'])['path'])));

$json = [
    'locations' => [
        "https://".$_SERVER['SERVER_NAME']."/nsz/",
        "https://".$_SERVER['SERVER_NAME']."/dlc",
        "https://".$_SERVER['SERVER_NAME']."/updates",
        "https://".$_SERVER['SERVER_NAME']."/demos",
        "https://".$_SERVER['SERVER_NAME']."/xci"
    ],
    'success' => 'Pakai Seperlunya, jangan di share kecuali ke member grup aja, biar ngga ditegur tendo. punya google drive mau dishare juga? mention @ibnux | Donasi Biaya Server trakteer.id/ibnux karyakarsa.com/ibnux'
];

echo json_encode($json);
