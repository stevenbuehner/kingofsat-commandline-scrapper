#!/usr/bin/env php
<?php
// application.php

require __DIR__ . '/vendor/autoload.php';
date_default_timezone_set("Europe/Berlin");

use StevenBuehner\Console\KingofsatCommand;
use Symfony\Component\Console\Application;

$application = new Application('KingOfSat', '@package_version@');
$application->add(new KingofsatCommand());
$application->run();
