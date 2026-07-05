<?php
// Endpoint IoT protetto da token segreto condiviso.
// Definisci IOT_SECRET in connection.php (o in un file di config separato)
// con lo stesso valore configurato sul device Arduino/ESP.

include("php/connection.php");
include("php/queryAndFunction.php");

// ── Lettura body JSON ─────────────────────────────────────────────────────────
// Il firmware ESP32 (wifiConnection.py) invia Content-Type: application/json
// con json.dumps({key: value, "Date": timestamp}). $_POST resta vuoto con
// JSON puro, quindi leggiamo da php://input e decodifichiamo.
$raw_body = file_get_contents('php://input');
$json_body = json_decode($raw_body, true);
if (!is_array($json_body)) {
    $json_body = [];
}

// ── Verifica token ────────────────────────────────────────────────────────────
// Priorità: header X-IoT-Token (standard per device IoT), fallback su JSON body.
$iot_secret = getenv('IOT_SECRET') ?: 'esp32-secret-token-changeme';
$token = $_SERVER['HTTP_X_IOT_TOKEN'] ?? ($json_body['token'] ?? '');
if (empty($token) || !hash_equals($iot_secret, $token)) {
    http_response_code(401);
    exit("Unauthorized");
}

if (!empty($json_body)) {
    $temperature = isset($json_body["Temp"]) ? $json_body["Temp"] : null;
    $dataSend    = isset($json_body["Date"]) ? substr((string)$json_body["Date"], 0, 10) : null;

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
} else {
    echo "Error: Empty or invalid JSON body";
}
$con->close();