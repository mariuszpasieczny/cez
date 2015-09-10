<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Element_SelectAttribs extends Zend_Form_Element_Multi {

    public $options = array();
    public $helper = 'selectAttribs';

    /**
     * Adds a new <option>
     * @param string $value value (key) used internally
     * @param string $label label that is shown to the user
     * @param array $attribs additional attributes
     */
    public function addMultiOption($value, $label = '', $attribs = array()) {
        $value = (string) $value;
        if (!empty($label))
            $label = (string) $label;
        else
            $label = $value;
        $this->options[$value] = array(
            'value' => $value,
            'label' => $label
                ) + $attribs;
        return $this;
    }

}
