<?php 

// Views common functions

function existsMsg($key) {
    return isset($_SESSION["flash"]) && isset($_SESSION["flash"][$key]);
}

function setMsg($key, $msg) {
    if (!isset($_SESSION["flash"])) {
        $_SESSION["flash"] = array();
    }
    $_SESSION["flash"][$key] = $msg;
}

function getMsg($key) {
    if (isset($_SESSION["flash"]) && isset($_SESSION["flash"][$key])) {
        $value = $_SESSION["flash"][$key];
        unset($_SESSION["flash"][$key]);
        return $value;
    }
}

function clearMessages() {
    unset($_SESSION["flash"]);
    $_SESSION["flash"] = array();
}

function writeErrorIfExists() {
    if (existsMsg("error")) {
        echo "<div><p>" . htmlentities(getMsg("error")) . "</p></div>";
    }   
}

function writeSuccessIfExists() {
    if (existsMsg("success")) {
        echo "<div><p>" . htmlentities(getMsg("success")) . "</p></div>";
    }  
}