<?php

namespace Core;

abstract class GameEngine
{
    public function run($term)
    {
        $term->raw();
        $size = $term->getSize();
        $term->clear();

        try
        {
            while (true)
            {
                if (false === $input = $term->getInput())
                {
                    break;
                }

                $this->processInput($input);
                $this->step();

                echo $this->getRenderFrame()->render($term)
                    . $term->cursorTo($size[0], $size[1]);

                usleep($this->getRunRate());
            }
        }
        catch (GameOver $e)
        {
            $term->clear();
            echo $term->cursorTo(0, 0) . "Game Over.\n";
        }

        $term->sane();
    }

    protected abstract function step() : void;
    protected abstract function getRenderFrame() : Frame;
    protected abstract function processInput(string $input) : void;
    protected abstract function getRunRate() : int;
}
