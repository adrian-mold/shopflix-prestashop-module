<?php

$databaseConfig = include '../../app/config/parameters.php';



$servername = $databaseConfig['parameters']['database_host'];

$username = $databaseConfig['parameters']['database_user'];

$password = $databaseConfig['parameters']['database_password'];

$database = $databaseConfig['parameters']['database_name'];

$prefix = $databaseConfig['parameters']['database_prefix'];

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



$id = (int)$_GET['id'];

//get config email

$secret = $pdo->prepare("SELECT value FROM ".$prefix."configuration WHERE name='PS_SHOP_EMAIL'");

$secret->execute();

$secret = $secret->fetch(PDO::FETCH_ASSOC);	 

$secret = md5($secret['value']);

if($secret != $_GET['s']){

	die('no entry');

} else {}

header("Content-type: text/xml"); 



//get offers from db

$offers = $pdo->prepare("SELECT * FROM ".$prefix."mpxmlproduct WHERE offers_id='$id'");

$offers->execute();

$offers = $offers->fetchAll(PDO::FETCH_ASSOC);	

if(count($offers) > 0){

        $xmlvalues = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

		$xmlvalues .= "<MPITEMS>";

        $xmlvalues .= '<created_at><![CDATA[' . date('Y-m-d G:i:s') . ']]></created_at>';

        $xmlvalues .= '<products>';	

		$i = 1;

	foreach($offers as $offer){

		$pid = $offer['product_id'];

		$sql = $pdo->prepare("SELECT p.id_product, p.active, p.ean13, pl.name, p.price, p.id_tax_rules_group, p.wholesale_price, p.reference, p.supplier_reference, p.id_supplier, p.id_manufacturer, p.upc, p.ecotax, p.weight, p.quantity as quantity, pl.description_short, pl.description, pl.meta_title, pl.meta_keywords, pl.meta_description, pl.link_rewrite, pl.available_now, pl.available_later, p.available_for_order, p.date_add, p.show_price, p.online_only, p.condition, p.id_shop_default,p.id_category_default

		FROM ".$prefix."product p

		LEFT JOIN ".$prefix."product_lang pl ON (p.id_product = pl.id_product)

		LEFT JOIN ".$prefix."category_product cp ON (p.id_product = cp.id_product)

		LEFT JOIN ".$prefix."category_lang cl ON (cp.id_category = cl.id_category)

		LEFT JOIN ".$prefix."category c ON (cp.id_category = c.id_category)

		LEFT JOIN ".$prefix."product_tag pt ON (p.id_product = pt.id_product)

		WHERE 

		p.id_product = '$pid' AND

		pl.id_lang = 1

		AND cl.id_lang = 1

		AND p.id_shop_default = 1

		AND c.id_shop_default = 1");

		$sql->execute();

		$product = $sql->fetch(PDO::FETCH_ASSOC);	

		$normalprice = round($product['price'],2);

		//get product img_url

				$imgp = $pdo->prepare("SELECT ".$prefix."product.id_product, ".$prefix."image.id_image 

				FROM ".$prefix."product, ".$prefix."image

				WHERE ".$prefix."product.id_product = '$pid' AND ".$prefix."image.id_product = ".$prefix."product.id_product AND ".$prefix."image.position = 1");	

				$imgp->execute();

				$imgp = $imgp->fetch(PDO::FETCH_ASSOC);		

				$imgpath = $imgp['id_image'];

		//get eshop url				

				$prestapath = $pdo->prepare("SELECT * from ".$prefix."shop_url where id_shop ='1'");

				$prestapath->execute();

				$prestapath = $prestapath->fetch(PDO::FETCH_ASSOC);

				$shopurl = 'https://'.$prestapath['domain_ssl'].$prestapath['physical_uri'];

				$img_url = $shopurl.'img/p/'.implode('/', str_split((string) $imgpath)).'/'.$imgpath.'.jpg';

		//get category name

				$catname = $pdo->prepare("SELECT ".$prefix."category_product.id_category,".$prefix."category_lang.name FROM ".$prefix."category_lang,".$prefix."category_product WHERE  ".$prefix."category_product.id_category=".$prefix."category_lang.id_category AND ".$prefix."category_product.id_product='$pid' AND ".$prefix."category_lang.id_lang=1 ORDER BY ".$prefix."category_lang.id_category DESC LIMIT 1");

				$catname->execute();

				$catname = $catname->fetch(PDO::FETCH_ASSOC);		

				$catnamereal = $catname['name'];

		//get product url

				$purl = $pdo->prepare('SELECT

				'.$prefix.'product.id_product,

				'.$prefix.'product.id_category_default,

				'.$prefix.'product.active,

				'.$prefix.'product_lang.link_rewrite,

				'.$prefix.'category_lang.link_rewrite,

				CONCAT("", '.$prefix.'category_lang.link_rewrite, "/", '.$prefix.'product.id_product, "-",'.$prefix.'product_lang.link_rewrite,".html") AS ConcatenatedString

				FROM

				'.$prefix.'product

				INNER JOIN '.$prefix.'product_lang ON '.$prefix.'product.id_product = '.$prefix.'product_lang.id_product

				INNER JOIN '.$prefix.'category_lang ON '.$prefix.'product.id_category_default = '.$prefix.'category_lang.id_category WHERE '.$prefix.'product.id_product='.$pid.'');	

				$purl->execute();

				$purl = $purl->fetch(PDO::FETCH_ASSOC);		

				$product_url = $shopurl.$purl['ConcatenatedString'];	

				$priceshop = round(Product::getPriceStatic($pid),2);

				$product['mpn'] = '';

				$xmlvalues .= '<product cnt="'.$i.'">';

                $xmlvalues .= '<product_id><![CDATA[' . $pid. ']]></product_id>';

				$xmlvalues .= '<category><![CDATA[' . $catnamereal. ']]></category>';

                $xmlvalues .= '<SKU><![CDATA[' . $product['reference'] . ']]></SKU>';

				$xmlvalues .= '<name><![CDATA[' . $product['name'] . ']]></name>';

                $xmlvalues .= '<EAN><![CDATA[' . $product['ean13'] . ']]></EAN>';

                $xmlvalues .= '<MPN><![CDATA[' . $product['mpn'] . ']]></MPN>';

                $xmlvalues .= '<url><![CDATA['.$product_url.']]></url>';

                $xmlvalues .= '<image><![CDATA[ '.$img_url.']]></image>';

                $xmlvalues .= '<price><![CDATA[' . $priceshop . ']]></price>';

				$xmlvalues .= '<quantity><![CDATA[' . $offer['offer_quantity'] . ']]></quantity>';

                $xmlvalues .= '<list_price><![CDATA[' . $priceshop . ']]></list_price>';

				$xmlvalues .= '<offer_from><![CDATA[' . $offer["offer_from"] . ']]></offer_from>';

				$xmlvalues .= '<offer_to><![CDATA[' . $offer["offer_to"] . ']]></offer_to>';

				$xmlvalues .= '<offer_price><![CDATA[' . $offer["offer_price"] . ']]></offer_price>';

				$xmlvalues .= '<offer_quantity><![CDATA[' . $offer['offer_quantity'] . ']]></offer_quantity>';

				$xmlvalues .= '<shipping_lead_time><![CDATA[' . $offer['shipping_lead_time'] . ']]></shipping_lead_time>';

                $xmlvalues .= '</product>';

				$i++;		

	}

        $xmlvalues .= '</products>';

		$xmlvalues .= '</MPITEMS>';	

		echo $xmlvalues;

}

