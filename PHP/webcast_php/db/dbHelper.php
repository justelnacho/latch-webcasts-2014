<?php

require_once 'db/db.php';
require_once 'views/common.php';
require_once 'latch/sdk/Error.php';
require_once 'latch/sdk/Latch.php';
require_once 'latch/sdk/LatchResponse.php';
require_once 'latch/LatchConfig.php';

class DBHelper {
    public static function getLatchApi() {
        return new Latch(LatchConfig::$appId, LatchConfig::$secret);
    }

    public static function isPaired($userId)  {
        $connection = getConnection();
        $stmt = $connection->prepare("SELECT count(*) as count FROM latch WHERE user=:userId");
        $stmt->execute(array(":userId" => $_SESSION["userId"]));
        return ($stmt->fetch()["count"]) > 0;
    }

    public static function storeAccountId($accountId) {
        $connection = getConnection();
        $stmt = $connection->prepare("INSERT INTO latch (user, accountId) VALUES (:userId, :accountId)");
        $stmt->execute(array(":userId" => $_SESSION["userId"], ":accountId" => $accountId));
    }

    public static function storeOtp($userId, $otp) {
        $connection = getConnection();
        $stmt = $connection->prepare("UPDATE latch SET otp=:otp WHERE user=:userId");
        $stmt->execute(array(":otp" => $otp, ":userId" => $userId));
    }

    public static function getAccountId($userId) {
        $connection = getConnection();
        $stmt = $connection->prepare("SELECT * FROM latch WHERE user=:userId");
        $stmt->execute(array(":userId" => $userId));
        return $stmt->fetch()["accountId"];
    }

    public static function removeAccountId($userId) {
        $connection = getConnection();
        $stmt = $connection->prepare("DELETE FROM latch WHERE user=:userId");
        $stmt->execute(array(":userId" => $userId));
    }

    public static function getAndRemoveOtp($userId) {
        $connection = getConnection();
        $stmt = $connection->prepare("SELECT * FROM latch WHERE user=:userId");
        $stmt->execute(array(":userId" => $userId));
        $otp = $stmt->fetch()["otp"];
        $stmt = $connection->prepare("UPDATE latch SET otp=null WHERE user=:userId");
        $stmt->execute(array(":userId" => $userId));
        return $otp;
    }

    public static function authenticate($user, $pass) {
        $connection = getConnection();
        $stmt = $connection->prepare("SELECT * FROM users WHERE username=:username AND password=:password");
        $stmt->execute(array(":username" => $user, ":password" => $pass));
        $result = $stmt->fetch();
        if ($result != null && isset($result["user_id"])) {
            return $result["user_id"];
        } else {
            return -1;
        }
    }

}