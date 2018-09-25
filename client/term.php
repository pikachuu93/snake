<?php
class Term
{
    private $x;
    private $y;
    private static $in;

    public static $bgc = array("red"    => "\033[41m", 
                               "green"  => "\033[42m",
                               "yellow" => "\033[43m",
                               "blue"   => "\033[44m",
                               "pink"   => "\033[45m",
                               "cyan"   => "\033[46m",
                               "grey"   => "\033[47m");

    public function __construct($x = FALSE, $y = FALSE)
    {
        if ($x && $y)
        {
            $this->x = $x;
            $this->y = $y;
        }
        else
        {
            $this->getTermDimensions();
        }

        $this->x -= $this->x % 2 + 1;
        self::$in = fopen("php://stdin", "r");
    }

    public function clear()
    {
        system("clear");
    }

    public function raw()
    {
        system("stty raw");
    }

    public function sane()
    {
        system("stty sane");
    }

    public function cursorTo(int $x, int $y)
    {
        if ($x > $this->x)
            $x = $this->x;
        else if ($x < 1)
            $x = 1;

        if ($y > $this->y)
            $y = $this->y;
        else if ($y < 1)
            $y = 1;

        return "\033[$y;{$x}H";
    }

    function getTermDimensions()
    {
        $this->x = (int)shell_exec("tput cols");
        $this->y = (int)shell_exec("tput lines");
    }

    function changeTermDimensions($x, $y)
    {
        echo "\033[8;$y;{$x}t";
        $xAfter = (int)shell_exec("tput cols");
        $yAfter = (int)shell_exec("tput lines");

        return $x == $xAfter && $y == $yAfter;
    }

    function getInput($fileHandle = FALSE)
    {
        $a = "";

        $stream = $fileHandle ? $fileHandle : self::$in;
        $in = array($stream);
        stream_select($in, $other, $other, 0);

        if (isset($in[0]))
        {
            if (($a = fgetc($in[0])) === ".")
                return FALSE;

            /* Clears any stdin backlog */
            while (stream_select($in, $other, $other, 0) && fgetc($in[0]))
                $in = array($stream);
        }

        return $a;
    }

    function getSize()
    {
        return array($this->x, $this->y);
    }
}
?>
