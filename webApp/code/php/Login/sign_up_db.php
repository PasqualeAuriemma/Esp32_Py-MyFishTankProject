<?php

session_start();

if (isset($_POST['signup'])) {
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $firstName       = trim($_POST["firstNameSignUp"]  ?? '');
    $email           = trim($_POST["emailSignUp"]       ?? '');
    $password        = $_POST["passwordSignUp"]         ?? '';
    $password_confirm = $_POST["passwordConfirmSignUp"] ?? '';
    $invite_code     = trim($_POST["inviteCode"]        ?? '');

    // ── Validazione ────────────────────────────────────────────────────────────
    if (empty($firstName) || empty($email) || empty($password) || empty($password_confirm)) {
        exit(json_encode(['status' => 'error', 'message' => 'All fields required']));
    }

    if ($password !== $password_confirm) {
        exit(json_encode(['status' => 'error', 'message' => 'Passwords do not match']));
    }

    if (strlen($password) < 8) {
        exit(json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']));
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid email format']));
    }

    // ── Punto 11: codice invito ────────────────────────────────────────────────
    // Definisci INVITE_CODE in connection.php oppure in un file di config separato
    if (!defined('INVITE_CODE') || $invite_code !== INVITE_CODE) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid invite code']));
    }

    // ── Controlla email duplicata (prepared statement) ─────────────────────────
    $check = $con->prepare("SELECT id FROM `users` WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $check->close();
        exit(json_encode(['status' => 'error', 'message' => 'Email already registered']));
    }
    $check->close();

    // ── Punto 1: bcrypt invece di MD5 ─────────────────────────────────────────
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // ── Punto 2: prepared statement per l'INSERT ──────────────────────────────
    $stmt = $con->prepare("INSERT INTO `users` (firstName, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $firstName, $email, $password_hashed);

    if ($stmt->execute()) {
        $stmt->close();

        $_SESSION["email"]    = $email;
        $_SESSION["loggedIn"] = '1';

        // ── Punto 5: SameSite=Strict sul cookie di sessione ───────────────────
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

        exit(json_encode(['status' => 'success', 'message' => 'Registration successful']));
    } else {
        $stmt->close();
        exit(json_encode(['status' => 'error', 'message' => 'Registration failed']));
    }
}