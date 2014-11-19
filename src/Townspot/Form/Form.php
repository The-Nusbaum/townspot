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
                $column = $e->getAttribute('column');
                if(!$column) $column = 1;
                if ( $column != $colnum) continue;
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
        $errorId = $e->getAttribute('errorId');
        $errorMessage = $e->getAttribute('errorMessage');
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
                foreach($e->getValueOptions() as $key => $text) {
                    if($value == $key) $selected = ' selected';
                    else $selected = '';
                    $html .= "<option value='$key'$selected>$text</option>";
                }
                $html .= "</select>";
                break;
            case 'radio':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<input type='hidden' name='$name' value='0'>";
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
                $html .= "<input type='hidden' name='$name' value='0'>";
                $html .= "<input type='$type' name='$name' id='$id' value='1'$checked>";
                break;
            case 'textarea':
                if($label) $html .= "<label for='$name'>$label</label>";
                $html .= "<textarea name='$name' class='form-control $class' id='$id'>$value</textarea>";
                break;
            case 'plupload-image':
                $url = $value?$value:'http://images.townspot.tv/resizer.php?id=none';
                $html .= "<button id='plupload-image' class='form-control' style='position: relative; z-index: 1;'>";
                $html .= "$label";
                $html .= "</button>";
                $html .= "<div class='form-group'>";
			    $html .= "<img id='$id' src='$url' class='picPreview img-responsive'>";
                $html .= "<input type='hidden' name='$name' id='plupPicVal' value='$url'>";
                $html .= "</div>";
                break;
            case 'plupload-video':
                $html .= "<button id='plupload-video' class='form-control' style='position: relative; z-index: 1;'>";
                $html .= "$label";
                $html .= "</button>";
                $html .= "<div class='progress'>";
                $html .= "<div class='progress-bar' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%;'>";
                $html .= "</div>";
                $html .= "</div>";
                $url = $value?$value:'';
                $html .= "<input type='hidden' id='videofile' value='$url' name='$name'>";
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
            case 'custom-block':
                if($label) $html .= "<h3>$label</h3>";
                $html .= $e->getAttribute('inner-html');
                break;
            case 'tree-selections':
                if($label) $html .= "<h3>$label</h3>";
                $selClass = $e->getAttribute('selClass')? $e->getAttribute('selClass'):'';
                $html .= "<span class='$name'>";
                $html .= $this->iterate_tree_selections($value,$selClass);
                $html .= "</span>";
                break;
            case 'tree':
                if($label) $html .= "<h3>$label</h3>";
                $subColumns = $e->getAttribute('tree-columns');
                if(is_array($subColumns)) {
                    $subColWidth = floor(12 / count($subColumns));
                    foreach ($subColumns as $colName => $options) {
                        $html .= "<div class='col-md-$subColWidth'>";
                        $html .= "<h3>$colName</h3>";
                        $colClass = '';
                        if(!empty($options['class'])) $colClass = " ".$options['class'];
                        $html .= "<ul class='list-group$colClass'>";
                        if(!empty($v['parents']) && $v['parents']) $parent = 'parent ';
                        else $parent = '';
                        if(!empty($v['item-class'])) $itemClass= " ".$v['item-class'];
                        $itemClass = '';
                        if(!empty($options['values']) && is_array($options['values'])){
                            foreach($options['values'] as $valId => $valName){
                                $html .= "<li class='list-group-item$parent$itemClass' data-id='$valId'>$valName</li>";
                            }
                        }
                        $html .= "</ul>";
                        $html .= "</div>";
                    }

                }
                break;
        }
        if($errorId) {
            $html .= "<div class='alert alert-warning  $errorId' style='display:none'>$errorMessage</div>";
        }

        if($type != 'hidden') {
            $html .= "</div>";
            $html .= "</div>";
            $html .= "</div>";
        }

        return $html;
    }

    protected function iterate_tree_selections($data,$selClass) {
        $html = '';
        foreach($data as $sel) {
            $html .= " <span class='$selClass' data-id='{$sel['id']}'>{$sel['name']} <i class='fa fa-times'></i>";
            if($sel['children']) $html .= $this->iterate_tree_selections($sel['children'],$selClass);
            $html .= "</span>";
        }
        return $html;
    }

} 