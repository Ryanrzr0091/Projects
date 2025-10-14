<?php
session_start();
session_unset();
session_destroy();
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);

    if (!empty($username)) {
    
        $_SESSION['username'] = $username;
        header('Location: flights.php');
        exit();
    } else {
        $error = "Please enter a username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Jet Trip Tracker</title>
    <link rel="stylesheet" href="style.css">
    <style>

        .form-container {
            background-color: rgba(0, 0, 0, 0.65);
            max-width: 400px;
            margin: 4em auto;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 15px #000;
            text-align: center;
        }

        .form-container h2 {
            margin-top: 0;
            font-size: 2em;
        }

        .form-container input[type="text"] {
            width: 90%;
            padding: 0.7em;
            margin: 1em 0;
            border: none;
            border-radius: 5px;
            font-size: 1em;
        }

        .form-container input[type="submit"] {
            background-color: #00cccc;
            color: #000;
            border: none;
            padding: 0.7em 2em;
            font-size: 1em;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #ffcc00;
        }

        .error {
            color: #ff7777;
            font-weight: bold;
        }
    </style>
</head>
<body class="signup">
    <header>
        <h1>Jet Trip Tracker</h1>
        <p>Create your flight tracking account</p>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>

    <main>
        <div class="form-container">
            <h2>Sign Up</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="post" action="signup.php">
                <input type="text" name="username" placeholder="Choose a username" required>
                <br>
                <input type="submit" value="Sign Up">
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Jet Trip Tracker</p>
    </footer>
</body>
</html>