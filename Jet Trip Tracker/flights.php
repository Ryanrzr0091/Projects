
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
$dataFile = "data/flights_{$username}.txt";
$error = '';
$flights = [];

// Helper functions
function getCityCoordinates($city) {
    $file = 'data/city_coords.txt';
    if (!file_exists($file)) return null;

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        [$name, $lat, $lng] = explode('|', $line);
        if (strcasecmp(trim($name), trim($city)) == 0) {
            return [(float)$lat, (float)$lng];
        }
    }
    return null;
}

function haversineDistance($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 3958.8; // in miles
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
    $c = 2 * asin(sqrt($a));
    return round($earthRadius * $c, 1);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from = trim($_POST['from']);
    $to = trim($_POST['to']);

    if ($from !== '' && $to !== '') {
        $entry = "$from -> $to";
        file_put_contents($dataFile, $entry . PHP_EOL, FILE_APPEND);
    } else {
        $error = "Please enter both departure and destination cities.";
    }
}

if (isset($_POST['remove_index'])) {
    $index = (int)$_POST['remove_index'];
    if (isset($flights[$index])) {
        unset($flights[$index]);
        file_put_contents($dataFile, implode(PHP_EOL, $flights) . PHP_EOL);
        $flights = array_values($flights); // reindex
    }
}

// Load flight history
if (file_exists($dataFile)) {
    $flights = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Flights - Jet Trip Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container, .log-container {
            background-color: rgba(0, 0, 0, 0.65);
            max-width: 600px;
            margin: 2em auto;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 0 10px #000;
        }

        input[type="text"] {
            width: 45%;
            padding: 0.6em;
            margin: 0.5em 2%;
            border-radius: 5px;
            border: none;
            font-size: 1em;
        }

        input[type="submit"] {
            padding: 0.6em 2em;
            background-color: #00cccc;
            color: #000;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background-color: #ffcc00;
        }

        ul.flight-log {
            list-style: none;
            padding: 0;
            margin-top: 1em;
        }

        ul.flight-log li {
            padding: 0.4em 0;
            border-bottom: 1px solid #666;
            color: #ddd;
        }

        .error {
            color: #ff8888;
            text-align: center;
        }
    </style>
</head>
<body class="flights">
    <header>
        <h1>Jet Trip Tracker</h1>
        <p>Hello, <?= htmlspecialchars($username) ?>! Log your flight below.</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="globe.php">View Globe</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <main>
        <div class="form-container">
            <h2>Log a New Flight</h2>
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="post" action="flights.php">
                <input type="text" name="from" placeholder="Departure City" required>
                <input type="text" name="to" placeholder="Destination City" required>
                <br><br>
                <input type="submit" value="Add Flight">
            </form>
        </div>

        <div class="log-container">
            <h2>Your Flight Log</h2>
            <?php if (empty($flights)): ?>
                <p>No flights logged yet.</p>
            <?php else: ?>
                <ul class="flight-log">
                    <?php
                    $totalMiles = 0;
                    foreach ($flights as $index => $flight):
                        if (strpos($flight, '->') !== false) {
                            [$from, $to] = array_map('trim', explode('->', $flight));
                            $fromCoord = getCityCoordinates($from);
                            $toCoord = getCityCoordinates($to);
                            if ($fromCoord && $toCoord) {
                                $miles = haversineDistance($fromCoord[0], $fromCoord[1], $toCoord[0], $toCoord[1]);
                                $totalMiles += $miles;
                                echo "<li>
                                        $from → $to <span style='color:#00ffff'>($miles miles)</span>
                                        <form method='post' style='display:inline; margin-left:10px;'>
                                            <input type='hidden' name='remove_index' value='$index'>
                                            <input type='submit' value='Remove' style='padding:2px 6px; font-size:0.9em; background-color:#cc0000; color:white; border:none; border-radius:4px; cursor:pointer;'>
                                        </form>
                                    </li>";
                            } else {
                                echo "<li>$flight <span style='color:red'>(City not found)</span></li>";
                            }
                        } else {
                            echo "<li>$flight</li>";
                        }
                    endforeach;
                    ?>
                </ul>
                <p style="text-align:center; color:#00ffcc;"><strong>Total Miles Flown:</strong> <?= $totalMiles ?> mi</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Jet Trip Tracker</p>
    </footer>
</body>
</html>