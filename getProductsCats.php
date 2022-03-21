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
$params = array();
parse_str($_POST['offers'], $params);
//print_r($params);//test
		
if(count($params['categoryBox']) > 0){
	$products = array();
	$langs = 1;
	$skipcat = join(",",$params['categoryBox']);
	
	$cats= $pdo->prepare('SELECT  p.`id_product` as pid    
        FROM `'.$prefix.'category_product` cp 
        LEFT JOIN `'.$prefix.'product` p ON p.`id_product` = cp.`id_product` 
        LEFT JOIN `'.$prefix.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1) 
        LEFT JOIN `'.$prefix.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.$langs.') 
        LEFT JOIN `'.$prefix.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.$langs.') 
        LEFT JOIN `'.$prefix.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1) 
        LEFT JOIN `'.$prefix.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.$langs.') 
        LEFT JOIN `'.$prefix.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`) 
        WHERE cp.`id_category` IN ('.$skipcat.') AND p.`active` = 1 
        GROUP BY cp.`id_product`');	
		$cats->execute();
		$cats = $cats->fetchAll(PDO::FETCH_ASSOC);
		foreach($cats as $product){
			$products[] = $product['pid'];
		}

} else {}
if(count($products) > 0){
		
		$div .= '<tr class="head-item">
		<td class="head-item-title">Προϊόν</td>
		<td class="head-item-price">Τιμή Offer</td>
		<td class="head-item-qty">Διαθέσιμη Ποσότητα</td>
		<td class="head-item-from">Ημερ. Από</td>
		<td class="head-item-to">Ημερ. Έως</td>
		<td class="head-item-ship">Ημέρες Διαθεσιμότητας</td>
		<td class="head-item-del">Διαγραφή</td>
		
		</tr>';
	foreach($products as $pid){
		$sql = $pdo->prepare("SELECT p.id_product, p.active, pl.name, p.price, p.id_tax_rules_group, p.wholesale_price, p.reference, p.supplier_reference, p.id_supplier, p.id_manufacturer, p.upc, p.ecotax, p.weight, p.quantity, pl.description_short, pl.description, pl.meta_title, pl.meta_keywords, pl.meta_description, pl.link_rewrite, pl.available_now, pl.available_later, p.available_for_order, p.date_add, p.show_price, p.online_only, p.condition, p.id_shop_default
FROM ps_product p
LEFT JOIN ps_product_lang pl ON (p.id_product = pl.id_product)
LEFT JOIN ps_category_product cp ON (p.id_product = cp.id_product)
LEFT JOIN ps_category_lang cl ON (cp.id_category = cl.id_category)
LEFT JOIN ps_category c ON (cp.id_category = c.id_category)
LEFT JOIN ps_product_tag pt ON (p.id_product = pt.id_product)
WHERE 
p.id_product = '$pid' AND
pl.id_lang = 1
AND cl.id_lang = 1
AND p.id_shop_default = 1
AND c.id_shop_default = 1");
$sql->execute();
		$product = $sql->fetch(PDO::FETCH_ASSOC);
		
		$div .= '<tr class="offer-item" id="offer_'.$pid.'">
		<td class="offer-item-title">'.$product['name'].'<input type="hidden" id="id'.$pid.'" name="pid[]" value="'.$pid.'"></td>
		<td class="offer-item-price"><input type="number" name="offer_price[]" step="0.01" value="'.round($product['price'],2).'" /></td>
		<td class="offer-item-qty"><input type="number" name="offer_qty[]" step="1" value="'.$product['quantity'].'" /></td>
		<td class="offer-item-from"><input type="text" id="from'.$pid.'" name="from[]" value="'.date('j/n/Y').'"></td>
		<td class="offer-item-to"><input type="text" id="to'.$pid.'" name="to[]" value="'.date('j/n/Y', strtotime("+1 years")).'"></td>
		<td class="offer-item-ship"><input type="number" step="1" id="ship'.$pid.'" name="offer_ship[]" value="0"></td> 
		<td class="offer-item-delete" id="offerdel_'.$pid.'">Χ Διαγραφή</td>
  <script>
  $( function() {
    var dateFormat = "mm/dd/yy",
      from = $( "#from'.$pid.'" )
        .datepicker({
          changeMonth: true,
          numberOfMonths: 2,
		  altFormat: "yy-mm-dd"
        })
        .on( "change", function() {
          to.datepicker( "option", "minDate", getDate( this ) );
        }),
      to = $( "#to'.$pid.'" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 2,
		altFormat: "yy-mm-dd"
      })
      .on( "change", function() {
        from.datepicker( "option", "maxDate", getDate( this ) );
      });
 
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
 
      return date;
    }
  } );
  </script>		
		</tr>';
	}
		echo $div;
}
?>