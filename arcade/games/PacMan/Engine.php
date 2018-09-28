<?php

namespace PacMan;

use Core\Direction;
use Core\Timer;

class Engine extends \Core\GameEngine
{
    private $map, $pacman, $ghosts = [];
    public function __construct()
    {
        $this->map    = new Map();
        $this->pacman = new PacMan($this->map->getStart()->getPosition(),
                                   Direction::LEFT);

        foreach ($this->map->getGhostStarts() as $cell)
        {
            $this->ghosts[] = new Ghost($cell->getPosition(), Direction::LEFT);
        }

        $this->pacmanTimer = (new Timer(0.4, true))
            ->setCallback([$this, "updatePacman"]);
        $this->ghostsTimer = (new Timer(0.5, true))
            ->setCallback([$this, "updateGhosts"]);
    }

    protected function step() : void
    {
        $this->pacmanTimer->tick();
        $this->ghostsTimer->tick();

        $this->checkGhostCollision();
    }

    public function updatePacman()
    {
        $nextPos = $this->pacman->getNextDesiredPosition();
        if ($result = $this->map->checkPosition($nextPos))
        {
            $this->pacman->setPosition($result);
            $this->pacman->setDirection($this->pacman->getDesiredDirection());

            return;
        }

        $nextPos = $this->pacman->getNextPosition();
        if ($result = $this->map->checkPosition($nextPos))
        {
            $this->pacman->setPosition($result);
        }
    }

    private function updateGhost($ghost)
    {
        $pos = $this->pacman->getPosition();
        $ghost->move($this->pacman, $this->map);
    }

    public function updateGhosts()
    {
        foreach ($this->ghosts as $ghost)
        {
            $this->updateGhost($ghost);
        }
    }

    protected function getRenderFrame() : \Core\Frame
    {
        $frame = (new \Core\Frame())
            ->addItem($this->map)
            ->addItem($this->pacman);

        foreach ($this->ghosts as $ghost)
        {
            $frame->addItem($ghost);
        }

        return $frame;
    }

    protected function processInput(string $input) : void
    {
        switch (strtolower($input))
        {
        case "w":
            $dir = Direction::UP;
            break;

        case "a":
            $dir = Direction::LEFT;
            break;

        case "s":
            $dir = Direction::DOWN;
            break;

        case "d":
            $dir = Direction::RIGHT;
            break;

        default:
            return;
        }

        $this->pacman->setDesiredDirection($dir);
    }

    protected function getRunRate() : int
    {
        return 40000;
    }

    private function checkGhostCollision()
    {
        $pos = $this->pacman->getPosition();
        foreach ($this->ghosts as $ghost)
        {
            if ($pos == $ghost->getPosition())
            {
                throw new \Core\GameOver();
            }
        }
    }
}
