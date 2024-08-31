<?php

use Ninja\ApiEntryPoint;
use Tapi\TaskApiWebsite;

include_once __DIR__ . '/functions/autoload.php';
include_once __DIR__ . '/functions/errorhandle.php';

header('Content-type: application/json; charset=UTF-8');

// error_reporting(E_ERROR | E_PARSE);

$uri = strtok(ltrim($_SERVER['REQUEST_URI'], '/'), '?');
$method = $_SERVER['REQUEST_METHOD'];

$website = new TaskApiWebsite();

$entryPoint = new ApiEntryPoint($website);
$entryPoint->run($uri, $method);