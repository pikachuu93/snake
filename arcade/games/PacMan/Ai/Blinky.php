<?php

namespace PacMan\Ai;

use Core\Direction;

use PacMan\PacMan;
use PacMan\Ghost;

class Blinky extends Ai
{
    protected function getFocusedTarget(PacMan $pacMan, Ghost $ghost) : array
    {
        return $pacMan->getPosition();
    }

    protected function getScatterTarget() : array
    {
        return [26, -4];
    }
}
