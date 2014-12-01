<?php

require 'dbConfig.php';

function getConnection() {
    return new PDO(DBConfig::$connString, DBConfig::$dbUser, DBConfig::$dbPass);
}

function getUserProfile($userId) {
    $connection = getConnection();
    $stmt = $connection->prepare("SELECT name,surname,description FROM profiles WHERE user=:userId");
    $stmt->execute(array(":userId" => $userId));
    return $stmt->fetch();
}

function updateProfile($userId, $name, $surname, $description) {
    $connection = getConnection();
    $stmt = $connection->prepare("UPDATE profiles SET name=:name, surname=:surname, description=:description WHERE user=:userId");
    $stmt->execute(array(":name" => $name, ":surname" => $surname, ":description" => $description, ":userId" => $userId));
}

