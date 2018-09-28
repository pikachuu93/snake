<?php

namespace Snake;

use Core\Term;
use Core\Direction;
use Core\Renderable;

class Snake implements Renderable
{
    private $pos = [], $body = [];

    public function __construct()
    {
        $this->pos       = [1, 1];
        $this->body      = array_fill(0, 2, [0, 0]);
        $this->direction = Direction::RIGHT;
    }

    public function render(Term $term) : string
    {
        $res = "";

        $res .= $term->cursorTo($this->pos[0], $this->pos[1])
                . Term::BACKGROUND_GREEN . $this->getHead() . "\033[0m";

        foreach ($this->body as $b)
        {
            $res .= $term->cursorTo($b[0], $b[1])
                . Term::BACKGROUND_GREEN . "  " . "\033[0m";
        }

        return $res;
    }

    public function move($wrapX, $wrapY)
    {
        array_unshift($this->body, $this->pos);

        switch ($this->direction)
        {
        case Direction::UP:
            $this->pos[1]--;
            break;

        case Direction::DOWN:
            $this->pos[1]++;
            break;

        case Direction::LEFT:
            $this->pos[0]--;
            break;

        case Direction::RIGHT:
            $this->pos[0]++;
            break;

        default:
            throw new RuntimeException("Unknow direction: '{$this->direction}'.");
        }

        if ($this->pos[0] < 0)
        {
            $this->pos[0] = $wrapX - 1;
        }
        else if ($this->pos[0] >= $wrapX)
        {
            $this->pos[0] = 0;
        }

        if ($this->pos[1] < 0)
        {
            $this->pos[1] = $wrapY - 1;
        }
        else if ($this->pos[1] >= $wrapY)
        {
            $this->pos[1] = 0;
        }
    }

    public function setDirection($char)
    {
        if (!$char || false === strpos("wasd", $char))
        {
            return;
        }

        $dir = $this->direction;
        if ($dir === Direction::UP || $dir === Direction::DOWN)
        {
            if ($char === "a")
            {
                $this->direction = Direction::LEFT;
            }
            else if ($char === "d")
            {
                $this->direction = Direction::RIGHT;
            }
        }
        else
        {
            if ($char === "w")
            {
                $this->direction = Direction::UP;
            }
            else if ($char === "s")
            {
                $this->direction = Direction::DOWN;
            }
        }
    }

    public function decrease()
    {
        array_pop($this->body);
    }

    public function getPos()
    {
        return $this->pos;
    }

    public function getBody()
    {
        return $this->body;
    }

    private function getHead()
    {
        switch ($this->direction)
        {
        case Direction::UP:
            return "''";

        case Direction::DOWN:
            return "..";

        case Direction::LEFT:
            return ": ";

        case Direction::RIGHT:
            return " :";

        default:
            throw new RuntimeException("Unknow direction: '{$this->direction}'.");
        }
    }
}
