<?php

abstract class Shape
{
    abstract public function draw(Term $term) : string;
    abstract public function add(Matrix $m) : Shape;
    abstract public function mul(Matrix $m) : Shape;
}
