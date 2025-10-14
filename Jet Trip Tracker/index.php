<?php
session_start();
session_unset();
session_destroy();
session_start();
$loggedIn = isset($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jet Trip Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="home">
    <header>
        <h1>Jet Trip Tracker</h1>
        <?php if ($loggedIn): ?>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
        <?php else: ?>
            <p>Track your flights across the globe in 3D!</p>
        <?php endif; ?>
    </header>

    <nav>
        <ul>
            <?php if (!$loggedIn): ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="signup.php">Sign Up</a></li>
            <?php else: ?>
                <li><a href="flights.php">Log/View Flights</a></li>
                <li><a href="globe.php">View Globe</a></li>
            <?php endif; ?>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <main>
        <section>
            <h2>Why Jet Trip Tracker?</h2>
            <p>Whether you're simulating cross-country hops or logging real-world trips, Jet Trip Tracker lets you visualize every flight with dynamic arcs across the Earth.</p>
        </section>
    </main>

    <footer>
        <p>Ryan Rogers | Auburn University</p>
    </footer>
</body>
</html>