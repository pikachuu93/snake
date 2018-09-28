<?php

namespace PacMan\Ai;

use Core\Direction;

use PacMan\PacMan;
use PacMan\Ghost;

class Inky extends Blinky
{
    protected function getScatterTarget() : array
    {
        return [28, 33];
    }
}
