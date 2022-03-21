<?php
//*******
//Marketplace XML Offers
//v.1.0.0 by Shopees
//*******
if (!defined('_PS_VERSION_'))
{
	exit;
}
class Mpxmloffers extends Module
{
	public function __construct()
	{
		$this->name = 'mpxmloffers';
		$this->tab = 'others';
		$this->version = '1.0.0';
		$this->author = 'Shopees';
		$this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Marketplace XML Offers');
		$this->description = $this->l('Create offers xml for Shopees.gr');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		if (!Configuration::get('mpxmloffers'))
		{
			$this->warning = $this->l('No name provided');
		}
	}


	//install module
	public function install()
	{	
		$sql = array();
		$sql[1] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'mpxmlproduct` (
		`id_mpxmlproduct` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		`offers_id` int(11),
		`product_id` INT(11) NOT NULL,
		  `offer_combination_id` int(10) NOT NULL,
		  `offer_combination_title` varchar(250),
		  `offer_price` decimal(10,2) NOT NULL,
		  `offer_from` timestamp NOT NULL,
		  `offer_to` timestamp NOT NULL,
		  `offer_quantity` int(10) NOT NULL,
		  `shipping_lead_time` int(3) NOT NULL,
		`enable` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
		PRIMARY KEY(`id_mpxmlproduct`),
		INDEX `product_id`(`product_id`)
		) ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8;';
		
		//create offers table
		$sql[2] = 'CREATE TABLE `'._DB_PREFIX_.'mpxmloffers` (
		  `offers_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
		  `offer_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
		  `date_added` datetime NOT NULL,
		  PRIMARY KEY(`offers_id`)
		) ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8;';

		foreach ($sql as $query) {
			if (Db::getInstance()->execute($query) == false) {
				return false;
			}
		}
      $registration = parent::install() && $this->registerHook('backOfficeHeader') &&
        $this->registerHook('displayBackOfficeFooter') && $this->registerHook('displayOrderConfirmation') &&
        $this->registerHook('actionProductSave') && $this->registerHook('actionCategoryAdd') &&
        $this->registerHook('actionProductDelete') && $this->registerHook('actionCategoryDelete') &&
        $this->registerHook('header') &&
            $this->registerHook('productFooter') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductTab') &&
            $this->registerHook('displayProductTabContent') &&
            $this->registerHook('actionProductUpdate')&& $this->registerHook('getOfferInfo');
      //$this->_installTabs();
      return $registration;
	}
	
	//unistall module
	public function uninstall()
	{
		$sql = array();
		$sql[1] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'mpxmlproduct';
		$sql[2] = 'DROP TABLE IF EXISTS '._DB_PREFIX_.'mpxmloffers';

		foreach ($sql as $query) {
			if (Db::getInstance()->execute($query) == false) {
				return false;
			}
		}		
      if (!parent::uninstall())
      {
        return false;
      }
      return true;
	}
    public function hookDisplayAdminProductsExtra($params)
    {
        $_GET['id_product'] = $params['id_product'];
        $this->context->smarty->assign('id_product', Tools::getValue('id_product'));
        $this->context->smarty->assign('related_category', Configuration::get('related_category' . Tools::getValue('id_product')));
        $this->context->smarty->assign('related_nb', Configuration::get('related_nb' . Tools::getValue('id_product')));
        $this->context->smarty->assign('related_link', Configuration::get('related_link' . Tools::getValue('id_product')));
        $this->context->smarty->assign('physical_uri', $this->context->shop->physical_uri);
        $this->context->smarty->assign('virtual_uri', $this->context->shop->virtual_uri);
        $this->context->smarty->assign('secure_key', $this->secure_key);
		$this->context->controller->addCSS(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/css/style.css');
		$this->context->controller->addJs(Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/js/script.js');
		$product_id = Tools::getValue('id_product');
        $offer = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'mpxmlproduct` WHERE product_id='.$product_id.' ORDER by offer_to DESC');
        $productdata = Db::getInstance()->getRow('SELECT ' . _DB_PREFIX_ . 'layered_price_index.price_min as price,' . _DB_PREFIX_ . 'stock_available.quantity as quantity FROM `' . _DB_PREFIX_ . 'stock_available`,`' . _DB_PREFIX_ . 'layered_price_index` WHERE ' . _DB_PREFIX_ . 'stock_available.id_product='.$product_id.' AND ' . _DB_PREFIX_ . 'stock_available.id_product=' . _DB_PREFIX_ . 'layered_price_index.id_product');	
		$offerdata = array('product'=>$productdata,'offer'=>$offer);
		$combdata = Db::getInstance()->executeS("SELECT
		pa.*,pl.*,pal.*,pac.*
		FROM " . _DB_PREFIX_ . "product p
		LEFT JOIN " . _DB_PREFIX_ . "product_attribute pa ON (p.id_product = pa.id_product)
		LEFT JOIN " . _DB_PREFIX_ . "product_lang pl ON (p.id_product = pl.id_product)
		LEFT JOIN " . _DB_PREFIX_ . "product_attribute_combination pac ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN " . _DB_PREFIX_ . "attribute_lang pal ON (pac.id_attribute = pal.id_attribute)
		WHERE pl.id_lang = 1
			AND pal.id_lang = 1
			AND  p.id_product = '$product_id'
		GROUP BY pac.id_product_attribute");
		$combname = array();
		$offercomb = array();
		foreach($combdata as $k=>$c){
			$combination = new Combination($c['id_product_attribute']);
			$arr = $combination->getAttributesName(1);
			$combname[$k] =$arr;
			$cid = $c['id_product_attribute'];
			$offercomb[] = Db::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "mpxmlproduct WHERE offer_combination_id='$cid'");
		}
		
        $this->context->smarty->assign('combname',$combname);//get combinations data
        $this->context->smarty->assign('combdata',$combdata);//get combinations
        $this->context->smarty->assign('offerdata',$offerdata);
		$this->context->smarty->assign('combsavedata',$offercomb);//get combinations data
        return $this->display(__FILE__, 'views/templates/admin/tabs.tpl');
    }

    public function getContent()
    {
      Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpxmloffersProducts', false) .
        '&token=' . Tools::getAdminTokenLite('AdminMpxmloffersProducts'));	
    }	
}
?>