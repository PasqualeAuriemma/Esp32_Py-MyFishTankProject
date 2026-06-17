<?php
session_start();

// ── Distruzione completa della sessione ───────────────────────────────────────
$_SESSION = [];

// Cancella il cookie di sessione dal browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        [
            'expires'  => time() - 42000,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => true,
            'samesite' => 'Strict',
        ]
    );
}

session_destroy();

// ── Costruzione URL adattiva al protocollo reale (HTTP in dev, HTTPS in prod) ──
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/index.php";
 
header("Location: " . $url);
exit('success');
