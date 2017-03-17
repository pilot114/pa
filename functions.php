<?php

$functions = get_defined_functions()['internal'];
echo count($functions);

foreach ($functions as $i => $name) {
	$f = new ReflectionFunction($name);
	$args = [];
    foreach ($f->getParameters() as $param) {
            $tmparg = '';
            if ($param->isPassedByReference()) $tmparg = '&';
            if ($param->isOptional()) {
            	try {
	                $tmparg = '[' . $tmparg . '$' . $param->getName() . ' = ' . $param->getDefaultValue() . ']';
            	} catch (\ReflectionException $e) {
            		continue;
            	}
            } else {
                $tmparg.= '&' . $param->getName();
            }
            $args[] = $tmparg;
            unset($tmparg);
    }
    $functions_list[] = 'function ' . $name . ' ( ' . implode(', ', $args) . ' )' . PHP_EOL;
}
print_r($functions_list);