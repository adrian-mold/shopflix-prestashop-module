<?php
//*******
//Marketplace XML Offers
//v.1.0.0 by Shopees
//*******
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
//print_r($_POST['offers']);//test
$params = array();
parse_str($_POST['offers'], $params);
//echo $params['product_id'];
if($params['product_id'] > 0){
	//create offers id in db
	$pid = (int)$params['product_id'];
	$nowdate = date('Y-m-d');
	if($params['has_comb'] == 0){
		$proffer = $pdo->prepare("SELECT product_id FROM ".$prefix."mpxmlproduct WHERE product_id='$pid' LIMIT 1");
		$proffer->execute();
		$proffer = $proffer->fetch(PDO::FETCH_ASSOC);	
		$lastoffersid = 0;
		//insert offers in db
		$price = $params['offer_price'];
		$qty = $params['offer_qty'];
		$from = $params['from'];
		$dfrom = explode("/",$from);
		$newfrom = $dfrom[2].'-'.$dfrom[1].'-'.$dfrom[0].' 00:00:00';
		$to = $params['to'];
		$dto = explode("/",$to);
		$newto = $dto[2].'-'.$dto[1].'-'.$dto[0].' 23:59:50';
		$shipdays =  $params['offer_ship'];
		if(isset($params['offer_enable'])){
			$enable = 1;
		} else {
			$enable = 0;
		}
		if(empty($proffer['product_id'])){
			//insert new
			$sql = "INSERT INTO `".$prefix."mpxmlproduct`(`id_mpxmlproduct`, `offers_id`, `product_id`, `offer_price`, `offer_from`, `offer_to`, `offer_quantity`, `shipping_lead_time`, `enable`) VALUES ('0','$lastoffersid','$pid','$price','$newfrom','$newto','$qty','$shipdays','$enable')";
			$insert = $pdo->exec($sql);
		} else {
			//update existed
			$sql = "UPDATE `".$prefix."mpxmlproduct` SET `offer_price`='$price',`offer_from`='$newfrom',`offer_to`='$newto',`offer_quantity`='$qty',`shipping_lead_time`='$shipdays',`enable`='$enable' WHERE product_id='$pid'";
			$update = $pdo->exec($sql);
		}
	} else {
		foreach($params['offer_combination_id'] as $k=>$val){
			$offer_combination_id = $val;
			$proffer = $pdo->prepare("SELECT id_mpxmlproduct FROM ".$prefix."mpxmlproduct WHERE product_id='$pid' AND offer_combination_id='$offer_combination_id' LIMIT 1");
			$proffer->execute();
			$proffer = $proffer->fetch(PDO::FETCH_ASSOC);	
			$lastoffersid = 0;
			//insert offers in db
				$price = $params['offer_price'][$k];
				$qty = $params['offer_qty'][$k];
				$from = $params['from'][$k];
				$dfrom = explode("/",$from);
				$newfrom = $dfrom[2].'-'.$dfrom[1].'-'.$dfrom[0].' 00:00:00';
				$to = $params['to'][$k];
				$dto = explode("/",$to);
				$newto = $dto[2].'-'.$dto[1].'-'.$dto[0].' 23:59:50';
				$shipdays =  $params['offer_ship'][$k];
				$combination_name = $params['offer_combination_title'][$k];
				if(isset($params['offer_enable'][$k])){
					$enable = 1;
				} else {
					$enable = 0;
				}
				if(empty($proffer['id_mpxmlproduct'])){
					//insert new
					$sql = "INSERT INTO `".$prefix."mpxmlproduct`(`id_mpxmlproduct`, `offers_id`, `product_id`,`offer_combination_id`,`offer_combination_title`, `offer_price`, `offer_from`, `offer_to`, `offer_quantity`, `shipping_lead_time`, `enable`) VALUES ('0','$lastoffersid','$pid','$offer_combination_id','$combination_name','$price','$newfrom','$newto','$qty','$shipdays','$enable')";
					$insert = $pdo->exec($sql);
				} else {
					//update existed
					$mid = $proffer['id_mpxmlproduct'];
					$sql = $pdo->prepare("UPDATE `".$prefix."mpxmlproduct` SET `offer_price`='$price',`offer_from`='$newfrom',`offer_to`='$newto',`offer_quantity`='$qty',`shipping_lead_time`='$shipdays',`enable`='$enable' WHERE id_mpxmlproduct='$mid'");
					$sql->execute();
				}		
		
		}
	}
	echo "Το Offer του προϊόντος ενημερώθηκε επιτυχώς!";
} else {echo 'Βρέθηκε κάποιο λάθος!';}
?>