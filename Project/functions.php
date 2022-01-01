<?php
session_start();
// connect to database
$db = mysqli_connect('localhost', 'root', '', 'whatstec');



/////////////////////////////////////REGISTER////////////////////////////////////////////////

if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['register_btn'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $contact = mysqli_real_escape_string($db, $_POST['contact']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($db, $_POST['confirm_password']);

    if ($password != $confirm_password) {
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO utilizadores (username,password,contacto,email)
VALUES ('" . $username . "','" . $hashed_password . "','" . $contact . "','" . $email . "')";
        $result = mysqli_query($db, $sql);

        $return = array();
        $return["message"] = "user added successfully";

        header("Location: login.html");

    }
}


/////////////////////////////////////LOGIN////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['login_btn'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);

    $result = mysqli_query($db, "SELECT * FROM utilizadores WHERE username='$username'");
     $row = mysqli_fetch_assoc($result);
    if(isset($row['username'])){
        $hashed_password = $row['password'];
        if(password_verify($_POST['password'], $hashed_password )){
            $_SESSION["user"] = $row["username"];
            $return = array();
            $return["message"] = "OK";
            echo json_encode($return);
            $sql = "UPDATE utilizadores SET online='1' WHERE username= '".$_SESSION['user']."'";
            $result = mysqli_query($db, $sql);
            header('Location: chat.html');
        }else{
            $return = array();
            $return["message"] = "Error trying to login";
            echo json_encode($return);
            header('Location: login.html');
        }
    }
}

/////////////////////////////////////GET CONTACTS////////////////////////////////////////////////

// Deve devolver apenas contactos que ainda não se encontrem na agenda do utilizador e também não deve devolver o próprio
if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['action']) and $_GET['action'] === 'getcontacts') {
    $search = mysqli_real_escape_string($db, $_GET['term']);
   /* $sql = "SELECT DISTINCT u.username FROM utilizadores u
            LEFT JOIN agenda_contactos c ON u.username = c.username
            WHERE u.username NOT LIKE '".$_SESSION['user']."' c.contacto IS NULL AND u.username NOT LIKE '".$_SESSION["user"]."' AND u.username LIKE '%".$search."%'";
*/

    /*$sql ="SELECT DISTINCT u.username FROM utilizadores u, agenda_contactos c where u.username = c.username AND u.username != '".$_SESSION['user']."'  AND u.username LIKE '%".$search."%' AND
    u.username NOT IN (SELECT contacto from agenda_contactos WHERE username = '".$_SESSION['user']."')";

    */

    $sql="SELECT username from utilizadores 
            where username not in(select contacto from agenda_contactos WHERE username LIKE '".$_SESSION['user']."')
             and username not like '".$_SESSION['user']."' 
             and username LIKE '%".$search."%'";
    $result = mysqli_query($db, $sql);
    $queryResults = mysqli_num_rows($result);
    $resultado = [];
    $linha = array();
    if ($queryResults > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $linha["username"] = $row["username"];
            array_push($resultado, $linha);
        }
    }
    echo json_encode($resultado);
}
/////////////////////////////////////ADD CONTACTS////////////////////////////////////////////////

//Atenção falta a query de insert para enviar a mensagem Olá ao utilizador vejam na aula de php como fazer está lá o código que precisam.
if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['action']) and $_GET['action'] === 'addContact') {
    $search= mysqli_real_escape_string($db, $_GET['user']);
    $sql = "Insert into agenda_contactos (username,contacto) values ('".$_SESSION["user"]."','$search' )";
    $result = mysqli_query($db, $sql);
    $resultado = Array();

    if($result)
    {
        $resultado["message"]="Contact added sucessfully";
        $sql_mensagem = "INSERT into mensagem (Emissor_id,Recetor_id,Corpo,Hora_envio) Values ('".$_SESSION["user"]."', '".$_GET['user']."', 'Hi!!',NOW())";
        $result = mysqli_query($db, $sql_mensagem);
    }else{
        $resultado["message"]="add contact failed!";
    }
    echo(json_encode($resultado));
}


/////////////////////////////////////GET MESSAGE////////////////////////////////////////////////

