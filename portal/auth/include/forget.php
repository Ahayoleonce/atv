<?php
/**
 * AGASOBANUYE TV - Password Reset Recovery Subsystem (Modular PHP Component)
 * Path: include/forget.php
 */

declare(strict_types=1);

// Ensure session is already active or start one securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer files relative to project root
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

// Initialize core controller state variables
$feedbackMessage = "";
$currentStep = isset($_SESSION['reset_target_email']) && $_SESSION['reset_target_email'] !== '' ? "VERIFY_OTP" : "REQUEST_EMAIL";

// Path to your login-info.json file
$jsonFile = __DIR__ . '/../../assets/json/login-info.json';

// Helper function to read json data safely
if (!function_exists('getLoginData')) {
    function getLoginData(string $file): array {
        if (!file_exists($file)) {
            return ['users' => []];
        }
        $data = file_get_contents($file);
        return json_decode($data, true) ?? ['users' => []];
    }
}

// Helper function to save json data back safely
if (!function_exists('saveLoginData')) {
    function saveLoginData(string $file, array $data): bool {
        return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT)) !== false;
    }
}

// Handle Request Stage 1: Validate Email, Generate Alphanumeric OTP, & Send Branded Email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_email') {
    $email = trim($_POST['email'] ?? '');

    if ($email !== '') {
        $jsonData = getLoginData($jsonFile);
        $foundUser = null;

        foreach ($jsonData['users'] as $user) {
            if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
                $foundUser = $user;
                break;
            }
        }

        if ($foundUser) {
            $pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $otpCode = '';
            for ($i = 0; $i < 6; $i++) {
                $otpCode .= $pool[random_int(0, strlen($pool) - 1)];
            }

            $_SESSION['reset_target_email'] = $email;
            $_SESSION['reset_secure_otp'] = $otpCode;
            $_SESSION['reset_otp_expiry'] = time() + (15 * 60);

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'adolphehirwa4@gmail.com';
                $mail->Password   = 'ggixnxypcsbxvyti';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('adolphehirwa4@gmail.com', 'AGASOBANUYE TV');
                $mail->addAddress($email, $foundUser['username'] ?? 'Valued User');

                $logoPath = __DIR__ . '/../../assets/agasobanuye.svg';
                if (file_exists($logoPath)) {
                    $mail->addEmbeddedImage($logoPath, 'agasobanuye_logo');
                }

                $mail->isHTML(true);
                $mail->Subject = 'AGASOBANUYE TV - Verification Token';
                
                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 480px; border: 1px solid rgba(255,255,255,0.16); background: #000; padding: 24px; color: #fff; margin: 0 auto;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <h2 style='font-family: sans-serif; letter-spacing: 4px; color: #fff;'>AGASOBANUYE TV</h2>
                        </div>
                        <h2 style='font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #fff; padding-bottom: 8px; text-align: center; letter-spacing: 0.5px;'>Security Verification Protocol</h2>
                        <p style='font-size: 12px; color: rgba(255,255,255,0.7); line-height: 1.5; margin-top: 16px;'>Hello " . htmlspecialchars($foundUser['username'] ?? 'User') . ",</p>
                        <p style='font-size: 12px; color: rgba(255,255,255,0.7); line-height: 1.5;'>A password recovery step was requested for your account profile. Use the verification token below to finalize your update:</p>
                        
                        <div style='background: #111; border: 1px dashed rgba(255,255,255,0.3); padding: 14px; text-align: center; font-family: monospace; font-size: 22px; font-weight: bold; letter-spacing: 6px; margin: 20px 0; color: #fff;'>
                            {$otpCode}
                        </div>
                        
                        <p style='font-size: 10px; color: rgba(255,255,255,0.4); line-height: 1.4; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 12px; margin-top: 20px;'>
                            This confirmation sequence expires in 15 minutes. Disregard safely if you did not request this change.
                        </p>
                    </div>
                ";

                $mail->send();
                $feedbackMessage = "success:A 6-character authentication token sequence was successfully compiled and transmitted.";
                $currentStep = "VERIFY_OTP";

            } catch (Exception $e) {
                $feedbackMessage = "error:Mailer pipeline initialization failure. Log: " . $mail->ErrorInfo;
            }
        } else {
            $feedbackMessage = "error:The referenced user email address does not match any current records.";
        }
    } else {
        $feedbackMessage = "error:A valid email address parameter is required.";
    }
}

// Handle Request Stage 2: Validate OTP Code Only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp_code') {
    $inputOtp = strtoupper(trim($_POST['otp'] ?? ''));
    $cachedEmail = $_SESSION['reset_target_email'] ?? '';
    $cachedOtp = $_SESSION['reset_secure_otp'] ?? '';
    $cachedExpiry = $_SESSION['reset_otp_expiry'] ?? 0;

    $currentStep = "VERIFY_OTP";

    if ($inputOtp !== '') {
        if (time() > $cachedExpiry) {
            $feedbackMessage = "error:The validation token timeline has expired. Please restart the routine.";
            $currentStep = "REQUEST_EMAIL";
            unset($_SESSION['reset_target_email'], $_SESSION['reset_secure_otp'], $_SESSION['reset_otp_expiry']);
        } elseif ($inputOtp !== $cachedOtp || $cachedEmail === '') {
            $feedbackMessage = "error:The security validation parameters failed mathematical alignment verifications.";
        } else {
            $_SESSION['otp_verified_flag'] = true;
            $feedbackMessage = "success:Token verified successfully. Please enter your new password.";
            $currentStep = "RESET_PASSWORD";
        }
    } else {
        $feedbackMessage = "error:All character verification components must be processed.";
    }
}

// Handle Request Stage 3: Save New Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_new_password') {
    $newPassword = $_POST['new_password'] ?? '';
    $cachedEmail = $_SESSION['reset_target_email'] ?? '';
    $isOtpVerified = $_SESSION['otp_verified_flag'] ?? false;

    if (!$isOtpVerified || $cachedEmail === '') {
        $currentStep = "VERIFY_OTP";
        $feedbackMessage = "error:Session state invalid. Please re-verify your token.";
    } elseif ($newPassword !== '') {
        $jsonData = getLoginData($jsonFile);
        $updated = false;

        $salt = bin2hex(random_bytes(16));
        $passwordHash = hash('sha256', $salt . $newPassword);

        foreach ($jsonData['users'] as &$user) {
            if (isset($user['email']) && strtolower($user['email']) === strtolower($cachedEmail)) {
                $user['salt'] = $salt;
                $user['passwordHash'] = $passwordHash;
                $user['password'] = $newPassword; 
                $updated = true;
                break;
            }
        }
        unset($user);

        if ($updated && saveLoginData($jsonFile, $jsonData)) {
            unset($_SESSION['reset_target_email'], $_SESSION['reset_secure_otp'], $_SESSION['reset_otp_expiry'], $_SESSION['otp_verified_flag']);
            $currentStep = "RESET_SUCCESS";
        } else {
            $currentStep = "RESET_PASSWORD";
            $feedbackMessage = "error:Failed to update user credentials inside the JSON repository file.";
        }
    } else {
        $currentStep = "RESET_PASSWORD";
        $feedbackMessage = "error:A new password parameter is required.";
    }
}