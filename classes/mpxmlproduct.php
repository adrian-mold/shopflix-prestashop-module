<?php

class Mpxmlproduct extends ObjectModel
{
    public $id_mpxmlproduct;
    public $product_id;
    public $enable;

    public static $definition = array(
        'table' => 'mpxmlproduct',
        'primary' => 'id_mpxmlproduct',
        'fields' => array(
            'id_mpxmlproduct' => array('type' => self::TYPE_INT),
            'product_id' => array('type' => self::TYPE_INT, ),
            'enable' => array('type' => self::TYPE_BOOL, ),
            ));
    public static function isFieldExistsInDatabase($product_id)
    {
        $id_mpxmlproduct = Db::getInstance()->getValue('SELECT `id_mpxmlproduct` FROM `' .
            _DB_PREFIX_ . 'mpxmlproduct` WHERE `product_id` = ' . (int)$product_id);
        return $id_mpxmlproduct;
    }
}
