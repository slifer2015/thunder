<?php
/**
 * Created by PhpStorm.
 * User: Abderrahim
 * Date: 11/25/2015
 * Time: 11:33 PM
 */
/*
 * All database connection variables
 */
ob_start();
session_start();
error_reporting(0);
return $_Config = array(
    'DB_SERVER' => 'localhost',// db server
    'DB_USER' => 'root',// db user
    'DB_PASSWORD' => 'root',// db password (mention your db password here)
    'DB_NAME' => 'socialnetwork',// database name
    'DB_TABLE_PREFIX' => 'fa_'//database prefix
);
?>