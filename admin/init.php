<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 9/26/2015
 * Time: 4:11 AM
 */

if (!file_exists('../config.php')) {
    header('Location: ../install/index.php');
    exit;
}
// include the database connection class
include '../core/classes/DataBase.php';
// include the Users class
include '../core/classes/Users.php';
// include the pagination class
include '../core/classes/Pagination.php';
// include the General class
include '../core/classes/General.php';;
// include the Security class
include '../core/classes/Security.php';
// include the Posts class
include '../core/classes/Posts.php';
// include the config.php file
include '../config.php';
// include the RelationShip class
include '../core/classes/RelationShip.php';

$_DB = new DataBase($_Config);
$_DB->connect();
$_DB->selectDB();
$Security = new Security($_DB);
$_GB = new General($_DB, $Security);
$Relation = new RelationShip($_GB);
$Users = new Users($_GB, $Relation);
$Feeds = new Posts($_GB, $Relation);

