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

// ── Lettura parametri dal JSON ────────────────────────────────────────────────
if (!empty($json_body)) {
    $ec       = isset($json_body["Ec"])   ? $json_body["Ec"]   : null;
    $dateSend = isset($json_body["Date"]) ? substr((string)$json_body["Date"], 0, 10) : null;

    if ($ec !== null && $dateSend !== null) {
        $tds = floatval($ec) * 0.64;

        // Inserimento in ec_tab
        $stmtEC = $con->prepare("INSERT INTO ec_tab (ec, data_send) VALUES (?, ?)");
        if ($stmtEC) {
            $stmtEC->bind_param("ds", $ec, $dateSend);
            if ($stmtEC->execute()) {
                echo "Ok ec=" . htmlspecialchars($ec) . " date=" . htmlspecialchars($dateSend) . "\n";
            } else {
                echo "Error ec_tab: " . $stmtEC->error . "\n";
            }
            $stmtEC->close();
        } else {
            echo "Error prepare ec_tab: " . $con->error . "\n";
        }

        // Inserimento in tds_tab
        $stmtTDS = $con->prepare("INSERT INTO tds_tab (tds, data_send) VALUES (?, ?)");
        if ($stmtTDS) {
            $stmtTDS->bind_param("ds", $tds, $dateSend);
            if ($stmtTDS->execute()) {
                echo "Ok tds=" . htmlspecialchars($tds) . " date=" . htmlspecialchars($dateSend) . "\n";
            } else {
                echo "Error tds_tab: " . $stmtTDS->error . "\n";
            }
            $stmtTDS->close();
        } else {
            echo "Error prepare tds_tab: " . $con->error . "\n";
        }
    } else {
        echo "Error: Missing parameters (Ec=" . var_export($ec, true) . " Date=" . var_export($dateSend, true) . ")\n";
    }
} else {
    echo "Error: Empty or invalid JSON body\n";
}

$con->close();