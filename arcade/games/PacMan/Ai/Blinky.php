<?php

namespace PacMan\Ai;

use Core\Direction;

class Blinky
{
    public function getDecision($pacManPos, $ghostPos, $map)
    {
        $target  = $this->getTarget($pacManPos, $ghostPos);
        $options = $this->getOptions($ghostPos, $map);
        $cell    = $this->getBestOption($target, $options);

        return $this->getDirection($ghostPos, $cell);
    }

    protected function getOptions($ghostPos, $map)
    {
        $options = [];

        for ($i = -1; $i < 2; $i += 2)
        {
            for ($j = -1; $j < 2; ++$j)
            {
                if ($i && $j || !($i || $j)) continue;

                $cell = $map->getCell([
                    $ghostPos[0] + $i,
                    $ghostPos[1] + $j
                ]);

                if ($cell->isWalkable())
                {
                    $options[] = $cell;
                }
            }
        }

        return $options;
    }

    protected function getTarget($pacManPos, $ghostPos)
    {
        return $pacManPos;
    }

    protected function getBestOption($target, $options)
    {
        $min  = PHP_INT_MAX;
        $cell = null;

        foreach ($options as $opt)
        {
            $dis = $this->getDistance($target, $opt->getPosition());

            if ($dis < $min)
            {
                $min  = $dis;
                $cell = $opt;
            }
        }

        return $cell;
    }

    protected function getDistance($pos1, $pos2)
    {
        $dx = $pos1[0] - $pos2[0];
        $dy = $pos1[1] - $pos2[1];

        return sqrt($dx * $dx + $dy * $dy);
    }

    protected function getDirection($ghostPos, $cell)
    {
        $cellPos = $cell->getPosition();
        if ($cellPos[0] < $ghostPos[0])
        {
            return Direction::LEFT;
        }
        else if ($cellPos[0] > $ghostPos[0])
        {
            return Direction::RIGHT;
        }
        else if ($cellPos[1] > $ghostPos[1])
        {
            return Direction::DOWN;
        }

        return Direction::UP;
    }
}

class Pinky extends Blinky
{
}

class Inky extends Blinky
{
}

class Clyde extends Blinky
{
}
