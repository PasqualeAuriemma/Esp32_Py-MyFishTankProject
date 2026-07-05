<?php
// Endpoint IoT protetto da token segreto condiviso.

include("php/connection.php");
include("php/queryAndFunction.php");

// ── Lettura body JSON ─────────────────────────────────────────────────────────
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
    $ph       = isset($json_body["PH"])   ? $json_body["PH"]   : null;
    $dataSend = isset($json_body["Date"]) ? substr((string)$json_body["Date"], 0, 10) : null;

    if ($ph !== null && $dataSend !== null) {
        $ph = round(floatval($ph), 2);

        $stmt = $con->prepare("INSERT INTO ph_tab (ph, data_send) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ds", $ph, $dataSend);
            if ($stmt->execute()) {
                echo "Ok " . htmlspecialchars($ph) . " " . htmlspecialchars($dataSend);
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