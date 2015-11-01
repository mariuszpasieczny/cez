<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Orders_ProductAdd extends Application_Form {

    protected $_productsCount = 1;

    public function setProductsCount($productsCount) {
        $this->_productsCount = $productsCount;
    }

    public function setUnits($config) {
        foreach ($this->getElements() as $element) {
            if (strpos($element->getName(), 'unitid') !== false) {
                foreach ($config as $parent) {
                    $element->addMultiOption($parent['id'], $parent['name']);
                }
            }
        }
    }

    public function setTechnicians($config) {
        //$config = $config->toArray();
        //$element = $this->getElement('technicianid');
        //$element->addMultiOption(null, 'Wybierz opcję...');
        //foreach ($config as $parent) {
        //    $element->addMultiOption($parent['id'], $parent['firstname'] . ' ' . $parent['lastname']);
        //}
    }

    public function setProducts($config) {
        foreach ($config as $i => $product) {
            $element = $this->getElement('id-' . $i);
            $element->setValue($product->id);
            $element = $this->getElement('unitid-' . $i);
            $element->setLabel($product->name . ' (' . $product->serial . ')');
            $element = $this->getElement('unitid-' . $i);
            $element->setValue($product->unitid);
            $element = $this->getElement('quantity-' . $i);
            $element->setValue($product->quantity);
        }
    }

    public function init() {
        parent::init();
        $this->setAttrib('class', 'form-inline form-load');
        $this->setDecorators(
            array(
                'FormElements',
                'Form',
                array('HtmlTag', array('tag' => 'dl', 'class' => 'overflow zend_form')),
            )
        );
        //$this->setIsArray(true);
        /* $this->setDecorators(
          array(
          'FormElements',
          'Form',
          array('HtmlTag', array('tag' => '')),
          )
          ); */


        /* $element = $this->createElement('select', 'technicianid', array(
          'label'      => 'Technik:',
          'required'   => true,
          'filters'    => array('StringTrim'),
          'validators' => array(
          array('lessThan', true, array('score')),
          ),
          'class' => 'form-control chosen-select',
          ));
          $this->addElement($element); */

        for ($i = 0; $i < $this->_productsCount; $i++) {

            $element = $this->createElement('hidden', 'id-' . $i, array(
                    //'belongsTo' => 'id', 
                    //'id' => 'id[' . $i . ']',
                    //'isArray' => true
            ));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addElement($element);

            // Add an email element
            $element = $this->createElement('text', 'quantity-' . $i, array(
                //'label' => 'Ilość:',
                'required' => true,
                //'belongsTo' => 'quantity', 
                //'id' => 'quantity[' . $i . ']',
                //'isArray' => true,
                'placeholder' => 'Ilość',
                'filters' => array('StringTrim'),
                'validators' => array(
                //'NotEmpty',
                ),
                'class' => 'form-control input-sm',
            ));
            $element->addDecorator('HtmlTag', array('class' => 'form-group'));
            $element->addDecorator('Label', array('tag' => 'label', 'class' => 'form-group', 'placement' => 'append'));
            $element->setAttrib('style', 'width: 50px');
            //$element->addDecorator('Label', array('tag' => 'label'));
            $this->addElement($element);

            $element = $this->createElement('select', 'unitid-' . $i, array(
                //'label' => 'Jednostka:',
                'required' => true,
                //'belongsTo' => 'unitid', 
                //'id' => 'unitid[' . $i . ']',
                //'isArray' => true,
                'filters' => array('StringTrim'),
                'validators' => array(
                //    array('lessThan', true, array('score')),
                ),
                'class' => 'form-control chosen-select input-sm',
            ));
            $element->addDecorator('HtmlTag', array('class' => 'form-group'));
            $element->addDecorator('Label', array('tag' => 'label', 'class' => 'form-group', 'placement' => 'append'));
            $element->setAttrib('style', 'width: 75px');
            $this->addElement($element);

            $this->addDisplayGroup(array('quantity-' . $i, 'unitid-' . $i), 'product-' . $i, array('decorators' => array(
                    'FormElements',
                    'Fieldset',
                    array('HtmlTag', array('tag' => 'div', 'style' => 'margin: 5px'))
            )));
        }
    }

}
