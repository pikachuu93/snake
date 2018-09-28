<?php

namespace Core;

class Frame implements Renderable
{
    const PRECLEAR = true;

    protected $items = [];

    public function __construct($clear = false)
    {
        $this->clear = $clear;
    }

    public function addItem(Renderable $item) : self
    {
        $this->items[] = $item;

        return $this;
    }

    public function render(Term $term) : string
    {
        if ($this->clear)
        {
            $term->clear();
        }

        $str = "";

        foreach ($this->items as $item)
        {
            $str .= $item->render($term);
        }

        return $str;
    }
}
