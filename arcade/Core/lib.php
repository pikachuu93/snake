<?php

namespace Core;

function autoload($class)
{
    $path = str_replace('\\', '/', $class) . ".php";

    if (file_exists($path))
    {
        require_once($path);
    }
    else if (file_exists("games/" . $path))
    {
        require_once("games/" . $path);
    }
}

function selectGame($argv)
{
    $games = getGameList();

    if (!isset($argv[1]) || !in_array($argv[1], $games))
    {
        $index = array_rand($games);

        return $games[$index];
    }

    return $argv[1];
}

function getGameList()
{
    return array_filter(
        scandir("games"),
        function($dir) {
            return $dir[0] !== ".";
        }
    );
}

function listGamesAndDie($option)
{
    $games = getGameList();

    echo implode("\n", $games) . "\n";
    die();
}
