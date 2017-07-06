<?php

mb_internal_encoding('utf-8');

$str = "Какова сумма номеров символов Unicode во всем этом вопросе?";
$len = mb_strlen($str);

$sum = 0;
for ($i=0; $i < $len; $i++) { 
	$char = mb_substr($str, $i, 1);
	echo $char . "\n";
	$ord = IntlChar::ord($char) . "\n";
	echo $ord . "\n";
	$sum += $ord;
}
echo $sum . "\n";
