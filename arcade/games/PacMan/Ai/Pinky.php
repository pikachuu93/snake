<?php

namespace PacMan\Ai;

use Core\Direction;

use PacMan\PacMan;
use PacMan\Ghost;

class Pinky extends Ai
{
    protected function getFocusedTarget(PacMan $pacMan, Ghost $ghost) : array
    {
        $pos = $pacMan->getPosition();

        switch ($pacMan->getDirection())
        {
        case Direction::UP:
            $pos[1] -= 4;
            break;

        case Direction::DOWN:
            $pos[1] += 4;
            break;

        case Direction::LEFT:
            $pos[0] -= 4;
            break;

        case Direction::RIGHT:
            $pos[0] += 4;
            break;

        }

        return $pos;
    }

    protected function getScatterTarget() : array
    {
        return [3, -4];
    }
}
