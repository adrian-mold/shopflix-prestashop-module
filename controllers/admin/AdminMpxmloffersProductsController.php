<?php

require_once (_PS_MODULE_DIR_ . 'mpxmloffers/classes/mpxmlproduct.php');
class AdminMpxmloffersProductsController extends ModuleAdminController
{
   public function __construct()
    {
        $this->bootstrap = true;
        $this->addRowAction('disableForMpxml');


        $this->table = 'mpxmlproduct';
        $this->className = 'Mpxmlproduct';
        $this->identifier = 'id_mpxmlproduct';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->default_form_language = $this->context->language->id;
        $this->_use_found_rows = false;
        parent::__construct();

		$productioffers = Db::getInstance()->executeS('SELECT * from ' . _DB_PREFIX_ . 'mpxmlproduct WHERE offers_id="0"');
		$productids = array();
		foreach($productioffers as $po){
			$langID = $this->context->language->id;
			$product = new Product($po['product_id'], false, $langID);
			$productname = $product->name;
			if(!empty($po['offer_combination_title'])){ $combtitle = ' ('.$po['offer_combination_title'].')';} else {$combtitle = '';}
			$productids[] = array('product_id'=>$po['product_id'],'product_name'=>$productname.$combtitle,'price'=>$po['offer_price'],'qty'=>$po['offer_quantity'],'offer_from'=>$po['offer_from'],'offer_to'=>$po['offer_to'],'offer_ship'=>$po['shipping_lead_time'],'comb_id'=>$po['offer_combination_id']);
		}
		$this->context->smarty->assign('products', $productids) ;
		//$helper = new Helper();

		$tree = new HelperTreeCategories('associated-categories-tree', 'Associated categories');
		$tree->setTemplate('tree_associated_categories.tpl')
		->setHeaderTemplate('tree_associated_header.tpl')
		->setUseCheckBox(true)
		->setUseSearch(false);

		$this->context->smarty->assign('product_cats', $tree->render()) ;
    }

    public function loadProducts()
    {
        $productids = Db::getInstance()->executeS('SELECT `id_product` FROM `' .
            _DB_PREFIX_ . 'product` WHERE 1');
        /*foreach ($productids as $productidRow)
        {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ .
                'mpxmlproduct` (`product_id`, `enable`) VALUES (' . (int)$productidRow['id_product'] .
                ', 1)');
        }*/
    }
	public function initContent()
	{
		$this->postProcess();
		$this->context->controller->addJS(_MODULE_DIR_.'mpxmloffers/js/script.js');
		$this->context->controller->addCSS(_MODULE_DIR_.'mpxmloffers/css/style.css');
		$this->show_toolbar = true;
		$this->display = 'view';
		$this->meta_title = $this->l('Products Offers XML');
		parent::initContent();	
	}
	
	public function initToolBarTitle()
	{
		$this->toolbar_title = $this->l('Products Offers XML');
	}

    public function displayDisableForMpxmlLink($token = null, $id_mpxmlproduct =
        0)
    {
        $link = $this->context->link->getAdminLink('AdminMpxmlProducts', true) .
            '&id_mpxmlproduct=' . (int)$id_mpxmlproduct . '&disableForMpxml' . $this->
            table;

        $href = $link;
        $action = $this->l('Toggle Include Status');
        $icon = 'icon-cogs';
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ .
            'mpxmloffers/views/templates/admin/_configure/helpers/list/list_action_generic.tpl',
            $this->context->smarty);

        $tpl->assign(array(
            'href' => $href,
            'action' => $action,
            'icon' => $icon,
            'target' => '_self',
            ));

        return $tpl->fetch();
    }


    protected function processBulkDisableForMpxml()
    {
        if (is_array($this->boxes) && !empty($this->boxes))
        {
            foreach ($this->boxes as $id)
            {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
                    'mpxmlproduct` SET `enable` = 0 WHERE `id_mpxmlproduct` = ' . (int)$id);
            }
        }
        return true;
    }

    protected function processBulkEnableForMpxml()
    {
        if (is_array($this->boxes) && !empty($this->boxes))
        {
            foreach ($this->boxes as $id)
            {
                Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
                    'mpxmlproduct` SET `enable` = 1 WHERE `id_mpxmlproduct` = ' . (int)$id);
            }
        }
        return true;
    }

    protected function processBulkDisableAllForMpxml()
    {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
            'mpxmlproduct` SET `enable` = 0 WHERE 1');
        return true;
    }

    protected function processBulkDeleteDuplicates()
    {
        if (is_array($this->boxes) && !empty($this->boxes))
        {
            foreach ($this->boxes as $id)
            {
                Db::getInstance()->execute('DELETE FROM  `' . _DB_PREFIX_ .
                    'mpxmlproduct` WHERE  `id_mpxmlproduct` = ' . (int)$id);
            }
        }
        return true;
    }

    protected function processBulkEnableAllForMpxml()
    {
        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
            'mpxmlproduct` SET `enable` = 1 WHERE 1');
        return true;
    }

    public function toggleProductStatus($id)
    {
        $mpxmlproduct = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ .
            'mpxmlproduct` WHERE `id_mpxmlproduct` = ' . (int)$id);
        if (empty($mpxmlproduct['enable']))
        {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
                'mpxmlproduct` SET `enable` = 1 WHERE `id_mpxmlproduct` = ' . (int)$id);
        } else
        {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ .
                'mpxmlproduct` SET `enable` = 0 WHERE `id_mpxmlproduct` = ' . (int)$id);
        }
    }

    public function disableForMpxml()
    {
        $id_mpxmlproduct = Tools::getValue('id_mpxmlproduct');
        $this->toggleProductStatus($id_mpxmlproduct);
        return true;
    }

    public function postProcess()
    {
        if (Tools::getIsset('disableForMpxml' . $this->table))
        {
            $this->disableForMpxml();
        }
        parent::postProcess();
    }

    public function renderList()
    {
        $this->toolbar_btn['back'] = array('href' => Context::getContext()->link->
                getAdminLink('AdminMpxmlAddFeed', true), 'desc' => $this->l('Back to Mpxml Data List', null, null, false));
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }
	
}
