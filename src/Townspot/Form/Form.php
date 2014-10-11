<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 10/1/14
 * Time: 9:09 PM
 */

namespace Townspot\Form;

class Form extends \Zend\Form\Form {
    public function __toString() {
        $html = '';
        $html .= "<form role='form' action='{$this->getAttribute('action')}' method='{$this->getAttribute('method')}'";
        $id = $this->getAttribute('id');
        if($id) $html .= " id='$id'";
        $html .= ">";
        $columns = $this->getAttribute('columns');
        if(!$columns) $columns = 1;

        $colWidth = floor(12 / $columns);
        $colnum = 1;

        $html .= "<div class='row'>";

        for ($i = 0; $i < $columns; $i++) {
            $html .= "<div class='col-md-$colWidth'>";
            foreach ($this->getElements() as $e) {
                if ($e->getAttribute('column') != $colnum) continue;
                $html .= $this->_renderElement($e);
            }
            $html .= "</div>";
            $colnum++;
        }

        $html .= "</div>";

        $html .= "<div class='row'>";

        $html .= "<div class='col-md-12'>";
        foreach ($this->getElements() as $e) {
            if ($e->getAttribute('column') != 'span') continue;
            $html .= $this->_renderElement($e);
        }
        $html .= "</div>";


        $html .= "</div>";

        $html .= "</form>";
        return $html;
    }

    protected function _renderElement($e) {
        $html = '';
        $width = $name = $label = $class = $length = $type = $value = $id = $placeholder = false;

        $width = $e->getAttribute('width');
        $name = $e->getName();
        $label = $e->getAttribute('label');
        $class = $e->getAttribute('class');
        $length = $e->getAttribute('maxlength');
        $type = $e->getAttribute('type');
        $value = $e->getValue();
        $id = $e->getAttribute('id');
        if(!$id) $id = $name;
        $placeholder = $e->getAttribute('placeholder');
        if(!$placeholder) $placeholder = $label;

        if($type != 'hidden') {
            if ($width) {
                $html .= "<div class='col-md-$width'>";
            } else {
                $html .= "<div class='col-md-12'>";
            }
            $html .= "<div class='form-group'>";
            $html .= "<div class='input $type'>";
        }


        switch($type) {
            case 'hidden':
            case 'text':
            case 'password':
            case 'email':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<input name='$name' id='$id' class='form-control $class'";
                if($length) $html .=" maxlength='$length'";
                $html .= " type='$type'";
                if($value) $html .=" value='$value'";
                if($placeholder) $html .= "placeholder='$placeholder'";
                $html .= '>';
                break;
            case 'select':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<select name='$name' id='$id' class='form-control $class'>";
                $html .= "<option value=''>$placeholder</option>";
                foreach($e->getValueOptions() as $value => $text) {
                    $html .= "<option value='$value'>$text</option>";
                }
                $html .= "</select>";
                break;
            case 'radio':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<input type='hidden' name='$name' id='$id' value='0'>";
                $html .= "<div>";
                $radioWidth = floor(12 / count($e->getValueOptions()));
                foreach($e->getValueOptions() as $key => $text) {
                    if($value == $key) $checked = " checked='1'";
                    else $checked = '';
                    $html .= "<div class='col-md-$radioWidth'><input type='radio' name='$name' value='$key'$checked><label>$text</label></div>";
                }
                $html .= "</div>";
                break;
            case 'checkbox':
                if($label) $html .= "<label for='$name'>$label</label>";
                if($value) $checked = " checked='1'";
                else $checked = '';
                $html .= "<input type='hidden' name='$name' id='$id' value='0'>";
                $html .= "<input type='$type' name='$name' id='$id' value='1'$checked>";
                break;
            case 'textarea':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<textarea name='$name' class='form-control $class'>$value</textarea>";
                break;
            case 'plupload-image':
                $html .= "<div class='profilePic'>";
		        $html .= "<div class='help-block well text-center'>$label</div>";
                $html .= "<div class='form-group'>";
			    $html .= "<img id='$id' src='$value' class='picPreview img-responsive'>";
                $html .= "<input type='hidden' name='image_url' id='plupPicVal' value='$value'>";
                $html .= "</div>";
                $html .= "</div>";
                break;
            case 'button':
                $btn_class =$e->getAttribute('button-class');
                if(!$btn_class) $btn_class = 'primary';
                $html .= "<button class='btn btn-$btn_class form-control $class''";
                if($id) $html .= " id='$id'";
                $btn_type = $e->getAttribute('btn-type');
                if($btn_type) $html .= " type='$btn_type'";
                $html .= ">";
                if($label) $html .= $label;
                else $html .= "submit";
                $html .= "</button>";
                break;
        }

        if($type != 'hidden') {
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }

        return $html;
    }

} 