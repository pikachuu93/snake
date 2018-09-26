<?php

class LineCollection extends Shape
{
    protected $lines = [];

    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function draw(Term $term) : string
    {
        $str = "";
        foreach ($this->lines as $line)
        {
            $str .= $line->draw($term);
        }

        return $str;
    }

    public function add(Matrix $m) : Shape
    {
        $lines = [];
        foreach ($this->lines as $line)
        {
            $lines[] = $line->add($m);
        }

        return new self($lines);
    }

    public function mul(Matrix $m) : Shape
    {
        $lines = [];
        foreach ($this->lines as $line)
        {
            $lines[] = $line->mul($m);
        }

        return new self($lines);
    }
}
