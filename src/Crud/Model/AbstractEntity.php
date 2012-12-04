<?php

namespace Crud\Model;

use Zend\InputFilter\InputFilter,
    \RuntimeException;

abstract class AbstractEntity
{

    use \Crud\Traits\getInputFilter;

    public function __construct(array $data = null)
    {
        if ($data) {
            $this->setData($data);
        }
        $this->inputFilter = null;
    }

    public function __set($key, $value)
    {
        $this->$key = $value; //$this->valid($key, $value);
    }

    public function __get($key)
    {
        return $this->$key;
    }

    public function setData($data)
    {
        foreach($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    public function toArray()
    {
        $dados = get_object_vars($this);
        foreach ($dados as $k => $item) {
            if (is_object($item) && isset($item->id)) {
                $dados[$k] = $item->id;
            } else {
                $dados[$k] = $item;
            }

        }
        return $dados;
    }

    public function isValid()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->setData($this->toArray());
        return $inputFilter()->isValid();
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }

}