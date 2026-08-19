<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="../../assets/agasobanuye.svg" type="image/x-icon">
<title>AGASOBANUYE TV</title>
<link rel="shortcut icon" href="../assets/agasobanuye.svg" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="include/index.css">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="shell">
  <!-- ================= LEFT: credentials form ================= -->
  <div class="form-panel">
  
    <a href="../../" class="wordmark">AGASOBANUYE TV</a>

    <div class="form-wrap">

      <h1>Sign in.</h1>
      <p class="lede">Enter your username, email, or phone number with your password to reach the dashboard.</p>

      <div class="alert error" id="alertError">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 8v5m0 3.5h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <span id="alertErrorText">Something went wrong.</span>
      </div>

      <form id="loginForm" novalidate>
        <div class="field">
          <label for="loginId">Address</label>
          <div class="input-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="7.5" r="4" stroke="currentColor" stroke-width="1.8"/></svg>
            <input type="text" id="loginId" name="loginId" placeholder="" autocomplete="username" required>
          </div>
        </div>

        <div class="field">
          <label for="loginPassword">Password</label>
          <div class="input-box">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="4.5" y="10.5" width="15" height="9.5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
            <input type="password" id="loginPassword" name="loginPassword" placeholder="" autocomplete="current-password" required>
            <button type="button" class="toggle-visibility" id="togglePassword" aria-label="Show password">
              <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M1.5 12S5 5 12 5s10.5 7 10.5 7-3.5 7-10.5 7S1.5 12 1.5 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="3.2" stroke="currentColor" stroke-width="1.8"/></svg>
            </button>
          </div>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" id="rememberMe">
            <span class="box"></span>
            Remember me
          </label>
          <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn-submit" id="submitBtn">
          <span class="btn-fill"></span>
          <span class="spinner"></span>
          <span class="btn-label">Sign In</span>
        </button>
      </form>

      <a href="../../" class="back-home">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M19 12H5m0 0 6-6m-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        BACK TO AGASOBANUYE TV
      </a>

      <!-- <div class="demo-note">
        DEMO — admin: <b>admin</b> / <b>Admin@123</b> · viewer: <b>jdoe</b> / <b>User@123</b><br>
        Passwords are checked as a salted SHA-256 hash from login-info.json. No plaintext is stored.
      </div> -->
    </div>
  </div>

  <!-- ================= RIGHT: typographic panel ================= -->
  <div class="image-panel">
    <img src="../assets/img/auth-cover.png" alt="" onerror="this.style.display='none'">
    <div class="grid-lines">
      <span style="left:25%"></span><span style="left:50%"></span><span style="left:75%"></span>
    </div>
    <div class="huge-mark">AGASOBANUYE — AGASOBANUYE — </div>
    <div class="image-caption">
  
      <h2>Drama, action &amp; horror — updated every week.</h2>
      <p>Sign in to pick up where you left off and keep browsing the full library.</p>
    </div>
  </div>
</div>

<script src="../assets/js/auth.js"></script>
<script src="include/index.js"></script>
</body>
</html>