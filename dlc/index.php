<!DOCTYPE html>
<html>

<head>
    <title>Index of Games</title>
    <meta charset="utf-8">
</head>

<body bgcolor="white">
    <h1>Index Games</h1>
    <hr>
    <pre><?php
        echo "\n";
        $games = file_get_contents("aktif.txt");
        $games = explode("\n", str_replace("\r", "", $games));
        foreach ($games as $game) {
            $gms = explode("#", $game);
            echo '<a href="' . trim($game) . '">' . trim($gms[1]) . '</a>' . "\n";
        }
    ?>
    </pre>
    <hr>
</body>

</html>