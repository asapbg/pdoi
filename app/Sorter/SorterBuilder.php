<?php namespace App\Sorter;

class SorterBuilder {
    protected $query;
    protected $sorter;
    protected $direction;
    protected $namespace;


    public function __construct($query, $sorter, $direction, $namespace)
    {
        $this->query = $query;
        $this->sorter = $sorter;
        $this->direction = !is_null($direction) && in_array($direction, ['asc', 'desc']) ? $direction : null;
        $this->namespace = $namespace;
    }

    public function apply()
    {
        if( !is_null($this->sorter) ) {
            $normailizedName = ucfirst(str_replace(['-', '_'], '',$this->sorter));
            $class = $this->namespace . "\\{$normailizedName}";
            if (class_exists($class)) {
                (new $class($this->query))->handle($this->direction);
            }
        }

        return $this->query;
    }

}
