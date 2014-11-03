<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 9/28/14
 * Time: 9:03 PM
 */

namespace Townspot\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Image extends AbstractHelper
{
    protected $count = 0;

    public function __invoke($type,$id,$width,$height = false)
    {
        return $this->{$type}($id,$width,$height);
    }

    public function user($id,$width,$height) {
        if ($height) $h = "&h=$height";
        else $h='';
		return $this->getView()->linkCdn("/resizer.php?id=$id&w=$width$h&type=profile");
    }

    public function video($id,$width,$height) {
        if ($height) $h = "&h=$height";
        else $h='';
		return $this->getView()->linkCdn("/resizer.php?id=$id&w=$width$h");
    }
}