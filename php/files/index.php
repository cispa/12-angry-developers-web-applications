<?php

require_once 'static/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, ['cache' => false, ]);

require_once "dblib.php";

$db = new Db;
$db->init();

$messages = "";
if ( isset($_COOKIE['messages']) ) {
    $messages = $_COOKIE['messages'];
}

session_start();
// Check if the User is logged in
if ( isset($_SESSION['user']) && session_status() === PHP_SESSION_ACTIVE ) {
    // If user is logged in render his Notes into the content.html template
    $template = $twig->load('content.html');
    $notes = $db->query("SELECT * FROM notes WHERE user=:user", array("user"=> $_SESSION['user']));
    echo $template->render(['notes' => $notes, 'messages' => $messages]);
} else {
    // If user is not logged in render the login / registration page
    $template = $twig->load('login.html');
    echo $template->render(['messages'=> $messages]);
}
exit();

?>
