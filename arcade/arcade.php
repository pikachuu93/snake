#!/usr/bin/env php
<?php

require_once("Core/lib.php");

spl_autoload_register("\\Core\\autoload");

(new \Core\Option("List", "l", "list", false))
    ->setCallback("\\Core\\listGamesAndDie");

\Core\Option::getopt();

$game   = \Core\selectGame($argv);
$term   = new \Core\Term();
$class  = "\\$game\\Engine";
$engine = new $class(30, 30);

$engine->run($term);
