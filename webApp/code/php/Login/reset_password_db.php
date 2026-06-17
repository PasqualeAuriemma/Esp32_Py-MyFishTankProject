<?php
session_start();

if (isset($_POST['resetPassword'])) {
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $token           = $_POST["resetToken"]        ?? '';
    $password        = $_POST["newPassword"]        ?? '';
    $password_confirm = $_POST["newPasswordConfirm"] ?? '';

    // ── Validazione ────────────────────────────────────────────────────────────
    if (empty($token) || empty($password) || empty($password_confirm)) {
        exit(json_encode(['status' => 'error', 'message' => 'All fields required']));
    }

    if ($password !== $password_confirm) {
        exit(json_encode(['status' => 'error', 'message' => 'Passwords do not match']));
    }

    if (strlen($password) < 8) {
        exit(json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']));
    }

    // ── Punto 2: prepared statement per verifica token ────────────────────────
    $token_hash = hash('sha256', $token);
    $now        = date('Y-m-d H:i:s');

    $check = $con->prepare(
        "SELECT id FROM `users` WHERE reset_token = ? AND reset_token_expiry > ?"
    );
    $check->bind_param("ss", $token_hash, $now);
    $check->execute();
    $check->bind_result($user_id);
    $found = $check->fetch();
    $check->close();

    if (!$found) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token']));
    }

    // ── Punto 1: bcrypt ───────────────────────────────────────────────────────
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);

    // ── Punto 2: prepared statement per UPDATE ────────────────────────────────
    $update = $con->prepare(
        "UPDATE `users` SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?"
    );
    $update->bind_param("si", $password_hashed, $user_id);

    if ($update->execute()) {
        $update->close();
        exit(json_encode(['status' => 'success', 'message' => 'Password reset successful. Please login with your new password']));
    } else {
        $update->close();
        exit(json_encode(['status' => 'error', 'message' => 'Failed to reset password']));
    }
}
