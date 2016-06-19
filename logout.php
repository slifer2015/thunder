<?php
include 'init.php';
if ($_GB->GetSession('userID') != false) {
    $userID = $_GB->GetSession('userID');
    $Users->updateUserStatusOffline($userID);
}
session_destroy();
header('Location: index.php');
?>