//Atenção está querie não está bem verifiquem no phpmyadmin. Ela vai retornar tudo em vez de retornar o pretendido.
//Aqui devem: 
// Ir buscar os contactos associado ao user logado;
// Ir buscar a ultima mensagem trocada entre os dois;
// Devolver também outros campos que são pedidos;
if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['action']) and $_GET['action'] === 'getMessages') {

    $sql = "SELECT u.online, u.username, m.Corpo, m.Hora_envio, m.Recetor_id, m.Emissor_id 
            FROM utilizadores u, mensagem m WHERE u.username = m.Recetor_id AND Emissor_id = '".$_SESSION["user"]."'
             GROUP BY m.Recetor_id ORDER BY m.Hora_envio";
    $result = mysqli_query($db,  $sql);
    if(mysqli_num_rows($result)>0)
    {
        $all_results = [];
        while($row = mysqli_fetch_assoc($result) ) {
             $linha = Array();
            $linha["online"] = $row["online"];
             $linha["contact_name"] = $row["username"];
             $linha["body"] = $row["Corpo"];
             $linha["sent_time"] = $row["Hora_envio"];
             $linha["sender_id"] = $row["Emissor_id"];
             $linha["receiver_id"] = $row["Recetor_id"];

             array_push($all_results, $linha);
        }
        echo json_encode($all_results);
    }else{
        $queryResults['message'] = "no messages";
        echo json_encode($queryResults);
    }
   
}


/////////////////////////////////////GET CHAT////////////////////////////////////////////////

if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['action']) and $_GET['action'] === 'getChat') {
    $search= mysqli_real_escape_string($db, $_GET['user']);
    $sql = "SELECT (CASE WHEN Emissor_id = (SELECT username FROM utilizadores WHERE 
                username = '".$_SESSION['user']."') THEN 'sent' WHEN Recetor_id = (SELECT username FROM utilizadores WHERE 
                username = '".$_SESSION['user']."') THEN 'received' END) AS `type`,`Corpo`, `read`, `Hora_envio` FROM mensagem WHERE (Emissor_id= (SELECT username FROM utilizadores WHERE 
                username = '".$_SESSION['user']."') AND Recetor_id = (SELECT username FROM utilizadores WHERE username = '".$_GET['user']."'))
                OR (Emissor_id = (SELECT username FROM utilizadores WHERE username = '".$_GET['user']."') AND
                Recetor_id = (SELECT username FROM utilizadores WHERE username = '".$_SESSION['user']."'))
                ORDER BY Hora_envio ASC;";



    $result = mysqli_query($db, $sql);
    $resultado = [];

        while($row = mysqli_fetch_assoc($result) ){
            $linha = Array();
            $linha["body"] = $row["Corpo"];
            $linha["type"] = $row["type"];
            $linha["read"] = $row["read"];
            array_push($resultado, $linha);

        }
    echo json_encode($resultado);
        $sql = "Update mensagem  set `read` = 1 where Emissor_id = '".$_GET['user']."'
                And Recetor_id = '".$_SESSION['user']."'";
        $result = mysqli_query($db, $sql);
}


/////////////////////////////////////SEND MESSAGE////////////////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['action']) and $_POST['action'] === 'sendMessage' and isset($_POST['body'])  and isset($_POST['user']) ) {

    $sql = "INSERT INTO mensagem (`Corpo`,`Hora_envio`,`Recetor_id`,`Emissor_id`, `read`) VALUES ('".$_POST['body']."',NOW(),'".$_POST['user']."','".$_SESSION['user']."','0')";

    $result = mysqli_query($db, $sql);

    if($result){
        $return = array();
        $return["message"] = "Message Sent Successfully";
        echo json_encode($return);
    }else{
        $return = array();
        $return["message"] = "no message";
        echo json_encode($return);

    }
}


/////////////////////////////////////GET LOGGED USER////////////////////////////////////////////////


if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['action']) and $_GET['action'] === 'getLoggedUser') {
   $result = Array();
   if( isset($_SESSION['user'])){
        $result['username'] = $_SESSION['user'];
        $result['message'] = "OK";
   }else{
    $result['message'] = "no messages";
  }
   echo json_encode($result);
}
