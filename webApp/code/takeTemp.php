<?php
// Endpoint IoT protetto da token segreto condiviso.
// Definisci IOT_SECRET in connection.php (o in un file di config separato)
// con lo stesso valore configurato sul device Arduino/ESP.

include("php/connection.php");
include("php/queryAndFunction.php");

// ── Verifica token ─────────────────────────────────────────────────────────────
$token = $_POST["token"] ?? $_SERVER['HTTP_X_IOT_TOKEN'] ?? '';
if (!defined('IOT_SECRET') || !hash_equals(IOT_SECRET, $token)) {
    http_response_code(401);
    exit("Unauthorized");
}

if (!empty($_POST)) {
    $temperature = isset($_POST["Temp"]) ? $_POST["Temp"] : null;
    $dataSend    = isset($_POST["Date"]) ? substr($_POST["Date"], 0, 10) : null;

    if ($temperature !== null && $dataSend !== null) {
        $stmt = $con->prepare("INSERT INTO temp_tab (temperature, data_send) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ds", $temperature, $dataSend);
            if ($stmt->execute()) {
                echo "Ok " . htmlspecialchars($temperature) . " " . htmlspecialchars($dataSend);
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $con->error;
        }
    } else {
        echo "Error: Missing parameters";
    }
}
$con->close();
