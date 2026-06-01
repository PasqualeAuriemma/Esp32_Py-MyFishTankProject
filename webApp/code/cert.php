<?php
// PHP code to retrieve your certificate:
    $g = stream_context_create (array("ssl" => array("capture_peer_cert" => true, "capture_peer_cert_chain" => true)));
    $r = stream_socket_client("ssl://myfishtank.altervista.org:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $g);
    $cont = stream_context_get_params($r);
    $certinfo = openssl_x509_parse($cont["options"]["ssl"]["peer_certificate"]);
print_r($certinfo);


    
?>      