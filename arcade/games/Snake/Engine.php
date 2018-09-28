<?php

namespace Snake;

class Engine extends \Core\GameEngine
{
    private
        $snake,
        $food;

    public function __construct(int $width, int $height)
    {
        $this->snake = new Snake();
        $this->food  = new Food($width, $height);

        $this->width  = $width;
        $this->height = $height;
    }

    protected function step() : void
    {
        $this->snake->move($this->width, $this->height);

        if ($this->checkSnakeCollision())
        {
            $this->gameOver();
        }

        if ($this->checkFoodCollision())
        {
            $this->food = new Food($this->width, $this->height);
        }
        else
        {
            $this->snake->decrease();
        }
    }

    protected function getRenderFrame() : \Core\Frame
    {
        return (new \Core\Frame(\Core\Frame::PRECLEAR))
            ->addItem($this->snake)
            ->addItem($this->food);
    }

    protected function processInput(string $input) : void
    {
        $this->snake->setDirection(strtolower($input));
    }

    protected function getRunRate() : int
    {
        return 200000;
    }

    private function gameOver()
    {
        throw new \Core\GameOver();
    }

    private function checkFoodCollision()
    {
        if ($this->snake->getPos() == $this->food->getPos())
        {
            return true;
        }

        return false;
    }

    private function checkSnakeCollision()
    {
        $pos = $this->snake->getPos();
        foreach ($this->snake->getBody() as $body)
        {
            if ($pos === $body)
            {
                return true;
            }
        }

        return false;
    }
}
