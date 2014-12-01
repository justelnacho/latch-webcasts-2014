<?php

require_once 'db/dbHelper.php';
require_once 'views/common.php';
require_once 'latch/sdk/Error.php';
require_once 'latch/sdk/Latch.php';
require_once 'latch/sdk/LatchResponse.php';
require_once 'latch/LatchConfig.php';

class LatchController {

    public static function pairingForm() {
        $userPaired = DBHelper::isPaired($_SESSION["userId"]);
        include 'latch/views/PairingForm.php';
    }

    public static function doPair() {
        $api = DBHelper::getLatchApi();
        $response = $api->pair($_POST["pairingToken"]);
        $data = $response->getData();
        if (!is_null($data) && property_exists($data, "accountId")) {
            $accountId = $data->accountId;
            if (!DBHelper::isPaired($_SESSION["userId"])) {
                DBHelper::storeAccountId($accountId);
                setMsg("success", "Correctly paired");
                header("Location: index.php?action=profile");
                die();
            }
        }
        setMsg("error", "Error pairing account");
        header("Location: index.php?action=profile");
        die();
    }

    public static function doUnpair() {
        if (DBHelper::isPaired($_SESSION["userId"])) {
            $api = DBHelper::getLatchApi();
            $accountId = DBHelper::getAccountId($_SESSION["userId"]);
            if ($accountId != null && $accountId != '') {
                $api->unpair($accountId);
                DBHelper::removeAccountId($_SESSION["userId"]);
                setMsg("success", "Correctly unpaired");
                header("Location: index.php?action=profile");
                die();
            }
        }
        setMsg("error", "Error unpairing account");
        header("Location: index.php?action=profile");
        die();
    }

    public static function checkOtp() {
        LatchController::checkIfAlmostAuthenticated();
        if (isset($_POST["otp"])) {
            $storedOtp = DBHelper::getAndRemoveOtp($_SESSION["userId"]);
            if ($_POST["otp"] == $storedOtp) {
                $_SESSION["loggedIn"] = true;
                unset($_SESSION["almostAuthenticated"]);
                header("Location: index.php?action=profile");
                die();
            } else {
                session_unset();
                setMsg("error", "Wrong second factor");
                header("Location: index.php?action=login");
                die();
            }
        } else {
            setMsg("error", "User/password incorrect");
            header("Location: index.php?action=login");
            die();
        }
    }

    private static function checkIfAlmostAuthenticated() {
        if (!isset($_SESSION["almostAuthenticated"])) {
            header("Location: index.php?action=logout");
        }
    }
}