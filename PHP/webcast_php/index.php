<?php

require_once 'controllers/DemoController.php';

session_start();

function checkAuthenticated() {
    if (!isset($_SESSION["loggedIn"])) {
        header("Location: index.php?action=login");
    }
}

require_once 'latch/LatchRoutes.php';
require_once 'routes.php';
die();

