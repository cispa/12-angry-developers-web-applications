<?php

require_once 'static/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, ['cache' => false, ]);

session_start();
// Check if the User is logged in
if ( isset($_SESSION['user']) && session_status() === PHP_SESSION_ACTIVE ) {
    // Render the about page
    $template = $twig->load('about.html');
    echo $template->render([]);
    exit();
} else {
    // Redirect to main page
    header("Location: index.php");
    exit();
}

?>
