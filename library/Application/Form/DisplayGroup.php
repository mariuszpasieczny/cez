<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_DisplayGroup extends Zend_Form_DisplayGroup {

    /**
     * Load default decorators
     * 
     * @return void
     */
    public function loadDefaultDecorators() {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('Description', array('tag' => 'li',
                        'class' => 'group_description'))
                    ->addDecorator('FormElements')
                    ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-group'));
        }
    }

}
