<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "login": 
            DemoController::login();
            break;
        case "logout":
            DemoController::logout();
            break;
        case "profile": 
            checkAuthenticated();
            DemoController::profile();
            break;
        case "editProfile": 
            checkAuthenticated();
            DemoController::editProfile();
            break;
        default:
            include 'views/404.php';
    }

} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "doLogin": 
            DemoController::doLogin();
            break;
        case "doEditProfile": 
            checkAuthenticated();
            DemoController::doEditProfile();
            break;
        default:
            include 'views/404.php';
    }
} else {
    include 'views/404.php';
}

