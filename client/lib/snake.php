<?php
/****************************************
 *         Dan's Super Awesome          *
 *          Server Snake Game           *
 ***************************************/
class Snake
{
    private static $keys = array("w" => 1, "a" => 2, "s" => 1, "d" => 2);

    private $term;
    private $socket;
    private $nullWrites;

    private $head = "..";

    private $snake = array(array("x" => 1, "y" => 3),
                           array("x" => 1, "y" => 2),
                           array("x" => 1, "y" => 1));

    private $length = 3;
    private $alive = TRUE;

    private $colour = "\033[41m";
    private $key = "s";

    public function __construct($term, $colour = FALSE, $socket = FALSE)
    {
        $this->socket = $socket;
        $this->term = $term;

        if ($colour)
        {
            $this->colour = $colour;
        }
    }

    public function __destruct()
    {
        echo "Destroying Snake.\n";
    }

    public function processKey($k = FALSE)
    {
        if ($this->isDead())
            return FALSE;

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

        $termSize = $this->term->getSize();
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

    public function drawSnake()
    {
        $snake = "";
        foreach ($this->snake as $key => $pos)
        {
            $snake .= $this->term->cursorTo($pos["x"], $pos["y"])
                     .$this->colour.($key == 0 ? $this->head : "  ");
        }

        $snake .= "\033[0m";

        return $snake;
    }

    public function getNullWrites()
    {
        return $this->nullWrites;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function kill()
    {
        $this->alive = FALSE;
    }

    public function isDead()
    {
        return !$this->alive;
    }

    public function getDeathString()
    {
        return $this->term->cursorTo(10, 5).
               "\033[31mGAME OVER\033[0m".
               $this->term->cursorTo(10, 6).
               "Score:".$this->getLength();
    }

    function writeFrame($frame)
    { 
        if ($this->isDead())
            $frame .= $this->getDeathString();

        if (!@socket_write($this->socket, $frame))
            $this->nullWrites++;
    }

    function getInput()
    {
        if ($this->socket)
        {
            $in = array($this->socket);
            $null = NULL;

            if (socket_select($in, $null, $null, 0, 800))
            {
                return socket_read($in[0], 1);
            }
        }
        else
        {
            return $this->term->getInput();
        }

        return $this->key;
    }
}
?>
