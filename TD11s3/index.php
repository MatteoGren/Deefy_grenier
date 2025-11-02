<?php

use iutnc\deefy\dispatch\Dispatcher;

require_once 'vendor/autoload.php';
session_start();

iutnc\deefy\repository\DeefyRepository::setConfig( 'db.config.ini' );


$dispatcher = new Dispatcher();
$dispatcher->run();
