<?php
session_start();

function getCityCoordinates($city) {
    $file = 'data/city_coords.txt';
    if (!file_exists($file)) return null;

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$name, $lat, $lng] = explode('|', $line);
        if (strcasecmp(trim($name), trim($city)) === 0) {
            return [(float)$lat, (float)$lng];
        }
    }
    return null;
}

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$dataFile = "data/flights_{$username}.txt";
$flights = file_exists($dataFile) ? file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

$arcData = [];
foreach ($flights as $flight) {
    if (strpos($flight, '->') !== false) {
        [$from, $to] = array_map('trim', explode('->', $flight));
        $fromCoord = getCityCoordinates($from);
        $toCoord = getCityCoordinates($to);

        if ($fromCoord && $toCoord) {
            $arcData[] = [
                "source" => ["lat" => $fromCoord[0], "lng" => $fromCoord[1]],
                "target" => ["lat" => $toCoord[0], "lng" => $toCoord[1]]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Globe - Jet Trip Tracker</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100%;
            font-family: sans-serif;
            background-image: url('images/bkg5.jpg');
            background-size: cover;
            background-position: center;
        }

        #globeViz {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
        }

        .nav-buttons {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 2;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 10px 15px;
            border-radius: 10px;
        }

        .nav-buttons a {
            color: #00ffff;
            text-decoration: none;
            font-weight: bold;
            margin-right: 15px;
            transition: color 0.3s;
        }

        .nav-buttons a:hover {
            color: #ffcc00;
        }
    </style>
</head>
<body>
    <div class="nav-buttons">
        <a href="index.php">Home</a>
        <a href="flights.php">Flight Log</a>
    </div>

    <div id="globeViz"></div>

    <script src="https://unpkg.com/three"></script>
    <script src="https://unpkg.com/globe.gl"></script>
    <script>
        const arcs = <?= json_encode($arcData) ?>.flatMap(d => [
            { ...d, type: 'static' },
            { ...d, type: 'animated' }
        ]);

        const globe = Globe()
        (document.getElementById('globeViz'))
            .globeImageUrl('//unpkg.com/three-globe/example/img/earth-night.jpg')
            .backgroundColor('rgba(0,0,0,0)')
            .arcsData(arcs)
            .arcStartLat(d => d.source.lat)
            .arcStartLng(d => d.source.lng)
            .arcEndLat(d => d.target.lat)
            .arcEndLng(d => d.target.lng)
            .arcColor(d => d.type === 'animated' ? '#00ffff' : 'rgba(0,255,255,0.4)')
            .arcStroke(d => d.type === 'animated' ? 0.3 : 0.2)
            .arcAltitude(d => d.type === 'animated' ? 0.28 : 0.28)
            .arcDashLength(d => d.type === 'animated' ? 0.8 : 1)
            .arcDashGap(d => d.type === 'animated' ? 2 : 0)
            .arcDashInitialGap(d => d.type === 'animated' ? Math.random() * 5 : 0)
            .arcDashAnimateTime(d => d.type === 'animated' ? 2000 : 0);
    </script>
</body>
</html>