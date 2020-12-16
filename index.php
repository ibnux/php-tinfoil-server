<?php
header("Content-Type: application/json");
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
