<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 30.03.2018
 * Time: 0:56
 */

namespace Component\Parser;

use PhpParser\NodeTraverser;

class MyTraverser extends NodeTraverser
{
    public function getVisitors()
    {
        return $this->visitors;
    }
}