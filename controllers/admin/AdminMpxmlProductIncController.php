<?php

require_once (_PS_MODULE_DIR_ . 'mpxmloffers/classes/mpxmlproduct.php');
class AdminMpxmlProductIncController extends ModuleAdminController
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
        $this->bulk_actions = array(
            'enableForMpxml' => array(
                'text' => $this->l('Include Product to Feed'),
                'icon' => 'icon-power-on text-success',
                ),
            'disableForMpxml' => array(
                'text' => $this->l('Remove From Feed'),
                'icon' => 'icon-power-off text-success',
                ),
            'enableAllForMpxml' => array(
                'text' => $this->l('Include All Product to Feed'),
                'icon' => 'icon-power-on text-danger',
                'confirm' => $this->l('Include All Products To Feed?')),
            'disableAllForMpxml' => array(
                'text' => $this->l('Remove All Products From Feed'),
                'icon' => 'icon-power-off text-danger',
                'confirm' => $this->l('REMOVE All Products from Feed?')),
            'deleteDuplicates' => array(
                'text' => $this->l('Delete From List'),
                'icon' => 'icon-power-off text-success',
                ),
            );
        $this->fields_list = array(
            'product_id' => array(
                'title' => $this->l('Product Id'),
                'filter_key' => 'a!product_id',
                'class' => 'fixed-width-xs',
                'remove_onclick' => true),
            'name' => array(
                'title' => $this->l('Product Name'),
                'filter_key' => 'pl!name',
                'remove_onclick' => true),
            'reference' => array(
                'title' => $this->l('Reference'),
                'filter_key' => 'p!reference',
                'remove_onclick' => true),
            'enable' => array(
                'title' => $this->l('Included'),
                'active' => 'disableForMpxml',
                'type' => 'bool',
                'filter_key' => 'a!enable',
                ),
            );

        $productsCount = Db::getInstance()->getValue('SELECT count(*) FROM  `' .
            _DB_PREFIX_ . 'mpxmlproduct` WHERE 1');
        if (!$productsCount)
        {
            $this->loadProducts();
        }
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'product_lang` pl ON (a.`product_id` = pl.`id_product` AND pl.`id_lang` = ' . (int)
            $this->context->language->id . ') ';
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ .
            'product` p ON (a.`product_id` = p.`id_product`) ';
        $this->_orderBy = 'a!product_id';
        $this->_orderWay = 'ASC';
        $this->_select .= ' a.`id_mpxmlproduct` ';
    }

    public function loadProducts()
    {
        $productids = Db::getInstance()->executeS('SELECT `id_product` FROM `' .
            _DB_PREFIX_ . 'product` WHERE 1');
        foreach ($productids as $productidRow)
        {
            Db::getInstance()->execute('INSERT INTO `' . _DB_PREFIX_ .
                'mpxmlproduct` (`product_id`, `enable`) VALUES (' . (int)$productidRow['id_product'] .
                ', 1)');
        }
    }

    public function displayDisableForMpxmlLink($token = null, $id_mpxmlproduct =
        0)
    {
        $link = $this->context->link->getAdminLink('AdminMpxmlProductInc', true) .
            '&id_mpxmlproduct=' . (int)$id_mpxmlproduct . '&disableForMpxml' . $this->
            table;

        $href = $link;
        $action = $this->l('Toggle Include Status');
        $icon = 'icon-cogs';
        $tpl = $this->context->smarty->createTemplate(_PS_MODULE_DIR_ .
            'mpxmloffers/views/templates/admin/_configure/helpers/list/list_products.tpl',
            $this->context->smarty);

        $tpl->assign(array(
            'href' => $href,
            'action' => $action,
            'icon' => $icon,
            'target' => '_self',
            ));

        return $tpl->fetch();
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $mpxml_feed_token = Tools::getAdminTokenLite('AdminMpxmlAddFeed');
        if (empty($this->display))
        {
            $this->page_header_toolbar_btn['add_new_feed'] = array(
                'href' => $this->context->link->getAdminLink('AdminMpxmlAddFeed', false) .
                    '&token=' . $mpxml_feed_token,
                'desc' => $this->l('Add New Feed', null, null, false),
                'icon' => 'process-icon-new');
            $this->page_header_toolbar_btn['mpxml_log'] = array(
                'href' => $this->context->link->getAdminLink('AdminMpxmlLog'),
                'desc' => $this->l('View Logs', null, null, false),
                'icon' => 'process-icon-cogs');
            $this->page_header_toolbar_btn['mpxml_settings'] = array(
                'href' => $this->context->link->getAdminLink('AdminMpxmlSettings'),
                'desc' => $this->l('Settings', null, null, false),
                'icon' => 'process-icon-configure');
        }
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
                getAdminLink('AdminMpxmlAddFeed', true), 'desc' => $this->l('Back to Offers XML Data List', null, null, false));
        unset($this->toolbar_btn['new']);
        return parent::renderList();
    }
}
