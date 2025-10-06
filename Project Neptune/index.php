<?php // index.php - landing page and login ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Project Neptune - Carrier Strike Group Readiness</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="landing">
  <div class="landing-overlay">
    <header class="landing-header">
      <h1>Project Neptune</h1>
      <h2>CSG Readiness Console</h2>
      <p>More often than not, history has taught us that logistics can kill a military 
        faster than the enemy. Alexander the Great in Persia, Napoleon in Russia, the 
        Wehrmacht on the Eastern Front, The Soviets in Afghanistan – all fell victim to a 
        lack of supply, rather than superior firepower or tactics. Nowhere is this lesson more 
        critical than in the open ocean, thousands of miles from land. Project Neptune is a web-based 
        system designed to simulate and track the logistics and combat readiness of a single 
        Carrier Strike Group (CSG). The application uses a PHP API layer returning JSON to dynamic 
        JavaScript, via Ajax, and a custom CSS theme for a clean, presentable dashboard.</p>
    </header>

    <main class="landing-main">
      <div id="gate">
        <div id="gate-welcome" style="display:none;">
          <p>Welcome back, <b id="gate-name"></b>.</p>
          <div class="gate-actions">
            <a class="btn-primary" href="dash.php">Open Dashboard</a>
            <button class="btn-secondary" id="changeUser">Change User</button>
          </div>
        </div>

        <form id="gate-form" autocomplete="off" style="display:none;">
          <label for="username">Enter a callsign:</label>
          <input id="username" name="username" placeholder="e.g., OpsChief" required>
          <button class="btn-primary" type="submit">Proceed</button>
        </form>
      </div>
    </main>
  </div>

  <script>
    const KEY = 'neptune_user';
    const name = localStorage.getItem(KEY) || '';
    const form = document.getElementById('gate-form');
    const welcome = document.getElementById('gate-welcome');
    const gateName = document.getElementById('gate-name');
    const changeBtn = document.getElementById('changeUser');

    function showForm() { form.style.display = ''; welcome.style.display = 'none'; }
    function showWelcome(n) { gateName.textContent = n; form.style.display = 'none'; welcome.style.display = ''; }

    if (name) showWelcome(name); else showForm();

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const n = document.getElementById('username').value.trim();
      if (!n) return;
      localStorage.setItem(KEY, n);
      window.location.href = 'dash.php';
    });
    changeBtn?.addEventListener('click', () => {
      localStorage.removeItem(KEY);
      showForm();
      document.getElementById('username').focus();
    });
  </script>
</body>
</html>