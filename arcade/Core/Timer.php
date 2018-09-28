<?php

namespace Core;

class Timer
{
    private $timeout, $repeat, $callback, $startTime, $fired = false;

    public function __construct($timeout, $repeat = false)
    {
        $this->timeout = $timeout;
        $this->repeat  = $repeat;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function tick()
    {
        if ($this->fired)
        {
            return;
        }

        if (!isset($this->startTime))
        {
            $this->startTime = microtime(true);
        }

        if (microtime(true) - $this->startTime > $this->timeout)
        {
            $c = $this->callback;
            $c();

            $this->startTime = microtime(true);

            if (!$this->repeat)
            {
                $this->fired = true;
            }
        }
    }
}
