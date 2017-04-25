<?php

use Slim\App;

require_once(__DIR__ . "/../bootstrap/autoload.php");

$app = container()->get(App::class);
$app->run();

