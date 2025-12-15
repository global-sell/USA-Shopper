<?php
session_start();

// Your Cloudflare Turnstile secret key
$secret = '0x4AAAAAAB-rRYNML43W3doxT-aPlMjwfC8';

// Homepage after verification
$home_url = 'https://www.usashopper.site/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['cf-turnstile-response'] ?? '';

    if (empty($token)) {
        die('Please complete the verification.');
    }

    // Verify with Cloudflare
    $verify_url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    $data = http_build_query([
        'secret' => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ]);

    $opts = ['http' => [
        'method'  => 'POST',
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'content' => $data,
        'timeout' => 10
    ]];

    $context  = stream_context_create($opts);
    $result = file_get_contents($verify_url, false, $context);
    $resp = json_decode($result, true);

    if (!empty($resp['success']) && $resp['success'] === true) {
        // Verified, set session
        $_SESSION['verified'] = true;
        // Redirect to homepage
        header('Location: ' . $home_url);
        exit();
    } else {
        die('Verification failed. Please try again.');
    }
}
