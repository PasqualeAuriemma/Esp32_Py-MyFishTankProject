<?php
session_start();

if(isset($_POST['resetPassword'])){
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $token = $_POST["resetToken"];
    $password = $_POST["newPassword"];
    $password_confirm = $_POST["newPasswordConfirm"];

    // Validazione
    if(empty($token) || empty($password) || empty($password_confirm)) {
        exit(json_encode(['status' => 'error', 'message' => 'All fields required']));
    }

    if($password !== $password_confirm) {
        exit(json_encode(['status' => 'error', 'message' => 'Passwords do not match']));
    }

    if(strlen($password) < 6) {
        exit(json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']));
    }

    // Verifica token
    $token_hash = hash('sha256', $token);
    $now = date('Y-m-d H:i:s');

    $check = $con->query("SELECT id, email FROM `users` WHERE reset_token='$token_hash' AND reset_token_expiry > '$now'");

    if($check->num_rows === 0) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token']));
    }

    $row = $check->fetch_assoc();
    $user_id = $row['id'];

    // Hash nuova password
    $password_hashed = md5($password);

    // Aggiorna password e cancella token
    $update = "UPDATE `users` SET password='$password_hashed', reset_token=NULL, reset_token_expiry=NULL WHERE id='$user_id'";

    if($con->query($update) === TRUE) {
        exit(json_encode(['status' => 'success', 'message' => 'Password reset successful. Please login with your new password']));
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Failed to reset password']));
    }
}
?>
