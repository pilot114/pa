<?php

require './vendor/autoload.php';

spl_autoload_register(function ($class) {
    require_once('./Component/'.$class.'.php');
});

require './Informer.php';
