<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_View_Helper_SelectAttribs extends Zend_View_Helper_FormElement {

    public function selectAttribs($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n") {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, id, value, attribs, options, listsep, disable
        // force $value to array so we can compare multiple values to multiple
        // options; also ensure it's a string for comparison purposes.
        $value = array_map('strval', (array) $value);

        // now start building the XHTML.
        $disabled = '';
        if (true === $disable) {
            $disabled = ' disabled="disabled"';
        }

        // Build the surrounding select element first.
        $xhtml = '<select'
                . ' name="' . $this->view->escape($name) . '[]"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . ">\n  ";

        // build the list of options
        $list = array();
        $translator = $this->getTranslator();
        foreach ($options as $opt_value => $option) {
            $opt_disable = '';
            if (is_array($disable) && in_array($opt_value, $disable)) {
                $opt_disable = ' disabled="disabled"';
            }
            $list[] = $this->_build($option, $value, $disabled);
        }

        // add the options to the xhtml and close the select
        $xhtml .= implode("\n   ", $list) . "\n</select>";

        return $xhtml;
    }

    protected function _build($option, $selected, $disabled) {
        $html = '<option';
        foreach ($option as $attrib => $value) {
            $html .= " $attrib=\"$value\"";
            // selected?
            if ($attrib == 'value' && in_array((string) $value, $selected)) {
                $html .= ' selected="selected"';
            }
        }
        return $html . $disabled . ">" . $option['label'] . "</option>";
    }

}
