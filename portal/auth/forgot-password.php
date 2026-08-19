<?php
/**
 * AGASOBANUYE TV - Password Reset Recovery Subsystem (Main Template)
 * Path: forgot-password.php
 */

declare(strict_types=1);

// Include the modular backend logic and controller
require_once __DIR__ . '/include/forget.php';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AGASOBANUYE TV</title>
<link rel="shortcut icon" href="../../assets/agasobanuye.svg" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Advanced Modular Stylesheet Link -->
<link rel="stylesheet" href="include/forget.css">
</head>
<body>

<div class="shell">
  <!-- ================= LEFT: Form Panel ================= -->
  <div class="form-panel">
    <div class="ambient-glow"></div>
    <a href="../" class="wordmark">AGASOBANUYE TV</a>

    <div class="form-wrap">
      <h1>Recovery.</h1>
      <p class="lede">
        <?php if ($currentStep === "REQUEST_EMAIL"): ?>
          Enter your account email to receive a secure password reset token.
        <?php elseif ($currentStep === "VERIFY_OTP"): ?>
          Input the 6-character verification token sent to your email address.
        <?php elseif ($currentStep === "RESET_PASSWORD"): ?>
          Token verified successfully. Specify your new account password.
        <?php else: ?>
          Password successfully updated!
        <?php endif; ?>
      </p>

      <?php if (!empty($feedbackMessage) && $currentStep !== "RESET_SUCCESS"): 
          $toastParts = explode(':', $feedbackMessage, 2);
          $toastType = $toastParts[0] ?? 'error';
          $toastText = $toastParts[1] ?? $feedbackMessage;
      ?>
          <div class="alert <?= $toastType === 'success' ? 'success' : 'error' ?>">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 8v5m0 3.5h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span><?= htmlspecialchars($toastText) ?></span>
          </div>
      <?php endif; ?>

      <?php if ($currentStep === "REQUEST_EMAIL"): ?>
        <form method="POST" action="" id="request_email_form" class="auth-form">
          <input type="hidden" name="action" value="submit_email">
          
          <div class="field">
            <label for="email">Email Address</label>
            <div class="input-box">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="7.5" r="4" stroke="currentColor" stroke-width="1.8"/></svg>
              <input type="email" id="email" name="email"  autocomplete="email" required autofocus>
            </div>
          </div>

          <button type="submit" id="request_token_btn" class="btn-submit">
            <span class="btn-label">Request Reset Token</span>
          </button>
        </form>

      <?php elseif ($currentStep === "VERIFY_OTP"): ?>
        <form method="POST" action="" id="otp_verification_form" class="auth-form">
          <input type="hidden" name="action" value="verify_otp_code">
          <input type="hidden" name="otp" id="hidden_compiled_otp" value="">

          <div class="field">
            <label>Security Verification Token</label>
            <div class="otp-split-container" role="group" aria-label="Verification Token Box">
                <input type="text" maxlength="1" class="otp-single-box" data-index="0" autocomplete="off" inputmode="text">
                <input type="text" maxlength="1" class="otp-single-box" data-index="1" autocomplete="off" inputmode="text">
                <input type="text" maxlength="1" class="otp-single-box" data-index="2" autocomplete="off" inputmode="text">
                <input type="text" maxlength="1" class="otp-single-box" data-index="3" autocomplete="off" inputmode="text">
                <input type="text" maxlength="1" class="otp-single-box" data-index="4" autocomplete="off" inputmode="text">
                <input type="text" maxlength="1" class="otp-single-box" data-index="5" autocomplete="off" inputmode="text">
            </div>
          </div>

          <button type="submit" id="verify_otp_btn" class="btn-submit" disabled>
            <span class="btn-label">Verify Token</span>
          </button>
        </form>

        <!-- Resend OTP Trigger Form -->
        <form method="POST" action="" class="auth-form" style="margin-top: 12px;">
          <input type="hidden" name="action" value="submit_email">
          <input type="hidden" name="email" value="<?= htmlspecialchars($_SESSION['reset_target_email'] ?? '') ?>">
          <button type="submit" style="background: transparent; border: none; color: #fff; font-size: 13px; text-decoration: underline; cursor: pointer; padding: 0; width: 100%; text-align: center; opacity: 0.8;">
            Didn't receive the token? Resend OTP
          </button>
        </form>

      <?php elseif ($currentStep === "RESET_PASSWORD"): ?>
        <form method="POST" action="" class="auth-form">
          <input type="hidden" name="action" value="save_new_password">

          <div class="field">
            <label for="new_password">New Password</label>
            <div class="input-box">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="4.5" y="10.5" width="15" height="9.5" rx="1.5" stroke="currentColor" stroke-width="1.8"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
              <input type="password" id="new_password" name="new_password" placeholder="••••••••" autocomplete="new-password" required autofocus>
              <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                <i class="fa-regular fa-eye"></i>
              </button>
            </div>
            <div class="password-strength-meter">
              <div class="strength-bar" id="strengthBar"></div>
            </div>
          </div>

          <button type="submit" id="update_password_btn" class="btn-submit">
            <span class="btn-label">Update Password</span>
          </button>
        </form>

      <?php elseif ($currentStep === "RESET_SUCCESS"): ?>
        <div class="alert success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6 9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <span>Your password has been successfully reset. Redirecting to login...</span>
        </div>

        <script>
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 2000);
        </script>
      <?php endif; ?>

      <a href="index.php" class="back-home">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M19 12H5m0 0 6-6m-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        RETURN TO SIGN IN
      </a>
    </div>
  </div>

  <!-- ================= RIGHT: Typographic Panel ================= -->
  <div class="image-panel">
    <div class="image-overlay-gradient"></div>
    <img src="../assets/img/auth-cover.png" alt="" onerror="this.style.display='none'">
    <div class="grid-lines">
      <span style="left:25%"></span><span style="left:50%"></span><span style="left:75%"></span>
    </div>
    <div class="huge-mark">AGASOBANUYE — AGASOBANUYE — </div>
    <div class="image-caption">
      <h2>Drama, action &amp; horror — updated every week.</h2>
      <p>Secure account recovery ensures you never miss your favorite translated films.</p>
    </div>
  </div>
</div>

<!-- Advanced Modular Script Link -->
<script src="include/forget.js"></script>
</body>
</html>