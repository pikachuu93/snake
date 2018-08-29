#!/usr/bin/php
<?php
require_once("client/lib/term.php");
require_once("client/lib/food.php");
require_once("client/lib/snake.php");

class SnakeInterface
{
    private $term;
    private $snakes;

    private $debug         = TRUE;
    public $serverSocket   = FALSE;
    private $frameLength   = 100000;
    private $currentColour = 0;

    private static $titleFile = "title.txt";
    private static $title     = "Daniely Snake";

    public function __construct($sockets)
    {
        $this->term  = new Term(200, 50);
        
        if (count($sockets) > 7)
        {
            die("Too many connections");
        }

        foreach ($sockets as $socket)
        {
            $colour = Term::$bgc[$this->currentColour++ % count(Term::$bgc)];
            $this->snakes[] = new Snake($this->term, $colour["value"], $socket);
        }
    }

    public function run()
    {
        $nommed = TRUE;

        while (TRUE)
        {
            $t = microtime(TRUE);
            $frame = "";

            foreach ($this->snakes as $snake)
            {
                $in = $snake->getInput();
                $frame .= "\033[0m";

                try
                {
                    $nommed = $snake->processKey($in) || $nommed;
                }
                catch (Exception $e)
                {
                    $snake->kill();
                }

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

            $frame .= "\0";
            $nommed = FALSE;

            foreach ($this->snakes as $i => $snake)
            {
                $snake->writeFrame($frame);
                if ($snake->getNullWrites() > 20)
                {
                    unset($this->snakes[$i]);
                    if (!count($this->snakes))
                    {
                        gc_collect_cycles();
                        die("\n\nAll snakes disconected, exiting.\n");
                    }
                }
            }

            if ($this->serverSocket !== FALSE && count($this->snakes) < 7)
            {
                if (($s = @socket_accept($this->serverSocket)) !== FALSE)
                {
                    $colour = each(Term::$bgc);
                    $this->snakes[] = new Snake($this->term, $colour["value"], $s);
                }
            }

            $t = microtime(TRUE) - $t;
            $t *= 1000000; /* Microseconds in a second */
            if ($this->debug)
            {
                echo "Loop time:".$t."\n";
                echo "Max mem:".memory_get_peak_usage()."\n";
                echo "Current mem:".memory_get_usage()."\n";
            }
            usleep($this->frameLength - $t);
        }
    }

    public function setFrameLength($new)
    {
        $this->frameLength = $new;
    }

    public function gameOver()
    {
        foreach($this->snakes as $snake)
        {
            return $this->term->cursorTo(10, 5).
                   "\033[31m".
                   "GAME OVER\033[0m\n\n".
                   $this->term->cursorTo(10, 6).
                   "Score:".$snake->getLength().
                   "\n\n\n\n\n";

            sleep(2);
            die();
        }
    }

    public static function getIntroScreen()
    {
        if (file_exists(self::$titleFile))
        {
            return "\033[32m\033[2;1H".file_get_contents(self::$titleFile);
        }
        else
        {
            return self::$title;
        }
    }
}

class SnakeServer
{
    private $socket;
    private $connections = array();

    function __construct()
    {
        $this->createSocket();
        $this->createConnections();
        $i = new SnakeInterface($this->connections);
        $i->serverSocket = $this->socket;
        $i->run();
    }

    function createSocket()
    {
        $socketListen = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if(!$socketListen)
        {
            die("Unable to create server socket.\n");
        }

        socket_set_nonblock($socketListen);

        $e = socket_bind($socketListen, '0.0.0.0', 314159);

        if(!$e)
        {
            die("Unable to bind server socket.\n");
        }

        $e = socket_listen($socketListen);

        if(!$e)
        {
            die("Server socket unable to listen.\n");
        }

        $this->socket = $socketListen;
        return TRUE;
    }

    function createConnections()
    {
        $i = 0;
        while (TRUE)
        {
            $temp = @socket_accept($this->socket);
            if ($temp !== FALSE)
            {
                $this->connections[] = $temp;
                socket_write($temp, SnakeInterface::getIntroScreen());
            }

            if (!$this->connections)
            {
                sleep(1);
                continue;
            }

            if ($i++ < 30)
            {
                sleep(1);
                continue;
            }

            return;
        }
    }
}

new SnakeServer();
?>
