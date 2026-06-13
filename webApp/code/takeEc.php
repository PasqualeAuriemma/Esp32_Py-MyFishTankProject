<?php
// Endpoint IoT protetto da token segreto condiviso.

include("php/connection.php");
include("php/queryAndFunction.php");

// ── Verifica token ─────────────────────────────────────────────────────────────
$token = $_POST["token"] ?? $_SERVER['HTTP_X_IOT_TOKEN'] ?? '';
if (!defined('IOT_SECRET') || !hash_equals(IOT_SECRET, $token)) {
    http_response_code(401);
    exit("Unauthorized");
}

if (!empty($_POST)) {
    $ec       = isset($_POST["Ec"])   ? $_POST["Ec"]                      : null;
    $dataSend = isset($_POST["Date"]) ? substr($_POST["Date"], 0, 10) : null;

    if ($ec !== null && $dataSend !== null) {
        $tds = floatval($ec) * 0.64;

        // Inserimento in ec_tab
        $stmtEC = $con->prepare("INSERT INTO ec_tab (ec, data_send) VALUES (?, ?)");
        if ($stmtEC) {
            $stmtEC->bind_param("ds", $ec, $dataSend);
            if ($stmtEC->execute()) {
                echo "Ok " . htmlspecialchars($tds) . " " . htmlspecialchars($dataSend) . "\n";
            } else {
                echo "Error: " . $stmtEC->error . "\n";
            }
            $stmtEC->close();
        } else {
            echo "Error: " . $con->error . "\n";
        }

        // Inserimento in tds_tab
        $stmtTDS = $con->prepare("INSERT INTO tds_tab (tds, data_send) VALUES (?, ?)");
        if ($stmtTDS) {
            $stmtTDS->bind_param("ds", $tds, $dataSend);
            if ($stmtTDS->execute()) {
                echo "Ok " . htmlspecialchars($ec) . " " . htmlspecialchars($dataSend) . "\n";
            } else {
                echo "Error: " . $stmtTDS->error . "\n";
            }
            $stmtTDS->close();
        } else {
            echo "Error: " . $con->error . "\n";
        }
    } else {
        echo "Error: Missing parameters\n";
    }
}
$con->close();
