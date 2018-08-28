#!/usr/bin/php
<?php
/****************************************
 *         Dan's Super Awesome          *
 *             Snake Game               *
 ****************************************/

require_once("../lib/term.php");
require_once("food.php");

class Snake
{
    private static $keys = array("w" => 1, "a" => 2, "s" => 1, "d" => 2);

    private $term;

    private $head = "..";

    private $snake = array(array("x" => 1, "y" => 3),
                           array("x" => 1, "y" => 2),
                           array("x" => 1, "y" => 1));

    private $length = 3;

    private $colour = "\033[41m";
    private $key = "s";

    public function __construct($term, $colour = FALSE)
    {
        $this->term = $term;

        if ($colour)
        {
            $this->colour = $colour;
        }
    }

    public function processKey($k, $term)
    {
        $old = $this->key;
        if (isset(self::$keys[strtolower($k)]) &&
            self::$keys[$old] !== self::$keys[$k])
        {
            $this->key = strtolower($k);
        }

        $x = $this->snake[0]["x"];
        $y = $this->snake[0]["y"];
        switch($this->key)
        {
            case "w":
                $y--;
                $head = "''";
                break;

            case "s":
                $y++;
                $head = "..";
                break;

            case "d":
                $x += 2;
                $head = " :";
                break;

            case "a":
                $x -= 2;
                $head = ": ";
                break;
        }

        $termSize = $term->getSize();
        if ($x > $termSize[0])
            $x = 1;
        else if ($x < 1)
            $x = $termSize[0];

        if ($y > $termSize[1])
            $y = 1;
        else if ($y < 1)
            $y = $termSize[1];

        $this->head = $head;
        $headPos = array("x" => $x, "y" => $y);
        $nommed = Food::checkCollision($x, $y);

        if (!$nommed)
        {
            array_pop($this->snake);
        }
        else
        {
            $this->length++;
        }

        if (array_search($headPos, $this->snake) !== FALSE)
            throw new Exception();

        array_unshift($this->snake, $headPos);

        return $nommed;
    }

    public function gameOver()
    {
        {
            $this->term->sane();
            echo $this->term->cursorTo(10, 5).
                 "\033[31m".
                 "GAME OVER\033[0m\n\n".
                 $this->term->cursorTo(10, 6).
                 "Score:".$this->getLength().
                 "\n\n\n\n\n";

            sleep(2);
            die();
        }
    }

    public function drawSnake()
    {
        $snake = "";
        foreach ($this->snake as $key => $pos)
        {
            $snake .= $this->term->cursorTo($pos["x"], $pos["y"])
                     .$this->colour.($key == 0 ? $this->head : "  ");
        }

        $snake .= "\033[0m";

        echo $snake;
    }

    public function getLength()
    {
        return $this->length;
    }
}

class SnakeInterface
{
    private $term;
    private $snake;

    public function __construct()
    {
        $this->term  = new Term();
        $this->snake = new Snake($this->term, "\033[42m");
    }

    public function run()
    {
        $in = TRUE;
        $this->term->raw();
        echo Food::drawNewFood($this->term);
        $pos = $this->term->getSize();
        $pos[0] -= 2;
        $pos[1]--;

        try
        {
            while (TRUE)
            {
                $t = microtime(TRUE);

                $this->term->clear();
                $in = $this->term->getInput();
                echo "\033[0m";
                if ($in === FALSE)
                    break;

                if ($this->snake->processKey($in, $this->term))
                {
                    echo Food::drawNewFood($this->term);
                }
                else
                {
                    echo Food::drawFood($this->term);
                }

                $this->snake->drawSnake();
                echo $this->term->cursorTo(1, $pos[1])."Score:"
                    .$this->snake->getLength()
                    .$this->term->cursorTo($pos[0], $pos[1])
                    ."\033[8m";

                $t -= microtime(TRUE);
                $t *= 1000000;
                usleep(100000 - $t);
            }
        }
        catch (Exception $e)
        {
            $this->snake->gameOver();
        }

        $this->term->sane();
    }
}

$s = new SnakeInterface();
$s->run();
?>
