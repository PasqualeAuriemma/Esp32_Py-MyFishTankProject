<?php
session_start();

if(isset($_POST['forgotPassword'])){
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $email = $con->real_escape_string($_POST["emailForgot"]);

    // Validazione
    if(empty($email)) {
        exit(json_encode(['status' => 'error', 'message' => 'Email required']));
    }

    // Controlla se l'email esiste
    $check = $con->query("SELECT id FROM `users` WHERE email='$email'");
    if($check->num_rows === 0) {
        // Per sicurezza, rispondi lo stesso anche se l'email non esiste
        exit(json_encode(['status' => 'success', 'message' => 'If email exists, reset link will be sent']));
    }

    // Genera token unico
    $reset_token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $reset_token);
    $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 ora di validità

    // Salva token nel database
    $update = "UPDATE `users` SET reset_token='$token_hash', reset_token_expiry='$expiry' WHERE email='$email'";

    if($con->query($update) === TRUE) {
        // TODO: Invia email con link di reset
        // Per ora, salviamo il token e ritorniamo il link per testing
        $reset_link = "reset_password.php?token=" . $reset_token;

        // In produzione, invia via email:
        // mail($email, "Password Reset", "Click here to reset: " . $reset_link);

        exit(json_encode(['status' => 'success', 'message' => 'Password reset link sent to email', 'link' => $reset_link]));
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Failed to process request']));
    }
}
?>
