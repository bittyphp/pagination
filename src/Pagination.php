<?php
/**
 * Kijtra/Object
 *
 * Licensed under The MIT License
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Kijtra;

class Pagination
{
    /**
     * Default values
     * @var array
     */
    private static $defaults = array(
        'limit' => 10,
        'range' => 7,
    );

    /**
     * Rendering callback
     * @var callable
     */
    private static $render;

    private $current = 1;
    private $limit;
    private $range;
    private $total;
    private $pages;
    private $offset;
    private $from;
    private $to;
    private $prev;
    private $next;
    private $start;
    private $end;

    public function __construct($total = null, $current = null)
    {
        foreach (self::$defaults as $key => $val) {
            $this->{$key} = $val;
        }

        if (!empty($total)) {
            if (is_array($total)) {
                $this->setOptions($total);
            } else {
                $this->setTotal($total);
            }
        }

        if (!empty($current)) {
            $this->setCurrent($current);
        }
    }

    public static function setDefaults($name, $value = null)
    {
        if (is_string($name) && array_key_exists($name, self::$defaults)) {
            if (ctype_digit(strval($value))) {
                self::$defaults[$name] = $value;
            }
        } elseif (is_array($name)) {
            foreach ($name as $key => $val) {
                self::setDefaults($key, $val);
            }
        }
    }

    public static function getDefaults()
    {
        return self::$defaults;
    }

    public function setRender($function)
    {
        if (is_callable($function)) {
            self::$render = $function;
        } else {
            throw new \InvalidArgumentException('Argument must be callable.');
        }
    }

    public function setOptions($options)
    {
        if (!is_array($options)) {
            throw new \InvalidArgumentException('Argument must be Array.');
        }

        if (!empty($options['total'])) {
            $this->setTotal($options['total']);
        }

        if (!empty($options['current'])) {
            $this->setCurrent($options['current']);
        }

        if (!empty($options['limit'])) {
            $this->setLimit($options['limit']);
        }

        if (!empty($options['range'])) {
            $this->setRange($options['range']);
        }

        return $this;
    }

    public function setTotal($int)
    {
        if (ctype_digit(strval($int))) {
            $this->total = (int) $int;
        }
        $this->calc();
        return $this;
    }

    public function setCurrent($int)
    {
        if (ctype_digit(strval($int))) {
            $this->current = (int) $int;
        }
        $this->calc();
        return $this;
    }

    public function setLimit($int)
    {
        if (ctype_digit(strval($int))) {
            $this->limit = (int) $int;
        } else {
            $this->limit = self::$defaults['limit'];
        }
        $this->calc();
        return $this;
    }

    public function setRange($int)
    {
        if (ctype_digit(strval($int))) {
            $this->range = (int) $int;
        } else {
            $this->range = self::$defaults['range'];
        }
        $this->calc();
        return $this;
    }

    public function getInfo()
    {
        return (object) array(
            'current' => $this->current,
            'limit' => $this->limit,
            'total' => $this->total,
            'pages' => $this->pages,
            'offset' => $this->offset,
            'from' => $this->from,
            'to' => $this->to,
            'prev' => $this->prev,
            'next' => $this->next,
            'start' => $this->start,
            'end' => $this->end,
            'range' => $this->range,
        );
    }

    private function calc()
    {
        if (empty($this->total) || empty($this->limit)) {
            return;
        }

        $this->pages = (int) ceil($this->total / $this->limit);
        $this->offset = ($this->current - 1) * $this->limit;
        $this->from = (empty($this->offset) ? 1 : $this->offset);
        $to = $this->offset + $this->limit;
        $this->to = ($to > $this->total ? $this->total : $to);
        $this->prev = ($this->pages > 1 && $this->current > 1 ? $this->current - 1 : null);
        $this->next = ($this->pages > 1 && $this->current < $this->pages ? $this->current + 1 : null);

        if (0 === ($this->range % 2)) {
            $term_start = ($this->range / 2) - 1;
            $term_end = $this->range / 2;
        } else {
            $term_start = $term_end = floor($this->range / 2);
        }
        $start = $this->current - $term_start;
        $end = $this->current + $term_end;
        if ($start <= 1) {
            $start = 1;
            if ($this->range <= $this->pages) {
                $end = $this->range;
            } else {
                $end = $this->pages;
            }
        } elseif ($end >= $this->pages) {
            $start = ($this->pages - $this->range) + 1;
            $end = $this->pages;
        }
        $this->start = (int) $start;
        $this->end = (int) $end;
    }

    public function render(array $options = array())
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }

        $info = $this->getInfo();

        if (empty($info->pages)) {
            return;
        }

        if ($render = self::$render) {
            if ($render instanceof \Closure && method_exists($render, 'bindTo')) {
                $render = $render->bindTo($info);
            }
            return $render($info);
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }
    }
}
