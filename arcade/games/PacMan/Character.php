<?php

namespace PacMan;

use Core\Direction;

class Character
{
    protected $position, $direction;

    public function __construct($position, $direction)
    {
        $this->position  = $position;
        $this->direction = $direction;
    }

    public function getNextPosition()
    {
        $nextPos = $this->position;

        switch ($this->direction)
        {
        case Direction::UP:
            $nextPos[1]--;
            break;

        case Direction::DOWN:
            $nextPos[1]++;
            break;

        case Direction::LEFT:
            $nextPos[0]--;
            break;

        case Direction::RIGHT:
            $nextPos[0]++;
            break;

        default:
            throw new \RuntimeException("Unknown direction, '{$this->direction}'.");
        }

        return $nextPos;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    public function setDirection($direction)
    {
        $this->direction = $direction;
    }
}
