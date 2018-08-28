<?php
class SnakeInterface
{
    private $term;
    private $snakes;

    private $frameLength = 100000;

    public function __construct($sockets)
    {
        $this->term  = new Term(50, 200);
        
        if (count($sockets) > 7)
        {
            die("Too many connections");
        }

        foreach ($sockets as $socket)
        {
            $colour = each(Term::$bgc);
            $this->snakes[] = new Snake($term, $colour["value"]);
        }
    }

    public function run()
    {
        $nommed = FALSE;

        try
        {
            while (TRUE)
            {
                $t = microtime(TRUE);
                $frame = "";

                foreach ($this->snakes as $snake)
                {
                    $in = $snake->getInput();
                    $frame .= "\033[0m";

                    $nommed = $snake->processKey($in) || $nommed;
                    $frame .= $snake->drawSnake();
                }

                if ($nommed)
                {
                    $frame .= Food::drawNewFood($this->term);
                }
                else
                {
                    $frame .= Food::drawFood($this->term);
                }

                /*
                echo $this->term->cursorTo(1, $pos[1])."Score:"
                    .$this->snake->getLength()
                    .$this->term->cursorTo($pos[0], $pos[1])
                    ."\033[8m";
                 */

                foreach ($this->snakes as $snake)
                {
                    $snake->writeFrame($frame);
                }

                $t -= microtime(TRUE);
                $t *= 1000000; /* Microseconds in a second */
                usleep($this->frameLength - $t);
            }
        }
        catch (Exception $e)
        {
            die("Game Over!!!");
            $snake->gameOver();
        }
    }

    public function setFrameLength($new)
    {
        $this->frameLength = $new;
    }
}
?>
