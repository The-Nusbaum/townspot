<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 10/4/14
 * Time: 5:33 AM
 */

namespace Townspot;


class Entity {
    public function toArray() {
        $data = array();
        foreach(get_class_methods($this) as $method) {
            if(substr($method,0,3) != 'get') continue;
            $propname = lcfirst(ltrim($method, 'get'));
            $value = $this->{$method}();
            if(is_object($value)) {
                if(method_exists($value,'getId')) {
                    $propname .= '_id';
                    $value = $value->getId();
                } else {
                    continue;
                }
            }
            $data[$propname] = $value;
        }
        return $data;
    }
} 