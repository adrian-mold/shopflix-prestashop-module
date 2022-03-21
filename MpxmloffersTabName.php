<?php
include(_PS_MODULE_DIR_.'mpxmloffers/offersTab.php');

class MpxmloffersTabName extends AdminTab
{
 public function __construct()
 {
   $this->mpxmloffers = new Mpxmloffers();
   return parent::__construct();
 }

 public function display()
 {
   $this->mpxmloffers->token = $this->token;
   $this->mpxmloffers->displayMain(); 
 }

}
?>