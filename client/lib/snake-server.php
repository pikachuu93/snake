<?php
class SnakeServer
{
    private $socket;
    private $connections = array();

    function __construct()
    {
        $this->createSocket();
        $this->createConnections();
        $i = new SnakeInterface($this->connections);
        $i->run();
    }

    function createSocket()
    {
        $socketListen = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if(!$socketListen)
        {
            die("Unable to create server socket.");
        }

        socket_set_nonblock($socketListen);

        $e = socket_bind($socketListen, '0.0.0.0', 314159);

        if(!$e)
        {
            die("Unable to bind server socket.");
        }

        $e = socket_listen($socketListen);

        if(!$e)
        {
            die("Server socket unable to listen.");
        }

        $this->socket = $socketListen;
        return TRUE;
    }

    function createConnections()
    {
        while (TRUE)
        {
            $temp = @socket_accept($this->socket);
            if ($temp !== FALSE)
            {
                $this->connections[] = $temp;
            }

            if (!$this->connections)
            {
                usleep(1);
                continue;
            }

            $tmpRead = $this->connections;
            $n = NULL;

            if (socket_select($tmpRead, $n, $n, 0))
            {
                foreach ($tmpRead as $tmprd)
                {
                    if (socket_read($tmprd, 5) === "start")
                    {
                        return TRUE;
                    }
                }
            }

            usleep(1);
        }
    }
}
?>
