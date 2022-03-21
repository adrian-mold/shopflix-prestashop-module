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
if(isset($_POST['products'])){
		
		$div .= '<tr class="head-item">
		<td class="head-item-title">Προϊόν</td>
		<td class="head-item-price">Τιμή Offer</td>
		<td class="head-item-qty">Διαθέσιμη Ποσότητα</td>
		<td class="head-item-from">Ημερ. Από</td>
		<td class="head-item-to">Ημερ. Έως</td>
		<td class="head-item-ship">Ημέρες Διαθεσιμότητας</td>
		<td class="head-item-del">Διαγραφή</td>
		
		</tr>';
	foreach($_POST['products'] as $p){		
	$proid = explode('_',$p);
	$pid = $proid[0].$proid[1];
	$product= $pdo->prepare("SELECT * FROM ".$prefix."mpxmlproduct WHERE product_id='$proid[0]' AND offer_combination_id='$proid[1]' AND offers_id='0' ");
	$product->execute();
	$product = $product->fetch(PDO::FETCH_ASSOC);
	$langID = 1;
	$productdata = new Product($product['product_id'], false, $langID);
	$productname = $productdata->name;
	if(!empty($product['offer_combination_title'])){ $combtitle = ' ('.$product['offer_combination_title'].')';} else {$combtitle = '';}
	
		$div .= '<tr class="offer-item" id="offer_'.$pid.'">
		<td class="offer-item-title">'.$productname.$combtitle.'<input type="hidden" id="id'.$pid.'" name="pid[]" value="'.$proid[0].'"><input type="hidden" id="cid'.$pid.'" name="cid[]" value="'.$proid[1].'"></td>
		<td class="offer-item-price"><input type="number" name="offer_price[]" step="0.01" value="'.round($product['offer_price'],2).'" /></td>
		<td class="offer-item-qty"><input type="number" name="offer_qty[]" step="1" value="'.$product['offer_quantity'].'" /></td>
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