<?php

// Try to load the JSON from the POST data
$messages = "";
$data = array();
try {
    $data = json_decode(file_get_contents('php://input'), true);
} catch(Exception $e) {
    $messages .= 'Received Malformed JSON: '.$e;
}

// Check if all parameters are there
if ( isset($data['username']) && isset($data['password']) ) {

    require_once "dblib.php";

    $db = new Db;
    $db->init();
    // Check if users exists
    $query_data = array("username" => $data['username']);
    $r = $db->query("SELECT * from users WHERE username=:username LIMIT 1", $query_data);
    // Check if passwords are matching
    if ( !empty($r) && isset($r[0]['password']) && password_verify($data['password'], $r[0]['password']) ) {
        // If credentials are correct, log in user
        session_start();
        if ( isset($r[0]['username']) ) {
            $_SESSION['user'] = $r[0]['username'];
        }
        header('HTTP/1.1 200 OK');
    } else {
        header('HTTP/1.1 400 Bad Request');
        $messages .= 'Username/Password mismatch!';
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    $messages .= 'Missing Parameters!';
}

setcookie("messages", $messages, time() + 1);
exit();

?>
