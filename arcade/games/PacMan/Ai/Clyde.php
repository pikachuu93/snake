<?php

namespace PacMan\Ai;

use Core\Direction;

use PacMan\PacMan;
use PacMan\Ghost;

class Clyde extends Ai
{
    protected function getFocusedTarget(PacMan $pacMan, Ghost $ghost) : array
    {
        if ($this->getDistance($pacMan->getPosition(), $ghost->getPosition()) > 8)
        {
            return $pacMan->getPosition();
        }
        else
        {
            return $this->getScatterTarget();
        }
    }

    protected function getScatterTarget() : array
    {
        return [1, 33];
    }
}
