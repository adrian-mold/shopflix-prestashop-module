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

//get config email
$secret = $pdo->prepare("SELECT value FROM ".$prefix."configuration WHERE name='PS_SHOP_EMAIL'");
$secret->execute();
$secret = $secret->fetch(PDO::FETCH_ASSOC);
$secret = md5($secret['value']);
$offers = $pdo->prepare("SELECT * FROM ".$prefix."mpxmloffers ORDER BY offers_id DESC");
$offers->execute();
$offers = $offers->fetchAll(PDO::FETCH_ASSOC);	
//print_r($params);
if(count($offers) > 0){
	$msg = '<div class="offers-links-outer"><p style="font-size:1.2em;font-weight:700;color:#222;">Links για Offers Xml:</p>';
	//insert offers in db
	foreach($offers as $offer){		
		$msg .= '<p style="font-size:1em;font-weight:700;background:#f5f5f5;padding:10px; margin:5px 0;" id="of'.$offer['offers_id'].'"><strong style="color:#0769ad;margin-right:20px;">'.$offer['offer_name'].':</strong> <span id="select_txt'.$offer['offers_id'].'">https://' . $_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__)).'/offersXml.php?id='.$offer['offers_id'].'&s='.$secret.'</span> 
		<button type="button" onclick="copy_data(select_txt'.$offer['offers_id'].')" style="font-weight:400;margin-left:20px;margin-right:20px;font-size:0.9em;">Αντιγραφή</button> 
		<button type="button" id="ofdel_'.$offer['offers_id'].'" style="color:#cc0000;font-size:0.9em;cursor:pointer;font-weight:400;border:1px solid #cc0000;border-radius:4px;" class="del_offers_xml">Διαγραφή</button>
		<a href="https://' . $_SERVER['HTTP_HOST'].str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(__DIR__)).'/offersXml.php?id='.$offer['offers_id'].'&s='.$secret.'" id="of_link_'.$offer['offers_id'].'" style="font-size:0.9em;cursor:pointer;font-weight:400;padding-left:25px;" class="link_offers_xml" target="_new">Προεπισκόπιση</a>
		</p>';
	}
	echo $msg.'</div>';
} else {echo '<p style="font-weight:700;padding:15px;background:#fff;color:#cc0000;">Δεν έχετε δημιουργήσει κάποιο Offers Xml Link!</p>';}
?>