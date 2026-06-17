<?php
session_start();

if (isset($_POST['forgotPassword'])) {
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $email = trim($_POST["emailForgot"] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['status' => 'error', 'message' => 'Valid email required']));
    }

    // Risposta generica in ogni caso (non rivela se l'email esiste)
    $generic = json_encode(['status' => 'success', 'message' => 'If the email exists, a reset link has been sent']);

    // Prepared statement ───────────────────────────────────────────
    $check = $con->prepare("SELECT id FROM `users` WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $check->close();
        exit($generic); // email non trovata → risposta identica per sicurezza
    }
    $check->close();

    // Genera token sicuro
    $reset_token = bin2hex(random_bytes(32));
    $token_hash  = hash('sha256', $reset_token);
    $expiry      = date('Y-m-d H:i:s', time() + 3600);

    // ── Prepared statement per UPDATE ────────────────────────────────
    $update = $con->prepare(
        "UPDATE `users` SET reset_token = ?, reset_token_expiry = ? WHERE email = ?"
    );
    $update->bind_param("sss", $token_hash, $expiry, $email);

    if (!$update->execute()) {
        $update->close();
        exit(json_encode(['status' => 'error', 'message' => 'Failed to process request']));
    }
    $update->close();

    // ── Invio via PHP mail() ────────────────────────────────────────
    $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/reset_password.php?token=" . $reset_token;

    $subject = "MyFishTank – Password Reset";
    $body    = "Hi,\n\n"
             . "You requested a password reset for your MyFishTank account.\n\n"
             . "Click the link below to set a new password (valid for 1 hour):\n"
             . $reset_link . "\n\n"
             . "If you did not request this, you can safely ignore this email.\n\n"
             . "— MyFishTank";

    $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n"
             . "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n"
             . "X-Mailer: PHP/" . phpversion();

    mail($email, $subject, $body, $headers);

    // ── Punto 4: il link NON viene mai restituito nella risposta JSON ─────────
    exit($generic);
}
