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

    $data = array("username" => $data['username'], "password" => password_hash($data['password'], PASSWORD_DEFAULT));
    // Try to create and log in the user
    try {
        $db->execute("INSERT INTO users (username, password) VALUES (:username, :password)", $data);
        session_start();
        $_SESSION["user"] = $data['username'];
        header('HTTP/1.1 200 OK');
    } catch(Exception $e) {
        header('HTTP/1.1 400 Bad Request');
        $messages .= 'Name is already chosen!';
    }
} else {
    header('HTTP/1.1 400 Bad Request');
    $messages .= 'Missing Parameters!';
}

setcookie("messages", $messages, time() + 1);
exit();

?>
