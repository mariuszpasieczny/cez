<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Orders_ProductReturn extends Application_Form
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
            //$element->setLabel($product->getProduct()->name . ' (' . $product->getProduct()->serial . ')');
            $desc = $product->getProduct()->name . ' (';
            if ($serial = $product->getProduct()->serial) {
                $desc .= $product->getProduct()->serial . ', ';
            }
            $desc .= max(0,$product->qtyavailable) . ' ' . $product->getProduct()->getUnit()->acronym . ')';
            $element->setLabel($desc);
            //$element->setChecked(true);
            //$element->setAttrib('disabled', 'disabled');
        }
    }

    public function setDemagecodes($config) {
        for ($i = 0; $i <= $this->_productsCount; $i++) {
            $element = $this->getElement('demagecodeid-' . $i);
            if (!$element) {
                return;
            }
            $element->addMultiOption(null, 'Wybierz opcję...');
            foreach ($config as $parent) {
                $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
            }
        }
    }
    
    public function init()
    {
        parent::init();
        $this->setAttrib('class', 'form-inline form-load');
        //$this->setAttrib('action', '/warehouse/orders/product-return');
        //$this->setIsArray(true);
        
        for ($i = 0; $i < $this->_productsCount; $i++) {
            
            $this->addElement('hidden', 'id-' . $i, array());
            $element = $this->createElement('checkbox', 'demaged-' . $i, array(
                'label' => 'Uszkodzony:',
                'belongsTo' => 'demaged',
                'class' => 'form-group input-small',
            ))->setAttribs(array('style' => 'width: ;'));
            $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
            $this->addElement($element);
            $element = $this->createElement('select', 'demagecodeid-' . $i, array(
                        'label' => 'Kod uszkodzenia:',
                        'belongsTo' => 'demagecodeid',
                        'class' => 'form-control chosen-select',
                    ))->setAttribs(array('placeholder' => 'Kod uszkodzenia', 'style' => 'max-width: 65%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
            //$element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
            $this->addElement($element);
            $this->addDisplayGroup(array('id-' . $i, 'demaged-' . $i, 'demagecodeid-' . $i), 'return-' . $i)->setAttribs(array('id' => 'product-' . $i));
        }
        $this->setOptions(array('class' => 'form-load'));
    }
    
}