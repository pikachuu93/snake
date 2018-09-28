<?php

namespace PacMan;

use \Core\Term;

class PacMan extends Character implements \Core\Renderable
{
    public function render(Term $term) : string
    {
        $pos = $this->getPosition();
        return $term->cursorTo($pos[0], $pos[1]) . Term::BACKGROUND_YELLOW . "  " . Term::CLEAR;
    }
}
