<?php

namespace PacMan;

use Core\Term;

class Cell
{
    protected $x, $y, $visited;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function getPosition()
    {
        return [$this->x, $this->y];
    }

    public function render(Term $term) : string
    {
        return $term->cursorTo($this->x, $this->y) . "  ";
    }

    public function isWalkable()
    {
        return true;
    }

    public function isDecision()
    {
        return false;
    }

    public function setVisited()
    {
        $this->visited = true;
    }

    public static function getCell($cell, $x, $y)
    {
        switch ($cell)
        {
        case "##":
            return new Wall($x, $y);

        case "<>":
            return new Food($x, $y);

        case "@@":
            return new SuperFood($x, $y);

        case "  ":
            return new Cell($x, $y);

        case "SS":
            return new Start($x, $y);

        case "GG":
            return new GhostStart($x, $y);

        case "DD":
            return new Decision($x, $y);

        case "DF":
            return new FoodDecision($x, $y);

        case "RD":
            return new RestrictedDecision($x, $y);

        default:
            throw new \RuntimeException("Cannot find cell for string '$cell'.");
        }
    }
}

class Wall extends Cell
{
    public function render(Term $term) : string
    {
        return $term->cursorTo($this->x, $this->y) . Term::BACKGROUND_BLUE . "  " . Term::CLEAR;
    }

    public function isWalkable()
    {
        return false;
    }
}

class Food extends Cell
{
    public function render(Term $term) : string
    {
        return $term->cursorTo($this->x, $this->y) . ($this->visited ? "  " : "<>");
    }
}

class SuperFood extends Food
{
    public function render(Term $term) : string
    {
        return $term->cursorTo($this->x, $this->y) . ($this->visited ? "  " : "@@");
    }
}

class Start extends Cell
{
}

class GhostStart extends Cell
{
}

trait DecisionTrait
{
    public function isDecision()
    {
        return true;
    }
}

class Decision extends Cell
{
    use DecisionTrait;
}

class RestrictedDecision extends Cell
{
    use DecisionTrait;
}

class FoodDecision extends Food
{
    use DecisionTrait;
}
