<?php

require_once 'controllers/LatchController.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "pairingForm": 
            checkAuthenticated();
            LatchController::pairingForm();
            die();
            break;
        case "otp":
            LatchController::otp();
            die();
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET["action"])) {
    $action = $_GET["action"];
    switch ($action) {
        case "doPair": 
            LatchController::doPair();
            die();
            break;
        case "doUnpair": 
            checkAuthenticated();
            LatchController::doUnpair();
            die();
            break;
        case "checkOtp":
            LatchController::checkOtp();
            die();
            break;
    }
}