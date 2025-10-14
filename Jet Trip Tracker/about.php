<?php
$cityFile = "data/city_coords.txt";

$cities = [];
if (file_exists($cityFile)) {
    $lines = file($cityFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', $line);
        if (count($parts) === 3) {
            $cities[] = trim($parts[0]);
        }
    }
    sort($cities);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About - Jet Trip Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>

        .container {
            max-width: 800px;
            margin: 2em auto;
            background-color: rgba(0,0,0,0.7);
            padding: 2em;
            border-radius: 10px;
        }

        h2 {
            border-bottom: 1px solid #aaa;
            padding-bottom: 0.3em;
        }

        ul.city-list {
            columns: 2;
            list-style: none;
            padding: 0;
        }

        ul.city-list li {
            padding: 0.3em 0;
        }
    </style>
</head>
<body class="about">
    <header>
        <h1>Jet Trip Tracker</h1>
        <p>Discover Your Path in the Skies</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <main>
        <div class="container">
            <h2>About Jet Trip Tracker</h2>
            <p>
                <strong>Jet Trip Tracker (JTT)</strong> is a web-based flight logging and 3D visualization tool built for 
                aviation enthusiasts and world travelers. Users can record real or fictional flights between cities and 
                instantly see their routes displayed on an interactive rotating globe.
            </p>
            <p>
                The site is powered by PHP and stores user data in simple text-based logs, while all flight paths are rendered 
                using <a href="https://github.com/vasturiano/globe.gl" target="_blank">Globe.gl</a>, a Three.js-powered 
                visualization library. Sample flight logs are available under the usernames 
                <em>Anna</em>, <em>Donna</em>, <em>Greg</em>, <em>Jack</em>, and <em>Jill</em>.
            </p>
            <p>
                All background images are sourced from 
                <a href="https://unsplash.com" target="_blank">Unsplash</a> and are free to use.
            </p>
            <p>
                Just enter a username to get started, log your flights and watch your experiences across
                the globe come to life!
            </p>

            <h2>Available Cities</h2>
            <p>Here is a list of the current cities available:</p>
            <ul class="city-list">
                <?php foreach ($cities as $city): ?>
                    <li><?= htmlspecialchars($city) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Jet Trip Tracker</p>
    </footer>
</body>
</html>