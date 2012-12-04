<?php

namespace Crud\Traits;

use \RuntimeException;

trait getInputFilter
{

    protected $inputFilter;

    public function getInputFilter()
    {
        if (null == $this->inputFilter) {
            $filter = $this->getInputFilterClassName();
            if (!class_exists($filter)) throw new RuntimeException ("Filter \"{$filter}\" not found");
            $this->inputFilter = new $filter();
        }
        return $this->inputFilter;
    }

    private function getInputFilterClassName()
    {
        $classParts = explode('\\', get_called_class());
        $classParts[1] = 'Filter';
        return implode('\\', $classParts);
    }

}