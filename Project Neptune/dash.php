<?php // dash.php - the main dashboard ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Project Neptune - CSG Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body">
  <main class="console">
    <header class="console-header">
      <div id="user-greet">Welcome, <span id="user-name">Guest</span></div>
    </header>

    <div id="csg-status" class="csg-indicator" data-status="GREEN">
      <span id="csg-label">CSG STATUS: LOADING...</span>
    </div>

    <section class="display" id="readout" aria-live="polite">
      Loading...
    </section>
  </main>

  <script src="dynamic.js"></script>
  <script>
    // Populate greeting from localStorage; non-blocking
    (function(){
      const n = localStorage.getItem('neptune_user') || 'Guest';
      document.getElementById('user-name').textContent = n;
    })();
  </script>
</body>
</html>