<?php
$databaseConfig = include '../../app/config/parameters.php';

$servername = $databaseConfig['parameters']['database_host'];
$username = $databaseConfig['parameters']['database_user'];
$password = $databaseConfig['parameters']['database_password'];
$database = $databaseConfig['parameters']['database_name'];
$prefix = $databaseConfig['parameters']['database_prefix'];
$secret = $databaseConfig['parameters']['secret'];

try {
$pdo = new PDO("mysql:host=$servername;dbname=$database", $username, $password,
       array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
}
catch(Exception $e) {
    echo 'Exception -> ';
    var_dump($e->getMessage());
}

$oid = (int)$_POST['offerid'];
if($oid > 0){
	$sql = "DELETE FROM `".$prefix."mpxmloffers` WHERE offers_id=".$oid;
	$delete = $pdo->exec($sql);	
	$sqlpro = "DELETE FROM `".$prefix."mpxmlproduct` WHERE offers_id=".$oid;
	$deletepro = $pdo->exec($sqlpro);
} else {}

