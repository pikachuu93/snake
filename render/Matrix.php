<?php

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

    public function mul($m) : Matrix
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

    public function mulConst(float $val) : Matrix
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

    public function add($val) : Matrix
    {
        if (is_numeric($val))
        {
            return $this->addConst($val);
        }

        $this->throwOnSizeMismatch($val);

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

    public function addConst(float $val) : Matrix
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

    public function copy() : Matrix
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

    public function get($row, $col) : float
    {
        return $this->matrix[$row][$col];
    }

    protected function throwOnSizeMismatch($b)
    {
        if ($this->rows !== $b->rows || $this->cols !== $b->cols)
        {
            throw new RuntimeException(
                "Unable to operate on matrices with "
                . "mismatching sizes. ({$this->rows}x{$this->cols}) "
                . "and ({$b->rows}x{$b->cols})."
            );
        }
    }

    public static function zeros(int $rows, int $cols) : Matrix
    {
        return new static(array_fill(0, $rows, array_fill(0, $cols, 0)));
    }
}
