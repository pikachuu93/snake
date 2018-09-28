<?php

namespace Snake;

use Core\Term;
use Core\Renderable;

class Food implements Renderable
{
    private $pos;

    public function __construct($width, $height)
    {
        $this->pos = [rand(1, $width - 1), rand(1, $height - 1)];
    }

    public function render(Term $term) : string
    {
        $res = "";

        $res .= $term->cursorTo($this->pos[0], $this->pos[1]) . "`@";

        return $res;
    }

    public function getPos()
    {
        return $this->pos;
    }
}
