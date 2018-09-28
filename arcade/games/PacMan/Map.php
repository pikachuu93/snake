<?php

namespace PacMan;

use \Core\Term;

class Map implements \Core\Renderable
{
    private $start, $ghostStarts = [], $cells = [], $map = [];

    public function __construct()
    {
        $this->getMap();
    }

    public function getStart()
    {
        return $this->start;
    }

    public function getGhostStarts()
    {
        return $this->ghostStarts;
    }

    public function getCell($position)
    {
        return $this->map[$position[0]][$position[1]];
    }

    public function checkPosition($position)
    {
        $cell = $this->getCell($position);

        if (!$cell->isWalkable())
        {
            return false;
        }

        $cell->setVisited();

        return $position;
    }

    public function render(Term $term) : string
    {
        $str = "";

        foreach ($this->cells as $cell)
        {
            $str .= $cell->render($term);
        }

        return $str;
    }

    protected function getMap()
    {
        $content = file(__DIR__ . "/map.txt");
        foreach ($content as $i => $row)
        {
            foreach (str_split(trim($row, "\n"), 2) as $j => $cell)
            {
                $this->cells[] = $cell = Cell::getCell($cell, $j + 1, $i + 1);
                $this->map[$j + 1][$i + 1] = $cell;

                if ($cell instanceof Start)
                {
                    $this->start = $cell;
                }

                if ($cell instanceof GhostStart)
                {
                    $this->ghostStarts[] = $cell;
                }
            }
        }
    }
}
