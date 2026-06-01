<?php
// Start the session
session_start();
$url="https://". $_SERVER['HTTP_HOST'] ."/index.php";
// Destroy the session.
if(session_destroy()){
  session_commit();
  header("Location: ".$url);
  exit('success');
}else{  
  exit('failed');
}
?>
