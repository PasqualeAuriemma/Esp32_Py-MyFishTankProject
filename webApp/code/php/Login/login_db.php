    <?php
        session_start();
    	if(isset($_POST['login'])){        
            include("../connection.php");
            include("../queryAndFunction.php");
            if ($con->connect_error) {
              die("Connection failed: " . $con->connect_error);
            }
            $email = $con->real_escape_string($_POST["emailPHP"]);
            $password = md5($con->real_escape_string($_POST["passwordPHP"]));
            $data =  $con->query("SELECT id FROM `users` WHERE email='$email' AND password='$password'");
            if($data->num_rows > 0){
              $_SESSION["email"] = $email;
              $_SESSION["loggedIn"] = '1';
              
              $params = session_get_cookie_params();
              setcookie(session_name(), $_COOKIE[session_name()], time() + 60*60*24*30, $params["domain"], $params["secure"], $params["httponly"]);
              
              exit('success');
            }else{
               alert("wrong user or password");
               exit('failed');
            }
        }
        
       
    

//if (!empty($data)) {
//   login($data); //memorizza in sessione i dati dell'utente
//   echo "Benvenuto {$data[user]}"; //visualizza benvenuto
//   header("Location: index.php"); //torna al form di autenticazione
//} else {
//   header("Location: index.php"); //torna al form di autenticazione
//   exit;

//}

/**
* Tiene traccia nella sessione dei dati
* @param array $info array con i dati estratti dal DB relative
* all’utente loggato
*/
//function login($info){
//$_SESSION['my_test_loggedin'] = $info;
//}


?>