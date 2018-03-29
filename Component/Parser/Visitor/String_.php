<?php

namespace Component\Parser\Visitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class String_ extends NodeVisitorAbstract
{
    private $buffer;

    public function leaveNode(Node $node) {
        if ($node instanceof Node\Scalar\String_) {
            $this->buffer[] = $node->value;
        }
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}
