<?php

namespace Core;

interface Renderable
{
    public function render(Term $term) : string;
}
