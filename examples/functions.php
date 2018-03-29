<?php

require './vendor/autoload.php';

// Вывод всех функции текущего рунтайма

$functions = get_defined_functions()['internal'];

foreach ($functions as $i => $name) {
	$f = new ReflectionFunction($name);
	$args = [];
    foreach ($f->getParameters() as $param) {
        // TODO
        $tmparg = $param->getName();
        $args[] = $tmparg;
    }
    $functions_list[] = [
        'name' => $name,
        'sign' => 'function ' . $name . '( ' . implode(', ', $args) . ' )'
    ];
}

dd($functions_list);
