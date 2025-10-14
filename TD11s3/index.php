<?php

use iutnc\deefy\dispatch\Dispatcher;

require_once 'vendor/autoload.php';
session_start();


$dispatcher = new Dispatcher();
$dispatcher->run();
