<?php

$messages = "";
session_start();
// Check if the User is logged in
if ( isset($_SESSION['user']) && session_status() === PHP_SESSION_ACTIVE ) {

    // Try to load the JSON from the POST data
    $data = array();
    try {
        $data = json_decode(file_get_contents('php://input'), true);
    } catch(Exception $e) {
        $messages .= 'Received Malformed JSON: '.$e;
    }

    // Check if all parameters are there
    if ( isset($data['head']) && isset($data['content']) ) {

        require_once "dblib.php";

        $db = new Db;
        $db->init();

        // Save new Note inside the Database
        $query_data = array("user" => $_SESSION['user'], "head" => $data['head'], "content" => $data['content']);
        try {
            $db->execute("INSERT INTO notes VALUES (:user, :head, :content, DATETIME('now'))", $query_data);
            header('HTTP/1.1 200 OK');
        } catch(Exception $e) {
            $messages .= 'Insertion Failed!';
            header('HTTP/1.1 400 Bad Request');
        }
    } else {
        $messages .= 'Missing Parameters!';
        header('HTTP/1.1 400 Bad Request');
    }
} else {
    $messages .= 'You are not logged in!';
    header('HTTP/1.1 400 Bad Request');
}

setcookie("messages", $messages, time() + 1);
exit();

?>
