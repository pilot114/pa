<?php

require './vendor/autoload.php';

$projectDir = './data/src/App/Model';
$outputDir = './data/src/tmp/proc_usage_generate';

$finder = new Component\Finder($projectDir);
$parser = new Component\Parser($finder);

// получаем токены типа String_
$strings = $parser->getListTokens('Component\Parser\Visitor\String_');
$strings = array_values(array_unique($strings));

// фильтруем всё, что похоже на вызовы процедур - должны содержать begin И end.
// если не содержат - чекаем вхождение `=> :`
$procStrings = array_filter($strings, function($string){
    $lower = mb_strtolower($string);
    return
        (strpos($lower, 'begin') !== false && strpos($lower, 'end') !== false)
        || strpos($string, '=>') !== false;
});
$procStrings = array_values(array_unique($procStrings));


// подстраховка - смотрим список литералов, где только begin или только end
// и при этом НЕ НАЙДЕННЫЕ ранее. Если в этом списке не наблюдаем вызовов процедур - всё ок.
//$test = array_filter($strings, function($string) use($procStrings) {
//    $lower = mb_strtolower($string);
//    return
//        ((strpos($lower, 'begin') !== false) xor (strpos($lower, 'end') !== false))
//        && !in_array($string, $procStrings);
//});
//$test = array_values(array_unique($test));
//var_dump($test);
//die();

// извлекаем имена процедур из предположительного списка вызовов
$procNames = array_reduce($procStrings, function($acc, $procString){
    preg_match_all('#([\w\.]+)\(#', mb_strtolower($procString), $matches);
    $procMatch = $matches[1];

    // фильтруем вызов стандартных функций и утилит
    $oracleFunctions = [
        'to_date', 'listagg', 'group', 'coalesce', 'max', 'table', 'replace', 'cast',
        'multiset', 'varchar2', 'to_number', 'sys.diutil.bool_to_int', 'trunc', 'last_day',
        'add_months', 'to_char', 'nvl'
    ];
    $procMatch = array_filter($procMatch, function($match) use ($oracleFunctions){
        return !in_array($match, $oracleFunctions);
    });
    $procMatch = array_values($procMatch);
    return array_merge($acc, $procMatch);
}, []);


$procNames = array_unique($procNames);
natsort($procNames);
$procNames = array_values(array_filter($procNames));

foreach($procNames as $p) {
echo $p . "\n";
}

file_put_contents($outputDir . '/' . date('d.m.Y') . '.json', [
    'list' => json_encode($procNames)
]);
