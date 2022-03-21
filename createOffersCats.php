<?php
$databaseConfig = include '../../app/config/parameters.php';

$servername = $databaseConfig['parameters']['database_host'];
$username = $databaseConfig['parameters']['database_user'];
$password = $databaseConfig['parameters']['database_password'];
$database = $databaseConfig['parameters']['database_name'];
$prefix = $databaseConfig['parameters']['database_prefix'];

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
print_r($_POST['offers']);//test
$params = array();
parse_str($_POST['offers'], $params);
if(count($params['pid']) > 0){
	//create offers id in db
	$nowdate = date('Y-m-d');
	$query = "INSERT INTO `".$prefix."mpxmloffers`(`offers_id`, `date_added`) VALUES ('0','$nowdate')";
	$insert = $pdo->exec($query);
	$lastid = $pdo->prepare("SELECT offers_id FROM ".$prefix."mpxmloffers ORDER BY offers_id DESC LIMIT 1");
	$lastid->execute();
	$lastid = $lastid->fetch(PDO::FETCH_ASSOC);	
	$lastoffersid = $lastid['offers_id'];
	//insert offers in db
	foreach($params['pid'] as $key=>$id){
		$price = $params['offer_price'][$key];
		$qty = $params['offer_qty'][$key];
		echo $from = $params['offer_from'][$key];
		$dfrom = explode("/",$from);
		$newfrom = $dfrom[2].'-'.$dfrom[1].'-'.$dfrom[0].' 00:00:00';
		$to = $params['offer_to'][$key];
		$dto = explode("/",$to);
		$newto = $dto[2].'-'.$dto[1].'-'.$dto[0].' 23:59:50';
		$shipdays =  $params['offer_ship'][$key];
		
		$sql = "INSERT INTO `".$prefix."mpxmlproduct`(`id_mpxmlproduct`, `offers_id`, `product_id`, `offer_price`, `offer_from`, `offer_to`, `offer_quantity`, `shipping_lead_time`, `enable`) VALUES ('0','$lastoffersid','$id','$price','$newfrom','$newto','$qty','$shipdays','1')";
		//$insert = $pdo->exec($sql);
	}
	echo "Το Offers XML δημιουργήθηκε επιτυχώς με κωδικό: ".$lastoffersid;
}
?>