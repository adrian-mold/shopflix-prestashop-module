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
$secret = $databaseConfig['parameters']['secret'];
include('../../config/config.inc.php'); //first check if this link is ok

include('../../init.php'); //this link also

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

//print_r($cookie);
$pids = array();
$data = array();
$proffer = $pdo->prepare('SELECT
		pp.price as pprice,p.id_product as pid,p.active as pactive, pa.id_product_attribute as id_product_attribute,ps.quantity as qty, pas.price as price
		FROM ps_product p
		LEFT JOIN ps_product_attribute pa ON (p.id_product = pa.id_product)
		LEFT JOIN ps_product_shop pp ON (p.id_product = pp.id_product)
		LEFT JOIN ps_product_attribute_combination pac ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN ps_product_attribute_shop pas ON (pac.id_product_attribute = pas.id_product_attribute)
		LEFT JOIN ps_attribute_lang pal ON (pac.id_attribute = pal.id_attribute)
		LEFT JOIN ps_stock_available ps  ON (pa.id_product_attribute = ps.id_product_attribute)
		WHERE  pal.id_lang = 1 GROUP BY pac.id_product_attribute');
$proffer->execute();
$proffer = $proffer->fetchAll(PDO::FETCH_ASSOC);
//print_r($proffer);
$nowdate = date('Y-m-d G:i:s');
$enddate = date('Y-m-d G:i:s', strtotime('+3 years'));
foreach($proffer as $p){
	$cn = '';
	$atrrid = $p['id_product_attribute'];
	$combination = new Combination($p['id_product_attribute']);
	$arr = $combination->getAttributesName(1);
	foreach($arr as $a){$cn .= $a['name'].' ';}
	$product = new Product($p['pid']);
	$tax = $product->getTaxesRate();

	$fprice = round(((($p['pprice'] + $p['price']) / 100) * $tax) + $p['pprice'] + $p['price'],2);
	//get discount
	$pid = $p['pid'];
	$discount = $pdo->prepare("SELECT reduction,reduction_type from ps_specific_price where id_product='$pid'");
	$discount->execute();
	$discount = $discount->fetch(PDO::FETCH_ASSOC);
	if(!empty($discount['reduction_type'])){
	if($discount['reduction_type'] == 'amount'){
		$discount_price = $discount['reduction'];
		$fprice =  $fprice - $discount_price;
	} else {
		$discount_price = $discount['reduction'] * 100;
		$fprice =  $fprice - round(((($fprice) / 100) * $discount_price) + $fprice,2);
	}
	} else {}
	$pids[] = $p['pid'];
	if($p['pactive'] == 1 && $p['qty'] > 0){ $enable = 1;} else {$enable = 0;}
	$data[] = array('productid'=>$p['pid'],'combination_id'=>$p['id_product_attribute'],'combination_title'=>$cn,'offer_price'=>$fprice,'offer_start'=>$nowdate,'offer_end'=>$enddate,'quantity'=>$p['qty'],'lead_time'=>1,'enable'=>$enable);
}	
	$products = array_unique($pids);

$profferp = $pdo->prepare("SELECT
		pp.price as pprice,p.id_product as pid,p.active as pactive, ps.quantity as qty
		FROM ps_product p
		LEFT JOIN ps_product_attribute pa ON (p.id_product = pa.id_product)
		LEFT JOIN ps_product_shop pp ON (p.id_product = pp.id_product)
		LEFT JOIN ps_stock_available ps  ON (p.id_product = ps.id_product)
		WHERE  p.id_product NOT IN ( '" . implode( "', '" , $products ) . "' )");
$profferp->execute();
$profferp = $profferp->fetchAll(PDO::FETCH_ASSOC);
foreach($profferp as $p){
	$product = new Product($p['pid']);
	$tax = $product->getTaxesRate();
	$fprice = round(((($p['pprice']) / 100) * $tax) + $p['pprice'],2);
	//get discount
	$pid = $p['pid'];
	$discount = $pdo->prepare("SELECT reduction,reduction_type from ps_specific_price where id_product='$pid'");
	$discount->execute();
	$discount = $discount->fetch(PDO::FETCH_ASSOC);
	if(!empty($discount['reduction_type'])){
	if($discount['reduction_type'] == 'amount'){
		$discount_price = $discount['reduction'];
		$fprice =  $fprice - $discount_price;
	} else {
		$discount_price = $discount['reduction'];		
		$fprice =  $fprice - round(($fprice * $discount_price),2);
	}
	} else {}
	$pids[] = $p['pid'];
	if($p['pactive'] == 1 && $p['qty'] > 0){ $enable = 1;} else {$enable = 0;}
			$data[] = array('productid'=>$p['pid'],'combination_id'=>'','combination_title'=>'','offer_price'=>$fprice,'offer_start'=>$nowdate,'offer_end'=>$enddate,'quantity'=>$p['qty'],'lead_time'=>1,'enable'=>$enable);
}

//insert products in offers data
foreach($data as $offer){
	$pid = $offer['productid'];
	$combination_id = $offer['combination_id'];
	$combination_title = $offer['combination_title'];
	$offer_price = $offer['offer_price'];
	$offer_start = $offer['offer_start'];
	$offer_end = $offer['offer_end'];
	$quantity = $offer['quantity'];
	$enable = $offer['enable'];
	$checkoffer = $pdo->prepare("SELECT id_mpxmlproduct from ".$prefix."mpxmlproduct where product_id='$pid' AND offer_combination_id='$combination_id'");
	$checkoffer->execute();
	$checkoffer = $checkoffer->fetch(PDO::FETCH_ASSOC);
	if(empty($checkoffer['id_mpxmlproduct'])){
		//create new
		$sql = "INSERT INTO `".$prefix."mpxmlproduct`(`id_mpxmlproduct`, `offers_id`, `product_id`, `offer_combination_id`, `offer_combination_title`, `offer_price`, `offer_from`, `offer_to`, `offer_quantity`, `shipping_lead_time`, `enable`) VALUES ('0','0','$pid','$combination_id','$combination_title','$offer_price','$offer_start','$offer_end','$quantity','1','$enable')";
		$insert = $pdo->exec($sql);		
	} else {
		//update old
		$offerid = $checkoffer['id_mpxmlproduct'];
		$sql = "UPDATE `".$prefix."mpxmlproduct` SET `offer_price`='$offer_price',`offer_quantity`='$quantity',`enable`='$enable' WHERE `id_mpxmlproduct`='$offerid'";
		$insert = $pdo->exec($sql);			
	}
	
}
	echo "Το Offers XML αναννεώθηκε επιτυχώς!";