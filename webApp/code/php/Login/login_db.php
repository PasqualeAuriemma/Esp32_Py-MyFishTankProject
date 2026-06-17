<?php
session_start();

if (isset($_POST['login'])) {
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die('failed');
    }

    $email    = trim($_POST["emailPHP"]    ?? '');
    $password = $_POST["passwordPHP"] ?? '';

    if (empty($email) || empty($password)) {
        exit('failed');
    }

    // ── Punto 2: prepared statement ───────────────────────────────────────────
    // Recupera l'hash bcrypt per email; il confronto avviene con password_verify
    $stmt = $con->prepare("SELECT id, password FROM `users` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($userId, $passwordHash);
    $found = $stmt->fetch();
    $stmt->close();
    $con->close();

    // ── Punto 1: password_verify invece di MD5 ────────────────────────────────
    if ($found && password_verify($password, $passwordHash)) {
        session_regenerate_id(true); // previene session fixation

        $_SESSION["email"]    = $email;
        $_SESSION["loggedIn"] = '1';

        // ── Punto 5: SameSite=Strict ──────────────────────────────────────────
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            session_id(),
            [
                'expires'  => time() + 60 * 60 * 24 * 30,
                'path'     => $params['path'],
                'domain'   => $params['domain'],
                'secure'   => $params['secure'],
                'httponly' => true,
                'samesite' => 'Strict',
            ]
        );

        exit('success');
    } else {
        exit('failed');
    }
}
