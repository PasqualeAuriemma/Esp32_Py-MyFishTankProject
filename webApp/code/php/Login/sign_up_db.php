<?php
session_start();

if(isset($_POST['signup'])){
    include("../connection.php");
    include("../queryAndFunction.php");

    if ($con->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Connection failed']));
    }

    $firstName = $con->real_escape_string($_POST["firstNameSignUp"]);
    $email = $con->real_escape_string($_POST["emailSignUp"]);
    $password = $_POST["passwordSignUp"];
    $password_confirm = $_POST["passwordConfirmSignUp"];

    // Validazione
    if(empty($firstName) || empty($email) || empty($password) || empty($password_confirm)) {
        exit(json_encode(['status' => 'error', 'message' => 'All fields required']));
    }

    if($password !== $password_confirm) {
        exit(json_encode(['status' => 'error', 'message' => 'Passwords do not match']));
    }

    if(strlen($password) < 6) {
        exit(json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']));
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        exit(json_encode(['status' => 'error', 'message' => 'Invalid email format']));
    }

    // Controlla se l'email esiste già
    $check = $con->query("SELECT id FROM `users` WHERE email='$email'");
    if($check->num_rows > 0) {
        exit(json_encode(['status' => 'error', 'message' => 'Email already registered']));
    }

    // Hash password
    $password_hashed = md5($password);

    // Inserisci nuovo utente
    $query = "INSERT INTO `users` (firstName, email, password) VALUES ('$firstName', '$email', '$password_hashed')";

    if($con->query($query) === TRUE) {
        $_SESSION["email"] = $email;
        $_SESSION["loggedIn"] = '1';

        $params = session_get_cookie_params();
        setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["domain"], $params["secure"], $params["httponly"]);

        exit(json_encode(['status' => 'success', 'message' => 'Registration successful']));
    } else {
        exit(json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $con->error]));
    }
}
?>
