<?php
// Start the session
session_start();
$url='https://myfishtank.altervista.org/index.php';
// Destroy the session.
if(session_destroy()){
  session_commit();
  header("Location: $url");
  exit('success');
  
}else{  
  exit('failed');
}
?>