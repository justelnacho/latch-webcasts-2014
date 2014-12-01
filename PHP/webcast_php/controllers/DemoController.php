<?php 

require_once 'db/dbHelper.php';
require_once 'views/common.php';
require_once 'latch/LatchConfig.php';

class DemoController {

    static function login() {
        if (isset($_SESSION["loggedIn"])) {
            header('Location: index.php?action=profile');
        } else {
            include 'views/login.php';
        }
    }

    static function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
    }

    static function doLogin() {
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $user = DBHelper::authenticate($username, $password);
            if ($user != -1) {
                $_SESSION["loggedIn"] = true;
                $_SESSION["userId"] = $user;

                $latchStatus = self::checkLatchStatus(LatchConfig::$loginOperation);
                if ($latchStatus == "off") {
                    unset($_SESSION["loggedIn"]);
                    unset($_SESSION["userId"]);
                    setMsg("error", "User/password incorrect");
                    header('Location: index.php?action=login');
                    die();
                }

                header('Location: index.php?action=profile');
            } else {
                setMsg("error", "User/password incorrect");
            }
        } 
        header('Location: index.php?action=login');
    }

    private static function checkLatchStatus($operationId) {
        $accountId = DBHelper::getAccountId($_SESSION["userId"]);
        if (isset($accountId) && !empty($accountId)) {
            $api = DBHelper::getLatchApi();
            $status = $api->operationStatus($accountId, $operationId);
            if (!empty($status) && $status->getData() != null) {
                $operations = $status->getData()->operations;
                $operation = $operations->{$operationId};
                if ($operation->status == "on" && property_exists($operation, "two_factor")) {
                    DBHelper::storeOtp($_SESSION["userId"], $operation->two_factor->token);
                    $_SESSION["almostAuthenticated"] = true;
                    include 'latch/views/otp.php';
                    die();
                }
                return $operation->status;
            }
        }
        return "on";
    }

    static function profile() {
        $profile = getUserProfile($_SESSION["userId"]);
        include 'views/profile.php';
    }

    static function editProfile() {
        $profile = getUserProfile($_SESSION["userId"]);
        include 'views/editProfile.php';
    }

    static function doEditProfile() {
        if (isset($_POST["name"]) && isset($_POST["surname"]) && isset($_POST["description"])) {
            $name = $_POST["name"];
            $surname = $_POST["surname"];
            $description = $_POST["description"];

            $latchStatus = self::checkLatchStatus(LatchConfig::$editProfileOperation);
            if ($latchStatus == "off") {
                setMsg("error", "Sorry, cannot update the profile.");
                header('Location: index.php?action=profile');
                die();
            }

            updateProfile($_SESSION["userId"], $name, $surname, $description);
            setMsg("success", "Data updated successfully.");
        } else {
            setMsg("error", "Error with the input data. Cannot save profile.");
        }
        header("Location: index.php?action=profile");
    }

    private static function getLatchApi() {
        return new Latch(LatchConfig::$appId, LatchConfig::$secret);
    }
}