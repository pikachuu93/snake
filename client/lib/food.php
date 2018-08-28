<?php
class Food
{
    static $pos = array();
    static $nomType = 0;
    static $noms = array("\033[32m`\033[31m@\033[0m");

    public static function makePos($term)
    {
        $pos = $term->getSize();

        $x = mt_rand(1, $pos[0]);
        $y = mt_rand(1, $pos[1]);
        while(!($x % 2))
            $x = mt_rand(1, $pos[0]);

        self::$pos = array("x" => $x, "y" => $y);
    }

   public static function drawNewFood($term)
    {
        self::makePos($term);
        self::makeType();
        return $term->cursorTo(self::$pos["x"],
                             self::$pos["y"]).
               self::$noms[self::$nomType];

    }

    public static function drawFood($term)
    {
        return $term->cursorTo(self::$pos["x"],
                               self::$pos["y"]).
               self::$noms[self::$nomType];
    }

    private static function makeType()
    {
        self::$nomType = mt_rand(0, count(self::$noms) - 1);
    }

    public static function checkCollision($x, $y)
    {
        return array("x" => $x, "y" => $y) == self::$pos;
    }
}
?>
