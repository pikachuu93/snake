<?php

namespace PacMan;

use Core\Term;
use Core\Direction;

class PacMan extends Character implements \Core\Renderable
{
    private $i = 0;

    protected $desiredDirection = Direction::LEFT;

    public function setDesiredDirection($dir)
    {
        $this->desiredDirection = $dir;
    }

    public function getDesiredDirection()
    {
        return $this->desiredDirection;
    }

    public function getNextDesiredPosition()
    {
        $nextPos = $this->position;

        switch ($this->desiredDirection)
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

    public function render(Term $term) : string
    {
        $this->i++;
        $pos = $this->getPosition();
        return $term->cursorTo($pos[0], $pos[1])
            . Term::BACKGROUND_YELLOW . " " 
            . ($this->i % 2 ? "<" : "-") . Term::CLEAR;
    }
}
