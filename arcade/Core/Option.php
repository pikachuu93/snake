<?php

namespace Core;

class Option
{
    private static
        $options = [],
        $populated = false;

    private
        $name,
        $short,
        $long,
        $required,
        $default,
        $callback;

    public function __construct($name, $short, $long, $required = NULL, $default = NULL, $description = NULL)
    {
        $this->name        = $name;
        $this->short       = $short;
        $this->long        = $long;
        $this->required    = $required;
        $this->default     = $default;
        $this->description = $description;
        $this->value       = null;

        self::$options[$name] = $this;

        self::$populated = false;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    public function value()
    {
        if (!self::$populated)
        {
            $this->getopt();
        }

        return $this->value;
    }

    protected function required()
    {
        if ($this->required === NULL)
        {
            return;
        }
        else if ($this->required)
        {
            return ":";
        }
        else
        {
            return "::";
        }
    }

    protected function setValue($values)
    {
        if (isset($values[$this->short]) || isset($values[$this->long]))
        {
            if ($this->required === NULL)
            {
                $this->value = isset($value[$this->short]) || isset($values[$this->long]);
            }
            else
            {
                $this->value = isset($values[$this->short]) ? $values[$this->short] : $values[$this->long];
            }

            if ($this->callback)
            {
                $c = $this->callback;
                $c($this);
            }
        }
    }

    public static function getopt()
    {
        new Option("Help", "h", "help");

        $short = "";
        $long  = [];

        $shortOptions = [];
        $longOptions  = [];

        foreach (self::$options as $option)
        {
            $short .= $option->short . $option->required();
            $long[] = $option->long  . $option->required();
        }

        $parsed = getopt($short, $long);

        foreach (self::$options as $option)
        {
            $option->setValue($parsed);
        }

        self::$populated = true;

        if (isset($parsed["h"]) || isset($parsed["help"]))
        {
            self::helpPage();
            die();
        }
    }

    public static function get($name)
    {
        if (!isset(self::$options[$name]))
        {
            throw new RuntimeException("Option '$name' not found.");
        }

        return self::$options[$name];
    }

    public static function helpPage()
    {
        $table = [["Name", "Flag", "Description"]];

        foreach (self::$options as $option)
        {
            $row = [];
            $row[] = "{$option->name}";

            $flag = "-{$option->short}, --{$option->long}";

            if ($option->required === NULL)
            {
                //Do nothing
            }
            else if ($option->required)
            {
                $flag .= " = VALUE\t";
            }
            else
            {
                $flag .= " [= VALUE]\t";
            }

            $row[] = $flag;

            if ($option->description !== NULL)
            {
                $row[] = $option->description;
            }
            else
            {
                $row[] = "Sets the option '{$option->name}'.";
            }

            $table[] = $row;
        }

        $getMax = function($col) {
            return function($v1, $v2) use ($col) {
                if (strlen($v1[$col]) > strlen($v2[$col]))
                {
                    return $v1;
                }

                return $v2;
            };
        };

        for ($i = 0; $i < 3; ++$i)
        {
            $lengths[$i] = strlen(array_reduce($table, $getMax($i))[$i]);
        }

        foreach ($table as $row)
        {
            echo " ";

            foreach ($row as $i => $cell)
            {
                echo str_pad($cell, $lengths[$i] + 3);
            }

            echo "\n";
        }
    }
}
