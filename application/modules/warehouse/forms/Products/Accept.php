<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Products_Accept extends Application_Form
{
    
    protected $_productsCount = 1;

    public function setProductsCount($productsCount) {
        $this->_productsCount = $productsCount;
    }

    public function setProducts($config) {
        foreach ($config as $i => $product) {
            $element = $this->getElement('id-' . $i);
            $element->setValue($product->id);
            //$element->setCheckedValue($product->id);
            $element->setLabel($product->name . ' (' . $product->serial . ')');
            //$element->setChecked(true);
            //$element->setAttrib('disabled', 'disabled');
        }
    }
    
    public function init()
    {
        parent::init();
        $this->setAttrib('class', 'form-inline form-load');
        //$this->setAttrib('action', '/warehouse/orders/product-delete');
        //$this->setIsArray(true);
        
        for ($i = 0; $i < $this->_productsCount; $i++) {
            
            $this->addElement('hidden', 'id-' . $i, array(
                //'belongsTo' => 'id', 
                //'id' => 'id[' . $i . ']',
                //'isArray' => true
            ));

            $this->addDisplayGroup(array('id-' . $i), 'product-' . $i);
        }
    }
    
}