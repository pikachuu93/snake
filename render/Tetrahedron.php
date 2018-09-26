<?php

class Tetrahedron extends LineCollection
{
    public function __construct(float $size)
    {
        $s3 = sqrt(3);
        $s6 = sqrt(6);

        $lines = [
            // Base triangle
            new Line(new Matrix([[-$size,  -$size / $s3, -$size / $s6, 1]]),
                     new Matrix([[ $size,  -$size / $s3, -$size / $s6, 1]])),
            new Line(new Matrix([[ $size,  -$size / $s3, -$size / $s6, 1]]),
                     new Matrix([[     0,  2 * $size / $s3, -$size / $s6, 1]])),
            new Line(new Matrix([[     0,  2 * $size / $s3, -$size / $s6, 1]]),
                     new Matrix([[-$size,  -$size / $s3, -$size / $s6, 1]])),

            // Uprights
            new Line(new Matrix([[-$size,  -$size / $s3, -$size / $s6,    1]]),
                     new Matrix([[     0,             0, 3 * $size / $s6, 1]])),
            new Line(new Matrix([[ $size,  -$size / $s3, -$size / $s6, 1]]),
                     new Matrix([[     0,             0, 3 * $size / $s6, 1]])),
            new Line(new Matrix([[     0,  2 * $size / $s3, -$size / $s6, 1]]),
                     new Matrix([[     0,             0, 3 * $size / $s6, 1]]))
    ];

        parent::__construct($lines);
    }
}
