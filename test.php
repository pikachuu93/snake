<?php

include("client/term.php");

spl_autoload_register(function($class) {
    if (file_exists("render/$class.php"))
    {
        require_once("render/$class.php");
    }
});

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

$tetr = new Tetrahedron(13);
$cube = new Cube(10);

$translate1 = new Matrix([[-20, 0, 30, 0]]);
$translate2 = new Matrix([[ 20, 0, 30, 0]]);
$size = $term->getSize();
for ($i = 0; $i < 1000 ; $i += 0.01)
{
    $rotateX = new Matrix([
        [cos($i) , 0 , -sin($i) , 0],
        [0       , 1 , 0        , 0],
        [sin($i) , 0 , cos($i)  , 0],
        [0       , 0 , 0        , 1]
    ]);

    $rotateY = new Matrix([
        [1 , 0             , 0              , 0],
        [0 , cos($i / M_E) , -sin($i / M_E) , 0],
        [0 , sin($i / M_E) , cos($i / M_E)  , 0],
        [0 , 0             , 0              , 1]
    ]);

    $str = $cube
        ->mul($rotateX)
        ->mul($rotateY)
        ->add($translate2)
        ->draw($term)
        . $tetr
        ->mul($rotateX)
        ->mul($rotateY)
        ->add($translate1)
        ->draw($term);

    $term->clear();
    echo $str . $term->cursorTo($size[0], $size[1]);
    usleep(10000);
}
