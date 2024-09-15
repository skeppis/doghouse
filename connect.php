<?php
$dbname = 'mindatabas';
$hostname = 'localhost';
$DB_USER = 'root';
$DB_PASSWORD = '';
$options  = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'");

try {
    $dbconn = new PDO("mysql:host=$hostname;dbname=$dbname;", $DB_USER, 
    $DB_PASSWORD, $options);
    $dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    /*** echo a message saying we have connected ***/
    //echo 'Connected to database.<br />';
}
catch(PDOException $e){
    // For debug purpose, shows all connection details
    echo 'Connection failed: '.$e->getMessage()."<br />";
      // Hide connection details.
    //echo 'Could not connect to database.<br />'); 
}
