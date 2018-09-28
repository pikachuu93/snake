<?php

namespace PacMan;

use Core\Term;
use Core\Direction;

class Ghost extends Character implements \Core\Renderable
{
    const COLOURS = [
        \Core\Term::BACKGROUND_RED,
        \Core\Term::BACKGROUND_PINK,
        \Core\Term::BACKGROUND_CYAN,
        \Core\Term::BACKGROUND_GREEN
    ];

    const AI_CLASSES = [
        "\\PacMan\\Ai\\Blinky",
        "\\PacMan\\Ai\\Pinky",
        "\\PacMan\\Ai\\Inky",
        "\\PacMan\\Ai\\Clyde"
    ];

    private static $count = 0;

    public function __construct($position, $direction)
    {
        parent::__construct($position, $direction);

        $aiClass = self::AI_CLASSES[self::$count];

        $this->colour = self::COLOURS[self::$count];
        $this->ai     = new $aiClass;

        self::$count++;
    }

    public function render(Term $term) : string
    {
        $pos = $this->getPosition();
        return $term->cursorTo($pos[0], $pos[1])
           . $this->colour . "  " . Term::CLEAR;
    }

    public function move($pacManPos, Map $map)
    {
        $current = $map->getCell($this->getPosition());

        if ($current->isDecision())
        {
            $dir = $this->ai->getDecision(
                $pacManPos, 
                $this->getPosition(),
                $map
            );

            $this->setDirection($dir);
        }

        $next = $this->getNextPosition();

        $cell = $map->getCell($next);
        if ($cell->isWalkable())
        {
            $this->setPosition($next);
        }
        else
        {
            $this->handleCorner($map);
        }
    }

    private function handleCorner($map)
    {
        $dir     = $this->getDirection();
        $current = $this->getPosition();

        if ($dir === Direction::UP || $dir === Direction::DOWN)
        {
            $next = $current;
            $next[0]++;

            if ($map->getCell($next)->isWalkable())
            {
                $this->setDirection(Direction::RIGHT);
                $this->setPosition($next);
            }
            else
            {
                $next = $current;
                $next[0]--;

                $this->setDirection(Direction::LEFT);
                $this->setPosition($next);
            }
        }
        else
        {
            $next = $current;
            $next[1]++;

            if ($map->getCell($next)->isWalkable())
            {
                $this->setDirection(Direction::DOWN);
                $this->setPosition($next);
            }
            else
            {
                $next = $current;
                $next[1]--;

                $this->setDirection(Direction::UP);
                $this->setPosition($next);
            }
        }
    }
}
