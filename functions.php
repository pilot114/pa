<?php

$functions = get_defined_functions()['internal'];

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
    $functions_list[] = [
        'name' => $name,
        'sign' => 'function ' . $name . ' ( ' . implode(', ', $args) . ' )' . PHP_EOL
    ];
}

$know = [
    // устанавливаем имя
    'cli_get_process_title',
    'cli_set_process_title',
]

echo count($functions_list);
print_r($functions);