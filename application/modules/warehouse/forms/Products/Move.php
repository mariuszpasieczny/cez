<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Products_Move extends Application_Form
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

    public function setWarehouses($config) {
        //$config = $config->toArray();
        $element = $this->getElement('warehouseid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent->id, $parent->name . ' (' . $parent->getType() . ')');
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

            //$this->addDisplayGroup(array('id-' . $i), 'product-' . $i);
        }
        
        $element = $this->createElement('select', 'warehouseid', array(
            'label' => 'Magazyn:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select'
        ));
        $element->addDecorator('Label', array('tag' => 'label', 'placement' => 'prepend', 'style' => 'margin-top: 25px;'));
        $this->addElement($element);
    }
    
}