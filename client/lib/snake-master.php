#!/usr/bin/php
<?php
/****************************************
 *         Dan's Super Awesome          *
 *          Server Snake Game           *
 ****************************************/

require_once("term.php");

class Client extends Term
{
    private $socket;

    function __construct($host = "linuxworkstation1", $port = 314159)
    {
        parent::__construct();
        $this->connect($host, $port);
    }

    function connect($host, $port)
    {
        if (!($s = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)))
        {
            die("Unable to create socket");
        }

        if (!@socket_connect($s, $host, $port))
        {
            die("Unable to connect to remote server");
        }

        socket_set_block($s);
        $this->socket = $s;
        echo "Waiting for connections...";

        GLOBAL $argv;
        if (isset($argv[1]) && $argv[1] === "dyoung")
        {
            socket_write($s, "start");
        }

        socket_read($this->socket, 5);
    }

    function socketWrite($in)
    {
        if (!@socket_write($this->socket, $in))
        {
            $this->gameOver();
        }
    }

    function socketRead()
    {
        return socket_read($this->socket, 10000);
    }

    function run()
    {
        $this->raw();
        while (TRUE)
        {
            $in = $this->getInput();
            if (!$in)
                $in = " ";

            $this->socketWrite($in);
            $frame = $this->socketRead();
            $this->clear();
            echo $frame;
            echo $this->cursorTo(200, 50);
            usleep(90000);
        }
        $this->sane();
    }

    function gameOver()
    {
        $frame = $this->socketRead();
        echo $frame;
        $this->sane();
        die();
    }
}

$c = new Client();
$c->run();
?>
