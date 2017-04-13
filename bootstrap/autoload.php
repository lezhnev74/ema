<?php
declare(strict_types=1);

//Workaround for https://github.com/prooph/proophessor-do/issues/64
error_reporting(E_ALL & ~E_NOTICE);

chdir(dirname(__DIR__));
require __DIR__ . '/../vendor/autoload.php';
require(__DIR__ . "/helpers.php");

