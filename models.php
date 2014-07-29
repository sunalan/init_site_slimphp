<?php

DBModule::configure('mysql:dbname='.$DB_CONFIG[$mode]['DB_NAME'].';host='.$DB_CONFIG[$mode]['DB_SERVER']);
DBModule::configure('username',         $DB_CONFIG[$mode]['DB_USER']);
DBModule::configure('password',         $DB_CONFIG[$mode]['DB_PASSWD']);
DBModule::configure('driver_options',   array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
));

//require 'models/xxx.php';