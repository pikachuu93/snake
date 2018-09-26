<?php

class Cube extends LineCollection
{
    public function __construct(float $size)
    {
        $lines = [
            // Front square
            new Line(new Matrix([[-$size,  $size, -$size, 1]]),
                     new Matrix([[ $size,  $size, -$size, 1]])),
            new Line(new Matrix([[ $size,  $size, -$size, 1]]),
                     new Matrix([[ $size, -$size, -$size, 1]])),
            new Line(new Matrix([[ $size, -$size, -$size, 1]]),
                     new Matrix([[-$size, -$size, -$size, 1]])),
            new Line(new Matrix([[-$size, -$size, -$size, 1]]),
                     new Matrix([[-$size,  $size, -$size, 1]])),

            // Back square
            new Line(new Matrix([[-$size,  $size, $size, 1]]),
                     new Matrix([[ $size,  $size, $size, 1]])),
            new Line(new Matrix([[ $size,  $size, $size, 1]]),
                     new Matrix([[ $size, -$size, $size, 1]])),
            new Line(new Matrix([[ $size, -$size, $size, 1]]),
                     new Matrix([[-$size, -$size, $size, 1]])),
            new Line(new Matrix([[-$size, -$size, $size, 1]]),
                     new Matrix([[-$size,  $size, $size, 1]])),

            // Connectors
            new Line(new Matrix([[-$size,  $size,  $size, 1]]),
                     new Matrix([[-$size,  $size, -$size, 1]])),
            new Line(new Matrix([[ $size,  $size,  $size, 1]]),
                     new Matrix([[ $size,  $size, -$size, 1]])),
            new Line(new Matrix([[ $size, -$size,  $size, 1]]),
                     new Matrix([[ $size, -$size, -$size, 1]])),
            new Line(new Matrix([[-$size, -$size,  $size, 1]]),
                     new Matrix([[-$size, -$size, -$size, 1]]))
        ];

        parent::__construct($lines);
    }
}
