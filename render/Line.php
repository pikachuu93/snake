<?php

class Line extends Shape
{
    protected $start, $end;

    public function __construct(Matrix $start, Matrix $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function draw(Term $term) : string
    {
        $cam   = getCameraMatrix(0.05);

        $start = $this->start->mul($cam);
        $end   = $this->end->mul($cam);

        $size = $term->getSize();

        $mX = $size[0] / 2;
        $mY = $size[1] / 2;

        $startX = $mX + $start->get(0, 0) / $start->get(0, 3);
        $startY = $mY + $start->get(0, 1) / $start->get(0, 3);
        $endX = $mX + $end->get(0, 0) / $end->get(0, 3);
        $endY = $mY + $end->get(0, 1) / $end->get(0, 3);

        $str = "";
        for ($i = 0; $i <= 1; $i += 0.02)
        {
            $x = $this->map($i, 0, 1, $startX, $endX);
            $y = $this->map($i, 0, 1, $startY, $endY);

            if (fmod($y, 1) < 1 / 3)
            {
                $chr = "'";
            }
            else if (fmod($y, 1) < 2 / 3)
            {
                $chr = "-";
            }
            else
            {
                $chr = ".";
            }

            $str .= $term->cursorTo($x, $y) . $chr;
        }

        return $str;
    }

    public function add(Matrix $m) : Shape
    {
        $newStart = $this->start->add($m);
        $newEnd   = $this->end->add($m);

        return new static($newStart, $newEnd);
    }

    public function mul(Matrix $m) : Shape
    {
        $newStart = $this->start->mul($m);
        $newEnd   = $this->end->mul($m);

        return new static($newStart, $newEnd);
    }

    /**
     * Linearly maps a number in a range to a domain.
     *
     * @param $num       The number to map.
     * @param $domainMin The min value of the domain.
     * @param $domainMax The max value of the domain.
     * @param $rangeMin  The min value of the range.
     * @param $rangeMax  The max value of the range.
     *
     * @return The mapped value.
     */
    protected function map(
        float $num,
        float $domainMin,
        float $domainMax,
        float $rangeMin,
        float $rangeMax
    ) : float
    {
        return $rangeMin + ($num - $domainMin) 
            * ($rangeMax - $rangeMin)
            / ($domainMax - $domainMin);
    }

}
