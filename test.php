<?php

include("snake/client/term.php");

$term = new Term;

function getCameraMatrix($length)
{
    return new Matrix([
        [2, 0, 0, 0],
        [0, 1, 0, 0],
        [0, 0, 1, -$length],
        [0, 0, 0, 0]
    ]);
}

function map($num, $domainMin, $domainMax, $rangeMin, $rangeMax)
{
    return $rangeMin + ($num - $domainMin) * ($rangeMax - $rangeMin) / ($domainMax - $domainMin);
}

class Line
{
    protected $start, $end;

    public function __construct(Matrix $start, Matrix $end)
    {
        $this->start = $start;
        $this->end   = $end;
    }

    public function draw(Term $term)
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
        for ($i = 0; $i <= 1; $i += 0.1)
        {
            $str .= $term->cursorTo(map($i, 0, 1, $startX, $endX),
                                    map($i, 0, 1, $startY, $endY)) . "##";
        }

        return $str;
    }

    public function add(Matrix $m)
    {
        $newStart = $this->start->add($m);
        $newEnd   = $this->end->add($m);

        return new static($newStart, $newEnd);
    }

    public function mul(Matrix $m)
    {
        $newStart = $this->start->mul($m);
        $newEnd   = $this->end->mul($m);

        return new static($newStart, $newEnd);
    }
}

class Matrix
{
    protected $rows, $cols;
    public function __construct($vals)
    {
        $this->rows = count($vals);

        $this->cols = count($vals[0]);
        foreach ($vals as $row)
        {
            if (count($row) !== $this->cols)
            {
                throw new Exception("Matrix doesn't have consistent number of cols.");
            }
        }

        $this->matrix = $vals;
    }

    public function mul($m)
    {
        if (is_numeric($m))
        {
            return $this->mulConst($m);
        }

        if ($this->cols !== $m->rows)
        {
            throw new Exception("Can't muliply mismatching matrices.");
        }

        $res = Matrix::zeros($this->rows, $m->cols);

        for ($i = 0; $i < $this->rows; ++$i)
        {
            for ($j = 0; $j < $m->cols; ++$j)
            {
                $val = 0;

                for ($k = 0; $k < $this->cols; ++$k)
                {
                    $val += $this->matrix[$i][$k] * $m->matrix[$k][$j];
                }

                $res->matrix[$i][$j] = $val;
            }
        }

        return $res;
    }

    public function mulConst($val)
    {
        $res = $this->copy();

        for ($i = 0; $i < $this->rows; ++$i)
        {
            for ($j = 0; $j < $this->cols; ++$j)
            {
                $res->matrix[$i][$j] *= $val;
            }
        }

        return $res;
    }

    public function add($val)
    {
        if (is_numeric($val))
        {
            return $this->addConst($val);
        }

        $res = $this->copy();

        for ($i = 0; $i < $this->rows; ++$i)
        {
            for ($j = 0; $j < $this->cols; ++$j)
            {
                $res->matrix[$i][$j] += $val->matrix[$i][$j];
            }
        }

        return $res;
    }

    public function addConst($val)
    {
        $res = $this->copy();

        for ($i = 0; $i < $this->rows; ++$i)
        {
            for ($j = 0; $j < $this->cols; ++$j)
            {
                $res->matrix[$i][$j] += $val;
            }
        }

        return $res;
    }

    public function copy()
    {
        return new static($this->matrix);
    }

    public function __toString()
    {
        $res = "";

        foreach ($this->matrix as $row)
        {
            $res .= "|" . implode(", ", $row) . "|\n";
        }

        return $res;
    }

    public function get($row, $col)
    {
        return $this->matrix[$row][$col];
    }

    public static function zeros(int $rows, int $cols)
    {
        return new static(array_fill(0, $rows, array_fill(0, $cols, 0)));
    }
}

$lines = [
    // Front square
    new Line(new Matrix([[-10,  10, -10, 1]]),
             new Matrix([[ 10,  10, -10, 1]])),
    new Line(new Matrix([[ 10,  10, -10, 1]]),
             new Matrix([[ 10, -10, -10, 1]])),
    new Line(new Matrix([[ 10, -10, -10, 1]]),
             new Matrix([[-10, -10, -10, 1]])),
    new Line(new Matrix([[-10, -10, -10, 1]]),
             new Matrix([[-10,  10, -10, 1]])),

    // Back square
    new Line(new Matrix([[-10,  10, 10, 1]]),
             new Matrix([[ 10,  10, 10, 1]])),
    new Line(new Matrix([[ 10,  10, 10, 1]]),
             new Matrix([[ 10, -10, 10, 1]])),
    new Line(new Matrix([[ 10, -10, 10, 1]]),
             new Matrix([[-10, -10, 10, 1]])),
    new Line(new Matrix([[-10, -10, 10, 1]]),
             new Matrix([[-10,  10, 10, 1]])),

    // Connectors
    new Line(new Matrix([[-10,  10,  10, 1]]),
             new Matrix([[-10,  10, -10, 1]])),
    new Line(new Matrix([[ 10,  10,  10, 1]]),
             new Matrix([[ 10,  10, -10, 1]])),
    new Line(new Matrix([[ 10, -10,  10, 1]]),
             new Matrix([[ 10, -10, -10, 1]])),
    new Line(new Matrix([[-10, -10,  10, 1]]),
             new Matrix([[-10, -10, -10, 1]])),
];

$translate = new Matrix([[0, 0, 30, 0]]);
while (true)
{
    for ($i = 0; $i <= M_PI; $i += 0.01)
    {
        $rotate = new Matrix([
            [cos($i), 0, -sin($i), 0],
            [     0, 1,      0, 0],
            [sin($i), 0, cos($i), 0],
            [     0, 0,      0, 1]
        ]);

        $str = "";
        foreach ($lines as $line)
        {
            $str .= $line->mul($rotate)->add($translate)->draw($term);
        }

        $term->clear();
        echo $str;
        usleep(10000);
    }
}
